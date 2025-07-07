<?php
// Script completo para limpiar todo tipo de caché

echo "=== LIMPIEZA COMPLETA DE CACHÉ - STYLOFITNESS ===\n";
echo "Iniciando limpieza completa del sistema...\n\n";

// 1. Limpiar directorio de caché
echo "1. Limpiando directorio storage/cache...\n";
$cacheDir = __DIR__ . '/storage/cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    $cleaned = 0;
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitkeep') {
            if (unlink($file)) {
                echo "   ✓ Eliminado: " . basename($file) . "\n";
                $cleaned++;
            }
        }
    }
    echo "   Total archivos eliminados: $cleaned\n";
} else {
    echo "   ⚠️  Directorio de caché no existe\n";
}

// 2. Limpiar sesiones
echo "\n2. Limpiando sesiones...\n";
$sessionDir = __DIR__ . '/storage/sessions';
if (is_dir($sessionDir)) {
    $sessionFiles = glob($sessionDir . '/sess_*');
    $sessionsCleaned = 0;
    foreach ($sessionFiles as $file) {
        if (unlink($file)) {
            echo "   ✓ Sesión eliminada: " . basename($file) . "\n";
            $sessionsCleaned++;
        }
    }
    echo "   Total sesiones eliminadas: $sessionsCleaned\n";
} else {
    echo "   ⚠️  Directorio de sesiones no existe\n";
}

// 3. Limpiar archivos temporales del sistema
echo "\n3. Limpiando archivos temporales...\n";
$tempDirs = [
    sys_get_temp_dir(),
    __DIR__ . '/tmp',
    __DIR__ . '/temp',
    __DIR__ . '/storage/tmp'
];

foreach ($tempDirs as $tempDir) {
    if (is_dir($tempDir)) {
        $tempFiles = glob($tempDir . '/stylofitness_*');
        $tempFiles = array_merge($tempFiles, glob($tempDir . '/php*'));
        $tempCleaned = 0;
        
        foreach ($tempFiles as $file) {
            if (is_file($file) && (time() - filemtime($file)) > 3600) { // Más de 1 hora
                if (unlink($file)) {
                    echo "   ✓ Temp eliminado: " . basename($file) . "\n";
                    $tempCleaned++;
                }
            }
        }
        
        if ($tempCleaned > 0) {
            echo "   Archivos temporales eliminados en $tempDir: $tempCleaned\n";
        }
    }
}

// 4. Limpiar caché de OPcache si está habilitado
echo "\n4. Limpiando OPcache...\n";
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "   ✓ OPcache limpiado exitosamente\n";
    } else {
        echo "   ⚠️  No se pudo limpiar OPcache\n";
    }
} else {
    echo "   ℹ️  OPcache no está habilitado\n";
}

// 5. Limpiar caché de APCu si está disponible
echo "\n5. Limpiando APCu...\n";
if (function_exists('apcu_clear_cache')) {
    if (apcu_clear_cache()) {
        echo "   ✓ APCu limpiado exitosamente\n";
    } else {
        echo "   ⚠️  No se pudo limpiar APCu\n";
    }
} else {
    echo "   ℹ️  APCu no está disponible\n";
}

// 6. Forzar limpieza de headers para el navegador
echo "\n6. Configurando headers anti-caché...\n";
if (!headers_sent()) {
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    echo "   ✓ Headers anti-caché configurados\n";
} else {
    echo "   ⚠️  Headers ya enviados\n";
}

// 7. Crear archivo de timestamp para forzar recarga
echo "\n7. Creando timestamp de actualización...\n";
$timestampFile = __DIR__ . '/public/cache_timestamp.txt';
if (file_put_contents($timestampFile, time())) {
    echo "   ✓ Timestamp creado: " . date('Y-m-d H:i:s') . "\n";
} else {
    echo "   ⚠️  No se pudo crear timestamp\n";
}

// 8. Verificar productos destacados actuales
echo "\n8. Verificando productos destacados...\n";
try {
    require_once 'app/Config/Database.php';
    $db = StyleFitness\Config\Database::getInstance();
    
    $featuredCount = $db->fetchColumn(
        "SELECT COUNT(*) FROM products WHERE is_featured = 1 AND is_active = 1"
    );
    
    echo "   ✓ Productos destacados activos: $featuredCount\n";
    
    if ($featuredCount > 0) {
        $featured = $db->fetchAll(
            "SELECT id, name FROM products WHERE is_featured = 1 AND is_active = 1 LIMIT 5"
        );
        
        echo "   Productos destacados encontrados:\n";
        foreach ($featured as $product) {
            echo "     • {$product['name']} (ID: {$product['id']})\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ Error verificando productos: " . $e->getMessage() . "\n";
}

echo "\n=== LIMPIEZA COMPLETADA ===\n";
echo "\n📋 INSTRUCCIONES PARA EL USUARIO:\n";
echo "1. Abre tu navegador y presiona Ctrl+Shift+Delete\n";
echo "2. Selecciona 'Imágenes y archivos en caché'\n";
echo "3. Haz clic en 'Eliminar datos'\n";
echo "4. Recarga la página con Ctrl+F5 (recarga forzada)\n";
echo "5. Si usas Chrome, también puedes:\n";
echo "   - Abrir DevTools (F12)\n";
echo "   - Hacer clic derecho en el botón de recarga\n";
echo "   - Seleccionar 'Vaciar caché y recargar de forma forzada'\n";
echo "\n🔗 Para verificar que todo funciona:\n";
echo "Visita: http://localhost:8000/force_refresh_featured_products.php\n";
echo "\n⏰ Timestamp de limpieza: " . date('Y-m-d H:i:s') . "\n";
?>