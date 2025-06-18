<?php
/**
 * Script de Diagnóstico y Reparación - StyloFitness
 * Identifica y corrige problemas comunes de enrutamiento
 */

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Diagnóstico y Reparación - StyloFitness</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 1000px; margin: 2rem auto; padding: 1rem; }";
echo "h1, h2 { color: #FF6B00; }";
echo ".success { color: green; background: #f0f8f0; padding: 1rem; border-radius: 5px; margin: 1rem 0; }";
echo ".error { color: red; background: #f8f0f0; padding: 1rem; border-radius: 5px; margin: 1rem 0; }";
echo ".warning { color: orange; background: #fff8f0; padding: 1rem; border-radius: 5px; margin: 1rem 0; }";
echo ".info { color: blue; background: #f0f0f8; padding: 1rem; border-radius: 5px; margin: 1rem 0; }";
echo ".action-btn { background: #FF6B00; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }";
echo ".action-btn:hover { background: #E55A00; }";
echo "pre { background: #f5f5f5; padding: 1rem; border-radius: 5px; overflow-x: auto; }";
echo "table { width: 100%; border-collapse: collapse; margin: 1rem 0; }";
echo "th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }";
echo "th { background: #f5f5f5; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🔧 Diagnóstico y Reparación - StyloFitness</h1>";

// Función para verificar si se puede escribir
function canWrite($file) {
    if (file_exists($file)) {
        return is_writable($file);
    } else {
        return is_writable(dirname($file));
    }
}

// Función para hacer backup
function makeBackup($file) {
    if (file_exists($file)) {
        $backup = $file . '.backup.' . date('Y-m-d-H-i-s');
        return copy($file, $backup);
    }
    return false;
}

// ==========================================
// 1. DIAGNÓSTICO GENERAL
// ==========================================

echo "<h2>📊 Diagnóstico General</h2>";

$diagnostics = [
    'PHP Version' => PHP_VERSION,
    'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido',
    'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'No definido',
    'Script Name' => $_SERVER['SCRIPT_NAME'] ?? 'No definido',
    'HTTP Host' => $_SERVER['HTTP_HOST'] ?? 'No definido',
    'mod_rewrite' => function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? 'Habilitado' : 'Desconocido/No Apache'
];

echo "<table>";
echo "<tr><th>Parámetro</th><th>Valor</th></tr>";
foreach ($diagnostics as $key => $value) {
    echo "<tr><td><strong>{$key}</strong></td><td>{$value}</td></tr>";
}
echo "</table>";

// ==========================================
// 2. VERIFICACIÓN DE ARCHIVOS
// ==========================================

echo "<h2>📁 Verificación de Archivos</h2>";

$files = [
    'index.php' => file_exists('index.php'),
    '.htaccess' => file_exists('.htaccess'),
    '.htaccess.fixed' => file_exists('.htaccess.fixed'),
    'app/Controllers/AuthController.php' => file_exists('app/Controllers/AuthController.php'),
    'app/Views/auth/login.php' => file_exists('app/Views/auth/login.php'),
    'app/Helpers/AppHelper.php' => file_exists('app/Helpers/AppHelper.php'),
    'app/Config/Database.php' => file_exists('app/Config/Database.php'),
];

echo "<table>";
echo "<tr><th>Archivo</th><th>Estado</th><th>Escribible</th></tr>";
foreach ($files as $file => $exists) {
    $status = $exists ? '✅ Existe' : '❌ No existe';
    $writable = $exists ? (canWrite($file) ? '✅ Sí' : '❌ No') : 'N/A';
    echo "<tr><td>{$file}</td><td>{$status}</td><td>{$writable}</td></tr>";
}
echo "</table>";

// ==========================================
// 3. PRUEBA DE URLs
// ==========================================

echo "<h2>🔗 Prueba de URLs</h2>";

// Simular y cargar AppHelper si existe
if (file_exists('app/Helpers/AppHelper.php')) {
    try {
        require_once 'app/Helpers/AppHelper.php';
        
        echo "<div class='success'>✅ AppHelper cargado correctamente</div>";
        
        $testUrls = [
            'Base URL' => AppHelper::getBaseUrl(),
            'Login URL' => AppHelper::baseUrl('login'),
            'Register URL' => AppHelper::baseUrl('register'),
            'Dashboard URL' => AppHelper::baseUrl('dashboard'),
            'Asset URL' => AppHelper::asset('css/styles.css')
        ];
        
        echo "<table>";
        echo "<tr><th>Tipo</th><th>URL Generada</th><th>¿Tiene //?</th></tr>";
        foreach ($testUrls as $type => $url) {
            $hasDoubleSlash = strpos($url, '//') !== false && strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0;
            $status = $hasDoubleSlash ? '❌ SÍ (Problema)' : '✅ NO (Correcto)';
            echo "<tr><td>{$type}</td><td>{$url}</td><td>{$status}</td></tr>";
        }
        echo "</table>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error cargando AppHelper: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ AppHelper.php no encontrado</div>";
}

// ==========================================
// 4. ANÁLISIS DEL .HTACCESS
// ==========================================

echo "<h2>⚙️ Análisis del .htaccess</h2>";

if (file_exists('.htaccess')) {
    $htaccessContent = file_get_contents('.htaccess');
    
    $checks = [
        'RewriteEngine On' => strpos($htaccessContent, 'RewriteEngine On') !== false,
        'RewriteBase definido' => strpos($htaccessContent, 'RewriteBase') !== false,
        'Regla principal correcta' => strpos($htaccessContent, 'RewriteRule') !== false,
        'Condiciones de archivo' => strpos($htaccessContent, 'REQUEST_FILENAME') !== false
    ];
    
    echo "<table>";
    echo "<tr><th>Verificación</th><th>Estado</th></tr>";
    foreach ($checks as $check => $result) {
        $status = $result ? '✅ Correcto' : '❌ Faltante';
        echo "<tr><td>{$check}</td><td>{$status}</td></tr>";
    }
    echo "</table>";
    
} else {
    echo "<div class='error'>❌ Archivo .htaccess no encontrado</div>";
}

// ==========================================
// 5. ACCIONES DE REPARACIÓN
// ==========================================

echo "<h2>🛠️ Acciones de Reparación</h2>";

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'fix_htaccess':
            if (file_exists('.htaccess.fixed')) {
                makeBackup('.htaccess');
                if (copy('.htaccess.fixed', '.htaccess')) {
                    echo "<div class='success'>✅ .htaccess reparado correctamente</div>";
                } else {
                    echo "<div class='error'>❌ Error al reparar .htaccess</div>";
                }
            } else {
                echo "<div class='error'>❌ Archivo .htaccess.fixed no encontrado</div>";
            }
            break;
            
        case 'test_routes':
            echo "<div class='info'>";
            echo "<h3>Probando rutas...</h3>";
            $testRoutes = ['/', '/login', '/register', '/dashboard'];
            foreach ($testRoutes as $route) {
                $url = "http://{$_SERVER['HTTP_HOST']}/stylofitness{$route}";
                echo "<p><a href='{$url}' target='_blank'>{$route}</a></p>";
            }
            echo "</div>";
            break;
            
        case 'clear_cache':
            if (function_exists('opcache_reset')) {
                opcache_reset();
                echo "<div class='success'>✅ Caché OPcache limpiado</div>";
            }
            echo "<div class='success'>✅ Operación de limpieza completada</div>";
            break;
    }
}

// Formulario de acciones
echo "<form method='post'>";
echo "<button type='submit' name='action' value='fix_htaccess' class='action-btn'>🔧 Reparar .htaccess</button>";
echo "<button type='submit' name='action' value='test_routes' class='action-btn'>🧪 Probar Rutas</button>";
echo "<button type='submit' name='action' value='clear_cache' class='action-btn'>🗑️ Limpiar Cache</button>";
echo "</form>";

// ==========================================
// 6. ENLACES DE PRUEBA DIRECTA
// ==========================================

echo "<h2>🔗 Enlaces de Prueba Directa</h2>";
echo "<div class='info'>";
echo "<p>Haz clic en estos enlaces para probar las rutas:</p>";
echo "<ul>";
echo "<li><a href='http://{$_SERVER['HTTP_HOST']}/stylofitness/' target='_blank'>🏠 Inicio</a></li>";
echo "<li><a href='http://{$_SERVER['HTTP_HOST']}/stylofitness/login' target='_blank'>🔐 Login</a></li>";
echo "<li><a href='http://{$_SERVER['HTTP_HOST']}/stylofitness/register' target='_blank'>📝 Registro</a></li>";
echo "<li><a href='http://{$_SERVER['HTTP_HOST']}/stylofitness/dashboard' target='_blank'>📊 Dashboard</a></li>";
echo "<li><a href='http://{$_SERVER['HTTP_HOST']}/stylofitness/routines' target='_blank'>🏋️ Rutinas</a></li>";
echo "<li><a href='http://{$_SERVER['HTTP_HOST']}/stylofitness/store' target='_blank'>🛒 Tienda</a></li>";
echo "</ul>";
echo "</div>";

// ==========================================
// 7. INFORMACIÓN TÉCNICA
// ==========================================

echo "<h2>🔍 Información Técnica</h2>";
echo "<div class='info'>";
echo "<h3>Variables del Servidor:</h3>";
echo "<ul>";
echo "<li><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'No definido') . "</li>";
echo "<li><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'No definido') . "</li>";
echo "<li><strong>dirname(SCRIPT_NAME):</strong> " . dirname($_SERVER['SCRIPT_NAME'] ?? '') . "</li>";
echo "<li><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "</li>";
echo "<li><strong>SERVER_NAME:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'No definido') . "</li>";
echo "</ul>";

echo "<h3>Configuración PHP:</h3>";
echo "<ul>";
echo "<li><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</li>";
echo "<li><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</li>";
echo "<li><strong>memory_limit:</strong> " . ini_get('memory_limit') . "</li>";
echo "<li><strong>session.save_path:</strong> " . session_save_path() . "</li>";
echo "</ul>";
echo "</div>";

echo "</body>";
echo "</html>";
?>