<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use App\Models\Campaign;
use App\Models\GuestHouse;
use App\Models\User;

echo "Projects:\n";
foreach(Project::all() as $p) { echo "- " . $p->name . "\n"; }

echo "\nCampaigns:\n";
foreach(Campaign::all() as $c) { echo "- " . $c->title . "\n"; }

echo "\nGuestHouses:\n";
foreach(GuestHouse::all() as $g) { echo "- " . $g->name . "\n"; }

echo "\nDepartments:\n";
$depts = User::distinct()->whereNotNull('department')->pluck('department')->filter()->values();
foreach($depts as $d) { echo "- " . $d . "\n"; }
