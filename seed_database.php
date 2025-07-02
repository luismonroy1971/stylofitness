<?php

/**
 * Script para ejecutar los seeders de STYLOFITNESS
 * Este archivo ejecuta todos los seeders necesarios para poblar la base de datos
 * con datos de ejemplo para las secciones de la landing page.
 */

require_once __DIR__ . '/app/Config/Database.php';

use StyleFitness\Config\Database;

echo "\n=== STYLOFITNESS - EJECUTANDO SEEDERS ===\n";
echo "Poblando la base de datos con datos de ejemplo...\n\n";

try {
    // Obtener conexión a la base de datos
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "✅ Conexión a la base de datos establecida\n\n";
    
    // Función para insertar datos de gimnasios
    function seedGyms($pdo) {
        echo "📊 Insertando datos de gimnasios...\n";
        
        $sql = "INSERT INTO gyms (name, address, phone, email, logo, theme_colors, settings, operating_hours, social_media, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        
        $gyms = [
            [
                'STYLOFITNESS Lima Centro',
                'Av. Javier Prado Este 1234, San Isidro, Lima',
                '+51 1 234-5678',
                'lima@stylofitness.com',
                'logo-lima.png',
                json_encode(['primary' => '#FF6B35', 'secondary' => '#2C3E50', 'accent' => '#F39C12']),
                json_encode(['currency' => 'PEN', 'timezone' => 'America/Lima', 'language' => 'es']),
                json_encode(['monday' => ['open' => '06:00', 'close' => '22:00'], 'tuesday' => ['open' => '06:00', 'close' => '22:00']]),
                json_encode(['facebook' => 'https://facebook.com/stylofitness', 'instagram' => 'https://instagram.com/stylofitness']),
                1
            ],
            [
                'STYLOFITNESS Miraflores',
                'Av. Larco 456, Miraflores, Lima',
                '+51 1 234-5679',
                'miraflores@stylofitness.com',
                'logo-miraflores.png',
                json_encode(['primary' => '#FF6B35', 'secondary' => '#2C3E50', 'accent' => '#E74C3C']),
                json_encode(['currency' => 'PEN', 'timezone' => 'America/Lima', 'language' => 'es']),
                json_encode(['monday' => ['open' => '05:30', 'close' => '23:00'], 'tuesday' => ['open' => '05:30', 'close' => '23:00']]),
                json_encode(['facebook' => 'https://facebook.com/stylofitness.miraflores', 'instagram' => 'https://instagram.com/stylofitness_miraflores']),
                1
            ]
        ];
        
        foreach ($gyms as $gym) {
            $stmt->execute($gym);
        }
        
        echo "✅ Gimnasios insertados correctamente\n";
    }
    
    // Función para insertar ofertas especiales
    function seedSpecialOffers($pdo) {
        echo "📊 Insertando ofertas especiales...\n";
        
        $sql = "INSERT INTO special_offers (title, subtitle, description, discount_percentage, image, button_text, button_link, start_date, end_date, is_active, display_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 1, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        
        $offers = [
            ['¡MEGA DESCUENTO!', 'Hasta 50% OFF en Suplementos', 'Aprovecha esta oferta limitada en nuestra selección premium de suplementos deportivos', 50.00, 'offer-supplements.jpg', 'Ver Ofertas', '/store?category=suplementos', 1],
            ['MEMBRESÍA PREMIUM', '3 Meses por el precio de 2', 'Acceso completo a todas nuestras instalaciones y clases grupales', 33.33, 'offer-membership.jpg', 'Suscribirse', '/membership', 2],
            ['RUTINAS PERSONALIZADAS', 'Gratis con tu primera compra', 'Recibe una rutina personalizada diseñada por nuestros expertos', 0.00, 'offer-routine.jpg', 'Empezar Ahora', '/routines', 3],
            ['CLASES GRUPALES', 'Primera semana GRATIS', 'Prueba todas nuestras clases grupales', 100.00, 'offer-classes.jpg', 'Reservar Clase', '/classes', 4],
            ['EVALUACIÓN NUTRICIONAL', '50% de descuento', 'Consulta con nuestros nutricionistas especializados', 50.00, 'offer-nutrition.jpg', 'Agendar Cita', '/nutrition', 5]
        ];
        
        foreach ($offers as $offer) {
            $stmt->execute($offer);
        }
        
        echo "✅ Ofertas especiales insertadas correctamente\n";
    }
    
    // Función para insertar características "Por qué elegirnos"
    function seedWhyChooseUs($pdo) {
        echo "📊 Insertando características 'Por qué elegirnos'...\n";
        
        $sql = "INSERT INTO why_choose_us (title, subtitle, description, icon, icon_color, highlights, stats, is_active, display_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        
        $features = [
            ['Rutinas Personalizadas', 'Entrenamientos inteligentes con IA', 'Entrenamientos diseñados específicamente para tus objetivos', 'fas fa-dumbbell', '#ff6b35', json_encode(['Videos HD Explicativos', 'Seguimiento en Tiempo Real', 'Ajustes Automáticos IA', 'Soporte 24/7']), json_encode(['exercises' => '1000+', 'success_rate' => '95%']), 1],
            ['Tienda Especializada', 'Los mejores suplementos del mercado', 'Productos certificados y de las marcas más reconocidas', 'fas fa-store', '#2c3e50', json_encode(['Productos Certificados', 'Envío Gratis', 'Garantía Total', 'Asesoría Nutricional']), json_encode(['products' => '500+', 'satisfaction' => '98%']), 2],
            ['Clases Grupales', 'Entrenamientos dinámicos y motivadores', 'Variedad de clases dirigidas por instructores certificados', 'fas fa-users', '#e74c3c', json_encode(['Instructores Certificados', 'Horarios Flexibles', 'Ambiente Motivador', 'Todos los Niveles']), json_encode(['classes' => '20+', 'instructors' => '15']), 3],
            ['Tecnología Avanzada', 'Seguimiento y análisis en tiempo real', 'Monitoreo completo de tu progreso con tecnología de vanguardia', 'fas fa-chart-line', '#f39c12', json_encode(['App Móvil', 'Análisis Detallado', 'Sincronización Cloud', 'Reportes Personalizados']), json_encode(['accuracy' => '99%', 'users' => '10000+']), 4]
        ];
        
        foreach ($features as $feature) {
            $stmt->execute($feature);
        }
        
        echo "✅ Características 'Por qué elegirnos' insertadas correctamente\n";
    }
    
    // Función para insertar testimonios
    function seedTestimonials($pdo) {
        echo "📊 Insertando testimonios...\n";
        
        $sql = "INSERT INTO testimonials (name, role, image, testimonial_text, rating, location, date_given, is_featured, is_active, display_order, social_proof, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        
        $testimonials = [
            ['María González', 'Cliente Premium', 'maria.jpg', 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord.', 5, 'Lima, Perú', '2024-01-15', 1, 1, json_encode(['verified' => true, 'membership_duration' => '8 meses'])],
            ['Carlos Rodríguez', 'Atleta Profesional', 'carlos.jpg', 'La combinación de entrenamiento personalizado y suplementos ha transformado mi rendimiento.', 5, 'Arequipa, Perú', '2024-01-20', 1, 2, json_encode(['verified' => true, 'sport' => 'Powerlifting'])],
            ['Ana Morales', 'Fitness Enthusiast', 'ana.jpg', 'Me encanta poder seguir mis rutinas desde casa con los videos HD.', 5, 'Cusco, Perú', '2024-01-25', 1, 3, json_encode(['verified' => true, 'followers' => '15K'])],
            ['Diego Fernández', 'Cliente VIP', 'diego.jpg', 'El seguimiento personalizado ha hecho que entrenar sea adictivo.', 5, 'Trujillo, Perú', '2024-01-30', 1, 4, json_encode(['verified' => true, 'profession' => 'CEO'])]
        ];
        
        foreach ($testimonials as $testimonial) {
            $stmt->execute($testimonial);
        }
        
        echo "✅ Testimonios insertados correctamente\n";
    }
    
    // Función para insertar configuración de landing page
    function seedLandingPageConfig($pdo) {
        echo "📊 Insertando configuración de landing page...\n";
        
        // Primero verificar si ya existen datos
        $checkSql = "SELECT COUNT(*) FROM landing_page_config";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();
        
        if ($count > 0) {
            echo "⚠️ La configuración de landing page ya existe, omitiendo inserción...\n";
            return;
        }
        
        $sql = "INSERT INTO landing_page_config (section_name, is_enabled, display_order, settings, created_at, updated_at) VALUES (?, 1, ?, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        
        $configs = [
            ['special_offers', 1, json_encode(['animation' => 'fadeIn', 'autoplay' => true, 'interval' => 5000])],
            ['why_choose_us', 2, json_encode(['animation' => 'slideUp', 'columns' => 3])],
            ['featured_products', 3, json_encode(['animation' => 'zoomIn', 'layout' => 'grid', 'items_per_row' => 4])],
            ['testimonials', 4, json_encode(['animation' => 'slideIn', 'autoplay' => true, 'show_ratings' => true])]
        ];
        
        foreach ($configs as $config) {
            $stmt->execute($config);
        }
        
        echo "✅ Configuración de landing page insertada correctamente\n";
    }
    
    // Ejecutar todos los seeders
    echo "🚀 Iniciando inserción de datos...\n\n";
    
    seedGyms($pdo);
    seedSpecialOffers($pdo);
    seedWhyChooseUs($pdo);
    seedTestimonials($pdo);
    seedLandingPageConfig($pdo);
    
    echo "\n🎉 ¡SEEDERS EJECUTADOS CORRECTAMENTE!\n";
    echo "\nDatos creados:\n";
    echo "- 2 Gimnasios de ejemplo\n";
    echo "- 5 Ofertas especiales\n";
    echo "- 4 Características 'Por qué elegirnos'\n";
    echo "- 4 Testimonios destacados\n";
    echo "- 4 Configuraciones de secciones\n";
    echo "\n🚀 La landing page ya tiene contenido para mostrar\n";
    
} catch (Exception $e) {
    echo "❌ Error ejecutando seeders: " . $e->getMessage() . "\n";
    echo "\nVerifica que:\n";
    echo "1. La base de datos esté configurada correctamente\n";
    echo "2. Las tablas estén creadas (ejecuta las migraciones primero)\n";
    echo "3. El archivo .env tenga la configuración correcta\n";
}

echo "\n=== FIN DE EJECUCIÓN ===\n";