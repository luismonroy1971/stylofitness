<?php

require_once __DIR__ . '/../../app/Config/Database.php';

use StyleFitness\Config\Database;

class LandingPageConfigSeeder
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
            
            $configs = [
            [
                'section_name' => 'special_offers',
                'is_enabled' => 1,
                'display_order' => 1,
                'custom_css' => '.special-offers { background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); }',
                'custom_js' => 'console.log("Special offers section loaded");',
                'config_data' => json_encode([
                    'animation' => 'fadeIn',
                    'autoplay' => true,
                    'interval' => 5000,
                    'show_countdown' => true,
                    'max_visible' => 3,
                    'responsive' => [
                        'mobile' => ['items' => 1],
                        'tablet' => ['items' => 2],
                        'desktop' => ['items' => 3]
                    ]
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'section_name' => 'why_choose_us',
                'is_enabled' => 1,
                'display_order' => 2,
                'custom_css' => '.why-choose-us .feature-card { transition: transform 0.3s ease; } .why-choose-us .feature-card:hover { transform: translateY(-10px); }',
                'custom_js' => 'document.addEventListener("DOMContentLoaded", function() { console.log("Why choose us section initialized"); });',
                'config_data' => json_encode([
                    'animation' => 'slideUp',
                    'columns' => 3,
                    'show_stats' => true,
                    'show_icons' => true,
                    'layout' => 'grid',
                    'hover_effects' => true,
                    'background_style' => 'gradient',
                    'responsive' => [
                        'mobile' => ['columns' => 1],
                        'tablet' => ['columns' => 2],
                        'desktop' => ['columns' => 3]
                    ]
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'section_name' => 'featured_products',
                'is_enabled' => 1,
                'display_order' => 3,
                'custom_css' => '.featured-products .product-card { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 12px; }',
                'custom_js' => 'function initFeaturedProducts() { console.log("Featured products loaded"); }',
                'config_data' => json_encode([
                    'animation' => 'zoomIn',
                    'layout' => 'grid',
                    'items_per_row' => 4,
                    'show_prices' => true,
                    'show_ratings' => true,
                    'show_badges' => true,
                    'enable_quick_view' => true,
                    'enable_wishlist' => true,
                    'responsive' => [
                        'mobile' => ['items_per_row' => 1],
                        'tablet' => ['items_per_row' => 2],
                        'desktop' => ['items_per_row' => 4]
                    ]
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'section_name' => 'group_classes',
                'is_enabled' => 1,
                'display_order' => 4,
                'custom_css' => '.group-classes { background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url("/images/gym-bg.jpg"); background-size: cover; }',
                'custom_js' => 'function loadClassSchedule() { console.log("Class schedule loaded"); }',
                'config_data' => json_encode([
                    'animation' => 'fadeInUp',
                    'show_schedule' => true,
                    'show_instructors' => true,
                    'show_difficulty' => true,
                    'enable_booking' => true,
                    'max_classes_shown' => 6,
                    'filter_by_day' => true,
                    'responsive' => [
                        'mobile' => ['layout' => 'list'],
                        'tablet' => ['layout' => 'grid'],
                        'desktop' => ['layout' => 'grid']
                    ]
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'section_name' => 'testimonials',
                'is_enabled' => 1,
                'display_order' => 5,
                'custom_css' => '.testimonials .testimonial-card { background: white; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); }',
                'custom_js' => 'function initTestimonialSlider() { console.log("Testimonial slider initialized"); }',
                'config_data' => json_encode([
                    'animation' => 'slideIn',
                    'autoplay' => true,
                    'show_ratings' => true,
                    'show_photos' => true,
                    'show_social_proof' => true,
                    'slider_speed' => 3000,
                    'items_per_slide' => 3,
                    'enable_navigation' => true,
                    'enable_pagination' => true,
                    'responsive' => [
                        'mobile' => ['items_per_slide' => 1],
                        'tablet' => ['items_per_slide' => 2],
                        'desktop' => ['items_per_slide' => 3]
                    ]
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'section_name' => 'hero_banner',
                'is_enabled' => 1,
                'display_order' => 0,
                'custom_css' => '.hero-banner { min-height: 100vh; background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); }',
                'custom_js' => 'function initHeroBanner() { console.log("Hero banner loaded"); }',
                'config_data' => json_encode([
                    'animation' => 'fadeIn',
                    'show_video' => false,
                    'show_cta_buttons' => true,
                    'show_stats' => true,
                    'parallax_effect' => true,
                    'auto_scroll' => false,
                    'background_type' => 'gradient',
                    'responsive' => [
                        'mobile' => ['min_height' => '70vh'],
                        'tablet' => ['min_height' => '80vh'],
                        'desktop' => ['min_height' => '100vh']
                    ]
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'section_name' => 'contact_form',
                'is_enabled' => 1,
                'display_order' => 6,
                'custom_css' => '.contact-form { background: #f8f9fa; padding: 60px 0; }',
                'custom_js' => 'function validateContactForm() { console.log("Contact form validation loaded"); }',
                'config_data' => json_encode([
                    'animation' => 'fadeInUp',
                    'show_map' => true,
                    'show_contact_info' => true,
                    'enable_captcha' => true,
                    'required_fields' => ['name', 'email', 'message'],
                    'success_redirect' => '/thank-you',
                    'email_notifications' => true,
                    'responsive' => [
                        'mobile' => ['layout' => 'stacked'],
                        'tablet' => ['layout' => 'side-by-side'],
                        'desktop' => ['layout' => 'side-by-side']
                    ]
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'section_name' => 'footer',
                'is_enabled' => 1,
                'display_order' => 7,
                'custom_css' => '.footer { background: #2c3e50; color: white; }',
                'custom_js' => 'function initFooter() { console.log("Footer initialized"); }',
                'config_data' => json_encode([
                    'show_social_links' => true,
                    'show_newsletter' => true,
                    'show_quick_links' => true,
                    'show_contact_info' => true,
                    'show_logo' => true,
                    'copyright_text' => '© 2024 STYLOFITNESS. Todos los derechos reservados.',
                    'responsive' => [
                        'mobile' => ['layout' => 'stacked'],
                        'tablet' => ['layout' => 'grid'],
                        'desktop' => ['layout' => 'grid']
                    ]
                ]),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ];
        
        foreach ($configs as $config) {
            $sql = "INSERT INTO landing_page_config (section_name, is_enabled, display_order, custom_css, custom_js, config_data, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $this->db->query($sql, [
                $config['section_name'],
                $config['is_enabled'],
                $config['display_order'],
                $config['custom_css'],
                $config['custom_js'],
                $config['config_data'],
                $config['created_at'],
                $config['updated_at']
            ]);
        }
        
        echo "LandingPageConfigSeeder: Configuraciones insertadas correctamente.\n";
        
        } catch (Exception $e) {
            echo "Error en LandingPageConfigSeeder: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}