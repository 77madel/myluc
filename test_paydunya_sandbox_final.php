<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Paydunya Sandbox Final ===\n\n";

try {
    // Vérifier les clés
    echo "1. Vérification des clés :\n";
    $masterKey = config('paydunya.master_key');
    $privateKey = config('paydunya.private_key');
    $token = config('paydunya.token');
    $testMode = config('paydunya.test_mode');
    
    echo "Master Key: " . ($masterKey ? 'Définie (' . substr($masterKey, 0, 10) . '...)' : 'NON DÉFINIE') . "\n";
    echo "Private Key: " . ($privateKey ? 'Définie (' . substr($privateKey, 0, 10) . '...)' : 'NON DÉFINIE') . "\n";
    echo "Token: " . ($token ? 'Définie (' . substr($token, 0, 10) . '...)' : 'NON DÉFINIE') . "\n";
    echo "Test Mode: " . ($testMode ? 'true' : 'false') . "\n";
    
    if (empty($masterKey) || empty($privateKey) || empty($token)) {
        echo "❌ Clés manquantes !\n";
        exit;
    }
    
    // Tester le service Paydunya
    echo "\n2. Test du service Paydunya :\n";
    
    // Simuler une session pour test
    session([
        'type' => 'course_purchase',
        'course_id' => 1,
        'organization_id' => 1,
        'amount' => 5000,
        'course_title' => 'Test Course'
    ]);
    
    echo "Session simulée créée\n";
    
    // Tester la création de paiement
    $result = \Modules\LMS\Services\Payment\PaydunyaService::makePayment();
    
    echo "Résultat du paiement :\n";
    echo "Status: " . ($result['status'] ?? 'N/A') . "\n";
    
    if (isset($result['status']) && $result['status'] === 'success') {
        echo "✅ Paiement initialisé avec succès !\n";
        echo "URL de paiement: " . ($result['checkout_url'] ?? 'N/A') . "\n";
        echo "Token: " . ($result['token'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Erreur lors de l'initialisation du paiement\n";
        echo "Message: " . ($result['message'] ?? 'N/A') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}

echo "\n=== Instructions ===\n";
echo "1. Si le test réussit, vous pouvez maintenant acheter des cours\n";
echo "2. Allez sur http://127.0.0.1:8000/org/courses\n";
echo "3. Cliquez sur 'Acheter' pour un cours\n";
echo "4. Vous serez redirigé vers Paydunya sandbox\n";
