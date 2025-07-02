<?php
// Configuración básica
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');

require_once APP_PATH . '/Config/Database.php';

use StyleFitness\Config\Database;

try {
    $db = Database::getInstance();
    
    echo "=== DEBUG DE VARIABLES ===\n\n";
    
    // Verificar datos directamente de las tablas
    echo "whyChooseUs count: ";
    $whyChooseUsCount = $db->fetch('SELECT COUNT(*) as count FROM why_choose_us');
    echo $whyChooseUsCount['count'] . "\n";
    
    if ($whyChooseUsCount['count'] == 0) {
        echo "Insertando datos de prueba en why_choose_us...\n";
        $db->execute("INSERT INTO why_choose_us (title, subtitle, description, icon, icon_color, display_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())", [
            'Equipos de Última Generación',
            'Tecnología avanzada para tu entrenamiento',
            'Contamos con los equipos más modernos y tecnología de punta para garantizar entrenamientos efectivos y seguros.',
            'fas fa-dumbbell',
            '#FF6B35',
            1
        ]);
        
        $db->execute("INSERT INTO why_choose_us (title, subtitle, description, icon, icon_color, display_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())", [
            'Entrenadores Certificados',
            'Profesionales expertos en fitness',
            'Nuestro equipo de entrenadores cuenta con certificaciones internacionales y años de experiencia.',
            'fas fa-user-graduate',
            '#4ECDC4',
            2
        ]);
    }
    
    echo "specialOffers count: ";
    $specialOffersCount = $db->fetch('SELECT COUNT(*) as count FROM special_offers');
    echo $specialOffersCount['count'] . "\n";
    
    echo "featuredProducts count: ";
    $featuredProductsCount = $db->fetch('SELECT COUNT(*) as count FROM products WHERE is_featured = 1');
    echo $featuredProductsCount['count'] . "\n";
    
    // Obtener datos reales
    $whyChooseUs = $db->fetchAll('SELECT * FROM why_choose_us ORDER BY display_order ASC LIMIT 6');
    $specialOffers = $db->fetchAll('SELECT * FROM special_offers WHERE is_active = 1 ORDER BY display_order ASC LIMIT 6');
    $featuredProducts = $db->fetchAll('SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT 8');
    
    echo "\nDatos obtenidos:\n";
    echo "whyChooseUs: " . count($whyChooseUs) . " elementos\n";
    echo "specialOffers: " . count($specialOffers) . " elementos\n";
    echo "featuredProducts: " . count($featuredProducts) . " elementos\n";
    
    if (count($whyChooseUs) > 0) {
        echo "\nPrimer elemento de whyChooseUs:\n";
        echo "- Título: " . $whyChooseUs[0]['title'] . "\n";
        echo "- Descripción: " . substr($whyChooseUs[0]['description'], 0, 50) . "...\n";
    }
    
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

?>