<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('analytics_daily_rollups', function (Blueprint $table): void {
            $table->unsignedInteger('visitors')->default(0)->after('dimension_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analytics_daily_rollups', function (Blueprint $table): void {
            $table->dropColumn('visitors');
        });
    }
};
