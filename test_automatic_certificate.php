<?php

require_once 'vendor/autoload.php';

use Modules\LMS\Http\Controllers\Student\TopicProgressController;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TEST SYSTÈME AUTOMATIQUE DE CERTIFICAT\n";
echo "========================================\n\n";

$userId = 50;
$topicId = 124; // Le topic du cours 14

echo "🎯 Test pour User: {$userId}, Topic: {$topicId}\n\n";

try {
    // Simuler une requête AJAX pour marquer le topic comme terminé
    $request = new Request();
    $request->merge([
        'user_id' => $userId,
        'topic_id' => $topicId,
    ]);
    
    echo "🔍 Appel du TopicProgressController::markAsCompleted...\n";
    
    $controller = new TopicProgressController();
    $response = $controller->markAsCompleted($topicId);
    
    $responseData = $response->getData(true);
    
    echo "📊 Réponse du contrôleur:\n";
    echo "   - Status: " . ($responseData['status'] ?? 'N/A') . "\n";
    echo "   - Message: " . ($responseData['message'] ?? 'N/A') . "\n";
    echo "   - Chapter Completed: " . ($responseData['chapter_completed'] ? 'OUI' : 'NON') . "\n";
    echo "   - Certificate Generated: " . ($responseData['certificate_generated'] ? 'OUI' : 'NON') . "\n";
    
    if ($responseData['certificate_generated'] ?? false) {
        echo "✅ Certificat généré automatiquement !\n";
    } else {
        echo "❌ Certificat non généré automatiquement\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🧪 TEST TERMINÉ\n";
