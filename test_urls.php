<?php
/**
 * Test de URLs - StyloFitness
 * Verifica que las URLs se generen correctamente
 */

// Simular el entorno
$_SERVER['HTTP_HOST'] = 'localhost:8000';
$_SERVER['SCRIPT_NAME'] = '/stylofitness/index.php';
$_SERVER['HTTPS'] = '';

// Incluir los helpers
require_once __DIR__ . '/app/Helpers/AppHelper.php';

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Test de URLs - StyloFitness</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 800px; margin: 2rem auto; padding: 1rem; }";
echo "h1, h2 { color: #FF6B00; }";
echo ".test-case { background: #f5f5f5; padding: 1rem; margin: 1rem 0; border-radius: 5px; }";
echo ".result { font-weight: bold; color: #007bff; }";
echo ".expected { color: #28a745; }";
echo ".error { color: #dc3545; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🧪 Test de Generación de URLs</h1>";

// Test 1: getBaseUrl
echo "<div class='test-case'>";
echo "<h3>Test 1: getBaseUrl()</h3>";
$baseUrl = AppHelper::getBaseUrl();
echo "<p><strong>Resultado:</strong> <span class='result'>{$baseUrl}</span></p>";
echo "<p><strong>Esperado:</strong> <span class='expected'>http://localhost:8000/stylofitness/</span></p>";
echo "<p><strong>¿Correcto?:</strong> " . ($baseUrl === 'http://localhost:8000/stylofitness/' ? "✅ SÍ" : "❌ NO") . "</p>";
echo "</div>";

// Test 2: baseUrl con parámetro
echo "<div class='test-case'>";
echo "<h3>Test 2: baseUrl('login')</h3>";
$loginUrl = AppHelper::baseUrl('login');
echo "<p><strong>Resultado:</strong> <span class='result'>{$loginUrl}</span></p>";
echo "<p><strong>Esperado:</strong> <span class='expected'>http://localhost:8000/stylofitness/login</span></p>";
echo "<p><strong>¿Correcto?:</strong> " . ($loginUrl === 'http://localhost:8000/stylofitness/login' ? "✅ SÍ" : "❌ NO") . "</p>";
echo "</div>";

// Test 3: baseUrl con slash inicial
echo "<div class='test-case'>";
echo "<h3>Test 3: baseUrl('/register')</h3>";
$registerUrl = AppHelper::baseUrl('/register');
echo "<p><strong>Resultado:</strong> <span class='result'>{$registerUrl}</span></p>";
echo "<p><strong>Esperado:</strong> <span class='expected'>http://localhost:8000/stylofitness/register</span></p>";
echo "<p><strong>¿Correcto?:</strong> " . ($registerUrl === 'http://localhost:8000/stylofitness/register' ? "✅ SÍ" : "❌ NO") . "</p>";
echo "</div>";

// Test 4: URL vacía
echo "<div class='test-case'>";
echo "<h3>Test 4: baseUrl('')</h3>";
$homeUrl = AppHelper::baseUrl('');
echo "<p><strong>Resultado:</strong> <span class='result'>{$homeUrl}</span></p>";
echo "<p><strong>Esperado:</strong> <span class='expected'>http://localhost:8000/stylofitness/</span></p>";
echo "<p><strong>¿Correcto?:</strong> " . ($homeUrl === 'http://localhost:8000/stylofitness/' ? "✅ SÍ" : "❌ NO") . "</p>";
echo "</div>";

// Test 5: asset URL
echo "<div class='test-case'>";
echo "<h3>Test 5: asset('css/styles.css')</h3>";
$assetUrl = AppHelper::asset('css/styles.css');
echo "<p><strong>Resultado:</strong> <span class='result'>{$assetUrl}</span></p>";
echo "<p><strong>Esperado:</strong> <span class='expected'>http://localhost:8000/stylofitness/public/css/styles.css</span></p>";
echo "<p><strong>¿Correcto?:</strong> " . ($assetUrl === 'http://localhost:8000/stylofitness/public/css/styles.css' ? "✅ SÍ" : "❌ NO") . "</p>";
echo "</div>";

// Test 6: URLs múltiples para verificar patrones
echo "<div class='test-case'>";
echo "<h3>Test 6: URLs Múltiples</h3>";
$testUrls = [
    'dashboard' => 'http://localhost:8000/stylofitness/dashboard',
    'routines' => 'http://localhost:8000/stylofitness/routines',
    'store' => 'http://localhost:8000/stylofitness/store',
    'classes' => 'http://localhost:8000/stylofitness/classes',
    'admin' => 'http://localhost:8000/stylofitness/admin'
];

foreach ($testUrls as $path => $expected) {
    $result = AppHelper::baseUrl($path);
    $isCorrect = $result === $expected;
    echo "<p><strong>{$path}:</strong> ";
    echo "<span class='result'>{$result}</span> ";
    echo ($isCorrect ? "✅" : "❌");
    echo "</p>";
}
echo "</div>";

// Información del servidor
echo "<div class='test-case'>";
echo "<h3>📊 Información del Servidor</h3>";
echo "<ul>";
echo "<li><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "</li>";
echo "<li><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'No definido') . "</li>";
echo "<li><strong>dirname(SCRIPT_NAME):</strong> " . dirname($_SERVER['SCRIPT_NAME']) . "</li>";
echo "<li><strong>PHP_SELF:</strong> " . ($_SERVER['PHP_SELF'] ?? 'No definido') . "</li>";
echo "</ul>";
echo "</div>";

// Enlaces de prueba
echo "<div class='test-case'>";
echo "<h3>🔗 Enlaces de Prueba</h3>";
echo "<p>Haz clic en estos enlaces para verificar que funcionan:</p>";
echo "<ul>";
echo "<li><a href='" . AppHelper::baseUrl() . "' target='_blank'>🏠 Inicio</a></li>";
echo "<li><a href='" . AppHelper::baseUrl('login') . "' target='_blank'>🔐 Login</a></li>";
echo "<li><a href='" . AppHelper::baseUrl('register') . "' target='_blank'>📝 Registro</a></li>";
echo "<li><a href='" . AppHelper::baseUrl('dashboard') . "' target='_blank'>📊 Dashboard</a></li>";
echo "<li><a href='" . AppHelper::baseUrl('routines') . "' target='_blank'>🏋️ Rutinas</a></li>";
echo "<li><a href='" . AppHelper::baseUrl('store') . "' target='_blank'>🛒 Tienda</a></li>";
echo "</ul>";
echo "</div>";

echo "</body>";
echo "</html>";
?>