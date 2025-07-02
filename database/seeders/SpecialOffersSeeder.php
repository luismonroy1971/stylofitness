<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpecialOffersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('special_offers')->insert([
            [
                'title' => '¡MEGA DESCUENTO!',
                'subtitle' => 'Hasta 50% OFF en Suplementos',
                'description' => 'Aprovecha esta oferta limitada en nuestra selección premium de suplementos deportivos. Proteínas, creatinas, vitaminas y más con descuentos increíbles.',
                'discount_percentage' => 50.00,
                'discount_amount' => null,
                'image' => 'offer-supplements.jpg',
                'background_color' => '#ff6b35',
                'text_color' => '#ffffff',
                'button_text' => 'Ver Ofertas',
                'button_link' => '/store?category=suplementos',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(30),
                'is_active' => 1,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'MEMBRESÍA PREMIUM',
                'subtitle' => '3 Meses por el precio de 2',
                'description' => 'Acceso completo a todas nuestras instalaciones, clases grupales, rutinas personalizadas y seguimiento nutricional. ¡La mejor inversión en tu salud!',
                'discount_percentage' => 33.33,
                'discount_amount' => null,
                'image' => 'offer-membership.jpg',
                'background_color' => '#2c3e50',
                'text_color' => '#ffffff',
                'button_text' => 'Suscribirse',
                'button_link' => '/membership',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(15),
                'is_active' => 1,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'RUTINAS PERSONALIZADAS',
                'subtitle' => 'Gratis con tu primera compra',
                'description' => 'Recibe una rutina personalizada diseñada por nuestros expertos entrenadores, adaptada a tus objetivos y nivel de condición física.',
                'discount_percentage' => 0.00,
                'discount_amount' => 0.00,
                'image' => 'offer-routine.jpg',
                'background_color' => '#e74c3c',
                'text_color' => '#ffffff',
                'button_text' => 'Empezar Ahora',
                'button_link' => '/routines',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(45),
                'is_active' => 1,
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'CLASES GRUPALES',
                'subtitle' => 'Primera semana GRATIS',
                'description' => 'Prueba todas nuestras clases grupales: Yoga, Pilates, Zumba, CrossFit, Spinning y más. Instructores certificados y ambiente motivador.',
                'discount_percentage' => 100.00,
                'discount_amount' => null,
                'image' => 'offer-classes.jpg',
                'background_color' => '#f39c12',
                'text_color' => '#ffffff',
                'button_text' => 'Reservar Clase',
                'button_link' => '/classes',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(60),
                'is_active' => 1,
                'display_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'EVALUACIÓN NUTRICIONAL',
                'subtitle' => '50% de descuento',
                'description' => 'Consulta con nuestros nutricionistas especializados en deportes. Incluye plan alimentario personalizado y seguimiento mensual.',
                'discount_percentage' => 50.00,
                'discount_amount' => null,
                'image' => 'offer-nutrition.jpg',
                'background_color' => '#27ae60',
                'text_color' => '#ffffff',
                'button_text' => 'Agendar Cita',
                'button_link' => '/nutrition',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(20),
                'is_active' => 1,
                'display_order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}