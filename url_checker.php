<?php
/**
 * STYLOFITNESS - Verificador de Configuración de URL
 */

echo "<!DOCTYPE html>";
echo "<html><head><title>Verificador URL - StyloFitness</title>";
echo "<style>body{font-family:Arial;margin:20px;background:#f5f5f5;}.container{max-width:800px;margin:0 auto;background:white;padding:30px;border-radius:10px;}.header{background:linear-gradient(135deg,#FF6B00,#E55A00);color:white;padding:20px;margin:-30px -30px 30px -30px;border-radius:10px 10px 0 0;text-align:center;}.info{background:#e9ecef;padding:15px;border-radius:6px;margin:15px 0;}.test-url{display:block;margin:10px 0;padding:10px;background:#f8f9fa;border-radius:4px;}.working{background:#d4edda;border-left:4px solid #28a745;}.error{background:#f8d7da;border-left:4px solid #dc3545;}</style>";
echo "</head><body>";

echo "<div class='container'>";
echo "<div class='header'><h1>🔍 Verificador de URL - StyloFitness</h1></div>";

echo "<div class='info'><h3>📊 Información del Servidor</h3>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>Server Name:</strong> " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p><strong>Server Port:</strong> " . $_SERVER['SERVER_PORT'] . "</p>";
echo "<p><strong>Base Path:</strong> " . dirname($_SERVER['SCRIPT_NAME']) . "</p>";
echo "</div>";

// Detectar URL base correcta
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . $host . $scriptPath;

echo "<div class='info working'><h3>✅ URL Base Detectada</h3>";
echo "<p><strong>URL Base:</strong> <code>{$baseUrl}</code></p>";
echo "</div>";

echo "<div class='info'><h3>🔗 URLs Correctas para Probar</h3>";

$routes = [
    '' => 'Página Principal',
    'login' => 'Login',
    'register' => 'Registro',
    'dashboard' => 'Dashboard', 
    'api/products' => 'API Productos',
    'quick_diagnosis.php' => 'Diagnóstico Rápido'
];

foreach ($routes as $route => $name) {
    $fullUrl = $baseUrl . '/' . $route;
    echo "<div class='test-url'>";
    echo "<strong>{$name}:</strong> <a href='{$fullUrl}' target='_blank'>{$fullUrl}</a>";
    echo "</div>";
}

echo "</div>";

echo "<div class='info'><h3>⚙️ Verificaciones</h3>";

// Verificar .htaccess
if (file_exists('.htaccess')) {
    echo "<div class='working'>✅ .htaccess existe</div>";
} else {
    echo "<div class='error'>❌ .htaccess no encontrado</div>";
}

// Verificar index.php
if (file_exists('index.php')) {
    echo "<div class='working'>✅ index.php existe</div>";
} else {
    echo "<div class='error'>❌ index.php no encontrado</div>";
}

echo "</div>";

echo "<div class='info'><h3>💡 Recomendación</h3>";
echo "<p>Usa las URLs de arriba para acceder a la aplicación. El problema era que estabas usando <code>localhost:8000/login</code> cuando debería ser <code>{$baseUrl}/login</code></p>";
echo "</div>";

echo "</div></body></html>";
?>