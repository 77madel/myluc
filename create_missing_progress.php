<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;

echo "🔧 CRÉATION DE LA PROGRESSION MANQUANTE\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Récupérer le topic 111
$topic = Topic::find(111);
if (!$topic) {
    echo "❌ Topic 111 non trouvé\n";
    exit;
}

echo "📋 Topic trouvé: {$topic->id}\n";
echo "📖 Chapitre: {$topic->chapter_id}\n";
echo "📚 Cours: {$topic->course_id}\n";

// Créer la progression pour le topic 111
$progress = TopicProgress::create([
    'user_id' => 50,
    'topic_id' => 111,
    'chapter_id' => $topic->chapter_id,
    'course_id' => $topic->course_id,
    'status' => 'completed',
    'started_at' => now()->subMinutes(10),
    'completed_at' => now(),
    'time_spent' => 600, // 10 minutes
]);

echo "✅ Progression créée pour topic 111:\n";
echo "   Status: {$progress->status}\n";
echo "   Started: {$progress->started_at}\n";
echo "   Completed: {$progress->completed_at}\n";

// Vérifier si le chapitre est terminé
$chapterTopics = Topic::where('chapter_id', $topic->chapter_id)->count();
$completedTopics = TopicProgress::where('user_id', 50)
    ->where('chapter_id', $topic->chapter_id)
    ->where('status', 'completed')
    ->count();

echo "\n📊 Chapitre {$topic->chapter_id}:\n";
echo "   Topics total: {$chapterTopics}\n";
echo "   Topics terminés: {$completedTopics}\n";

if ($completedTopics >= $chapterTopics) {
    // Créer la progression du chapitre
    $chapterProgress = ChapterProgress::create([
        'user_id' => 50,
        'chapter_id' => $topic->chapter_id,
        'course_id' => $topic->course_id,
        'status' => 'completed',
        'started_at' => now()->subMinutes(15),
        'completed_at' => now(),
        'time_spent' => 900, // 15 minutes
    ]);
    
    echo "✅ Chapitre marqué comme terminé\n";
}

echo "\n🎉 PROGRESSION CRÉÉE AVEC SUCCÈS !\n";
echo "🔄 Rechargez la page pour voir les icônes vertes ✅\n";
