<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->string('banner')->nullable();

            // Scheduling
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->string('timezone')->default('UTC');
            $table->integer('duration_minutes')->default(60);

            // Platform Integration
            $table->enum('platform', ['zoom', 'teams', 'google_meet', 'custom', 'other'])->default('zoom');
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->text('meeting_url')->nullable();
            $table->text('join_url')->nullable();
            $table->text('recording_url')->nullable();

            // Capacity and Registration
            $table->integer('max_participants')->default(100);
            $table->integer('current_participants')->default(0);
            $table->boolean('registration_required')->default(true);
            $table->boolean('registration_open')->default(true);
            $table->datetime('registration_deadline')->nullable();

            // Pricing
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('is_free')->default(true);
            $table->string('currency', 3)->default('USD');

            // Status and Settings
            $table->enum('status', ['draft', 'published', 'live', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_live')->default(false);
            $table->boolean('allow_recording')->default(true);
            $table->boolean('allow_chat')->default(true);
            $table->boolean('allow_questions')->default(true);
            $table->boolean('allow_screen_sharing')->default(true);

            // Instructor/Organization
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');

            // SEO and Meta
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();

            // Analytics
            $table->integer('views')->default(0);
            $table->integer('registrations')->default(0);
            $table->integer('attendees')->default(0);
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('total_ratings')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'start_date']);
            $table->index(['instructor_id', 'start_date']);
            $table->index(['organization_id', 'start_date']);
            $table->index(['category_id', 'start_date']);
            $table->index(['is_featured', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinars');
    }
};





