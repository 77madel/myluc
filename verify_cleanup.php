<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION FINALE DU NETTOYAGE\n";
echo "====================================\n\n";

$email = 'madoumadeltitokone77@gmail.com';

echo "📧 Email vérifié: $email\n\n";

// 1. Vérifier l'utilisateur
echo "1️⃣ VÉRIFICATION UTILISATEUR:\n";
echo "=============================\n";
$user = \Modules\LMS\Models\User::where('email', $email)->first();
if ($user) {
    echo "❌ Utilisateur encore présent - ID: {$user->id}\n";
} else {
    echo "✅ Utilisateur supprimé\n";
}

// 2. Vérifier les enrollments
echo "\n2️⃣ VÉRIFICATION ENROLLMENTS:\n";
echo "==============================\n";
$enrollments = \Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', 40)->get();
echo "📊 Enrollments restants pour user_id 40: " . $enrollments->count() . "\n";

// 3. Vérifier l'étudiant
echo "\n3️⃣ VÉRIFICATION ÉTUDIANT:\n";
echo "===========================\n";
$student = \Modules\LMS\Models\Auth\Student::find(11);
if ($student) {
    echo "❌ Étudiant encore présent - ID: {$student->id}\n";
    echo "   - Prénom: {$student->first_name}\n";
    echo "   - Nom: {$student->last_name}\n";
} else {
    echo "✅ Étudiant supprimé\n";
}

// 4. Vérifier les sessions
echo "\n4️⃣ VÉRIFICATION SESSIONS:\n";
echo "===========================\n";
$sessions = \DB::table('sessions')->where('user_id', 40)->get();
echo "📊 Sessions restantes pour user_id 40: " . $sessions->count() . "\n";

// 5. Vérifier les wishlists
echo "\n5️⃣ VÉRIFICATION WISHLISTS:\n";
echo "============================\n";
try {
    $wishlists = \DB::table('wishlists')->where('user_id', 40)->get();
    echo "📊 Wishlists restantes pour user_id 40: " . $wishlists->count() . "\n";
} catch (\Exception $e) {
    echo "ℹ️ Table wishlists non accessible: " . $e->getMessage() . "\n";
}

echo "\n🎯 RÉSUMÉ FINAL:\n";
echo "=================\n";
echo "Utilisateur: " . ($user ? "PRÉSENT" : "SUPPRIMÉ") . "\n";
echo "Étudiant: " . ($student ? "PRÉSENT" : "SUPPRIMÉ") . "\n";
echo "Enrollments: " . $enrollments->count() . "\n";
echo "Sessions: " . $sessions->count() . "\n";

if (!$user && !$student && $enrollments->count() == 0 && $sessions->count() == 0) {
    echo "\n🎉 NETTOYAGE COMPLET RÉUSSI !\n";
} else {
    echo "\n⚠️ IL RESTE DES DONNÉES À NETTOYER\n";
}
