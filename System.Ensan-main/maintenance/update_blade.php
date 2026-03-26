<?php

$dir = "resources/views";
$count = 0;

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isDir()) {
        continue;
    }
    
    if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($file->getPathname());
        
        $new_content = preg_replace("/asset\('storage\/'\s*\.\s*\\$([a-zA-Z0-9_]+)->([a-zA-Z0-9_]+)path\)/", "$$$1->image_url", $content, -1, $replacements);
        
        if ($replacements > 0) {
            file_put_contents($file->getPathname(), $new_content);
            $count += $replacements;
            echo "Updated " . $file->getPathname() . " ($replacements replacements)\n";
        }
    }
}

echo "Total replacements: $count\n";
