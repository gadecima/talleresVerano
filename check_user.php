<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$admin = User::where('email', 'admin@mistarter.local')->first();
if ($admin) {
    echo "Usuario encontrado: \n";
    echo "ID: " . $admin->id . "\n";
    echo "Email: " . $admin->email . "\n";
    echo "Role ID: " . $admin->role_id . "\n";
    echo "Role: " . ($admin->role ? $admin->role->name . " (" . $admin->role->slug . ")" : "null") . "\n";
} else {
    echo "Usuario no encontrado\n";
}
