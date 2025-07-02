<!DOCTYPE html>
<html>
<head>
    <title>Debug Frontend - Productos Destacados</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-section { border: 1px solid #ccc; margin: 10px 0; padding: 15px; }
        .product-card { border: 1px solid #ddd; margin: 10px; padding: 10px; display: inline-block; width: 200px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Debug Frontend - Productos Destacados</h1>
    
    <div class="debug-section">
        <h2>1. Verificación de Productos en PHP</h2>
        <?php
        require_once 'app/Config/Database.php';
        require_once 'app/Models/Product.php';
        require_once 'app/Helpers/AppHelper.php';
        
        try {
            $db = new Database();
            $productModel = new Product($db->getConnection());
            $products = $productModel->getFeaturedProducts(8);
            
            echo "<p class='success'>✓ Productos cargados: " . count($products) . "</p>";
            
            foreach($products as $index => $product) {
                echo "<div class='product-card'>";
                echo "<strong>Producto " . ($index + 1) . "</strong><br>";
                echo "ID: " . $product['id'] . "<br>";
                echo "Nombre: " . htmlspecialchars($product['name']) . "<br>";
                echo "Precio: S/ " . $product['price'] . "<br>";
                echo "Stock: " . $product['stock_quantity'] . "<br>";
                
                // Verificar imagen
                $productImages = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
                if (!empty($productImages)) {
                    if (strpos($productImages[0], '/uploads/') === 0) {
                        $mainImage = AppHelper::getBaseUrl() . ltrim($productImages[0], '/');
                    } else {
                        $mainImage = AppHelper::uploadUrl($productImages[0]);
                    }
                    echo "<img src='" . $mainImage . "' style='width: 50px; height: 50px; object-fit: cover;' alt='Imagen'><br>";
                    echo "URL: " . $mainImage . "<br>";
                } else {
                    echo "<span class='error'>Sin imagen</span><br>";
                }
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
    
    <div class="debug-section">
        <h2>2. Simulación de HTML de Productos</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <?php if (isset($products) && !empty($products)): ?>
                <?php foreach ($products as $index => $product): ?>
                    <div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: white;">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p>Categoría: <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></p>
                        <p>Precio: S/ <?php echo number_format($product['price'], 2); ?></p>
                        <p>Stock: <?php echo $product['stock_quantity']; ?></p>
                        <?php 
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
                        ?>
                        <img src="<?php echo $mainImage; ?>" style="width: 100%; height: 150px; object-fit: cover;" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="debug-section">
        <h2>3. Verificación de CSS y JavaScript</h2>
        <p>Verificar en la consola del navegador si hay errores de JavaScript o CSS.</p>
        <button onclick="checkProductCards()">Verificar Tarjetas de Productos</button>
        <div id="js-debug-output"></div>
    </div>
    
    <script>
        function checkProductCards() {
            const output = document.getElementById('js-debug-output');
            let html = '<h4>Resultados de verificación:</h4>';
            
            // Verificar si existen elementos de productos
            const productCards = document.querySelectorAll('.product-card-enhanced');
            html += '<p>Tarjetas de productos encontradas: ' + productCards.length + '</p>';
            
            const featuredSection = document.querySelector('.featured-products-section-enhanced');
            html += '<p>Sección de productos destacados: ' + (featuredSection ? 'Encontrada' : 'No encontrada') + '</p>';
            
            const productsGrid = document.querySelector('.products-grid-enhanced');
            html += '<p>Grid de productos: ' + (productsGrid ? 'Encontrado' : 'No encontrado') + '</p>';
            
            // Verificar errores en consola
            html += '<p>Revisa la consola del navegador (F12) para errores de JavaScript o CSS.</p>';
            
            output.innerHTML = html;
        }
        
        // Verificar automáticamente al cargar
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Debug Frontend cargado');
            console.log('Verificando elementos de productos...');
            
            setTimeout(checkProductCards, 1000);
        });
    </script>
</body>
</html>