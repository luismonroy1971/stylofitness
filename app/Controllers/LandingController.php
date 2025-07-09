<?php

namespace StyleFitness\Controllers;

use StyleFitness\Models\SpecialOffer;
use StyleFitness\Models\WhyChooseUs;
use StyleFitness\Models\Testimonial;
use StyleFitness\Models\GroupClass;
use StyleFitness\Models\LandingPageConfig;
use StyleFitness\Models\Product;
use StyleFitness\Models\Gym;
use StyleFitness\Helpers\AppHelper;

/**
 * Controlador para gestionar las secciones de la landing page
 * STYLOFITNESS - Sistema de Gestión de Contenido
 */
class LandingController
{
    private $specialOfferModel;
    private $whyChooseUsModel;
    private $testimonialModel;
    private $groupClassModel;
    private $landingConfigModel;
    private $productModel;
    private $gymModel;

    public function __construct()
    {
        $this->specialOfferModel = new SpecialOffer();
        $this->whyChooseUsModel = new WhyChooseUs();
        $this->testimonialModel = new Testimonial();
        $this->groupClassModel = new GroupClass();
        $this->landingConfigModel = new LandingPageConfig();
        $this->productModel = new Product();
        $this->gymModel = new Gym();
    }

    /**
     * Obtener todos los datos para la landing page
     */
    public function getLandingData()
    {
        try {
            $data = [
                // Ofertas especiales
                'special_offers' => $this->specialOfferModel->getActiveOffers(6),
                
                // Por qué elegirnos
                'why_choose_us' => $this->whyChooseUsModel->getActiveFeatures(6),
                
                // Productos destacados (todos los disponibles)
                'featured_products' => $this->productModel->getFeaturedProducts(),
                
                // Clases grupales próximas
                'upcoming_classes' => $this->groupClassModel->getUpcomingClasses(6),
                
                // Testimonios destacados
                'testimonials' => $this->testimonialModel->getFeaturedTestimonials(4),
                
                // Configuración general de la landing
                'landing_config' => $this->landingConfigModel->getAllActiveConfig(),
                
                // Estadísticas del gimnasio
                'stats' => $this->getGymStats(),
                
                // Información de gimnasios
                'gyms' => $this->gymModel->getActiveGyms()
            ];

            return $data;
        } catch (\Exception $e) {
            error_log("Error en getLandingData: " . $e->getMessage());
            return $this->getDefaultLandingData();
        }
    }

    /**
     * Obtener datos por defecto en caso de error
     */
    private function getDefaultLandingData()
    {
        return [
            'special_offers' => [],
            'why_choose_us' => [],
            'featured_products' => [],
            'upcoming_classes' => [],
            'testimonials' => [],
            'landing_config' => [],
            'stats' => [
                [
                    'value' => '1,500',
                    'label' => 'Miembros Activos'
                ],
                [
                    'value' => '50',
                    'label' => 'Clases Disponibles'
                ],
                [
                    'value' => '25',
                    'label' => 'Entrenadores Expertos'
                ],
                [
                    'value' => '98%',
                    'label' => 'Satisfacción'
                ]
            ],
            'gyms' => []
        ];
    }

    /**
     * Obtener estadísticas del gimnasio
     */
    public function getGymStats()
    {
        try {
            // Obtener estadísticas reales de la base de datos
            $rawStats = [
                'total_members' => $this->getTotalMembers(),
                'total_classes' => $this->getTotalClasses(),
                'total_trainers' => $this->getTotalTrainers(),
                'satisfaction_rate' => $this->getSatisfactionRate(),
                'total_gyms' => $this->getTotalGyms(),
                'total_products' => $this->getTotalProducts()
            ];
        } catch (\Exception $e) {
            error_log("Error en getGymStats: " . $e->getMessage());
            $rawStats = [
                'total_members' => 1500,
                'total_classes' => 50,
                'total_trainers' => 25,
                'satisfaction_rate' => 98,
                'total_gyms' => 3,
                'total_products' => 150
            ];
        }
        
        // Formatear estadísticas para la vista
        return [
            [
                'value' => number_format($rawStats['total_members']),
                'label' => 'Miembros Activos'
            ],
            [
                'value' => number_format($rawStats['total_classes']),
                'label' => 'Clases Disponibles'
            ],
            [
                'value' => number_format($rawStats['total_trainers']),
                'label' => 'Entrenadores Expertos'
            ],
            [
                'value' => $rawStats['satisfaction_rate'] . '%',
                'label' => 'Satisfacción'
            ]
        ];
    }

    /**
     * Obtener ofertas especiales activas
     */
    public function getSpecialOffers($limit = 6)
    {
        return $this->specialOfferModel->getActiveOffers($limit);
    }

    /**
     * Obtener características "Por qué elegirnos"
     */
    public function getWhyChooseUsFeatures($limit = 6)
    {
        return $this->whyChooseUsModel->getActiveFeatures($limit);
    }

    /**
     * Obtener productos destacados
     */
    public function getFeaturedProducts($limit = null)
    {
        return $this->productModel->getFeaturedProducts($limit);
    }

    /**
     * Obtener clases grupales próximas
     */
    public function getUpcomingClasses($limit = 6)
    {
        return $this->groupClassModel->getUpcomingClasses($limit);
    }

    /**
     * Obtener testimonios destacados
     */
    public function getFeaturedTestimonials($limit = 4)
    {
        return $this->testimonialModel->getFeaturedTestimonials($limit);
    }

    /**
     * Obtener configuración de una sección específica
     */
    public function getSectionConfig($section)
    {
        return $this->landingConfigModel->getBySection($section);
    }

    /**
     * Actualizar configuración de una sección
     */
    public function updateSectionConfig($section, $config_data)
    {
        return $this->landingConfigModel->updateConfigData($section, $config_data);
    }

    /**
     * Obtener datos para el hero/banner principal
     */
    public function getHeroData()
    {
        $config = $this->landingConfigModel->getHeroConfig();
        $stats = $this->getGymStats();
        
        return [
            'config' => $config,
            'stats' => $stats,
            'featured_classes' => $this->groupClassModel->getUpcomingClasses(3)
        ];
    }

    /**
     * Obtener datos para la sección de servicios
     */
    public function getServicesData()
    {
        return [
            'why_choose_us' => $this->whyChooseUsModel->getActiveFeatures(),
            'config' => $this->landingConfigModel->getFeaturesConfig()
        ];
    }

    /**
     * Obtener datos para la sección de productos
     */
    public function getProductsData()
    {
        return [
            'featured_products' => $this->productModel->getFeaturedProducts(8),
            'special_offers' => $this->specialOfferModel->getActiveOffers(4),
            'categories' => $this->productModel->getActiveCategories()
        ];
    }

    /**
     * Obtener datos para la sección de clases
     */
    public function getClassesData()
    {
        return [
            'upcoming_classes' => $this->groupClassModel->getUpcomingClasses(6),
            'popular_classes' => $this->groupClassModel->getPopularClasses(4),
            'class_stats' => $this->groupClassModel->getStats()
        ];
    }

    /**
     * Obtener datos para la sección de testimonios
     */
    public function getTestimonialsData()
    {
        return [
            'featured_testimonials' => $this->testimonialModel->getFeaturedTestimonials(6),
            'recent_testimonials' => $this->testimonialModel->getRecent(30, 4),
            'testimonial_stats' => $this->testimonialModel->getStats()
        ];
    }

    /**
     * Métodos auxiliares para estadísticas
     */
    private function getTotalMembers()
    {
        // Implementar lógica para contar miembros reales
        // Por ahora retornamos un valor fijo
        return 1500;
    }

    private function getTotalClasses()
    {
        $stats = $this->groupClassModel->getStats();
        return $stats['total_classes'] ?? 50;
    }

    private function getTotalTrainers()
    {
        // Implementar lógica para contar entrenadores
        // Por ahora retornamos un valor fijo
        return 25;
    }

    private function getSatisfactionRate()
    {
        $stats = $this->testimonialModel->getStats();
        $avgRating = $stats['average_rating'] ?? 4.9;
        return round(($avgRating / 5) * 100);
    }

    private function getTotalGyms()
    {
        return count($this->gymModel->getActiveGyms());
    }

    private function getTotalProducts()
    {
        // Implementar método en ProductModel si no existe
        return 150; // Valor temporal
    }

    /**
     * Buscar contenido en todas las secciones
     */
    public function searchContent($term, $section = null)
    {
        $results = [];

        if (!$section || $section === 'offers') {
            $results['offers'] = $this->specialOfferModel->search($term, 5);
        }

        if (!$section || $section === 'features') {
            $results['features'] = $this->whyChooseUsModel->search($term, 5);
        }

        if (!$section || $section === 'testimonials') {
            $results['testimonials'] = $this->testimonialModel->search($term, 5);
        }

        if (!$section || $section === 'classes') {
            $results['classes'] = $this->groupClassModel->search($term, 5);
        }

        return $results;
    }

    /**
     * Obtener resumen de todas las secciones para administración
     */
    public function getAdminSummary()
    {
        return [
            'special_offers' => [
                'total' => count($this->specialOfferModel->getActiveOffers()),
                'active' => count($this->specialOfferModel->getActiveOffers()),
                'featured' => count($this->specialOfferModel->getFeaturedOffers())
            ],
            'why_choose_us' => [
                'total' => count($this->whyChooseUsModel->getActiveFeatures()),
                'active' => count($this->whyChooseUsModel->getActiveFeatures())
            ],
            'testimonials' => $this->testimonialModel->getStats(),
            'classes' => $this->groupClassModel->getStats(),
            'landing_config' => $this->landingConfigModel->getStats()
        ];
    }

    /**
     * Validar y sanitizar datos de entrada
     */
    private function validateAndSanitize($data, $type)
    {
        // Implementar validación específica según el tipo de datos
        switch ($type) {
            case 'offer':
                return $this->validateOfferData($data);
            case 'feature':
                return $this->validateFeatureData($data);
            case 'testimonial':
                return $this->validateTestimonialData($data);
            default:
                return $data;
        }
    }

    private function validateOfferData($data)
    {
        // Validación específica para ofertas
        return array_filter($data, function($value, $key) {
            return !empty($value) || $key === 'description';
        }, ARRAY_FILTER_USE_BOTH);
    }

    private function validateFeatureData($data)
    {
        // Validación específica para características
        return array_filter($data, function($value, $key) {
            return !empty($value) || in_array($key, ['description', 'additional_info']);
        }, ARRAY_FILTER_USE_BOTH);
    }

    private function validateTestimonialData($data)
    {
        // Validación específica para testimonios
        $data['rating'] = max(1, min(5, intval($data['rating'] ?? 5)));
        return $data;
    }
}