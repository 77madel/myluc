<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\Courses\Topics\Video;

echo "🔍 ANALYSE COMPLÈTE DE LA BASE DE DONNÉES\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// 1. Analyser la structure des topics
echo "📋 STRUCTURE DES TOPICS:\n";
$topics = Topic::whereIn('id', [65, 111])->get();
foreach($topics as $topic) {
    echo "  Topic ID: {$topic->id}\n";
    echo "  Topicable ID: {$topic->topicable_id}\n";
    echo "  Topicable Type: {$topic->topicable_type}\n";
    echo "  Course ID: {$topic->course_id}\n";
    echo "  Chapter ID: {$topic->chapter_id}\n";
    echo "  Order: {$topic->order}\n";
    
    if ($topic->topicable) {
        echo "  Content Title: " . ($topic->topicable->title ?? 'N/A') . "\n";
        echo "  Content ID: {$topic->topicable->id}\n";
    }
    echo "  " . str_repeat("-", 30) . "\n";
}

// 2. Analyser la table topic_progress
echo "\n📊 TABLE TOPIC_PROGRESS:\n";
$progresses = TopicProgress::where('user_id', 1)->get();
foreach($progresses as $progress) {
    echo "  Progress ID: {$progress->id}\n";
    echo "  User ID: {$progress->user_id}\n";
    echo "  Topic ID: {$progress->topic_id}\n";
    echo "  Chapter ID: {$progress->chapter_id}\n";
    echo "  Course ID: {$progress->course_id}\n";
    echo "  Status: {$progress->status}\n";
    echo "  Started At: {$progress->started_at}\n";
    echo "  Completed At: {$progress->completed_at}\n";
    echo "  " . str_repeat("-", 30) . "\n";
}

// 3. Analyser les vidéos
echo "\n🎬 TABLE VIDEOS:\n";
$videos = Video::whereIn('id', [65, 111])->get();
foreach($videos as $video) {
    echo "  Video ID: {$video->id}\n";
    echo "  Title: {$video->title}\n";
    echo "  URL: {$video->video_url}\n";
    echo "  Duration: {$video->duration}\n";
    echo "  " . str_repeat("-", 30) . "\n";
}

// 4. Vérifier les relations
echo "\n🔗 RELATIONS:\n";
foreach($topics as $topic) {
    echo "  Topic {$topic->id}:\n";
    
    // Vérifier la progression avec l'ID du topic
    $progressWithTopicId = TopicProgress::where('user_id', 1)
        ->where('topic_id', $topic->id)
        ->first();
    echo "    Progress avec topic_id={$topic->id}: " . ($progressWithTopicId ? $progressWithTopicId->status : 'NONE') . "\n";
    
    // Vérifier la progression avec l'ID du topicable
    $progressWithTopicableId = TopicProgress::where('user_id', 1)
        ->where('topic_id', $topic->topicable_id)
        ->first();
    echo "    Progress avec topic_id={$topic->topicable_id}: " . ($progressWithTopicableId ? $progressWithTopicableId->status : 'NONE') . "\n";
    
    // Vérifier si le topicable existe
    if ($topic->topicable) {
        echo "    Topicable existe: OUI (ID: {$topic->topicable->id})\n";
    } else {
        echo "    Topicable existe: NON\n";
    }
}

echo "\n✅ ANALYSE TERMINÉE\n";

