<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Webinar;
use App\Models\WebinarPlatformIntegration;
use Carbon\Carbon;

class CreateTestWebinars extends Command
{
    protected $signature = 'webinars:create-test';
    protected $description = 'Create test webinars for testing the interface';

    public function handle()
    {
        $this->info('Creating test webinars...');

        // Create platform integrations if they don't exist
        if (WebinarPlatformIntegration::count() === 0) {
            $this->createPlatformIntegrations();
        }

        // Create test webinars
        $webinars = [
            [
                'title' => 'Introduction à Laravel',
                'description' => 'Un webinaire complet pour apprendre les bases de Laravel, le framework PHP le plus populaire.',
                'short_description' => 'Découvrez Laravel en 1 heure',
                'slug' => 'introduction-laravel',
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(7)->addHour(),
                'max_participants' => 50,
                'is_free' => true,
                'status' => 'published',
                'instructor_id' => 30
            ],
            [
                'title' => 'React.js Avancé',
                'description' => 'Techniques avancées de React.js pour développer des applications modernes.',
                'short_description' => 'React.js pour les développeurs expérimentés',
                'slug' => 'react-js-avance',
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(14)->addHours(2),
                'max_participants' => 30,
                'is_free' => false,
                'price' => 29.99,
                'status' => 'published',
                'instructor_id' => 30
            ],
            [
                'title' => 'Gestion de Projet Agile',
                'description' => 'Apprenez les méthodologies agiles pour gérer vos projets efficacement.',
                'short_description' => 'Méthodologies agiles en pratique',
                'slug' => 'gestion-projet-agile',
                'start_date' => Carbon::now()->addDays(21),
                'end_date' => Carbon::now()->addDays(21)->addHour(),
                'max_participants' => 100,
                'is_free' => true,
                'status' => 'published',
                'instructor_id' => 30
            ],
            [
                'title' => 'Webinaire Live - Questions/Réponses',
                'description' => 'Session de questions-réponses en direct avec nos experts.',
                'short_description' => 'Q&A en direct',
                'slug' => 'webinaire-live-questions-reponses',
                'start_date' => Carbon::now()->addHours(2),
                'end_date' => Carbon::now()->addHours(3),
                'max_participants' => 200,
                'is_free' => true,
                'status' => 'published',
                'is_live' => true,
                'instructor_id' => 30
            ]
        ];

        foreach ($webinars as $webinarData) {
            $webinar = Webinar::create($webinarData);
            $this->info("Created webinar: {$webinar->title}");
        }

        $this->info('Test webinars created successfully!');
        $this->info('You can now test the webinar system in the interface.');
    }

    private function createPlatformIntegrations()
    {
        $integrations = [
            [
                'platform' => 'zoom',
                'name' => 'Zoom Integration',
                'description' => 'Intégration Zoom pour les webinaires',
                'is_active' => true,
                'is_default' => true,
                'supports_recording' => true,
                'supports_chat' => true,
                'supports_screen_sharing' => true
            ],
            [
                'platform' => 'teams',
                'name' => 'Microsoft Teams Integration',
                'description' => 'Intégration Microsoft Teams pour les webinaires',
                'is_active' => true,
                'supports_recording' => true,
                'supports_chat' => true,
                'supports_screen_sharing' => true
            ],
            [
                'platform' => 'google_meet',
                'name' => 'Google Meet Integration',
                'description' => 'Intégration Google Meet pour les webinaires',
                'is_active' => true,
                'supports_recording' => true,
                'supports_chat' => true,
                'supports_screen_sharing' => true
            ]
        ];

        foreach ($integrations as $integrationData) {
            WebinarPlatformIntegration::create($integrationData);
        }

        $this->info('Platform integrations created');
    }
}
