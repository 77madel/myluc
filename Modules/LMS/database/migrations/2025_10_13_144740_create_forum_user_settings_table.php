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
        Schema::create('forum_user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->boolean('email_on_reply')->default(true);
            $table->boolean('email_on_mention')->default(true);
            $table->boolean('email_on_new_post')->default(false);
            $table->boolean('platform_notification')->default(true);
            $table->enum('email_frequency', ['realtime', 'daily', 'weekly'])->default('realtime');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_user_settings');
    }
};
