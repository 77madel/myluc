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
        Schema::table('lms_conversations', function (Blueprint $table) {
            $table->foreign('last_message_id')->references('id')->on('lms_messages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_conversations', function (Blueprint $table) {
            $table->dropForeign(['last_message_id']);
        });
    }
};
