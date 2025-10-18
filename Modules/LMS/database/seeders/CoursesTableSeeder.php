<?php

namespace Modules\LMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\LMS\Models\Auth\Admin;
use Modules\LMS\Models\Category;
use Modules\LMS\Models\Courses\Course;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // =================================================================
        // 1. GESTION DE L'INSTRUCTEUR (USER_ID)
        // =================================================================
        // Récupère le Super Admin créé par RolesAndAdminsTableSeeder pour l'utiliser comme instructeur (user_id).
        $instructor = Admin::where('email', 'superadmin@example.com')->first();

        if (!$instructor) {
            echo "Erreur: L'utilisateur Super Admin est introuvable. Veuillez exécuter RolesAndAdminsTableSeeder d'abord.\n";
            return;
        }
        $instructorId = $instructor->id;


        // =================================================================
        // 2. GESTION DE LA CATÉGORIE
        // =================================================================
        // Crée la catégorie de démonstration si elle n'existe pas.
        $demoCategory = Category::updateOrCreate(
            ['slug' => 'programmation'],
            [
                'title' => 'Programmation & Logiciel',
                // 'description' retirée car le champ n'existe pas ou est non-nullable sans valeur par défaut.
            ]
        );
        $categoryId = $demoCategory->id;


        // =================================================================
        // 3. CRÉATION DES COURS ET DU SLUG
        // =================================================================
        $courses = [
            [
                'title' => 'Introduction à Laravel 10',
                // 'price' retiré car la colonne n'existe pas dans la migration fournie
                'short_description' => 'Apprenez les bases du framework PHP Laravel.',
                'description' => 'Ce cours couvre les routes, les vues, les modèles Eloquent et les migrations.',
                'status' => 'Approved', // VÉRIFIÉ : Correspond à l'ENUM
                'duration' => '10 hours', // Ajout du champ 'duration' obligatoire
            ],
            [
                'title' => 'Maîtriser Tailwind CSS',
                // 'price' retiré
                'short_description' => 'Concevez des interfaces modernes sans quitter votre HTML.',
                'description' => 'Un guide complet pour devenir un expert en design utilitaire avec Tailwind CSS.',
                'status' => 'Approved', // VÉRIFIÉ : Correspond à l'ENUM
                'duration' => '5 hours', // Ajout du champ 'duration' obligatoire
            ],
            [
                'title' => 'Développement d\'Applications Modulaires (LMS)',
                // 'price' retiré
                'short_description' => 'Apprenez l\'architecture modulaire avec Nwidart Modules.',
                'description' => 'De la configuration du seeder aux erreurs 500, maîtrisez la modularité.',
                'status' => 'Pending', // VÉRIFIÉ : Correspond à l'ENUM
                'duration' => '20 hours', // Ajout du champ 'duration' obligatoire
            ],
        ];

        foreach ($courses as $courseData) {
            // Assignation des IDs récupérés ci-dessus
            // Attention: Votre table utilise 'admin_id' comme clé étrangère
            $courseData['admin_id'] = $instructorId; // Changé de 'user_id' à 'admin_id' pour correspondre à la foreign key
            $courseData['category_id'] = $categoryId;

            // CRÉATION DU SLUG: Utilise la fonction Str::slug() pour transformer le titre en une URL conviviale.
            $courseData['slug'] = Str::slug($courseData['title']);

            Course::updateOrCreate(['slug' => $courseData['slug']], $courseData);
        }

        echo "Seeders de Cours exécuté avec succès. Trois cours de démonstration créés.\n";
    }
}
