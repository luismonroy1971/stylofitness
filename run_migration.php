<?php

// Script para ejecutar la migración de las tablas de landing page
require_once __DIR__ . '/app/Config/Database.php';

use StyleFitness\Config\Database;

try {
    $db = Database::getInstance();
    
    // Leer el archivo SQL
    $sqlFile = __DIR__ . '/database/landing_sections_migration.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("Archivo SQL no encontrado: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    if ($sql === false) {
        throw new Exception("Error al leer el archivo SQL");
    }
    
    echo "Ejecutando migración de landing page...\n";
    
    // Dividir el SQL en declaraciones individuales
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            echo "Ejecutando: " . substr($statement, 0, 50) . "...\n";
            
            try {
                $db->query($statement);
                echo "✓ Ejecutado correctamente\n";
            } catch (Exception $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
                // Continuar con la siguiente declaración
            }
        }
    }
    
    echo "\n=== Migración completada ===\n";
    
    // Verificar que las tablas se crearon correctamente
    echo "\nVerificando tablas creadas:\n";
    
    $tables = [
        'special_offers',
        'why_choose_us', 
        'featured_products_config',
        'testimonials',
        'landing_page_config'
    ];
    
    foreach ($tables as $table) {
        try {
            $result = $db->fetch("SHOW TABLES LIKE '$table'");
            if ($result) {
                echo "✓ Tabla '$table' creada correctamente\n";
                
                // Contar registros
                $count = $db->fetch("SELECT COUNT(*) as count FROM $table");
                echo "  - Registros: " . ($count['count'] ?? 0) . "\n";
            } else {
                echo "✗ Tabla '$table' no encontrada\n";
            }
        } catch (Exception $e) {
            echo "✗ Error verificando tabla '$table': " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== Verificación completada ===\n";
    
} catch (Exception $e) {
    echo "Error fatal: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nMigración ejecutada exitosamente.\n";