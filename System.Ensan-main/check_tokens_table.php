<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (Illuminate\Support\Facades\Schema::hasTable('personal_access_tokens')) {
    echo "EXISTS\n";
} else {
    echo "MISSING\n";
}
