<?php
function fixMojibakeInFile($file) {
    if (!is_file($file)) return false;
    
    $content = file_get_contents($file);
    if ($content === false) return false;
    
    // Quick check to skip untouched files
    if (strpos($content, 'ط') === false && strpos($content, 'ظ') === false) {
        return false;
    }
    
    $fixed = preg_replace_callback('/[طظ]./u', function($m) {
        $attempt = @iconv('UTF-8', 'Windows-1256', $m[0]);
        if ($attempt !== false && strlen($attempt) >= 2 && mb_check_encoding($attempt, 'UTF-8')) {
            return $attempt;
        }
        return $m[0];
    }, $content);
    
    if ($fixed !== null && $fixed !== $content) {
        $mangledCount = substr_count($content, 'ط') + substr_count($content, 'ظ');
        $fixedArabicDiff = substr_count($fixed, 'ط') + substr_count($fixed, 'ظ');
        
        // If the number of weird characters significantly decreased, it was a successful fix
        if ($mangledCount > ($fixedArabicDiff + 2)) {
            file_put_contents($file, $fixed);
            return true;
        }
    }
    return false;
}

$directories = [
    __DIR__ . '/resources',
    __DIR__ . '/lang',
    __DIR__ . '/app',
    __DIR__ . '/config',
    __DIR__ . '/database/seeders',
    __DIR__ . '/database/migrations'
];

$fixedFiles = 0;

foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = $file->getExtension();
            if (in_array($ext, ['php', 'json', 'html', 'js', 'vue', 'css', 'yaml', 'yml'])) {
                if (fixMojibakeInFile($file->getPathname())) {
                    echo "Fixed: " . $file->getPathname() . "\n";
                    $fixedFiles++;
                }
            }
        }
    }
}

echo "\nTotal files fixed: $fixedFiles\n";
// Clear views cache
exec(__DIR__ . '/.tools/php83/php.exe artisan view:clear');
