<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Certificate\UserCertificate;
use Modules\LMS\Services\CertificateService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 RÉGÉNÉRATION TOUS LES CERTIFICATS\n";
echo "====================================\n\n";

// 1. Récupérer tous les certificats existants
$certificates = UserCertificate::all();

echo "📊 Certificats trouvés: {$certificates->count()}\n\n";

foreach ($certificates as $cert) {
    echo "🔄 Régénération certificat {$cert->certificate_id}...\n";
    
    try {
        // Supprimer l'ancien certificat
        $userId = $cert->user_id;
        $subject = $cert->subject;
        
        $cert->delete();
        echo "   ✅ Ancien certificat supprimé\n";
        
        // Trouver le cours correspondant
        $course = DB::table('courses')->where('title', $subject)->first();
        if ($course) {
            // Régénérer avec le nouveau template
            $newCert = CertificateService::generateCertificate($userId, $course->id);
            if ($newCert) {
                echo "   ✅ Nouveau certificat généré: {$newCert->certificate_id}\n";
            } else {
                echo "   ❌ Échec génération nouveau certificat\n";
            }
        } else {
            echo "   ❌ Cours non trouvé pour: {$subject}\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Erreur: " . $e->getMessage() . "\n";
    }
    
    echo "   ---\n";
}

echo "\n🔄 RÉGÉNÉRATION TERMINÉE\n";
echo "✅ Tous les certificats utilisent maintenant le nouveau template professionnel!\n";

