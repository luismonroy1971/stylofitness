<?php
// Test simple de routing
session_start();

// Incluir configuración
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');

require_once APP_PATH . '/Config/Database.php';
require_once APP_PATH . '/Helpers/AppHelper.php';
require_once APP_PATH . '/Controllers/StoreController.php';
require_once APP_PATH . '/Models/Product.php';
require_once APP_PATH . '/Models/ProductCategory.php';

use StyleFitness\Controllers\StoreController;

echo "<h1>Test de Ruta del Producto</h1>";

try {
    $controller = new StoreController();
    echo "<p>✓ StoreController creado correctamente</p>";
    
    // Test del método product
    echo "<p>Intentando cargar producto 'c4-original-pre-workout-390g'...</p>";
    
    ob_start();
    $controller->product('c4-original-pre-workout-390g');
    $output = ob_get_clean();
    
    if (strlen($output) > 0) {
        echo "<p>✓ Producto cargado correctamente. Longitud de salida: " . strlen($output) . " caracteres</p>";
        echo "<details><summary>Ver salida</summary><pre>" . htmlspecialchars(substr($output, 0, 1000)) . "...</pre></details>";
    } else {
        echo "<p>✗ No se generó salida</p>";
    }
    
} catch (Exception $e) {
    echo "<p>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>