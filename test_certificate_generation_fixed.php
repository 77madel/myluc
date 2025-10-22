<?php

require_once 'vendor/autoload.php';

use Modules\LMS\Services\CertificateService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST GÉNÉRATION CERTIFICAT CORRIGÉ\n";
echo "====================================\n\n";

$userId = 50;
$courseId = 12;

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
        
        echo "\n📄 Contenu du certificat:\n";
        echo "========================\n";
        echo $result->certificate_content;
        
        // Vérifier si le nom est correctement affiché
        if (strpos($result->certificate_content, 'Madou KONE') !== false) {
            echo "\n✅ SUCCÈS: Le nom 'Madou KONE' est correctement affiché!\n";
        } else {
            echo "\n❌ PROBLÈME: Le nom n'est pas correctement affiché\n";
        }
    } else {
        echo "❌ Échec de génération du certificat (retour null)\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur lors de la génération: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🧪 TEST TERMINÉ\n";

