<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
$user = User::first();
if ($user) {
    echo $user->phone;
} else {
    echo "NO_USER";
}
