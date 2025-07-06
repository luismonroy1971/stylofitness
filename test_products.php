<?php
require_once 'app/Config/Database.php';

use StyleFitness\Config\Database;

try {
    $db = Database::getInstance();
    $products = $db->fetchAll('SELECT id, name, slug FROM products WHERE is_active = 1 LIMIT 5');
    
    echo "Productos encontrados:\n";
    foreach($products as $p) {
        echo "ID: {$p['id']}, Name: {$p['name']}, Slug: {$p['slug']}\n";
    }
    
    if (empty($products)) {
        echo "No se encontraron productos activos.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>