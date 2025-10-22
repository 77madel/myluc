<?php

/**
 * Script pour exécuter le seeder des cours d'organisation
 * 
 * Ce script crée :
 * - 2 cours avec des vidéos YouTube courtes (1-2 minutes)
 * - Des chapitres et leçons pour chaque cours
 * - Des prix payants (29.99€ et 19.99€)
 * - Une organisation et un instructeur
 * - Certification activée
 */

require_once 'vendor/autoload.php';

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

echo "🚀 Démarrage du seeder OrganizationCoursesSeeder...\n\n";

try {
    // Exécuter le seeder
    Artisan::call('db:seed', [
        '--class' => 'Modules\LMS\Database\Seeders\OrganizationCoursesSeeder'
    ]);
    
    echo "\n✅ Seeder exécuté avec succès !\n";
    echo "📚 2 cours créés avec chapitres et leçons\n";
    echo "🎥 Vidéos YouTube courtes (1-2 minutes)\n";
    echo "💰 Cours payants avec certification\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de l'exécution du seeder : " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Processus terminé !\n";



