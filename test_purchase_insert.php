<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Insertion Achat ===\n\n";

try {
    // Test d'insertion directe
    $result = DB::table('organization_course_purchases')->insert([
        'organization_id' => 11,
        'course_id' => 1,
        'amount' => 5000.00,
        'purchase_date' => now()->format('Y-m-d H:i:s'),
        'enrollment_link_id' => 1,
        'status' => 'completed',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    if ($result) {
        echo "✅ Insertion réussie avec DB::table\n";
        
        // Récupérer l'ID
        $lastId = DB::table('organization_course_purchases')->orderBy('id', 'desc')->first();
        echo "ID: " . $lastId->id . "\n";
    } else {
        echo "❌ Échec de l'insertion avec DB::table\n";
    }
    
    echo "\n=== Test avec Eloquent ===\n";
    
    // Test avec Eloquent
    $purchase = \Modules\LMS\Models\Auth\OrganizationCoursePurchase::create([
        'organization_id' => 11,
        'course_id' => 2,
        'amount' => 3000.00,
        'purchase_date' => now(),
        'enrollment_link_id' => 1,
        'status' => 'completed',
    ]);
    
    echo "✅ Insertion réussie avec Eloquent (ID: {$purchase->id})\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
