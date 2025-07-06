<?php
/**
 * Script de Migración de Productos y Categorías
 * De WordPress/WooCommerce a StyloFitness Database
 * 
 * Este script migra:
 * - Categorías de productos (wp_terms -> product_categories)
 * - Productos (wp_posts + wp_postmeta -> products)
 * - Metadatos de productos (precios, stock, SKU, etc.)
 */

class StyloFitnessMigration {
    private $wpdb;
    private $sfdb;
    private $categoryMapping = [];
    
    public function __construct($wpConfig, $sfConfig) {
        // Conexión a WordPress Database
        $this->wpdb = new PDO(
            "mysql:host={$wpConfig['host']};dbname={$wpConfig['database']};charset=utf8mb4",
            $wpConfig['username'],
            $wpConfig['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Conexión a StyloFitness Database
        $this->sfdb = new PDO(
            "mysql:host={$sfConfig['host']};dbname={$sfConfig['database']};charset=utf8mb4",
            $sfConfig['username'],
            $sfConfig['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
    
    /**
     * Ejecuta la migración completa
     */
    public function migrate() {
        echo "=== INICIANDO MIGRACIÓN DE STYLOFITNESS ===\n";
        
        try {
            $this->migrateCategories();
            $this->migrateProducts();
            echo "\n=== MIGRACIÓN COMPLETADA EXITOSAMENTE ===\n";
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    /**
     * Migra las categorías de productos
     */
    private function migrateCategories() {
        echo "\n--- Migrando Categorías ---\n";
        
        // Obtener categorías de productos de WordPress
        $sql = "
            SELECT DISTINCT t.term_id, t.name, t.slug
            FROM wp_terms t
            INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'product_cat'
            AND t.term_id IN (18, 19, 20, 21, 22, 23, 24, 25, 26, 27)
            ORDER BY t.term_id
        ";
        
        $categories = $this->wpdb->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($categories)) {
            // Si no hay categorías con taxonomy, usar las categorías principales identificadas
            $categoryData = [
                ['term_id' => 18, 'name' => 'PROTEÍNAS WHEY', 'slug' => 'whey'],
                ['term_id' => 19, 'name' => 'GANADORES DE MASA', 'slug' => 'ganadores-de-masa'],
                ['term_id' => 20, 'name' => 'PROTEINAS ISOLATADAS', 'slug' => 'proteinas-isolatadas'],
                ['term_id' => 21, 'name' => 'PRE ENTRENOS Y ÓXIDO NITRICO', 'slug' => 'pre-entrenos'],
                ['term_id' => 22, 'name' => 'PRECURSOR DE LA TESTO', 'slug' => 'testo'],
                ['term_id' => 23, 'name' => 'MULTIVITAMINICO Colágenos OMEGAS', 'slug' => 'multivitaminico'],
                ['term_id' => 24, 'name' => 'QUEMADORES DE GRASA', 'slug' => 'quemadores'],
                ['term_id' => 25, 'name' => 'AMINOÁCIDOS Y BCAA', 'slug' => 'aminoacidos-y-bcaa'],
                ['term_id' => 26, 'name' => 'CREATINAS Y GLUTAMINAS', 'slug' => 'creatinas-y-glutaminas'],
                ['term_id' => 27, 'name' => 'PROTECTOR HEPÁTICO', 'slug' => 'protector-hepatico']
            ];
            $categories = $categoryData;
        }
        
        $insertSql = "
            INSERT INTO product_categories (name, slug, description, is_active)
            VALUES (?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            description = VALUES(description)
        ";
        
        $stmt = $this->sfdb->prepare($insertSql);
        
        foreach ($categories as $category) {
            $description = $this->generateCategoryDescription($category['name']);
            
            $stmt->execute([
                $category['name'],
                $category['slug'],
                $description
            ]);
            
            // Obtener el ID de la categoría insertada
            $newCategoryId = $this->sfdb->lastInsertId();
            if (!$newCategoryId) {
                // Si no hay lastInsertId, buscar por slug
                $findSql = "SELECT id FROM product_categories WHERE slug = ?";
                $findStmt = $this->sfdb->prepare($findSql);
                $findStmt->execute([$category['slug']]);
                $newCategoryId = $findStmt->fetchColumn();
            }
            
            $this->categoryMapping[$category['term_id']] = $newCategoryId;
            
            echo "✓ Categoría migrada: {$category['name']} (ID: {$newCategoryId})\n";
        }
        
        echo "Total categorías migradas: " . count($categories) . "\n";
    }
    
    /**
     * Migra los productos
     */
    private function migrateProducts() {
        echo "\n--- Migrando Productos ---\n";
        
        // Obtener productos de WordPress
        $sql = "
            SELECT p.ID, p.post_title, p.post_content, p.post_excerpt, p.post_name as slug,
                   p.post_date, p.post_status
            FROM wp_posts p
            WHERE p.post_type = 'product'
            AND p.post_status IN ('publish', 'draft')
            ORDER BY p.ID
        ";
        
        $products = $this->wpdb->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        $insertSql = "
            INSERT INTO products (
                category_id, name, slug, description, short_description, sku, price, sale_price,
                stock_quantity, weight, images, brand, is_featured, is_active, views_count,
                sales_count
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0
            )
            ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            description = VALUES(description),
            price = VALUES(price)
        ";
        
        $stmt = $this->sfdb->prepare($insertSql);
        
        foreach ($products as $product) {
            // Obtener metadatos del producto
            $metadata = $this->getProductMetadata($product['ID']);
            
            // Obtener categoría del producto
            $categoryId = $this->getProductCategory($product['ID']);
            
            // Procesar datos del producto
            $productData = $this->processProductData($product, $metadata);
            
            $stmt->execute([
                $categoryId,
                $productData['name'],
                $productData['slug'],
                $productData['description'],
                $productData['short_description'],
                $productData['sku'],
                $productData['price'],
                $productData['sale_price'],
                $productData['stock_quantity'],
                $productData['weight'],
                $productData['images'],
                $productData['brand'],
                $productData['is_featured'],
                $productData['is_active']
            ]);
            
            echo "✓ Producto migrado: {$productData['name']}\n";
        }
        
        echo "Total productos migrados: " . count($products) . "\n";
    }
    
    /**
     * Obtiene los metadatos de un producto
     */
    private function getProductMetadata($productId) {
        $sql = "
            SELECT meta_key, meta_value
            FROM wp_postmeta
            WHERE post_id = ?
            AND meta_key IN ('_price', '_regular_price', '_sale_price', '_sku', '_stock', 
                           '_stock_status', '_manage_stock', '_weight', '_featured')
        ";
        
        $stmt = $this->wpdb->prepare($sql);
        $stmt->execute([$productId]);
        $metadata = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($metadata as $meta) {
            $result[$meta['meta_key']] = $meta['meta_value'];
        }
        
        return $result;
    }
    
    /**
     * Obtiene la categoría de un producto
     */
    private function getProductCategory($productId) {
        $sql = "
            SELECT tt.term_id
            FROM wp_term_relationships tr
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tr.object_id = ?
            AND tt.term_id IN (18, 19, 20, 21, 22, 23, 24, 25, 26, 27)
            LIMIT 1
        ";
        
        $stmt = $this->wpdb->prepare($sql);
        $stmt->execute([$productId]);
        $termId = $stmt->fetchColumn();
        
        return isset($this->categoryMapping[$termId]) ? $this->categoryMapping[$termId] : 1;
    }
    
    /**
     * Procesa los datos del producto
     */
    private function processProductData($product, $metadata) {
        // Limpiar y procesar el contenido
        $description = $this->cleanHtml($product['post_content']);
        $shortDescription = $this->cleanHtml($product['post_excerpt']);
        
        // Si no hay descripción corta, crear una desde la descripción
        if (empty($shortDescription) && !empty($description)) {
            $shortDescription = $this->createShortDescription($description);
        }
        
        // Procesar precios
        $price = floatval($metadata['_price'] ?? $metadata['_regular_price'] ?? 0);
        $salePrice = !empty($metadata['_sale_price']) ? floatval($metadata['_sale_price']) : null;
        
        // Procesar stock
        $stockQuantity = intval($metadata['_stock'] ?? 100);
        if ($stockQuantity <= 0) {
            $stockQuantity = ($metadata['_stock_status'] ?? 'instock') === 'instock' ? 100 : 0;
        }
        
        // Generar SKU si no existe
        $sku = $metadata['_sku'] ?? $this->generateSku($product['post_title']);
        
        // Determinar marca desde el título
        $brand = $this->extractBrand($product['post_title']);
        
        return [
            'name' => $product['post_title'],
            'slug' => $product['slug'],
            'description' => $description,
            'short_description' => $shortDescription,
            'sku' => $sku,
            'price' => $price,
            'sale_price' => $salePrice,
            'stock_quantity' => $stockQuantity,
            'weight' => floatval($metadata['_weight'] ?? 0),
            'images' => json_encode([]), // Las imágenes se migrarían por separado
            'brand' => $brand,
            'is_featured' => ($metadata['_featured'] ?? 'no') === 'yes' ? 1 : 0,
            'is_active' => $product['post_status'] === 'publish' ? 1 : 0
        ];
    }
    
    /**
     * Limpia el HTML del contenido
     */
    private function cleanHtml($content) {
        // Remover shortcodes de WordPress
        $content = preg_replace('/\[.*?\]/', '', $content);
        
        // Limpiar HTML básico pero mantener estructura
        $content = strip_tags($content, '<p><br><strong><b><em><i><ul><li><h1><h2><h3><h4><h5><h6>');
        
        // Limpiar espacios extra
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        return $content;
    }
    
    /**
     * Crea una descripción corta desde la descripción completa
     */
    private function createShortDescription($description) {
        $text = strip_tags($description);
        $text = preg_replace('/\s+/', ' ', $text);
        
        if (strlen($text) <= 200) {
            return $text;
        }
        
        $short = substr($text, 0, 200);
        $lastSpace = strrpos($short, ' ');
        
        if ($lastSpace !== false) {
            $short = substr($short, 0, $lastSpace);
        }
        
        return $short . '...';
    }
    
    /**
     * Genera un SKU basado en el título del producto
     */
    private function generateSku($title) {
        $sku = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $title));
        $sku = substr($sku, 0, 10);
        return $sku . rand(100, 999);
    }
    
    /**
     * Extrae la marca del título del producto
     */
    private function extractBrand($title) {
        $brands = ['CARNIVOR', 'MUTANT', 'PROSTAR', 'NITROTECH', 'MUSCLETECH', 'DIMATIZE', 
                  'RONNIE COLEMAN', 'LAB', 'ISOLATE', 'MASS', 'WHEY'];
        
        $title = strtoupper($title);
        
        foreach ($brands as $brand) {
            if (strpos($title, $brand) !== false) {
                return $brand;
            }
        }
        
        // Si no se encuentra marca, usar la primera palabra
        $words = explode(' ', $title);
        return ucfirst(strtolower($words[0]));
    }
    
    /**
     * Genera descripción para categorías
     */
    private function generateCategoryDescription($categoryName) {
        $descriptions = [
            'PROTEÍNAS WHEY' => 'Proteínas de suero de leche de alta calidad para el desarrollo muscular.',
            'GANADORES DE MASA' => 'Suplementos hipercalóricos para ganar peso y masa muscular.',
            'PROTEINAS ISOLATADAS' => 'Proteínas aisladas de máxima pureza y absorción rápida.',
            'PRE ENTRENOS Y ÓXIDO NITRICO' => 'Suplementos pre-entrenamiento para máximo rendimiento.',
            'PRECURSOR DE LA TESTO' => 'Suplementos naturales para optimizar niveles hormonales.',
            'MULTIVITAMINICO Colágenos OMEGAS' => 'Vitaminas, minerales y suplementos para salud general.',
            'QUEMADORES DE GRASA' => 'Suplementos termogénicos para pérdida de grasa.',
            'AMINOÁCIDOS Y BCAA' => 'Aminoácidos esenciales para recuperación muscular.',
            'CREATINAS Y GLUTAMINAS' => 'Suplementos para fuerza, potencia y recuperación.',
            'PROTECTOR HEPÁTICO' => 'Suplementos para protección y salud hepática.'
        ];
        
        return $descriptions[$categoryName] ?? 'Categoría de productos de nutrición deportiva.';
    }
}

// Configuración de bases de datos
$wpConfig = [
    'host' => 'localhost',
    'database' => 'wordpress_db',
    'username' => 'root',
    'password' => ''
];

$sfConfig = [
    'host' => 'localhost',
    'database' => 'stylofitness_gym',
    'username' => 'root',
    'password' => ''
];

// Ejecutar migración
try {
    $migration = new StyloFitnessMigration($wpConfig, $sfConfig);
    $migration->migrate();
} catch (Exception $e) {
    echo "Error en la migración: " . $e->getMessage() . "\n";
    exit(1);
}
?>