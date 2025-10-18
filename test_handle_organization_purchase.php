<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test handleOrganizationPurchase ===\n\n";

try {
    // Simuler l'authentification
    $user = \Modules\LMS\Models\User::find(34);
    if (!$user) {
        echo "❌ Utilisateur non trouvé\n";
        exit;
    }
    
    auth()->login($user);
    echo "✅ Utilisateur connecté: {$user->email}\n";
    
    $organization = $user->organization;
    echo "✅ Organisation: {$organization->name} (ID: {$organization->id})\n\n";
    
    // Simuler les données de session
    session([
        'type' => 'course_purchase',
        'course_id' => 1,
        'organization_id' => $organization->id,
        'amount' => 7500,
        'course_title' => 'Test Course Real'
    ]);
    
    echo "✅ Session simulée créée\n";
    echo "Type: " . session('type') . "\n";
    echo "Course ID: " . session('course_id') . "\n";
    echo "Amount: " . session('amount') . "\n\n";
    
    // Appeler directement handleOrganizationPurchase
    echo "=== Test handleOrganizationPurchase ===\n";
    
    \Modules\LMS\Repositories\Order\OrderRepository::handleOrganizationPurchase(999, 'paydunya');
    
    echo "✅ handleOrganizationPurchase exécuté\n\n";
    
    // Vérifier les résultats
    echo "=== Vérification des Résultats ===\n";
    
    // Vérifier la table organization_course_purchases
    $orgPurchases = \Modules\LMS\Models\Auth\OrganizationCoursePurchase::orderBy('id', 'desc')->get();
    echo "Achats dans 'organization_course_purchases': " . $orgPurchases->count() . "\n";
    
    if ($orgPurchases->count() > 0) {
        $lastOrgPurchase = $orgPurchases->first();
        echo "Dernier achat organisation:\n";
        echo "  ID: {$lastOrgPurchase->id}\n";
        echo "  Organisation: {$lastOrgPurchase->organization_id}\n";
        echo "  Cours: {$lastOrgPurchase->course_id}\n";
        echo "  Montant: {$lastOrgPurchase->amount}\n";
        echo "  Statut: {$lastOrgPurchase->status}\n";
        echo "  Référence: {$lastOrgPurchase->payment_reference}\n\n";
    }
    
    // Vérifier les liens d'inscription
    $links = \Modules\LMS\Models\Auth\OrganizationEnrollmentLink::orderBy('id', 'desc')->get();
    echo "Liens d'inscription: " . $links->count() . "\n";
    
    if ($links->count() > 0) {
        $lastLink = $links->first();
        echo "Dernier lien:\n";
        echo "  ID: {$lastLink->id}\n";
        echo "  Nom: {$lastLink->name}\n";
        echo "  Slug: {$lastLink->slug}\n";
        echo "  URL: " . url('/enroll/' . $lastLink->slug) . "\n\n";
    }
    
    echo "🎉 Test handleOrganizationPurchase réussi !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
