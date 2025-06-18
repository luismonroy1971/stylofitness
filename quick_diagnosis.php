<?php
/**
 * DIAGNÓSTICO RÁPIDO - STYLOFITNESS
 * Script simple para identificar problemas de conexión
 */

// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>";
echo "<html><head><title>StyloFitness - Diagnóstico Rápido</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }";
echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }";
echo ".header { background: linear-gradient(135deg, #FF6B00, #E55A00); color: white; padding: 20px; margin: -30px -30px 30px -30px; border-radius: 10px 10px 0 0; text-align: center; }";
echo ".test { margin: 15px 0; padding: 15px; border-radius: 8px; border-left: 4px solid #ddd; }";
echo ".success { background: #d4edda; border-left-color: #28a745; }";
echo ".error { background: #f8d7da; border-left-color: #dc3545; }";
echo ".warning { background: #fff3cd; border-left-color: #ffc107; }";
echo ".info { background: #d1ecf1; border-left-color: #17a2b8; }";
echo ".code { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }";
echo "h3 { margin: 0 0 10px 0; }";
echo "p { margin: 5px 0; }";
echo "</style>";
echo "</head><body>";

echo "<div class='container'>";
echo "<div class='header'>";
echo "<h1>🔧 StyloFitness - Diagnóstico Rápido</h1>";
echo "<p>Verificación de conectividad y configuración</p>";
echo "</div>";

// Test 1: Verificar archivo .env
echo "<div class='test " . (file_exists('.env') ? 'success' : 'error') . "'>";
echo "<h3>📄 Archivo .env</h3>";
if (file_exists('.env')) {
    echo "<p>✅ Archivo .env encontrado</p>";
    
    // Leer variables específicas
    $envContent = file_get_contents('.env');
    $dbHost = '';
    $dbName = '';
    $dbUser = '';
    
    if (preg_match('/DB_HOST=(.*)/', $envContent, $matches)) {
        $dbHost = trim($matches[1]);
    }
    if (preg_match('/DB_DATABASE=(.*)/', $envContent, $matches)) {
        $dbName = trim($matches[1]);
    }
    if (preg_match('/DB_USERNAME=(.*)/', $envContent, $matches)) {
        $dbUser = trim($matches[1]);
    }
    
    echo "<div class='code'>";
    echo "DB_HOST: {$dbHost}<br>";
    echo "DB_DATABASE: {$dbName}<br>";
    echo "DB_USERNAME: {$dbUser}";
    echo "</div>";
} else {
    echo "<p>❌ Archivo .env no encontrado</p>";
    echo "<p>💡 Crea el archivo .env basado en .env.example</p>";
}
echo "</div>";

// Test 2: Verificar PHP y extensiones
echo "<div class='test success'>";
echo "<h3>🐘 PHP y Extensiones</h3>";
echo "<p>✅ PHP " . PHP_VERSION . "</p>";

$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<p>" . ($loaded ? '✅' : '❌') . " {$ext}: " . ($loaded ? 'Cargada' : 'No encontrada') . "</p>";
}
echo "</div>";

// Test 3: Conexión directa a MySQL
echo "<div class='test info'>";
echo "<h3>🗄️ Prueba de Conexión Directa</h3>";

try {
    // Valores por defecto
    $host = $dbHost ?: 'localhost';
    $dbname = $dbName ?: 'stylofitness_gym';
    $username = $dbUser ?: 'root';
    $password = ''; // Vacía por defecto
    
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<p>✅ Conexión exitosa a MySQL</p>";
    
    // Información de la base de datos
    $version = $pdo->query("SELECT VERSION() as version")->fetch();
    echo "<p>Versión MySQL: {$version['version']}</p>";
    
    $currentDb = $pdo->query("SELECT DATABASE() as db_name")->fetch();
    echo "<p>Base de datos actual: {$currentDb['db_name']}</p>";
    
    // Contar tablas
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Tablas encontradas: " . count($tables) . "</p>";
    
    if (count($tables) > 0) {
        echo "<div class='code'>";
        echo "Tablas: " . implode(', ', array_slice($tables, 0, 10));
        if (count($tables) > 10) {
            echo " ... (+" . (count($tables) - 10) . " más)";
        }
        echo "</div>";
    }
    
    echo "</div>";
    echo "<div class='test success'>";
    echo "<h3>✅ ¡Todo parece estar funcionando!</h3>";
    echo "<p>La base de datos está conectada y las tablas existen.</p>";
    echo "<p>Si los scripts de verificación aún muestran errores, puede ser un problema de:</p>";
    echo "<ul>";
    echo "<li>Cache de PHP/OPcache</li>";
    echo "<li>Permisos de archivos</li>";
    echo "<li>Configuración del servidor web</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p>❌ Error de conexión: " . $e->getMessage() . "</p>";
    
    echo "</div>";
    echo "<div class='test error'>";
    echo "<h3>🔧 Posibles Soluciones</h3>";
    echo "<ol>";
    echo "<li><strong>Verificar MySQL:</strong> Asegúrate de que MySQL esté ejecutándose</li>";
    echo "<li><strong>Crear base de datos:</strong> Ejecuta este comando en MySQL:</li>";
    echo "<div class='code'>CREATE DATABASE stylofitness_gym CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</div>";
    echo "<li><strong>Importar esquema:</strong> Importa el archivo SQL:</li>";
    echo "<div class='code'>mysql -u root -p stylofitness_gym < database/stylofitness_complete.sql</div>";
    echo "<li><strong>Verificar usuario:</strong> Asegúrate de que el usuario MySQL tenga permisos</li>";
    echo "</ol>";
}

echo "</div>";

// Test 4: Verificar archivos del sistema
echo "<div class='test info'>";
echo "<h3>📁 Archivos del Sistema</h3>";

$criticalFiles = [
    'index.php' => 'Archivo principal',
    'app/Config/Database.php' => 'Configuración de BD',
    'app/Controllers/ApiController.php' => 'Controlador API',
    'app/Models/User.php' => 'Modelo de usuarios'
];

foreach ($criticalFiles as $file => $desc) {
    $exists = file_exists($file);
    echo "<p>" . ($exists ? '✅' : '❌') . " {$desc}: " . ($exists ? 'Encontrado' : 'No encontrado') . "</p>";
}
echo "</div>";

// Botones de acción
echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='?' style='display: inline-block; padding: 12px 24px; background: #FF6B00; color: white; text-decoration: none; border-radius: 6px; margin: 5px;'>🔄 Verificar Nuevamente</a>";
echo "<a href='db_verification.php' style='display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 6px; margin: 5px;'>📊 Verificación Completa</a>";
echo "<a href='system_diagnostic.php' style='display: inline-block; padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 6px; margin: 5px;'>🔧 Diagnóstico del Sistema</a>";
echo "</div>";

echo "</div>";
echo "</body></html>";
?>