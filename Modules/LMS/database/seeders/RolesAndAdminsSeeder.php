<?php

namespace Modules\LMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LMS\Models\Auth\Admin;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndAdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 1. Création des Rôles

        $superAdminRole = Role::updateOrCreate(['name' => 'Super Admin'], ['guard_name' => 'admin']);
        $adminRole = Role::updateOrCreate(['name' => 'Admin'], ['guard_name' => 'admin']);

        // 2. Assignation des Permissions

        // Le Super Admin reçoit TOUTES les permissions
        $allPermissions = Permission::pluck('name');
        $superAdminRole->syncPermissions($allPermissions);

        // L'Admin reçoit un ensemble limité de permissions
        $adminPermissions = [
            'view dashboard',
            'manage users',
            'manage courses',
            'publish courses',
        ];
        $adminRole->syncPermissions($adminPermissions);


        // 3. Création des Utilisateurs Administrateurs

        // Création du Super Admin
        $superAdmin = Admin::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Administrateur',
                // Utilisez bcrypt ou Hash::make() pour hacher le mot de passe
                'password' => bcrypt('password'),
                'phone' => '0123456789',
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Création de l'Admin
        $admin = Admin::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrateur Simple',
                'password' => bcrypt('password'),
                'phone' => '9876543210',
            ]
        );
        $admin->assignRole($adminRole);
    }
}
