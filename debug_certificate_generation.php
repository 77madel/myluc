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

echo "🔍 DEBUG GÉNÉRATION DE CERTIFICAT\n";
echo "==================================\n\n";

$userId = 30;
$courseId = 14;

echo "🎯 Test pour User: {$userId}, Course: {$courseId}\n\n";

// 1. Vérifier le cours
$course = Course::find($courseId);
if (!$course) {
    echo "❌ Cours non trouvé\n";
    exit;
}
echo "✅ Cours: {$course->title}\n";

// 2. Vérifier les paramètres du cours
$courseSetting = CourseSetting::where('course_id', $courseId)->first();
if (!$courseSetting) {
    echo "❌ Aucun paramètre de cours trouvé\n";
    exit;
}

echo "📋 Paramètres du cours:\n";
echo "   - is_certificate: " . ($courseSetting->is_certificate ? 'OUI' : 'NON') . "\n";
echo "   - is_downloadable: " . ($courseSetting->is_downloadable ? 'OUI' : 'NON') . "\n";

if (!$courseSetting->is_certificate) {
    echo "❌ Le cours n'est pas configuré pour la certification\n";
    exit;
}

// 3. Vérifier les chapitres
$totalChapters = $course->chapters()->count();
$completedChapters = ChapterProgress::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('status', 'completed')
    ->count();

echo "\n📚 Chapitres:\n";
echo "   - Total: {$totalChapters}\n";
echo "   - Terminés: {$completedChapters}\n";

if ($completedChapters < $totalChapters) {
    echo "❌ Tous les chapitres ne sont pas terminés ({$completedChapters}/{$totalChapters})\n";
    exit;
}

// 4. Vérifier les topics (via les chapitres)
$allTopics = 0;
foreach ($course->chapters as $chapter) {
    $allTopics += $chapter->topics()->count();
}

$completedTopics = TopicProgress::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('status', 'completed')
    ->count();

echo "\n📝 Topics:\n";
echo "   - Total: {$allTopics}\n";
echo "   - Terminés: {$completedTopics}\n";

if ($completedTopics < $allTopics) {
    echo "❌ Tous les topics ne sont pas terminés ({$completedTopics}/{$allTopics})\n";
    exit;
}

// 5. Vérifier les templates de certificat
$certificateTemplates = Certificate::where('type', 'course')->get();
echo "\n🏆 Templates de certificat:\n";
echo "   - Disponibles: {$certificateTemplates->count()}\n";

if ($certificateTemplates->count() == 0) {
    echo "❌ Aucun template de certificat trouvé\n";
    exit;
}

// 6. Vérifier les certificats existants
$existingCertificate = UserCertificate::where('user_id', $userId)
    ->where('subject', $course->title)
    ->where('type', 'course')
    ->first();

if ($existingCertificate) {
    echo "\n⚠️ Un certificat existe déjà:\n";
    echo "   - ID: {$existingCertificate->certificate_id}\n";
    echo "   - Date: {$existingCertificate->certificated_date}\n";
    exit;
}

echo "\n✅ Toutes les conditions sont remplies !\n";
echo "🧪 Tentative de génération du certificat...\n";

try {
    $certificateService = new \Modules\LMS\Services\CertificateService();
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
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🔍 DEBUG TERMINÉ\n";
