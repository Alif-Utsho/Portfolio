<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_visitors', function (Blueprint $table): void {
            $table->id();
            $table->uuid('visitor_key')->unique();
            $table->timestamp('first_seen_at')->index();
            $table->timestamp('last_seen_at')->index();
            $table->timestamps();
        });

        Schema::create('analytics_sessions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('session_key')->unique();
            $table->foreignId('visitor_id')->constrained('analytics_visitors')->cascadeOnDelete();
            $table->string('landing_path', 512);
            $table->string('referrer_domain', 190)->nullable()->index();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 150)->nullable();
            $table->string('utm_content', 150)->nullable();
            $table->string('utm_term', 150)->nullable();
            $table->string('device', 24)->default('unknown')->index();
            $table->string('browser', 40)->default('unknown')->index();
            $table->string('operating_system', 40)->default('unknown')->index();
            $table->char('country_code', 2)->nullable()->index();
            $table->string('country', 100)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->timestamp('started_at')->index();
            $table->timestamp('last_activity_at')->index();
            $table->timestamp('ended_at')->nullable()->index();
            $table->timestamps();
            $table->index(['visitor_id', 'started_at']);
            $table->index(['started_at', 'country_code', 'device']);
        });

        Schema::create('analytics_page_views', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('analytics_session_id')->constrained('analytics_sessions')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('portfolio_items')->nullOnDelete();
            $table->string('path', 512)->index();
            $table->string('page_title', 190)->nullable();
            $table->timestamp('viewed_at')->index();
            $table->unsignedSmallInteger('duration_seconds')->nullable();
            $table->timestamps();
            $table->index(['analytics_session_id', 'viewed_at']);
        });

        Schema::create('analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('analytics_session_id')->constrained('analytics_sessions')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('portfolio_items')->nullOnDelete();
            $table->string('event_name', 50)->index();
            $table->string('path', 512)->index();
            $table->json('properties')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
            $table->index(['event_name', 'occurred_at']);
        });

        Schema::create('analytics_daily_rollups', function (Blueprint $table): void {
            $table->id();
            $table->date('day');
            $table->string('dimension', 30);
            $table->string('dimension_value', 190);
            $table->unsignedInteger('page_views')->default(0);
            $table->unsignedInteger('events')->default(0);
            $table->unsignedInteger('sessions')->default(0);
            $table->timestamps();
            $table->unique(['day', 'dimension', 'dimension_value']);
            $table->index(['dimension', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_daily_rollups');
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('analytics_page_views');
        Schema::dropIfExists('analytics_sessions');
        Schema::dropIfExists('analytics_visitors');
    }
};
