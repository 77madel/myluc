<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\Courses\Topics\Video;
use Modules\LMS\Models\Courses\Chapter;
use Modules\LMS\Models\Courses\Course;

echo "🔄 Restauration des anciens topics...\n";

// Récupérer le premier cours
$course = Course::first();
if (!$course) {
    echo "❌ Aucun cours trouvé\n";
    exit;
}

echo "📚 Cours trouvé: {$course->title}\n";

// Récupérer le premier chapitre
$chapter = Chapter::where('course_id', $course->id)->first();
if (!$chapter) {
    echo "❌ Aucun chapitre trouvé\n";
    exit;
}

echo "📖 Chapitre trouvé: {$chapter->title}\n";

// Créer les anciens topics (66, 67, 68, 69)
$oldTopics = [
    ['id' => 66, 'title' => 'What Is Full Stack?'],
    ['id' => 67, 'title' => 'JavaScript Full Course | Learn JavaScript In 4 Hours'],
    ['id' => 68, 'title' => 'Angular Full Course - Learn Angular In 3 Hours'],
    ['id' => 69, 'title' => 'Roadmap To Become Full Stack Developer For Beginners']
];

foreach ($oldTopics as $topicData) {
    echo "🔄 Création du topic {$topicData['id']}: {$topicData['title']}\n";
    
    // Vérifier si le topic existe déjà
    $existingTopic = Topic::find($topicData['id']);
    if ($existingTopic) {
        echo "  -> Topic {$topicData['id']} existe déjà, mise à jour...\n";
        $topic = $existingTopic;
    } else {
        echo "  -> Création du topic {$topicData['id']}...\n";
        $topic = new Topic();
    }
    
    // Créer d'abord la vidéo
    $video = Video::firstOrCreate(
        ['id' => $topicData['id'] + 10], // 76, 77, 78, 79
        [
            'title' => $topicData['title'],
            'description' => 'This is a video lesson for the course.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Rick Roll pour test
            'duration' => '03:33',
            'thumbnail' => null,
            'is_active' => true,
        ]
    );
    
    // Maintenant créer le topic avec la vidéo
    $topic->id = $topicData['id'];
    $topic->course_id = $course->id;
    $topic->chapter_id = $chapter->id;
    $topic->order = $topicData['id'] - 65; // 1, 2, 3, 4
    $topic->topicable_type = Video::class;
    $topic->topicable_id = $video->id;
    $topic->save();
    
    echo "  -> ✅ Topic {$topicData['id']} créé/mis à jour avec vidéo {$video->id}\n";
}

echo "🎉 Restauration terminée !\n";
echo "📋 Topics restaurés: 66, 67, 68, 69\n";
echo "🎬 Vidéos créées: 76, 77, 78, 79\n";
