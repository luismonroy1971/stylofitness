<?php
// Script para verificar qué productos existen en la base de datos

// Definir constantes necesarias
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

require_once 'app/Config/Database.php';
require_once 'app/Models/Product.php';

echo "=== VERIFICACIÓN DE PRODUCTOS EN LA BASE DE DATOS ===\n";

try {
    $product = new StyleFitness\Models\Product();
    
    // Obtener todos los productos
    $products = $product->getProducts(['is_active' => '']); // Obtener todos, activos e inactivos
    
    echo "Total de productos encontrados: " . count($products) . "\n\n";
    
    if (count($products) > 0) {
        echo "Lista de productos:\n";
        echo str_repeat("-", 80) . "\n";
        
        foreach ($products as $prod) {
            echo "ID: {$prod['id']}\n";
            echo "Nombre: {$prod['name']}\n";
            echo "Slug: {$prod['slug']}\n";
            echo "Precio: {$prod['price']}\n";
            echo "Stock: {$prod['stock']}\n";
            echo "Estado: " . ($prod['is_active'] ? 'Activo' : 'Inactivo') . "\n";
            echo str_repeat("-", 80) . "\n";
        }
        
        // Buscar específicamente el producto que estamos probando
        echo "\n=== BÚSQUEDA ESPECÍFICA ===\n";
        $testProduct = $product->findBySlug('proteina-whey-gold-standard');
        
        if ($testProduct) {
            echo "✓ Producto 'proteina-whey-gold-standard' encontrado:\n";
            echo "Nombre: {$testProduct['name']}\n";
            echo "Slug: {$testProduct['slug']}\n";
        } else {
            echo "✗ Producto 'proteina-whey-gold-standard' NO encontrado\n";
            
            // Buscar productos similares
            echo "\nBuscando productos con 'whey' en el nombre o slug:\n";
            foreach ($products as $prod) {
                if (stripos($prod['name'], 'whey') !== false || stripos($prod['slug'], 'whey') !== false) {
                    echo "- {$prod['name']} (slug: {$prod['slug']})\n";
                }
            }
            
            echo "\nBuscando productos con 'proteina' en el nombre o slug:\n";
            foreach ($products as $prod) {
                if (stripos($prod['name'], 'proteina') !== false || stripos($prod['slug'], 'proteina') !== false) {
                    echo "- {$prod['name']} (slug: {$prod['slug']})\n";
                }
            }
        }
        
    } else {
        echo "No se encontraron productos en la base de datos.\n";
        echo "Esto podría indicar un problema de conexión a la base de datos o que no hay datos.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n=== VERIFICACIÓN COMPLETADA ===\n";
?>