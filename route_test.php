<?php
/**
 * STYLOFITNESS - Test de Rutas Simplificado
 * Para diagnosticar problemas de enrutamiento
 */

// Configuración básica
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>StyloFitness - Test de Rutas</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }";
echo ".header { background: linear-gradient(135deg, #FF6B00, #E55A00); color: white; padding: 20px; margin: -30px -30px 30px -30px; border-radius: 10px 10px 0 0; text-align: center; }";
echo ".test-link { display: inline-block; margin: 10px; padding: 10px 20px; background: #FF6B00; color: white; text-decoration: none; border-radius: 6px; }";
echo ".test-link:hover { background: #E55A00; }";
echo ".info { background: #e9ecef; padding: 15px; border-radius: 6px; margin: 20px 0; }";
echo ".success { background: #d4edda; border-left: 4px solid #28a745; }";
echo ".error { background: #f8d7da; border-left: 4px solid #dc3545; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<div class='header'>";
echo "<h1>🧪 StyloFitness - Test de Rutas</h1>";
echo "<p>Herramienta para diagnosticar problemas de enrutamiento</p>";
echo "</div>";

// Información del servidor
echo "<div class='info'>";
echo "<h3>📊 Información del Servidor</h3>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Base Path:</strong> " . dirname($_SERVER['SCRIPT_NAME']) . "</p>";
echo "</div>";

// Verificar archivos críticos
echo "<div class='info'>";
echo "<h3>📁 Verificación de Archivos</h3>";

$criticalFiles = [
    'index.php' => 'Archivo principal',
    '.htaccess' => 'Configuración Apache',
    'app/Controllers/AuthController.php' => 'Controlador Auth',
    'app/Controllers/HomeController.php' => 'Controlador Home',
    'app/Controllers/ApiController.php' => 'Controlador API',
    'app/Helpers/AppHelper.php' => 'Helper Principal'
];

foreach ($criticalFiles as $file => $desc) {
    $exists = file_exists($file);
    $class = $exists ? 'success' : 'error';
    $icon = $exists ? '✅' : '❌';
    echo "<div class='{$class}' style='margin: 5px 0; padding: 8px;'>";
    echo "{$icon} {$desc}: {$file}";
    echo "</div>";
}
echo "</div>";

// Links de prueba
echo "<div class='info'>";
echo "<h3>🔗 Enlaces de Prueba</h3>";
echo "<p>Haz clic en estos enlaces para probar las rutas:</p>";

$testRoutes = [
    '/' => 'Página Principal',
    '/login' => 'Login',
    '/register' => 'Registro', 
    '/dashboard' => 'Dashboard',
    '/api/products' => 'API Productos',
    '/api/exercises/categories' => 'API Categorías',
    '/quick_diagnosis.php' => 'Diagnóstico Rápido',
    '/db_verification.php' => 'Verificación BD'
];

foreach ($testRoutes as $route => $name) {
    echo "<a href='{$route}' class='test-link' target='_blank'>{$name}</a>";
}
echo "</div>";

// Información de depuración
echo "<div class='info'>";
echo "<h3>🐛 Información de Depuración</h3>";
echo "<p>Si algún enlace no funciona, revisa el archivo de logs de PHP o activa el modo debug en la aplicación.</p>";
echo "<p>Los logs de enrutamiento se guardan en el log de errores de PHP.</p>";
echo "</div>";

// Instrucciones
echo "<div class='info'>";
echo "<h3>📋 Instrucciones</h3>";
echo "<ol>";
echo "<li><strong>Página Principal (/):</strong> Debería mostrar la landing page de StyloFitness</li>";
echo "<li><strong>Login (/login):</strong> Debería mostrar el formulario de inicio de sesión</li>";
echo "<li><strong>Dashboard (/dashboard):</strong> Debería redirigir a /login si no estás autenticado</li>";
echo "<li><strong>APIs:</strong> Deberían devolver datos JSON o error de autenticación</li>";
echo "<li><strong>Diagnósticos:</strong> Scripts PHP directos que siempre deberían funcionar</li>";
echo "</ol>";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";
?>