<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Simulation d'Achat Réussi ===\n\n";

try {
    // Simuler l'authentification
    $user = \Modules\LMS\Models\User::find(34);
    if (!$user) {
        echo "❌ Utilisateur non trouvé\n";
        exit;
    }
    
    auth()->login($user);
    echo "✅ Utilisateur connecté: {$user->email}\n";
    
    // Simuler les données de session
    session([
        'amount' => 5000,
        'organization_id' => $user->organization->id,
        'course_id' => 1,
        'course_title' => 'Test Course'
    ]);
    
    echo "✅ Session simulée créée\n";
    echo "Montant: " . session('amount') . " XOF\n";
    echo "Organisation: " . session('organization_id') . "\n";
    echo "Cours: " . session('course_id') . "\n\n";
    
    // Simuler l'appel à purchaseSuccess
    $course = \Modules\LMS\Models\Courses\Course::find(1);
    if (!$course) {
        echo "❌ Cours non trouvé\n";
        exit;
    }
    
    echo "✅ Cours trouvé: {$course->title}\n\n";
    
    // Créer le contrôleur et appeler purchaseSuccess
    $controller = new \Modules\LMS\Http\Controllers\Organization\CourseController(
        new \Modules\LMS\Services\OrganizationEnrollmentService()
    );
    
    $request = new \Illuminate\Http\Request();
    $response = $controller->purchaseSuccess($request, $course);
    
    echo "✅ Méthode purchaseSuccess exécutée\n";
    
    // Vérifier les résultats
    $purchases = \Modules\LMS\Models\Auth\OrganizationCoursePurchase::all();
    echo "\nNombre d'achats après simulation: " . $purchases->count() . "\n";
    
    $links = \Modules\LMS\Models\Auth\OrganizationEnrollmentLink::all();
    echo "Nombre de liens d'inscription: " . $links->count() . "\n";
    
    if ($purchases->count() > 0) {
        echo "✅ Achat enregistré avec succès !\n";
        $purchase = $purchases->first();
        echo "ID: {$purchase->id}\n";
        echo "Organisation: {$purchase->organization_id}\n";
        echo "Cours: {$purchase->course_id}\n";
        echo "Montant: {$purchase->amount}\n";
        echo "Statut: {$purchase->payment_status}\n";
    } else {
        echo "❌ Aucun achat enregistré\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
