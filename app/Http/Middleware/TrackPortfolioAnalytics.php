<?php

namespace App\Http\Middleware;

use App\Services\PortfolioAnalytics;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackPortfolioAnalytics
{
    public function __construct(private readonly PortfolioAnalytics $analytics) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $agent = (string) $request->userAgent();
        $isBot = preg_match('/bot|crawl|spider|slurp|headless|preview|fetch|curl|wget|python-requests|httpclient|lighthouse|pagespeed|uptimerobot|validator|facebookexternalhit|twitterbot|linkedinbot|discordbot|slackbot|google-inspectiontool|googleother|bingpreview|feedfetcher|libwww-perl/i', $agent) === 1;
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200 || ! Str::contains(strtolower((string) $response->headers->get('Content-Type')), 'text/html') || $isBot) {
            return $response;
        }

        $content = $response->getContent();
        if (is_string($content) && preg_match('/<title[^>]*>(.*?)<\/title>/is', $content, $match) === 1) {
            $request->attributes->set('analytics_page_title', html_entity_decode(strip_tags($match[1]), ENT_QUOTES | ENT_HTML5));
        }
        $tracking = $this->analytics->recordPageView($request);
        if ($tracking) {
            $secure = $request->isSecure();
            $response->headers->setCookie(cookie('analytics_visitor', $request->attributes->get('analytics_tracking')['visitor_key'], 60 * 24 * 30 * 13, '/', null, $secure, true, false, 'Lax'));
            $response->headers->setCookie(cookie('analytics_session', $request->attributes->get('analytics_tracking')['session_key'], 30, '/', null, $secure, true, false, 'Lax'));
        }

        return $response;
    }
}
