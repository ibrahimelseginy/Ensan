<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use App\Models\Campaign;
use App\Models\GuestHouse;

echo "Projects:\n";
foreach(Project::all() as $p) { echo "- " . $p->name . " (ID: " . $p->id . ")\n"; }

echo "\nCampaigns:\n";
foreach(Campaign::all() as $c) { echo "- " . $c->name . " (ID: " . $c->id . ")\n"; }

echo "\nGuestHouses:\n";
foreach(GuestHouse::all() as $g) { echo "- " . $g->name . " (ID: " . $g->id . ")\n"; }
