<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Exécuter seulement le seeder des cours d'organisation
        $this->call([
            \Modules\LMS\Database\Seeders\OrganizationCoursesSeeder::class,
        ]);

        echo "\n🎉 Cours d'organisation créés avec succès !\n";
        echo "📚 2 cours avec chapitres et leçons\n";
        echo "🎥 Vidéos YouTube courtes (1-2 minutes)\n";
        echo "💰 Cours payants avec certification\n";
        echo "🏢 Organisation : Tech Academy\n";
        echo "👨‍🏫 Instructeur : Jean Dupont\n";
    }
}
