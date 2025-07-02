<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturedProductsConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('featured_products_config')->insert([
            [
                'section_title' => 'Productos Destacados',
                'section_subtitle' => 'Los favoritos de nuestros clientes - Calidad premium al mejor precio',
                'max_products' => 8,
                'display_type' => 'grid',
                'auto_select' => 1,
                'selection_criteria' => json_encode([
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
                ]),
                'background_style' => 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}