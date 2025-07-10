<?php

require_once __DIR__ . '/../../app/Config/Database.php';

use StyleFitness\Config\Database;

class FeaturedProductsConfigSeeder
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $selectionCriteria = json_encode([
                'criteria' => [
                    'is_featured',
                    'high_rating',
                    'best_sellers',
                    'new_arrivals'
                ],
                'order_by' => 'popularity',
                'min_rating' => 4.0,
                'min_sales' => 10,
                'categories' => [
                    'suplementos',
                    'equipamiento',
                    'ropa-deportiva',
                    'accesorios'
                ],
                'exclude_out_of_stock' => true,
                'price_range' => [
                    'min' => 0,
                    'max' => 1000
                ],
                'brands' => [
                    'optimum-nutrition',
                    'muscletech',
                    'bsn',
                    'dymatize'
                ]
            ]);
            
            $currentTime = date('Y-m-d H:i:s');
            
            $sql = "INSERT INTO featured_products_config 
                    (section_title, section_subtitle, max_products, display_type, auto_select, 
                     selection_criteria, background_style, is_active, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $this->db->query($sql, [
                'Productos Destacados',
                'Los favoritos de nuestros clientes - Calidad premium al mejor precio',
                8,
                'grid',
                1,
                $selectionCriteria,
                'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)',
                1,
                $currentTime,
                $currentTime
            ]);
            
            echo "FeaturedProductsConfigSeeder: Configuración de productos destacados insertada correctamente.\n";
            
        } catch (Exception $e) {
            echo "Error en FeaturedProductsConfigSeeder: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}