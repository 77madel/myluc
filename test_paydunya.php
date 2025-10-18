<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test de Configuration Paydunya ===\n\n";

// Vérifier les variables d'environnement
echo "1. Variables d'environnement :\n";
echo "PAYDUNYA_MASTER_KEY: " . (env('PAYDUNYA_MASTER_KEY') ? 'Définie' : 'NON DÉFINIE') . "\n";
echo "PAYDUNYA_PRIVATE_KEY: " . (env('PAYDUNYA_PRIVATE_KEY') ? 'Définie' : 'NON DÉFINIE') . "\n";
echo "PAYDUNYA_TOKEN: " . (env('PAYDUNYA_TOKEN') ? 'Définie' : 'NON DÉFINIE') . "\n";
echo "PAYDUNYA_TEST_MODE: " . (env('PAYDUNYA_TEST_MODE') ? 'true' : 'false') . "\n\n";

// Vérifier la configuration
echo "2. Configuration Paydunya :\n";
$config = config('paydunya');
echo "Master Key: " . ($config['master_key'] ? 'Définie' : 'NON DÉFINIE') . "\n";
echo "Private Key: " . ($config['private_key'] ? 'Définie' : 'NON DÉFINIE') . "\n";
echo "Token: " . ($config['token'] ? 'Définie' : 'NON DÉFINIE') . "\n";
echo "Test Mode: " . ($config['test_mode'] ? 'true' : 'false') . "\n\n";

// Test de création d'une facture simple
echo "3. Test de création de facture :\n";
try {
    // Configuration du store
    \Paydunya\Checkout\Store::setName('Test Store');
    \Paydunya\Checkout\Store::setTagline('Test Tagline');
    \Paydunya\Checkout\Store::setPhoneNumber('+22300000000');
    \Paydunya\Checkout\Store::setPostalAddress('Test Address');
    \Paydunya\Checkout\Store::setWebsiteUrl('https://example.com');
    \Paydunya\Checkout\Store::setLogoUrl('https://example.com/logo.png');
    
    // Création de la facture
    $invoice = new \Paydunya\Checkout\Invoice();
    $invoice->addItem('Test Course', 1, 1000, 1000);
    $invoice->setTotalAmount(1000);
    $invoice->setDescription('Test de paiement');
    $invoice->setReturnUrl('https://example.com/success');
    $invoice->setCancelUrl('https://example.com/cancel');
    
    if ($invoice->create()) {
        echo "✅ Facture créée avec succès !\n";
        echo "URL de paiement: " . $invoice->getInvoiceUrl() . "\n";
    } else {
        echo "❌ Erreur lors de la création de la facture\n";
        echo "Erreur: " . $invoice->getResponseText() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== Fin du test ===\n";
