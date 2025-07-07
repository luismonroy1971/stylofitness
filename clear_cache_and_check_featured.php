<?php
// Script para limpiar caché y verificar productos destacados

// Definir constantes necesarias
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

require_once 'app/Config/Database.php';
require_once 'app/Models/Product.php';

echo "=== LIMPIEZA DE CACHÉ Y VERIFICACIÓN DE PRODUCTOS DESTACADOS ===\n";

try {
    // 1. Limpiar caché del sistema manualmente
    echo "\n1. Limpiando archivos de caché...\n";
    
    // 2. Limpiar archivos de caché manualmente
    echo "\n2. Limpiando archivos de caché manualmente...\n";
    $cacheDir = ROOT_PATH . '/storage/cache';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '/*.cache');
        foreach ($files as $file) {
            if (unlink($file)) {
                echo "✓ Eliminado: " . basename($file) . "\n";
            }
        }
        echo "Total archivos de caché eliminados: " . count($files) . "\n";
    }
    
    // 3. Limpiar sesiones
    echo "\n3. Limpiando sesiones...\n";
    $sessionDir = ROOT_PATH . '/storage/sessions';
    if (is_dir($sessionDir)) {
        $sessionFiles = glob($sessionDir . '/sess_*');
        foreach ($sessionFiles as $file) {
            if (unlink($file)) {
                echo "✓ Sesión eliminada: " . basename($file) . "\n";
            }
        }
        echo "Total sesiones eliminadas: " . count($sessionFiles) . "\n";
    }
    
    // 4. Verificar productos destacados en la base de datos
    echo "\n4. Verificando productos destacados en la base de datos...\n";
    $product = new StyleFitness\Models\Product();
    
    // Obtener productos destacados
    $featuredProducts = $product->getFeaturedProducts();
    
    echo "Total de productos destacados encontrados: " . count($featuredProducts) . "\n\n";
    
    if (count($featuredProducts) > 0) {
        echo "Lista de productos destacados actuales:\n";
        echo str_repeat("-", 100) . "\n";
        
        foreach ($featuredProducts as $prod) {
            echo "ID: {$prod['id']}\n";
            echo "Nombre: {$prod['name']}\n";
            echo "Slug: {$prod['slug']}\n";
            echo "Precio: {$prod['price']}\n";
            echo "Precio oferta: " . ($prod['sale_price'] ?? 'N/A') . "\n";
            echo "Stock: " . ($prod['stock_quantity'] ?? 'N/A') . "\n";
            echo "Marca: " . ($prod['brand'] ?? 'N/A') . "\n";
            echo "Estado: " . ($prod['is_active'] ? 'Activo' : 'Inactivo') . "\n";
            echo "Destacado: " . ($prod['is_featured'] ? 'Sí' : 'No') . "\n";
            echo str_repeat("-", 100) . "\n";
        }
    } else {
        echo "⚠️  No se encontraron productos destacados en la base de datos.\n";
        echo "Esto explica por qué podrían estar apareciendo productos antiguos.\n";
    }
    
    // 5. Verificar productos activos con is_featured = 1
    echo "\n5. Verificación directa en la base de datos...\n";
    $db = StyleFitness\Config\Database::getInstance();
    $directQuery = $db->fetchAll(
        "SELECT id, name, slug, price, sale_price, stock_quantity, brand, is_active, is_featured 
         FROM products 
         WHERE is_featured = 1 AND is_active = 1 
         ORDER BY created_at DESC"
    );
    
    echo "Productos con is_featured = 1 y is_active = 1: " . count($directQuery) . "\n";
    
    if (count($directQuery) > 0) {
        foreach ($directQuery as $prod) {
            echo "- {$prod['name']} (ID: {$prod['id']})\n";
        }
    }
    
    // 6. Enviar headers para limpiar caché del navegador
    echo "\n6. Configurando headers para limpiar caché del navegador...\n";
    if (!headers_sent()) {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo "✓ Headers de no-caché enviados\n";
    } else {
        echo "⚠️  Headers ya enviados, no se pueden modificar\n";
    }
    
    echo "\n=== PROCESO COMPLETADO ===\n";
    echo "\nRecomendaciones:\n";
    echo "1. Actualiza la página en el navegador con Ctrl+F5 (forzar recarga)\n";
    echo "2. Limpia la caché del navegador manualmente\n";
    echo "3. Si el problema persiste, verifica que los productos destacados estén correctamente marcados en la base de datos\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}
?>