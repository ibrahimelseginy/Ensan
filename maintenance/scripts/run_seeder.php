<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

include_once __DIR__ . '/database/seeders/PermissionSeeder.php';

$seeder = new \Database\Seeders\PermissionSeeder();
$seeder->run();

echo "Permissions seeded successfully.\n";
