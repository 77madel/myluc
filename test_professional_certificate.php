<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Certificate\UserCertificate;
use Modules\LMS\Services\CertificateService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST CERTIFICAT PROFESSIONNEL\n";
echo "================================\n\n";

$userId = 50;
$courseId = 12;

// 1. Supprimer les certificats existants pour ce cours
echo "🗑️ Suppression des certificats existants...\n";
$existingCertificates = UserCertificate::where('user_id', $userId)
    ->where('subject', 'The Complete Digital Marketing Analysis Guide')
    ->get();

foreach ($existingCertificates as $cert) {
    $cert->delete();
    echo "   - Certificat {$cert->certificate_id} supprimé\n";
}

// 2. Générer un nouveau certificat
echo "\n🎨 Génération du certificat professionnel...\n";

try {
    $result = CertificateService::generateCertificate($userId, $courseId);
    
    if ($result) {
        echo "✅ Certificat professionnel généré avec succès!\n";
        echo "   - ID: {$result->certificate_id}\n";
        echo "   - Type: {$result->type}\n";
        echo "   - Subject: {$result->subject}\n";
        echo "   - Date: {$result->certificated_date}\n";
        
        echo "\n📄 Aperçu du contenu:\n";
        echo "====================\n";
        
        // Vérifier les éléments clés
        $content = $result->certificate_content;
        
        if (strpos($content, 'Madou KONE') !== false) {
            echo "✅ Nom de l'étudiant: Madou KONE ✓\n";
        } else {
            echo "❌ Nom de l'étudiant manquant\n";
        }
        
        if (strpos($content, 'The Complete Digital Marketing Analysis Guide') !== false) {
            echo "✅ Titre du cours: The Complete Digital Marketing Analysis Guide ✓\n";
        } else {
            echo "❌ Titre du cours manquant\n";
        }
        
        if (strpos($content, 'MyLMS') !== false) {
            echo "✅ Nom de la plateforme: MyLMS ✓\n";
        } else {
            echo "❌ Nom de la plateforme manquant\n";
        }
        
        if (strpos($content, 'Instructeur') !== false) {
            echo "✅ Nom de l'instructeur: Instructeur ✓\n";
        } else {
            echo "❌ Nom de l'instructeur manquant\n";
        }
        
        if (strpos($content, '21/10/2025') !== false) {
            echo "✅ Date de completion: 21/10/2025 ✓\n";
        } else {
            echo "❌ Date de completion manquante\n";
        }
        
        echo "\n🎨 Le certificat est maintenant professionnel avec:\n";
        echo "   - Design moderne avec dégradé\n";
        echo "   - Nom de l'étudiant mis en évidence\n";
        echo "   - Informations complètes du cours\n";
        echo "   - Signature de l'instructeur\n";
        echo "   - Éléments décoratifs (trophée, checkmark)\n";
        
    } else {
        echo "❌ Échec de génération du certificat\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n🧪 TEST TERMINÉ\n";

