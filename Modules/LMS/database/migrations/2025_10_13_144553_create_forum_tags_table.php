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
        Schema::create('forum_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#3B82F6');
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });

        // Table pivot pour posts-tags
        Schema::create('forum_post_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_post_id');
            $table->unsignedBigInteger('forum_tag_id');
            $table->timestamps();

            $table->foreign('forum_post_id')->references('id')->on('forum_posts')->onDelete('cascade');
            $table->foreign('forum_tag_id')->references('id')->on('forum_tags')->onDelete('cascade');

            $table->unique(['forum_post_id', 'forum_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_post_tags');
        Schema::dropIfExists('forum_tags');
    }
};
