<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\TopicProgress;

echo "🔍 DIAGNOSTIC COMPLET DES ICÔNES\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// 1. Vérifier le topic 111
$topic = Topic::find(111);
echo "📋 Topic 111:\n";
echo "   ID: {$topic->id}\n";
echo "   Topicable ID: {$topic->topicable_id}\n";
echo "   Topicable Type: {$topic->topicable_type}\n";

// 2. Vérifier la progression pour l'utilisateur 50
$progress = TopicProgress::where('user_id', 50)
    ->where('topic_id', 111)
    ->first();

echo "\n📊 Progression utilisateur 50:\n";
if ($progress) {
    echo "   ✅ Progression trouvée:\n";
    echo "   Topic ID: {$progress->topic_id}\n";
    echo "   Status: {$progress->status}\n";
    echo "   Started: {$progress->started_at}\n";
    echo "   Completed: {$progress->completed_at}\n";
} else {
    echo "   ❌ Aucune progression trouvée\n";
}

// 3. Simuler exactement ce que fait le code Blade
echo "\n🎯 SIMULATION DU CODE BLADE:\n";
$realTopicId = $topic->id; // Ce que fait le code Blade
echo "   Real Topic ID utilisé: {$realTopicId}\n";

$topicProgress = TopicProgress::where('user_id', 50)
    ->where('topic_id', $realTopicId)
    ->first();

if ($topicProgress) {
    echo "   ✅ Progression trouvée dans le code Blade\n";
    echo "   Status: {$topicProgress->status}\n";
    
    // Simuler les conditions du code Blade
    if ($topicProgress->status === 'completed') {
        echo "   🎯 CONDITION: Status === 'completed' → ICÔNE VERTE ✅\n";
    } elseif ($topicProgress->status === 'in_progress') {
        echo "   🎯 CONDITION: Status === 'in_progress' → ICÔNE ORANGE 🟠\n";
    } else {
        echo "   🎯 CONDITION: Autre status → ICÔNE GRISE ⚪\n";
    }
} else {
    echo "   ❌ Aucune progression trouvée dans le code Blade\n";
}

// 4. Vérifier tous les utilisateurs
echo "\n👥 TOUS LES UTILISATEURS AVEC PROGRESSION:\n";
$allProgress = TopicProgress::where('topic_id', 111)->get();
foreach($allProgress as $prog) {
    echo "   User ID: {$prog->user_id} - Status: {$prog->status}\n";
}

echo "\n✅ DIAGNOSTIC TERMINÉ\n";

