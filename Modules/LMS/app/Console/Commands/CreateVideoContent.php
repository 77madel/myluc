<?php

namespace Modules\LMS\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\Courses\Video;
use Modules\LMS\Models\TopicType;

class CreateVideoContent extends Command
{
    protected $signature = 'lms:create-video-content';
    protected $description = 'Create video content for topics without content';

    public function handle()
    {
        $this->info('🎬 Création du contenu vidéo pour les topics...');

        // Créer le type de topic 'video' s'il n'existe pas
        $videoType = TopicType::firstOrCreate(
            ['slug' => 'video'],
            [
                'name' => 'Video',
                'slug' => 'video',
                'icon' => 'ri-file-video-line',
                'color' => '#3B82F6'
            ]
        );

        // Récupérer les topics qui n'ont pas de contenu
        $topicsWithoutContent = Topic::whereDoesntHave('topicable')->get();
        
        $this->info("📋 Trouvé {$topicsWithoutContent->count()} topics sans contenu");
        
        foreach ($topicsWithoutContent as $topic) {
            // Créer une vidéo pour ce topic
            $video = Video::create([
                'title' => $topic->title ?? 'Video Lesson',
                'description' => 'This is a video lesson for the course.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Rick Roll pour test
                'duration' => '03:33',
                'thumbnail' => null,
                'is_active' => true,
            ]);

            // Associer la vidéo au topic
            $topic->update([
                'topicable_type' => Video::class,
                'topicable_id' => $video->id,
            ]);

            $this->info("✅ Vidéo créée pour le topic {$topic->id}: {$video->title}");
        }

        $this->info("🎬 Toutes les vidéos ont été créées avec succès !");
        return 0;
    }
}

