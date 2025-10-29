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
        // Ajouter les champs démographiques à la table students
        Schema::table('students', function (Blueprint $table) {
            $table->integer('age')->nullable()->after('last_name');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('age');
            $table->string('profession', 100)->nullable()->after('gender');
        });
        
        // Ajouter les champs démographiques à la table instructors
        Schema::table('instructors', function (Blueprint $table) {
            $table->integer('age')->nullable()->after('last_name');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('age');
            $table->string('profession', 100)->nullable()->after('gender');
        });
        
        // Ajouter les champs démographiques à la table organizations (optionnel)
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('organization_type', 100)->nullable()->after('name'); // Type d'organisation
            $table->integer('employee_count')->nullable()->after('organization_type'); // Nombre d'employés
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['age', 'gender', 'profession']);
        });
        
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn(['age', 'gender', 'profession']);
        });
        
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['organization_type', 'employee_count']);
        });
    }
};
