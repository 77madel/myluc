<?php

require_once 'vendor/autoload.php';

// Charger la configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Configuration Utilisateur Organisation Existant ===\n\n";

try {
    // Récupérer l'utilisateur organisation existant
    echo "1. Récupération de l'utilisateur organisation :\n";
    $user = \Modules\LMS\Models\User::where('userable_type', 'Modules\LMS\Models\Auth\Organization')->first();
    
    if (!$user) {
        echo "  ❌ Aucun utilisateur organisation trouvé\n";
        exit;
    }
    
    echo "  ✓ Utilisateur trouvé (ID: {$user->id})\n";
    echo "  ✓ Email: {$user->email}\n";
    echo "  ✓ Type: {$user->userable_type}\n";
    echo "  ✓ ID userable: {$user->userable_id}\n";

    // Vérifier l'organisation associée
    echo "\n2. Vérification de l'organisation :\n";
    $organization = $user->organization;
    if ($organization) {
        echo "  ✓ Organisation trouvée (ID: {$organization->id})\n";
        echo "  ✓ Nom: {$organization->name}\n";
        echo "  ✓ Statut: {$organization->status}\n";
        
        // S'assurer que l'organisation est active
        if ($organization->status != 1) {
            $organization->update(['status' => 1]);
            echo "  ✓ Statut mis à jour à 'actif'\n";
        }
    } else {
        echo "  ❌ Aucune organisation associée\n";
        exit;
    }

    // Vérifier et créer le rôle Organization
    echo "\n3. Vérification du rôle Organization :\n";
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Organization']);
    echo "  ✓ Rôle 'Organization' créé/trouvé (ID: {$role->id})\n";

    // Assigner le rôle si nécessaire
    if (!$user->hasRole('Organization')) {
        $user->assignRole('Organization');
        echo "  ✓ Rôle 'Organization' attribué\n";
    } else {
        echo "  ✓ Rôle 'Organization' déjà attribué\n";
    }

    // Vérifier la relation finale
    echo "\n4. Vérification finale :\n";
    $userOrg = $user->organization;
    if ($userOrg && $userOrg->status == 1) {
        echo "  ✓ Relation organisation fonctionnelle\n";
        echo "  ✓ Organisation active\n";
    } else {
        echo "  ❌ Problème avec la relation organisation\n";
    }

    echo "\n=== Informations de Connexion ===\n";
    echo "Email: {$user->email}\n";
    echo "Mot de passe: (utilisez le mot de passe existant ou réinitialisez)\n";
    echo "URL de connexion: http://127.0.0.1:8000/login\n";
    echo "URL du dashboard: http://127.0.0.1:8000/org\n";

    echo "\n=== Test de l'Accès ===\n";
    echo "1. Connectez-vous avec l'email: {$user->email}\n";
    echo "2. Accédez à http://127.0.0.1:8000/org\n";
    echo "3. Le dashboard organisation devrait s'afficher\n";

    // Optionnel: Réinitialiser le mot de passe
    echo "\n=== Optionnel: Réinitialisation du Mot de Passe ===\n";
    echo "Si vous ne connaissez pas le mot de passe, exécutez :\n";
    echo "php artisan tinker\n";
    echo ">>> \$user = \\Modules\\LMS\\Models\\User::find({$user->id});\n";
    echo ">>> \$user->password = bcrypt('password');\n";
    echo ">>> \$user->save();\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
