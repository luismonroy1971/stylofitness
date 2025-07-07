<?php
/**
 * Script de migración para implementar sistema de doble precio
 * Convierte los precios actuales en precios de oferta y establece
 * el precio normal como precio actual + 30 soles
 */

require_once __DIR__ . '/../../app/Config/Database.php';

use StyleFitness\Config\Database;

try {
    $db = Database::getInstance();
    
    echo "Iniciando migración de sistema de doble precio...\n";
    
    // Obtener todos los productos que no tienen sale_price o tienen sale_price = 0
    $products = $db->fetchAll(
        "SELECT id, name, price, sale_price FROM products WHERE sale_price IS NULL OR sale_price = 0 OR sale_price = price"
    );
    
    echo "Productos a actualizar: " . count($products) . "\n";
    
    $updated = 0;
    
    foreach ($products as $product) {
        $currentPrice = floatval($product['price']);
        $newNormalPrice = $currentPrice + 30.00; // Precio normal = precio actual + 30 soles
        $newSalePrice = $currentPrice; // Precio de oferta = precio actual
        
        // Actualizar el producto
        $result = $db->query(
            "UPDATE products SET price = ?, sale_price = ? WHERE id = ?",
            [$newNormalPrice, $newSalePrice, $product['id']]
        );
        
        if ($result) {
            $updated++;
            echo "✓ Producto '{$product['name']}': Precio normal S/{$newNormalPrice}, Precio oferta S/{$newSalePrice}\n";
        } else {
            echo "✗ Error actualizando producto '{$product['name']}'\n";
        }
    }
    
    echo "\nMigración completada. Productos actualizados: {$updated}\n";
    
    // Mostrar algunos ejemplos de los cambios
    echo "\nEjemplos de productos actualizados:\n";
    $examples = $db->fetchAll(
        "SELECT id, name, price as precio_normal, sale_price as precio_oferta, 
         (price - sale_price) as descuento 
         FROM products 
         WHERE sale_price IS NOT NULL AND sale_price > 0 
         LIMIT 5"
    );
    
    foreach ($examples as $example) {
        echo "- {$example['name']}: Normal S/{$example['precio_normal']}, Oferta S/{$example['precio_oferta']}, Descuento S/{$example['descuento']}\n";
    }
    
} catch (Exception $e) {
    echo "Error durante la migración: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n¡Migración completada exitosamente!\n";