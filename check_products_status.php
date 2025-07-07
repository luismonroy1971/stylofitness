<?php
/**
 * Script para verificar el estado de todos los productos
 */

require_once __DIR__ . '/app/Config/Database.php';

use StyleFitness\Config\Database;

try {
    $db = Database::getInstance();
    
    // Contar total de productos
    $total = $db->fetch('SELECT COUNT(*) as total FROM products')['total'];
    echo "Total de productos en la base de datos: {$total}\n\n";
    
    // Contar productos con precio de oferta
    $withSalePrice = $db->fetch('SELECT COUNT(*) as total FROM products WHERE sale_price IS NOT NULL AND sale_price > 0')['total'];
    echo "Productos con precio de oferta: {$withSalePrice}\n";
    
    // Contar productos sin precio de oferta
    $withoutSalePrice = $db->fetch('SELECT COUNT(*) as total FROM products WHERE sale_price IS NULL OR sale_price = 0')['total'];
    echo "Productos sin precio de oferta: {$withoutSalePrice}\n\n";
    
    // Mostrar ejemplos de productos
    echo "Ejemplos de productos:\n";
    $examples = $db->fetchAll('SELECT name, price, sale_price FROM products ORDER BY id LIMIT 15');
    
    foreach ($examples as $product) {
        $salePrice = $product['sale_price'] ?? 'NULL';
        echo "- {$product['name']}: Precio normal S/{$product['price']}, Precio oferta S/{$salePrice}\n";
    }
    
    echo "\n";
    
    // Si hay productos sin precio de oferta, mostrarlos
    if ($withoutSalePrice > 0) {
        echo "Productos que necesitan migración:\n";
        $needMigration = $db->fetchAll('SELECT id, name, price, sale_price FROM products WHERE sale_price IS NULL OR sale_price = 0 LIMIT 10');
        
        foreach ($needMigration as $product) {
            echo "- ID {$product['id']}: {$product['name']} (Precio actual: S/{$product['price']})\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}