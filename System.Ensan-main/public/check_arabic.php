<?php
header('Content-Type: text/plain; charset=utf-8');
echo "Checking Arabic Support...\n";
if (function_exists('mb_split')) {
    echo "SUCCESS: mb_split is DEFINED.\n";
    $text = "مؤسسة إنسان";
    echo "Test Text: " . $text . "\n";
}
else {
    echo "ERROR: mb_split is NOT DEFINED.\n";
}

if (extension_loaded('openssl')) {
    echo "SUCCESS: OpenSSL (Encryption) is LOADED.\n";
}
else {
    echo "ERROR: OpenSSL (Encryption) is NOT LOADED.\n";
}
?>
