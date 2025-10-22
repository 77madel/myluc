<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\TopicProgress;

echo "🔍 Vérification des relations topics...\n";

// Vérifier les topics 65 et 111
$topics = Topic::whereIn('id', [65, 111])->get();

foreach($topics as $topic) {
    echo "\n📋 Topic ID: {$topic->id}\n";
    echo "   Topicable ID: {$topic->topicable_id}\n";
    echo "   Topicable Type: {$topic->topicable_type}\n";
    
    if ($topic->topicable) {
        echo "   Content Title: " . ($topic->topicable->title ?? 'N/A') . "\n";
    }
    
    // Vérifier la progression avec l'ID du topic
    $progressWithTopicId = TopicProgress::where('user_id', 1)
        ->where('topic_id', $topic->id)
        ->first();
    
    // Vérifier la progression avec l'ID du topicable
    $progressWithTopicableId = TopicProgress::where('user_id', 1)
        ->where('topic_id', $topic->topicable_id)
        ->first();
    
    echo "   Progress avec topic_id={$topic->id}: " . ($progressWithTopicId ? $progressWithTopicId->status : 'NONE') . "\n";
    echo "   Progress avec topic_id={$topic->topicable_id}: " . ($progressWithTopicableId ? $progressWithTopicableId->status : 'NONE') . "\n";
}

echo "\n✅ Vérification terminée\n";

