<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Migration de l'Achat Existant ===\n\n";

try {
    // Récupérer l'achat existant de la table purchases
    $purchase = DB::table('purchases')
        ->join('users', 'purchases.user_id', '=', 'users.id')
        ->where('users.userable_type', 'Modules\LMS\Models\Auth\Organization')
        ->where('purchases.status', 'success')
        ->where('purchases.id', 10) // L'achat que nous avons vu
        ->select('purchases.*', 'users.userable_id as organization_id')
        ->first();
    
    if (!$purchase) {
        echo "❌ Achat non trouvé\n";
        exit;
    }
    
    echo "Achat trouvé:\n";
    echo "  ID: {$purchase->id}\n";
    echo "  Organisation: {$purchase->organization_id}\n";
    echo "  Montant: {$purchase->total_amount}\n";
    echo "  Date: {$purchase->created_at}\n\n";
    
    // Vérifier si déjà migré
    $existing = DB::table('organization_course_purchases')
        ->where('organization_id', $purchase->organization_id)
        ->where('amount', $purchase->total_amount)
        ->first();
    
    if ($existing) {
        echo "⚠️ Achat déjà migré (ID: {$existing->id})\n";
        exit;
    }
    
    // Créer un lien d'inscription
    $enrollmentLink = \Modules\LMS\Models\Auth\OrganizationEnrollmentLink::create([
        'organization_id' => $purchase->organization_id,
        'name' => 'Lien d\'inscription - Cours acheté',
        'slug' => \Illuminate\Support\Str::random(10),
        'description' => 'Lien créé pour le cours acheté',
        'valid_until' => now()->addYear(),
        'max_enrollments' => null,
        'current_enrollments' => 0,
        'status' => 'active',
    ]);
    
    echo "✅ Lien d'inscription créé (ID: {$enrollmentLink->id})\n";
    
    // Enregistrer dans organization_course_purchases
    $orgPurchase = \Modules\LMS\Models\Auth\OrganizationCoursePurchase::create([
        'organization_id' => $purchase->organization_id,
        'course_id' => 1, // Par défaut
        'amount' => $purchase->total_amount,
        'purchase_date' => $purchase->created_at,
        'enrollment_link_id' => $enrollmentLink->id,
        'status' => 'completed',
    ]);
    
    echo "✅ Achat migré avec succès (ID: {$orgPurchase->id})\n\n";
    
    // Vérifier les résultats
    $totalPurchases = DB::table('organization_course_purchases')->count();
    $totalLinks = DB::table('organization_enrollment_links')->count();
    
    echo "=== Résumé ===\n";
    echo "Total achats organisation: {$totalPurchases}\n";
    echo "Total liens d'inscription: {$totalLinks}\n";
    
    // Afficher les détails
    $purchases = DB::table('organization_course_purchases')->get();
    echo "\nDétails des achats:\n";
    foreach ($purchases as $p) {
        echo "- ID: {$p->id}, Org: {$p->organization_id}, Montant: {$p->amount}, Statut: {$p->status}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}





