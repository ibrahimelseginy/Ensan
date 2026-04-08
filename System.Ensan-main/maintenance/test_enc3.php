<?php
$html = file_get_contents(__DIR__ . '/test_login.html');
$pos = strpos($html, 'ظ…');
if ($pos !== false) {
    echo "FOUND MANGLING at pos $pos\n";
    $substr = substr($html, $pos, 20);
    echo "HEX: " . bin2hex($substr) . "\n";
} else {
    echo "NO MANGLING string 'ظ…' found. \n";
    // Look for correct Arabic
    $pos2 = strpos($html, 'تسجيل');
    if ($pos2 !== false) {
        echo "FOUND CORRECT ARABIC 'تسجيل' at pos $pos2\n";
        $substr2 = substr($html, $pos2, 20);
        echo "HEX: " . bin2hex($substr2) . "\n";
    }
}
