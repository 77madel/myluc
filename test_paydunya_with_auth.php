<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Paydunya avec Authentification ===\n\n";

try {
    // Simuler l'authentification
    $user = \Modules\LMS\Models\User::find(34); // Utilisateur organisation
    if (!$user) {
        echo "❌ Utilisateur organisation non trouvé\n";
        exit;
    }
    
    // Simuler la connexion
    auth()->login($user);
    echo "✅ Utilisateur authentifié : {$user->email}\n";
    
    // Vérifier les clés
    echo "\n1. Vérification des clés :\n";
    $masterKey = config('paydunya.master_key');
    $privateKey = config('paydunya.private_key');
    $token = config('paydunya.token');
    $testMode = config('paydunya.test_mode');
    
    echo "Master Key: " . ($masterKey ? 'Définie' : 'NON DÉFINIE') . "\n";
    echo "Private Key: " . ($privateKey ? 'Définie' : 'NON DÉFINIE') . "\n";
    echo "Token: " . ($token ? 'Définie' : 'NON DÉFINIE') . "\n";
    echo "Test Mode: " . ($testMode ? 'true' : 'false') . "\n";
    
    if (empty($masterKey) || empty($privateKey) || empty($token)) {
        echo "❌ Clés manquantes !\n";
        exit;
    }
    
    // Simuler une session pour test
    session([
        'type' => 'course_purchase',
        'course_id' => 1,
        'organization_id' => $user->organization->id,
        'amount' => 5000,
        'course_title' => 'Test Course'
    ]);
    
    echo "\n2. Session simulée créée\n";
    echo "Organisation: " . $user->organization->name . "\n";
    echo "Montant: 5000 XOF\n";
    
    // Tester la création de paiement
    echo "\n3. Test de création de paiement Paydunya :\n";
    $result = \Modules\LMS\Services\Payment\PaydunyaService::makePayment();
    
    echo "Résultat :\n";
    echo "Status: " . ($result['status'] ?? 'N/A') . "\n";
    
    if (isset($result['status']) && $result['status'] === 'success') {
        echo "✅ Paiement initialisé avec succès !\n";
        echo "URL de paiement: " . ($result['checkout_url'] ?? 'N/A') . "\n";
        echo "Token: " . ($result['token'] ?? 'N/A') . "\n";
        echo "\n🎉 Paydunya fonctionne correctement en mode sandbox !\n";
    } else {
        echo "❌ Erreur lors de l'initialisation du paiement\n";
        echo "Message: " . ($result['message'] ?? 'N/A') . "\n";
        
        if (strpos($result['message'] ?? '', 'Invalid Masterkey') !== false) {
            echo "\n🔧 Solution :\n";
            echo "1. Vérifiez vos clés dans le fichier .env\n";
            echo "2. Assurez-vous d'utiliser les clés sandbox de Paydunya\n";
            echo "3. Videz le cache : php artisan config:clear\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}

echo "\n=== Instructions ===\n";
echo "1. Si le test réussit, Paydunya est configuré correctement\n";
echo "2. Vous pouvez maintenant acheter des cours via /org/courses\n";
echo "3. Les paiements seront traités en mode sandbox\n";
