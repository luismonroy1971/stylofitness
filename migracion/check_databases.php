<?php
/**
 * Script para verificar bases de datos disponibles
 */

try {
    // Conectar a MySQL sin especificar base de datos
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexión a MySQL exitosa\n\n";
    
    // Mostrar todas las bases de datos
    echo "📋 BASES DE DATOS DISPONIBLES:\n";
    echo str_repeat("-", 40) . "\n";
    
    $stmt = $pdo->query('SHOW DATABASES');
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($databases as $db) {
        echo "  - $db\n";
    }
    
    echo "\n";
    
    // Buscar bases de datos relacionadas con StyloFitness
    $stylofitness_dbs = array_filter($databases, function($db) {
        return stripos($db, 'stylo') !== false || stripos($db, 'fitness') !== false;
    });
    
    if (!empty($stylofitness_dbs)) {
        echo "🎯 BASES DE DATOS DE STYLOFITNESS ENCONTRADAS:\n";
        foreach ($stylofitness_dbs as $db) {
            echo "  ✓ $db\n";
        }
    } else {
        echo "⚠️  No se encontraron bases de datos de StyloFitness\n";
        echo "💡 Bases de datos candidatas:\n";
        foreach ($databases as $db) {
            if (!in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
                echo "  ? $db\n";
            }
        }
    }
    
    // Buscar bases de datos de WordPress
    $wordpress_dbs = array_filter($databases, function($db) {
        return stripos($db, 'wordpress') !== false || stripos($db, 'wp') !== false;
    });
    
    echo "\n";
    if (!empty($wordpress_dbs)) {
        echo "📝 BASES DE DATOS DE WORDPRESS ENCONTRADAS:\n";
        foreach ($wordpress_dbs as $db) {
            echo "  ✓ $db\n";
        }
    } else {
        echo "⚠️  No se encontraron bases de datos de WordPress\n";
    }
    
    echo "\n";
    echo "🔧 CONFIGURACIÓN ACTUAL EN config.json:\n";
    echo "  - WordPress: wordpress_db\n";
    echo "  - StyloFitness: stylofitness_gym\n";
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
    echo "\n💡 Posibles soluciones:\n";
    echo "  1. Verificar que MySQL esté ejecutándose\n";
    echo "  2. Verificar credenciales de acceso\n";
    echo "  3. Verificar que el puerto 3306 esté disponible\n";
}
?>