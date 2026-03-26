<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$c = App\Models\Campaign::first();
$p = App\Models\Project::first();
echo 'CAMPAIGN_ID:' . ($c->id ?? 0) . "\n";
echo 'PROJECT_ID:' . ($p->id ?? 0) . "\n";
