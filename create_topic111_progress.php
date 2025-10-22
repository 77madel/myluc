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

echo "🔧 CRÉATION PROGRESSION TOPIC 111 POUR UTILISATEUR 50\n";
echo "===================================================\n\n";

$userId = 50;
$topicId = 111;
$courseId = 12;
$chapterId = 22;

// 1. Vérifier le topic
$topic = DB::table('topics')->where('id', $topicId)->first();
if (!$topic) {
    echo "❌ Topic non trouvé\n";
    exit;
}

echo "✅ Topic: {$topicId} (Chapitre: {$topic->chapter_id})\n";

// 2. Créer la progression du topic
echo "\n📝 Création de la progression du topic...\n";

try {
    $topicProgress = TopicProgress::create([
        'user_id' => $userId,
        'course_id' => $courseId,
        'chapter_id' => $chapterId,
        'topic_id' => $topicId,
        'status' => 'completed',
        'progress' => 100,
        'completed_at' => now(),
    ]);
    
    echo "✅ TopicProgress créé (ID: {$topicProgress->id})\n";
} catch (\Exception $e) {
    echo "❌ Erreur TopicProgress: " . $e->getMessage() . "\n";
}

// 3. Activer la certification pour le cours 12
echo "\n🔧 Activation de la certification pour le cours 12...\n";

try {
    $courseSetting = CourseSetting::where('course_id', $courseId)->first();
    if ($courseSetting) {
        $courseSetting->update([
            'is_certificate' => 1,
            'is_downloadable' => 1,
        ]);
        echo "✅ Certification activée pour le cours 12\n";
    } else {
        // Créer les paramètres du cours
        CourseSetting::create([
            'course_id' => $courseId,
            'is_certificate' => 1,
            'is_downloadable' => 1,
        ]);
        echo "✅ Paramètres de cours créés avec certification activée\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur activation certification: " . $e->getMessage() . "\n";
}

// 4. Vérifier la progression complète
echo "\n📊 VÉRIFICATION PROGRESSION COMPLÈTE:\n";

$course = Course::find($courseId);
$totalChapters = $course->chapters()->count();
$completedChapters = ChapterProgress::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('status', 'completed')
    ->count();

$totalTopics = 0;
foreach ($course->chapters as $chapter) {
    $totalTopics += $chapter->topics()->count();
}
$completedTopics = TopicProgress::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('status', 'completed')
    ->count();

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
            echo "   - Date: {$result->certificated_date}\n";
        } else {
            echo "❌ Échec de génération du certificat\n";
        }
    } catch (\Exception $e) {
        echo "❌ Erreur lors de la génération: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ Progression incomplète\n";
}

echo "\n🎯 PROGRESSION CRÉÉE\n";

