<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vérification Table Purchases ===\n\n";

try {
    // Vérifier la table purchases générale
    $purchases = DB::table('purchases')->get();
    echo "Nombre d'achats dans la table 'purchases': " . $purchases->count() . "\n\n";
    
    if ($purchases->count() > 0) {
        echo "Détails des achats:\n";
        foreach ($purchases as $purchase) {
            echo "- ID: {$purchase->id}\n";
            echo "  User ID: {$purchase->user_id}\n";
            echo "  Course ID: {$purchase->course_id}\n";
            echo "  Amount: {$purchase->amount}\n";
            echo "  Status: {$purchase->status}\n";
            echo "  Date: {$purchase->created_at}\n\n";
        }
    }
    
    // Vérifier la table organization_course_purchases
    $orgPurchases = DB::table('organization_course_purchases')->get();
    echo "Nombre d'achats dans la table 'organization_course_purchases': " . $orgPurchases->count() . "\n\n";
    
    if ($orgPurchases->count() > 0) {
        echo "Détails des achats organisation:\n";
        foreach ($orgPurchases as $purchase) {
            echo "- ID: {$purchase->id}\n";
            echo "  Organization ID: {$purchase->organization_id}\n";
            echo "  Course ID: {$purchase->course_id}\n";
            echo "  Amount: {$purchase->amount}\n";
            echo "  Status: {$purchase->payment_status}\n";
            echo "  Date: {$purchase->created_at}\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
