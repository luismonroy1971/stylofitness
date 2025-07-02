-- ==========================================
-- MIGRACIONES PARA SECCIONES DE LANDING PAGE
-- STYLOFITNESS - Sistema de Gestión de Contenido
-- ==========================================

-- Tabla para gestionar ofertas especiales
CREATE TABLE IF NOT EXISTS `special_offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(500),
  `description` text,
  `discount_percentage` decimal(5,2),
  `discount_amount` decimal(10,2),
  `image` varchar(255),
  `background_color` varchar(7) DEFAULT '#ff6b35',
  `text_color` varchar(7) DEFAULT '#ffffff',
  `button_text` varchar(100) DEFAULT 'Ver Oferta',
  `button_link` varchar(500),
  `start_date` datetime,
  `end_date` datetime,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_offers_active` (`is_active`),
  KEY `idx_offers_dates` (`start_date`, `end_date`),
  KEY `idx_offers_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para características "Por qué elegirnos"
CREATE TABLE IF NOT EXISTS `why_choose_us` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(500),
  `description` text,
  `icon` varchar(100), -- Clase de FontAwesome
  `icon_color` varchar(7) DEFAULT '#ff6b35',
  `background_gradient` varchar(100),
  `highlights` json, -- Array de características destacadas
  `stats` json, -- Estadísticas relacionadas
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_features_active` (`is_active`),
  KEY `idx_features_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para productos destacados (configuración)
CREATE TABLE IF NOT EXISTS `featured_products_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_title` varchar(255) DEFAULT 'Productos Destacados',
  `section_subtitle` varchar(500),
  `max_products` int(11) DEFAULT 8,
  `display_type` enum('grid', 'carousel', 'masonry') DEFAULT 'grid',
  `auto_select` tinyint(1) DEFAULT 1, -- Si selecciona automáticamente productos destacados
  `selection_criteria` json, -- Criterios para selección automática
  `background_style` varchar(100),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para testimonios
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role` varchar(255),
  `company` varchar(255),
  `image` varchar(255),
  `testimonial_text` text NOT NULL,
  `rating` tinyint(1) DEFAULT 5 CHECK (rating >= 1 AND rating <= 5),
  `location` varchar(255),
  `date_given` date,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `social_proof` json, -- Links a redes sociales, verificaciones, etc.
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_testimonials_active` (`is_active`),
  KEY `idx_testimonials_featured` (`is_featured`),
  KEY `idx_testimonials_order` (`display_order`),
  KEY `idx_testimonials_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para configuración general de la landing page
CREATE TABLE IF NOT EXISTS `landing_page_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(100) NOT NULL UNIQUE,
  `is_enabled` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `custom_css` text,
  `custom_js` text,
  `settings` json,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_section` (`section_name`),
  KEY `idx_landing_enabled` (`is_enabled`),
  KEY `idx_landing_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- DATOS INICIALES
-- ==========================================

-- Insertar configuración inicial de secciones
INSERT INTO `landing_page_config` (`section_name`, `is_enabled`, `display_order`, `settings`) VALUES
('special_offers', 1, 1, '{"animation": "fadeIn", "autoplay": true, "interval": 5000}'),
('why_choose_us', 1, 2, '{"animation": "slideUp", "columns": 3}'),
('featured_products', 1, 3, '{"animation": "zoomIn", "layout": "grid", "items_per_row": 4}'),
('group_classes', 1, 4, '{"animation": "fadeInUp", "show_schedule": true}'),
('testimonials', 1, 5, '{"animation": "slideIn", "autoplay": true, "show_ratings": true}');

-- Insertar ofertas especiales de ejemplo
INSERT INTO `special_offers` (`title`, `subtitle`, `description`, `discount_percentage`, `image`, `button_text`, `button_link`, `start_date`, `end_date`, `display_order`) VALUES
('¡MEGA DESCUENTO!', 'Hasta 50% OFF en Suplementos', 'Aprovecha esta oferta limitada en nuestra selección premium de suplementos deportivos', 50.00, 'offer-supplements.jpg', 'Ver Ofertas', '/store?category=suplementos', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 1),
('MEMBRESÍA PREMIUM', '3 Meses por el precio de 2', 'Acceso completo a todas nuestras instalaciones y clases grupales', 33.33, 'offer-membership.jpg', 'Suscribirse', '/membership', NOW(), DATE_ADD(NOW(), INTERVAL 15 DAY), 2),
('RUTINAS PERSONALIZADAS', 'Gratis con tu primera compra', 'Recibe una rutina personalizada diseñada por nuestros expertos', 0.00, 'offer-routine.jpg', 'Empezar Ahora', '/routines', NOW(), DATE_ADD(NOW(), INTERVAL 45 DAY), 3);

-- Insertar características "Por qué elegirnos"
INSERT INTO `why_choose_us` (`title`, `subtitle`, `description`, `icon`, `highlights`, `stats`, `display_order`) VALUES
('Rutinas Personalizadas', 'Entrenamientos inteligentes con IA', 'Entrenamientos diseñados específicamente para tus objetivos, nivel y disponibilidad de tiempo', 'fas fa-dumbbell', '["Videos HD Explicativos", "Seguimiento en Tiempo Real", "Ajustes Automáticos IA", "Soporte 24/7"]', '{"exercises": "1000+", "success_rate": "95%"}', 1),
('Tienda Especializada', 'Los mejores suplementos del mercado', 'Productos certificados y de las marcas más reconocidas mundialmente', 'fas fa-store', '["Productos Certificados", "Envío Gratis", "Garantía Total", "Asesoría Nutricional"]', '{"products": "500+", "satisfaction": "98%"}', 2),
('Clases Grupales', 'Entrenamientos dinámicos y motivadores', 'Variedad de clases dirigidas por instructores certificados', 'fas fa-users', '["Instructores Certificados", "Horarios Flexibles", "Ambiente Motivador", "Todos los Niveles"]', '{"classes": "20+", "instructors": "15"}', 3),
('Tecnología Avanzada', 'Seguimiento y análisis en tiempo real', 'Monitoreo completo de tu progreso con tecnología de vanguardia', 'fas fa-chart-line', '["App Móvil", "Análisis Detallado", "Sincronización Cloud", "Reportes Personalizados"]', '{"accuracy": "99%", "users": "10000+"}', 4);

-- Insertar configuración de productos destacados
INSERT INTO `featured_products_config` (`section_title`, `section_subtitle`, `max_products`, `selection_criteria`) VALUES
('Productos Destacados', 'Los favoritos de nuestros clientes', 8, '{"criteria": ["is_featured", "high_rating", "best_sellers"], "order_by": "popularity"}');

-- Insertar testimonios de ejemplo
INSERT INTO `testimonials` (`name`, `role`, `image`, `testimonial_text`, `rating`, `location`, `date_given`, `is_featured`, `display_order`) VALUES
('María González', 'Cliente Premium', 'maria.jpg', 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord. Los videos explicativos son increíbles y el seguimiento es muy detallado.', 5, 'Lima, Perú', '2024-01-15', 1, 1),
('Carlos Rodríguez', 'Atleta Profesional', 'carlos.jpg', 'La combinación de entrenamiento personalizado y suplementos recomendados ha transformado completamente mi rendimiento deportivo. Recomiendo STYLOFITNESS al 100%.', 5, 'Arequipa, Perú', '2024-01-20', 1, 2),
('Ana Morales', 'Fitness Enthusiast', 'ana.jpg', 'Me encanta poder seguir mis rutinas desde casa con los videos HD. La tienda online tiene los mejores precios y la entrega es súper rápida.', 5, 'Cusco, Perú', '2024-01-25', 1, 3),
('Diego Fernández', 'Cliente VIP', 'diego.jpg', 'El seguimiento personalizado y las clases grupales han hecho que entrenar sea adictivo. Los resultados están garantizados con este sistema.', 5, 'Trujillo, Perú', '2024-01-30', 1, 4),
('Lucía Vargas', 'Entrenadora Personal', 'lucia.jpg', 'Como profesional del fitness, puedo decir que STYLOFITNESS tiene el mejor sistema de rutinas que he visto. La tecnología es impresionante.', 5, 'Chiclayo, Perú', '2024-02-05', 0, 5),
('Roberto Silva', 'Empresario', 'roberto.jpg', 'Perfecto para personas ocupadas como yo. Las rutinas se adaptan a mi horario y los resultados son visibles desde la primera semana.', 5, 'Piura, Perú', '2024-02-10', 0, 6);