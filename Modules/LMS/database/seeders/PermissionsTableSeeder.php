<?php

namespace Modules\LMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 1. Définition des permissions avec le champ 'module'
        $permissions = [
            // Dashboard
            ['name' => 'view dashboard', 'module' => 'LMS'],

            // Utilisateurs
            ['name' => 'manage users', 'module' => 'LMS'],
            ['name' => 'view users', 'module' => 'LMS'],
            ['name' => 'create users', 'module' => 'LMS'],
            ['name' => 'edit users', 'module' => 'LMS'],
            ['name' => 'delete users', 'module' => 'LMS'],

            // Rôles et Permissions
            ['name' => 'manage roles', 'module' => 'LMS'],
            ['name' => 'manage permissions', 'module' => 'LMS'],

            // Paramètres
            ['name' => 'manage settings', 'module' => 'LMS'],

            // Exemple LMS : Cours
            ['name' => 'manage courses', 'module' => 'LMS'],
            ['name' => 'publish courses', 'module' => 'LMS'],
            ['name' => 'delete courses', 'module' => 'LMS'],
        ];

        // 2. Insertion des données
        foreach ($permissions as $permission) {
            // updateOrCreate s'occupe d'ajouter guard_name='web' par défaut si non spécifié,
            // mais nous l'avons ajouté dans la réponse précédente. Assurons-nous que
            // tous les champs requis par la table existent dans le tableau.
            $data = array_merge(['guard_name' => 'admin'], $permission);
            Permission::updateOrCreate(['name' => $permission['name']], $data);
        }
    }
}
