<?php
// Router script for PHP development server
// This file handles routing for the built-in PHP server

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Check if it's a static file request
if ($requestUri !== '/') {
    // Try to serve from public directory first
    $publicFile = __DIR__ . '/public' . $requestUri;
    
    // For static files, check if they exist in public directory
    if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|eot|ttf|mp4|webm|pdf)$/i', $requestUri)) {
        if (file_exists($publicFile)) {
            return false; // Let PHP serve the file
        }
        // If the file doesn't exist in public, return 404
        http_response_code(404);
        echo "File not found: " . $requestUri;
        return true;
    }
    
    // For other files, try to serve from public directory
    if (file_exists($publicFile)) {
        return false; // Let PHP serve the file
    }
}

// For all other requests, route through index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
require_once __DIR__ . '/index.php';
?>