<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vérification des Achats ===\n\n";

try {
    // Vérifier les achats enregistrés
    $purchases = \Modules\LMS\Models\Auth\OrganizationCoursePurchase::all();
    echo "Nombre d'achats enregistrés: " . $purchases->count() . "\n\n";
    
    if ($purchases->count() > 0) {
        echo "Détails des achats:\n";
        foreach ($purchases as $purchase) {
            echo "- ID: {$purchase->id}\n";
            echo "  Organisation: {$purchase->organization_id}\n";
            echo "  Cours: {$purchase->course_id}\n";
            echo "  Montant: {$purchase->amount}\n";
            echo "  Date: {$purchase->purchase_date}\n";
            echo "  Statut: {$purchase->payment_status}\n";
            echo "  Lien d'inscription: {$purchase->enrollment_link_id}\n\n";
        }
    } else {
        echo "❌ Aucun achat trouvé dans la base de données\n\n";
    }
    
    // Vérifier les liens d'inscription
    $links = \Modules\LMS\Models\Auth\OrganizationEnrollmentLink::all();
    echo "Nombre de liens d'inscription: " . $links->count() . "\n\n";
    
    if ($links->count() > 0) {
        echo "Détails des liens:\n";
        foreach ($links as $link) {
            echo "- ID: {$link->id}\n";
            echo "  Nom: {$link->name}\n";
            echo "  Organisation: {$link->organization_id}\n";
            echo "  Cours: {$link->course_id}\n";
            echo "  Slug: {$link->slug}\n";
            echo "  Statut: {$link->status}\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
