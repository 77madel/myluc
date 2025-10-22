<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\Courses\Topics\Video;
use Modules\LMS\Models\Courses\TopicType;

echo "🎬 Création du contenu vidéo pour les topics...\n";

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

echo "📋 Trouvé {$topicsWithoutContent->count()} topics sans contenu\n";

// Vérifier tous les topics
$allTopics = Topic::all();
echo "📋 Total topics: {$allTopics->count()}\n";

foreach ($allTopics as $topic) {
    echo "Topic {$topic->id}: topicable_type = " . ($topic->topicable_type ?? 'NULL') . ", topicable_id = " . ($topic->topicable_id ?? 'NULL') . "\n";
    
    // Vérifier les topics 112-115 spécifiquement
    if (in_array($topic->id, [112, 113, 114, 115])) {
        echo "  -> Vérification du contenu pour le topic {$topic->id}...\n";
        if ($topic->topicable) {
            echo "  -> Contenu trouvé: " . get_class($topic->topicable) . " (ID: {$topic->topicable->id})\n";
            if (method_exists($topic->topicable, 'video_url')) {
                echo "  -> URL vidéo: " . ($topic->topicable->video_url ?? 'NULL') . "\n";
            }
            if (method_exists($topic->topicable, 'title')) {
                echo "  -> Titre: " . ($topic->topicable->title ?? 'NULL') . "\n";
            }
            if (method_exists($topic->topicable, 'duration')) {
                echo "  -> Durée: " . ($topic->topicable->duration ?? 'NULL') . "\n";
            }
        } else {
            echo "  -> ❌ Aucun contenu trouvé pour le topic {$topic->id}\n";
        }
    }
}

// Vérifier spécifiquement le topic 113
echo "🔍 Vérification du topic 113...\n";
$topic113 = Topic::find(113);
if ($topic113) {
    echo "✅ Topic 113 trouvé\n";
    echo "  -> ID: {$topic113->id}\n";
    echo "  -> Titre: {$topic113->title}\n";
    echo "  -> Topicable Type: {$topic113->topicable_type}\n";
    echo "  -> Topicable ID: {$topic113->topicable_id}\n";
    
    if ($topic113->topicable) {
        echo "  -> Contenu trouvé: " . get_class($topic113->topicable) . "\n";
        if (method_exists($topic113->topicable, 'video_url')) {
            echo "  -> URL vidéo: " . ($topic113->topicable->video_url ?? 'NULL') . "\n";
        }
    } else {
        echo "  -> ❌ Aucun contenu trouvé pour le topic 113\n";
    }
} else {
    echo "❌ Topic 113 non trouvé\n";
}

// Créer des vidéos valides pour les topics 112-115
$topicsToUpdate = Topic::whereIn('id', [112, 113, 114, 115])->get();

foreach ($topicsToUpdate as $topic) {
    echo "🔄 Mise à jour du topic {$topic->id}...\n";
    
    // Vérifier si la vidéo existe déjà
    if ($topic->topicable && $topic->topicable_type === Video::class) {
        $video = $topic->topicable;
        echo "  -> Vidéo existante trouvée (ID: {$video->id})\n";
        
        // Mettre à jour la vidéo existante
        $video->update([
            'title' => $topic->title ?? 'Video Lesson',
            'description' => 'This is a video lesson for the course.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Rick Roll pour test
            'duration' => '03:33',
            'thumbnail' => null,
            'is_active' => true,
        ]);
        
        echo "  -> Vidéo mise à jour: {$video->title}\n";
        echo "  -> URL: {$video->video_url}\n";
        echo "  -> Durée: {$video->duration}\n";
    } else {
        echo "  -> ❌ Aucune vidéo trouvée pour le topic {$topic->id}\n";
        
        // Créer une nouvelle vidéo
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
        
        echo "  -> ✅ Nouvelle vidéo créée: {$video->title}\n";
        echo "  -> URL: {$video->video_url}\n";
        echo "  -> Durée: {$video->duration}\n";
    }
}

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

    echo "✅ Vidéo créée pour le topic {$topic->id}: {$video->title}\n";
}

echo "🎬 Toutes les vidéos ont été créées avec succès !\n";
