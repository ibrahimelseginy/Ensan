<?php

$replacements = [
    'ًں\'‹' => '👋', // Actually the single quote is not right, wait
    'ًں‘‹' => '👋',
    'ًں’°' => '💰',
    'ًں“‹' => '📋',
    'ًںŒں' => '🌟',
    'ًں‘¨â€چًں’¼' => '👨‍💼',
    'ًں‘¨â€چًں’»' => '👨‍💻',
    'ًں“¢' => '📢',
    'ًں“¦' => '📦',
    'ًںڑڑ' => '🚚',
    'ًںڈ ' => '🏠',
    'ًں¤‌' => '🤝',
    'ًں”چ' => '🔍',
    'ًںژ¯' => '🎯',
    'ًں’ھ' => '💪',
    'ًں†ک' => '🆘',
    'ًں“±' => '📱',
    'ًں›،ï¸ڈ' => '🛡️'
];

$files = [
    __DIR__ . '/../resources/views/audits/index.blade.php',
    __DIR__ . '/../resources/views/dashboard/index.blade.php',
    __DIR__ . '/../resources/views/mobile/cases.blade.php',
    __DIR__ . '/../resources/views/mobile/dashboard.blade.php',
    __DIR__ . '/../resources/views/mobile/in_kind_donations.blade.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            echo "Fixed emojis in: $file\n";
        }
    }
}
