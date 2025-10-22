<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;
use Modules\LMS\Models\Courses\Course;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION DES PROGRESSIONS EN BASE\n";
echo "========================================\n\n";

// 1. Vérifier topic_progress
echo "📚 TOPIC_PROGRESS:\n";
echo "==================\n";

$topicProgresses = TopicProgress::with(['user', 'course', 'topic'])->get();

if ($topicProgresses->count() > 0) {
    foreach ($topicProgresses as $progress) {
        echo "✅ TopicProgress ID: {$progress->id}\n";
        echo "   - User: {$progress->user_id} (" . ($progress->user->name ?? 'N/A') . ")\n";
        echo "   - Course: {$progress->course_id} (" . ($progress->course->title ?? 'N/A') . ")\n";
        echo "   - Chapter: {$progress->chapter_id}\n";
        echo "   - Topic: {$progress->topic_id} (" . ($progress->topic->title ?? 'N/A') . ")\n";
        echo "   - Status: {$progress->status}\n";
        echo "   - Progress: {$progress->progress}%\n";
        echo "   - Completed: " . ($progress->completed_at ?? 'N/A') . "\n";
        echo "   - Created: {$progress->created_at}\n";
        echo "   ---\n";
    }
} else {
    echo "❌ Aucune progression de topic trouvée\n";
}

echo "\n📖 CHAPTER_PROGRESS:\n";
echo "===================\n";

$chapterProgresses = ChapterProgress::with(['user', 'course', 'chapter'])->get();

if ($chapterProgresses->count() > 0) {
    foreach ($chapterProgresses as $progress) {
        echo "✅ ChapterProgress ID: {$progress->id}\n";
        echo "   - User: {$progress->user_id} (" . ($progress->user->name ?? 'N/A') . ")\n";
        echo "   - Course: {$progress->course_id} (" . ($progress->course->title ?? 'N/A') . ")\n";
        echo "   - Chapter: {$progress->chapter_id} (" . ($progress->chapter->title ?? 'N/A') . ")\n";
        echo "   - Status: {$progress->status}\n";
        echo "   - Progress: {$progress->progress}%\n";
        echo "   - Completed: " . ($progress->completed_at ?? 'N/A') . "\n";
        echo "   - Created: {$progress->created_at}\n";
        echo "   ---\n";
    }
} else {
    echo "❌ Aucune progression de chapitre trouvée\n";
}

// 3. Vérifier les certificats existants
echo "\n🏆 CERTIFICATS EXISTANTS:\n";
echo "========================\n";

$certificates = DB::table('user_certificates')->get();
foreach ($certificates as $cert) {
    echo "✅ Certificat ID: {$cert->id}\n";
    echo "   - User: {$cert->user_id}\n";
    echo "   - Certificate ID: {$cert->certificate_id}\n";
    echo "   - Type: {$cert->type}\n";
    echo "   - Subject: {$cert->subject}\n";
    echo "   - Date: {$cert->certificated_date}\n";
    echo "   ---\n";
}

// 4. Vérifier spécifiquement le cours "full-stack-web-development-bootcamp"
echo "\n🎯 COURS SPÉCIFIQUE:\n";
echo "===================\n";

$course = Course::where('slug', 'full-stack-web-development-bootcamp')->first();
if ($course) {
    echo "✅ Cours trouvé: {$course->title} (ID: {$course->id})\n";
    
    // Progression pour ce cours
    $courseTopicProgress = TopicProgress::where('course_id', $course->id)->get();
    $courseChapterProgress = ChapterProgress::where('course_id', $course->id)->get();
    
    echo "   - Topic Progress: {$courseTopicProgress->count()} entrées\n";
    echo "   - Chapter Progress: {$courseChapterProgress->count()} entrées\n";
    
    if ($courseTopicProgress->count() > 0) {
        echo "   📚 Topics terminés:\n";
        foreach ($courseTopicProgress as $tp) {
            echo "     - User {$tp->user_id}: Topic {$tp->topic_id} ({$tp->status})\n";
        }
    }
    
    if ($courseChapterProgress->count() > 0) {
        echo "   📖 Chapitres terminés:\n";
        foreach ($courseChapterProgress as $cp) {
            echo "     - User {$cp->user_id}: Chapter {$cp->chapter_id} ({$cp->status})\n";
        }
    }
} else {
    echo "❌ Cours non trouvé\n";
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";

