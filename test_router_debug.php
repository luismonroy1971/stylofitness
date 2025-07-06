<?php
echo "=== INICIANDO TEST ===\n";

// Configurar manejo de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Configuración de errores establecida\n";

// Definir constantes necesarias
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

echo "Constantes definidas\n";

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

echo "Variables de servidor configuradas\n";

try {
    echo "Cargando Database.php...\n";
    require_once 'app/Config/Database.php';
    echo "✓ Database.php cargado\n";
    
    echo "Cargando Product.php...\n";
    require_once 'app/Models/Product.php';
    echo "✓ Product.php cargado\n";
    
    echo "Cargando ProductCategory.php...\n";
    require_once 'app/Models/ProductCategory.php';
    echo "✓ ProductCategory.php cargado\n";
    
    echo "Cargando AppHelper.php...\n";
    require_once 'app/Helpers/AppHelper.php';
    echo "✓ AppHelper.php cargado\n";
    
    echo "Cargando StoreController.php...\n";
    require_once 'app/Controllers/StoreController.php';
    echo "✓ StoreController.php cargado\n";
    
    echo "Probando modelo Product...\n";
    $product = new StyleFitness\Models\Product();
    echo "✓ Instancia de Product creada\n";
    
    $testProduct = $product->findBySlug('gold-standard-100-whey-5-libras');
    
    if ($testProduct) {
        echo "✓ Producto encontrado: {$testProduct['name']}\n";
    } else {
        echo "✗ Producto NO encontrado\n";
        exit(1);
    }
    
    echo "Probando StoreController...\n";
    $controller = new StyleFitness\Controllers\StoreController();
    echo "✓ StoreController instanciado\n";
    
    echo "Ejecutando método product...\n";
    ob_start();
    
    $params = ['slug' => 'gold-standard-100-whey-5-libras'];
    $controller->product($params);
    
    $output = ob_get_contents();
    ob_end_clean();
    
    echo "✓ Método product ejecutado\n";
    echo "Longitud de salida: " . strlen($output) . " caracteres\n";
    
    if (strlen($output) > 0) {
        echo "✓ Se generó contenido\n";
        echo "Primeros 200 caracteres:\n";
        echo substr($output, 0, 200) . "...\n";
    } else {
        echo "✗ No se generó contenido\n";
    }
    
} catch (Exception $e) {
    echo "ERROR EXCEPTION: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "ERROR FATAL: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n=== TEST COMPLETADO ===\n";
?>