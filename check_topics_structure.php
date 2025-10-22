<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Courses\Topic;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION STRUCTURE DES TOPICS\n";
echo "===================================\n\n";

// 1. Vérifier tous les topics
echo "📚 TOUS LES TOPICS:\n";
echo "==================\n";

$topics = Topic::with(['chapter.course'])->get();

foreach ($topics as $topic) {
    echo "✅ Topic ID: {$topic->id}\n";
    echo "   - Title: " . ($topic->title ?? 'N/A') . "\n";
    echo "   - Chapter: " . ($topic->chapter_id . " (" . ($topic->chapter->title ?? 'N/A') . ")") . "\n";
    echo "   - Course: " . ($topic->chapter->course->title ?? 'N/A') . "\n";
    echo "   - Topic Type: " . ($topic->topic_type_id ?? 'N/A') . "\n";
    echo "   - Topicable Type: " . ($topic->topicable_type ?? 'N/A') . "\n";
    echo "   - Topicable ID: " . ($topic->topicable_id ?? 'N/A') . "\n";
    echo "   - Order: " . ($topic->order ?? 'N/A') . "\n";
    echo "   - Created: {$topic->created_at}\n";
    echo "   ---\n";
}

// 2. Vérifier les types de topics
echo "\n🏷️ TYPES DE TOPICS:\n";
echo "===================\n";

$topicTypes = DB::table('topic_types')->get();
foreach ($topicTypes as $type) {
    echo "✅ Type ID: {$type->id}\n";
    echo "   - Name: {$type->name}\n";
    echo "   - Slug: {$type->slug}\n";
    echo "   - Status: {$type->status}\n";
    echo "   ---\n";
}

// 3. Vérifier les topics par cours
echo "\n📊 TOPICS PAR COURS:\n";
echo "===================\n";

$courses = DB::table('courses')->get();
foreach ($courses as $course) {
    $topicsCount = DB::table('topics')
        ->join('chapters', 'topics.chapter_id', '=', 'chapters.id')
        ->where('chapters.course_id', $course->id)
        ->count();
    
    echo "📚 Cours: {$course->title} (ID: {$course->id})\n";
    echo "   - Topics: {$topicsCount}\n";
    
    // Détail des topics pour ce cours
    $courseTopics = DB::table('topics')
        ->join('chapters', 'topics.chapter_id', '=', 'chapters.id')
        ->join('topic_types', 'topics.topic_type_id', '=', 'topic_types.id')
        ->where('chapters.course_id', $course->id)
        ->select('topics.*', 'topic_types.slug as type_slug')
        ->get();
    
    foreach ($courseTopics as $topic) {
        echo "     - Topic {$topic->id}: " . ($topic->title ?? 'N/A') . " (Type: {$topic->type_slug})\n";
    }
    echo "   ---\n";
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";
