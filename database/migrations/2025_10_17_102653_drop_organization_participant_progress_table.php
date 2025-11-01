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
        // Supprimer la table organization_participant_progress
        Schema::dropIfExists('organization_participant_progress');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la table organization_participant_progress si nécessaire
        Schema::create('organization_participant_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->unsignedBigInteger('organization_participant_id');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'failed'])->default('not_started');
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_participant_id', 'course_id']);
            $table->index(['organization_id', 'status']);
            
            // Ajouter la contrainte de clé étrangère avec un nom plus court
            $table->foreign('organization_participant_id', 'org_part_progress_fk')
                  ->references('id')
                  ->on('organization_participants')
                  ->onDelete('cascade');
        });
    }
};
