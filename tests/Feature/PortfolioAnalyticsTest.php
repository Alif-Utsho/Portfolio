<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use App\Models\User;
use App\Services\PortfolioAnalytics;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PortfolioAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_public_html_visits_create_anonymous_session_and_page_view(): void
    {
        $project = PortfolioItem::query()->create(['type' => 'project', 'title' => 'Measured project', 'slug' => 'measured-project', 'is_published' => true]);
        $response = $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/123.0 Safari/537.36')
            ->get('/projects/measured-project?utm_source=mail&utm_campaign=spring&private_token=must-not-be-saved');

        $response->assertOk()->assertCookie('analytics_visitor')->assertCookie('analytics_session');
        $this->assertDatabaseCount('analytics_visitors', 1);
        $this->assertDatabaseCount('analytics_sessions', 1);
        $this->assertDatabaseHas('analytics_sessions', ['device' => 'desktop', 'browser' => 'Chrome', 'operating_system' => 'Windows', 'utm_source' => 'mail', 'utm_campaign' => 'spring']);
        $this->assertDatabaseHas('analytics_sessions', ['country_code' => null, 'city' => null]);
        $this->assertDatabaseHas('analytics_page_views', ['path' => '/projects/measured-project', 'project_id' => $project->id]);
        $session = DB::table('analytics_sessions')->first();
        $this->assertStringNotContainsString('private_token', json_encode($session));
        $this->assertStringNotContainsString('Mozilla', json_encode($session));
        $this->assertArrayNotHasKey('ip_address', (array) $session);
    }

    public function test_admin_routes_bots_and_opted_out_browsers_do_not_create_views(): void
    {
        $this->get('/admin/login')->assertOk();
        $this->assertDatabaseCount('analytics_page_views', 0);

        $this->withHeader('User-Agent', 'Googlebot/2.1 (+http://www.google.com/bot.html)')->get('/')->assertOk();
        $this->withUnencryptedCookies(['analytics_opt_out' => '1'])->get('/')->assertOk();
        $this->assertDatabaseCount('analytics_visitors', 0);
        $this->assertDatabaseCount('analytics_page_views', 0);
    }

    public function test_opt_out_endpoint_sets_a_persistent_browser_preference(): void
    {
        $response = $this->post(route('analytics.opt-out'));
        $this->assertSame('1', $response->getCookie('analytics_opt_out', false)->getValue());

        $this->withUnencryptedCookies(['analytics_opt_out' => '1'])->get('/')->assertOk();
        $this->assertDatabaseCount('analytics_page_views', 0);
        $this->assertDatabaseCount('analytics_visitors', 0);
    }

    public function test_returning_visitor_reuses_visitor_and_inactive_session_is_replaced(): void
    {
        CarbonImmutable::setTestNow('2026-10-04 10:00:00');
        $this->get('/')->assertOk();
        $visitor = DB::table('analytics_visitors')->first();
        $firstSession = DB::table('analytics_sessions')->first();
        $staleActivity = now()->subMinutes(31);
        DB::table('analytics_sessions')->where('id', $firstSession->id)->update(['last_activity_at' => $staleActivity]);
        CarbonImmutable::setTestNow('2026-10-04 10:05:00');

        $this->withCookie('analytics_visitor', $visitor->visitor_key)->withCookie('analytics_session', $firstSession->session_key)->get('/projects')->assertOk();
        $this->assertDatabaseCount('analytics_visitors', 1);
        $this->assertDatabaseCount('analytics_sessions', 2);
        $this->assertDatabaseHas('analytics_sessions', ['id' => $firstSession->id, 'ended_at' => $staleActivity->toDateTimeString()]);
        $this->assertDatabaseHas('analytics_sessions', ['id' => $firstSession->id + 1, 'is_returning' => true]);
        $summary = app(PortfolioAnalytics::class)->overview(CarbonImmutable::parse('2026-10-04'), CarbonImmutable::parse('2026-10-04'));
        $this->assertSame(1, $summary['visitors']);
        $this->assertSame(2, $summary['sessions']);
        $this->assertSame(2, $summary['page_views']);
        $this->assertSame(1, $summary['new_visitors']);
        $this->assertSame(1, $summary['returning_visitors']);
        CarbonImmutable::setTestNow();
    }

    public function test_event_endpoint_accepts_only_known_events_and_does_not_store_contact_values(): void
    {
        $this->get('/')->assertOk();
        $session = DB::table('analytics_sessions')->first();
        $this->withCredentials()->withCookie('analytics_session', $session->session_key)->postJson(route('analytics.events'), [
            'event' => 'contact_submit', 'path' => '/#contact', 'name' => 'Private visitor name', 'email' => 'private@example.com', 'message' => 'private form content',
        ])->assertOk()->assertJson(['recorded' => true]);
        $this->assertDatabaseHas('analytics_events', ['event_name' => 'contact_submit', 'path' => '/']);
        $this->assertStringNotContainsString('private@example.com', DB::table('analytics_events')->value('properties') ?? '');
        $this->withCredentials()->withCookie('analytics_session', $session->session_key)->postJson(route('analytics.events'), ['event' => 'client_custom_event', 'path' => '/'])->assertUnprocessable();
    }

    public function test_page_duration_event_updates_the_matching_page_view(): void
    {
        $this->get('/')->assertOk();
        $session = DB::table('analytics_sessions')->first();
        $this->withCredentials()->withCookie('analytics_session', $session->session_key)->postJson(route('analytics.events'), [
            'event' => 'page_duration', 'path' => '/', 'duration' => 47,
        ])->assertOk()->assertJson(['recorded' => true]);

        $this->assertDatabaseHas('analytics_page_views', ['analytics_session_id' => $session->id, 'path' => '/', 'duration_seconds' => 47]);
    }

    public function test_successful_contact_submission_records_a_content_free_conversion_event(): void
    {
        $this->get('/')->assertOk();
        $session = DB::table('analytics_sessions')->first();
        $this->withCredentials()->withCookie('analytics_session', $session->session_key)->post(route('contact.store'), [
            'name' => 'Private Person', 'email' => 'private.person@example.com', 'subject' => 'Private subject', 'message' => 'Private message content', 'website' => '',
        ])->assertRedirect();

        $event = DB::table('analytics_events')->where('event_name', 'contact_submit')->first();
        $this->assertNotNull($event);
        $this->assertNull($event->properties);
        $storedAnalytics = json_encode(DB::table('analytics_events')->get()->toArray());
        $this->assertStringNotContainsString('private.person@example.com', $storedAnalytics);
        $this->assertStringNotContainsString('Private message content', $storedAnalytics);
    }

    public function test_analytics_pages_require_owner_and_show_real_zero_data_state(): void
    {
        $this->get(route('admin.analytics.overview'))->assertRedirect(route('admin.login'));
        $owner = User::factory()->create();
        $owner->forceFill(['is_admin' => true, 'admin_slot' => 'owner'])->save();
        $this->actingAs($owner)->get(route('admin.analytics.overview'))->assertOk()->assertSee('No visitor analytics yet.');
        $this->actingAs($owner)->get(route('admin.analytics.visitors'))->assertOk()->assertSee('No visitors were recorded');
    }

    public function test_csv_exports_filtered_anonymous_sessions_without_spreadsheet_formulas(): void
    {
        $this->get('/')->assertOk();
        $visitor = DB::table('analytics_visitors')->first();
        $session = DB::table('analytics_sessions')->first();
        DB::table('analytics_sessions')->where('id', $session->id)->update(['landing_path' => '=HYPERLINK("https://example.invalid")']);
        $owner = User::factory()->create();
        $owner->forceFill(['is_admin' => true, 'admin_slot' => 'owner'])->save();

        $response = $this->actingAs($owner)->get(route('admin.analytics.export', ['from' => now()->toDateString(), 'to' => now()->toDateString()]));
        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString("'=HYPERLINK", $response->streamedContent());
        $this->assertStringContainsString($visitor->visitor_key, $response->streamedContent());
    }

    public function test_daily_rollup_aggregates_real_rows_without_removing_detail(): void
    {
        CarbonImmutable::setTestNow('2026-10-03 14:00:00');
        $this->get('/')->assertOk();
        $this->artisan('analytics:rollup', ['--date' => '2026-10-03'])->assertExitCode(0);

        $this->assertDatabaseHas('analytics_daily_rollups', ['day' => '2026-10-03', 'dimension' => 'page', 'dimension_value' => '/', 'page_views' => 1, 'visitors' => 1]);
        $this->assertDatabaseCount('analytics_page_views', 1);
        CarbonImmutable::setTestNow();
    }

    public function test_inactivity_command_ends_sessions_after_thirty_minutes(): void
    {
        CarbonImmutable::setTestNow('2026-10-04 10:00:00');
        $this->get('/')->assertOk();
        $session = DB::table('analytics_sessions')->first();
        $lastActivity = now()->subMinutes(31);
        DB::table('analytics_sessions')->where('id', $session->id)->update(['last_activity_at' => $lastActivity]);

        $this->artisan('analytics:sessions:close')->assertExitCode(0);
        $this->assertDatabaseHas('analytics_sessions', ['id' => $session->id, 'ended_at' => $lastActivity->toDateTimeString()]);
        CarbonImmutable::setTestNow();
    }
}
