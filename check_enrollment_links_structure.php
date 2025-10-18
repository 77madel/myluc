<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Structure de organization_enrollment_links ===\n\n";

try {
    $columns = DB::select("DESCRIBE organization_enrollment_links");
    foreach ($columns as $column) {
        echo "  - {$column->Field}: {$column->Type}\n";
    }
    
    echo "\n=== Données existantes ===\n";
    $links = DB::table('organization_enrollment_links')->get();
    echo "Nombre de liens: " . $links->count() . "\n";
    
    if ($links->count() > 0) {
        foreach ($links as $link) {
            echo "- ID: {$link->id}, Nom: {$link->name}, Slug: {$link->slug}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
