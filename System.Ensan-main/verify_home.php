<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Api\MobileApiController;
use Illuminate\Http\Request;

$controller = new MobileApiController();

echo "--- Testing Home Content ---\n";
$request = Request::create('/api/v1/mobile/home', 'GET');
$response = $controller->getHomeContent();
$data = json_decode($response->getContent(), true);

echo "Status: " . $data['status'] . "\n";
echo "Hero Count: " . count($data['data']['heroes'] ?? []) . "\n";

if (!empty($data['data']['heroes'])) {
    foreach ($data['data']['heroes'] as $hero) {
        echo "- Hero Title: {$hero['title']}, Image: {$hero['image_url']}\n";
        if (!empty($hero['cards'])) {
            echo "  - Cards Count: " . count($hero['cards']) . "\n";
            foreach ($hero['cards'] as $card) {
                echo "    - Card Title: {$card['title']}, Card Image: {$card['image_url']}\n";
            }
        }
    }
}

echo "\n--- Sections Check ---\n";
foreach (['gallery', 'services', 'share_what_you_dont_need', 'seasonal_campaigns'] as $section) {
    echo "Section '$section' count: " . count($data['data'][$section] ?? []) . "\n";
}

echo "\n--- About Us Check ---\n";
print_r($data['data']['about_us']);
echo "\n";
