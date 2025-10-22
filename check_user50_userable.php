<?php

require_once 'vendor/autoload.php';

use Modules\LMS\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION USERABLE UTILISATEUR 50\n";
echo "======================================\n\n";

$userId = 50;

// 1. Vérifier l'utilisateur LMS
$user = User::find($userId);
if (!$user) {
    echo "❌ Utilisateur non trouvé\n";
    exit;
}

echo "✅ Utilisateur LMS trouvé:\n";
echo "   - ID: {$user->id}\n";
echo "   - Username: '" . ($user->username ?? 'NULL') . "'\n";
echo "   - Email: '" . ($user->email ?? 'NULL') . "'\n";
echo "   - Userable Type: '" . ($user->userable_type ?? 'NULL') . "'\n";
echo "   - Userable ID: " . ($user->userable_id ?? 'NULL') . "\n";

// 2. Vérifier le userable
if ($user->userable) {
    echo "\n✅ Userable trouvé:\n";
    echo "   - Type: " . get_class($user->userable) . "\n";
    echo "   - ID: {$user->userable->id}\n";
    
    // Vérifier les propriétés du userable
    $userable = $user->userable;
    echo "   - Name: '" . ($userable->name ?? 'NULL') . "'\n";
    echo "   - First Name: '" . ($userable->first_name ?? 'NULL') . "'\n";
    echo "   - Last Name: '" . ($userable->last_name ?? 'NULL') . "'\n";
    echo "   - Email: '" . ($userable->email ?? 'NULL') . "'\n";
    
    // Tester la logique de remplacement avec userable
    $studentName = $userable->name ?? $userable->first_name . ' ' . $userable->last_name ?? 'Étudiant';
    echo "   - Student Name calculé: '" . $studentName . "'\n";
} else {
    echo "❌ Aucun userable trouvé\n";
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";

