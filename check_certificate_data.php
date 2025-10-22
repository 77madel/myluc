<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Certificate\UserCertificate;
use Modules\LMS\Models\Certificate\Certificate;

echo "=== VÉRIFICATION DES DONNÉES CERTIFICAT ===\n\n";

// 1. Vérifier les certificats utilisateur
echo "1. CERTIFICATS UTILISATEUR:\n";
$userCerts = UserCertificate::all();
foreach ($userCerts as $cert) {
    echo "- ID: " . $cert->id . "\n";
    echo "  User ID: " . $cert->user_id . "\n";
    echo "  Course ID: " . ($cert->course_id ?? 'NULL') . "\n";
    echo "  Certificate ID: " . $cert->certificate_id . "\n";
    echo "  Subject: " . $cert->subject . "\n";
    echo "  Type: " . $cert->type . "\n";
    echo "  Date: " . $cert->certificated_date . "\n";
    echo "---\n";
}

echo "\n2. MODÈLES DE CERTIFICATS:\n";
$certTemplates = Certificate::all();
foreach ($certTemplates as $template) {
    echo "- ID: " . $template->id . "\n";
    echo "  Title: " . $template->title . "\n";
    echo "  Type: " . $template->type . "\n";
    echo "  Status: " . $template->status . "\n";
    echo "---\n";
}
