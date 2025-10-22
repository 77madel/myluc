<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\TopicProgress;

echo "🔧 CRÉATION DE LA PROGRESSION AVEC TOPICABLE_ID\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Récupérer le topic 111
$topic = Topic::find(111);
if (!$topic) {
    echo "❌ Topic 111 non trouvé\n";
    exit;
}

echo "📋 Topic trouvé: {$topic->id}\n";
echo "🎯 Topicable ID: {$topic->topicable_id}\n";
echo "📖 Chapitre: {$topic->chapter_id}\n";
echo "📚 Cours: {$topic->course_id}\n";

// Supprimer l'ancienne progression avec topic_id = 111
$oldProgress = TopicProgress::where('user_id', 50)
    ->where('topic_id', 111)
    ->first();

if ($oldProgress) {
    echo "🗑️ Suppression de l'ancienne progression (topic_id=111)\n";
    $oldProgress->delete();
}

// Créer la nouvelle progression avec topicable_id = 65
$progress = TopicProgress::create([
    'user_id' => 50,
    'topic_id' => $topic->topicable_id, // Utiliser topicable_id (65)
    'chapter_id' => $topic->chapter_id,
    'course_id' => $topic->course_id,
    'status' => 'completed',
    'started_at' => now()->subMinutes(10),
    'completed_at' => now(),
    'time_spent' => 600, // 10 minutes
]);

echo "✅ Nouvelle progression créée:\n";
echo "   Topic ID: {$progress->topic_id}\n";
echo "   Status: {$progress->status}\n";
echo "   Started: {$progress->started_at}\n";
echo "   Completed: {$progress->completed_at}\n";

// Vérifier que la progression existe maintenant
$checkProgress = TopicProgress::where('user_id', 50)
    ->where('topic_id', $topic->topicable_id)
    ->first();

if ($checkProgress) {
    echo "✅ Vérification: Progression trouvée avec topic_id={$topic->topicable_id}\n";
} else {
    echo "❌ Vérification: Progression non trouvée\n";
}

echo "\n🎉 PROGRESSION CRÉÉE AVEC SUCCÈS !\n";
echo "🔄 Rechargez la page pour voir les icônes vertes ✅\n";

