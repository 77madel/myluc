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
        Schema::create('forum_attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable'); // forum_posts ou forum_post_replies
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // document, image, video, link
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable(); // en bytes
            $table->string('url')->nullable(); // Pour les liens externes
            $table->timestamps();

           // $table->index(['attachable_type', 'attachable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_attachments');
    }
};

