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

echo "🔧 CORRECTION COURS 12 - PROGRESSION ET CERTIFICATION\n";
echo "====================================================\n\n";

$userId = 50;
$courseId = 12;

// 1. Trouver le cours
$course = Course::find($courseId);
if (!$course) {
    echo "❌ Cours non trouvé\n";
    exit;
}

echo "✅ Cours: {$course->title} (ID: {$course->id})\n";

// 2. Trouver le chapitre et le topic
$chapter = $course->chapters()->first();
if (!$chapter) {
    echo "❌ Aucun chapitre trouvé\n";
    exit;
}

echo "✅ Chapitre: {$chapter->title} (ID: {$chapter->id})\n";

$topic = $chapter->topics()->first();
if (!$topic) {
    echo "❌ Aucun topic trouvé\n";
    exit;
}

echo "✅ Topic: {$topic->title} (ID: {$topic->id})\n";

// 3. Créer la progression du topic
echo "\n📝 Création de la progression du topic...\n";

try {
    $topicProgress = TopicProgress::create([
        'user_id' => $userId,
        'course_id' => $courseId,
        'chapter_id' => $chapter->id,
        'topic_id' => $topic->id,
        'status' => 'completed',
        'progress' => 100,
        'completed_at' => now(),
    ]);
    
    echo "✅ TopicProgress créé (ID: {$topicProgress->id})\n";
} catch (\Exception $e) {
    echo "❌ Erreur TopicProgress: " . $e->getMessage() . "\n";
}

// 4. Activer la certification pour ce cours
echo "\n🔧 Activation de la certification pour ce cours...\n";

try {
    $courseSetting = CourseSetting::where('course_id', $courseId)->first();
    if ($courseSetting) {
        $courseSetting->update([
            'is_certificate' => 1,
            'is_downloadable' => 1,
        ]);
        echo "✅ Certification activée pour le cours\n";
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

// 5. Maintenant tester la génération de certificat
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
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🎯 CORRECTION TERMINÉE\n";

