-- ==========================================
-- TABLA DE TESTIMONIOS PARA STYLOFITNESS
-- ==========================================

USE `stylofitness_gym`;

-- Crear tabla de testimonios
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(100) DEFAULT 'Cliente',
  `email` varchar(255),
  `image` varchar(255),
  `testimonial_text` text NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `location` varchar(100) DEFAULT 'Lima, Perú',
  `results_achieved` json,
  `time_as_client` varchar(50),
  `is_featured` tinyint(1) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `approved_by` int(11),
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_approved` (`is_approved`),
  KEY `idx_active` (`is_active`),
  KEY `idx_display_order` (`display_order`),
  KEY `idx_approved_by` (`approved_by`),
  CONSTRAINT `fk_testimonials_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_testimonials_approver` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar testimonios de ejemplo
INSERT INTO `testimonials` (`name`, `role`, `image`, `testimonial_text`, `rating`, `location`, `results_achieved`, `time_as_client`, `is_featured`, `is_approved`, `display_order`, `approved_at`) VALUES
('María González', 'Cliente Premium', 'maria.jpg', 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord. Los videos explicativos son increíbles.', 5, 'San Isidro, Lima', JSON_OBJECT('weight_lost', '12kg', 'muscle_gained', '3kg', 'body_fat_reduced', '8%'), '8 meses', 1, 1, 1, NOW()),

('Carlos Rodríguez', 'Atleta', 'carlos.jpg', 'La combinación de entrenamiento y suplementos recomendados ha transformado completamente mi rendimiento deportivo.', 5, 'Miraflores, Lima', JSON_OBJECT('strength_increase', '35%', 'endurance_improvement', '40%', 'competition_wins', 3), '1 año', 1, 1, 2, NOW()),

('Ana Morales', 'Fitness Enthusiast', 'ana.jpg', 'Me encanta poder seguir mis rutinas desde casa con los videos HD. La tienda online tiene los mejores precios.', 5, 'La Molina, Lima', JSON_OBJECT('consistency_months', 6, 'flexibility_improvement', '50%', 'energy_level', 'excellent'), '6 meses', 1, 1, 3, NOW()),

('Diego Fernández', 'Cliente VIP', 'diego.jpg', 'El seguimiento personalizado y las clases grupales han hecho que entrenar sea adictivo. Resultados garantizados.', 5, 'Surco, Lima', JSON_OBJECT('body_transformation', 'complete', 'confidence_boost', '100%', 'lifestyle_change', true), '10 meses', 1, 1, 4, NOW()),

('Lucía Mendoza', 'Madre Deportista', 'lucia.jpg', 'Después del embarazo, STYLOFITNESS me ayudó a recuperar mi forma física de manera segura y efectiva.', 5, 'San Borja, Lima', JSON_OBJECT('post_pregnancy_recovery', '100%', 'core_strength', 'excellent', 'energy_for_kids', 'unlimited'), '4 meses', 1, 1, 5, NOW()),

('Roberto Silva', 'Ejecutivo Senior', 'roberto.jpg', 'Con mi horario ocupado, las rutinas personalizadas y la flexibilidad de horarios fueron perfectas para mantenerme en forma.', 5, 'San Isidro, Lima', JSON_OBJECT('stress_reduction', '70%', 'work_performance', 'improved', 'sleep_quality', 'excellent'), '1.5 años', 1, 1, 6, NOW());

-- Índices adicionales para optimización
CREATE INDEX idx_testimonials_featured_approved ON testimonials(is_featured, is_approved, is_active);
CREATE INDEX idx_testimonials_display_order ON testimonials(display_order, is_active);

-- Vista para testimonios activos
CREATE VIEW v_active_testimonials AS
SELECT 
    id,
    name,
    role,
    image,
    testimonial_text,
    rating,
    location,
    results_achieved,
    time_as_client,
    is_featured,
    display_order,
    created_at
FROM testimonials 
WHERE is_approved = 1 AND is_active = 1
ORDER BY display_order ASC, created_at DESC;
