<?php
$str = file_get_contents(__DIR__ . '/../resources/views/layouts/app.blade.php');

$fixed = preg_replace_callback('/[طظ]./u', function($m) {
    $attempt = @iconv('UTF-8', 'Windows-1256', $m[0]);
    // The attempt is bytes. If those bytes form valid UTF-8, it might be the reversed Arabic!
    if ($attempt !== false && strlen($attempt) >= 2 && mb_check_encoding($attempt, 'UTF-8')) {
        return $attempt;
    }
    return $m[0];
}, $str);

$mangledCount = substr_count($str, 'ظ…');
$fixedCount = substr_count($fixed, 'م');
echo "BEFORE mangle count: $mangledCount, AFTER arabic 'م' count: $fixedCount\n";
echo "SUCCESS? " . ($fixedCount > 0 && $fixedCount >= $mangledCount ? "YES" : "NO") . "\n";
