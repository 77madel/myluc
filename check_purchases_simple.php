<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vérification Tables d'Achats ===\n\n";

try {
    // Vérifier la structure de la table purchases
    echo "1. Structure de la table 'purchases':\n";
    $columns = DB::select("DESCRIBE purchases");
    foreach ($columns as $column) {
        echo "  - {$column->Field}: {$column->Type}\n";
    }
    echo "\n";
    
    // Vérifier les données dans purchases
    $purchases = DB::table('purchases')->get();
    echo "2. Nombre d'achats dans 'purchases': " . $purchases->count() . "\n";
    
    if ($purchases->count() > 0) {
        echo "Dernier achat:\n";
        $lastPurchase = $purchases->last();
        foreach ($lastPurchase as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
    }
    echo "\n";
    
    // Vérifier la table organization_course_purchases
    $orgPurchases = DB::table('organization_course_purchases')->get();
    echo "3. Nombre d'achats dans 'organization_course_purchases': " . $orgPurchases->count() . "\n";
    
    if ($orgPurchases->count() > 0) {
        echo "Dernier achat organisation:\n";
        $lastOrgPurchase = $orgPurchases->last();
        foreach ($lastOrgPurchase as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
