<?php
/**
 * Script de configuración inicial - STYLOFITNESS
 * Configuración automática del sistema
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Colores para output
function colorOutput($text, $color = 'white') {
    $colors = [
        'black' => '0;30',
        'red' => '0;31',
        'green' => '0;32',
        'yellow' => '0;33',
        'blue' => '0;34',
        'magenta' => '0;35',
        'cyan' => '0;36',
        'white' => '0;37',
        'bright_red' => '1;31',
        'bright_green' => '1;32',
        'bright_yellow' => '1;33',
        'bright_blue' => '1;34'
    ];
    
    return "\033[" . $colors[$color] . "m" . $text . "\033[0m";
}

// Función para imprimir mensajes
function printMessage($message, $type = 'info') {
    $timestamp = date('Y-m-d H:i:s');
    
    switch ($type) {
        case 'success':
            echo colorOutput("[$timestamp] ✅ $message", 'bright_green') . "\n";
            break;
        case 'error':
            echo colorOutput("[$timestamp] ❌ $message", 'bright_red') . "\n";
            break;
        case 'warning':
            echo colorOutput("[$timestamp] ⚠️  $message", 'bright_yellow') . "\n";
            break;
        case 'info':
        default:
            echo colorOutput("[$timestamp] ℹ️  $message", 'bright_blue') . "\n";
            break;
    }
}

// Banner inicial
function printBanner() {
    echo colorOutput("
╔══════════════════════════════════════════════════════════════════════════════╗
║                                                                              ║
║   🏋️‍♂️ STYLOFITNESS - Sistema de Gestión de Gimnasios                         ║
║                                                                              ║
║   Configuración automática del sistema                                      ║
║   Versión: 1.0.0                                                            ║
║                                                                              ║
╚══════════════════════════════════════════════════════════════════════════════╝
", 'bright_cyan') . "\n\n";
}

// Verificar requisitos del sistema
function checkSystemRequirements() {
    printMessage("Verificando requisitos del sistema...", 'info');
    
    $requirements = [
        'PHP Version' => version_compare(PHP_VERSION, '8.1.0', '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
        'JSON Extension' => extension_loaded('json'),
        'MBString Extension' => extension_loaded('mbstring'),
        'GD Extension' => extension_loaded('gd'),
        'cURL Extension' => extension_loaded('curl'),
        'Zip Extension' => extension_loaded('zip'),
        'FileInfo Extension' => extension_loaded('fileinfo')
    ];
    
    $allPassed = true;
    
    foreach ($requirements as $requirement => $passed) {
        if ($passed) {
            printMessage("$requirement: OK", 'success');
        } else {
            printMessage("$requirement: FALTA", 'error');
            $allPassed = false;
        }
    }
    
    if (!$allPassed) {
        printMessage("Algunos requisitos no están instalados. Por favor instala las extensiones faltantes.", 'error');
        exit(1);
    }
    
    printMessage("Todos los requisitos están instalados.", 'success');
}

// Verificar permisos de archivos
function checkFilePermissions() {
    printMessage("Verificando permisos de archivos...", 'info');
    
    $directories = [
        'public/uploads',
        'public/uploads/images',
        'public/uploads/videos',
        'public/uploads/documents',
        'logs'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                printMessage("Directorio creado: $dir", 'success');
            } else {
                printMessage("Error al crear directorio: $dir", 'error');
                exit(1);
            }
        }
        
        if (is_writable($dir)) {
            printMessage("Permisos OK: $dir", 'success');
        } else {
            printMessage("Sin permisos de escritura: $dir", 'warning');
            if (chmod($dir, 0755)) {
                printMessage("Permisos corregidos: $dir", 'success');
            } else {
                printMessage("Error al corregir permisos: $dir", 'error');
            }
        }
    }
}

// Configurar archivo .env
function setupEnvironmentFile() {
    printMessage("Configurando archivo de entorno...", 'info');
    
    if (!file_exists('.env')) {
        if (file_exists('.env.example')) {
            copy('.env.example', '.env');
            printMessage("Archivo .env creado desde .env.example", 'success');
        } else {
            printMessage("Archivo .env.example no encontrado", 'error');
            return false;
        }
    } else {
        printMessage("Archivo .env ya existe", 'info');
    }
    
    return true;
}

// Generar clave de aplicación
function generateAppKey() {
    printMessage("Generando clave de aplicación...", 'info');
    
    $key = 'base64:' . base64_encode(random_bytes(32));
    
    $envContent = file_get_contents('.env');
    $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $envContent);
    
    if (file_put_contents('.env', $envContent)) {
        printMessage("Clave de aplicación generada", 'success');
        return true;
    }
    
    printMessage("Error al generar clave de aplicación", 'error');
    return false;
}

// Configurar base de datos
function setupDatabase() {
    printMessage("Configurando base de datos...", 'info');
    
    // Leer configuración del .env
    $envContent = file_get_contents('.env');
    $envLines = explode("\n", $envContent);
    $config = [];
    
    foreach ($envLines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
            list($key, $value) = explode('=', $line, 2);
            $config[trim($key)] = trim($value);
        }
    }
    
    $host = $config['DB_HOST'] ?? 'localhost';
    $database = $config['DB_DATABASE'] ?? 'stylofitness_gym';
    $username = $config['DB_USERNAME'] ?? 'root';
    $password = $config['DB_PASSWORD'] ?? '';
    
    try {
        // Conectar sin especificar base de datos
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Crear base de datos si no existe
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        printMessage("Base de datos '$database' creada/verificada", 'success');
        
        // Conectar a la base de datos específica
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verificar si ya hay tablas
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            // Importar esquema
            if (file_exists('database/stylofitness_complete.sql')) {
                $sql = file_get_contents('database/stylofitness_complete.sql');
                
                // Ejecutar SQL
                $pdo->exec($sql);
                printMessage("Esquema de base de datos importado", 'success');
            } else {
                printMessage("Archivo SQL no encontrado: database/stylofitness_complete.sql", 'error');
                return false;
            }
        } else {
            printMessage("Base de datos ya contiene tablas", 'info');
        }
        
        return true;
        
    } catch (PDOException $e) {
        printMessage("Error de base de datos: " . $e->getMessage(), 'error');
        return false;
    }
}

// Verificar configuración web
function checkWebServerConfig() {
    printMessage("Verificando configuración del servidor web...", 'info');
    
    // Verificar mod_rewrite
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if (in_array('mod_rewrite', $modules)) {
            printMessage("mod_rewrite está habilitado", 'success');
        } else {
            printMessage("mod_rewrite no está habilitado", 'warning');
        }
    }
    
    // Verificar .htaccess
    if (file_exists('.htaccess')) {
        printMessage("Archivo .htaccess encontrado", 'success');
    } else {
        printMessage("Archivo .htaccess no encontrado", 'warning');
    }
    
    // Verificar URL amigables
    if (isset($_SERVER['REQUEST_URI'])) {
        printMessage("Soporte para URLs amigables disponible", 'success');
    }
}

// Crear archivos de configuración adicionales
function createAdditionalFiles() {
    printMessage("Creando archivos de configuración adicionales...", 'info');
    
    // Crear archivo de configuración de cache
    $cacheConfig = '<?php
// Configuración de cache
return [
    "default" => "file",
    "stores" => [
        "file" => [
            "driver" => "file",
            "path" => __DIR__ . "/../storage/cache"
        ],
        "redis" => [
            "driver" => "redis",
            "host" => "127.0.0.1",
            "port" => 6379,
            "database" => 0
        ]
    ]
];
';
    
    if (!is_dir('app/Config')) {
        mkdir('app/Config', 0755, true);
    }
    
    file_put_contents('app/Config/Cache.php', $cacheConfig);
    printMessage("Configuración de cache creada", 'success');
    
    // Crear directorio de storage
    if (!is_dir('storage/cache')) {
        mkdir('storage/cache', 0755, true);
        printMessage("Directorio de cache creado", 'success');
    }
    
    // Crear archivo de configuración de sesiones
    $sessionConfig = '<?php
// Configuración de sesiones
return [
    "driver" => "file",
    "lifetime" => 120,
    "path" => __DIR__ . "/../storage/sessions",
    "cookie" => [
        "name" => "stylofitness_session",
        "path" => "/",
        "domain" => null,
        "secure" => false,
        "httponly" => true,
        "samesite" => "strict"
    ]
];
';
    
    file_put_contents('app/Config/Session.php', $sessionConfig);
    printMessage("Configuración de sesiones creada", 'success');
    
    // Crear directorio de sesiones
    if (!is_dir('storage/sessions')) {
        mkdir('storage/sessions', 0755, true);
        printMessage("Directorio de sesiones creado", 'success');
    }
}

// Optimizar configuración
function optimizeConfiguration() {
    printMessage("Optimizando configuración...", 'info');
    
    // Crear archivo de configuración optimizada
    $phpConfig = '<?php
// Configuración PHP optimizada para STYLOFITNESS

// Configuración de memoria
ini_set("memory_limit", "256M");
ini_set("max_execution_time", "300");

// Configuración de uploads
ini_set("upload_max_filesize", "50M");
ini_set("post_max_size", "50M");
ini_set("max_file_uploads", "20");

// Configuración de sesiones
ini_set("session.gc_maxlifetime", "7200");
ini_set("session.cookie_lifetime", "7200");
ini_set("session.cookie_httponly", "1");
ini_set("session.cookie_secure", "0"); // Cambiar a 1 en HTTPS
ini_set("session.use_strict_mode", "1");

// Configuración de errores
if (getenv("APP_ENV") === "production") {
    ini_set("display_errors", "0");
    ini_set("log_errors", "1");
    ini_set("error_log", __DIR__ . "/../logs/php_errors.log");
} else {
    ini_set("display_errors", "1");
    ini_set("log_errors", "1");
}

// Configuración de timezone
date_default_timezone_set("America/Lima");

// Configuración de OPcache (si está disponible)
if (function_exists("opcache_get_status")) {
    $status = opcache_get_status();
    if ($status && $status["opcache_enabled"]) {
        ini_set("opcache.memory_consumption", "256");
        ini_set("opcache.interned_strings_buffer", "16");
        ini_set("opcache.max_accelerated_files", "10000");
        ini_set("opcache.validate_timestamps", "0");
        ini_set("opcache.enable_cli", "1");
    }
}
';
    
    file_put_contents('app/Config/Optimize.php', $phpConfig);
    printMessage("Configuración optimizada creada", 'success');
}

// Función principal
function main() {
    printBanner();
    
    printMessage("Iniciando configuración de STYLOFITNESS...", 'info');
    
    // Verificar requisitos
    checkSystemRequirements();
    
    // Verificar permisos
    checkFilePermissions();
    
    // Configurar archivo .env
    if (!setupEnvironmentFile()) {
        printMessage("Error al configurar archivo .env", 'error');
        exit(1);
    }
    
    // Generar clave de aplicación
    if (!generateAppKey()) {
        printMessage("Error al generar clave de aplicación", 'error');
        exit(1);
    }
    
    // Configurar base de datos
    if (!setupDatabase()) {
        printMessage("Error al configurar base de datos", 'error');
        exit(1);
    }
    
    // Verificar servidor web
    checkWebServerConfig();
    
    // Crear archivos adicionales
    createAdditionalFiles();
    
    // Optimizar configuración
    optimizeConfiguration();
    
    printMessage("", 'info');
    printMessage("🎉 ¡Configuración completada exitosamente!", 'success');
    printMessage("", 'info');
    printMessage("Próximos pasos:", 'info');
    printMessage("1. Configura tu servidor web para apuntar al directorio del proyecto", 'info');
    printMessage("2. Edita el archivo .env con tus configuraciones específicas", 'info');
    printMessage("3. Configura las credenciales de email y pagos en .env", 'info');
    printMessage("4. Visita tu sitio web en el navegador", 'info');
    printMessage("", 'info');
    printMessage("Cuentas de prueba:", 'info');
    printMessage("- Admin: admin@stylofitness.com / password", 'info');
    printMessage("- Instructor: instructor@stylofitness.com / password", 'info');
    printMessage("- Cliente: cliente@stylofitness.com / password", 'info');
    printMessage("", 'info');
    printMessage("🔗 Documentación: https://docs.stylofitness.com", 'info');
    printMessage("📧 Soporte: support@stylofitness.com", 'info');
    printMessage("", 'info');
    
    echo colorOutput("¡Disfruta usando STYLOFITNESS! 🏋️‍♂️", 'bright_green') . "\n\n";
}

// Ejecutar configuración
main();
?>