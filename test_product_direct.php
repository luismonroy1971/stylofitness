<?php
// Test directo de la página de producto

// Configurar manejo de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Simular una solicitud GET a la página de producto
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/store/product/proteina-whey-gold-standard';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['QUERY_STRING'] = '';
$_SERVER['DOCUMENT_ROOT'] = __DIR__;
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Test)';

// Capturar toda la salida
ob_start();

try {
    // Incluir el archivo principal
    include 'index.php';
    
    $output = ob_get_contents();
    ob_end_clean();
    
    echo "Iniciando test de producto...\n";
    echo "Variables de servidor configuradas...\n";
    echo "Incluyendo index.php...\n";
    echo "=== RESULTADO DEL TEST ===\n";
    echo "Longitud de salida: " . strlen($output) . " caracteres\n";
    
    // Verificar si contiene elementos clave de la página de producto
    if (strpos($output, 'product-detail-container') !== false) {
        echo "✓ Contenedor de producto encontrado\n";
    } else {
        echo "✗ Contenedor de producto NO encontrado\n";
    }
    
    if (strpos($output, 'Proteína Whey Gold Standard') !== false) {
        echo "✓ Título del producto encontrado\n";
    } else {
        echo "✗ Título del producto NO encontrado\n";
    }
    
    if (strpos($output, '500') !== false || strpos($output, 'Error') !== false) {
        echo "✗ Posible error 500 detectado\n";
    } else {
        echo "✓ No se detectaron errores 500\n";
    }
    
    if (strpos($output, '404') !== false || strpos($output, 'no encontrada') !== false) {
        echo "✗ Posible error 404 detectado\n";
    } else {
        echo "✓ No se detectaron errores 404\n";
    }
    
    // Mostrar los primeros 500 caracteres de la salida
    echo "\n=== PRIMEROS 500 CARACTERES ===\n";
    echo substr($output, 0, 500) . "...\n";
    
    // Mostrar los últimos 500 caracteres de la salida
    if (strlen($output) > 500) {
        echo "\n=== ÚLTIMOS 500 CARACTERES ===\n";
        echo "..." . substr($output, -500) . "\n";
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "ERROR EXCEPTION: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    ob_end_clean();
    echo "ERROR FATAL: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\nTest completado.\n";
?>