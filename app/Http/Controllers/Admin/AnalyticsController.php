<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PortfolioAnalytics;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function __construct(private readonly PortfolioAnalytics $analytics) {}

    public function overview(Request $request): View
    {
        $filters = $this->filters($request);
        $summary = $this->analytics->overview($filters['from'], $filters['to'], $filters);

        return view('admin.analytics.overview', compact('filters', 'summary'));
    }

    public function visitors(Request $request): View
    {
        $filters = $this->filters($request);
        $visitors = $this->analytics->sessionsQuery($filters)->paginate(25)->withQueryString();

        return view('admin.analytics.list', ['title' => 'Visitors', 'eyebrow' => 'VISITOR INTELLIGENCE', 'description' => 'Anonymous visitor sessions with approximate device and location details.', 'filters' => $filters, 'rows' => $visitors, 'kind' => 'visitors']);
    }

    public function visitor(int $visitor): View
    {
        $record = DB::table('analytics_visitors')->where('id', $visitor)->firstOrFail();
        $sessions = DB::table('analytics_sessions')->where('visitor_id', $visitor)->orderByDesc('started_at')->paginate(25);
        $timeline = DB::table('analytics_page_views as p')->join('analytics_sessions as s', 's.id', '=', 'p.analytics_session_id')->where('s.visitor_id', $visitor)->select('p.*')->orderByDesc('viewed_at')->limit(100)->get();

        return view('admin.analytics.visitor', compact('record', 'sessions', 'timeline'));
    }

    public function sources(Request $request): View
    {
        $filters = $this->filters($request);
        $rows = $this->analytics->overview($filters['from'], $filters['to'], $filters)['sources'];

        return view('admin.analytics.breakdown', ['title' => 'Traffic sources', 'eyebrow' => 'ACQUISITION', 'description' => 'Referring domains and direct visits from recorded sessions.', 'filters' => $filters, 'rows' => $rows, 'valueLabel' => 'Sessions']);
    }

    public function pages(Request $request): View
    {
        $filters = $this->filters($request);
        $rows = DB::table('analytics_page_views')->whereIn('analytics_session_id', $this->analytics->sessionIds($filters['from'], $filters['to'], $filters))->whereBetween('viewed_at', [$filters['from']->startOfDay(), $filters['to']->endOfDay()])->when($filters['page'], fn ($query, $page) => $query->where('path', $page))->select('path')->selectRaw('COUNT(*) as total')->groupBy('path')->orderByDesc('total')->paginate(25)->withQueryString();

        return view('admin.analytics.list', ['title' => 'Pages', 'eyebrow' => 'CONTENT PERFORMANCE', 'description' => 'Page views recorded from successful public HTML visits.', 'filters' => $filters, 'rows' => $rows, 'kind' => 'pages']);
    }

    public function page(Request $request): View
    {
        $path = '/'.ltrim((string) $request->query('path', '/'), '/');
        $filters = $this->filters($request);
        $views = DB::table('analytics_page_views')->whereIn('analytics_session_id', $this->analytics->sessionIds($filters['from'], $filters['to'], $filters))->where('path', $path)->whereBetween('viewed_at', [$filters['from']->startOfDay(), $filters['to']->endOfDay()])->orderByDesc('viewed_at')->paginate(25)->withQueryString();

        return view('admin.analytics.page', compact('path', 'filters', 'views'));
    }

    public function events(Request $request): View
    {
        $filters = $this->filters($request);
        $events = DB::table('analytics_events')->whereIn('analytics_session_id', $this->analytics->sessionIds($filters['from'], $filters['to'], $filters))->whereBetween('occurred_at', [$filters['from']->startOfDay(), $filters['to']->endOfDay()])->when($filters['page'], fn ($query, $page) => $query->where('path', $page))->when($request->query('event'), fn ($query, $event) => $query->where('event_name', $event))->orderByDesc('occurred_at')->paginate(50)->withQueryString();

        return view('admin.analytics.list', ['title' => 'Events', 'eyebrow' => 'INTERACTIONS', 'description' => 'Clicks, contact milestones, downloads, and project engagement.', 'filters' => $filters, 'rows' => $events, 'kind' => 'events']);
    }

    public function geography(Request $request): View
    {
        $filters = $this->filters($request);
        $rows = DB::table('analytics_sessions')->whereIn('id', $this->analytics->sessionIds($filters['from'], $filters['to'], $filters))->selectRaw("COALESCE(country, 'Unknown') as label, COUNT(*) as total")->groupBy('country')->orderByDesc('total')->paginate(25)->withQueryString();

        return view('admin.analytics.breakdown', ['title' => 'Geography', 'eyebrow' => 'APPROXIMATE LOCATION', 'description' => 'Country and city are resolved locally from the configured GeoLite2 City database.', 'filters' => $filters, 'rows' => $rows, 'valueLabel' => 'Sessions']);
    }

    public function realtime(): View
    {
        return view('admin.analytics.realtime');
    }

    public function live(): JsonResponse
    {
        $visitors = DB::table('analytics_sessions as s')->join('analytics_page_views as p', function ($join): void {
            $join->on('p.analytics_session_id', '=', 's.id')->whereRaw('p.viewed_at = (SELECT MAX(p2.viewed_at) FROM analytics_page_views p2 WHERE p2.analytics_session_id = s.id)');
        })->where('s.last_activity_at', '>=', now()->subMinutes(2))->select('p.path', 's.device', 's.country', 's.city', 's.last_activity_at')->orderByDesc('s.last_activity_at')->limit(100)->get();

        return response()->json(['count' => $visitors->count(), 'visitors' => $visitors]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        $kind = in_array($request->query('kind'), ['events', 'pages'], true) ? $request->query('kind') : 'sessions';
        $query = match ($kind) {
            'events' => DB::table('analytics_events')->whereIn('analytics_session_id', $this->analytics->sessionIds($filters['from'], $filters['to'], $filters))->whereBetween('occurred_at', [$filters['from']->startOfDay(), $filters['to']->endOfDay()])->when($filters['page'], fn ($query, $page) => $query->where('path', $page))->orderBy('occurred_at'),
            'pages' => DB::table('analytics_page_views')->whereIn('analytics_session_id', $this->analytics->sessionIds($filters['from'], $filters['to'], $filters))->whereBetween('viewed_at', [$filters['from']->startOfDay(), $filters['to']->endOfDay()])->when($filters['page'], fn ($query, $page) => $query->where('path', $page))->orderBy('viewed_at'),
            default => $this->analytics->sessionsQuery($filters),
        };
        $filename = 'portfolio-analytics-'.$kind.'-'.$filters['from']->toDateString().'.csv';

        return response()->streamDownload(function () use ($query, $kind): void {
            $handle = fopen('php://output', 'w');
            if ($kind === 'events') {
                fputcsv($handle, ['Event', 'Path', 'Project ID', 'Occurred at']);
            } elseif ($kind === 'pages') {
                fputcsv($handle, ['Page path', 'Project ID', 'Viewed at', 'Duration seconds']);
            } else {
                fputcsv($handle, ['Visitor', 'Started', 'Last activity', 'Landing page', 'Referrer', 'Country', 'Region', 'City', 'Device', 'Browser', 'Operating system', 'Page views']);
            }
            foreach ($query->cursor() as $row) {
                $values = match ($kind) {
                    'events' => [$row->event_name, $row->path, $row->project_id, $row->occurred_at],
                    'pages' => [$row->path, $row->project_id, $row->viewed_at, $row->duration_seconds],
                    default => [$row->visitor_key, $row->started_at, $row->last_activity_at, $row->landing_path, $row->referrer_domain, $row->country, $row->region, $row->city, $row->device, $row->browser, $row->operating_system, $row->page_views],
                };
                fputcsv($handle, array_map(fn ($value) => $this->spreadsheetSafe((string) ($value ?? '')), $values));
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array{from: CarbonImmutable, to: CarbonImmutable, country_code: ?string, device: ?string, browser: ?string, operating_system: ?string, referrer_domain: ?string, visitor_type: ?string} */
    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'device' => ['nullable', Rule::in(['desktop', 'mobile', 'tablet', 'unknown'])],
            'browser' => ['nullable', 'string', 'max:40'],
            'operating_system' => ['nullable', 'string', 'max:40'],
            'referrer_domain' => ['nullable', 'string', 'max:190'],
            'source' => ['nullable', 'string', 'max:190'],
            'page' => ['nullable', 'string', 'max:512'],
            'visitor_type' => ['nullable', Rule::in(['new', 'returning'])],
        ]);
        $to = CarbonImmutable::parse($validated['to'] ?? now()->toDateString());
        $from = CarbonImmutable::parse($validated['from'] ?? $to->subDays(29)->toDateString());
        if ($from->diffInDays($to) > 364) {
            abort(422, 'Date range cannot exceed 365 days.');
        }

        return ['from' => $from, 'to' => $to, 'country_code' => $validated['country_code'] ?? null, 'device' => $validated['device'] ?? null, 'browser' => $validated['browser'] ?? null, 'operating_system' => $validated['operating_system'] ?? null, 'referrer_domain' => $validated['source'] ?? $validated['referrer_domain'] ?? null, 'page' => $validated['page'] ?? null, 'visitor_type' => $validated['visitor_type'] ?? null];
    }

    private function spreadsheetSafe(string $value): string
    {
        return preg_match('/^[\s]*[=+\-@]/', $value) ? "'".$value : $value;
    }
}
