<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\Product;
use StyleFitness\Models\ProductCategory;
use StyleFitness\Controllers\LandingController;
use StyleFitness\Helpers\AppHelper;
use Exception;

/**
 * Controlador Principal - STYLOFITNESS
 * Maneja la página de inicio y navegación principal
 */

class HomeController
{
    private $db;
    private $landingController;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->landingController = new LandingController();
    }

    public function index()
    {
        // Headers anti-caché agresivos para prevenir cacheo en navegadores
        if (!headers_sent()) {
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0, private');
            header('Pragma: no-cache');
            header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Vary: *');
            header('X-Accel-Expires: 0');
        }
        
        try {
            // Obtener todos los datos de la landing page usando el nuevo controlador
            $landingData = $this->landingController->getLandingData();
            
            // Mantener compatibilidad con variables existentes en la vista
            $featuredProducts = $landingData['featured_products'];
            $upcomingClasses = $landingData['upcoming_classes'];
            $testimonials = $landingData['testimonials'];
            $stats = $landingData['stats'];
            $promotionalProducts = $landingData['special_offers'];
            
            // Nuevas variables para las secciones mejoradas
            $specialOffers = $landingData['special_offers'];
            $whyChooseUs = $landingData['why_choose_us'];
            $landingConfig = $landingData['landing_config'];
            $gyms = $landingData['gyms'];
            
            // Incluir la vista principal
            $pageTitle = 'Inicio - STYLOFITNESS';
            $pageDescription = 'Gimnasio profesional con rutinas personalizadas y tienda de suplementos';

            include APP_PATH . '/Views/layout/header.php';
            include APP_PATH . '/Views/home/index.php';
            include APP_PATH . '/Views/layout/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en HomeController::index: " . $e->getMessage());
            
            // Fallback a datos por defecto
            $this->indexFallback();
        }
    }
    
    /**
     * Método de respaldo en caso de error
     */
    private function indexFallback()
    {
        $featuredProducts = $this->getFeaturedProducts();
        $upcomingClasses = $this->getUpcomingClasses();
        $testimonials = $this->getTestimonials();
        $stats = $this->getGymStats();
        $promotionalProducts = $this->getPromotionalProducts();
        
        // Variables vacías para las nuevas secciones
        $specialOffers = [];
        $whyChooseUs = [];
        $landingConfig = [];
        $gyms = [];
        
        $pageTitle = 'Inicio - STYLOFITNESS';
        $pageDescription = 'Gimnasio profesional con rutinas personalizadas y tienda de suplementos';

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/home/index.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    private function getFeaturedProducts($limit = 8)
    {
        $sql = 'SELECT p.*, pc.name as category_name 
                FROM products p 
                LEFT JOIN product_categories pc ON p.category_id = pc.id 
                WHERE p.is_featured = 1 AND p.is_active = 1 
                ORDER BY p.created_at DESC 
                LIMIT ?';

        return $this->db->fetchAll($sql, [$limit]);
    }

    private function getPromotionalProducts($limit = 10)
    {
        $sql = 'SELECT p.*, pc.name as category_name,
                ROUND(((p.price - p.sale_price) / p.price) * 100) as discount_percentage
                FROM products p 
                LEFT JOIN product_categories pc ON p.category_id = pc.id 
                WHERE p.sale_price IS NOT NULL 
                AND p.sale_price < p.price 
                AND p.is_active = 1 
                ORDER BY discount_percentage DESC 
                LIMIT ?';

        return $this->db->fetchAll($sql, [$limit]);
    }

    private function getUpcomingClasses($limit = 6)
    {
        $sql = "SELECT gc.id, gc.name, gc.description, gc.duration_minutes, gc.max_participants,
                cs.id as schedule_id, cs.day_of_week, cs.start_time, cs.end_time,
                u.first_name, u.last_name,
                g.name as gym_name, g.address as gym_address,
                COUNT(cb.id) as booked_spots
                FROM group_classes gc
                JOIN class_schedules cs ON gc.id = cs.class_id
                JOIN users u ON gc.instructor_id = u.id
                JOIN gyms g ON gc.gym_id = g.id
                LEFT JOIN class_bookings cb ON cs.id = cb.schedule_id 
                    AND cb.booking_date >= CURDATE() 
                    AND cb.status = 'booked'
                WHERE gc.is_active = 1 AND cs.is_active = 1
                GROUP BY gc.id, cs.id
                ORDER BY 
                    CASE cs.day_of_week 
                        WHEN 'monday' THEN 1
                        WHEN 'tuesday' THEN 2
                        WHEN 'wednesday' THEN 3
                        WHEN 'thursday' THEN 4
                        WHEN 'friday' THEN 5
                        WHEN 'saturday' THEN 6
                        WHEN 'sunday' THEN 7
                    END,
                    cs.start_time
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    private function getTestimonials($limit = 4)
    {
        // Testimonios estáticos por ahora, después se pueden mover a base de datos
        return [
            [
                'name' => 'María González',
                'role' => 'Cliente Premium',
                'image' => 'maria.jpg',
                'text' => 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord. Los videos explicativos son increíbles.',
                'rating' => 5,
            ],
            [
                'name' => 'Carlos Rodríguez',
                'role' => 'Atleta',
                'image' => 'carlos.jpg',
                'text' => 'La combinación de entrenamiento y suplementos recomendados ha transformado completamente mi rendimiento deportivo.',
                'rating' => 5,
            ],
            [
                'name' => 'Ana Morales',
                'role' => 'Fitness Enthusiast',
                'image' => 'ana.jpg',
                'text' => 'Me encanta poder seguir mis rutinas desde casa con los videos HD. La tienda online tiene los mejores precios.',
                'rating' => 5,
            ],
            [
                'name' => 'Diego Fernández',
                'role' => 'Cliente VIP',
                'image' => 'diego.jpg',
                'text' => 'El seguimiento personalizado y las clases grupales han hecho que entrenar sea adictivo. Resultados garantizados.',
                'rating' => 5,
            ],
        ];
    }

    private function getGymStats()
    {
        $rawStats = [];

        // Total de clientes activos
        $rawStats['active_clients'] = $this->db->count(
            "SELECT COUNT(*) FROM users WHERE role = 'client' AND is_active = 1"
        );

        // Total de rutinas creadas
        $rawStats['total_routines'] = $this->db->count(
            'SELECT COUNT(*) FROM routines WHERE is_active = 1'
        );

        // Total de productos en tienda
        $rawStats['total_products'] = $this->db->count(
            'SELECT COUNT(*) FROM products WHERE is_active = 1'
        );

        // Clases disponibles esta semana
        $rawStats['weekly_classes'] = $this->db->count(
            'SELECT COUNT(*) FROM class_schedules cs 
             JOIN group_classes gc ON cs.class_id = gc.id 
             WHERE cs.is_active = 1 AND gc.is_active = 1'
        );

        // Formatear estadísticas para la vista
        return [
            [
                'value' => number_format($rawStats['active_clients']),
                'label' => 'Clientes Activos'
            ],
            [
                'value' => number_format($rawStats['total_routines']),
                'label' => 'Rutinas Creadas'
            ],
            [
                'value' => number_format($rawStats['total_products']),
                'label' => 'Productos Disponibles'
            ],
            [
                'value' => number_format($rawStats['weekly_classes']),
                'label' => 'Clases Semanales'
            ]
        ];
    }

    public function dashboard()
    {
        // Verificar si el usuario está logueado
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        $dashboardData = [];

        switch ($user['role']) {
            case 'admin':
                $dashboardData = $this->getAdminDashboardData();
                break;
            case 'instructor':
            case 'trainer':
                $dashboardData = $this->getInstructorDashboardData($user['id']);
                break;
            case 'staff':
                $dashboardData = $this->getStaffDashboardData($user['id']);
                break;
            case 'client':
                $dashboardData = $this->getClientDashboardData($user['id']);
                break;
        }

        $pageTitle = 'Dashboard - STYLOFITNESS';
        include APP_PATH . '/Views/layout/header.php';
        
        // Mapear roles a archivos de vista
        $viewFile = $user['role'];
        if ($user['role'] === 'trainer') {
            $viewFile = 'instructor'; // Los trainers usan la misma vista que instructors
        }
        
        include APP_PATH . '/Views/dashboard/' . $viewFile . '.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    private function getAdminDashboardData()
    {
        return [
            'total_users' => $this->db->count('SELECT COUNT(*) FROM users WHERE is_active = 1'),
            'monthly_revenue' => $this->db->fetch(
                'SELECT SUM(total_amount) as revenue FROM orders 
                 WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
                 AND YEAR(created_at) = YEAR(CURRENT_DATE())'
            )['revenue'] ?? 0,
            'pending_orders' => $this->db->count(
                "SELECT COUNT(*) FROM orders WHERE status = 'pending'"
            ),
            'active_memberships' => $this->db->count(
                "SELECT COUNT(*) FROM users 
                 WHERE role = 'client' AND membership_expires > CURDATE()"
            ),
        ];
    }

    private function getInstructorDashboardData($instructorId)
    {
        return [
            'assigned_clients' => $this->db->count(
                'SELECT COUNT(DISTINCT client_id) FROM routines WHERE instructor_id = ?',
                [$instructorId]
            ),
            'active_routines' => $this->db->count(
                'SELECT COUNT(*) FROM routines WHERE instructor_id = ? AND is_active = 1',
                [$instructorId]
            ),
            'scheduled_classes' => $this->db->count(
                'SELECT COUNT(*) FROM group_classes gc
                 JOIN class_schedules cs ON gc.id = cs.class_id
                 WHERE gc.instructor_id = ? AND cs.is_active = 1',
                [$instructorId]
            ),
            'this_week_sessions' => $this->db->count(
                'SELECT COUNT(*) FROM class_bookings cb
                 JOIN class_schedules cs ON cb.schedule_id = cs.id
                 JOIN group_classes gc ON cs.class_id = gc.id
                 WHERE gc.instructor_id = ? 
                 AND cb.booking_date BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) 
                 AND DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 6 DAY)',
                [$instructorId]
            ),
        ];
    }

    private function getClientDashboardData($clientId)
    {
        return [
            'active_routine' => $this->db->fetch(
                'SELECT * FROM routines WHERE client_id = ? AND is_active = 1 ORDER BY created_at DESC LIMIT 1',
                [$clientId]
            ),
            'completed_workouts' => $this->db->count(
                'SELECT COUNT(*) FROM workout_logs WHERE user_id = ?',
                [$clientId]
            ),
            'upcoming_classes' => $this->db->fetchAll(
                "SELECT gc.name, cs.day_of_week, cs.start_time, cb.booking_date
                 FROM class_bookings cb
                 JOIN class_schedules cs ON cb.schedule_id = cs.id
                 JOIN group_classes gc ON cs.class_id = gc.id
                 WHERE cb.user_id = ? AND cb.booking_date >= CURDATE() AND cb.status = 'booked'
                 ORDER BY cb.booking_date, cs.start_time LIMIT 5",
                [$clientId]
            ),
            'recent_orders' => $this->db->fetchAll(
                'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 3',
                [$clientId]
            ),
        ];
    }

    private function getStaffDashboardData($staffId)
    {
        return [
            'total_members' => $this->db->count(
                "SELECT COUNT(*) FROM users WHERE role = 'client' AND is_active = 1"
            ),
            'pending_orders' => $this->db->count(
                "SELECT COUNT(*) FROM orders WHERE status = 'pending'"
            ),
            'todays_classes' => $this->db->count(
                'SELECT COUNT(*) FROM class_schedules cs
                 JOIN group_classes gc ON cs.class_id = gc.id
                 WHERE cs.day_of_week = LOWER(DAYNAME(CURDATE())) AND cs.is_active = 1'
            ),
            'inventory_alerts' => $this->db->count(
                'SELECT COUNT(*) FROM products WHERE stock_quantity <= 10 AND is_active = 1'
            ),
            'recent_registrations' => $this->db->fetchAll(
                "SELECT first_name, last_name, email, created_at 
                 FROM users 
                 WHERE role = 'client' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                 ORDER BY created_at DESC LIMIT 5"
            ),
        ];
    }
}
