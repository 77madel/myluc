<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Migration des Achats vers Table Organisation ===\n\n";

try {
    // Récupérer les achats de la table purchases pour les utilisateurs organisation
    $purchases = DB::table('purchases')
        ->join('users', 'purchases.user_id', '=', 'users.id')
        ->where('users.userable_type', 'Modules\LMS\Models\Auth\Organization')
        ->where('purchases.status', 'success')
        ->select('purchases.*', 'users.userable_id as organization_id')
        ->get();
    
    echo "Nombre d'achats organisation trouvés: " . $purchases->count() . "\n\n";
    
    if ($purchases->count() > 0) {
        foreach ($purchases as $purchase) {
            echo "Traitement de l'achat ID: {$purchase->id}\n";
            echo "  Organisation: {$purchase->organization_id}\n";
            echo "  Montant: {$purchase->total_amount}\n";
            echo "  Date: {$purchase->created_at}\n";
            
            // Vérifier si l'achat existe déjà dans organization_course_purchases
            $existingPurchase = DB::table('organization_course_purchases')
                ->where('organization_id', $purchase->organization_id)
                ->where('amount', $purchase->total_amount)
                ->where('purchase_date', $purchase->created_at)
                ->first();
            
            if ($existingPurchase) {
                echo "  ⚠️ Achat déjà migré (ID: {$existingPurchase->id})\n\n";
                continue;
            }
            
            // Créer un lien d'inscription pour cet achat
            $enrollmentLink = \Modules\LMS\Models\Auth\OrganizationEnrollmentLink::create([
                'organization_id' => $purchase->organization_id,
                'name' => 'Lien d\'inscription migré',
                'slug' => \Illuminate\Support\Str::random(10),
                'description' => 'Lien créé lors de la migration des achats',
                'valid_until' => now()->addYear(),
                'max_enrollments' => null,
                'current_enrollments' => 0,
                'status' => 'active',
            ]);
            
            // Enregistrer dans organization_course_purchases
            $orgPurchase = \Modules\LMS\Models\Auth\OrganizationCoursePurchase::create([
                'organization_id' => $purchase->organization_id,
                'course_id' => 1, // Par défaut, on peut ajuster selon vos besoins
                'amount' => $purchase->total_amount,
                'purchase_date' => $purchase->created_at,
                'enrollment_link_id' => $enrollmentLink->id,
                'payment_status' => 'completed',
            ]);
            
            echo "  ✅ Migré avec succès (ID: {$orgPurchase->id})\n";
            echo "  ✅ Lien d'inscription créé (ID: {$enrollmentLink->id})\n\n";
        }
        
        echo "=== Résumé ===\n";
        $totalOrgPurchases = DB::table('organization_course_purchases')->count();
        $totalEnrollmentLinks = DB::table('organization_enrollment_links')->count();
        
        echo "Total achats organisation: {$totalOrgPurchases}\n";
        echo "Total liens d'inscription: {$totalEnrollmentLinks}\n";
        
    } else {
        echo "Aucun achat organisation trouvé à migrer.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}





