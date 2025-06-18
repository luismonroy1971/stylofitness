<?php
/**
 * Verificación rápida de rutas - StyloFitness
 * Ejecuta este archivo para verificar si el sistema de rutas funciona
 */

// Configurar errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Verificación de Rutas - StyloFitness</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 800px; margin: 2rem auto; padding: 1rem; }";
echo "h1, h2 { color: #FF6B00; }";
echo ".success { color: green; background: #f0f8f0; padding: 1rem; border-radius: 5px; margin: 1rem 0; }";
echo ".error { color: red; background: #f8f0f0; padding: 1rem; border-radius: 5px; margin: 1rem 0; }";
echo ".info { color: blue; background: #f0f0f8; padding: 1rem; border-radius: 5px; margin: 1rem 0; }";
echo "pre { background: #f5f5f5; padding: 1rem; border-radius: 5px; overflow-x: auto; }";
echo "ul { list-style-type: none; padding: 0; }";
echo "li { padding: 0.5rem; margin: 0.5rem 0; border-left: 4px solid #FF6B00; background: #fafafa; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🚀 Verificación de Rutas - StyloFitness</h1>";

// 1. Verificar información del servidor
echo "<h2>📊 Información del Servidor</h2>";
echo "<ul>";
echo "<li><strong>Servidor:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No definido') . "</li>";
echo "<li><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'No definido') . "</li>";
echo "<li><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'No definido') . "</li>";
echo "<li><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'No definido') . "</li>";
echo "<li><strong>HTTP Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "</li>";
echo "<li><strong>PHP Version:</strong> " . PHP_VERSION . "</li>";
echo "</ul>";

// 2. Verificar mod_rewrite
echo "<h2>🔄 Verificación de mod_rewrite</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<div class='success'>✅ mod_rewrite está habilitado</div>";
    } else {
        echo "<div class='error'>❌ mod_rewrite NO está habilitado</div>";
    }
} else {
    echo "<div class='info'>ℹ️ No se puede verificar mod_rewrite (no es Apache o función no disponible)</div>";
}

// 3. Verificar archivos principales
echo "<h2>📁 Verificación de Archivos</h2>";
$files = [
    'index.php' => file_exists(__DIR__ . '/index.php'),
    '.htaccess' => file_exists(__DIR__ . '/.htaccess'),
    'app/Controllers/AuthController.php' => file_exists(__DIR__ . '/app/Controllers/AuthController.php'),
    'app/Views/auth/login.php' => file_exists(__DIR__ . '/app/Views/auth/login.php'),
    'app/Config/Database.php' => file_exists(__DIR__ . '/app/Config/Database.php'),
    'app/Config/App.php' => file_exists(__DIR__ . '/app/Config/App.php'),
    'app/Helpers/AppHelper.php' => file_exists(__DIR__ . '/app/Helpers/AppHelper.php'),
];

foreach ($files as $file => $exists) {
    $status = $exists ? "✅" : "❌";
    $class = $exists ? "success" : "error";
    echo "<div class='{$class}'>{$status} {$file}</div>";
}

// 4. Test de rutas simuladas
echo "<h2>🧪 Test de Rutas</h2>";

// Simular diferentes URLs
$testUrls = [
    '/',
    '/login',
    '/register', 
    '/dashboard',
    '/routines',
    '/store'
];

// Definir constantes
if (!defined('ROOT_PATH')) define('ROOT_PATH', __DIR__);
if (!defined('APP_PATH')) define('APP_PATH', ROOT_PATH . '/app');

foreach ($testUrls as $testUrl) {
    echo "<h3>Testing: {$testUrl}</h3>";
    
    // Simular request
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $testUrl;
    $_SERVER['SCRIPT_NAME'] = '/stylofitness/index.php';
    
    // Simular el procesamiento de rutas
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = str_replace('\\', '/', dirname($scriptName));
    
    if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
        $requestUri = substr($requestUri, strlen($basePath));
    }
    
    $requestUri = '/' . ltrim($requestUri, '/');
    $requestUri = $requestUri === '/' ? '/' : rtrim($requestUri, '/');
    
    echo "<ul>";
    echo "<li><strong>URI original:</strong> {$testUrl}</li>";
    echo "<li><strong>URI procesada:</strong> {$requestUri}</li>";
    echo "<li><strong>Base path:</strong> {$basePath}</li>";
    echo "</ul>";
}

// 5. Probar cargar AuthController
echo "<h2>🎯 Test de Carga de Controladores</h2>";
try {
    // Intentar cargar dependencias
    if (file_exists(__DIR__ . '/app/Config/Database.php')) {
        require_once __DIR__ . '/app/Config/Database.php';
        echo "<div class='success'>✅ Database.php cargado</div>";
    }
    
    if (file_exists(__DIR__ . '/app/Config/App.php')) {
        require_once __DIR__ . '/app/Config/App.php';
        echo "<div class='success'>✅ App.php cargado</div>";
    }
    
    if (file_exists(__DIR__ . '/app/Helpers/AppHelper.php')) {
        require_once __DIR__ . '/app/Helpers/AppHelper.php';
        echo "<div class='success'>✅ AppHelper.php cargado</div>";
    }
    
    if (file_exists(__DIR__ . '/app/Models/User.php')) {
        require_once __DIR__ . '/app/Models/User.php';
        echo "<div class='success'>✅ User.php cargado</div>";
    }
    
    if (file_exists(__DIR__ . '/app/Controllers/AuthController.php')) {
        require_once __DIR__ . '/app/Controllers/AuthController.php';
        echo "<div class='success'>✅ AuthController.php cargado</div>";
        
        // Intentar instanciar
        $controller = new AuthController();
        echo "<div class='success'>✅ AuthController instanciado correctamente</div>";
        
        if (method_exists($controller, 'login')) {
            echo "<div class='success'>✅ Método login() existe</div>";
        } else {
            echo "<div class='error'>❌ Método login() NO existe</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
} catch (Error $e) {
    echo "<div class='error'>❌ Error fatal: " . $e->getMessage() . "</div>";
}

// 6. Verificar configuración de PHP
echo "<h2>⚙️ Configuración PHP</h2>";
echo "<ul>";
echo "<li><strong>session.auto_start:</strong> " . (ini_get('session.auto_start') ? 'On' : 'Off') . "</li>";
echo "<li><strong>display_errors:</strong> " . (ini_get('display_errors') ? 'On' : 'Off') . "</li>";
echo "<li><strong>error_reporting:</strong> " . error_reporting() . "</li>";
echo "<li><strong>memory_limit:</strong> " . ini_get('memory_limit') . "</li>";
echo "<li><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</li>";
echo "</ul>";

// 7. Enlaces de prueba
echo "<h2>🔗 Enlaces de Prueba</h2>";
echo "<ul>";
echo "<li><a href='/stylofitness/' target='_blank'>🏠 Página Principal</a></li>";
echo "<li><a href='/stylofitness/login' target='_blank'>🔐 Login</a></li>";
echo "<li><a href='/stylofitness/register' target='_blank'>📝 Registro</a></li>";
echo "<li><a href='/stylofitness/dashboard' target='_blank'>📊 Dashboard</a></li>";
echo "</ul>";

echo "<div class='info'>";
echo "<h3>💡 Recomendaciones:</h3>";
echo "<p>1. Si mod_rewrite no está habilitado, contacta a tu proveedor de hosting.</p>";
echo "<p>2. Si ves errores de archivos faltantes, verifica que todos los archivos estén subidos correctamente.</p>";
echo "<p>3. Si las rutas no funcionan, prueba acceder directamente a: <code>/stylofitness/index.php?route=login</code></p>";
echo "</div>";

echo "</body>";
echo "</html>";
?>