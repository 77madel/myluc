<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;

echo "🔍 TEST DE LA PROGRESSION DES CHAPITRES\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Vérifier le chapitre 22
$chapterId = 22;
$userId = 50;

echo "📖 Chapitre: {$chapterId}\n";
echo "👤 Utilisateur: {$userId}\n\n";

// Vérifier tous les topics du chapitre
$chapterTopics = Topic::where('chapter_id', $chapterId)->get();
echo "📋 Topics dans le chapitre:\n";
foreach($chapterTopics as $topic) {
    echo "   Topic ID: {$topic->id} - Title: " . ($topic->topicable->title ?? 'N/A') . "\n";
}

// Vérifier les progressions des topics
$completedTopics = TopicProgress::where('user_id', $userId)
    ->where('chapter_id', $chapterId)
    ->where('status', 'completed')
    ->get();

echo "\n📊 Progressions terminées:\n";
foreach($completedTopics as $progress) {
    echo "   Topic ID: {$progress->topic_id} - Status: {$progress->status}\n";
}

// Vérifier si le chapitre est terminé
$chapterCompleted = $completedTopics->count() >= $chapterTopics->count();
echo "\n🎯 Chapitre terminé: " . ($chapterCompleted ? 'OUI ✅' : 'NON ❌') . "\n";

// Vérifier la progression du chapitre
$chapterProgress = ChapterProgress::where('user_id', $userId)
    ->where('chapter_id', $chapterId)
    ->first();

if ($chapterProgress) {
    echo "📈 Progression du chapitre:\n";
    echo "   Status: {$chapterProgress->status}\n";
    echo "   Started: {$chapterProgress->started_at}\n";
    echo "   Completed: {$chapterProgress->completed_at}\n";
} else {
    echo "❌ Aucune progression de chapitre trouvée\n";
}

echo "\n✅ TEST TERMINÉ\n";

