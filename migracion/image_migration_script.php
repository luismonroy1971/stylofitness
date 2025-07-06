<?php
/**
 * Script de Migración de Imágenes
 * De WordPress/WooCommerce a StyloFitness
 * 
 * Este script migra:
 * - Imágenes principales de productos
 * - Galerías de imágenes de productos
 * - Imágenes de categorías
 * - Descarga y copia de archivos físicos
 * 
 * @author Sistema de Migración StyloFitness
 * @version 1.0
 */

class ImageMigration {
    private $wordpress_db;
    private $stylofitness_db;
    private $config;
    private $wp_uploads_path;
    private $sf_uploads_path;
    private $migrated_images = [];
    private $failed_images = [];
    
    public function __construct() {
        $this->loadConfig();
        $this->connectDatabases();
        $this->setupPaths();
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
            // Conexión a WordPress
            $wp_config = $this->config['wordpress_database'];
            $wp_dsn = "mysql:host={$wp_config['host']};dbname={$wp_config['database']};charset={$wp_config['charset']}";
            $this->wordpress_db = new PDO($wp_dsn, $wp_config['username'], $wp_config['password']);
            $this->wordpress_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Conexión a StyloFitness
            $sf_config = $this->config['stylofitness_database'];
            $sf_dsn = "mysql:host={$sf_config['host']};dbname={$sf_config['database']};charset={$sf_config['charset']}";
            $this->stylofitness_db = new PDO($sf_dsn, $sf_config['username'], $sf_config['password']);
            $this->stylofitness_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "✅ Conexiones a bases de datos establecidas\n";
            
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }
    
    private function setupPaths() {
        // Configurar rutas de uploads
        $this->wp_uploads_path = $this->config['image_settings']['wordpress_uploads_path'] ?? 'C:/xampp/htdocs/wordpress/wp-content/uploads';
        $this->sf_uploads_path = $this->config['image_settings']['stylofitness_uploads_path'] ?? 'C:/trabajos/stylofitness/public/uploads/images';
        
        // Crear directorios si no existen
        $directories = [
            $this->sf_uploads_path,
            $this->sf_uploads_path . '/products',
            $this->sf_uploads_path . '/categories',
            $this->sf_uploads_path . '/thumbnails'
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                echo "📁 Directorio creado: $dir\n";
            }
        }
    }
    
    /**
     * Ejecutar migración completa de imágenes
     */
    public function migrateAllImages() {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "    MIGRACIÓN DE IMÁGENES STYLOFITNESS\n";
        echo str_repeat("=", 60) . "\n\n";
        
        try {
            $this->migrateProductImages();
            $this->migrateCategoryImages();
            $this->generateThumbnails();
            $this->updateDatabaseReferences();
            $this->generateReport();
            
            echo "\n✅ Migración de imágenes completada exitosamente\n";
            
        } catch (Exception $e) {
            echo "❌ Error durante la migración: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    /**
     * Migrar imágenes de productos
     */
    public function migrateProductImages() {
        echo "🖼️  MIGRANDO IMÁGENES DE PRODUCTOS\n";
        echo str_repeat("-", 40) . "\n";
        
        // Obtener productos con sus imágenes
        $query = "
            SELECT DISTINCT
                p.ID as product_id,
                p.post_title as product_name,
                p.post_name as product_slug,
                pm_thumb.meta_value as thumbnail_id,
                pm_gallery.meta_value as gallery_ids
            FROM wp_posts p
            LEFT JOIN wp_postmeta pm_thumb ON p.ID = pm_thumb.post_id AND pm_thumb.meta_key = '_thumbnail_id'
            LEFT JOIN wp_postmeta pm_gallery ON p.ID = pm_gallery.post_id AND pm_gallery.meta_key = '_product_image_gallery'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND (pm_thumb.meta_value IS NOT NULL OR pm_gallery.meta_value IS NOT NULL)
            ORDER BY p.ID
        ";
        
        $stmt = $this->wordpress_db->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $migrated_count = 0;
        
        foreach ($products as $product) {
            echo "📦 Procesando producto: {$product['product_name']}\n";
            
            $product_images = [];
            
            // Migrar imagen principal (thumbnail)
            if (!empty($product['thumbnail_id'])) {
                $main_image = $this->migrateImage($product['thumbnail_id'], 'products', $product['product_slug'] . '_main');
                if ($main_image) {
                    $product_images[] = $main_image;
                    echo "  ✓ Imagen principal migrada\n";
                }
            }
            
            // Migrar galería de imágenes
            if (!empty($product['gallery_ids'])) {
                $gallery_ids = explode(',', $product['gallery_ids']);
                $gallery_count = 0;
                
                foreach ($gallery_ids as $index => $image_id) {
                    $image_id = trim($image_id);
                    if (!empty($image_id) && is_numeric($image_id)) {
                        $gallery_image = $this->migrateImage($image_id, 'products', $product['product_slug'] . '_gallery_' . ($index + 1));
                        if ($gallery_image) {
                            $product_images[] = $gallery_image;
                            $gallery_count++;
                        }
                    }
                }
                
                if ($gallery_count > 0) {
                    echo "  ✓ Galería migrada: $gallery_count imágenes\n";
                }
            }
            
            // Actualizar producto en StyloFitness con las imágenes
            if (!empty($product_images)) {
                $this->updateProductImages($product['product_id'], $product_images);
                $migrated_count++;
            }
            
            echo "\n";
        }
        
        echo "📊 Total productos con imágenes migradas: $migrated_count\n\n";
    }
    
    /**
     * Migrar una imagen específica
     */
    private function migrateImage($attachment_id, $subfolder, $new_filename = null) {
        // Obtener información de la imagen desde WordPress
        $query = "
            SELECT 
                p.post_title,
                p.post_name,
                p.guid,
                pm.meta_value as file_path
            FROM wp_posts p
            LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file'
            WHERE p.ID = ? AND p.post_type = 'attachment'
        ";
        
        $stmt = $this->wordpress_db->prepare($query);
        $stmt->execute([$attachment_id]);
        $image_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$image_data || empty($image_data['file_path'])) {
            echo "  ⚠️  Imagen no encontrada: ID $attachment_id\n";
            return null;
        }
        
        // Construir rutas de origen y destino
        $source_path = $this->wp_uploads_path . '/' . $image_data['file_path'];
        
        // Generar nombre de archivo de destino
        $file_extension = pathinfo($image_data['file_path'], PATHINFO_EXTENSION);
        $filename = $new_filename ? $new_filename . '.' . $file_extension : basename($image_data['file_path']);
        $destination_path = $this->sf_uploads_path . '/' . $subfolder . '/' . $filename;
        
        // Verificar si el archivo de origen existe
        if (!file_exists($source_path)) {
            // Intentar descargar desde URL si no existe localmente
            $image_url = $image_data['guid'];
            if ($this->downloadImageFromUrl($image_url, $destination_path)) {
                echo "  ✓ Imagen descargada desde URL: $filename\n";
            } else {
                echo "  ❌ No se pudo obtener la imagen: $filename\n";
                $this->failed_images[] = [
                    'id' => $attachment_id,
                    'source' => $source_path,
                    'url' => $image_url,
                    'reason' => 'Archivo no encontrado'
                ];
                return null;
            }
        } else {
            // Copiar archivo local
            if (copy($source_path, $destination_path)) {
                echo "  ✓ Imagen copiada: $filename\n";
            } else {
                echo "  ❌ Error al copiar: $filename\n";
                $this->failed_images[] = [
                    'id' => $attachment_id,
                    'source' => $source_path,
                    'destination' => $destination_path,
                    'reason' => 'Error de copia'
                ];
                return null;
            }
        }
        
        // Registrar imagen migrada
        $relative_path = '/uploads/images/' . $subfolder . '/' . $filename;
        $this->migrated_images[] = [
            'wp_id' => $attachment_id,
            'original_path' => $image_data['file_path'],
            'new_path' => $relative_path,
            'filename' => $filename
        ];
        
        return $relative_path;
    }
    
    /**
     * Descargar imagen desde URL
     */
    private function downloadImageFromUrl($url, $destination) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'user_agent' => 'StyloFitness Image Migration Bot'
            ]
        ]);
        
        $image_data = @file_get_contents($url, false, $context);
        
        if ($image_data !== false) {
            return file_put_contents($destination, $image_data) !== false;
        }
        
        return false;
    }
    
    /**
     * Actualizar referencias de imágenes en productos
     */
    private function updateProductImages($wp_product_id, $images) {
        // Buscar el producto en StyloFitness por nombre o slug
        $wp_product_query = "SELECT post_title, post_name FROM wp_posts WHERE ID = ?";
        $wp_stmt = $this->wordpress_db->prepare($wp_product_query);
        $wp_stmt->execute([$wp_product_id]);
        $wp_product = $wp_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$wp_product) {
            return false;
        }
        
        // Buscar en StyloFitness
        $sf_query = "SELECT id FROM products WHERE name = ? OR slug = ? LIMIT 1";
        $sf_stmt = $this->stylofitness_db->prepare($sf_query);
        $sf_stmt->execute([$wp_product['post_title'], $wp_product['post_name']]);
        $sf_product = $sf_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($sf_product) {
            // Actualizar campo images con JSON
            $update_query = "UPDATE products SET images = ?, updated_at = NOW() WHERE id = ?";
            $update_stmt = $this->stylofitness_db->prepare($update_query);
            $update_stmt->execute([json_encode($images), $sf_product['id']]);
            
            echo "  ✓ Referencias de imágenes actualizadas en BD\n";
            return true;
        }
        
        return false;
    }
    
    /**
     * Migrar imágenes de categorías
     */
    public function migrateCategoryImages() {
        echo "📁 MIGRANDO IMÁGENES DE CATEGORÍAS\n";
        echo str_repeat("-", 40) . "\n";
        
        // Obtener categorías con imágenes
        $query = "
            SELECT DISTINCT
                t.term_id,
                t.name,
                t.slug,
                tm.meta_value as image_id
            FROM wp_terms t
            INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
            LEFT JOIN wp_termmeta tm ON t.term_id = tm.term_id AND tm.meta_key = 'thumbnail_id'
            WHERE tt.taxonomy = 'product_cat'
            AND tm.meta_value IS NOT NULL
            AND tm.meta_value != ''
        ";
        
        $stmt = $this->wordpress_db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($categories as $category) {
            echo "📂 Procesando categoría: {$category['name']}\n";
            
            $category_image = $this->migrateImage($category['image_id'], 'categories', $category['slug']);
            
            if ($category_image) {
                // Actualizar categoría en StyloFitness
                $update_query = "UPDATE product_categories SET image_url = ?, updated_at = NOW() WHERE name = ? OR slug = ?";
                $update_stmt = $this->stylofitness_db->prepare($update_query);
                $update_stmt->execute([$category_image, $category['name'], $category['slug']]);
                
                echo "  ✓ Imagen de categoría migrada y actualizada\n";
            }
            
            echo "\n";
        }
    }
    
    /**
     * Generar thumbnails automáticamente
     */
    public function generateThumbnails() {
        echo "🖼️  GENERANDO THUMBNAILS\n";
        echo str_repeat("-", 40) . "\n";
        
        $thumbnail_sizes = [
            'small' => [150, 150],
            'medium' => [300, 300],
            'large' => [600, 600]
        ];
        
        foreach ($this->migrated_images as $image) {
            $source_path = $this->sf_uploads_path . str_replace('/uploads/images', '', $image['new_path']);
            
            if (file_exists($source_path) && $this->isImageFile($source_path)) {
                foreach ($thumbnail_sizes as $size_name => $dimensions) {
                    $this->createThumbnail($source_path, $size_name, $dimensions[0], $dimensions[1]);
                }
            }
        }
        
        echo "✓ Thumbnails generados\n\n";
    }
    
    /**
     * Crear thumbnail de una imagen
     */
    private function createThumbnail($source_path, $size_name, $width, $height) {
        $path_info = pathinfo($source_path);
        $thumbnail_name = $path_info['filename'] . '_' . $size_name . '.' . $path_info['extension'];
        $thumbnail_path = $this->sf_uploads_path . '/thumbnails/' . $thumbnail_name;
        
        // Verificar si GD está disponible
        if (!extension_loaded('gd')) {
            return false;
        }
        
        $image_info = getimagesize($source_path);
        if (!$image_info) {
            return false;
        }
        
        // Crear imagen desde archivo
        switch ($image_info[2]) {
            case IMAGETYPE_JPEG:
                $source_image = imagecreatefromjpeg($source_path);
                break;
            case IMAGETYPE_PNG:
                $source_image = imagecreatefrompng($source_path);
                break;
            case IMAGETYPE_GIF:
                $source_image = imagecreatefromgif($source_path);
                break;
            default:
                return false;
        }
        
        if (!$source_image) {
            return false;
        }
        
        // Calcular dimensiones manteniendo proporción
        $original_width = imagesx($source_image);
        $original_height = imagesy($source_image);
        
        $ratio = min($width / $original_width, $height / $original_height);
        $new_width = intval($original_width * $ratio);
        $new_height = intval($original_height * $ratio);
        
        // Crear thumbnail
        $thumbnail = imagecreatetruecolor($new_width, $new_height);
        
        // Preservar transparencia para PNG
        if ($image_info[2] == IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }
        
        imagecopyresampled($thumbnail, $source_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        
        // Guardar thumbnail
        $result = false;
        switch ($image_info[2]) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($thumbnail, $thumbnail_path, 85);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($thumbnail, $thumbnail_path);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($thumbnail, $thumbnail_path);
                break;
        }
        
        imagedestroy($source_image);
        imagedestroy($thumbnail);
        
        return $result;
    }
    
    /**
     * Verificar si es un archivo de imagen
     */
    private function isImageFile($file_path) {
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        return in_array($extension, $image_extensions);
    }
    
    /**
     * Actualizar referencias en base de datos
     */
    private function updateDatabaseReferences() {
        echo "🔄 ACTUALIZANDO REFERENCIAS EN BASE DE DATOS\n";
        echo str_repeat("-", 40) . "\n";
        
        // Actualizar productos sin imágenes con imagen placeholder
        $placeholder_query = "
            UPDATE products 
            SET images = JSON_ARRAY('/uploads/images/placeholder.jpg')
            WHERE (images IS NULL OR images = '[]' OR images = '')
        ";
        
        $this->stylofitness_db->exec($placeholder_query);
        
        echo "✓ Referencias actualizadas\n\n";
    }
    
    /**
     * Generar reporte de migración
     */
    private function generateReport() {
        echo "📊 REPORTE DE MIGRACIÓN DE IMÁGENES\n";
        echo str_repeat("-", 40) . "\n";
        
        $total_migrated = count($this->migrated_images);
        $total_failed = count($this->failed_images);
        
        echo "✅ Imágenes migradas exitosamente: $total_migrated\n";
        echo "❌ Imágenes que fallaron: $total_failed\n";
        
        if ($total_failed > 0) {
            echo "\n⚠️  IMÁGENES FALLIDAS:\n";
            foreach ($this->failed_images as $failed) {
                echo "  - ID {$failed['id']}: {$failed['reason']}\n";
            }
        }
        
        // Calcular espacio utilizado
        $total_size = 0;
        foreach ($this->migrated_images as $image) {
            $file_path = $this->sf_uploads_path . str_replace('/uploads/images', '', $image['new_path']);
            if (file_exists($file_path)) {
                $total_size += filesize($file_path);
            }
        }
        
        echo "\n📁 Espacio utilizado: " . $this->formatBytes($total_size) . "\n";
        
        // Exportar reporte detallado
        $report_file = __DIR__ . '/image_migration_report_' . date('Y-m-d_H-i-s') . '.json';
        $report_data = [
            'migration_date' => date('Y-m-d H:i:s'),
            'total_migrated' => $total_migrated,
            'total_failed' => $total_failed,
            'total_size_bytes' => $total_size,
            'migrated_images' => $this->migrated_images,
            'failed_images' => $this->failed_images
        ];
        
        file_put_contents($report_file, json_encode($report_data, JSON_PRETTY_PRINT));
        echo "📄 Reporte detallado guardado en: $report_file\n";
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
}

// Ejecutar migración de imágenes
if (php_sapi_name() === 'cli') {
    try {
        echo "Iniciando migración de imágenes...\n";
        
        $image_migration = new ImageMigration();
        
        // Verificar argumentos de línea de comandos
        if (isset($argv[1])) {
            switch ($argv[1]) {
                case 'products':
                    $image_migration->migrateProductImages();
                    break;
                case 'categories':
                    $image_migration->migrateCategoryImages();
                    break;
                case 'thumbnails':
                    $image_migration->generateThumbnails();
                    break;
                case 'all':
                default:
                    $image_migration->migrateAllImages();
                    break;
            }
        } else {
            $image_migration->migrateAllImages();
        }
        
        echo "\n🎯 Migración de imágenes completada.\n";
        
    } catch (Exception $e) {
        echo "❌ Error durante la migración de imágenes: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "Este script debe ejecutarse desde línea de comandos.\n";
}
?>