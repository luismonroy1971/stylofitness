<?php
/**
 * Verificación Rápida - StyloFitness
 * Prueba rápida de que las URLs funcionan correctamente
 */

// Configurar headers para JSON
header('Content-Type: application/json');

// Configurar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

$results = [
    'status' => 'success',
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => []
];

try {
    // Test 1: Cargar AppHelper
    if (file_exists(__DIR__ . '/app/Helpers/AppHelper.php')) {
        require_once __DIR__ . '/app/Helpers/AppHelper.php';
        $results['tests']['apphelper'] = ['status' => 'ok', 'message' => 'AppHelper cargado correctamente'];
        
        // Test 2: Generar URLs
        $baseUrl = AppHelper::getBaseUrl();
        $loginUrl = AppHelper::baseUrl('login');
        $hasDoubleSlash = strpos($loginUrl, '//') !== false && strpos($loginUrl, 'http://') !== 0;
        
        $results['tests']['urls'] = [
            'status' => $hasDoubleSlash ? 'error' : 'ok',
            'base_url' => $baseUrl,
            'login_url' => $loginUrl,
            'has_double_slash' => $hasDoubleSlash
        ];
        
    } else {
        $results['tests']['apphelper'] = ['status' => 'error', 'message' => 'AppHelper no encontrado'];
    }
    
    // Test 3: Verificar archivos críticos
    $criticalFiles = [
        'index.php',
        '.htaccess',
        'app/Controllers/AuthController.php',
        'app/Views/auth/login.php'
    ];
    
    $missingFiles = [];
    foreach ($criticalFiles as $file) {
        if (!file_exists(__DIR__ . '/' . $file)) {
            $missingFiles[] = $file;
        }
    }
    
    $results['tests']['files'] = [
        'status' => empty($missingFiles) ? 'ok' : 'error',
        'missing_files' => $missingFiles
    ];
    
    // Test 4: Verificar .htaccess
    if (file_exists(__DIR__ . '/.htaccess')) {
        $htaccessContent = file_get_contents(__DIR__ . '/.htaccess');
        $hasRewriteBase = strpos($htaccessContent, 'RewriteBase') !== false;
        
        $results['tests']['htaccess'] = [
            'status' => $hasRewriteBase ? 'ok' : 'warning',
            'has_rewrite_base' => $hasRewriteBase,
            'message' => $hasRewriteBase ? '.htaccess configurado correctamente' : '.htaccess sin RewriteBase'
        ];
    } else {
        $results['tests']['htaccess'] = ['status' => 'error', 'message' => '.htaccess no encontrado'];
    }
    
    // Test 5: Información del servidor
    $results['server_info'] = [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido',
        'http_host' => $_SERVER['HTTP_HOST'] ?? 'No definido',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'No definido',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'No definido'
    ];
    
    // Determinar status general
    $hasErrors = false;
    foreach ($results['tests'] as $test) {
        if ($test['status'] === 'error') {
            $hasErrors = true;
            break;
        }
    }
    
    $results['status'] = $hasErrors ? 'error' : 'success';
    
} catch (Exception $e) {
    $results['status'] = 'error';
    $results['error'] = $e->getMessage();
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>