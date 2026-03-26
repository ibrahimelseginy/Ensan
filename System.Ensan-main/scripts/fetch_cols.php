<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$models = [
    'App\Models\Membership',
    'App\Models\SchoolCollaboration',
    'App\Models\OncologyMedicineRep',
    'App\Models\KafrElSheikhBroker',
    'App\Models\KafrElSheikhDelivery',
    'App\Models\KafrElSheikhService',
    'App\Models\TantaWorker'
];

foreach ($models as $modelClass) {
    if (class_exists($modelClass)) {
        $model = new $modelClass();
        $table = $model->getTable();
        $columns = Illuminate\Support\Facades\Schema::getColumnListing($table);
        echo $modelClass . " -> " . implode(", ", $columns) . "\n";
    }
    else {
        echo $modelClass . " -> NOT FOUND\n";
    }
}
