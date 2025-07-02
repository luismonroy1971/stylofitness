<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=stylofitness_gym', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query('SELECT id, name, images FROM products WHERE is_featured = 1 ORDER BY id');
    
    echo "=== PRODUCTOS DESTACADOS ===\n";
    while($row = $stmt->fetch()) {
        echo "ID: " . $row['id'] . "\n";
        echo "Nombre: " . $row['name'] . "\n";
        echo "Imágenes: " . $row['images'] . "\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}