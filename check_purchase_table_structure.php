<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Structure de organization_course_purchases ===\n\n";

try {
    $columns = DB::select("DESCRIBE organization_course_purchases");
    foreach ($columns as $column) {
        echo "  - {$column->Field}: {$column->Type} (Default: {$column->Default}, Null: {$column->Null})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
