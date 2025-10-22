<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Certificate\UserCertificate;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION CERTIFICAT RÉCENT\n";
echo "=================================\n\n";

// 1. Vérifier les certificats générés récemment
$recentCertificates = UserCertificate::orderBy('created_at', 'desc')->limit(1)->get();

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
    
    // Vérifier si les variables ont été remplacées
    if (strpos($cert->certificate_content, '{student_name}') !== false) {
        echo "❌ PROBLÈME: {student_name} n'a pas été remplacé\n";
    } else {
        echo "✅ {student_name} a été remplacé\n";
    }
    
    if (strpos($cert->certificate_content, '{platform_name}') !== false) {
        echo "❌ PROBLÈME: {platform_name} n'a pas été remplacé\n";
    } else {
        echo "✅ {platform_name} a été remplacé\n";
    }
    
    if (strpos($cert->certificate_content, '{course_title}') !== false) {
        echo "❌ PROBLÈME: {course_title} n'a pas été remplacé\n";
    } else {
        echo "✅ {course_title} a été remplacé\n";
    }
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";

