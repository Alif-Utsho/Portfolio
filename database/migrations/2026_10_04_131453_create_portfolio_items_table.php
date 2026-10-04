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
        Schema::create('portfolio_items', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 32)->index();
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->string('eyebrow')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->json('technologies')->nullable();
            $table->json('metadata')->nullable();
            $table->string('url')->nullable();
            $table->string('secondary_url')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
