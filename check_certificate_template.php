<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Certificate\Certificate;
use Modules\LMS\Models\Certificate\UserCertificate;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION TEMPLATE ET CONTENU CERTIFICAT\n";
echo "=============================================\n\n";

// 1. Vérifier le template de certificat
echo "🏆 TEMPLATE DE CERTIFICAT:\n";
echo "==========================\n";

$template = Certificate::where('type', 'course')->first();
if ($template) {
    echo "✅ Template trouvé: {$template->title}\n";
    echo "📄 Contenu du template:\n";
    echo "=======================\n";
    echo $template->certificate_content;
    echo "\n\n";
    
    echo "📝 Input Content:\n";
    echo "================\n";
    echo $template->input_content ?? 'N/A';
    echo "\n\n";
} else {
    echo "❌ Aucun template trouvé\n";
}

// 2. Vérifier les certificats générés récemment
echo "📜 CERTIFICATS GÉNÉRÉS RÉCEMMENT:\n";
echo "=================================\n";

$recentCertificates = UserCertificate::orderBy('created_at', 'desc')->limit(2)->get();
foreach ($recentCertificates as $cert) {
    echo "✅ Certificat ID: {$cert->id}\n";
    echo "   - Certificate ID: {$cert->certificate_id}\n";
    echo "   - User: {$cert->user_id}\n";
    echo "   - Subject: {$cert->subject}\n";
    echo "   - Date: {$cert->certificated_date}\n";
    echo "📄 Contenu du certificat:\n";
    echo "========================\n";
    echo $cert->certificate_content;
    echo "\n\n";
}

echo "🔍 VÉRIFICATION TERMINÉE\n";

