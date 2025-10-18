<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test d'Accès Organisation ===\n\n";

try {
    // Simuler l'authentification
    $user = \Modules\LMS\Models\User::find(34);
    if (!$user) {
        echo "❌ Utilisateur non trouvé\n";
        exit;
    }
    
    echo "1. Utilisateur : {$user->email}\n";
    
    // Vérifier l'organisation
    $organization = $user->organization;
    if (!$organization) {
        echo "❌ Organisation non trouvée\n";
        exit;
    }
    
    echo "2. Organisation : {$organization->name}\n";
    echo "3. Statut : {$organization->status}\n";
    
    // Vérifier les conditions du middleware
    echo "\n4. Vérification des conditions du middleware :\n";
    
    // Condition 1: Utilisateur authentifié
    echo "   ✓ Utilisateur authentifié\n";
    
    // Condition 2: Rôle Organization
    if ($user->hasRole('Organization')) {
        echo "   ✓ Rôle 'Organization' trouvé\n";
    } else {
        echo "   ❌ Rôle 'Organization' manquant\n";
    }
    
    // Condition 3: Organisation associée
    if ($organization) {
        echo "   ✓ Organisation associée\n";
    } else {
        echo "   ❌ Aucune organisation associée\n";
    }
    
    // Condition 4: Organisation active
    if ($organization->status === 'active' || $organization->status == 1) {
        echo "   ✓ Organisation active (statut: {$organization->status})\n";
    } else {
        echo "   ❌ Organisation inactive (statut: {$organization->status})\n";
    }
    
    echo "\n5. Résultat :\n";
    if ($user->hasRole('Organization') && $organization && ($organization->status === 'active' || $organization->status == 1)) {
        echo "   ✅ ACCÈS AUTORISÉ - Toutes les conditions sont remplies\n";
        echo "   → Vous pouvez maintenant accéder à http://127.0.0.1:8000/org\n";
    } else {
        echo "   ❌ ACCÈS REFUSÉ - Certaines conditions ne sont pas remplies\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
