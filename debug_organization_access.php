<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Diagnostic d'Accès Organisation ===\n\n";

// Vérifier l'authentification
echo "1. Vérification de l'authentification :\n";
if (Auth::check()) {
    echo "  ✓ Utilisateur authentifié\n";
    $user = Auth::user();
    echo "  ✓ ID utilisateur: " . $user->id . "\n";
    echo "  ✓ Email: " . $user->email . "\n";
} else {
    echo "  ❌ Utilisateur non authentifié\n";
    echo "  → Redirection vers login nécessaire\n";
    exit;
}

// Vérifier les rôles
echo "\n2. Vérification des rôles :\n";
try {
    $roles = $user->roles;
    echo "  ✓ Rôles trouvés: " . $roles->count() . "\n";
    foreach ($roles as $role) {
        echo "    - " . $role->name . "\n";
    }
    
    if ($user->hasRole('Organization')) {
        echo "  ✓ Rôle 'Organization' trouvé\n";
    } else {
        echo "  ❌ Rôle 'Organization' manquant\n";
        echo "  → L'utilisateur doit avoir le rôle 'Organization'\n";
    }
} catch (Exception $e) {
    echo "  ❌ Erreur lors de la vérification des rôles: " . $e->getMessage() . "\n";
}

// Vérifier l'organisation
echo "\n3. Vérification de l'organisation :\n";
try {
    $organization = $user->organization;
    if ($organization) {
        echo "  ✓ Organisation trouvée\n";
        echo "  ✓ ID organisation: " . $organization->id . "\n";
        echo "  ✓ Nom: " . $organization->name . "\n";
        echo "  ✓ Statut: " . ($organization->status ?? 'N/A') . "\n";
        
        if ($organization->status === 'active') {
            echo "  ✓ Organisation active\n";
        } else {
            echo "  ❌ Organisation non active (statut: " . ($organization->status ?? 'N/A') . ")\n";
        }
    } else {
        echo "  ❌ Aucune organisation associée\n";
        echo "  → L'utilisateur doit être associé à une organisation\n";
    }
} catch (Exception $e) {
    echo "  ❌ Erreur lors de la vérification de l'organisation: " . $e->getMessage() . "\n";
}

// Vérifier la relation userable
echo "\n4. Vérification de la relation userable :\n";
try {
    echo "  ✓ Type userable: " . ($user->userable_type ?? 'N/A') . "\n";
    echo "  ✓ ID userable: " . ($user->userable_id ?? 'N/A') . "\n";
    
    if ($user->userable_type === 'Modules\LMS\Models\Auth\Organization') {
        echo "  ✓ Relation userable correcte\n";
    } else {
        echo "  ❌ Relation userable incorrecte\n";
        echo "  → userable_type doit être 'Modules\LMS\Models\Auth\Organization'\n";
    }
} catch (Exception $e) {
    echo "  ❌ Erreur lors de la vérification userable: " . $e->getMessage() . "\n";
}

echo "\n=== Résumé ===\n";
echo "Pour résoudre l'erreur 403, vérifiez que :\n";
echo "1. L'utilisateur est authentifié\n";
echo "2. L'utilisateur a le rôle 'Organization'\n";
echo "3. L'utilisateur est associé à une organisation\n";
echo "4. L'organisation a le statut 'active'\n";
echo "5. La relation userable est correcte\n";
