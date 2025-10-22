<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION INFORMATIONS UTILISATEUR 50\n";
echo "==========================================\n\n";

$userId = 50;

// 1. Vérifier l'utilisateur
$user = User::find($userId);
if (!$user) {
    echo "❌ Utilisateur non trouvé\n";
    exit;
}

echo "✅ Utilisateur trouvé:\n";
echo "   - ID: {$user->id}\n";
echo "   - Name: '" . ($user->name ?? 'NULL') . "'\n";
echo "   - First Name: '" . ($user->first_name ?? 'NULL') . "'\n";
echo "   - Last Name: '" . ($user->last_name ?? 'NULL') . "'\n";
echo "   - Email: '" . ($user->email ?? 'NULL') . "'\n";

// 2. Tester la logique de remplacement
echo "\n🧪 TEST LOGIQUE REMPLACEMENT:\n";

$studentName = $user->name ?? $user->first_name . ' ' . $user->last_name ?? 'Étudiant';
echo "   - studentName calculé: '" . $studentName . "'\n";

// 3. Vérifier les colonnes de la table users
echo "\n📊 COLONNES TABLE USERS:\n";
$columns = DB::select("DESCRIBE users");
foreach ($columns as $column) {
    echo "   - {$column->Field}: {$column->Type}\n";
}

echo "\n🔍 VÉRIFICATION TERMINÉE\n";

