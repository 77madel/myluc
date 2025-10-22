<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Certificate\UserCertificate;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION CONTENU DES CERTIFICATS\n";
echo "=====================================\n\n";

// 1. Vérifier les certificats existants
$certificates = UserCertificate::orderBy('created_at', 'desc')->limit(3)->get();

foreach ($certificates as $cert) {
    echo "✅ Certificat ID: {$cert->id}\n";
    echo "   - Certificate ID: {$cert->certificate_id}\n";
    echo "   - User: {$cert->user_id}\n";
    echo "   - Subject: {$cert->subject}\n";
    echo "   - Type: {$cert->type}\n";
    echo "   - Date: {$cert->certificated_date}\n";
    echo "   - Content (preview): " . substr(strip_tags($cert->certificate_content), 0, 200) . "...\n";
    echo "   ---\n";
}

// 2. Vérifier le template de certificat
echo "\n🏆 TEMPLATE DE CERTIFICAT:\n";
echo "==========================\n";

$template = DB::table('certificates')->where('type', 'course')->first();
if ($template) {
    echo "✅ Template trouvé: {$template->title}\n";
    echo "   - Content (preview): " . substr(strip_tags($template->certificate_content), 0, 300) . "...\n";
    echo "   - Input Content: " . substr(strip_tags($template->input_content ?? ''), 0, 200) . "...\n";
} else {
    echo "❌ Aucun template trouvé\n";
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";

