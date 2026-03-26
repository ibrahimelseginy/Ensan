<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = Illuminate\Support\Facades\DB::select('DESCRIBE donations');
foreach ($columns as $col) {
    if ($col->Field === 'status') {
        echo "Field: " . $col->Field . "\n";
        echo "Type: " . $col->Type . "\n";
        echo "Default: " . $col->Default . "\n";
    }
}
