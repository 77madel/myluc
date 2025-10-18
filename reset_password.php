<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Réinitialisation du Mot de Passe ===\n\n";

try {
    $user = \Modules\LMS\Models\User::find(34);
    if ($user) {
        $user->password = bcrypt('password');
        $user->save();
        echo "✓ Mot de passe réinitialisé avec succès\n";
        echo "✓ Email: organization@gmail.com\n";
        echo "✓ Nouveau mot de passe: password\n";
    } else {
        echo "❌ Utilisateur non trouvé\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
