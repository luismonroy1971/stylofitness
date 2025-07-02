<?php
// Configuración básica
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');

require_once APP_PATH . '/Config/Database.php';

use StyleFitness\Config\Database;

try {
    $db = Database::getInstance();
    
    echo "=== ESTRUCTURA DE TABLAS ===\n\n";
    
    // Verificar estructura de why_choose_us
    echo "why_choose_us structure:\n";
    $result = $db->fetchAll('DESCRIBE why_choose_us');
    foreach($result as $row) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
    
    echo "\nspecial_offers structure:\n";
    $result = $db->fetchAll('DESCRIBE special_offers');
    foreach($result as $row) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
    
    echo "\nproducts structure (sample):\n";
    $result = $db->fetchAll('DESCRIBE products LIMIT 10');
    foreach($result as $row) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
    
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

?>