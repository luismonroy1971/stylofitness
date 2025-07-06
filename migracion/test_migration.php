<?php
/**
 * Script de prueba para migración simplificada
 */

try {
    // Conexión a WordPress
    $wpdb = new PDO('mysql:host=localhost;dbname=wordpress_db;charset=utf8mb4', 'root', '');
    $wpdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Conexión a StyloFitness
    $sfdb = new PDO('mysql:host=localhost;dbname=stylofitness_gym;charset=utf8mb4', 'root', '');
    $sfdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexiones establecidas\n\n";
    
    // Probar inserción simple en product_categories
    echo "🧪 PROBANDO INSERCIÓN EN product_categories:\n";
    
    $testSql = "INSERT INTO product_categories (name, slug, description, is_active) VALUES (?, ?, ?, 1)";
    $stmt = $sfdb->prepare($testSql);
    
    $testData = [
        'name' => 'CATEGORÍA DE PRUEBA',
        'slug' => 'categoria-prueba',
        'description' => 'Descripción de prueba'
    ];
    
    $stmt->execute([$testData['name'], $testData['slug'], $testData['description']]);
    echo "✓ Inserción exitosa - ID: " . $sfdb->lastInsertId() . "\n\n";
    
    // Verificar categorías existentes en WordPress
    echo "📋 CATEGORÍAS EN WORDPRESS:\n";
    $wpSql = "SELECT t.term_id, t.name, t.slug FROM wp_terms t INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'product_cat' LIMIT 10";
    $wpStmt = $wpdb->query($wpSql);
    $wpCategories = $wpStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($wpCategories as $cat) {
        echo "  - ID: {$cat['term_id']}, Nombre: {$cat['name']}, Slug: {$cat['slug']}\n";
    }
    
    echo "\n📋 PRODUCTOS EN WORDPRESS (primeros 5):\n";
    $wpProdSql = "SELECT ID, post_title, post_name, post_status FROM wp_posts WHERE post_type = 'product' LIMIT 5";
    $wpProdStmt = $wpdb->query($wpProdSql);
    $wpProducts = $wpProdStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($wpProducts as $prod) {
        echo "  - ID: {$prod['ID']}, Título: {$prod['post_title']}, Status: {$prod['post_status']}\n";
    }
    
    // Limpiar datos de prueba
    $cleanSql = "DELETE FROM product_categories WHERE name = 'CATEGORÍA DE PRUEBA'";
    $sfdb->exec($cleanSql);
    echo "\n🧹 Datos de prueba eliminados\n";
    
    echo "\n✅ PRUEBA COMPLETADA - Las tablas funcionan correctamente\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Código de error: " . $e->getCode() . "\n";
}
?>