<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\Courses\CourseSetting;
use Modules\LMS\Models\Certificate\Certificate;
use Modules\LMS\Models\Certificate\UserCertificate;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DIAGNOSTIC DU COURS CERTIFIÉ\n";
echo "================================\n\n";

// 1. Trouver le cours
$course = Course::where('slug', 'full-stack-web-development-bootcamp')->first();

if (!$course) {
    echo "❌ Cours non trouvé\n";
    exit;
}

echo "✅ Cours trouvé: {$course->title} (ID: {$course->id})\n";

// 2. Vérifier les paramètres du cours
$courseSetting = CourseSetting::where('course_id', $course->id)->first();
if ($courseSetting) {
    echo "📋 Paramètres du cours:\n";
    echo "   - is_certificate: " . ($courseSetting->is_certificate ? 'OUI' : 'NON') . "\n";
    echo "   - is_downloadable: " . ($courseSetting->is_downloadable ? 'OUI' : 'NON') . "\n";
} else {
    echo "❌ Aucun paramètre trouvé pour ce cours\n";
}

// 3. Vérifier les chapitres
$chapters = $course->chapters()->get();
echo "\n📚 Chapitres ({$chapters->count()}):\n";
foreach ($chapters as $chapter) {
    echo "   - Chapitre {$chapter->id}: {$chapter->title}\n";
    
    // Vérifier les topics de ce chapitre
    $topics = $chapter->topics()->get();
    echo "     Topics ({$topics->count()}):\n";
    foreach ($topics as $topic) {
        echo "       - Topic {$topic->id}: {$topic->title}\n";
    }
}

// 4. Vérifier les templates de certificat
$certificateTemplates = Certificate::where('type', 'course')->get();
echo "\n🏆 Templates de certificat disponibles ({$certificateTemplates->count()}):\n";
foreach ($certificateTemplates as $template) {
    echo "   - Template {$template->id}: {$template->title}\n";
}

// 5. Vérifier les certificats existants pour ce cours
$existingCertificates = UserCertificate::where('subject', $course->title)->get();
echo "\n📜 Certificats existants pour ce cours ({$existingCertificates->count()}):\n";
foreach ($existingCertificates as $cert) {
    echo "   - Certificat {$cert->id}: {$cert->certificate_id} (User: {$cert->user_id})\n";
}

// 6. Vérifier la progression des utilisateurs
echo "\n👥 Progression des utilisateurs:\n";
$userProgress = TopicProgress::where('course_id', $course->id)->get();
$users = $userProgress->groupBy('user_id');

foreach ($users as $userId => $progresses) {
    echo "   Utilisateur {$userId}:\n";
    $completedTopics = $progresses->where('status', 'completed')->count();
    $totalTopics = $progresses->count();
    echo "     - Topics terminés: {$completedTopics}/{$totalTopics}\n";
    
    // Vérifier les chapitres
    $chapterProgress = ChapterProgress::where('user_id', $userId)
        ->where('course_id', $course->id)
        ->get();
    $completedChapters = $chapterProgress->where('status', 'completed')->count();
    $totalChapters = $chapterProgress->count();
    echo "     - Chapitres terminés: {$completedChapters}/{$totalChapters}\n";
}

// 7. Test de génération de certificat
echo "\n🧪 TEST DE GÉNÉRATION DE CERTIFICAT:\n";
if ($courseSetting && $courseSetting->is_certificate) {
    echo "✅ Le cours est configuré pour la certification\n";
    
    if ($certificateTemplates->count() > 0) {
        echo "✅ Des templates de certificat existent\n";
        
        // Tester avec le premier utilisateur qui a de la progression
        $testUser = $userProgress->first();
        if ($testUser) {
            echo "🧪 Test avec l'utilisateur {$testUser->user_id}...\n";
            
            try {
                $certificateService = new \Modules\LMS\Services\CertificateService();
                $result = $certificateService::generateCertificate($testUser->user_id, $course->id);
                
                if ($result) {
                    echo "✅ Certificat généré avec succès!\n";
                    echo "   - ID: {$result->certificate_id}\n";
                } else {
                    echo "❌ Échec de génération du certificat\n";
                }
            } catch (\Exception $e) {
                echo "❌ Erreur lors de la génération: " . $e->getMessage() . "\n";
            }
        } else {
            echo "❌ Aucun utilisateur avec progression trouvé\n";
        }
    } else {
        echo "❌ Aucun template de certificat trouvé\n";
    }
} else {
    echo "❌ Le cours n'est pas configuré pour la certification\n";
}

echo "\n🔍 DIAGNOSTIC TERMINÉ\n";

