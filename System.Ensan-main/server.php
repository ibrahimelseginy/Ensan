<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Always send CORS headers for every request
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Cache-Control, Accept, Origin");

// Handle preflight OPTIONS requests immediately
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    $filePath = __DIR__.'/public'.$uri;
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    
    // Quick MIME types for common static files
    $mimes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];
    
    if (isset($mimes[$ext])) {
        header("Content-Type: " . $mimes[$ext]);
    } elseif (function_exists('mime_content_type')) {
        header("Content-Type: " . mime_content_type($filePath));
    }
    
    readfile($filePath);
    return true;
}

require_once __DIR__.'/public/index.php';
