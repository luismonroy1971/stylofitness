<?php
/**
 * Script de Verificación de Migración StyloFitness
 * 
 * Este script verifica que la migración se haya realizado correctamente
 * y genera reportes detallados de los datos migrados.
 * 
 * @author Sistema de Migración StyloFitness
 * @version 1.0
 */

class MigrationVerifier {
    private $stylofitness_db;
    private $wordpress_db;
    private $config;
    
    public function __construct() {
        $this->loadConfig();
        $this->connectDatabases();
    }
    
    private function loadConfig() {
        $config_file = __DIR__ . '/config.json';
        if (!file_exists($config_file)) {
            throw new Exception("Archivo de configuración no encontrado: $config_file");
        }
        
        $this->config = json_decode(file_get_contents($config_file), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error al leer el archivo de configuración: " . json_last_error_msg());
        }
    }
    
    private function connectDatabases() {
        try {
            // Conexión a StyloFitness
            $sf_config = $this->config['stylofitness_database'];
            $sf_dsn = "mysql:host={$sf_config['host']};dbname={$sf_config['database']};charset={$sf_config['charset']}";
            $this->stylofitness_db = new PDO($sf_dsn, $sf_config['username'], $sf_config['password']);
            $this->stylofitness_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Conexión a WordPress
            $wp_config = $this->config['wordpress_database'];
            $wp_dsn = "mysql:host={$wp_config['host']};dbname={$wp_config['database']};charset={$wp_config['charset']}";
            $this->wordpress_db = new PDO($wp_dsn, $wp_config['username'], $wp_config['password']);
            $this->wordpress_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "✅ Conexiones a bases de datos establecidas correctamente\n";
            
        } catch (PDOException $e) {
            throw new Exception("Error de conexión a base de datos: " . $e->getMessage());
        }
    }
    
    public function runFullVerification() {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "    REPORTE DE VERIFICACIÓN DE MIGRACIÓN\n";
        echo "    StyloFitness - WordPress to New Platform\n";
        echo str_repeat("=", 60) . "\n\n";
        
        $this->verifyCategories();
        $this->verifyProducts();
        $this->verifyProductCategoryMapping();
        $this->verifyPrices();
        $this->verifyStock();
        $this->generateSummaryReport();
        $this->generateRecommendations();
    }
    
    private function verifyCategories() {
        echo "📁 VERIFICACIÓN DE CATEGORÍAS\n";
        echo str_repeat("-", 40) . "\n";
        
        // Contar categorías en WordPress
        $wp_query = "
            SELECT COUNT(DISTINCT t.term_id) as total
            FROM wp_terms t
            INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'product_cat'
        ";
        $wp_stmt = $this->wordpress_db->prepare($wp_query);
        $wp_stmt->execute();
        $wp_categories = $wp_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Contar categorías en StyloFitness
        $sf_query = "SELECT COUNT(*) as total FROM product_categories";
        $sf_stmt = $this->stylofitness_db->prepare($sf_query);
        $sf_stmt->execute();
        $sf_categories = $sf_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo "Categorías en WordPress: $wp_categories\n";
        echo "Categorías en StyloFitness: $sf_categories\n";
        
        if ($sf_categories >= $wp_categories) {
            echo "✅ Migración de categorías: EXITOSA\n";
        } else {
            echo "⚠️  Migración de categorías: INCOMPLETA\n";
        }
        
        // Listar categorías migradas
        $sf_categories_query = "SELECT id, name, slug, is_active FROM product_categories ORDER BY id";
        $sf_categories_stmt = $this->stylofitness_db->prepare($sf_categories_query);
        $sf_categories_stmt->execute();
        $categories = $sf_categories_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nCategorías migradas:\n";
        foreach ($categories as $category) {
            $status = $category['is_active'] ? '🟢' : '🔴';
            echo "  $status ID: {$category['id']} - {$category['name']} ({$category['slug']})\n";
        }
        
        echo "\n";
    }
    
    private function verifyProducts() {
        echo "📦 VERIFICACIÓN DE PRODUCTOS\n";
        echo str_repeat("-", 40) . "\n";
        
        // Contar productos en WordPress
        $wp_query = "SELECT COUNT(*) as total FROM wp_posts WHERE post_type = 'product' AND post_status = 'publish'";
        $wp_stmt = $this->wordpress_db->prepare($wp_query);
        $wp_stmt->execute();
        $wp_products = $wp_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Contar productos en StyloFitness
        $sf_query = "SELECT COUNT(*) as total FROM products";
        $sf_stmt = $this->stylofitness_db->prepare($sf_query);
        $sf_stmt->execute();
        $sf_products = $sf_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo "Productos en WordPress: $wp_products\n";
        echo "Productos en StyloFitness: $sf_products\n";
        
        if ($sf_products >= $wp_products) {
            echo "✅ Migración de productos: EXITOSA\n";
        } else {
            echo "⚠️  Migración de productos: INCOMPLETA\n";
        }
        
        // Estadísticas adicionales
        $stats_query = "
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN is_active = 1 THEN 1 END) as activos,
                COUNT(CASE WHEN is_featured = 1 THEN 1 END) as destacados,
                COUNT(CASE WHEN price > 0 THEN 1 END) as con_precio,
                COUNT(CASE WHEN stock_quantity > 0 THEN 1 END) as con_stock,
                AVG(price) as precio_promedio,
                MAX(price) as precio_maximo,
                MIN(CASE WHEN price > 0 THEN price END) as precio_minimo
            FROM products
        ";
        $stats_stmt = $this->stylofitness_db->prepare($stats_query);
        $stats_stmt->execute();
        $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "\nEstadísticas de productos:\n";
        echo "  📊 Total: {$stats['total']}\n";
        echo "  🟢 Activos: {$stats['activos']}\n";
        echo "  ⭐ Destacados: {$stats['destacados']}\n";
        echo "  💰 Con precio: {$stats['con_precio']}\n";
        echo "  📦 Con stock: {$stats['con_stock']}\n";
        echo "  💵 Precio promedio: $" . number_format($stats['precio_promedio'], 2) . "\n";
        echo "  💵 Precio máximo: $" . number_format($stats['precio_maximo'], 2) . "\n";
        echo "  💵 Precio mínimo: $" . number_format($stats['precio_minimo'], 2) . "\n";
        
        echo "\n";
    }
    
    private function verifyProductCategoryMapping() {
        echo "🔗 VERIFICACIÓN DE MAPEO PRODUCTO-CATEGORÍA\n";
        echo str_repeat("-", 40) . "\n";
        
        $mapping_query = "
            SELECT 
                pc.name as categoria,
                COUNT(p.id) as total_productos,
                COUNT(CASE WHEN p.is_active = 1 THEN 1 END) as productos_activos
            FROM product_categories pc
            LEFT JOIN products p ON pc.id = p.category_id
            GROUP BY pc.id, pc.name
            ORDER BY total_productos DESC
        ";
        
        $mapping_stmt = $this->stylofitness_db->prepare($mapping_query);
        $mapping_stmt->execute();
        $mappings = $mapping_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Distribución de productos por categoría:\n";
        foreach ($mappings as $mapping) {
            echo "  📂 {$mapping['categoria']}: {$mapping['total_productos']} productos ({$mapping['productos_activos']} activos)\n";
        }
        
        // Verificar productos sin categoría
        $orphan_query = "SELECT COUNT(*) as total FROM products WHERE category_id IS NULL OR category_id = 0";
        $orphan_stmt = $this->stylofitness_db->prepare($orphan_query);
        $orphan_stmt->execute();
        $orphans = $orphan_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($orphans > 0) {
            echo "  ⚠️  Productos sin categoría: $orphans\n";
        } else {
            echo "  ✅ Todos los productos tienen categoría asignada\n";
        }
        
        echo "\n";
    }
    
    private function verifyPrices() {
        echo "💰 VERIFICACIÓN DE PRECIOS\n";
        echo str_repeat("-", 40) . "\n";
        
        // Productos sin precio
        $no_price_query = "SELECT COUNT(*) as total FROM products WHERE price = 0 OR price IS NULL";
        $no_price_stmt = $this->stylofitness_db->prepare($no_price_query);
        $no_price_stmt->execute();
        $no_price = $no_price_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Productos con precio de oferta
        $sale_price_query = "SELECT COUNT(*) as total FROM products WHERE sale_price > 0 AND sale_price < price";
        $sale_price_stmt = $this->stylofitness_db->prepare($sale_price_query);
        $sale_price_stmt->execute();
        $sale_price = $sale_price_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Rangos de precios
        $price_ranges_query = "
            SELECT 
                CASE 
                    WHEN price = 0 THEN 'Sin precio'
                    WHEN price <= 50 THEN '$0 - $50'
                    WHEN price <= 100 THEN '$51 - $100'
                    WHEN price <= 200 THEN '$101 - $200'
                    WHEN price <= 500 THEN '$201 - $500'
                    ELSE 'Más de $500'
                END as rango,
                COUNT(*) as cantidad
            FROM products
            GROUP BY 
                CASE 
                    WHEN price = 0 THEN 'Sin precio'
                    WHEN price <= 50 THEN '$0 - $50'
                    WHEN price <= 100 THEN '$51 - $100'
                    WHEN price <= 200 THEN '$101 - $200'
                    WHEN price <= 500 THEN '$201 - $500'
                    ELSE 'Más de $500'
                END
            ORDER BY cantidad DESC
        ";
        
        $price_ranges_stmt = $this->stylofitness_db->prepare($price_ranges_query);
        $price_ranges_stmt->execute();
        $price_ranges = $price_ranges_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Productos sin precio: $no_price\n";
        echo "Productos con precio de oferta: $sale_price\n";
        echo "\nDistribución por rangos de precio:\n";
        foreach ($price_ranges as $range) {
            echo "  💵 {$range['rango']}: {$range['cantidad']} productos\n";
        }
        
        echo "\n";
    }
    
    private function verifyStock() {
        echo "📦 VERIFICACIÓN DE INVENTARIO\n";
        echo str_repeat("-", 40) . "\n";
        
        // Productos sin stock
        $no_stock_query = "SELECT COUNT(*) as total FROM products WHERE stock_quantity = 0 OR stock_quantity IS NULL";
        $no_stock_stmt = $this->stylofitness_db->prepare($no_stock_query);
        $no_stock_stmt->execute();
        $no_stock = $no_stock_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Productos con bajo stock
        $low_stock_query = "SELECT COUNT(*) as total FROM products WHERE stock_quantity > 0 AND stock_quantity <= 10";
        $low_stock_stmt = $this->stylofitness_db->prepare($low_stock_query);
        $low_stock_stmt->execute();
        $low_stock = $low_stock_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Estadísticas de stock
        $stock_stats_query = "
            SELECT 
                AVG(stock_quantity) as promedio,
                MAX(stock_quantity) as maximo,
                MIN(stock_quantity) as minimo,
                SUM(stock_quantity) as total_inventario
            FROM products 
            WHERE stock_quantity > 0
        ";
        
        $stock_stats_stmt = $this->stylofitness_db->prepare($stock_stats_query);
        $stock_stats_stmt->execute();
        $stock_stats = $stock_stats_stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Productos sin stock: $no_stock\n";
        echo "Productos con bajo stock (≤10): $low_stock\n";
        echo "Stock promedio: " . number_format($stock_stats['promedio'], 2) . " unidades\n";
        echo "Stock máximo: {$stock_stats['maximo']} unidades\n";
        echo "Stock mínimo: {$stock_stats['minimo']} unidades\n";
        echo "Inventario total: {$stock_stats['total_inventario']} unidades\n";
        
        echo "\n";
    }
    
    private function generateSummaryReport() {
        echo "📋 RESUMEN GENERAL\n";
        echo str_repeat("-", 40) . "\n";
        
        $summary_query = "
            SELECT 
                (SELECT COUNT(*) FROM product_categories) as total_categorias,
                (SELECT COUNT(*) FROM products) as total_productos,
                (SELECT COUNT(*) FROM products WHERE is_active = 1) as productos_activos,
                (SELECT COUNT(*) FROM products WHERE is_featured = 1) as productos_destacados,
                (SELECT COUNT(*) FROM products WHERE price > 0) as productos_con_precio,
                (SELECT COUNT(*) FROM products WHERE stock_quantity > 0) as productos_con_stock
        ";
        
        $summary_stmt = $this->stylofitness_db->prepare($summary_query);
        $summary_stmt->execute();
        $summary = $summary_stmt->fetch(PDO::FETCH_ASSOC);
        
        $completeness = ($summary['productos_con_precio'] / max($summary['total_productos'], 1)) * 100;
        
        echo "✅ Migración completada exitosamente\n";
        echo "📊 Estadísticas finales:\n";
        echo "   • Categorías migradas: {$summary['total_categorias']}\n";
        echo "   • Productos migrados: {$summary['total_productos']}\n";
        echo "   • Productos activos: {$summary['productos_activos']}\n";
        echo "   • Productos destacados: {$summary['productos_destacados']}\n";
        echo "   • Productos con precio: {$summary['productos_con_precio']}\n";
        echo "   • Productos con stock: {$summary['productos_con_stock']}\n";
        echo "   • Completitud de datos: " . number_format($completeness, 1) . "%\n";
        
        echo "\n";
    }
    
    private function generateRecommendations() {
        echo "💡 RECOMENDACIONES\n";
        echo str_repeat("-", 40) . "\n";
        
        $recommendations = [];
        
        // Verificar productos sin precio
        $no_price_query = "SELECT COUNT(*) as total FROM products WHERE price = 0 OR price IS NULL";
        $no_price_stmt = $this->stylofitness_db->prepare($no_price_query);
        $no_price_stmt->execute();
        $no_price = $no_price_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($no_price > 0) {
            $recommendations[] = "⚠️  Revisar y asignar precios a $no_price productos";
        }
        
        // Verificar productos sin stock
        $no_stock_query = "SELECT COUNT(*) as total FROM products WHERE stock_quantity = 0 OR stock_quantity IS NULL";
        $no_stock_stmt = $this->stylofitness_db->prepare($no_stock_query);
        $no_stock_stmt->execute();
        $no_stock = $no_stock_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($no_stock > 0) {
            $recommendations[] = "📦 Actualizar inventario de $no_stock productos";
        }
        
        // Verificar productos sin categoría
        $orphan_query = "SELECT COUNT(*) as total FROM products WHERE category_id IS NULL OR category_id = 0";
        $orphan_stmt = $this->stylofitness_db->prepare($orphan_query);
        $orphan_stmt->execute();
        $orphans = $orphan_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($orphans > 0) {
            $recommendations[] = "📂 Asignar categorías a $orphans productos";
        }
        
        // Verificar productos inactivos
        $inactive_query = "SELECT COUNT(*) as total FROM products WHERE is_active = 0";
        $inactive_stmt = $this->stylofitness_db->prepare($inactive_query);
        $inactive_stmt->execute();
        $inactive = $inactive_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($inactive > 0) {
            $recommendations[] = "🔄 Revisar estado de $inactive productos inactivos";
        }
        
        if (empty($recommendations)) {
            echo "🎉 ¡Excelente! No se encontraron problemas importantes.\n";
            echo "✅ La migración se completó exitosamente sin issues críticos.\n";
        } else {
            echo "Acciones recomendadas para optimizar la migración:\n";
            foreach ($recommendations as $recommendation) {
                echo "  $recommendation\n";
            }
        }
        
        echo "\n📝 Próximos pasos sugeridos:\n";
        echo "  1. Revisar y optimizar descripciones de productos\n";
        echo "  2. Configurar imágenes de productos\n";
        echo "  3. Establecer variaciones de productos si es necesario\n";
        echo "  4. Configurar métodos de envío y pagos\n";
        echo "  5. Realizar pruebas de funcionalidad del catálogo\n";
        
        echo "\n";
    }
    
    public function exportMigrationReport() {
        $report_file = __DIR__ . '/migration_report_' . date('Y-m-d_H-i-s') . '.txt';
        
        ob_start();
        $this->runFullVerification();
        $report_content = ob_get_clean();
        
        file_put_contents($report_file, $report_content);
        echo "📄 Reporte exportado a: $report_file\n";
        
        return $report_file;
    }
}

// Ejecutar verificación
try {
    echo "Iniciando verificación de migración...\n";
    
    $verifier = new MigrationVerifier();
    
    // Verificar si se solicita exportar reporte
    if (isset($argv[1]) && $argv[1] === '--export') {
        $verifier->exportMigrationReport();
    } else {
        $verifier->runFullVerification();
    }
    
    echo "\n🎯 Verificación completada exitosamente.\n";
    echo "\nPara exportar este reporte a un archivo, ejecuta:\n";
    echo "php verification_script.php --export\n";
    
} catch (Exception $e) {
    echo "❌ Error durante la verificación: " . $e->getMessage() . "\n";
    exit(1);
}
?>