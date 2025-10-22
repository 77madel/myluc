<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;
use Modules\LMS\Models\Courses\Course;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION DERNIÈRE PROGRESSION\n";
echo "====================================\n\n";

// 1. Vérifier les dernières progressions créées
echo "📚 DERNIÈRES PROGRESSIONS DE TOPIC:\n";
echo "==================================\n";

$latestTopicProgress = TopicProgress::orderBy('created_at', 'desc')->limit(5)->get();
foreach ($latestTopicProgress as $progress) {
    echo "✅ TopicProgress ID: {$progress->id}\n";
    echo "   - User: {$progress->user_id}\n";
    echo "   - Course: {$progress->course_id}\n";
    echo "   - Chapter: {$progress->chapter_id}\n";
    echo "   - Topic: {$progress->topic_id}\n";
    echo "   - Status: {$progress->status}\n";
    echo "   - Progress: {$progress->progress}%\n";
    echo "   - Completed: " . ($progress->completed_at ?? 'N/A') . "\n";
    echo "   - Created: {$progress->created_at}\n";
    echo "   ---\n";
}

echo "\n📖 DERNIÈRES PROGRESSIONS DE CHAPITRE:\n";
echo "=====================================\n";

$latestChapterProgress = ChapterProgress::orderBy('created_at', 'desc')->limit(5)->get();
foreach ($latestChapterProgress as $progress) {
    echo "✅ ChapterProgress ID: {$progress->id}\n";
    echo "   - User: {$progress->user_id}\n";
    echo "   - Course: {$progress->course_id}\n";
    echo "   - Chapter: {$progress->chapter_id}\n";
    echo "   - Status: {$progress->status}\n";
    echo "   - Progress: {$progress->progress}%\n";
    echo "   - Completed: " . ($progress->completed_at ?? 'N/A') . "\n";
    echo "   - Created: {$progress->created_at}\n";
    echo "   ---\n";
}

// 2. Vérifier les certificats récents
echo "\n🏆 CERTIFICATS RÉCENTS:\n";
echo "=======================\n";

$recentCertificates = DB::table('user_certificates')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($recentCertificates as $cert) {
    echo "✅ Certificat ID: {$cert->id}\n";
    echo "   - User: {$cert->user_id}\n";
    echo "   - Certificate ID: {$cert->certificate_id}\n";
    echo "   - Type: {$cert->type}\n";
    echo "   - Subject: {$cert->subject}\n";
    echo "   - Date: {$cert->certificated_date}\n";
    echo "   - Created: {$cert->created_at}\n";
    echo "   ---\n";
}

// 3. Vérifier les quiz dans les cours
echo "\n🧪 QUIZ DANS LES COURS:\n";
echo "======================\n";

$courses = Course::with(['chapters.topics'])->get();
foreach ($courses as $course) {
    $quizCount = 0;
    $totalTopics = 0;
    
    foreach ($course->chapters as $chapter) {
        $topics = $chapter->topics;
        $totalTopics += $topics->count();
        
        foreach ($topics as $topic) {
            if ($topic->topic_type && $topic->topic_type->slug === 'quiz') {
                $quizCount++;
            }
        }
    }
    
    echo "📚 Cours: {$course->title} (ID: {$course->id})\n";
    echo "   - Total Topics: {$totalTopics}\n";
    echo "   - Quiz: {$quizCount}\n";
    echo "   - Autres: " . ($totalTopics - $quizCount) . "\n";
    echo "   ---\n";
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";

