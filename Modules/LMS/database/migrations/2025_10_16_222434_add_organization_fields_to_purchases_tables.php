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
        // Ajouter seulement les colonnes manquantes à purchase_details
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('set null');
            $table->foreignId('enrollment_link_id')->nullable()->constrained('organization_enrollment_links')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les colonnes de purchase_details
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['enrollment_link_id']);
            $table->dropColumn(['organization_id', 'enrollment_link_id']);
        });
    }
};
