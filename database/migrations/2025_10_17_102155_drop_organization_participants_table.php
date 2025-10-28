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
        // Supprimer d'abord la contrainte de clé étrangère depuis organization_participant_progress
        Schema::table('organization_participant_progress', function (Blueprint $table) {
            $table->dropForeign('org_part_progress_fk');
        });

        // Maintenant supprimer la table organization_participants
        Schema::dropIfExists('organization_participants');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la table organization_participants si nécessaire
        Schema::create('organization_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('enrollment_link_id')->nullable()->constrained('organization_enrollment_links')->onDelete('set null');
            $table->string('department')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamps();
            $table->unique(['organization_id', 'user_id']);
            $table->index(['organization_id', 'status']);
        });
    }
};
