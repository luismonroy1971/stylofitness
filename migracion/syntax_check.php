<?php
/**
 * Script de verificación de sintaxis
 * Verifica que todos los archivos PHP tengan sintaxis correcta
 */

echo "🔍 VERIFICANDO SINTAXIS DE ARCHIVOS PHP\n";
echo str_repeat("-", 50) . "\n";

$files_to_check = [
    'migration_script.php',
    'image_migration_script.php',
    'verification_script.php',
    'utilities.php'
];

foreach ($files_to_check as $file) {
    $file_path = __DIR__ . '/' . $file;
    
    if (!file_exists($file_path)) {
        echo "⚠️  Archivo no encontrado: $file\n";
        continue;
    }
    
    // Verificar sintaxis usando php -l
    $output = [];
    $return_code = 0;
    exec("php -l \"$file_path\" 2>&1", $output, $return_code);
    
    if ($return_code === 0) {
        echo "✅ $file - Sintaxis correcta\n";
    } else {
        echo "❌ $file - Error de sintaxis:\n";
        foreach ($output as $line) {
            echo "   $line\n";
        }
    }
}

echo "\n🔍 VERIFICANDO CLASES Y MÉTODOS\n";
echo str_repeat("-", 50) . "\n";

// Verificar que las clases se pueden cargar
try {
    // Verificar ImageMigration
    if (file_exists(__DIR__ . '/image_migration_script.php')) {
        $content = file_get_contents(__DIR__ . '/image_migration_script.php');
        
        // Verificar que los métodos existen
        $required_methods = [
            'migrateProductImages',
            'migrateCategoryImages', 
            'generateThumbnails',
            'migrateAllImages'
        ];
        
        foreach ($required_methods as $method) {
            if (strpos($content, "function $method") !== false) {
                echo "✅ Método $method encontrado\n";
            } else {
                echo "❌ Método $method NO encontrado\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error al verificar clases: " . $e->getMessage() . "\n";
}

echo "\n📋 VERIFICACIÓN COMPLETADA\n";
?>