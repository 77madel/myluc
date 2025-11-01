<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\Courses\Chapter;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\Courses\Topics\Video;
use Modules\LMS\Models\Courses\Topics\Reading;

class TestTopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Trouver le premier cours
        $course = Course::first();
        if (!$course) {
            $this->command->error('Aucun cours trouvé. Créez d\'abord un cours.');
            return;
        }

        // Trouver le premier chapitre
        $chapter = Chapter::where('course_id', $course->id)->first();
        if (!$chapter) {
            $this->command->error('Aucun chapitre trouvé. Créez d\'abord un chapitre.');
            return;
        }

        // Créer des vidéos de test
        $videos = [
            [
                'title' => 'Introduction à PHP',
                'duration' => '05:30',
                'video_src_type' => 'youtube',
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ],
            [
                'title' => 'Variables et Types de données',
                'duration' => '08:15',
                'video_src_type' => 'youtube',
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ],
            [
                'title' => 'Boucles et Conditions',
                'duration' => '12:45',
                'video_src_type' => 'youtube',
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ],
        ];

        // Créer des lectures de test
        $readings = [
            [
                'title' => 'Documentation PHP',
                'description' => 'Guide complet de la documentation PHP officielle.',
            ],
            [
                'title' => 'Bonnes pratiques',
                'description' => 'Les meilleures pratiques pour écrire du code PHP propre.',
            ],
        ];

        $this->command->info('Création des vidéos de test...');
        foreach ($videos as $index => $videoData) {
            $video = Video::create([
                'topic_type_id' => 1, // Assumer que l'ID 1 existe
                'title' => $videoData['title'],
                'duration' => $videoData['duration'],
                'video_src_type' => $videoData['video_src_type'],
                'video_url' => $videoData['video_url'],
            ]);

            Topic::create([
                'chapter_id' => $chapter->id,
                'course_id' => $course->id,
                'topicable_id' => $video->id,
                'topicable_type' => Video::class,
                'order' => $index + 1,
            ]);

            $this->command->info("✅ Vidéo créée: {$video->title}");
        }

        $this->command->info('Création des lectures de test...');
        foreach ($readings as $index => $readingData) {
            $reading = Reading::create([
                'topic_type_id' => 2, // Assumer que l'ID 2 existe
                'title' => $readingData['title'],
                'description' => $readingData['description'],
            ]);

            Topic::create([
                'chapter_id' => $chapter->id,
                'course_id' => $course->id,
                'topicable_id' => $reading->id,
                'topicable_type' => Reading::class,
                'order' => count($videos) + $index + 1,
            ]);

            $this->command->info("✅ Lecture créée: {$reading->title}");
        }

        $this->command->info('✅ Topics de test créés avec succès !');
        $this->command->info('IDs des topics créés: ' . Topic::latest()->take(5)->pluck('id')->implode(', '));
    }
}