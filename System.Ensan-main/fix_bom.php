<?php

function checkAndRemoveBOM($dir) {
    if (!is_dir($dir)) return;
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade.php'])) {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
                echo "Found and removed BOM in: " . $path . "\n";
                file_put_contents($path, substr($content, 3));
            }
        }
    }
}

// Check common directories where you might have recently created/edited files
checkAndRemoveBOM(__DIR__ . '/app');
checkAndRemoveBOM(__DIR__ . '/resources/views');
checkAndRemoveBOM(__DIR__ . '/routes');
checkAndRemoveBOM(__DIR__ . '/config');

echo "BOM Check Complete.\n";
