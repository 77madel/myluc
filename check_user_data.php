<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VÉRIFICATION DES DONNÉES UTILISATEUR\n";
echo "=====================================\n\n";

$email = 'madoumadeltitokone77@gmail.com';

echo "📧 Email recherché: $email\n\n";

// 1. Vérifier dans la table users
echo "1️⃣ VÉRIFICATION TABLE USERS:\n";
echo "============================\n";
$user = \Modules\LMS\Models\User::where('email', $email)->first();
if ($user) {
    echo "✅ Utilisateur trouvé - ID: {$user->id}\n";
    echo "   - userable_type: {$user->userable_type}\n";
    echo "   - userable_id: {$user->userable_id}\n";
    echo "   - organization_id: {$user->organization_id}\n";
    echo "   - enrollment_link_id: {$user->enrollment_link_id}\n";
    echo "   - Créé le: {$user->created_at}\n";
} else {
    echo "❌ Utilisateur non trouvé dans la table users\n";
}

echo "\n";

// 2. Vérifier dans la table students via userable
echo "2️⃣ VÉRIFICATION TABLE STUDENTS:\n";
echo "================================\n";
if ($user && $user->userable_type === 'Modules\LMS\Models\Auth\Student') {
    $student = $user->userable;
    if ($student) {
        echo "✅ Étudiant trouvé - ID: {$student->id}\n";
        echo "   - Prénom: {$student->first_name}\n";
        echo "   - Nom: {$student->last_name}\n";
        echo "   - Téléphone: {$student->phone}\n";
        echo "   - Créé le: {$student->created_at}\n";
    } else {
        echo "❌ Relation userable cassée\n";
    }
} else {
    echo "❌ Pas d'utilisateur ou pas de type Student\n";
}

echo "\n";

// 3. Vérifier les enrollments
echo "3️⃣ VÉRIFICATION ENROLLMENTS:\n";
echo "=============================\n";
if ($user) {
    $enrollments = \Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', $user->id)->get();
    echo "📊 Nombre d'enrollments trouvés: " . $enrollments->count() . "\n";
    
    if ($enrollments->count() > 0) {
        foreach ($enrollments as $enrollment) {
            echo "   - Course ID: {$enrollment->course_id}\n";
            echo "   - Type: {$enrollment->type}\n";
            echo "   - Status: {$enrollment->status}\n";
            echo "   - Organization ID: {$enrollment->organization_id}\n";
            echo "   - Enrollment Link ID: {$enrollment->enrollment_link_id}\n";
            echo "   - Créé le: {$enrollment->created_at}\n";
            echo "   ---\n";
        }
    }
} else {
    echo "❌ Pas d'utilisateur pour vérifier les enrollments\n";
}

echo "\n";

// 4. Vérifier les wishlists
echo "4️⃣ VÉRIFICATION WISHLISTS:\n";
echo "===========================\n";
if ($user) {
    $wishlists = \Modules\LMS\Models\Wishlist\Wishlist::where('user_id', $user->id)->get();
    echo "📊 Nombre de wishlists trouvées: " . $wishlists->count() . "\n";
    
    if ($wishlists->count() > 0) {
        foreach ($wishlists as $wishlist) {
            echo "   - Course ID: {$wishlist->course_id}\n";
            echo "   - Créé le: {$wishlist->created_at}\n";
        }
    }
} else {
    echo "❌ Pas d'utilisateur pour vérifier les wishlists\n";
}

echo "\n";

// 5. Vérifier les sessions
echo "5️⃣ VÉRIFICATION SESSIONS:\n";
echo "==========================\n";
$sessions = \DB::table('sessions')->where('user_id', $user ? $user->id : 0)->get();
echo "📊 Nombre de sessions trouvées: " . $sessions->count() . "\n";

echo "\n";

// 6. Vérifier les logs d'erreur
echo "6️⃣ VÉRIFICATION LOGS D'ERREUR:\n";
echo "===============================\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $errorCount = substr_count($logContent, $email);
    echo "📊 Nombre d'erreurs liées à cet email dans les logs: $errorCount\n";
} else {
    echo "❌ Fichier de log non trouvé\n";
}

echo "\n";
echo "🎯 RÉSUMÉ:\n";
echo "==========\n";
echo "Utilisateur: " . ($user ? "TROUVÉ" : "NON TROUVÉ") . "\n";
echo "Étudiant: " . ($student ? "TROUVÉ" : "NON TROUVÉ") . "\n";
echo "Enrollments: " . ($user ? \Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', $user->id)->count() : 0) . "\n";
echo "Wishlists: " . ($user ? \Modules\LMS\Models\Wishlist\Wishlist::where('user_id', $user->id)->count() : 0) . "\n";
echo "Sessions: " . ($user ? \DB::table('sessions')->where('user_id', $user->id)->count() : 0) . "\n";
