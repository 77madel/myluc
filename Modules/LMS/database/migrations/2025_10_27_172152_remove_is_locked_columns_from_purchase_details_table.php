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
        Schema::table('purchase_details', function (Blueprint $table) {
            // Supprimer seulement les colonnes qui existent
            if (Schema::hasColumn('purchase_details', 'is_locked')) {
                $table->dropColumn('is_locked');
            }
            if (Schema::hasColumn('purchase_details', 'locked_at')) {
                $table->dropColumn('locked_at');
            }
            if (Schema::hasColumn('purchase_details', 'lock_reason')) {
                $table->dropColumn('lock_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('status');
            $table->timestamp('locked_at')->nullable()->after('is_locked');
            $table->string('lock_reason')->nullable()->after('locked_at');
        });
    }
};
