<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\ChapterProgress;
use Modules\LMS\Models\TopicProgress;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST CRÉATION CHAPTER_PROGRESS\n";
echo "================================\n\n";

$userId = 50;
$topicId = 65; // Le topic qui cause l'erreur

echo "🎯 Test pour User: {$userId}, Topic: {$topicId}\n\n";

// 1. Vérifier le topic
$topic = Topic::find($topicId);
if (!$topic) {
    echo "❌ Topic non trouvé\n";
    exit;
}

echo "✅ Topic trouvé: {$topic->id} (Chapitre: {$topic->chapter_id})\n";

// 2. Vérifier la progression actuelle
$topicProgress = TopicProgress::where('user_id', $userId)
    ->where('topic_id', $topicId)
    ->first();

$chapterProgress = ChapterProgress::where('user_id', $userId)
    ->where('chapter_id', $topic->chapter_id)
    ->first();

echo "\n📊 PROGRESSION ACTUELLE:\n";
echo "   - Topic Progress: " . ($topicProgress ? $topicProgress->status : 'N/A') . "\n";
echo "   - Chapter Progress: " . ($chapterProgress ? $chapterProgress->status : 'N/A') . "\n";

// 3. Simuler la logique du contrôleur
echo "\n🔧 SIMULATION LOGIQUE CONTRÔLEUR:\n";

// Vérifier si tous les topics du chapitre sont terminés
$chapterTopics = Topic::where('chapter_id', $topic->chapter_id)->get();
$completedTopics = TopicProgress::where('user_id', $userId)
    ->where('chapter_id', $topic->chapter_id)
    ->where('status', 'completed')
    ->count();

echo "   - Topics dans le chapitre: {$chapterTopics->count()}\n";
echo "   - Topics terminés: {$completedTopics}\n";

$chapterCompleted = $completedTopics >= $chapterTopics->count();
echo "   - Chapitre terminé: " . ($chapterCompleted ? 'OUI' : 'NON') . "\n";

if ($chapterCompleted) {
    echo "\n📝 CRÉATION CHAPTER_PROGRESS:\n";
    
    if (!$chapterProgress) {
        try {
            $newChapterProgress = ChapterProgress::create([
                'user_id' => $userId,
                'chapter_id' => $topic->chapter_id,
                'course_id' => $topic->course_id,
                'status' => 'not_started',
            ]);
            
            echo "✅ ChapterProgress créé (ID: {$newChapterProgress->id})\n";
            
            // Marquer comme terminé
            $newChapterProgress->markAsCompleted();
            echo "✅ ChapterProgress marqué comme terminé\n";
            
        } catch (\Exception $e) {
            echo "❌ Erreur création ChapterProgress: " . $e->getMessage() . "\n";
        }
    } else {
        echo "✅ ChapterProgress existe déjà\n";
        $chapterProgress->markAsCompleted();
        echo "✅ ChapterProgress marqué comme terminé\n";
    }
} else {
    echo "❌ Le chapitre n'est pas encore terminé\n";
}

echo "\n🧪 TEST TERMINÉ\n";

