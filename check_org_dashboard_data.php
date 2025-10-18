<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Données Dashboard Organisation ===\n\n";

try {
    // Simuler l'authentification
    $user = \Modules\LMS\Models\User::find(34);
    if (!$user) {
        echo "❌ Utilisateur non trouvé\n";
        exit;
    }
    
    auth()->login($user);
    echo "✅ Utilisateur connecté: {$user->email}\n";
    
    $organization = $user->organization;
    if (!$organization) {
        echo "❌ Organisation non trouvée\n";
        exit;
    }
    
    echo "✅ Organisation: {$organization->name} (ID: {$organization->id})\n\n";
    
    // Statistiques pour l'organisation
    $totalPurchasedCourses = $organization->purchasedCourses()->count();
    $totalStudents = $organization->organizationParticipants()->count();
    $totalEnrollmentLinks = $organization->enrollmentLinks()->count();
    
    echo "=== Statistiques Organisation ===\n";
    echo "Total Cours Achetés: {$totalPurchasedCourses}\n";
    echo "Total Étudiants: {$totalStudents}\n";
    echo "Total Liens d'Inscription: {$totalEnrollmentLinks}\n\n";
    
    // Détails des cours achetés
    if ($totalPurchasedCourses > 0) {
        echo "=== Cours Achetés ===\n";
        $purchases = $organization->purchasedCourses()->with('course')->get();
        foreach ($purchases as $purchase) {
            echo "- ID: {$purchase->id}\n";
            echo "  Cours: " . ($purchase->course->title ?? 'N/A') . "\n";
            echo "  Montant: {$purchase->amount}\n";
            echo "  Statut: {$purchase->status}\n";
            echo "  Date: {$purchase->purchase_date}\n";
            echo "  Lien d'inscription: {$purchase->enrollment_link_id}\n\n";
        }
    }
    
    // Détails des liens d'inscription
    if ($totalEnrollmentLinks > 0) {
        echo "=== Liens d'Inscription ===\n";
        $links = $organization->enrollmentLinks()->with('course')->get();
        foreach ($links as $link) {
            echo "- ID: {$link->id}\n";
            echo "  Nom: {$link->name}\n";
            echo "  Cours: " . ($link->course->title ?? 'N/A') . "\n";
            echo "  Slug: {$link->slug}\n";
            echo "  URL: " . url('/enroll/' . $link->slug) . "\n";
            echo "  Statut: {$link->status}\n";
            echo "  Inscriptions actuelles: {$link->current_enrollments}\n\n";
        }
    }
    
    echo "✅ Toutes les données sont disponibles pour le dashboard !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
