<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\Courses\CourseSetting;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;
use Modules\LMS\Services\CertificateService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION COURS 12 ET CERTIFICATION\n";
echo "========================================\n\n";

$userId = 50;
$courseId = 12;

// 1. Vérifier le cours
$course = Course::find($courseId);
if (!$course) {
    echo "❌ Cours non trouvé\n";
    exit;
}

echo "✅ Cours: {$course->title} (ID: {$course->id})\n";

// 2. Vérifier les paramètres du cours
$courseSetting = CourseSetting::where('course_id', $courseId)->first();
if ($courseSetting) {
    echo "📋 Paramètres du cours:\n";
    echo "   - is_certificate: " . ($courseSetting->is_certificate ? 'OUI' : 'NON') . "\n";
    echo "   - is_downloadable: " . ($courseSetting->is_downloadable ? 'OUI' : 'NON') . "\n";
} else {
    echo "❌ Aucun paramètre trouvé pour ce cours\n";
}

// 3. Vérifier la progression
$topicProgress = TopicProgress::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->get();

$chapterProgress = ChapterProgress::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->get();

echo "\n📊 PROGRESSION:\n";
echo "   - Topic Progress: {$topicProgress->count()} entrées\n";
echo "   - Chapter Progress: {$chapterProgress->count()} entrées\n";

if ($topicProgress->count() > 0) {
    echo "   📚 Topics:\n";
    foreach ($topicProgress as $tp) {
        echo "     - Topic {$tp->topic_id}: {$tp->status}\n";
    }
}

if ($chapterProgress->count() > 0) {
    echo "   📖 Chapitres:\n";
    foreach ($chapterProgress as $cp) {
        echo "     - Chapter {$cp->chapter_id}: {$cp->status}\n";
    }
}

// 4. Vérifier si tous les chapitres et topics sont terminés
$totalChapters = $course->chapters()->count();
$completedChapters = $chapterProgress->where('status', 'completed')->count();

$totalTopics = 0;
foreach ($course->chapters as $chapter) {
    $totalTopics += $chapter->topics()->count();
}
$completedTopics = $topicProgress->where('status', 'completed')->count();

echo "\n📊 RÉSUMÉ:\n";
echo "   - Chapitres: {$completedChapters}/{$totalChapters}\n";
echo "   - Topics: {$completedTopics}/{$totalTopics}\n";

if ($completedChapters == $totalChapters && $completedTopics == $totalTopics) {
    echo "   ✅ TOUS LES CHAPITRES ET TOPICS SONT TERMINÉS !\n";
    
    // 5. Tester la génération de certificat
    echo "\n🧪 Test de génération de certificat...\n";
    
    try {
        $certificateService = new CertificateService();
        $result = $certificateService::generateCertificate($userId, $courseId);
        
        if ($result) {
            echo "✅ Certificat généré avec succès!\n";
            echo "   - ID: {$result->certificate_id}\n";
            echo "   - Type: {$result->type}\n";
            echo "   - Subject: {$result->subject}\n";
        } else {
            echo "❌ Échec de génération du certificat\n";
        }
    } catch (\Exception $e) {
        echo "❌ Erreur lors de la génération: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ Progression incomplète\n";
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";

