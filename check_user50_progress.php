<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;
use Modules\LMS\Models\Courses\Course;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION PROGRESSION UTILISATEUR 50\n";
echo "==========================================\n\n";

$userId = 50;

// 1. Vérifier topic_progress pour l'utilisateur 50
echo "📚 TOPIC_PROGRESS pour l'utilisateur {$userId}:\n";
echo "=============================================\n";

$topicProgresses = TopicProgress::where('user_id', $userId)->get();

if ($topicProgresses->count() > 0) {
    foreach ($topicProgresses as $progress) {
        echo "✅ TopicProgress ID: {$progress->id}\n";
        echo "   - User: {$progress->user_id}\n";
        echo "   - Course: {$progress->course_id}\n";
        echo "   - Chapter: {$progress->chapter_id}\n";
        echo "   - Topic: {$progress->topic_id}\n";
        echo "   - Status: {$progress->status}\n";
        echo "   - Progress: {$progress->progress}%\n";
        echo "   - Completed: " . ($progress->completed_at ?? 'N/A') . "\n";
        echo "   ---\n";
    }
} else {
    echo "❌ Aucune progression de topic trouvée pour l'utilisateur {$userId}\n";
}

// 2. Vérifier chapter_progress pour l'utilisateur 50
echo "\n📖 CHAPTER_PROGRESS pour l'utilisateur {$userId}:\n";
echo "===============================================\n";

$chapterProgresses = ChapterProgress::where('user_id', $userId)->get();

if ($chapterProgresses->count() > 0) {
    foreach ($chapterProgresses as $progress) {
        echo "✅ ChapterProgress ID: {$progress->id}\n";
        echo "   - User: {$progress->user_id}\n";
        echo "   - Course: {$progress->course_id}\n";
        echo "   - Chapter: {$progress->chapter_id}\n";
        echo "   - Status: {$progress->status}\n";
        echo "   - Progress: {$progress->progress}%\n";
        echo "   - Completed: " . ($progress->completed_at ?? 'N/A') . "\n";
        echo "   ---\n";
    }
} else {
    echo "❌ Aucune progression de chapitre trouvée pour l'utilisateur {$userId}\n";
}

// 3. Vérifier s'il y a d'autres tables de progression
echo "\n🔍 AUTRES TABLES DE PROGRESSION:\n";
echo "=================================\n";

// Vérifier toutes les tables qui contiennent "progress"
$tables = DB::select("SHOW TABLES");
$progressTables = [];

foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    if (strpos($tableName, 'progress') !== false) {
        $progressTables[] = $tableName;
    }
}

echo "Tables contenant 'progress':\n";
foreach ($progressTables as $table) {
    echo "   - {$table}\n";
}

// 4. Vérifier spécifiquement le cours "full-stack-web-development-bootcamp" pour l'utilisateur 50
echo "\n🎯 COURS SPÉCIFIQUE POUR L'UTILISATEUR {$userId}:\n";
echo "===============================================\n";

$course = Course::where('slug', 'full-stack-web-development-bootcamp')->first();
if ($course) {
    echo "✅ Cours trouvé: {$course->title} (ID: {$course->id})\n";
    
    // Progression pour ce cours et cet utilisateur
    $courseTopicProgress = TopicProgress::where('user_id', $userId)
        ->where('course_id', $course->id)
        ->get();
    $courseChapterProgress = ChapterProgress::where('user_id', $userId)
        ->where('course_id', $course->id)
        ->get();
    
    echo "   - Topic Progress: {$courseTopicProgress->count()} entrées\n";
    echo "   - Chapter Progress: {$courseChapterProgress->count()} entrées\n";
    
    if ($courseTopicProgress->count() > 0) {
        echo "   📚 Topics pour ce cours:\n";
        foreach ($courseTopicProgress as $tp) {
            echo "     - Topic {$tp->topic_id}: {$tp->status} (Progress: {$tp->progress}%)\n";
        }
    }
    
    if ($courseChapterProgress->count() > 0) {
        echo "   📖 Chapitres pour ce cours:\n";
        foreach ($courseChapterProgress as $cp) {
            echo "     - Chapter {$cp->chapter_id}: {$cp->status} (Progress: {$cp->progress}%)\n";
        }
    }
    
    // Vérifier si l'utilisateur a terminé TOUS les topics et chapitres
    $totalChapters = $course->chapters()->count();
    $completedChapters = $courseChapterProgress->where('status', 'completed')->count();
    
    $totalTopics = 0;
    foreach ($course->chapters as $chapter) {
        $totalTopics += $chapter->topics()->count();
    }
    $completedTopics = $courseTopicProgress->where('status', 'completed')->count();
    
    echo "\n   📊 RÉSUMÉ DE PROGRESSION:\n";
    echo "   - Chapitres: {$completedChapters}/{$totalChapters}\n";
    echo "   - Topics: {$completedTopics}/{$totalTopics}\n";
    
    if ($completedChapters == $totalChapters && $completedTopics == $totalTopics) {
        echo "   ✅ TOUS LES CHAPITRES ET TOPICS SONT TERMINÉS !\n";
        echo "   🎓 Le certificat devrait être généré automatiquement !\n";
    } else {
        echo "   ❌ Progression incomplète - certificat non généré\n";
    }
} else {
    echo "❌ Cours non trouvé\n";
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";

