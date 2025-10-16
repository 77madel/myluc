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
        Schema::create('forum_search_index', function (Blueprint $table) {
            $table->id();
            $table->morphs('searchable'); // forum_posts ou forum_post_replies
            $table->text('keywords'); // Mots-clés extraits
            $table->timestamps();

            // Index full-text pour recherche rapide
            $table->fullText(['keywords']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_search_index');
    }
};
