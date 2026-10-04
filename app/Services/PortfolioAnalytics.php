<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use GeoIp2\Database\Reader;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PortfolioAnalytics
{
    public const EVENTS = [
        'project_view', 'repository_click', 'demo_click', 'social_click', 'navigation_click',
        'email_click', 'cv_click', 'cv_download', 'contact_section_view', 'contact_form_start',
        'contact_submit', 'page_duration',
    ];

    /** @return array{session_id: int, is_returning: bool}|null */
    public function recordPageView(Request $request): ?array
    {
        if ($request->cookie('analytics_opt_out') === '1') {
            return null;
        }

        $now = now();
        $visitorKey = $request->cookie('analytics_visitor');
        $visitor = is_string($visitorKey) && Str::isUuid($visitorKey)
            ? DB::table('analytics_visitors')->where('visitor_key', $visitorKey)->first()
            : null;
        $isReturning = $visitor !== null;

        if (! $visitor) {
            $visitorKey = (string) Str::uuid();
            $visitorId = DB::table('analytics_visitors')->insertGetId([
                'visitor_key' => $visitorKey,
                'first_seen_at' => $now,
                'last_seen_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $visitorId = $visitor->id;
            DB::table('analytics_visitors')->where('id', $visitorId)->update(['last_seen_at' => $now, 'updated_at' => $now]);
        }

        $sessionKey = $request->cookie('analytics_session');
        $session = is_string($sessionKey) && Str::isUuid($sessionKey)
            ? DB::table('analytics_sessions')->where('session_key', $sessionKey)->where('visitor_id', $visitorId)->first()
            : null;
        if (! $session || CarbonImmutable::parse($session->last_activity_at)->lt($now->copy()->subMinutes(30))) {
            if ($session) {
                DB::table('analytics_sessions')->where('id', $session->id)->update(['ended_at' => $session->last_activity_at, 'updated_at' => $now]);
            }
            $sessionKey = (string) Str::uuid();
            $sessionId = DB::table('analytics_sessions')->insertGetId(array_merge([
                'session_key' => $sessionKey,
                'visitor_id' => $visitorId,
                'is_returning' => $isReturning,
                'landing_path' => $this->path($request),
                'referrer_domain' => $this->referrerDomain($request),
                'started_at' => $now,
                'last_activity_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ], $this->campaign($request), $this->clientDetails($request)));
        } else {
            $sessionId = $session->id;
            DB::table('analytics_sessions')->where('id', $sessionId)->update(['last_activity_at' => $now, 'ended_at' => null, 'updated_at' => $now]);
        }

        $path = $this->path($request);
        $projectId = null;
        if (preg_match('#^/projects/([^/]+)$#', $path, $matches)) {
            $projectId = DB::table('portfolio_items')->where('type', 'project')->where('slug', $matches[1])->value('id');
        }
        DB::table('analytics_page_views')->insert([
            'analytics_session_id' => $sessionId,
            'project_id' => $projectId,
            'path' => $path,
            'page_title' => Str::limit(strip_tags((string) $request->attributes->get('analytics_page_title', '')), 190, ''),
            'viewed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        if ($projectId && $path !== '/projects') {
            DB::table('analytics_events')->insert([
                'analytics_session_id' => $sessionId,
                'project_id' => $projectId,
                'event_name' => 'project_view',
                'path' => $path,
                'occurred_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $request->attributes->set('analytics_tracking', ['visitor_key' => $visitorKey, 'session_key' => $sessionKey, 'returning' => $isReturning]);

        return ['session_id' => $sessionId, 'is_returning' => $isReturning];
    }

    /** @param array<string, mixed> $input */
    public function recordEvent(Request $request, array $input): bool
    {
        if ($request->cookie('analytics_opt_out') === '1') {
            return false;
        }
        $key = $request->cookie('analytics_session');
        $session = is_string($key) && Str::isUuid($key)
            ? DB::table('analytics_sessions')->where('session_key', $key)->where('last_activity_at', '>=', now()->subMinutes(30))->first()
            : null;
        if (! $session) {
            return false;
        }

        $name = $input['event'] ?? '';
        if (! in_array($name, self::EVENTS, true)) {
            return false;
        }
        $path = $this->cleanPath((string) ($input['path'] ?? '/'));
        if (! DB::table('analytics_page_views')->where('analytics_session_id', $session->id)->where('path', $path)->exists()) {
            return false;
        }
        $projectId = isset($input['project_id']) ? (int) $input['project_id'] : null;
        if ($projectId && ! DB::table('portfolio_items')->where('id', $projectId)->where('type', 'project')->where('is_published', true)->exists()) {
            $projectId = null;
        }
        $properties = [];
        if (in_array($name, ['repository_click', 'demo_click', 'social_click', 'navigation_click', 'cv_click', 'cv_download'], true)) {
            $properties['target'] = in_array($input['target'] ?? null, ['repository', 'demo', 'social', 'navigation', 'cv', 'email'], true) ? $input['target'] : null;
        }
        if ($name === 'page_duration') {
            $duration = filter_var($input['duration'] ?? null, FILTER_VALIDATE_INT);
            if ($duration !== false && $duration >= 0 && $duration <= 86400) {
                $properties['duration_seconds'] = $duration;
                $viewId = DB::table('analytics_page_views')->where('analytics_session_id', $session->id)->where('path', $path)->orderByDesc('viewed_at')->value('id');
                if ($viewId) {
                    DB::table('analytics_page_views')->where('id', $viewId)->update(['duration_seconds' => min($duration, 65535)]);
                }
            }
        }
        DB::table('analytics_events')->insert([
            'analytics_session_id' => $session->id,
            'project_id' => $projectId,
            'event_name' => $name,
            'path' => $path,
            'properties' => $properties ? json_encode($properties, JSON_THROW_ON_ERROR) : null,
            'occurred_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('analytics_sessions')->where('id', $session->id)->update(['last_activity_at' => now(), 'updated_at' => now()]);

        return true;
    }

    /** @return array<string, mixed> */
    public function overview(CarbonImmutable $from, CarbonImmutable $to, array $filters = []): array
    {
        $cacheKey = 'portfolio.analytics.overview.'.sha1($from->toDateString().$to->toDateString().json_encode($filters));

        return cache()->remember($cacheKey, now()->addMinute(), function () use ($from, $to, $filters): array {
            $sessions = $this->filteredSessions($from, $to, $filters);
            $sessionIds = (clone $sessions)->select('analytics_sessions.id');
            $views = DB::table('analytics_page_views')->whereIn('analytics_session_id', clone $sessionIds)->whereBetween('viewed_at', [$from->startOfDay(), $to->endOfDay()]);
            if (! empty($filters['page'])) {
                $views->where('path', $filters['page']);
            }
            $sessionCount = (clone $sessions)->count();
            $viewCount = (clone $views)->count();
            $durationSql = DB::connection()->getDriverName() === 'sqlite'
                ? 'AVG(MAX(0, (julianday(last_activity_at) - julianday(started_at)) * 86400)) as average_duration'
                : 'AVG(GREATEST(0, TIMESTAMPDIFF(SECOND, started_at, last_activity_at))) as average_duration';
            $averageDuration = (clone $sessions)->selectRaw($durationSql)->value('average_duration');

            return [
                'visitors' => (clone $sessions)->distinct('visitor_id')->count('visitor_id'),
                'sessions' => $sessionCount,
                'page_views' => $viewCount,
                'new_visitors' => $this->visitorCount($from, $to, $filters, 'new'),
                'returning_visitors' => $this->visitorCount($from, $to, $filters, 'returning'),
                'bounce_rate' => $sessionCount ? round((clone $sessions)->whereIn('id', DB::table('analytics_page_views')->select('analytics_session_id')->groupBy('analytics_session_id')->havingRaw('COUNT(*) = 1'))->count() / $sessionCount * 100, 1) : 0,
                'avg_pages' => $sessionCount ? round($viewCount / $sessionCount, 2) : 0,
                'avg_duration' => (int) round($averageDuration ?? 0),
                'online' => DB::table('analytics_sessions')->where('last_activity_at', '>=', now()->subMinutes(2))->count(),
                'daily' => (clone $views)->selectRaw('DATE(viewed_at) as day, COUNT(*) as total')->groupBy('day')->orderBy('day')->get(),
                'popular_pages' => (clone $views)->select('path')->selectRaw('COUNT(*) as total')->groupBy('path')->orderByDesc('total')->limit(8)->get(),
                'popular_projects' => DB::table('analytics_page_views as p')->join('portfolio_items as i', 'i.id', '=', 'p.project_id')->whereIn('p.analytics_session_id', clone $sessionIds)->whereBetween('p.viewed_at', [$from->startOfDay(), $to->endOfDay()])->select('i.title', 'i.slug')->selectRaw('COUNT(*) as total')->groupBy('i.id', 'i.title', 'i.slug')->orderByDesc('total')->limit(8)->get(),
                'sources' => (clone $sessions)->selectRaw("COALESCE(referrer_domain, 'Direct') as label, COUNT(*) as total")->groupBy('referrer_domain')->orderByDesc('total')->limit(8)->get(),
                'devices' => (clone $sessions)->select('device as label')->selectRaw('COUNT(*) as total')->groupBy('device')->orderByDesc('total')->get(),
                'browsers' => (clone $sessions)->select('browser as label')->selectRaw('COUNT(*) as total')->groupBy('browser')->orderByDesc('total')->get(),
                'operating_systems' => (clone $sessions)->select('operating_system as label')->selectRaw('COUNT(*) as total')->groupBy('operating_system')->orderByDesc('total')->get(),
                'countries' => (clone $sessions)->selectRaw("COALESCE(country, 'Unknown') as label, COUNT(*) as total")->groupBy('country')->orderByDesc('total')->limit(8)->get(),
                'events' => DB::table('analytics_events')->whereIn('analytics_session_id', clone $sessionIds)->whereBetween('occurred_at', [$from->startOfDay(), $to->endOfDay()])->select('event_name')->selectRaw('COUNT(*) as total')->groupBy('event_name')->orderByDesc('total')->get(),
                'cv_downloads' => DB::table('analytics_events')->whereIn('analytics_session_id', clone $sessionIds)->whereBetween('occurred_at', [$from->startOfDay(), $to->endOfDay()])->where('event_name', 'cv_download')->count(),
                'contact_section_views' => DB::table('analytics_events')->whereIn('analytics_session_id', clone $sessionIds)->whereBetween('occurred_at', [$from->startOfDay(), $to->endOfDay()])->where('event_name', 'contact_section_view')->count(),
                'contact_form_starts' => DB::table('analytics_events')->whereIn('analytics_session_id', clone $sessionIds)->whereBetween('occurred_at', [$from->startOfDay(), $to->endOfDay()])->where('event_name', 'contact_form_start')->count(),
                'contact_submissions' => DB::table('analytics_events')->whereIn('analytics_session_id', clone $sessionIds)->whereBetween('occurred_at', [$from->startOfDay(), $to->endOfDay()])->where('event_name', 'contact_submit')->count(),
            ];
        });
    }

    public function sessionsQuery(array $filters = []): Builder
    {
        $query = DB::table('analytics_sessions as s')->join('analytics_visitors as v', 'v.id', '=', 's.visitor_id')
            ->select('s.*', 'v.visitor_key', 'v.first_seen_at', DB::raw('(SELECT COUNT(*) FROM analytics_page_views pv WHERE pv.analytics_session_id = s.id) as page_views'))
            ->selectRaw("CASE WHEN s.is_returning = 1 THEN 'Returning' ELSE 'New' END as visitor_type");
        foreach (['country_code', 'device', 'browser', 'operating_system', 'referrer_domain'] as $field) {
            if (! empty($filters[$field])) {
                $query->where('s.'.$field, $filters[$field]);
            }
        }
        if (isset($filters['from'])) {
            $query->where('s.started_at', '>=', $filters['from']->startOfDay());
        }
        if (isset($filters['to'])) {
            $query->where('s.started_at', '<=', $filters['to']->endOfDay());
        }
        if (($filters['visitor_type'] ?? null) === 'new') {
            $query->where('s.is_returning', false);
        } elseif (($filters['visitor_type'] ?? null) === 'returning') {
            $query->where('s.is_returning', true);
        }
        if (! empty($filters['page'])) {
            $query->whereIn('s.id', DB::table('analytics_page_views')->select('analytics_session_id')->where('path', $filters['page'])->distinct());
        }

        return $query->orderByDesc('s.started_at');
    }

    private function filteredSessions(CarbonImmutable $from, CarbonImmutable $to, array $filters): Builder
    {
        $query = DB::table('analytics_sessions')->whereBetween('started_at', [$from->startOfDay(), $to->endOfDay()]);
        foreach (['country_code', 'device', 'browser', 'operating_system', 'referrer_domain'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (($filters['visitor_type'] ?? null) === 'new') {
            $query->where('is_returning', false);
        } elseif (($filters['visitor_type'] ?? null) === 'returning') {
            $query->where('is_returning', true);
        }
        if (! empty($filters['page'])) {
            $query->whereIn('id', DB::table('analytics_page_views')->select('analytics_session_id')->where('path', $filters['page'])->distinct());
        }

        return $query;
    }

    private function visitorCount(CarbonImmutable $from, CarbonImmutable $to, array $filters, string $visitorType): int
    {
        $filters['visitor_type'] = $visitorType;

        return $this->filteredSessions($from, $to, $filters)->distinct('visitor_id')->count('visitor_id');
    }

    public function sessionIds(CarbonImmutable $from, CarbonImmutable $to, array $filters = []): Builder
    {
        return $this->filteredSessions($from, $to, $filters)->select('id');
    }

    private function path(Request $request): string
    {
        return $this->cleanPath('/'.$request->path());
    }

    private function cleanPath(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';

        return Str::limit('/'.ltrim($path, '/'), 512, '');
    }

    private function referrerDomain(Request $request): ?string
    {
        $host = parse_url((string) $request->header('referer'), PHP_URL_HOST);
        if (! is_string($host) || $host === '' || $host === $request->getHost() || Str::endsWith($host, '.'.$request->getHost())) {
            return null;
        }

        return Str::limit(Str::lower($host), 190, '');
    }

    /** @return array<string, string|null> */
    private function campaign(Request $request): array
    {
        $campaign = [];
        foreach (['source', 'medium', 'campaign', 'content', 'term'] as $key) {
            $value = $request->query('utm_'.$key);
            $campaign['utm_'.$key] = is_string($value) && preg_match('/^[\pL\pN ._\-]+$/u', $value) ? Str::limit(trim($value), $key === 'source' || $key === 'medium' ? 100 : 150, '') : null;
        }

        return $campaign;
    }

    /** @return array<string, string|null> */
    private function clientDetails(Request $request): array
    {
        $agent = (string) $request->userAgent();
        $device = preg_match('/ipad|tablet/i', $agent) ? 'tablet' : (preg_match('/mobile|iphone|android/i', $agent) ? 'mobile' : 'desktop');
        $browser = match (true) {
            preg_match('/Edg\//', $agent) === 1 => 'Edge',
            preg_match('/OPR\//', $agent) === 1 => 'Opera',
            preg_match('/Firefox\//', $agent) === 1 => 'Firefox',
            preg_match('/Chrome\//', $agent) === 1 => 'Chrome',
            preg_match('/Safari\//', $agent) === 1 => 'Safari',
            default => 'Other',
        };
        $os = match (true) {
            preg_match('/Windows/i', $agent) === 1 => 'Windows',
            preg_match('/iPhone|iPad|iOS/i', $agent) === 1 => 'iOS',
            preg_match('/Android/i', $agent) === 1 => 'Android',
            preg_match('/Mac OS/i', $agent) === 1 => 'macOS',
            preg_match('/Linux/i', $agent) === 1 => 'Linux',
            default => 'Other',
        };
        $geo = $this->lookupLocation($request->ip());

        return ['device' => $device, 'browser' => $browser, 'operating_system' => $os, 'country_code' => $geo['country_code'], 'country' => $geo['country'], 'region' => $geo['region'], 'city' => $geo['city']];
    }

    /** @return array{country_code: ?string, country: ?string, region: ?string, city: ?string} */
    private function lookupLocation(?string $ip): array
    {
        $unknown = ['country_code' => null, 'country' => null, 'region' => null, 'city' => null];
        $database = config('analytics.geoip_database');
        if (is_string($database) && ! is_file($database) && is_file(base_path($database))) {
            $database = base_path($database);
        }
        if (! $ip || ! $database || ! is_file($database) || ! class_exists(Reader::class)) {
            return $unknown;
        }

        try {
            $reader = new Reader($database);
            $record = $reader->city($ip);
            $result = ['country_code' => $record->country->isoCode, 'country' => $record->country->name, 'region' => $record->mostSpecificSubdivision->name, 'city' => $record->city->name];
            $reader->close();

            return $result;
        } catch (\Throwable) {
            return $unknown;
        }
    }
}
