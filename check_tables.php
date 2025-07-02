<?php
require_once 'app/Config/Database.php';

use StyleFitness\Config\Database;

try {
    $db = Database::getInstance();
    
    // Verificar tabla why_choose_us
    try {
        $result = $db->fetchAll('SELECT COUNT(*) as count FROM why_choose_us');
        echo 'why_choose_us table: ' . $result[0]['count'] . ' records\n';
        
        if ($result[0]['count'] > 0) {
            $sample = $db->fetchAll('SELECT * FROM why_choose_us LIMIT 3');
            echo 'Sample data from why_choose_us:\n';
            print_r($sample);
        }
    } catch(Exception $e) {
        echo 'Error with why_choose_us: ' . $e->getMessage() . '\n';
    }
    
    // Verificar tabla special_offers
    try {
        $result = $db->fetchAll('SELECT COUNT(*) as count FROM special_offers');
        echo 'special_offers table: ' . $result[0]['count'] . ' records\n';
        
        if ($result[0]['count'] > 0) {
            $sample = $db->fetchAll('SELECT * FROM special_offers LIMIT 3');
            echo 'Sample data from special_offers:\n';
            print_r($sample);
        }
    } catch(Exception $e) {
        echo 'Error with special_offers: ' . $e->getMessage() . '\n';
    }
    
} catch(Exception $e) {
    echo 'Database connection error: ' . $e->getMessage() . '\n';
}
?>