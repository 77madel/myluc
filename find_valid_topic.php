<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Courses\Topic;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 RECHERCHE TOPICS VALIDES\n";
echo "==========================\n\n";

// 1. Vérifier tous les topics
$topics = Topic::with(['chapter.course'])->get();

echo "📚 TOUS LES TOPICS:\n";
foreach ($topics as $topic) {
    echo "✅ Topic ID: {$topic->id}\n";
    echo "   - Chapter: {$topic->chapter_id} (" . ($topic->chapter->title ?? 'N/A') . ")\n";
    echo "   - Course: " . ($topic->chapter->course->title ?? 'N/A') . "\n";
    echo "   - Topicable Type: " . ($topic->topicable_type ?? 'N/A') . "\n";
    echo "   - Topicable ID: " . ($topic->topicable_id ?? 'N/A') . "\n";
    echo "   ---\n";
}

// 2. Vérifier spécifiquement le topic 65
echo "\n🎯 VÉRIFICATION TOPIC 65:\n";
$topic65 = Topic::find(65);
if ($topic65) {
    echo "✅ Topic 65 trouvé\n";
    echo "   - Chapter: {$topic65->chapter_id}\n";
    echo "   - Course: " . ($topic65->chapter->course->title ?? 'N/A') . "\n";
} else {
    echo "❌ Topic 65 non trouvé\n";
}

// 3. Chercher des topics avec topicable_id = 65
echo "\n🔍 TOPICS AVEC TOPICABLE_ID = 65:\n";
$topicsWithTopicableId65 = Topic::where('topicable_id', 65)->get();
foreach ($topicsWithTopicableId65 as $topic) {
    echo "✅ Topic ID: {$topic->id} (Topicable ID: {$topic->topicable_id})\n";
    echo "   - Chapter: {$topic->chapter_id}\n";
    echo "   - Course: " . ($topic->chapter->course->title ?? 'N/A') . "\n";
}

echo "\n🔍 RECHERCHE TERMINÉE\n";

