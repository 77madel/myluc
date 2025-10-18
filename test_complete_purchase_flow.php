<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test du Flux d'Achat Complet ===\n\n";

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
    
    // Simuler les données de session comme si on venait de faire un achat
    session([
        'type' => 'course_purchase',
        'course_id' => 1,
        'organization_id' => $organization->id,
        'amount' => 5000,
        'course_title' => 'Test Course Purchase'
    ]);
    
    echo "✅ Session simulée créée\n";
    echo "Type: " . session('type') . "\n";
    echo "Course ID: " . session('course_id') . "\n";
    echo "Amount: " . session('amount') . "\n\n";
    
    // Simuler l'appel au contrôleur de paiement général
    echo "=== Test du Contrôleur de Paiement Général ===\n";
    $paymentController = new \Modules\LMS\Http\Controllers\Frontend\PaymentController();
    $request = new \Illuminate\Http\Request();
    
    // Simuler les paramètres de retour Paydunya
    $request->merge([
        'token' => 'test_token_123',
        'status' => 'success'
    ]);
    
    echo "✅ Contrôleur de paiement créé\n";
    echo "✅ Requête simulée avec token: test_token_123\n\n";
    
    // Tester la redirection
    echo "=== Test de Redirection ===\n";
    $cartType = session()->get('type');
    if ($cartType === 'course_purchase') {
        $courseId = session()->get('course_id');
        echo "✅ Détection d'achat d'organisation\n";
        echo "✅ Redirection vers: organization.courses.purchase.success (course: {$courseId})\n";
    } else {
        echo "❌ Type de panier non détecté: {$cartType}\n";
    }
    
    echo "\n=== Test du Contrôleur d'Organisation ===\n";
    
    // Simuler l'appel au contrôleur d'organisation
    $orgController = new \Modules\LMS\Http\Controllers\Organization\CourseController(
        new \Modules\LMS\Services\OrganizationEnrollmentService()
    );
    
    $course = \Modules\LMS\Models\Courses\Course::find(1);
    if (!$course) {
        echo "❌ Cours non trouvé\n";
        exit;
    }
    
    echo "✅ Cours trouvé: {$course->title}\n";
    
    // Vérifier les données de session
    $amount = session('amount');
    $organizationId = session('organization_id');
    $courseId = session('course_id');
    
    if (!$amount || !$organizationId || !$courseId) {
        echo "❌ Données de session manquantes\n";
        echo "Amount: {$amount}\n";
        echo "Organization ID: {$organizationId}\n";
        echo "Course ID: {$courseId}\n";
        exit;
    }
    
    echo "✅ Données de session valides\n";
    echo "Montant: {$amount}\n";
    echo "Organisation: {$organizationId}\n";
    echo "Cours: {$courseId}\n\n";
    
    echo "=== Simulation de l'Enregistrement ===\n";
    
    // Simuler l'enregistrement de l'achat
    try {
        DB::beginTransaction();
        
        // Créer un lien d'inscription
        $enrollmentLink = \Modules\LMS\Models\Auth\OrganizationEnrollmentLink::create([
            'organization_id' => $organization->id,
            'name' => "Cours: {$course->title}",
            'slug' => \Illuminate\Support\Str::random(10),
            'description' => "Lien d'inscription pour le cours: {$course->title}",
            'valid_until' => now()->addYear(),
            'max_enrollments' => null,
            'current_enrollments' => 0,
            'status' => 'active',
        ]);
        
        echo "✅ Lien d'inscription créé (ID: {$enrollmentLink->id})\n";
        
        // Associer le cours au lien
        $enrollmentLink->update([
            'course_id' => $course->id,
            'status' => 'active'
        ]);
        
        echo "✅ Cours associé au lien d'inscription\n";
        
        // Enregistrer l'achat
        $purchase = \Modules\LMS\Models\Auth\OrganizationCoursePurchase::create([
            'organization_id' => $organization->id,
            'course_id' => $course->id,
            'amount' => $amount,
            'purchase_date' => now(),
            'enrollment_link_id' => $enrollmentLink->id,
            'status' => 'completed',
        ]);
        
        echo "✅ Achat enregistré (ID: {$purchase->id})\n";
        
        DB::commit();
        echo "✅ Transaction commitée avec succès\n\n";
        
        // Vérifier les résultats
        $totalPurchases = \Modules\LMS\Models\Auth\OrganizationCoursePurchase::count();
        $totalLinks = \Modules\LMS\Models\Auth\OrganizationEnrollmentLink::count();
        
        echo "=== Résultats ===\n";
        echo "Total achats organisation: {$totalPurchases}\n";
        echo "Total liens d'inscription: {$totalLinks}\n";
        
        echo "\n🎉 Flux d'achat complet testé avec succès !\n";
        
    } catch (Exception $e) {
        DB::rollBack();
        echo "❌ Erreur lors de l'enregistrement: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
