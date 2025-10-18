<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vérification des Clés Paydunya ===\n\n";

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
    echo "Currency: " . ($config['currency'] ?? 'N/A') . "\n";
} else {
    echo "❌ Configuration Paydunya non trouvée\n";
}

// Vérifier les clés actuelles
echo "\n3. Clés actuelles :\n";
$masterKey = config('paydunya.master_key');
$privateKey = config('paydunya.private_key');
$token = config('paydunya.token');

if ($masterKey) {
    echo "Master Key: " . substr($masterKey, 0, 10) . "...\n";
    echo "Longueur: " . strlen($masterKey) . " caractères\n";
} else {
    echo "❌ Master Key manquante\n";
}

if ($privateKey) {
    echo "Private Key: " . substr($privateKey, 0, 10) . "...\n";
    echo "Longueur: " . strlen($privateKey) . " caractères\n";
} else {
    echo "❌ Private Key manquante\n";
}

if ($token) {
    echo "Token: " . substr($token, 0, 10) . "...\n";
    echo "Longueur: " . strlen($token) . " caractères\n";
} else {
    echo "❌ Token manquant\n";
}

echo "\n4. Diagnostic :\n";
if (empty($masterKey) || empty($privateKey) || empty($token)) {
    echo "❌ Clés Paydunya manquantes ou invalides\n";
    echo "→ Solution : Configurer les vraies clés Paydunya dans le fichier .env\n";
} else {
    echo "✓ Clés Paydunya présentes\n";
    echo "→ Vérifiez que les clés sont valides et correspondent au mode (test/production)\n";
}

echo "\n5. Solutions :\n";
echo "a) Obtenir de vraies clés Paydunya :\n";
echo "   - Allez sur https://paydunya.com\n";
echo "   - Créez un compte développeur\n";
echo "   - Activez le mode sandbox\n";
echo "   - Générez vos clés sandbox\n\n";

echo "b) Mettre à jour le fichier .env :\n";
echo "   PAYDUNYA_MASTER_KEY=votre_vraie_master_key\n";
echo "   PAYDUNYA_PRIVATE_KEY=votre_vraie_private_key\n";
echo "   PAYDUNYA_TOKEN=votre_vrai_token\n";
echo "   PAYDUNYA_TEST_MODE=true\n\n";

echo "c) Vider le cache après modification :\n";
echo "   php artisan config:clear\n";
