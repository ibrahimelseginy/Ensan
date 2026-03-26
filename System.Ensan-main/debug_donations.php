<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WebDonation;

$donations = WebDonation::all();
foreach ($donations as $d) {
    echo "ID: {$d->id}, Type: '{$d->donationable_type}', Target ID: {$d->target_id}, Donationable ID: {$d->donationable_id}\n";
}
