<?php
// Direct product test - bypass routing

// Define required constants
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

require_once '../app/config/database.php';
require_once '../app/Models/Product.php';
require_once '../app/Models/ProductCategory.php';
require_once '../app/Controllers/StoreController.php';
require_once '../app/Helpers/AppHelper.php';

use StyleFitness\Controllers\StoreController;

// Set up basic environment
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/store/product/c4-original-pre-workout-390g';

try {
    $controller = new StoreController();
    echo "<h1>Direct Product Test</h1>";
    echo "<p>Testing product method with slug: c4-original-pre-workout-390g</p>";
    
    // Call the product method directly
    $controller->product('c4-original-pre-workout-390g');
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>