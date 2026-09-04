<?php
// Diagnostic - saves results to a file AND displays them
$results = [];
$results['php_version'] = phpversion();
$results['loaded_ini'] = php_ini_loaded_file();
$results['scanned_inis'] = php_ini_scanned_files();
$results['extension_dir'] = ini_get('extension_dir');
$results['max_execution_time'] = ini_get('max_execution_time');
$results['php_binary'] = PHP_BINARY;

$required = ['mbstring', 'gd', 'pdo_mysql', 'openssl', 'fileinfo', 'curl', 'intl'];
foreach ($required as $ext) {
    $results['extensions'][$ext] = extension_loaded($ext) ? 'LOADED' : 'MISSING';
}

// Save to file so it can be read by tools
$output = print_r($results, true);
file_put_contents(__DIR__ . '/../storage/php_diagnosis.txt', $output);

// Display
echo "<pre>$output</pre>";
echo "<hr><b style='color:green'>Results saved to storage/php_diagnosis.txt</b>";
echo "<hr><b style='color:red'>DELETE this file after use!</b>";
