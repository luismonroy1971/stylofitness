<?php

// Configuración PHP optimizada para STYLOFITNESS

// Configuración de memoria
ini_set('memory_limit', '256M');
ini_set('max_execution_time', '300');

// Configuración de uploads
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '50M');
ini_set('max_file_uploads', '20');

// Configuración de sesiones
ini_set('session.gc_maxlifetime', '7200');
ini_set('session.cookie_lifetime', '7200');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '0'); // Cambiar a 1 en HTTPS
ini_set('session.use_strict_mode', '1');

// Configuración de errores
if (getenv('APP_ENV') === 'production') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
} else {
    ini_set('display_errors', '1');
    ini_set('log_errors', '1');
}

// Configuración de timezone
date_default_timezone_set('America/Lima');

// Configuración de OPcache (si está disponible)
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status();
    if ($status && $status['opcache_enabled']) {
        ini_set('opcache.memory_consumption', '256');
        ini_set('opcache.interned_strings_buffer', '16');
        ini_set('opcache.max_accelerated_files', '10000');
        ini_set('opcache.validate_timestamps', '0');
        ini_set('opcache.enable_cli', '1');
    }
}
