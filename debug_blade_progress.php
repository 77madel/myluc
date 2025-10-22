<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\TopicProgress;

echo "🔍 DIAGNOSTIC COMPLET DU CODE BLADE\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Simuler exactement ce que fait le code Blade
$topic = Topic::find(111);
$userId = 50; // L'utilisateur connecté

echo "📋 Topic trouvé: {$topic->id}\n";
echo "👤 User ID: {$userId}\n";

// Ce que fait le code Blade actuellement
$realTopicId = $topic->id; // Utilise topic->id (111)
echo "🎯 Real Topic ID utilisé: {$realTopicId}\n";

// Recherche de la progression comme dans le code Blade
$topicProgress = TopicProgress::where('user_id', $userId)
    ->where('topic_id', $realTopicId)
    ->first();

echo "\n📊 RÉSULTAT DE LA RECHERCHE:\n";
if ($topicProgress) {
    echo "✅ Progression trouvée:\n";
    echo "   Status: {$topicProgress->status}\n";
    echo "   Started: {$topicProgress->started_at}\n";
    echo "   Completed: {$topicProgress->completed_at}\n";
} else {
    echo "❌ Aucune progression trouvée pour topic_id={$realTopicId}\n";
}

// Vérifier toutes les progressions de l'utilisateur
echo "\n📋 TOUTES LES PROGRESSIONS DE L'UTILISATEUR {$userId}:\n";
$allProgress = TopicProgress::where('user_id', $userId)->get();
foreach($allProgress as $progress) {
    echo "   Topic ID: {$progress->topic_id} - Status: {$progress->status}\n";
}

// Vérifier si le topic 111 a une progression
echo "\n🔍 VÉRIFICATION SPÉCIFIQUE TOPIC 111:\n";
$progress111 = TopicProgress::where('user_id', $userId)
    ->where('topic_id', 111)
    ->first();
if ($progress111) {
    echo "✅ Topic 111 a une progression: {$progress111->status}\n";
} else {
    echo "❌ Topic 111 n'a pas de progression\n";
}

echo "\n✅ DIAGNOSTIC TERMINÉ\n";

