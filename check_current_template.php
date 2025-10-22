<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Certificate\Certificate;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION TEMPLATE ACTUEL\n";
echo "==============================\n\n";

// 1. Vérifier le template actuel
$template = Certificate::where('type', 'course')->first();
if ($template) {
    echo "✅ Template trouvé: {$template->title}\n";
    echo "📄 Contenu actuel (preview):\n";
    echo "============================\n";
    echo substr($template->certificate_content, 0, 500) . "...\n\n";
    
    // Vérifier si c'est le nouveau template professionnel
    if (strpos($template->certificate_content, 'certificate-container') !== false) {
        echo "✅ NOUVEAU TEMPLATE PROFESSIONNEL DÉTECTÉ ✓\n";
    } else {
        echo "❌ ANCIEN TEMPLATE DÉTECTÉ - Mise à jour nécessaire\n";
    }
    
    // Vérifier les variables
    $variables = ['{student_name}', '{course_title}', '{instructor_name}', '{platform_name}', '{course_completed_date}'];
    echo "\n🔍 Variables présentes:\n";
    foreach ($variables as $var) {
        if (strpos($template->certificate_content, $var) !== false) {
            echo "   ✅ {$var}\n";
        } else {
            echo "   ❌ {$var}\n";
        }
    }
} else {
    echo "❌ Aucun template trouvé\n";
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";

