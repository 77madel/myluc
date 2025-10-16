<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_post_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_post_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable(); // Pour les réponses imbriquées
            $table->text('content');
            $table->boolean('is_solution')->default(false);
            $table->integer('likes')->default(0);
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('forum_post_id')->references('id')->on('forum_posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('forum_post_replies')->onDelete('cascade');

            $table->index(['forum_post_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_post_replies');
    }
};
