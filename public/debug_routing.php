<?php
// Debug routing issue

// Simulate the routing logic
$requestUri = '/store/product/c4-original-pre-workout-390g';
$scriptName = '/index.php';
$basePath = str_replace('\\', '/', dirname($scriptName));

echo "<h1>Routing Debug</h1>";
echo "<p><strong>Original URI:</strong> {$requestUri}</p>";
echo "<p><strong>Script Name:</strong> {$scriptName}</p>";
echo "<p><strong>Base Path:</strong> {$basePath}</p>";

// Process URI like the router does
if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

$requestUri = '/' . ltrim($requestUri, '/');
$requestUri = $requestUri === '/' ? '/' : rtrim($requestUri, '/');

echo "<p><strong>Processed URI:</strong> {$requestUri}</p>";

// Test regex conversion
$route = '/store/product/{slug}';
$pattern = str_replace('/', '\\/', $route);
$pattern = preg_replace('/\\{([^}]+)\\}/', '([^\\/]+)', $pattern);
$pattern = '/^' . $pattern . '$/';

echo "<p><strong>Route Pattern:</strong> {$route}</p>";
echo "<p><strong>Regex Pattern:</strong> {$pattern}</p>";

// Test if it matches
if (preg_match($pattern, $requestUri, $matches)) {
    echo "<p style='color: green;'><strong>✓ MATCH FOUND!</strong></p>";
    echo "<p><strong>Matches:</strong> " . print_r($matches, true) . "</p>";
} else {
    echo "<p style='color: red;'><strong>✗ NO MATCH</strong></p>";
}

// Test with actual $_SERVER values
echo "<h2>Actual Server Values</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "</p>";
echo "<p><strong>REQUEST_METHOD:</strong> " . ($_SERVER['REQUEST_METHOD'] ?? 'Not set') . "</p>";
?>