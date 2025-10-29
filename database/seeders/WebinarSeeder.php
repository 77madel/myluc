<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LMS\Models\Webinar;
use Modules\LMS\Models\Auth\Instructor;
use Modules\LMS\Models\Category;
use Carbon\Carbon;

class WebinarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and categories
        $instructors = Instructor::take(3)->get();
        $categories = Category::take(3)->get();

        if ($instructors->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('No instructors or categories found. Please seed users and categories first.');
            return;
        }

        $webinars = [
            [
                'title' => 'Introduction au Développement Web',
                'description' => 'Découvrez les bases du développement web avec HTML, CSS et JavaScript. Ce webinaire vous donnera une introduction complète aux technologies web modernes.',
                'short_description' => 'Apprenez les bases du développement web en 1 heure',
                'start_date' => Carbon::now()->addDays(7)->setTime(14, 0),
                'end_date' => Carbon::now()->addDays(7)->setTime(15, 0),
                'duration' => 60,
                'max_participants' => 50,
                'price' => 0,
                'is_free' => true,
                'is_published' => true,
                'tags' => ['développement', 'web', 'html', 'css', 'javascript'],
                'requirements' => [
                    'Aucune connaissance préalable requise',
                    'Ordinateur avec connexion internet',
                    'Navigateur web moderne'
                ],
                'learning_outcomes' => [
                    'Comprendre les bases du HTML',
                    'Maîtriser les concepts CSS',
                    'Apprendre les fondamentaux de JavaScript',
                    'Créer votre première page web'
                ]
            ],
            [
                'title' => 'Marketing Digital Avancé',
                'description' => 'Maîtrisez les stratégies de marketing digital les plus efficaces. Ce webinaire couvre le SEO, les réseaux sociaux, l\'email marketing et l\'analyse de données.',
                'short_description' => 'Stratégies avancées de marketing digital',
                'start_date' => Carbon::now()->addDays(10)->setTime(10, 0),
                'end_date' => Carbon::now()->addDays(10)->setTime(12, 0),
                'duration' => 120,
                'max_participants' => 30,
                'price' => 25000,
                'is_free' => false,
                'is_published' => true,
                'tags' => ['marketing', 'digital', 'seo', 'réseaux sociaux'],
                'requirements' => [
                    'Connaissances de base en marketing',
                    'Expérience avec les réseaux sociaux',
                    'Notions d\'analyse de données'
                ],
                'learning_outcomes' => [
                    'Développer une stratégie SEO efficace',
                    'Optimiser sa présence sur les réseaux sociaux',
                    'Créer des campagnes email performantes',
                    'Analyser et interpréter les données marketing'
                ]
            ],
            [
                'title' => 'Gestion de Projet Agile',
                'description' => 'Apprenez les méthodologies agiles pour gérer vos projets efficacement. Scrum, Kanban, et autres frameworks seront abordés avec des exemples pratiques.',
                'short_description' => 'Méthodologies agiles pour la gestion de projet',
                'start_date' => Carbon::now()->addDays(14)->setTime(16, 0),
                'end_date' => Carbon::now()->addDays(14)->setTime(18, 0),
                'duration' => 120,
                'max_participants' => 25,
                'price' => 35000,
                'is_free' => false,
                'is_published' => true,
                'tags' => ['gestion', 'projet', 'agile', 'scrum', 'kanban'],
                'requirements' => [
                    'Expérience en gestion de projet',
                    'Connaissance des équipes de travail',
                    'Notions de planification'
                ],
                'learning_outcomes' => [
                    'Maîtriser les principes de l\'agilité',
                    'Implémenter Scrum dans vos projets',
                    'Utiliser Kanban pour l\'amélioration continue',
                    'Gérer les équipes agiles efficacement'
                ]
            ],
            [
                'title' => 'Intelligence Artificielle pour Débutants',
                'description' => 'Découvrez l\'IA et ses applications pratiques. Ce webinaire vous introduira aux concepts de machine learning, deep learning et aux outils populaires.',
                'short_description' => 'Introduction à l\'intelligence artificielle',
                'start_date' => Carbon::now()->addDays(21)->setTime(15, 0),
                'end_date' => Carbon::now()->addDays(21)->setTime(17, 0),
                'duration' => 120,
                'max_participants' => 40,
                'price' => 0,
                'is_free' => true,
                'is_published' => true,
                'tags' => ['IA', 'machine learning', 'intelligence artificielle', 'python'],
                'requirements' => [
                    'Notions de base en programmation',
                    'Curiosité pour les nouvelles technologies',
                    'Ordinateur avec Python installé'
                ],
                'learning_outcomes' => [
                    'Comprendre les concepts de l\'IA',
                    'Explorer le machine learning',
                    'Découvrir les outils populaires',
                    'Créer votre premier modèle simple'
                ]
            ],
            [
                'title' => 'Entrepreneuriat et Innovation',
                'description' => 'Transformez vos idées en entreprises prospères. Ce webinaire couvre la création d\'entreprise, le financement, le marketing et la gestion.',
                'short_description' => 'De l\'idée à l\'entreprise prospère',
                'start_date' => Carbon::now()->addDays(28)->setTime(9, 0),
                'end_date' => Carbon::now()->addDays(28)->setTime(11, 0),
                'duration' => 120,
                'max_participants' => 35,
                'price' => 45000,
                'is_free' => false,
                'is_published' => true,
                'tags' => ['entrepreneuriat', 'innovation', 'business', 'startup'],
                'requirements' => [
                    'Esprit entrepreneurial',
                    'Idée de projet ou d\'entreprise',
                    'Motivation pour créer'
                ],
                'learning_outcomes' => [
                    'Développer un business plan solide',
                    'Identifier les sources de financement',
                    'Créer une stratégie marketing efficace',
                    'Gérer la croissance de votre entreprise'
                ]
            ]
        ];

        foreach ($webinars as $index => $webinarData) {
            $webinar = Webinar::create([
                'title' => $webinarData['title'],
                'description' => $webinarData['description'],
                'short_description' => $webinarData['short_description'],
                'slug' => \Illuminate\Support\Str::slug($webinarData['title']),
                'start_date' => $webinarData['start_date'],
                'end_date' => $webinarData['end_date'],
                'duration' => $webinarData['duration'],
                'max_participants' => $webinarData['max_participants'],
                'current_participants' => rand(0, 10),
                'price' => $webinarData['price'],
                'is_free' => $webinarData['is_free'],
                'is_recorded' => rand(0, 1),
                'is_published' => $webinarData['is_published'],
                'status' => 'scheduled',
                'instructor_id' => $instructors->random()->user->id,
                'category_id' => $categories->random()->id,
                'tags' => $webinarData['tags'],
                'requirements' => $webinarData['requirements'],
                'learning_outcomes' => $webinarData['learning_outcomes'],
                'meeting_url' => 'https://meet.google.com/abc-defg-hij',
                'meeting_id' => 'abc-defg-hij',
                'meeting_password' => 'webinar123',
                'notes' => 'Webinaire créé automatiquement par le seeder'
            ]);

            $this->command->info("Webinar created: {$webinar->title}");
        }

        $this->command->info('Webinar seeding completed successfully!');
    }
}
