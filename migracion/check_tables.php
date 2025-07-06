<?php
/**
 * Script para verificar la estructura de las tablas de StyloFitness
 */

try {
    // Conectar a la base de datos StyloFitness
    $pdo = new PDO('mysql:host=localhost;dbname=stylofitness_gym;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexión a stylofitness_gym exitosa\n\n";
    
    // Mostrar todas las tablas
    echo "📋 TABLAS DISPONIBLES:\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    echo "\n";
    
    // Verificar estructura de product_categories
    if (in_array('product_categories', $tables)) {
        echo "🔍 ESTRUCTURA DE product_categories:\n";
        echo str_repeat("-", 50) . "\n";
        
        $stmt = $pdo->query('DESCRIBE product_categories');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo sprintf("  %-20s %-15s %s\n", 
                $column['Field'], 
                $column['Type'], 
                $column['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
            );
        }
        echo "\n";
    }
    
    // Verificar estructura de products
    if (in_array('products', $tables)) {
        echo "🔍 ESTRUCTURA DE products:\n";
        echo str_repeat("-", 50) . "\n";
        
        $stmt = $pdo->query('DESCRIBE products');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo sprintf("  %-20s %-15s %s\n", 
                $column['Field'], 
                $column['Type'], 
                $column['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
            );
        }
        echo "\n";
    }
    
    // Verificar si existen datos en las tablas
    foreach (['product_categories', 'products'] as $table) {
        if (in_array($table, $tables)) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "📊 Registros en $table: $count\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>