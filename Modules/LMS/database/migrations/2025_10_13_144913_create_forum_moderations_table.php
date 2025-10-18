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
        Schema::create('forum_moderations', function (Blueprint $table) {
            $table->id();
            $table->morphs('moderatable'); // forum_posts ou forum_post_replies
            $table->unsignedBigInteger('moderator_id');
            $table->enum('action', ['approved', 'rejected', 'hidden', 'deleted', 'flagged']);
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('moderator_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['moderatable_type', 'moderatable_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_moderations');
    }
};

