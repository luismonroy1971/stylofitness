<?php
// Debug del producto específico
session_start();

// Incluir configuración
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');

require_once APP_PATH . '/Config/Database.php';
require_once APP_PATH . '/Helpers/AppHelper.php';
require_once APP_PATH . '/Models/Product.php';

use StyleFitness\Models\Product;

$productModel = new Product();
$product = $productModel->findBySlug('c4-original-pre-workout-390g');

header('Content-Type: application/json');
echo json_encode([
    'product_found' => !empty($product),
    'product_data' => $product,
    'error' => $product ? null : 'Producto no encontrado'
], JSON_PRETTY_PRINT);
?>