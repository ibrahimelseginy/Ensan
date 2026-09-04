<?php
$str = file_get_contents(__DIR__ . '/../resources/views/layouts/app.blade.php');
if (strpos($str, "ظ…") !== false) {
    echo "FILE CONTAINS DOUBLE ENCODED ARABIC (ظ…ط¤ط³ط³ط©)\n";
} else {
    echo "FILE IS FINE\n";
}
