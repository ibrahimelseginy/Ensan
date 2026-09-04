<?php
$str = "ظ…ط¤ط³ط³ط© ط¥ظ†ط³ط§ظ†";
if (function_exists('iconv')) {
    $bytes = iconv('UTF-8', 'CP1256', $str);
    echo "REVERSED VIA CP1256: " . $bytes . "\n";
    
    $bytes2 = iconv('UTF-8', 'Windows-1256', $str);
    echo "REVERSED VIA Windows-1256: " . $bytes2 . "\n";
} else {
    echo "NO ICONV\n";
}
