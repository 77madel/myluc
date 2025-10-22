<?php

require_once 'vendor/autoload.php';

use Modules\LMS\Services\CertificateService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST GÉNÉRATION CERTIFICAT UTILISATEUR 50\n";
echo "============================================\n\n";

$userId = 50;
$courseId = 14;

echo "🎯 Test pour User: {$userId}, Course: {$courseId}\n\n";

try {
    echo "🔍 Appel du CertificateService...\n";
    $result = CertificateService::generateCertificate($userId, $courseId);
    
    if ($result) {
        echo "✅ Certificat généré avec succès!\n";
        echo "   - ID: {$result->certificate_id}\n";
        echo "   - Type: {$result->type}\n";
        echo "   - Subject: {$result->subject}\n";
        echo "   - Date: {$result->certificated_date}\n";
        echo "   - User ID: {$result->user_id}\n";
    } else {
        echo "❌ Échec de génération du certificat (retour null)\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur lors de la génération: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🧪 TEST TERMINÉ\n";

