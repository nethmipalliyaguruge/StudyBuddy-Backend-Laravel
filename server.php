<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Serve /storage/* files with CORS headers so Flutter web can load them.
// PHP's built-in server bypasses Laravel (and its CORS middleware) for
// files that physically exist in public/, so we handle them here.
if (str_starts_with($uri, '/storage/') && file_exists($publicPath.$uri)) {
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Max-Age: 86400');
        http_response_code(204);
        return true;
    }

    // Determine Content-Type from extension
    $ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
    $mimeTypes = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'svg'  => 'image/svg+xml',
        'pdf'  => 'application/pdf',
    ];
    $contentType = $mimeTypes[$ext] ?? mime_content_type($publicPath.$uri) ?: 'application/octet-stream';

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: '.$contentType);
    header('Content-Length: '.filesize($publicPath.$uri));
    readfile($publicPath.$uri);

    return true;
}

// For all other static files, let PHP's built-in server handle them directly
if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

$formattedDateTime = date('D M j H:i:s Y');
$requestMethod = $_SERVER['REQUEST_METHOD'];
$remoteAddress = $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'];

file_put_contents('php://stdout', "[$formattedDateTime] $remoteAddress [$requestMethod] URI: $uri\n");

require_once $publicPath.'/index.php';
