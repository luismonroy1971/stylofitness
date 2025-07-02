<?php

// Define constants
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Load composer autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Load environment for testing
use StyleFitness\Config\Environment;

Environment::load();

// Set testing environment
$_ENV['APP_ENV'] = 'testing';
$_ENV['DB_DATABASE'] = 'stylofitness_test';
$_ENV['CACHE_DRIVER'] = 'array';

// Start session for testing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create test database connection
try {
    $config = Environment::getDatabaseConfig();
    $dsn = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);

    // Create test database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['database']}");

    echo "Test environment initialized successfully.\n";
} catch (PDOException $e) {
    echo 'Warning: Could not initialize test database: ' . $e->getMessage() . "\n";
}

// Helper function for tests
function createTestUser(array $data = []): array
{
    return array_merge([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'user',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ], $data);
}

function createTestProduct(array $data = []): array
{
    return array_merge([
        'id' => 1,
        'name' => 'Test Product',
        'slug' => 'test-product',
        'description' => 'Test product description',
        'price' => 29.99,
        'stock' => 100,
        'category_id' => 1,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ], $data);
}

function createTestRoutine(array $data = []): array
{
    return array_merge([
        'id' => 1,
        'name' => 'Test Routine',
        'description' => 'Test routine description',
        'user_id' => 1,
        'difficulty' => 'beginner',
        'duration' => 30,
        'exercises' => json_encode([]),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ], $data);
}
