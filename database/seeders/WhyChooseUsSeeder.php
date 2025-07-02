<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WhyChooseUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('why_choose_us')->insert([
            [
                'title' => 'Rutinas Personalizadas',
                'subtitle' => 'Entrenamientos inteligentes con IA',
                'description' => 'Entrenamientos diseñados específicamente para tus objetivos, nivel y disponibilidad de tiempo. Nuestra inteligencia artificial analiza tu progreso y ajusta automáticamente la intensidad y ejercicios.',
                'icon' => 'fas fa-dumbbell',
                'icon_color' => '#ff6b35',
                'background_gradient' => 'linear-gradient(135deg, #ff6b35 0%, #f7931e 100%)',
                'highlights' => json_encode([
                    'Videos HD Explicativos',
                    'Seguimiento en Tiempo Real',
                    'Ajustes Automáticos IA',
                    'Soporte 24/7'
                ]),
                'stats' => json_encode([
                    'exercises' => '1000+',
                    'success_rate' => '95%',
                    'avg_results' => '4 semanas'
                ]),
                'is_active' => 1,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Tienda Especializada',
                'subtitle' => 'Los mejores suplementos del mercado',
                'description' => 'Productos certificados y de las marcas más reconocidas mundialmente. Contamos con asesoría nutricional especializada para ayudarte a elegir los suplementos ideales.',
                'icon' => 'fas fa-store',
                'icon_color' => '#2c3e50',
                'background_gradient' => 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)',
                'highlights' => json_encode([
                    'Productos Certificados',
                    'Envío Gratis',
                    'Garantía Total',
                    'Asesoría Nutricional'
                ]),
                'stats' => json_encode([
                    'products' => '500+',
                    'satisfaction' => '98%',
                    'delivery_time' => '24-48h'
                ]),
                'is_active' => 1,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Clases Grupales',
                'subtitle' => 'Entrenamientos dinámicos y motivadores',
                'description' => 'Variedad de clases dirigidas por instructores certificados. Desde yoga relajante hasta CrossFit intenso, tenemos la clase perfecta para cada objetivo y nivel.',
                'icon' => 'fas fa-users',
                'icon_color' => '#e74c3c',
                'background_gradient' => 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)',
                'highlights' => json_encode([
                    'Instructores Certificados',
                    'Horarios Flexibles',
                    'Ambiente Motivador',
                    'Todos los Niveles'
                ]),
                'stats' => json_encode([
                    'classes' => '20+',
                    'instructors' => '15',
                    'weekly_sessions' => '80+'
                ]),
                'is_active' => 1,
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Tecnología Avanzada',
                'subtitle' => 'Seguimiento y análisis en tiempo real',
                'description' => 'Monitoreo completo de tu progreso con tecnología de vanguardia. App móvil, análisis detallado de rendimiento y reportes personalizados para optimizar tus resultados.',
                'icon' => 'fas fa-chart-line',
                'icon_color' => '#f39c12',
                'background_gradient' => 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)',
                'highlights' => json_encode([
                    'App Móvil',
                    'Análisis Detallado',
                    'Sincronización Cloud',
                    'Reportes Personalizados'
                ]),
                'stats' => json_encode([
                    'accuracy' => '99%',
                    'users' => '10000+',
                    'data_points' => '1M+'
                ]),
                'is_active' => 1,
                'display_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Nutrición Especializada',
                'subtitle' => 'Planes alimentarios personalizados',
                'description' => 'Nuestros nutricionistas deportivos crean planes alimentarios adaptados a tus objetivos, preferencias y estilo de vida. Seguimiento continuo y ajustes según tu progreso.',
                'icon' => 'fas fa-apple-alt',
                'icon_color' => '#27ae60',
                'background_gradient' => 'linear-gradient(135deg, #27ae60 0%, #2ecc71 100%)',
                'highlights' => json_encode([
                    'Nutricionistas Certificados',
                    'Planes Personalizados',
                    'Seguimiento Continuo',
                    'Recetas Saludables'
                ]),
                'stats' => json_encode([
                    'nutritionists' => '5',
                    'meal_plans' => '200+',
                    'success_rate' => '92%'
                ]),
                'is_active' => 1,
                'display_order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Comunidad Activa',
                'subtitle' => 'Motivación y apoyo constante',
                'description' => 'Únete a una comunidad de personas comprometidas con su bienestar. Comparte logros, participa en desafíos y encuentra la motivación que necesitas para alcanzar tus metas.',
                'icon' => 'fas fa-heart',
                'icon_color' => '#9b59b6',
                'background_gradient' => 'linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%)',
                'highlights' => json_encode([
                    'Grupos de Apoyo',
                    'Desafíos Mensuales',
                    'Eventos Especiales',
                    'Red Social Fitness'
                ]),
                'stats' => json_encode([
                    'members' => '5000+',
                    'challenges' => '12/año',
                    'engagement' => '85%'
                ]),
                'is_active' => 1,
                'display_order' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}