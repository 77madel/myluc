<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧹 NETTOYAGE COMPLET DES DONNÉES UTILISATEUR\n";
echo "=============================================\n\n";

$email = 'madoumadeltitokone77@gmail.com';

echo "📧 Email à nettoyer: $email\n\n";

// 1. Trouver l'utilisateur
$user = \Modules\LMS\Models\User::where('email', $email)->first();

if (!$user) {
    echo "❌ Utilisateur non trouvé. Aucun nettoyage nécessaire.\n";
    exit;
}

echo "✅ Utilisateur trouvé - ID: {$user->id}\n";
echo "   - userable_type: {$user->userable_type}\n";
echo "   - userable_id: {$user->userable_id}\n";
echo "   - organization_id: {$user->organization_id}\n\n";

// 2. Supprimer les enrollments
echo "🗑️ SUPPRESSION DES ENROLLMENTS:\n";
echo "================================\n";
$enrollments = \Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', $user->id)->get();
echo "📊 Enrollments trouvés: " . $enrollments->count() . "\n";

if ($enrollments->count() > 0) {
    foreach ($enrollments as $enrollment) {
        echo "   - Suppression enrollment Course ID: {$enrollment->course_id}\n";
    }
    \Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', $user->id)->delete();
    echo "✅ Enrollments supprimés\n";
} else {
    echo "ℹ️ Aucun enrollment à supprimer\n";
}

echo "\n";

// 3. Supprimer les wishlists (si elles existent)
echo "🗑️ SUPPRESSION DES WISHLISTS:\n";
echo "==============================\n";
try {
    $wishlists = \DB::table('wishlists')->where('user_id', $user->id)->get();
    echo "📊 Wishlists trouvées: " . $wishlists->count() . "\n";
    
    if ($wishlists->count() > 0) {
        \DB::table('wishlists')->where('user_id', $user->id)->delete();
        echo "✅ Wishlists supprimées\n";
    } else {
        echo "ℹ️ Aucune wishlist à supprimer\n";
    }
} catch (\Exception $e) {
    echo "ℹ️ Table wishlists non trouvée ou erreur: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Supprimer les sessions
echo "🗑️ SUPPRESSION DES SESSIONS:\n";
echo "==============================\n";
$sessions = \DB::table('sessions')->where('user_id', $user->id)->get();
echo "📊 Sessions trouvées: " . $sessions->count() . "\n";

if ($sessions->count() > 0) {
    \DB::table('sessions')->where('user_id', $user->id)->delete();
    echo "✅ Sessions supprimées\n";
} else {
    echo "ℹ️ Aucune session à supprimer\n";
}

echo "\n";

// 5. Supprimer l'étudiant (userable)
echo "🗑️ SUPPRESSION DE L'ÉTUDIANT:\n";
echo "===============================\n";
if ($user->userable_type === 'Modules\LMS\Models\Auth\Student' && $user->userable_id) {
    try {
        $student = \Modules\LMS\Models\Auth\Student::find($user->userable_id);
        if ($student) {
            echo "✅ Étudiant trouvé - ID: {$student->id}\n";
            echo "   - Prénom: {$student->first_name}\n";
            echo "   - Nom: {$student->last_name}\n";
            $student->delete();
            echo "✅ Étudiant supprimé\n";
        } else {
            echo "ℹ️ Étudiant déjà supprimé\n";
        }
    } catch (\Exception $e) {
        echo "⚠️ Erreur lors de la suppression de l'étudiant: " . $e->getMessage() . "\n";
    }
} else {
    echo "ℹ️ Pas d'étudiant lié\n";
}

echo "\n";

// 6. Supprimer l'utilisateur
echo "🗑️ SUPPRESSION DE L'UTILISATEUR:\n";
echo "=================================\n";
try {
    $user->delete();
    echo "✅ Utilisateur supprimé\n";
} catch (\Exception $e) {
    echo "⚠️ Erreur lors de la suppression de l'utilisateur: " . $e->getMessage() . "\n";
}

echo "\n";

// 7. Vérification finale
echo "🔍 VÉRIFICATION FINALE:\n";
echo "========================\n";
$finalCheck = \Modules\LMS\Models\User::where('email', $email)->first();
if ($finalCheck) {
    echo "❌ L'utilisateur existe encore !\n";
} else {
    echo "✅ Utilisateur complètement supprimé\n";
}

echo "\n";

// 8. Vérifier les enrollments restants
$remainingEnrollments = \Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', $user->id)->count();
if ($remainingEnrollments > 0) {
    echo "⚠️ Il reste $remainingEnrollments enrollments\n";
} else {
    echo "✅ Aucun enrollment restant\n";
}

echo "\n🎉 NETTOYAGE TERMINÉ !\n";
