<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;
use Modules\LMS\Services\CertificateService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 CORRECTION DE LA PROGRESSION\n";
echo "===============================\n\n";

// 1. Trouver le cours
$course = Course::where('slug', 'full-stack-web-development-bootcamp')->first();
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

// 3. Utiliser l'utilisateur 30 (qui a déjà le chapitre terminé)
$userId = 30;

echo "\n📝 Création de la progression du topic pour l'utilisateur {$userId}...\n";

// 4. Créer la progression du topic
try {
    $topicProgress = TopicProgress::create([
        'user_id' => $userId,
        'course_id' => $course->id,
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

// 5. Maintenant tester la génération de certificat
echo "\n🧪 Test de génération de certificat...\n";

try {
    $certificateService = new CertificateService();
    $result = $certificateService::generateCertificate($userId, $course->id);
    
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

