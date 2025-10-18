<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Création Utilisateur Organisation (Corrigé) ===\n\n";

try {
    // Créer une organisation avec les bons champs
    echo "1. Création d'une organisation :\n";
    $orgId = \DB::table('organizations')->insertGetId([
        'name' => 'Organisation Test',
        'phone' => '+22300000000',
        'address' => 'Adresse Test',
        'status' => 1, // 1 = actif, 0 = inactif
        'user_balance' => 0.00,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "  ✓ Organisation créée (ID: {$orgId})\n";

    // Créer un utilisateur
    echo "\n2. Création d'un utilisateur :\n";
    $userId = \DB::table('users')->insertGetId([
        'name' => 'Admin Organisation',
        'email' => 'org@test.com',
        'password' => bcrypt('password'),
        'userable_type' => 'Modules\LMS\Models\Auth\Organization',
        'userable_id' => $orgId,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "  ✓ Utilisateur créé (ID: {$userId})\n";

    // Créer le rôle Organization s'il n'existe pas
    echo "\n3. Création du rôle Organization :\n";
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Organization']);
    echo "  ✓ Rôle 'Organization' créé/trouvé (ID: {$role->id})\n";

    // Assigner le rôle à l'utilisateur
    echo "\n4. Attribution du rôle :\n";
    $user = \Modules\LMS\Models\User::find($userId);
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

    echo "\n=== Test de l'Accès ===\n";
    echo "1. Connectez-vous avec les identifiants ci-dessus\n";
    echo "2. Accédez à http://127.0.0.1:8000/org\n";
    echo "3. Le dashboard organisation devrait s'afficher\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
