#!/usr/bin/env php
<?php

/**
 * StyloFitness Console Application
 * 
 * This script provides command-line interface for various maintenance tasks
 */

// Define constants
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Load composer autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Load environment
use StyleFitness\Config\Environment;
use StyleFitness\Config\Migration;
use StyleFitness\Config\CacheManager;
use StyleFitness\Config\LoggerConfig;

Environment::load();

// Available commands
$commands = [
    'migrate' => 'Run database migrations',
    'migrate:rollback' => 'Rollback last batch of migrations',
    'migrate:status' => 'Show migration status',
    'migrate:create' => 'Create a new migration file',
    'cache:clear' => 'Clear application cache',
    'cache:stats' => 'Show cache statistics',
    'logs:clear' => 'Clear application logs',
    'test' => 'Run PHPUnit tests',
    'help' => 'Show this help message'
];

// Get command from arguments
$command = $argv[1] ?? 'help';
$args = array_slice($argv, 2);

// Execute command
try {
    switch ($command) {
        case 'migrate':
            runMigrations();
            break;
            
        case 'migrate:rollback':
            rollbackMigrations();
            break;
            
        case 'migrate:status':
            showMigrationStatus();
            break;
            
        case 'migrate:create':
            createMigration($args);
            break;
            
        case 'cache:clear':
            clearCache();
            break;
            
        case 'cache:stats':
            showCacheStats();
            break;
            
        case 'logs:clear':
            clearLogs();
            break;
            
        case 'test':
            runTests($args);
            break;
            
        case 'help':
        default:
            showHelp();
            break;
    }
} catch (Exception $e) {
    echo "\033[31mError: " . $e->getMessage() . "\033[0m\n";
    exit(1);
}

/**
 * Run database migrations
 */
function runMigrations(): void
{
    echo "\033[33mRunning migrations...\033[0m\n";
    
    $migration = new Migration();
    $executed = $migration->migrate();
    
    if (empty($executed)) {
        echo "\033[32mNo pending migrations found.\033[0m\n";
    } else {
        echo "\033[32mExecuted migrations:\033[0m\n";
        foreach ($executed as $migrationName) {
            echo "  - {$migrationName}\n";
        }
    }
}

/**
 * Rollback migrations
 */
function rollbackMigrations(): void
{
    echo "\033[33mRolling back migrations...\033[0m\n";
    
    $migration = new Migration();
    $rolledBack = $migration->rollback();
    
    if (empty($rolledBack)) {
        echo "\033[32mNo migrations to rollback.\033[0m\n";
    } else {
        echo "\033[32mRolled back migrations:\033[0m\n";
        foreach ($rolledBack as $migrationName) {
            echo "  - {$migrationName}\n";
        }
    }
}

/**
 * Show migration status
 */
function showMigrationStatus(): void
{
    $migration = new Migration();
    $status = $migration->status();
    
    echo "\033[36mMigration Status:\033[0m\n";
    echo "Total migrations: {$status['total']}\n";
    echo "Executed: {$status['executed']}\n";
    echo "Pending: {$status['pending']}\n";
    
    if (!empty($status['pending_list'])) {
        echo "\n\033[33mPending migrations:\033[0m\n";
        foreach ($status['pending_list'] as $pending) {
            echo "  - {$pending}\n";
        }
    }
}

/**
 * Create new migration
 */
function createMigration(array $args): void
{
    if (empty($args[0])) {
        echo "\033[31mError: Migration name is required\033[0m\n";
        echo "Usage: php console.php migrate:create <migration_name>\n";
        exit(1);
    }
    
    $name = $args[0];
    $migration = new Migration();
    $filename = $migration->createMigration($name);
    
    echo "\033[32mMigration created: {$filename}\033[0m\n";
}

/**
 * Clear application cache
 */
function clearCache(): void
{
    echo "\033[33mClearing cache...\033[0m\n";
    
    $cache = CacheManager::getInstance();
    $cache->clear();
    
    echo "\033[32mCache cleared successfully.\033[0m\n";
}

/**
 * Show cache statistics
 */
function showCacheStats(): void
{
    $cache = CacheManager::getInstance();
    $stats = $cache->getStats();
    
    echo "\033[36mCache Statistics:\033[0m\n";
    echo "Total entries: {$stats['total_entries']}\n";
    echo "Valid entries: {$stats['valid_entries']}\n";
    echo "Expired entries: {$stats['expired_entries']}\n";
    echo "Total size: {$stats['total_size_human']}\n";
    
    if ($stats['expired_entries'] > 0) {
        echo "\n\033[33mCleaning expired entries...\033[0m\n";
        $cleaned = $cache->cleanExpired();
        echo "\033[32mCleaned {$cleaned} expired entries.\033[0m\n";
    }
}

/**
 * Clear application logs
 */
function clearLogs(): void
{
    echo "\033[33mClearing logs...\033[0m\n";
    
    $logDir = ROOT_PATH . '/storage/logs';
    
    if (!is_dir($logDir)) {
        echo "\033[32mNo logs directory found.\033[0m\n";
        return;
    }
    
    $files = glob($logDir . '/*.log');
    $cleared = 0;
    
    foreach ($files as $file) {
        if (unlink($file)) {
            $cleared++;
        }
    }
    
    echo "\033[32mCleared {$cleared} log files.\033[0m\n";
}

/**
 * Run PHPUnit tests
 */
function runTests(array $args): void
{
    echo "\033[33mRunning tests...\033[0m\n";
    
    $testCommand = 'vendor/bin/phpunit';
    
    // Add additional arguments
    if (!empty($args)) {
        $testCommand .= ' ' . implode(' ', $args);
    }
    
    // Check if phpunit exists
    if (!file_exists(ROOT_PATH . '/vendor/bin/phpunit') && !file_exists(ROOT_PATH . '/vendor/bin/phpunit.bat')) {
        echo "\033[31mPHPUnit not found. Run 'composer install' first.\033[0m\n";
        exit(1);
    }
    
    // Execute tests
    $output = [];
    $returnCode = 0;
    exec($testCommand, $output, $returnCode);
    
    foreach ($output as $line) {
        echo $line . "\n";
    }
    
    if ($returnCode === 0) {
        echo "\033[32mTests completed successfully.\033[0m\n";
    } else {
        echo "\033[31mTests failed with exit code {$returnCode}.\033[0m\n";
        exit($returnCode);
    }
}

/**
 * Show help message
 */
function showHelp(): void
{
    global $commands;
    
    echo "\033[36mStyloFitness Console Application\033[0m\n\n";
    echo "\033[33mUsage:\033[0m\n";
    echo "  php console.php <command> [arguments]\n\n";
    echo "\033[33mAvailable commands:\033[0m\n";
    
    foreach ($commands as $command => $description) {
        echo sprintf("  \033[32m%-20s\033[0m %s\n", $command, $description);
    }
    
    echo "\n\033[33mExamples:\033[0m\n";
    echo "  php console.php migrate\n";
    echo "  php console.php migrate:create create_users_table\n";
    echo "  php console.php cache:clear\n";
    echo "  php console.php test --filter ProductTest\n";
}

/**
 * Format output with colors
 */
function colorOutput(string $text, string $color = 'white'): string
{
    $colors = [
        'black' => '30',
        'red' => '31',
        'green' => '32',
        'yellow' => '33',
        'blue' => '34',
        'magenta' => '35',
        'cyan' => '36',
        'white' => '37'
    ];
    
    $colorCode = $colors[$color] ?? '37';
    return "\033[{$colorCode}m{$text}\033[0m";
}

/**
 * Check system requirements
 */
function checkRequirements(): array
{
    $requirements = [
        'PHP Version >= 8.1' => version_compare(PHP_VERSION, '8.1.0', '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
        'JSON Extension' => extension_loaded('json'),
        'MBString Extension' => extension_loaded('mbstring'),
        'GD Extension' => extension_loaded('gd'),
        'cURL Extension' => extension_loaded('curl'),
        'Zip Extension' => extension_loaded('zip'),
        'FileInfo Extension' => extension_loaded('fileinfo'),
        'Storage Directory Writable' => is_writable(ROOT_PATH . '/storage') || mkdir(ROOT_PATH . '/storage', 0755, true),
        'Uploads Directory Writable' => is_writable(PUBLIC_PATH . '/uploads') || mkdir(PUBLIC_PATH . '/uploads', 0755, true)
    ];
    
    return $requirements;
}

/**
 * Show system information
 */
function showSystemInfo(): void
{
    echo "\033[36mSystem Information:\033[0m\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Operating System: " . PHP_OS . "\n";
    echo "Memory Limit: " . ini_get('memory_limit') . "\n";
    echo "Max Execution Time: " . ini_get('max_execution_time') . "s\n";
    echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "\n";
    echo "Post Max Size: " . ini_get('post_max_size') . "\n";
    
    echo "\n\033[33mRequirements Check:\033[0m\n";
    $requirements = checkRequirements();
    
    foreach ($requirements as $requirement => $met) {
        $status = $met ? "\033[32m✓\033[0m" : "\033[31m✗\033[0m";
        echo "  {$status} {$requirement}\n";
    }
}