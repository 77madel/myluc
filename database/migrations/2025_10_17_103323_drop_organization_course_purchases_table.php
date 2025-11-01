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
        // Supprimer la table organization_course_purchases
        Schema::dropIfExists('organization_course_purchases');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la table organization_course_purchases si nécessaire
        Schema::create('organization_course_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('enrollment_link_id')->nullable()->constrained('organization_enrollment_links')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('payment_status')->default('pending');
            $table->string('invoice_token')->nullable();
            $table->timestamp('purchase_date')->useCurrent();
            $table->timestamps();

            $table->unique(['organization_id', 'course_id']);
        });
    }
};
