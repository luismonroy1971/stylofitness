<?php

require_once __DIR__ . '/../../app/Config/Database.php';

use StyleFitness\Config\Database;

class TestimonialsSeeder
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
            $currentTime = date('Y-m-d H:i:s');
            
            $testimonials = [
            [
                'name' => 'María González',
                'role' => 'Cliente Premium',
                'company' => 'STYLOFITNESS Lima',
                'image' => 'maria-gonzalez.jpg',
                'testimonial_text' => 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord. Los videos explicativos son increíbles y el seguimiento es muy detallado. He perdido 15 kg en 6 meses y me siento mejor que nunca.',
                'rating' => 5,
                'location' => 'Lima, Perú',
                'date_given' => '2024-01-15 00:00:00',
                'is_featured' => 1,
                'is_active' => 1,
                'display_order' => 1,
                'social_proof' => json_encode([
                    'instagram' => '@maria_fit_journey',
                    'verified' => true,
                    'membership_duration' => '8 meses',
                    'achievements' => ['Pérdida de peso', 'Mejora cardiovascular']
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'Carlos Rodríguez',
                'role' => 'Atleta Profesional',
                'company' => 'Club Deportivo Nacional',
                'image' => 'carlos-rodriguez.jpg',
                'testimonial_text' => 'La combinación de entrenamiento personalizado y suplementos recomendados ha transformado completamente mi rendimiento deportivo. Recomiendo STYLOFITNESS al 100%. Mi fuerza aumentó un 30% en 4 meses.',
                'rating' => 5,
                'location' => 'Arequipa, Perú',
                'date_given' => '2024-01-20 00:00:00',
                'is_featured' => 1,
                'is_active' => 1,
                'display_order' => 2,
                'social_proof' => json_encode([
                    'facebook' => 'Carlos Rodriguez Atleta',
                    'verified' => true,
                    'sport' => 'Powerlifting',
                    'achievements' => ['Récord nacional', 'Competencia internacional']
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'Ana Morales',
                'role' => 'Fitness Enthusiast',
                'company' => 'Freelancer',
                'image' => 'ana-morales.jpg',
                'testimonial_text' => 'Me encanta poder seguir mis rutinas desde casa con los videos HD. La tienda online tiene los mejores precios y la entrega es súper rápida. Las clases grupales virtuales son fantásticas, especialmente el yoga matutino.',
                'rating' => 5,
                'location' => 'Cusco, Perú',
                'date_given' => '2024-01-25 00:00:00',
                'is_featured' => 1,
                'is_active' => 1,
                'display_order' => 3,
                'social_proof' => json_encode([
                    'youtube' => 'Ana Fit Life',
                    'verified' => true,
                    'followers' => '15K',
                    'content_type' => 'Fitness vlogs'
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'Diego Fernández',
                'role' => 'Cliente VIP',
                'company' => 'Empresa Tecnológica',
                'image' => 'diego-fernandez.jpg',
                'testimonial_text' => 'El seguimiento personalizado y las clases grupales han hecho que entrenar sea adictivo. Los resultados están garantizados con este sistema. Como ejecutivo, valoro mucho la flexibilidad de horarios y la calidad del servicio.',
                'rating' => 5,
                'location' => 'Trujillo, Perú',
                'date_given' => '2024-01-30 00:00:00',
                'is_featured' => 1,
                'is_active' => 1,
                'display_order' => 4,
                'social_proof' => json_encode([
                    'linkedin' => 'Diego Fernández CEO',
                    'verified' => true,
                    'profession' => 'CEO',
                    'company_size' => '500+ empleados'
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'Lucía Vargas',
                'role' => 'Entrenadora Personal',
                'company' => 'Certificada ACSM',
                'image' => 'lucia-vargas.jpg',
                'testimonial_text' => 'Como profesional del fitness, puedo decir que STYLOFITNESS tiene el mejor sistema de rutinas que he visto. La tecnología es impresionante y los resultados de mis clientes han mejorado significativamente desde que uso esta plataforma.',
                'rating' => 5,
                'location' => 'Chiclayo, Perú',
                'date_given' => '2024-02-05 00:00:00',
                'is_featured' => 0,
                'is_active' => 1,
                'display_order' => 5,
                'social_proof' => json_encode([
                    'certification' => 'ACSM Certified',
                    'verified' => true,
                    'experience' => '8 años',
                    'specialization' => 'Entrenamiento funcional'
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'Roberto Silva',
                'role' => 'Empresario',
                'company' => 'Silva & Asociados',
                'image' => 'roberto-silva.jpg',
                'testimonial_text' => 'Perfecto para personas ocupadas como yo. Las rutinas se adaptan a mi horario y los resultados son visibles desde la primera semana. La app móvil es excelente para hacer seguimiento durante mis viajes de negocios.',
                'rating' => 5,
                'location' => 'Piura, Perú',
                'date_given' => '2024-02-10 00:00:00',
                'is_featured' => 0,
                'is_active' => 1,
                'display_order' => 6,
                'social_proof' => json_encode([
                    'business' => 'Silva & Asociados',
                    'verified' => true,
                    'industry' => 'Consultoría',
                    'travel_frequency' => 'Alto'
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'Carmen Delgado',
                'role' => 'Madre de Familia',
                'company' => 'Ama de Casa',
                'image' => 'carmen-delgado.jpg',
                'testimonial_text' => 'Después de tener a mis hijos pensé que nunca recuperaría mi forma física. STYLOFITNESS me demostró lo contrario. Las rutinas para mamás son perfectas y puedo entrenar mientras los niños duermen. ¡Increíble!',
                'rating' => 5,
                'location' => 'Iquitos, Perú',
                'date_given' => '2024-02-15 00:00:00',
                'is_featured' => 0,
                'is_active' => 1,
                'display_order' => 7,
                'social_proof' => json_encode([
                    'mom_community' => 'Mamás Fit Perú',
                    'verified' => true,
                    'children' => 2,
                    'transformation' => 'Post-parto'
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'Javier Mendoza',
                'role' => 'Estudiante Universitario',
                'company' => 'Universidad Nacional',
                'image' => 'javier-mendoza.jpg',
                'testimonial_text' => 'Como estudiante, el precio es muy accesible y la calidad es excelente. He ganado masa muscular y mejorado mi resistencia. Los compañeros de la universidad no pueden creer mi transformación en solo 3 meses.',
                'rating' => 5,
                'location' => 'Huancayo, Perú',
                'date_given' => '2024-02-20 00:00:00',
                'is_featured' => 0,
                'is_active' => 1,
                'display_order' => 8,
                'social_proof' => json_encode([
                    'university' => 'Universidad Nacional del Centro',
                    'verified' => true,
                    'student_discount' => true,
                    'study' => 'Ingeniería'
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ];
        
        foreach ($testimonials as $testimonial) {
            $sql = "INSERT INTO testimonials (name, role, company, image, testimonial_text, rating, location, date_given, is_featured, is_active, display_order, social_proof, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $this->db->query($sql, [
                $testimonial['name'],
                $testimonial['role'],
                $testimonial['company'],
                $testimonial['image'],
                $testimonial['testimonial_text'],
                $testimonial['rating'],
                $testimonial['location'],
                $testimonial['date_given'],
                $testimonial['is_featured'],
                $testimonial['is_active'],
                $testimonial['display_order'],
                $testimonial['social_proof'],
                $testimonial['created_at'],
                $testimonial['updated_at']
            ]);
        }
        
        echo "TestimonialsSeeder: Testimonios insertados correctamente.\n";
        
        } catch (Exception $e) {
            echo "Error en TestimonialsSeeder: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}