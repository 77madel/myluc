<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "👥 UTILISATEURS DISPONIBLES:\n";
echo "=" . str_repeat("=", 30) . "\n";

$users = User::all();
foreach($users as $user) {
    echo "  ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Guard: " . ($user->guard ?? 'N/A') . "\n";
    echo "  " . str_repeat("-", 20) . "\n";
}

echo "\n✅ Total utilisateurs: " . $users->count() . "\n";

