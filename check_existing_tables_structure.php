<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Structure des Tables Existantes ===\n\n";

try {
    // Vérifier la structure de purchases
    echo "1. Structure de la table 'purchases':\n";
    $columns = DB::select("DESCRIBE purchases");
    foreach ($columns as $column) {
        echo "  - {$column->Field}: {$column->Type}\n";
    }
    echo "\n";
    
    // Vérifier la structure de purchase_details
    echo "2. Structure de la table 'purchase_details':\n";
    $columns = DB::select("DESCRIBE purchase_details");
    foreach ($columns as $column) {
        echo "  - {$column->Field}: {$column->Type}\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
