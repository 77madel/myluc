<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained('webinars')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('registration_token')->unique();

            // Registration Details
            $table->enum('status', ['registered', 'confirmed', 'attended', 'no_show', 'cancelled'])->default('registered');
            $table->datetime('registered_at');
            $table->datetime('confirmed_at')->nullable();
            $table->datetime('attended_at')->nullable();

            // Payment Information
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            // Communication Preferences
            $table->boolean('email_reminders')->default(true);
            $table->boolean('sms_reminders')->default(false);
            $table->json('custom_fields')->nullable(); // For additional registration data

            // Platform Integration
            $table->string('platform_user_id')->nullable(); // ID in the webinar platform
            $table->text('platform_join_url')->nullable();
            $table->boolean('platform_access_granted')->default(false);

            // Attendance Tracking
            $table->datetime('join_time')->nullable();
            $table->datetime('leave_time')->nullable();
            $table->integer('attendance_duration_minutes')->default(0);
            $table->boolean('was_present')->default(false);

            // Feedback
            $table->integer('rating')->nullable(); // 1-5 stars
            $table->text('feedback')->nullable();
            $table->text('suggestions')->nullable();

            $table->timestamps();

            // Indexes
            $table->unique(['webinar_id', 'user_id']);
            $table->index(['webinar_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['payment_status', 'registered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_registrations');
    }
};

