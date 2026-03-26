<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function describe($table) {
    echo "\nTable: $table\n";
    $columns = Illuminate\Support\Facades\DB::select("DESCRIBE $table");
    foreach ($columns as $col) {
        echo $col->Field . ": " . $col->Type . "\n";
    }
}

describe('users');
describe('donation_proofs');
describe('donations');
