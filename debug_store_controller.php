<?php
// Script de debug para StoreController

// Definir constantes necesarias
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

require_once 'app/Config/Database.php';
require_once 'app/Models/Product.php';
require_once 'app/Models/ProductCategory.php';
require_once 'app/Models/Order.php';
require_once 'app/Helpers/AppHelper.php';
require_once 'app/Controllers/StoreController.php';

use StyleFitness\Controllers\StoreController;
use StyleFitness\Helpers\AppHelper;
use StyleFitness\Models\Product;

try {
    echo "=== DEBUG STORE CONTROLLER ===\n";
    
    // Paso 1: Verificar que el modelo Product funciona
    echo "1. Probando modelo Product...\n";
    $productModel = new Product();
    $product = $productModel->findBySlug('whey-protein-gold-standard-2-5kg');
    
    if ($product) {
        echo "✓ Producto encontrado: {$product['name']}\n";
    } else {
        echo "✗ Producto NO encontrado\n";
        exit;
    }
    
    // Paso 2: Verificar que el controlador se puede instanciar
    echo "\n2. Probando instanciación del StoreController...\n";
    
    // Simular variables de sesión y GET
    $_SESSION = [];
    $_GET = [];
    
    $controller = new StoreController();
    echo "✓ StoreController instanciado correctamente\n";
    
    // Paso 3: Probar el método product directamente
    echo "\n3. Probando método product()...\n";
    
    // Capturar errores y salida
    ob_start();
    
    try {
        $controller->product('whey-protein-gold-standard-2-5kg');
        $output = ob_get_clean();
        
        if (empty($output)) {
            echo "✗ El método product() no generó salida\n";
        } else {
            echo "✓ El método product() generó " . strlen($output) . " caracteres\n";
            
            // Verificar contenido clave
            if (strpos($output, 'product-detail-container') !== false) {
                echo "✓ Contiene estructura de producto\n";
            } else {
                echo "✗ NO contiene estructura de producto\n";
            }
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "✗ Error en método product(): " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR GENERAL: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>