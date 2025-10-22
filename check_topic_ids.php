<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;

echo "🔍 Vérification des Topic IDs...\n";

// Vérifier les nouveaux topics (112-115)
$newTopics = Topic::whereIn('id', [112, 113, 114, 115])->get();
echo "📋 Nouveaux topics (112-115):\n";
foreach($newTopics as $topic) {
    echo "  - Topic ID: {$topic->id}\n";
    if ($topic->topicable) {
        echo "    Title: " . ($topic->topicable->title ?? 'N/A') . "\n";
        echo "    Type: " . $topic->topicable_type . "\n";
    } else {
        echo "    ❌ Pas de contenu associé\n";
    }
}

// Vérifier les anciens topics (66-69)
$oldTopics = Topic::whereIn('id', [66, 67, 68, 69])->get();
echo "\n📋 Anciens topics (66-69):\n";
foreach($oldTopics as $topic) {
    echo "  - Topic ID: {$topic->id}\n";
    if ($topic->topicable) {
        echo "    Title: " . ($topic->topicable->title ?? 'N/A') . "\n";
        echo "    Type: " . $topic->topicable_type . "\n";
    } else {
        echo "    ❌ Pas de contenu associé\n";
    }
}

echo "\n✅ Vérification terminée\n";

