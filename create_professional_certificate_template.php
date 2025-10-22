<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Certificate\Certificate;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎨 CRÉATION TEMPLATE CERTIFICAT PROFESSIONNEL\n";
echo "============================================\n\n";

// Template HTML professionnel
$professionalTemplate = '
<div class="certificate-container" style="
    width: 800px; 
    height: 600px; 
    margin: 0 auto; 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px; 
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
    font-family: \'Arial\', sans-serif;
">
    <!-- Background Pattern -->
    <div style="
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url(\'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>\');
        opacity: 0.3;
    "></div>
    
    <!-- Main Content -->
    <div style="
        position: relative;
        z-index: 2;
        padding: 40px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: white;
    ">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="
                font-size: 48px;
                font-weight: bold;
                color: #FFD700;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                margin-bottom: 10px;
                letter-spacing: 3px;
            ">CERTIFICAT</div>
            <div style="
                font-size: 18px;
                color: rgba(255,255,255,0.9);
                font-weight: 300;
                letter-spacing: 2px;
            ">de Réussite</div>
        </div>
        
        <!-- Achievement Section -->
        <div style="text-align: center; flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
            <div style="
                font-size: 16px;
                color: rgba(255,255,255,0.8);
                margin-bottom: 20px;
            ">Ce certificat est décerné à</div>
            
            <div style="
                font-size: 36px;
                font-weight: bold;
                color: #FFD700;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                margin-bottom: 30px;
                border-bottom: 3px solid #FFD700;
                padding-bottom: 10px;
                display: inline-block;
                min-width: 300px;
            ">{student_name}</div>
            
            <div style="
                font-size: 16px;
                color: rgba(255,255,255,0.8);
                margin-bottom: 15px;
            ">pour avoir terminé avec succès le cours</div>
            
            <div style="
                font-size: 24px;
                font-weight: 600;
                color: white;
                margin-bottom: 30px;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            ">"{course_title}"</div>
            
            <div style="
                font-size: 14px;
                color: rgba(255,255,255,0.7);
                margin-bottom: 20px;
            ">Date de completion: {course_completed_date}</div>
        </div>
        
        <!-- Footer -->
        <div style="
            display: flex;
            justify-content: space-between;
            align-items: end;
            margin-top: 30px;
        ">
            <!-- Platform -->
            <div style="text-align: left;">
                <div style="
                    font-size: 18px;
                    font-weight: bold;
                    color: #FFD700;
                    margin-bottom: 5px;
                ">{platform_name}</div>
                <div style="
                    font-size: 12px;
                    color: rgba(255,255,255,0.7);
                ">Plateforme d\'apprentissage</div>
            </div>
            
            <!-- Instructor -->
            <div style="text-align: right;">
                <div style="
                    font-size: 16px;
                    font-weight: bold;
                    color: white;
                    margin-bottom: 5px;
                ">{instructor_name}</div>
                <div style="
                    font-size: 12px;
                    color: rgba(255,255,255,0.7);
                ">Instructeur</div>
                <div style="
                    width: 150px;
                    height: 1px;
                    background: #FFD700;
                    margin-top: 20px;
                "></div>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div style="
            position: absolute;
            top: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border: 3px solid #FFD700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        ">🏆</div>
        
        <div style="
            position: absolute;
            bottom: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        ">✓</div>
    </div>
</div>';

// Mettre à jour le template existant
try {
    $template = Certificate::where('type', 'course')->first();
    if ($template) {
        $template->update([
            'certificate_content' => $professionalTemplate,
            'title' => 'Certificat Professionnel'
        ]);
        echo "✅ Template de certificat professionnel créé avec succès!\n";
        echo "📄 Le nouveau template inclut:\n";
        echo "   - Design moderne et professionnel\n";
        echo "   - Nom de l'étudiant: {student_name}\n";
        echo "   - Titre du cours: {course_title}\n";
        echo "   - Nom de l'instructeur: {instructor_name}\n";
        echo "   - Nom de la plateforme: {platform_name}\n";
        echo "   - Date de completion: {course_completed_date}\n";
        echo "   - Éléments décoratifs et signature\n";
    } else {
        echo "❌ Aucun template trouvé\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n🎨 TEMPLATE PROFESSIONNEL CRÉÉ\n";

