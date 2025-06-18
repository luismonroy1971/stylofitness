<?php
// Debug simple para verificar rutas
echo "<h1>Debug de Rutas - StyloFitness</h1>";

// Información del request
echo "<h2>Información del Request:</h2>";
echo "<ul>";
echo "<li>REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'No definido') . "</li>";
echo "<li>REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'No definido') . "</li>";
echo "<li>SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'No definido') . "</li>";
echo "<li>PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'No definido') . "</li>";
echo "<li>HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "</li>";
echo "</ul>";

// Verificar si los archivos existen
echo "<h2>Verificación de Archivos:</h2>";
echo "<ul>";
echo "<li>index.php existe: " . (file_exists(__DIR__ . '/index.php') ? 'SÍ' : 'NO') . "</li>";
echo "<li>AuthController existe: " . (file_exists(__DIR__ . '/app/Controllers/AuthController.php') ? 'SÍ' : 'NO') . "</li>";
echo "<li>Vista login existe: " . (file_exists(__DIR__ . '/app/Views/auth/login.php') ? 'SÍ' : 'NO') . "</li>";
echo "<li>.htaccess existe: " . (file_exists(__DIR__ . '/.htaccess') ? 'SÍ' : 'NO') . "</li>";
echo "</ul>";

// Mostrar contenido del .htaccess si existe
if (file_exists(__DIR__ . '/.htaccess')) {
    echo "<h2>Contenido del .htaccess:</h2>";
    echo "<pre>" . htmlspecialchars(file_get_contents(__DIR__ . '/.htaccess')) . "</pre>";
}

// Simular el router manualmente
echo "<h2>Test del Router:</h2>";

// Definir constantes si no están definidas
if (!defined('ROOT_PATH')) define('ROOT_PATH', __DIR__);
if (!defined('APP_PATH')) define('APP_PATH', ROOT_PATH . '/app');

// Simular una petición GET a /login
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/login';
$_SERVER['SCRIPT_NAME'] = '/stylofitness/index.php';

// Parse de la URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $requestUri = substr($requestUri, strlen($basePath));
}
$requestUri = $requestUri ?: '/';

echo "<ul>";
echo "<li>URI parseada: " . $requestUri . "</li>";
echo "<li>Ruta base: " . $basePath . "</li>";
echo "<li>¿Coincide con /login?: " . ($requestUri === '/login' ? 'SÍ' : 'NO') . "</li>";
echo "</ul>";

// Verificar si la clase AuthController se puede cargar
echo "<h2>Test de Carga de Clases:</h2>";
try {
    // Incluir archivos necesarios
    require_once __DIR__ . '/app/Config/Database.php';
    require_once __DIR__ . '/app/Config/App.php';
    require_once __DIR__ . '/app/Helpers/AppHelper.php';
    require_once __DIR__ . '/app/Models/User.php';
    require_once __DIR__ . '/app/Controllers/AuthController.php';
    
    $controller = new AuthController();
    echo "<p style='color: green;'>✓ AuthController se cargó correctamente</p>";
    
    if (method_exists($controller, 'login')) {
        echo "<p style='color: green;'>✓ Método login() existe</p>";
    } else {
        echo "<p style='color: red;'>✗ Método login() NO existe</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error al cargar AuthController: " . $e->getMessage() . "</p>";
}
?>