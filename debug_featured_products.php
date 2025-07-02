<?php
require_once 'app/Config/Database.php';
require_once 'app/Models/Product.php';
require_once 'app/Helpers/AppHelper.php';

try {
    $db = new Database();
    $productModel = new Product($db->getConnection());
    $products = $productModel->getFeaturedProducts(8);
    
    echo "<h2>Diagnóstico de Productos Destacados</h2>";
    echo "<p>Total productos encontrados: " . count($products) . "</p>";
    
    foreach($products as $index => $product) {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<h3>Producto " . ($index + 1) . "</h3>";
        echo "<p><strong>ID:</strong> " . ($product['id'] ?? 'NULL') . "</p>";
        echo "<p><strong>Nombre:</strong> " . ($product['name'] ?? 'NULL') . "</p>";
        echo "<p><strong>Slug:</strong> " . ($product['slug'] ?? 'NULL') . "</p>";
        echo "<p><strong>Precio:</strong> " . ($product['price'] ?? 'NULL') . "</p>";
        echo "<p><strong>Precio de oferta:</strong> " . ($product['sale_price'] ?? 'NULL') . "</p>";
        echo "<p><strong>Stock:</strong> " . ($product['stock_quantity'] ?? 'NULL') . "</p>";
        echo "<p><strong>Categoría:</strong> " . ($product['category_name'] ?? 'NULL') . "</p>";
        echo "<p><strong>Descripción corta:</strong> " . ($product['short_description'] ?? 'NULL') . "</p>";
        echo "<p><strong>Imágenes (raw):</strong> " . ($product['images'] ?? 'NULL') . "</p>";
        
        // Procesar imágenes como en el código real
        $productImages = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
        if (!empty($productImages)) {
            if (strpos($productImages[0], '/uploads/') === 0) {
                $mainImage = AppHelper::getBaseUrl() . ltrim($productImages[0], '/');
            } else {
                $mainImage = AppHelper::uploadUrl($productImages[0]);
            }
        } else {
            $mainImage = AppHelper::asset('images/placeholder.jpg');
        }
        echo "<p><strong>URL de imagen procesada:</strong> " . $mainImage . "</p>";
        
        echo "<p><strong>Es destacado:</strong> " . ($product['is_featured'] ? 'Sí' : 'No') . "</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>