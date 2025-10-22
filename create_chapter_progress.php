<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\ChapterProgress;

echo "🔧 CRÉATION DE LA PROGRESSION DU CHAPITRE\n";
echo "=" . str_repeat("=", 40) . "\n\n";

$chapterId = 22;
$userId = 50;

// Récupérer le chapitre
$chapter = \Modules\LMS\Models\Courses\Chapter::find($chapterId);
if (!$chapter) {
    echo "❌ Chapitre {$chapterId} non trouvé\n";
    exit;
}

echo "📖 Chapitre trouvé: {$chapter->title}\n";
echo "📚 Cours: {$chapter->course_id}\n";

// Créer la progression du chapitre
$chapterProgress = ChapterProgress::create([
    'user_id' => $userId,
    'chapter_id' => $chapterId,
    'course_id' => $chapter->course_id,
    'status' => 'completed',
    'started_at' => now()->subMinutes(20),
    'completed_at' => now(),
    'time_spent' => 1200, // 20 minutes
]);

echo "✅ Progression du chapitre créée:\n";
echo "   Status: {$chapterProgress->status}\n";
echo "   Started: {$chapterProgress->started_at}\n";
echo "   Completed: {$chapterProgress->completed_at}\n";

// Vérifier que la progression existe maintenant
$checkProgress = ChapterProgress::where('user_id', $userId)
    ->where('chapter_id', $chapterId)
    ->first();

if ($checkProgress) {
    echo "✅ Vérification: Progression du chapitre trouvée\n";
} else {
    echo "❌ Vérification: Progression du chapitre non trouvée\n";
}

echo "\n🎉 PROGRESSION DU CHAPITRE CRÉÉE AVEC SUCCÈS !\n";

