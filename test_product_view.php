<?php
// Test simple de la vista del producto
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

if (!$product) {
    die('Producto no encontrado');
}

// Variables necesarias para la vista
$pageTitle = $product['name'] . ' - STYLOFITNESS';
$pageDescription = $product['short_description'];
$additionalCSS = ['product.css'];
$additionalJS = ['product.js'];
$relatedProducts = [];
$reviews = [];
$canReview = false;
$breadcrumb = [];

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>" . htmlspecialchars($pageTitle) . "</title>";
echo "<link rel='stylesheet' href='/public/css/styles.css'>";
echo "<link rel='stylesheet' href='/public/css/product.css'>";
echo "</head>";
echo "<body>";
echo "<div style='padding: 20px; color: white; background: #0a0a0a;'>";
echo "<h1>Test de Vista de Producto</h1>";
echo "<p>Producto encontrado: " . htmlspecialchars($product['name']) . "</p>";
echo "<p>Precio: $" . htmlspecialchars($product['price']) . "</p>";
echo "<p>Descripción: " . htmlspecialchars($product['short_description']) . "</p>";
echo "</div>";

// Incluir la vista del producto
try {
    include APP_PATH . '/Views/store/product.php';
} catch (Exception $e) {
    echo "<div style='color: red; padding: 20px;'>Error al cargar la vista: " . $e->getMessage() . "</div>";
}

echo "<script src='/public/js/product.js'></script>";
echo "</body>";
echo "</html>";
?>