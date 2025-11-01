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
        // Ajouter session_token à la table users
        Schema::table('users', function (Blueprint $table) {
            $table->string('session_token', 100)->nullable()->after('remember_me');
            $table->index('session_token');
        });

        // Ajouter session_token à la table admins
        Schema::table('admins', function (Blueprint $table) {
            $table->string('session_token', 100)->nullable()->after('password');
            $table->index('session_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['session_token']);
            $table->dropColumn('session_token');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropIndex(['session_token']);
            $table->dropColumn('session_token');
        });
    }
};
