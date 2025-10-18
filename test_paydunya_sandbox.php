<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Configuration Paydunya Sandbox ===\n\n";

// Vérifier les variables d'environnement
echo "1. Variables d'environnement :\n";
echo "PAYDUNYA_MASTER_KEY: " . (env('PAYDUNYA_MASTER_KEY') ? 'Définie (' . substr(env('PAYDUNYA_MASTER_KEY'), 0, 10) . '...)' : 'NON DÉFINIE') . "\n";
echo "PAYDUNYA_PRIVATE_KEY: " . (env('PAYDUNYA_PRIVATE_KEY') ? 'Définie (' . substr(env('PAYDUNYA_PRIVATE_KEY'), 0, 10) . '...)' : 'NON DÉFINIE') . "\n";
echo "PAYDUNYA_TOKEN: " . (env('PAYDUNYA_TOKEN') ? 'Définie (' . substr(env('PAYDUNYA_TOKEN'), 0, 10) . '...)' : 'NON DÉFINIE') . "\n";
echo "PAYDUNYA_TEST_MODE: " . (env('PAYDUNYA_TEST_MODE') ? 'true' : 'false') . "\n\n";

// Vérifier la configuration
echo "2. Configuration Paydunya :\n";
$config = config('paydunya');
if ($config) {
    echo "Master Key: " . ($config['master_key'] ? 'Définie' : 'NON DÉFINIE') . "\n";
    echo "Private Key: " . ($config['private_key'] ? 'Définie' : 'NON DÉFINIE') . "\n";
    echo "Token: " . ($config['token'] ? 'Définie' : 'NON DÉFINIE') . "\n";
    echo "Test Mode: " . ($config['test_mode'] ? 'true' : 'false') . "\n";
    echo "Currency: " . ($config['currency'] ?? 'N/A') . "\n\n";
} else {
    echo "❌ Configuration Paydunya non trouvée\n\n";
}

// Test de création d'une facture simple
echo "3. Test de création de facture sandbox :\n";
try {
    // Configuration du store
    \Paydunya\Checkout\Store::setName('Test Store Sandbox');
    \Paydunya\Checkout\Store::setTagline('Test Tagline Sandbox');
    \Paydunya\Checkout\Store::setPhoneNumber('+22300000000');
    \Paydunya\Checkout\Store::setPostalAddress('Test Address Sandbox');
    \Paydunya\Checkout\Store::setWebsiteUrl('https://example.com');
    \Paydunya\Checkout\Store::setLogoUrl('https://example.com/logo.png');
    
    // Création de la facture
    $invoice = new \Paydunya\Checkout\Invoice();
    $invoice->addItem('Test Course Sandbox', 1, 1000, 1000);
    $invoice->setTotalAmount(1000);
    $invoice->setDescription('Test de paiement sandbox');
    $invoice->setReturnUrl('https://example.com/success');
    $invoice->setCancelUrl('https://example.com/cancel');
    
    if ($invoice->create()) {
        echo "✅ Facture sandbox créée avec succès !\n";
        echo "URL de paiement: " . $invoice->getInvoiceUrl() . "\n";
        echo "Token de la facture: " . $invoice->getToken() . "\n";
    } else {
        echo "❌ Erreur lors de la création de la facture sandbox\n";
        echo "Erreur: " . $invoice->getResponseText() . "\n";
        echo "Code d'erreur: " . $invoice->getResponseCode() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Type: " . get_class($e) . "\n";
}

echo "\n=== Fin du test sandbox ===\n";
echo "\nSi vous voyez des erreurs, vérifiez vos clés Paydunya dans le fichier .env\n";
echo "Assurez-vous d'utiliser les vraies clés sandbox de votre compte Paydunya.\n";
