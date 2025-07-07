<?php
/**
 * Script para verificar las imágenes de productos destacados
 */

require_once 'app/Config/Database.php';

use StyleFitness\Config\Database;

// Función simple para generar URL base
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    return $protocol . '://' . $host;
}

try {
    $db = Database::getInstance();
    
    // Obtener productos destacados
    $stmt = $db->query("
        SELECT p.id, p.name, p.images, p.is_featured 
        FROM products p 
        WHERE p.is_featured = 1 
        ORDER BY p.id
    ");
    
    $featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== VERIFICACIÓN DE IMÁGENES DE PRODUCTOS DESTACADOS ===\n\n";
    echo "Total productos destacados: " . count($featuredProducts) . "\n\n";
    
    foreach ($featuredProducts as $product) {
        echo "Producto ID: {$product['id']} - {$product['name']}\n";
        echo "Imágenes JSON: {$product['images']}\n";
        
        // Decodificar imágenes
        $images = json_decode($product['images'], true);
        
        if (empty($images)) {
            echo "❌ No hay imágenes definidas\n";
        } else {
            echo "📸 Imágenes encontradas: " . count($images) . "\n";
            
            foreach ($images as $index => $imagePath) {
                // Construir ruta completa del archivo
                $fullPath = 'public' . $imagePath;
                
                echo "  Imagen {$index}: {$imagePath}\n";
                echo "  Ruta completa: {$fullPath}\n";
                
                if (file_exists($fullPath)) {
                    echo "  ✅ Archivo existe\n";
                    
                    // Generar URL
                    if (strpos($imagePath, '/uploads/') !== false) {
                        $url = getBaseUrl() . ltrim($imagePath, '/');
                    } else {
                        $url = getBaseUrl() . '/uploads/' . ltrim($imagePath, '/');
                    }
                    echo "  🔗 URL generada: {$url}\n";
                } else {
                    echo "  ❌ Archivo NO existe\n";
                }
                echo "\n";
            }
        }
        
        echo "" . str_repeat('-', 50) . "\n\n";
    }
    
    // Verificar archivos en directorio
    echo "\n=== ARCHIVOS DISPONIBLES EN DIRECTORIO ===\n";
    $imageDir = 'public/uploads/images/products';
    $files = scandir($imageDir);
    $imageFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $file);
    });
    
    echo "Total archivos de imagen: " . count($imageFiles) . "\n";
    echo "Primeros 10 archivos:\n";
    foreach (array_slice($imageFiles, 0, 10) as $file) {
        echo "  - {$file}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>