<?php
require_once 'app/Config/Database.php';
require_once 'app/Models/Product.php';
require_once 'app/Helpers/AppHelper.php';

use StyleFitness\Models\Product;
use StyleFitness\Helpers\AppHelper;

try {
    $productModel = new Product();
    
    echo "Probando findBySlug con: whey-protein-gold-standard-2-5kg\n";
    $product = $productModel->findBySlug('whey-protein-gold-standard-2-5kg');
    
    if ($product) {
        echo "Producto encontrado:\n";
        echo "ID: {$product['id']}\n";
        echo "Nombre: {$product['name']}\n";
        echo "Slug: {$product['slug']}\n";
        echo "Precio: {$product['price']}\n";
        echo "Descripción: " . substr($product['description'], 0, 100) . "...\n";
    } else {
        echo "Producto NO encontrado\n";
    }
    
    // Probar también con un slug más corto
    echo "\nProbando findBySlug con: whey\n";
    $product2 = $productModel->findBySlug('whey');
    
    if ($product2) {
        echo "Producto encontrado:\n";
        echo "ID: {$product2['id']}\n";
        echo "Nombre: {$product2['name']}\n";
        echo "Slug: {$product2['slug']}\n";
    } else {
        echo "Producto NO encontrado\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>