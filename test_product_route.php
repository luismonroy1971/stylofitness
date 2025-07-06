<?php
// Simular el acceso a una URL de producto
$_SERVER['REQUEST_URI'] = '/store/product/whey-protein-gold-standard-2-5kg';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Capturar toda la salida
ob_start();

try {
    // Incluir el index.php que maneja el enrutamiento
    include 'index.php';
    
    $output = ob_get_clean();
    
    echo "=== RESULTADO DEL ENRUTAMIENTO ===\n";
    echo "Longitud de salida: " . strlen($output) . " caracteres\n";
    
    if (empty($output)) {
        echo "ERROR: No se generó ninguna salida\n";
    } else {
        echo "Se generó contenido HTML\n";
        
        // Verificar si contiene elementos clave de la página de producto
        if (strpos($output, 'product-detail-container') !== false) {
            echo "✓ Contiene contenedor de producto\n";
        } else {
            echo "✗ NO contiene contenedor de producto\n";
        }
        
        if (strpos($output, 'Whey Protein') !== false) {
            echo "✓ Contiene información del producto\n";
        } else {
            echo "✗ NO contiene información del producto\n";
        }
        
        if (strpos($output, '404') !== false) {
            echo "✗ Muestra página 404\n";
        } else {
            echo "✓ No es página 404\n";
        }
        
        // Mostrar las primeras líneas para debug
        echo "\n=== PRIMERAS LÍNEAS DE SALIDA ===\n";
        $lines = explode("\n", $output);
        for ($i = 0; $i < min(15, count($lines)); $i++) {
            $line = trim($lines[$i]);
            if (!empty($line)) {
                echo "Línea $i: " . substr($line, 0, 100) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>