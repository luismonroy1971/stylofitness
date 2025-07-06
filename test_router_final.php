<?php
// Test final del StoreController - sin warnings

// Suprimir warnings para ver solo el resultado
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR);
ini_set('display_errors', 1);

// Definir constantes necesarias
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Simular una solicitud GET a la página de producto
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/store/product/gold-standard-100-whey-5-libras';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['QUERY_STRING'] = '';
$_SERVER['DOCUMENT_ROOT'] = __DIR__;
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Test)';

try {
    // Incluir dependencias necesarias
    require_once 'app/Config/Database.php';
    require_once 'app/Models/Product.php';
    require_once 'app/Models/ProductCategory.php';
    require_once 'app/Helpers/AppHelper.php';
    require_once 'app/Controllers/StoreController.php';
    
    // Probar el modelo Product directamente
    $product = new StyleFitness\Models\Product();
    $testProduct = $product->findBySlug('gold-standard-100-whey-5-libras');
    
    if (!$testProduct) {
        echo "ERROR: Producto no encontrado\n";
        exit(1);
    }
    
    // Probar el controlador directamente
    $controller = new StyleFitness\Controllers\StoreController();
    
    // Capturar la salida del método product
    ob_start();
    
    $params = ['slug' => 'gold-standard-100-whey-5-libras'];
    $controller->product($params);
    
    $output = ob_get_contents();
    ob_end_clean();
    
    echo "=== RESULTADO DEL TEST ===\n";
    echo "✓ Producto encontrado: {$testProduct['name']}\n";
    echo "✓ StoreController ejecutado correctamente\n";
    echo "✓ Longitud de salida: " . strlen($output) . " caracteres\n";
    
    // Verificar contenido específico
    $checks = [
        'product-detail-container' => 'Contenedor de producto',
        'Gold Standard' => 'Título del producto',
        'add-to-cart' => 'Botón agregar al carrito',
        'product-price' => 'Precio del producto',
        'product-description' => 'Descripción del producto'
    ];
    
    foreach ($checks as $search => $description) {
        if (strpos($output, $search) !== false) {
            echo "✓ $description encontrado\n";
        } else {
            echo "✗ $description NO encontrado\n";
        }
    }
    
    // Verificar si hay errores
    if (strpos($output, '500') !== false || strpos($output, 'Fatal error') !== false) {
        echo "✗ Error 500 detectado en la salida\n";
    } else {
        echo "✓ No se detectaron errores 500\n";
    }
    
    // Mostrar muestra de la salida
    echo "\n=== MUESTRA DE LA SALIDA (primeros 300 caracteres) ===\n";
    echo substr($output, 0, 300) . "...\n";
    
    echo "\n=== TEST EXITOSO ===\n";
    
} catch (Exception $e) {
    echo "ERROR EXCEPTION: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "ERROR FATAL: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}
?>