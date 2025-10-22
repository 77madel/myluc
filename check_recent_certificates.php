<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Certificate\UserCertificate;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION CERTIFICATS RÉCENTS\n";
echo "===================================\n\n";

// 1. Vérifier les certificats récents
$recentCertificates = UserCertificate::orderBy('created_at', 'desc')->limit(3)->get();

foreach ($recentCertificates as $cert) {
    echo "✅ Certificat ID: {$cert->id}\n";
    echo "   - Certificate ID: {$cert->certificate_id}\n";
    echo "   - User: {$cert->user_id}\n";
    echo "   - Subject: {$cert->subject}\n";
    echo "   - Date: {$cert->certificated_date}\n";
    
    // Vérifier le type de template utilisé
    if (strpos($cert->certificate_content, 'certificate-container') !== false) {
        echo "   ✅ NOUVEAU TEMPLATE PROFESSIONNEL ✓\n";
    } elseif (strpos($cert->certificate_content, 'certificate-template-container') !== false) {
        echo "   ❌ ANCIEN TEMPLATE (certificate-template-container)\n";
    } else {
        echo "   ❓ TEMPLATE INCONNU\n";
    }
    
    echo "   📄 Contenu (preview):\n";
    echo "   " . substr(strip_tags($cert->certificate_content), 0, 100) . "...\n";
    echo "   ---\n";
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";

