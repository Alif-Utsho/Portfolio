<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('analytics:sessions:close')]
#[Description('Close analytics sessions inactive for at least 30 minutes')]
class CloseInactiveAnalyticsSessions extends Command
{
    public function handle(): int
    {
        $closed = DB::table('analytics_sessions')
            ->whereNull('ended_at')
            ->where('last_activity_at', '<=', now()->subMinutes(30))
            ->update(['ended_at' => DB::raw('last_activity_at'), 'updated_at' => now()]);

        $this->info('Closed '.$closed.' inactive analytics sessions.');

        return self::SUCCESS;
    }
}
