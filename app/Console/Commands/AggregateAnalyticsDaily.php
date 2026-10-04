<?php

namespace App\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateAnalyticsDaily extends Command
{
    protected $signature = 'analytics:rollup {--date= : Application-local date to aggregate (YYYY-MM-DD)}';

    protected $description = 'Aggregate daily analytics breakdowns without deleting detailed records';

    public function handle(): int
    {
        $day = CarbonImmutable::parse($this->option('date') ?: now()->subDay()->toDateString());
        $start = $day->startOfDay();
        $end = $day->endOfDay();
        $rows = [];
        $sessionDimensions = ['country' => 'country', 'device' => 'device', 'browser' => 'browser', 'operating_system' => 'operating_system', 'source' => 'referrer_domain', 'visitor_type' => 'is_returning'];
        foreach ($sessionDimensions as $dimension => $column) {
            $query = DB::table('analytics_sessions')->whereBetween('started_at', [$start, $end])->select($column)->selectRaw('COUNT(*) as total, COUNT(DISTINCT visitor_id) as visitors')->groupBy($column)->get();
            foreach ($query as $row) {
                $dimensionValue = $dimension === 'visitor_type' ? ($row->{$column} ? 'Returning' : 'New') : ($row->{$column} ?: ($dimension === 'source' ? 'Direct' : 'Unknown'));
                $rows[] = ['day' => $day->toDateString(), 'dimension' => $dimension, 'dimension_value' => $dimensionValue, 'visitors' => $row->visitors, 'sessions' => $row->total, 'page_views' => 0, 'events' => 0];
            }
        }
        foreach (['page' => ['analytics_page_views', 'path', 'viewed_at'], 'event' => ['analytics_events', 'event_name', 'occurred_at']] as $dimension => [$table, $column, $dateColumn]) {
            $query = DB::table($table)->join('analytics_sessions as s', 's.id', '=', $table.'.analytics_session_id')->whereBetween($dateColumn, [$start, $end])->select($table.'.'.$column)->selectRaw('COUNT(*) as total, COUNT(DISTINCT s.visitor_id) as visitors')->groupBy($table.'.'.$column)->get();
            foreach ($query as $row) {
                $rows[] = ['day' => $day->toDateString(), 'dimension' => $dimension, 'dimension_value' => $row->{$column}, 'visitors' => $row->visitors, 'sessions' => 0, 'page_views' => $dimension === 'page' ? $row->total : 0, 'events' => $dimension === 'event' ? $row->total : 0];
            }
        }
        DB::transaction(function () use ($day, $rows): void {
            DB::table('analytics_daily_rollups')->whereDate('day', $day)->delete();
            foreach ($rows as $row) {
                DB::table('analytics_daily_rollups')->insert(array_merge($row, ['created_at' => now(), 'updated_at' => now()]));
            }
        });
        $this->info('Aggregated '.$day->toDateString().' ('.$this->count($rows).' dimension rows).');

        return self::SUCCESS;
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function count(array $rows): int
    {
        return count($rows);
    }
}
