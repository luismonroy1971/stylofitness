<?php
/**
 * Utilidades para Migración StyloFitness
 * 
 * Script con funciones auxiliares para mantenimiento,
 * limpieza de datos y tareas post-migración.
 * 
 * @author Sistema de Migración StyloFitness
 * @version 1.0
 */

class MigrationUtilities {
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
            
            // Conexión a WordPress (opcional)
            if (isset($this->config['wordpress_database'])) {
                $wp_config = $this->config['wordpress_database'];
                $wp_dsn = "mysql:host={$wp_config['host']};dbname={$wp_config['database']};charset={$wp_config['charset']}";
                $this->wordpress_db = new PDO($wp_dsn, $wp_config['username'], $wp_config['password']);
                $this->wordpress_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
            echo "✅ Conexiones establecidas correctamente\n";
            
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }
    
    /**
     * Crear respaldo de la base de datos StyloFitness
     */
    public function createBackup() {
        echo "📦 Creando respaldo de la base de datos...\n";
        
        $config = $this->config['stylofitness_database'];
        $backup_file = __DIR__ . '/backup_stylofitness_' . date('Y-m-d_H-i-s') . '.sql';
        
        $command = sprintf(
            'mysqldump -h%s -u%s -p%s %s > "%s"',
            $config['host'],
            $config['username'],
            $config['password'],
            $config['database'],
            $backup_file
        );
        
        // En Windows, usar exec con manejo de errores
        $output = [];
        $return_code = 0;
        exec($command, $output, $return_code);
        
        if ($return_code === 0 && file_exists($backup_file)) {
            echo "✅ Respaldo creado exitosamente: $backup_file\n";
            echo "📊 Tamaño del archivo: " . $this->formatBytes(filesize($backup_file)) . "\n";
            return $backup_file;
        } else {
            throw new Exception("Error al crear el respaldo. Código de retorno: $return_code");
        }
    }
    
    /**
     * Limpiar datos duplicados
     */
    public function cleanDuplicates() {
        echo "🧹 Limpiando datos duplicados...\n";
        
        // Limpiar categorías duplicadas
        $category_query = "
            DELETE pc1 FROM product_categories pc1
            INNER JOIN product_categories pc2 
            WHERE pc1.id > pc2.id 
            AND pc1.name = pc2.name
        ";
        
        $category_stmt = $this->stylofitness_db->prepare($category_query);
        $category_stmt->execute();
        $deleted_categories = $category_stmt->rowCount();
        
        // Limpiar productos duplicados por SKU
        $product_query = "
            DELETE p1 FROM products p1
            INNER JOIN products p2 
            WHERE p1.id > p2.id 
            AND p1.sku = p2.sku
            AND p1.sku != ''
        ";
        
        $product_stmt = $this->stylofitness_db->prepare($product_query);
        $product_stmt->execute();
        $deleted_products = $product_stmt->rowCount();
        
        echo "🗑️  Categorías duplicadas eliminadas: $deleted_categories\n";
        echo "🗑️  Productos duplicados eliminados: $deleted_products\n";
        
        if ($deleted_categories > 0 || $deleted_products > 0) {
            echo "✅ Limpieza de duplicados completada\n";
        } else {
            echo "ℹ️  No se encontraron duplicados\n";
        }
    }
    
    /**
     * Optimizar descripciones de productos
     */
    public function optimizeDescriptions() {
        echo "📝 Optimizando descripciones de productos...\n";
        
        // Obtener productos con descripciones que necesitan limpieza
        $query = "
            SELECT id, name, description, short_description 
            FROM products 
            WHERE description LIKE '%[%' 
               OR description LIKE '%<%' 
               OR short_description IS NULL 
               OR short_description = ''
        ";
        
        $stmt = $this->stylofitness_db->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $updated_count = 0;
        
        foreach ($products as $product) {
            $cleaned_description = $this->cleanHtml($product['description']);
            $short_description = $product['short_description'];
            
            // Generar descripción corta si no existe
            if (empty($short_description)) {
                $short_description = $this->generateShortDescription($cleaned_description, $product['name']);
            }
            
            // Actualizar producto
            $update_query = "
                UPDATE products 
                SET description = :description, 
                    short_description = :short_description,
                    updated_at = NOW()
                WHERE id = :id
            ";
            
            $update_stmt = $this->stylofitness_db->prepare($update_query);
            $update_stmt->execute([
                ':description' => $cleaned_description,
                ':short_description' => $short_description,
                ':id' => $product['id']
            ]);
            
            $updated_count++;
        }
        
        echo "📝 Descripciones optimizadas: $updated_count productos\n";
    }
    
    /**
     * Generar SKUs faltantes
     */
    public function generateMissingSKUs() {
        echo "🏷️  Generando SKUs faltantes...\n";
        
        $query = "SELECT id, name FROM products WHERE sku IS NULL OR sku = ''";
        $stmt = $this->stylofitness_db->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $generated_count = 0;
        
        foreach ($products as $product) {
            $sku = $this->generateSKU($product['name'], $product['id']);
            
            $update_query = "UPDATE products SET sku = :sku WHERE id = :id";
            $update_stmt = $this->stylofitness_db->prepare($update_query);
            $update_stmt->execute([
                ':sku' => $sku,
                ':id' => $product['id']
            ]);
            
            $generated_count++;
        }
        
        echo "🏷️  SKUs generados: $generated_count\n";
    }
    
    /**
     * Actualizar precios en lote
     */
    public function updatePricesInBatch($percentage_increase = 0) {
        echo "💰 Actualizando precios...\n";
        
        if ($percentage_increase > 0) {
            $query = "
                UPDATE products 
                SET price = ROUND(price * (1 + :percentage / 100), 2),
                    updated_at = NOW()
                WHERE price > 0
            ";
            
            $stmt = $this->stylofitness_db->prepare($query);
            $stmt->execute([':percentage' => $percentage_increase]);
            
            $updated_count = $stmt->rowCount();
            echo "💰 Precios actualizados con incremento del {$percentage_increase}%: $updated_count productos\n";
        }
        
        // Actualizar productos sin precio con precio base
        $default_price_query = "
            UPDATE products 
            SET price = 50.00,
                updated_at = NOW()
            WHERE price = 0 OR price IS NULL
        ";
        
        $default_stmt = $this->stylofitness_db->prepare($default_price_query);
        $default_stmt->execute();
        $default_updated = $default_stmt->rowCount();
        
        if ($default_updated > 0) {
            echo "💰 Productos con precio base asignado ($50.00): $default_updated\n";
        }
    }
    
    /**
     * Actualizar stock en lote
     */
    public function updateStockInBatch($default_stock = 100) {
        echo "📦 Actualizando inventario...\n";
        
        $query = "
            UPDATE products 
            SET stock_quantity = :default_stock,
                updated_at = NOW()
            WHERE stock_quantity = 0 OR stock_quantity IS NULL
        ";
        
        $stmt = $this->stylofitness_db->prepare($query);
        $stmt->execute([':default_stock' => $default_stock]);
        
        $updated_count = $stmt->rowCount();
        echo "📦 Productos con stock actualizado ($default_stock unidades): $updated_count\n";
    }
    
    /**
     * Activar todos los productos
     */
    public function activateAllProducts() {
        echo "🔄 Activando todos los productos...\n";
        
        $query = "
            UPDATE products 
            SET is_active = 1,
                updated_at = NOW()
            WHERE is_active = 0
        ";
        
        $stmt = $this->stylofitness_db->prepare($query);
        $stmt->execute();
        
        $activated_count = $stmt->rowCount();
        echo "🔄 Productos activados: $activated_count\n";
    }
    
    /**
     * Generar reporte de productos por categoría
     */
    public function generateCategoryReport() {
        echo "📊 Generando reporte por categorías...\n";
        
        $query = "
            SELECT 
                pc.name as categoria,
                COUNT(p.id) as total_productos,
                COUNT(CASE WHEN p.is_active = 1 THEN 1 END) as productos_activos,
                COUNT(CASE WHEN p.is_featured = 1 THEN 1 END) as productos_destacados,
                AVG(p.price) as precio_promedio,
                SUM(p.stock_quantity) as stock_total
            FROM product_categories pc
            LEFT JOIN products p ON pc.id = p.category_id
            GROUP BY pc.id, pc.name
            ORDER BY total_productos DESC
        ";
        
        $stmt = $this->stylofitness_db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $report_file = __DIR__ . '/category_report_' . date('Y-m-d_H-i-s') . '.csv';
        $fp = fopen($report_file, 'w');
        
        // Encabezados CSV
        fputcsv($fp, [
            'Categoría',
            'Total Productos',
            'Productos Activos',
            'Productos Destacados',
            'Precio Promedio',
            'Stock Total'
        ]);
        
        // Datos
        foreach ($categories as $category) {
            fputcsv($fp, [
                $category['categoria'],
                $category['total_productos'],
                $category['productos_activos'],
                $category['productos_destacados'],
                number_format($category['precio_promedio'], 2),
                $category['stock_total']
            ]);
        }
        
        fclose($fp);
        
        echo "📊 Reporte generado: $report_file\n";
        return $report_file;
    }
    
    /**
     * Limpiar HTML y shortcodes
     */
    private function cleanHtml($content) {
        // Remover shortcodes de WordPress
        $content = preg_replace('/\[.*?\]/', '', $content);
        
        // Remover tags HTML básicos pero mantener saltos de línea
        $content = strip_tags($content, '<br><p>');
        
        // Convertir entidades HTML
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        
        // Limpiar espacios múltiples
        $content = preg_replace('/\s+/', ' ', $content);
        
        return trim($content);
    }
    
    /**
     * Generar descripción corta
     */
    private function generateShortDescription($description, $name) {
        if (empty($description)) {
            return "Producto de alta calidad: " . $name;
        }
        
        // Tomar las primeras 150 caracteres
        $short = substr($description, 0, 150);
        
        // Cortar en la última palabra completa
        $last_space = strrpos($short, ' ');
        if ($last_space !== false) {
            $short = substr($short, 0, $last_space);
        }
        
        return $short . '...';
    }
    
    /**
     * Generar SKU único
     */
    private function generateSKU($name, $id) {
        // Extraer palabras clave del nombre
        $words = explode(' ', strtoupper($name));
        $sku_parts = [];
        
        foreach ($words as $word) {
            if (strlen($word) >= 3 && !in_array($word, ['THE', 'AND', 'FOR', 'WITH'])) {
                $sku_parts[] = substr($word, 0, 3);
                if (count($sku_parts) >= 3) break;
            }
        }
        
        $sku = implode('', $sku_parts) . str_pad($id, 3, '0', STR_PAD_LEFT);
        
        return substr($sku, 0, 20); // Limitar a 20 caracteres
    }
    
    /**
     * Formatear bytes a formato legible
     */
    private function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Ejecutar todas las optimizaciones
     */
    public function runAllOptimizations() {
        echo "🚀 Ejecutando todas las optimizaciones...\n\n";
        
        $this->cleanDuplicates();
        echo "\n";
        
        $this->optimizeDescriptions();
        echo "\n";
        
        $this->generateMissingSKUs();
        echo "\n";
        
        $this->updatePricesInBatch(0); // Sin incremento
        echo "\n";
        
        $this->updateStockInBatch(100);
        echo "\n";
        
        $this->activateAllProducts();
        echo "\n";
        
        echo "✅ Todas las optimizaciones completadas\n";
    }
    
    /**
     * Mostrar menú de opciones
     */
    public function showMenu() {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "    UTILIDADES DE MIGRACIÓN STYLOFITNESS\n";
        echo str_repeat("=", 50) . "\n\n";
        
        echo "Opciones disponibles:\n";
        echo "1. Crear respaldo de base de datos\n";
        echo "2. Limpiar datos duplicados\n";
        echo "3. Optimizar descripciones\n";
        echo "4. Generar SKUs faltantes\n";
        echo "5. Actualizar precios (con incremento)\n";
        echo "6. Actualizar stock por defecto\n";
        echo "7. Activar todos los productos\n";
        echo "8. Generar reporte por categorías\n";
        echo "9. Ejecutar todas las optimizaciones\n";
        echo "0. Salir\n";
        echo "\nSeleccione una opción: ";
    }
}

// Ejecutar utilidades
if (php_sapi_name() === 'cli') {
    try {
        $utilities = new MigrationUtilities();
        
        // Si se pasa un argumento, ejecutar directamente
        if (isset($argv[1])) {
            switch ($argv[1]) {
                case 'backup':
                    $utilities->createBackup();
                    break;
                case 'clean':
                    $utilities->cleanDuplicates();
                    break;
                case 'optimize':
                    $utilities->runAllOptimizations();
                    break;
                case 'report':
                    $utilities->generateCategoryReport();
                    break;
                default:
                    echo "Argumento no reconocido. Use: backup, clean, optimize, report\n";
            }
        } else {
            // Menú interactivo
            while (true) {
                $utilities->showMenu();
                $option = trim(fgets(STDIN));
                
                switch ($option) {
                    case '1':
                        $utilities->createBackup();
                        break;
                    case '2':
                        $utilities->cleanDuplicates();
                        break;
                    case '3':
                        $utilities->optimizeDescriptions();
                        break;
                    case '4':
                        $utilities->generateMissingSKUs();
                        break;
                    case '5':
                        echo "Ingrese el porcentaje de incremento (ej: 10 para 10%): ";
                        $percentage = (float)trim(fgets(STDIN));
                        $utilities->updatePricesInBatch($percentage);
                        break;
                    case '6':
                        echo "Ingrese la cantidad de stock por defecto: ";
                        $stock = (int)trim(fgets(STDIN));
                        $utilities->updateStockInBatch($stock);
                        break;
                    case '7':
                        $utilities->activateAllProducts();
                        break;
                    case '8':
                        $utilities->generateCategoryReport();
                        break;
                    case '9':
                        $utilities->runAllOptimizations();
                        break;
                    case '0':
                        echo "¡Hasta luego!\n";
                        exit(0);
                    default:
                        echo "Opción no válida. Intente de nuevo.\n";
                }
                
                echo "\nPresione Enter para continuar...";
                fgets(STDIN);
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
?>