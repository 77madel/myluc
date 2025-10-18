<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Correction du Statut de l'Organisation ===\n\n";

try {
    // Récupérer l'utilisateur organisation
    $user = \Modules\LMS\Models\User::find(34);
    if (!$user) {
        echo "❌ Utilisateur non trouvé\n";
        exit;
    }
    
    echo "1. Utilisateur trouvé : {$user->email}\n";
    
    // Récupérer l'organisation
    $organization = $user->organization;
    if (!$organization) {
        echo "❌ Organisation non trouvée\n";
        exit;
    }
    
    echo "2. Organisation trouvée : {$organization->name}\n";
    echo "3. Statut actuel : {$organization->status}\n";
    
    // Vérifier le type de statut
    if (is_numeric($organization->status)) {
        echo "4. Le statut est numérique (1 = actif, 0 = inactif)\n";
        
        if ($organization->status == 1) {
            echo "5. ✓ Organisation déjà active (statut = 1)\n";
        } else {
            echo "5. ❌ Organisation inactive (statut = {$organization->status})\n";
            $organization->update(['status' => 1]);
            echo "6. ✓ Statut mis à jour à 1 (actif)\n";
        }
    } else {
        echo "4. Le statut est textuel : '{$organization->status}'\n";
        
        if ($organization->status === 'active') {
            echo "5. ✓ Organisation déjà active (statut = 'active')\n";
        } else {
            echo "5. ❌ Organisation inactive (statut = '{$organization->status}')\n";
            $organization->update(['status' => 'active']);
            echo "6. ✓ Statut mis à jour à 'active'\n";
        }
    }
    
    // Vérifier le middleware
    echo "\n7. Vérification du middleware :\n";
    echo "   Le middleware vérifie : \$organization->status !== 'active'\n";
    echo "   Mais le statut en DB est : " . (is_numeric($organization->status) ? $organization->status : "'{$organization->status}'") . "\n";
    
    if (is_numeric($organization->status) && $organization->status == 1) {
        echo "   → Problème : Le middleware attend 'active' mais trouve 1\n";
        echo "   → Solution : Modifier le middleware pour accepter 1 comme actif\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
