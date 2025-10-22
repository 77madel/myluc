<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar_platform_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // zoom, teams, google_meet, etc.
            $table->string('name'); // Display name
            $table->text('description')->nullable();

            // API Configuration
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->text('webhook_url')->nullable();
            $table->text('redirect_uri')->nullable();

            // Platform Settings
            $table->json('settings')->nullable(); // Platform-specific settings
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);

            // Rate Limiting
            $table->integer('rate_limit_per_hour')->default(100);
            $table->integer('rate_limit_per_day')->default(1000);
            $table->datetime('last_api_call')->nullable();
            $table->integer('api_calls_today')->default(0);

            // Features Support
            $table->boolean('supports_recording')->default(true);
            $table->boolean('supports_polling')->default(true);
            $table->boolean('supports_breakout_rooms')->default(false);
            $table->boolean('supports_waiting_room')->default(true);
            $table->boolean('supports_chat')->default(true);
            $table->boolean('supports_screen_sharing')->default(true);
            $table->boolean('supports_whiteboard')->default(false);

            // Webhook Configuration
            $table->string('webhook_secret')->nullable();
            $table->boolean('webhook_enabled')->default(false);
            $table->json('webhook_events')->nullable(); // Events to listen for

            // Status and Health
            $table->enum('status', ['active', 'inactive', 'error', 'maintenance'])->default('active');
            $table->text('last_error')->nullable();
            $table->datetime('last_successful_call')->nullable();
            $table->integer('success_rate')->default(100); // Percentage

            $table->timestamps();

            // Indexes
            $table->index(['platform', 'is_active']);
            $table->index(['is_default', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_platform_integrations');
    }
};

