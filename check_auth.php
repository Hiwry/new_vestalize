<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;

$user = User::where('email', 'admin@example.com')->first();
if ($user) {
    echo "Hash: " . $user->password . "\n";
    echo "Check 'password': " . (Illuminate\Support\Facades\Hash::check('password', $user->password) ? 'OK' : 'FAIL') . "\n";
} else {
    echo "User not found\n";
}
