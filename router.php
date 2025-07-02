<?php
// Router script for PHP development server
// This file handles routing for the built-in PHP server

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Check if it's a static file request
if ($requestUri !== '/' && file_exists(__DIR__ . '/public' . $requestUri)) {
    // Serve static files directly
    return false;
}

// For all other requests, route through index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
require_once __DIR__ . '/index.php';
?>