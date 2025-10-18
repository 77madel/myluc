<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vérification de la Table Organizations ===\n\n";

try {
    // Vérifier la structure de la table
    echo "1. Structure de la table organizations :\n";
    $columns = \DB::select('DESCRIBE organizations');
    foreach ($columns as $column) {
        echo "  - {$column->Field}: {$column->Type} " . ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }

    // Vérifier les données existantes
    echo "\n2. Données existantes :\n";
    $organizations = \DB::table('organizations')->get();
    echo "  Nombre d'organisations: " . $organizations->count() . "\n";
    
    if ($organizations->count() > 0) {
        foreach ($organizations as $org) {
            echo "  - ID: {$org->id}, Nom: {$org->name}, Statut: " . ($org->status ?? 'N/A') . "\n";
        }
    }

    // Créer une organisation simple sans statut
    echo "\n3. Création d'une organisation de test :\n";
    $orgId = \DB::table('organizations')->insertGetId([
        'name' => 'Organisation Test',
        'email' => 'org@test.com',
        'phone' => '+22300000000',
        'address' => 'Adresse Test',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "  ✓ Organisation créée (ID: {$orgId})\n";

    // Créer un utilisateur
    echo "\n4. Création d'un utilisateur :\n";
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

    // Assigner le rôle
    echo "\n5. Attribution du rôle :\n";
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Organization']);
    $user = \Modules\LMS\Models\User::find($userId);
    $user->assignRole('Organization');
    echo "  ✓ Rôle 'Organization' attribué\n";

    echo "\n=== Informations de Connexion ===\n";
    echo "Email: org@test.com\n";
    echo "Mot de passe: password\n";
    echo "URL: http://127.0.0.1:8000/login\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
