<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test OrderRepository avec Achat Organisation ===\n\n";

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
    
    // Simuler les données de session comme dans un vrai achat
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
    
    // Simuler un panier avec des cours
    \Modules\LMS\Classes\Cart::add([
        'id' => 1,
        'title' => 'Test Course',
        'price' => 7500,
        'type' => 'course'
    ]);
    
    echo "✅ Panier simulé créé\n";
    echo "Articles dans le panier: " . count(\Modules\LMS\Classes\Cart::get()) . "\n\n";
    
    // Tester OrderRepository::placeOrder
    echo "=== Test OrderRepository::placeOrder ===\n";
    
    $result = \Modules\LMS\Repositories\Order\OrderRepository::placeOrder('paydunya', [
        'token' => 'test_token_456',
        'status' => 'success'
    ]);
    
    echo "Résultat OrderRepository:\n";
    echo "Order ID: " . $result['order_id'] . "\n";
    echo "Payment Method: " . $result['payment_method'] . "\n";
    echo "Order Status: " . $result['order_status'] . "\n\n";
    
    // Vérifier les résultats
    echo "=== Vérification des Résultats ===\n";
    
    // Vérifier la table purchases
    $purchases = DB::table('purchases')->orderBy('id', 'desc')->first();
    echo "Dernier achat dans 'purchases':\n";
    echo "  ID: {$purchases->id}\n";
    echo "  User ID: {$purchases->user_id}\n";
    echo "  Amount: {$purchases->total_amount}\n";
    echo "  Status: {$purchases->status}\n\n";
    
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
    
    echo "🎉 Test OrderRepository réussi !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
