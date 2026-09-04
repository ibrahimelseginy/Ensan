<?php
$str = file_get_contents(__DIR__ . '/../resources/views/layouts/app.blade.php');
$reversed = @iconv('UTF-8', 'Windows-1256', $str);
if ($reversed === false) {
    echo "ICONV FAILED. Error: " . error_get_last()['message'] . "\n";
} else {
    echo "ICONV SUCCESS.\n";
}
