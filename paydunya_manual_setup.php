<?php

// Configuration manuelle Paydunya
// Remplacez ces valeurs par vos vraies clés sandbox

$paydunya_config = [
    'master_key' => 'votre_vraie_master_key_sandbox',
    'private_key' => 'votre_vraie_private_key_sandbox', 
    'token' => 'votre_vrai_token_sandbox',
    'test_mode' => true,
    'currency' => 'XOF'
];

// Test de configuration
echo "=== Configuration Paydunya Manuelle ===\n";
echo "Master Key: " . ($paydunya_config['master_key'] ? 'Définie' : 'NON DÉFINIE') . "\n";
echo "Private Key: " . ($paydunya_config['private_key'] ? 'Définie' : 'NON DÉFINIE') . "\n";
echo "Token: " . ($paydunya_config['token'] ? 'Définie' : 'NON DÉFINIE') . "\n";
echo "Test Mode: " . ($paydunya_config['test_mode'] ? 'true' : 'false') . "\n";
echo "Currency: " . $paydunya_config['currency'] . "\n\n";

echo "Pour utiliser Paydunya :\n";
echo "1. Créez un compte sur https://paydunya.com\n";
echo "2. Activez le mode sandbox\n";
echo "3. Générez vos clés sandbox\n";
echo "4. Remplacez les valeurs dans ce fichier\n";
echo "5. Mettez à jour votre .env\n\n";

echo "En attendant, utilisez la version de test du système.\n";
