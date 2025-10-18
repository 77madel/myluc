<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Création d'un Utilisateur Organisation ===\n\n";

use Modules\LMS\Models\User;
use Modules\LMS\Models\Auth\Organization;
use Spatie\Permission\Models\Role;

try {
    // Créer ou récupérer le rôle Organization
    echo "1. Création du rôle Organization :\n";
    $role = Role::firstOrCreate(['name' => 'Organization']);
    echo "  ✓ Rôle 'Organization' créé/trouvé\n";

    // Créer une organisation de test
    echo "\n2. Création d'une organisation de test :\n";
    $organization = Organization::firstOrCreate(
        ['name' => 'Organisation Test'],
        [
            'name' => 'Organisation Test',
            'email' => 'org@test.com',
            'phone' => '+22300000000',
            'address' => 'Adresse Test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]
    );
    echo "  ✓ Organisation créée/trouvée (ID: {$organization->id})\n";

    // Créer un utilisateur de test
    echo "\n3. Création d'un utilisateur de test :\n";
    $user = User::firstOrCreate(
        ['email' => 'org@test.com'],
        [
            'name' => 'Admin Organisation',
            'email' => 'org@test.com',
            'password' => bcrypt('password'),
            'userable_type' => 'Modules\LMS\Models\Auth\Organization',
            'userable_id' => $organization->id,
            'created_at' => now(),
            'updated_at' => now()
        ]
    );
    echo "  ✓ Utilisateur créé/trouvé (ID: {$user->id})\n";

    // Assigner le rôle
    echo "\n4. Attribution du rôle :\n";
    if (!$user->hasRole('Organization')) {
        $user->assignRole('Organization');
        echo "  ✓ Rôle 'Organization' attribué\n";
    } else {
        echo "  ✓ Rôle 'Organization' déjà attribué\n";
    }

    // Vérifier la relation
    echo "\n5. Vérification de la relation :\n";
    $userOrg = $user->organization;
    if ($userOrg) {
        echo "  ✓ Relation organisation fonctionnelle\n";
        echo "  ✓ Organisation: {$userOrg->name}\n";
        echo "  ✓ Statut: {$userOrg->status}\n";
    } else {
        echo "  ❌ Relation organisation non fonctionnelle\n";
    }

    echo "\n=== Informations de Connexion ===\n";
    echo "Email: org@test.com\n";
    echo "Mot de passe: password\n";
    echo "URL de connexion: http://127.0.0.1:8000/login\n";
    echo "URL du dashboard: http://127.0.0.1:8000/org\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
