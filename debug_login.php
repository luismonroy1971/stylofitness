<?php
/**
 * Script de debug para identificar problemas de login
 */

// Iniciar sesión
session_start();

// Incluir configuración
require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Helpers/AppHelper.php';

use StyleFitness\Config\Database;
use StyleFitness\Helpers\AppHelper;

echo "<h1>Debug de Login - STYLOFITNESS</h1>";

// Verificar estado de la sesión
echo "<h2>Estado de la Sesión:</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "User ID en sesión: " . ($_SESSION['user_id'] ?? 'No definido') . "\n";
echo "Usuario logueado: " . (AppHelper::isLoggedIn() ? 'SÍ' : 'NO') . "\n";

if (AppHelper::isLoggedIn()) {
    $user = AppHelper::getCurrentUser();
    echo "Datos del usuario:\n";
    print_r($user);
}

echo "\nTodos los datos de sesión:\n";
print_r($_SESSION);
echo "</pre>";

// Verificar conexión a la base de datos
echo "<h2>Conexión a Base de Datos:</h2>";
try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✅ Conexión exitosa a la base de datos</p>";
    
    // Probar consulta simple
    $result = $db->fetch('SELECT COUNT(*) as total FROM users');
    echo "<p>Total de usuarios en la base de datos: " . $result['total'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $e->getMessage() . "</p>";
}

// Verificar rutas
echo "<h2>Información de Rutas:</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Base URL: " . AppHelper::getBaseUrl() . "\n";
echo "</pre>";

// Simular redirección
echo "<h2>Prueba de Redirección:</h2>";
echo "<p><a href='javascript:testRedirect()'>Probar redirección a /admin/dashboard</a></p>";

echo "<script>
function testRedirect() {
    console.log('Probando redirección...');
    window.location.href = '/admin/dashboard';
}
</script>";

// Mostrar logs recientes si existen
echo "<h2>Logs de Error Recientes:</h2>";
$logFile = ini_get('error_log');
if ($logFile && file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $recentLogs = array_slice(explode("\n", $logs), -20);
    echo "<pre style='background: #f0f0f0; padding: 10px; max-height: 300px; overflow-y: auto;'>";
    foreach ($recentLogs as $log) {
        if (strpos($log, 'AuthController') !== false || strpos($log, 'redirect') !== false) {
            echo htmlspecialchars($log) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "<p>No se encontró archivo de logs o no está configurado.</p>";
}

echo "<hr>";
echo "<p><a href='/login'>← Volver al Login</a> | <a href='/admin/dashboard'>Ir al Dashboard</a></p>";
?>