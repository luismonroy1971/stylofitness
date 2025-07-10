-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2025 at 02:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stylofitness_gym`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variation_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_bookings`
--

CREATE TABLE `class_bookings` (
  `id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `status` enum('booked','confirmed','attended','no_show','cancelled','waitlist') DEFAULT 'booked',
  `payment_status` enum('pending','paid','refunded','free') DEFAULT 'free',
  `amount_paid` decimal(8,2) DEFAULT 0.00,
  `booking_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `check_in_time` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_reviews`
--

CREATE TABLE `class_reviews` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `instructor_rating` tinyint(1) DEFAULT NULL,
  `difficulty_rating` tinyint(1) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

CREATE TABLE `class_schedules` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_recurring` tinyint(1) DEFAULT 1,
  `exceptions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`exceptions`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_schedules`
--

INSERT INTO `class_schedules` (`id`, `class_id`, `day_of_week`, `start_time`, `end_time`, `start_date`, `end_date`, `is_recurring`, `exceptions`, `is_active`, `created_at`) VALUES
(1, 1, 'monday', '07:00:00', '08:00:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17'),
(2, 1, 'wednesday', '07:00:00', '08:00:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17'),
(3, 1, 'friday', '07:00:00', '08:00:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17'),
(4, 2, 'tuesday', '18:30:00', '19:45:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17'),
(5, 2, 'thursday', '18:30:00', '19:45:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17'),
(6, 3, 'monday', '06:30:00', '07:15:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17'),
(7, 3, 'wednesday', '06:30:00', '07:15:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17'),
(8, 3, 'friday', '06:30:00', '07:15:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17'),
(9, 4, 'monday', '08:00:00', '08:50:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17'),
(10, 4, 'wednesday', '08:00:00', '08:50:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17'),
(11, 4, 'friday', '08:00:00', '08:50:00', NULL, NULL, 1, NULL, 1, '2025-06-16 16:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `cms_pages`
--

CREATE TABLE `cms_pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `status` enum('draft','published','private') DEFAULT 'draft',
  `template` varchar(100) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cms_pages`
--

INSERT INTO `cms_pages` (`id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `meta_title`, `meta_description`, `meta_keywords`, `status`, `template`, `author_id`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'Acerca de STYLOFITNESS', 'acerca-de', '<h1>Bienvenido a STYLOFITNESS</h1><p>Somos tu partner en el camino hacia una vida más saludable y activa.</p>', 'Conoce más sobre STYLOFITNESS', NULL, NULL, NULL, NULL, 'published', NULL, 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(2, 'Política de Privacidad', 'politica-privacidad', '<h1>Política de Privacidad</h1><p>Tu privacidad es importante para nosotros.</p>', 'Política de privacidad y protección de datos', NULL, NULL, NULL, NULL, 'published', NULL, 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(3, 'Términos y Condiciones', 'terminos-condiciones', '<h1>Términos y Condiciones</h1><p>Al usar nuestros servicios, aceptas estos términos.</p>', 'Términos y condiciones de uso', NULL, NULL, NULL, NULL, 'published', NULL, 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17', '2025-06-16 16:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('fixed','percentage') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `minimum_amount` decimal(10,2) DEFAULT 0.00,
  `maximum_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `user_limit` int(11) DEFAULT 1,
  `valid_from` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `valid_until` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `applicable_products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_products`)),
  `applicable_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_categories`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `minimum_amount`, `maximum_discount`, `usage_limit`, `used_count`, `user_limit`, `valid_from`, `valid_until`, `applicable_products`, `applicable_categories`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BIENVENIDO20', 'percentage', 20.00, 100.00, NULL, 100, 0, 1, '2025-06-16 16:04:17', '2025-09-16 16:04:17', NULL, NULL, 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(2, 'ENVIOGRATIS', 'fixed', 15.00, 80.00, NULL, 500, 0, 1, '2025-06-16 16:04:17', '2025-12-16 16:04:17', NULL, NULL, 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(3, 'PRIMERACOMPRA', 'percentage', 15.00, 50.00, NULL, 1000, 0, 1, '2025-06-16 16:04:17', '2026-06-16 16:04:17', NULL, NULL, 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usage`
--

CREATE TABLE `coupon_usage` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `muscle_groups` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`muscle_groups`)),
  `difficulty_level` enum('beginner','intermediate','advanced') DEFAULT 'intermediate',
  `equipment_needed` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `video_thumbnail` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `calories_burned` int(11) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `views_count` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `rating_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `category_id`, `name`, `description`, `instructions`, `muscle_groups`, `difficulty_level`, `equipment_needed`, `video_url`, `video_thumbnail`, `image_url`, `duration_minutes`, `calories_burned`, `tags`, `views_count`, `rating`, `rating_count`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Press de Banca Plano', 'Ejercicio fundamental para el desarrollo del pectoral mayor, triceps y deltoides anterior', 'Acuéstate en el banco con los pies firmes en el suelo. Agarra la barra con las manos ligeramente más separadas que el ancho de los hombros. Baja la barra controladamente hasta tocar el pecho, luego empuja hacia arriba hasta extensión completa de los brazos.', '[\"pectorales\", \"triceps\", \"deltoides anteriores\"]', 'intermediate', 'Banca, Barra, Discos', NULL, NULL, NULL, 3, 15, '[\"pecho\", \"fuerza\", \"básico\", \"compound\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(2, 1, 'Sentadilla Trasera', 'Ejercicio rey para el desarrollo de piernas y glúteos', 'Coloca la barra en tus trapecios, mantén los pies separados al ancho de hombros. Desciende flexionando las rodillas hasta que los muslos estén paralelos al suelo, manteniendo la espalda recta. Sube empujando con los talones.', '[\"cuádriceps\", \"glúteos\", \"isquiotibiales\", \"core\"]', 'intermediate', 'Barra, Discos, Rack de Sentadillas', NULL, NULL, NULL, 4, 25, '[\"piernas\", \"glúteos\", \"compound\", \"básico\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(3, 1, 'Peso Muerto Convencional', 'Ejercicio compuesto para toda la cadena posterior', 'Con los pies separados al ancho de cadera, agarra la barra con agarre mixto. Mantén la espalda recta y levanta la barra extendiendo las caderas y rodillas simultáneamente. Contrae los glúteos en la parte superior.', '[\"espalda baja\", \"glúteos\", \"isquiotibiales\", \"trapecios\"]', 'advanced', 'Barra, Discos', NULL, NULL, NULL, 5, 30, '[\"espalda\", \"glúteos\", \"compound\", \"potencia\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(4, 1, 'Dominadas', 'Ejercicio de tracción vertical con peso corporal', 'Cuélgate de la barra con agarre prono, manos separadas al ancho de hombros. Tira de tu cuerpo hacia arriba hasta que la barbilla pase la barra. Baja controladamente hasta extensión completa.', '[\"dorsales\", \"bíceps\", \"romboides\", \"trapecio medio\"]', 'advanced', 'Barra de Dominadas', NULL, NULL, NULL, 2, 12, '[\"espalda\", \"calistenia\", \"tracción\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(5, 1, 'Press Militar', 'Desarrollo de hombros de pie con barra', 'De pie con los pies separados al ancho de hombros, coloca la barra a la altura de los hombros. Empuja la barra hacia arriba manteniéndola en línea con tu cabeza. Baja controladamente.', '[\"deltoides\", \"triceps\", \"core\"]', 'intermediate', 'Barra, Discos', NULL, NULL, NULL, 3, 18, '[\"hombros\", \"core\", \"estabilidad\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(6, 2, 'Burpees', 'Ejercicio de cuerpo completo de alta intensidad', 'Desde posición de pie, baja a cuclillas y coloca las manos en el suelo. Lleva los pies hacia atrás a posición de plancha, haz una flexión, regresa a cuclillas y salta hacia arriba con los brazos extendidos.', '[\"cuerpo completo\"]', 'intermediate', 'Ninguno', NULL, NULL, NULL, 1, 15, '[\"hiit\", \"funcional\", \"cardio\", \"quema grasa\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(7, 2, 'Mountain Climbers', 'Ejercicio cardiovascular dinámico', 'En posición de plancha alta, alterna llevando las rodillas hacia el pecho de forma rápida y controlada. Mantén las caderas estables y el core activado durante todo el movimiento.', '[\"core\", \"hombros\", \"piernas\"]', 'beginner', 'Ninguno', NULL, NULL, NULL, 1, 12, '[\"cardio\", \"core\", \"agilidad\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(8, 2, 'Sprint en Cinta', 'Carrera de alta intensidad', 'Corre a máxima velocidad durante intervalos cortos, manteniendo una postura corporal correcta. Alterna con períodos de recuperación activa.', '[\"piernas\", \"sistema cardiovascular\"]', 'advanced', 'Cinta de Correr', NULL, NULL, NULL, 1, 20, '[\"velocidad\", \"intervalos\", \"hiit\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(9, 3, 'Estiramiento de Isquiotibiales', 'Estiramiento para la parte posterior del muslo', 'Sentado en el suelo, extiende una pierna y flexiona la otra. Inclínate hacia adelante sobre la pierna extendida, manteniendo la espalda recta. Sostén el estiramiento.', '[\"isquiotibiales\", \"espalda baja\"]', 'beginner', 'Colchoneta', NULL, NULL, NULL, 2, 3, '[\"flexibilidad\", \"recuperación\", \"estiramiento\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(10, 3, 'Cobra Yoga', 'Postura de yoga para flexibilidad de espalda', 'Acuéstate boca abajo, coloca las palmas bajo los hombros. Empuja el torso hacia arriba arqueando la espalda, manteniendo las caderas en el suelo.', '[\"espalda\", \"core\", \"hombros\"]', 'beginner', 'Colchoneta', NULL, NULL, NULL, 2, 5, '[\"yoga\", \"movilidad\", \"espalda\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(11, 4, 'Kettlebell Swing', 'Movimiento balístico con kettlebell', 'Con los pies separados, agarra la kettlebell con ambas manos. Flexiona las caderas hacia atrás y balancea la kettlebell entre las piernas. Extiende las caderas explosivamente para balancear la kettlebell a la altura del pecho.', '[\"glúteos\", \"core\", \"hombros\"]', 'intermediate', 'Kettlebell', NULL, NULL, NULL, 1, 18, '[\"funcional\", \"potencia\", \"cardio\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(12, 4, 'Farmer\'s Walk', 'Caminata funcional con peso', 'Agarra pesos pesados en cada mano y camina manteniendo una postura erguida, hombros hacia atrás y core activado. Da pasos controlados y respira normalmente.', '[\"core\", \"trapecios\", \"antebrazos\", \"piernas\"]', 'intermediate', 'Mancuernas o Kettlebells', NULL, NULL, NULL, 3, 12, '[\"funcional\", \"grip\", \"core\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(13, 5, 'Plancha Frontal', 'Ejercicio isométrico fundamental para core', 'Apóyate en antebrazos y pies, manteniendo el cuerpo en línea recta desde la cabeza hasta los talones. Contrae el abdomen y glúteos. Respira normalmente.', '[\"core\", \"hombros\", \"glúteos\"]', 'beginner', 'Colchoneta', NULL, NULL, NULL, 2, 8, '[\"core\", \"estabilidad\", \"isométrico\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(14, 5, 'Dead Bug', 'Ejercicio de control motor para core', 'Acuéstate boca arriba con brazos extendidos y rodillas flexionadas a 90°. Extiende lentamente brazo opuesto y pierna, mantén la posición y regresa controladamente.', '[\"core\", \"estabilizadores\"]', 'beginner', 'Colchoneta', NULL, NULL, NULL, 2, 6, '[\"core\", \"estabilidad\", \"control\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(15, 6, 'Saltos al Cajón', 'Ejercicio pliométrico para potencia de piernas', 'Desde posición de pie frente a un cajón, salta explosivamente aterrizando suavemente con ambos pies en el cajón. Baja controladamente y repite.', '[\"piernas\", \"glúteos\", \"potencia\"]', 'intermediate', 'Cajón Pliométrico', NULL, NULL, NULL, 1, 15, '[\"pliométrico\", \"potencia\", \"salto\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(16, 6, 'Flexiones Explosivas', 'Flexiones con componente pliométrico', 'Realiza una flexión normal pero en la fase concéntrica empuja explosivamente para que las manos se separen del suelo. Aterriza suavemente y repite.', '[\"pectorales\", \"triceps\", \"core\"]', 'advanced', 'Ninguno', NULL, NULL, NULL, 1, 12, '[\"pliométrico\", \"potencia\", \"upper body\"]', 0, 0.00, 0, 1, 2, '2025-06-16 16:04:17', '2025-06-16 16:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_categories`
--

CREATE TABLE `exercise_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `color` varchar(7) DEFAULT '#FF6B00',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercise_categories`
--

INSERT INTO `exercise_categories` (`id`, `name`, `description`, `icon`, `color`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Fuerza', 'Ejercicios de fuerza y resistencia muscular para desarrollo y tonificación', 'dumbbell', '#FF6B00', 1, 1, '2025-06-16 16:04:17'),
(2, 'Cardio', 'Ejercicios cardiovasculares para mejorar resistencia y quema de calorías', 'heart', '#E55A00', 2, 1, '2025-06-16 16:04:17'),
(3, 'Flexibilidad', 'Estiramientos, yoga y ejercicios de movilidad articular', 'wind', '#FFB366', 3, 1, '2025-06-16 16:04:17'),
(4, 'Funcional', 'Entrenamiento funcional y movimientos compuestos naturales', 'activity', '#FF8533', 4, 1, '2025-06-16 16:04:17'),
(5, 'Core', 'Ejercicios específicos para fortalecer el núcleo y estabilidad', 'target', '#CC5500', 5, 1, '2025-06-16 16:04:17'),
(6, 'Pliométricos', 'Ejercicios explosivos para desarrollo de potencia y agilidad', 'zap', '#FF4500', 6, 1, '2025-06-16 16:04:17'),
(7, 'Rehabilitación', 'Ejercicios terapéuticos para recuperación y prevención de lesiones', 'shield', '#28A745', 7, 1, '2025-06-16 16:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `featured_products_config`
--

CREATE TABLE `featured_products_config` (
  `id` int(11) NOT NULL,
  `section_title` varchar(255) DEFAULT 'Productos Destacados',
  `section_subtitle` varchar(500) DEFAULT NULL,
  `max_products` int(11) DEFAULT 8,
  `display_type` enum('grid','carousel','masonry') DEFAULT 'grid',
  `auto_select` tinyint(1) DEFAULT 1,
  `selection_criteria` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selection_criteria`)),
  `background_style` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `featured_products_config`
--

INSERT INTO `featured_products_config` (`id`, `section_title`, `section_subtitle`, `max_products`, `display_type`, `auto_select`, `selection_criteria`, `background_style`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Productos Destacados', 'Los favoritos de nuestros clientes', 8, 'grid', 1, '{\"criteria\": [\"is_featured\", \"high_rating\", \"best_sellers\"], \"order_by\": \"popularity\"}', NULL, 1, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(2, 'Productos Destacados', 'Los favoritos de nuestros clientes', 8, 'grid', 1, '{\"criteria\": [\"is_featured\", \"high_rating\", \"best_sellers\"], \"order_by\": \"popularity\"}', NULL, 1, '2025-06-30 03:36:29', '2025-06-30 03:36:29');

-- --------------------------------------------------------

--
-- Table structure for table `group_classes`
--

CREATE TABLE `group_classes` (
  `id` int(11) NOT NULL,
  `gym_id` int(11) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `class_type` enum('cardio','strength','flexibility','dance','martial_arts','aqua','yoga','pilates','crossfit','hiit','spinning') DEFAULT 'cardio',
  `duration_minutes` int(11) DEFAULT 60,
  `max_participants` int(11) DEFAULT 20,
  `room` varchar(100) DEFAULT NULL,
  `equipment_needed` text DEFAULT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') DEFAULT 'intermediate',
  `price` decimal(8,2) DEFAULT 0.00,
  `image_url` varchar(255) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `group_classes`
--

INSERT INTO `group_classes` (`id`, `gym_id`, `instructor_id`, `name`, `description`, `class_type`, `duration_minutes`, `max_participants`, `room`, `equipment_needed`, `difficulty_level`, `price`, `image_url`, `requirements`, `benefits`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'CrossFit WOD', 'Entrenamiento funcional de alta intensidad que combina ejercicios de gimnasia, halterofilia y cardio', 'crossfit', 60, 15, 'Sala CrossFit', NULL, 'intermediate', 25.00, NULL, NULL, 'Mejora fuerza, resistencia, coordinación y composición corporal', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(2, 1, 2, 'Yoga Flow', 'Clase de yoga dinámico que conecta movimiento con respiración', 'yoga', 75, 20, 'Sala Yoga', NULL, 'beginner', 20.00, NULL, NULL, 'Flexibilidad, relajación, equilibrio mental y físico', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(3, 1, 2, 'HIIT Cardio', 'Entrenamiento cardiovascular de intervalos de alta intensidad', 'hiit', 45, 25, 'Sala Cardio', NULL, 'intermediate', 22.00, NULL, NULL, 'Quema de grasa, mejora cardiovascular, tonificación', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(4, 1, 2, 'Spinning Power', 'Clase de ciclismo indoor con música motivadora y diferentes intensidades', 'spinning', 50, 30, 'Sala Spinning', NULL, 'intermediate', 18.00, NULL, NULL, 'Resistencia cardiovascular, quema de calorías, fortalecimiento de piernas', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `gyms`
--

CREATE TABLE `gyms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `theme_colors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`theme_colors`)),
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `operating_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`operating_hours`)),
  `social_media` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_media`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gyms`
--

INSERT INTO `gyms` (`id`, `name`, `address`, `phone`, `email`, `logo`, `theme_colors`, `settings`, `operating_hours`, `social_media`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'STYLOFITNESS Principal', 'Av. Principal 123, San Isidro, Lima', '+51 999 888 777', 'info@stylofitness.com', NULL, '{\"primary\": \"#FF6B00\", \"secondary\": \"#E55A00\", \"accent\": \"#FFB366\", \"dark\": \"#2C2C2C\", \"light\": \"#F8F9FA\", \"success\": \"#28A745\", \"warning\": \"#FFC107\", \"error\": \"#DC3545\"}', '{\"currency\": \"PEN\", \"timezone\": \"America/Lima\", \"language\": \"es\", \"tax_rate\": 0.18, \"membership_duration_days\": 30}', '{\"monday\": {\"open\": \"06:00\", \"close\": \"23:00\"}, \"tuesday\": {\"open\": \"06:00\", \"close\": \"23:00\"}, \"wednesday\": {\"open\": \"06:00\", \"close\": \"23:00\"}, \"thursday\": {\"open\": \"06:00\", \"close\": \"23:00\"}, \"friday\": {\"open\": \"06:00\", \"close\": \"23:00\"}, \"saturday\": {\"open\": \"07:00\", \"close\": \"22:00\"}, \"sunday\": {\"open\": \"08:00\", \"close\": \"20:00\"}}', '{\"facebook\": \"https://facebook.com/stylofitness\", \"instagram\": \"https://instagram.com/stylofitness\", \"youtube\": \"https://youtube.com/stylofitness\", \"tiktok\": \"https://tiktok.com/@stylofitness\"}', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(2, 'STYLOFITNESS Lima Centro', 'Av. Javier Prado Este 1234, San Isidro, Lima', '+51 1 234-5678', 'lima@stylofitness.com', 'logo-lima.png', '{\"primary\":\"#FF6B35\",\"secondary\":\"#2C3E50\",\"accent\":\"#F39C12\"}', '{\"currency\":\"PEN\",\"timezone\":\"America\\/Lima\",\"language\":\"es\"}', '{\"monday\":{\"open\":\"06:00\",\"close\":\"22:00\"},\"tuesday\":{\"open\":\"06:00\",\"close\":\"22:00\"}}', '{\"facebook\":\"https:\\/\\/facebook.com\\/stylofitness\",\"instagram\":\"https:\\/\\/instagram.com\\/stylofitness\"}', 1, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(3, 'STYLOFITNESS Miraflores', 'Av. Larco 456, Miraflores, Lima', '+51 1 234-5679', 'miraflores@stylofitness.com', 'logo-miraflores.png', '{\"primary\":\"#FF6B35\",\"secondary\":\"#2C3E50\",\"accent\":\"#E74C3C\"}', '{\"currency\":\"PEN\",\"timezone\":\"America\\/Lima\",\"language\":\"es\"}', '{\"monday\":{\"open\":\"05:30\",\"close\":\"23:00\"},\"tuesday\":{\"open\":\"05:30\",\"close\":\"23:00\"}}', '{\"facebook\":\"https:\\/\\/facebook.com\\/stylofitness.miraflores\",\"instagram\":\"https:\\/\\/instagram.com\\/stylofitness_miraflores\"}', 1, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(4, 'STYLOFITNESS Lima Centro', 'Av. Javier Prado Este 1234, San Isidro, Lima', '+51 1 234-5678', 'lima@stylofitness.com', 'logo-lima.png', '{\"primary\":\"#FF6B35\",\"secondary\":\"#2C3E50\",\"accent\":\"#F39C12\"}', '{\"currency\":\"PEN\",\"timezone\":\"America\\/Lima\",\"language\":\"es\"}', '{\"monday\":{\"open\":\"06:00\",\"close\":\"22:00\"},\"tuesday\":{\"open\":\"06:00\",\"close\":\"22:00\"}}', '{\"facebook\":\"https:\\/\\/facebook.com\\/stylofitness\",\"instagram\":\"https:\\/\\/instagram.com\\/stylofitness\"}', 1, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(5, 'STYLOFITNESS Miraflores', 'Av. Larco 456, Miraflores, Lima', '+51 1 234-5679', 'miraflores@stylofitness.com', 'logo-miraflores.png', '{\"primary\":\"#FF6B35\",\"secondary\":\"#2C3E50\",\"accent\":\"#E74C3C\"}', '{\"currency\":\"PEN\",\"timezone\":\"America\\/Lima\",\"language\":\"es\"}', '{\"monday\":{\"open\":\"05:30\",\"close\":\"23:00\"},\"tuesday\":{\"open\":\"05:30\",\"close\":\"23:00\"}}', '{\"facebook\":\"https:\\/\\/facebook.com\\/stylofitness.miraflores\",\"instagram\":\"https:\\/\\/instagram.com\\/stylofitness_miraflores\"}', 1, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(6, 'STYLOFITNESS Lima Centro', 'Av. Javier Prado Este 1234, San Isidro, Lima', '+51 1 234-5678', 'lima@stylofitness.com', 'logo-lima.png', '{\"primary\":\"#FF6B35\",\"secondary\":\"#2C3E50\",\"accent\":\"#F39C12\"}', '{\"currency\":\"PEN\",\"timezone\":\"America\\/Lima\",\"language\":\"es\"}', '{\"monday\":{\"open\":\"06:00\",\"close\":\"22:00\"},\"tuesday\":{\"open\":\"06:00\",\"close\":\"22:00\"}}', '{\"facebook\":\"https:\\/\\/facebook.com\\/stylofitness\",\"instagram\":\"https:\\/\\/instagram.com\\/stylofitness\"}', 1, '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(7, 'STYLOFITNESS Miraflores', 'Av. Larco 456, Miraflores, Lima', '+51 1 234-5679', 'miraflores@stylofitness.com', 'logo-miraflores.png', '{\"primary\":\"#FF6B35\",\"secondary\":\"#2C3E50\",\"accent\":\"#E74C3C\"}', '{\"currency\":\"PEN\",\"timezone\":\"America\\/Lima\",\"language\":\"es\"}', '{\"monday\":{\"open\":\"05:30\",\"close\":\"23:00\"},\"tuesday\":{\"open\":\"05:30\",\"close\":\"23:00\"}}', '{\"facebook\":\"https:\\/\\/facebook.com\\/stylofitness.miraflores\",\"instagram\":\"https:\\/\\/instagram.com\\/stylofitness_miraflores\"}', 1, '2025-06-30 04:07:36', '2025-06-30 04:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `landing_page_config`
--

CREATE TABLE `landing_page_config` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `is_enabled` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `custom_css` text DEFAULT NULL,
  `custom_js` text DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `landing_page_config`
--

INSERT INTO `landing_page_config` (`id`, `section_name`, `is_enabled`, `display_order`, `custom_css`, `custom_js`, `settings`, `created_at`, `updated_at`) VALUES
(1, 'special_offers', 1, 1, NULL, NULL, '{\"animation\": \"fadeIn\", \"autoplay\": true, \"interval\": 5000}', '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(2, 'why_choose_us', 1, 2, NULL, NULL, '{\"animation\": \"slideUp\", \"columns\": 3}', '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(3, 'featured_products', 1, 3, NULL, NULL, '{\"animation\": \"zoomIn\", \"layout\": \"grid\", \"items_per_row\": 4}', '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(4, 'group_classes', 1, 4, NULL, NULL, '{\"animation\": \"fadeInUp\", \"show_schedule\": true}', '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(5, 'testimonials', 1, 5, NULL, NULL, '{\"animation\": \"slideIn\", \"autoplay\": true, \"show_ratings\": true}', '2025-06-30 03:35:55', '2025-06-30 03:35:55');

-- --------------------------------------------------------

--
-- Table structure for table `media_files`
--

CREATE TABLE `media_files` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_type` enum('image','video','audio','document','other') NOT NULL,
  `dimensions` varchar(20) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `action_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  `payment_status` enum('pending','paid','partial_paid','refunded','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'PEN',
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`billing_address`)),
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_address`)),
  `shipping_method` varchar(100) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `internal_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variation_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `product_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `order_items`
--
DELIMITER $$
CREATE TRIGGER `trg_update_product_stock` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    UPDATE products 
    SET stock_quantity = stock_quantity - NEW.quantity,
        sales_count = sales_count + NEW.quantity
    WHERE id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `sku` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 5,
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `nutritional_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nutritional_info`)),
  `usage_instructions` text DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `warnings` text DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `views_count` int(11) DEFAULT 0,
  `sales_count` int(11) DEFAULT 0,
  `avg_rating` decimal(3,2) DEFAULT 0.00,
  `reviews_count` int(11) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `sale_price`, `cost_price`, `stock_quantity`, `min_stock_level`, `weight`, `dimensions`, `images`, `gallery`, `specifications`, `nutritional_info`, `usage_instructions`, `ingredients`, `warnings`, `brand`, `is_featured`, `is_active`, `views_count`, `sales_count`, `avg_rating`, `reviews_count`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 1, 'Whey Protein Gold Standard 2.5kg', 'whey-protein-gold-standard-2-5kg', 'La proteína de suero más vendida del mundo. Whey Protein Gold Standard ofrece 24g de proteína de alta calidad por porción, con excelente sabor y disolución.', 'Proteína whey premium con 24g por porción - Sabor Chocolate', 'WP-GS-001', 289.90, 259.90, NULL, 45, 5, 2.50, NULL, '[\"/uploads/images/products/whey-gold-1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Optimum Nutrition', 1, 0, 10, 0, 0.00, 0, NULL, NULL, NULL, '2025-06-16 16:04:17', '2025-07-07 03:43:08'),
(2, 2, 'C4 Original Pre-Workout 390g', 'c4-original-pre-workout-390g', 'El pre-entreno más popular del mundo. C4 Original combina ingredientes científicamente probados como beta-alanina, creatina y cafeína.', 'Pre-entreno #1 mundial - Energía explosiva garantizada', 'PRE-C4-001', 149.90, 129.90, NULL, 25, 5, 0.39, NULL, '[\"/uploads/images/products/c4-original-1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Cellucor', 1, 0, 8, 0, 0.00, 0, NULL, NULL, NULL, '2025-06-16 16:04:17', '2025-07-07 03:43:14'),
(3, 3, 'Multivitamínico Complete Sport', 'multivitaminico-complete-sport', 'Complejo vitamínico completo diseñado específicamente para deportistas. Contiene 25 vitaminas y minerales esenciales plus antioxidantes.', 'Multivitamínico específico para deportistas - 90 cápsulas', 'VIT-COMP-001', 89.90, 79.90, NULL, 80, 5, 0.15, NULL, '[\"images\\/products\\/multivit-1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Universal Nutrition', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-06-16 16:04:17', '2025-06-30 03:16:25'),
(5, 6, 'BCAA 2:1:1 Powder 300g', 'bcaa-211-powder-300g', 'Aminoácidos de cadena ramificada en proporción 2:1:1 (Leucina, Isoleucina, Valina). Ideal para prevenir el catabolismo muscular y acelerar la recuperación post-entreno.', 'BCAA 2:1:1 - Recuperación muscular avanzada - Sabor Frutas Tropicales', 'BCAA-211-001', 119.90, 99.90, NULL, 60, 5, 0.30, NULL, '[\"/uploads/images/products/multivit-1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Scivation', 1, 0, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-02 01:55:43', '2025-07-07 03:42:49'),
(6, 5, 'Hydroxycut Hardcore Elite 100 caps', 'hydroxycut-hardcore-elite-100caps', 'Quemador de grasa termogénico de máxima potencia. F??rmula avanzada con cafe??na, extracto de caf?? verde y otros ingredientes cient??ficamente probados para acelerar el metabolismo.', 'Quemador #1 en ventas - Fórmula termogénica avanzada', 'BURN-HCE-001', 179.90, 159.90, NULL, 35, 5, 0.12, NULL, '[\"/uploads/images/products/multivit-1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'MuscleTech', 1, 0, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-02 01:55:43', '2025-07-07 03:43:00'),
(7, 10, 'Carnivor protein de 4 libras', 'carnivor-protein-de-4-libras', 'CARNIVOR es la proteína de carne pura más vendida del mundo! La proteína aislada de carne de res CARNIVOR proporciona toda la potencia de construcción muscular de la carne de vacuno con niveles de aminoácidos más altos que otras fuentes de proteínas utilizadas en los suplementos, como el suero de leche, la soja, la leche y el huevo. ¡La proteína aislada de carne de res CARNIVOR (BPI) está incluso un 350% más concentrada en aminoácidos anabólicos para el desarrollo muscular que un filete de solomillo de primera calidad! ¡Y no contiene grasas ni colesterol! Además, ¡CARNIVOR es uno de los batidos de proteínas más deliciosos que jamás hayas probado! El poder muscular de CARNIVOR: respaldado por la investigación*Una nueva investigación corrobora lo que sabías desde el principio: ¡la carne de res desarrolla músculos y fuerza! Un innovador estudio clínico presentado en la conferencia de 2015 de la Sociedad Internacional de Nutrición Deportiva (ISSN) en Austin, Texas, demostró que los atletas que entrenaban duro y que tomaban suplementos con proteína de res aislada (BPI) CARNIVOR ganaron un promedio de 7.7 libras de masa muscular y, al mismo tiempo, aumentaron su fuerza. Los investigadores administraron a los sujetos de prueba CARNIVOR Beef Protein Isolate diariamente durante 8 semanas mientras hacían ejercicio 5 días a la semana. Los atletas que tomaron CARNIVOR BPI experimentaron un impresionante aumento promedio del 6,4% en la masa corporal magra. Por el contrario, el grupo que recibió el placebo no mejoró significativamente su masa corporal con respecto a los valores basales.* Sharp, et al., Conferencia Internacional del ISSN de 2015 . Datos de masa muscular basados en sujetos masculinos. Aumentos de masa corporal magra en comparación con los valores iniciales. Basado en 2 cucharadas (46 gramos de proteína) al día. Las mujeres también experimentaron un aumento en la masa corporal magra. CARNIVOR cuenta con la certificación Informed-Choice, se ha sometido a pruebas para detectar más de 200 sustancias prohibidas de la lista de la AMA y se produce en una instalación que cumple con las buenas prácticas de fabricación. Puede estar seguro de que obtendrá un producto de la más alta calidad disponible.', 'CARNIVOR es la proteína de carne pura más vendida del mundo! La proteína aislada de carne de res CARNIVOR proporciona toda la potencia de construcción muscular de la carne de vacuno con niveles de aminoácidos más altos que otras fuentes de proteínas utilizadas en los suplementos, como el suero de leche, la soja, la leche y el huevo. ¡La proteína aislada de carne de res CARNIVOR (BPI) está incluso un 350% más concentrada en aminoácidos anabólicos para el desarrollo muscular que un filete de solomillo de primera calidad! ¡Y no contiene grasas ni colesterol! Además, ¡CARNIVOR es uno de los batidos de proteínas más deliciosos que jamás hayas probado!', 'CARNIVORPR505', 295.00, 265.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/carnivor-protein-de-4-libras-255_main.jpg\",\"\\/uploads\\/images\\/products\\/carnivor-protein-de-4-libras-255_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/carnivor-protein-de-4-libras-255_gallery_2.jpg\",\"\\/uploads\\/images\\/products\\/carnivor-protein-de-4-libras-255_gallery_3.jpg\",\"\\/uploads\\/images\\/products\\/carnivor-protein-de-4-libras-255_gallery_4.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'CARNIVOR', 1, 1, 0, 0, 0.00, 0, '', '', NULL, '2025-07-06 04:01:09', '2025-07-08 00:01:09'),
(8, 10, 'Mutant whey 10 libras', 'mutant-whey-10-libras', '<strong>Especificaciones</strong> <strong>Marca Mutant</strong> Formato Polvo Indicaciones Proteína de suero de leche, tomar 1 servicio después del entrenamiento o entre comidas para complementar la nutrición Cantidad contenida en el empaque 10 lb Peso del producto 4.53 Kg Tipo de vitamina Proteínas Información adicional Nuestros suplementos y vitaminas ayudan a mantener un equilibrio en la dieta, proporcionando todos los nutrientes esenciales para que el cuerpo funcione de manera óptima. ¡Todo lo que necesitas para mejorar tu salud está aquí! <strong>Ficha del producto:</strong> Marca: Mutant Peso: 4.53 Kg Condición del producto: Nuevo Contenido: 10 lb Indicaciones: Proteína de suero de leche tomar 1 servicio después del entrenamiento o entre comidas para complementar la nutrición Tipo: Proteínas Formato: Polvo', 'Especificaciones Marca Mutant Formato Polvo Indicaciones Proteína de suero de leche, tomar 1 servicio después del entrenamiento o entre comidas para complementar la nutrición Cantidad contenida en...', 'MUTANTWHEY640', 449.00, 419.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/mutant-whey-10-libras_main.jpg\",\"\\/uploads\\/images\\/products\\/mutant-whey-10-libras_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/mutant-whey-10-libras_gallery_2.gif\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'MUTANT', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(9, 10, 'Prostar Whey de 5 libras', 'prostar-whey-de-5-libras', '<h2 class=\"ui-pdp-description__title\">Descripción</h2> <p class=\"ui-pdp-description__content\">Una nutrición balanceada juega un papel fundamental en la calidad de vida. Con el ritmo acelerado que llevan muchas personas, a veces resulta complejo prestar atención a todos los requerimientos que el cuerpo necesita para estar sano y fuerte. En ese sentido, los suplementos cumplen la función de complementar la alimentación y ayudan a obtener las vitaminas, minerales, proteínas y otros componentes indispensables para el correcto funcionamiento del organismo.</p> Fuente rápida de energía La principal función de las proteínas es contribuir con la regeneración muscular. Es por esto que los suplementos proteicos suelen ser utilizados en ciertas circunstancias, por ejemplo ante el aumento de la intensidad del ejercicio, la recuperación luego de una lesión o para complementar la alimentación de personas veganas y vegetarianas. Algunos de los beneficios son: su consumo fácil, su bajo contenido en azúcares y grasas y la posible reducción del apetito. *Este producto es un suplemento, no es un medicamento. Ante cualquier duda, consulte a su médico. Aviso legal • Edad mínima recomendada: 18 años. • Este producto es un suplemento dietario, no es un medicamento. Suplementa dietas insuficientes. Consulte a su médico y/o farmacéutico.', 'Descripción Una nutrición balanceada juega un papel fundamental en la calidad de vida. Con el ritmo acelerado que llevan muchas personas, a veces resulta complejo prestar atención a todos los...', 'PROSTARWHE516', 319.00, 289.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/prostar-whey-de-5-libras_main.jpg\",\"\\/uploads\\/images\\/products\\/prostar-whey-de-5-libras_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/prostar-whey-de-5-libras_gallery_2.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'PROSTAR', 1, 1, 0, 0, 0.00, 0, '', '', NULL, '2025-07-06 04:01:09', '2025-07-07 23:56:26'),
(10, 10, 'Nitrotech Whey de 5 libras', 'nitrotech-whey-de-5-libras', '<h2>Nitrotech Whey Gold de Muscletech.</h2> La mejor calidad de proteína para ayudarles a construir músculo de manera efectiva. El resultado de años de investigación y desarrollo, diseñados por expertos en nutrición deportiva para proporcionar la mejor calidad de proteína posible. Con una nueva y mejorada fórmula, es la elección perfecta para aquellos que persiguen ganancias musculares y quieren asegurarse de tener suficiente proteína en su dieta diaria. Una mezcla de proteína de suero de leche aislada y concentrada, lo que la convierte en una fuente de proteína completa, con un perfil de aminoácidos excepcional. Además, contiene un ingrediente clave llamado “peptidos de suero”, que son moléculas más pequeñas que los aislados y concentrados de suero de leche tradicionales. Los péptidos de suero son más fáciles de digerir y su cuerpo puede usarlos más rápidamente que las proteínas más grandes. Esto se traduce en una mayor capacidad de recuperación, una síntesis proteica mejorada y una producción de músculo más rápida. Ayuda a construir músculo más rápidamente, mejorar tus recuperaciones post-entrenamiento y mejorar tu fuerza y resistencia muscular. Con tantos beneficios en una sola bebida, no es sorprendente que Nitrotech sea uno de los mejores suplementos proteicos disponibles en el mercado. Si buscas una proteína de alta calidad y efectiva que te ayude a construir músculo sin agregar grasas o carbohidratos innecesarios, entonces Nitrotech Whey Gold es la solución que estás buscando. ¡Prueba la proteína de suero de leche más avanzada del mercado y conviértete en la mejor versión de ti mismo con Nitrotech Whey Gold! <h2>Tabla de Información</h2> Categoría Suplementos Marca MUSCLETECH Nombre NITRO TECH WHEY GOLD Tipo Proteina 100% Whey de Suero de Leche Peso 5 lb. – 2.28 kg Sabor Dark Chocolate Formato Polvo Envase Bote Unidades 1 Servicios 69 <h2>Precauciones</h2> ESTE PRODUCTO NO ES UN MEDICAMENTO. EL CONSUMO DE ESTE PRODUCTO ES RESPONSABILIDAD DE QUIEN LO USA Y DE QUIEN LO RECOMIENDA. LAS IMÁGENES PUEDEN SER ILUSTRATIVAS. &nbsp;', 'Nitrotech Whey Gold de Muscletech. La mejor calidad de proteína para ayudarles a construir músculo de manera efectiva. El resultado de años de investigación y desarrollo, diseñados por expertos...', 'NITROTECHW809', 302.50, 272.50, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/nitrotech-whey-de-5-libras_main.jpg\",\"\\/uploads\\/images\\/products\\/nitrotech-whey-de-5-libras_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/nitrotech-whey-de-5-libras_gallery_2.jpg\",\"\\/uploads\\/images\\/products\\/nitrotech-whey-de-5-libras_gallery_3.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'NITROTECH', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(11, 11, 'Super Mass Gainer Dimatize 6 libras', 'super-mass-gainer', '<h2><strong>Super Mass Gainer:</strong></h2> Es un ponerse de peso muy energético, ideal para ayudar a desarrollar y mantener la masa muscular especialmente en deportistas de gran peso con necesidades energéticas elevadas. <strong>Super Mass Gainer </strong> tiene un contenido muy alto en carbohidratos y proteínas; además está enriquecido con creatina, vitaminas y minerales, ideal para consumir en cualquier momento del día, especialmente después del entrenamiento. <h2><strong>Beneficios Principales:</strong></h2> <ul> <li>Aumenta la energía y favorece el anabolismo.</li> <li>Recupera el glucógeno.</li> <li>Mejora el rendimiento.</li> <li>Alto contenido en proteínas y aminoácidos, con 52 gramos de proteínas y 17 gramos de BCAA.</li> </ul> <h2><strong>Advertencias: </strong></h2> Mantener en un lugar fresco, no usar en mujeres embarazadas, en periodo de lactancia, menores de 18 años; manténgase alejado del alcance de los niños.  Consulte a su médico antes de usar este producto si está tomando un medicamento o presenta algún padecimiento.', 'Super Mass Gainer: Es un ponerse de peso muy energético, ideal para ayudar a desarrollar y mantener la masa muscular especialmente en deportistas de gran peso con necesidades energéticas...', 'SUPERMASSG991', 209.00, 179.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/super-mass-gainer_main.jpg\",\"\\/uploads\\/images\\/products\\/super-mass-gainer_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/super-mass-gainer_gallery_2.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'DIMATIZE', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(12, 11, 'Carnivor Mass de 6 libras', 'carnivor-mass-de-6-libras', '<h1 class=\"product_title entry-title\">CARNIVOR MASS 6LB</h1> <p class=\"price\">Ganador de peso de alta calidad de macronutrientes.</p> – Con 50 gramos de aislado de proteína de carne de vaca hidrolizada. – Con 125 gramos de carbohidratos de alta impacto reactivo.  – Amplifica y señaliza la insulina. – Potenciado con creatina y BCAAs. ', 'CARNIVOR MASS 6LB Ganador de peso de alta calidad de macronutrientes. – Con 50 gramos de aislado de proteína de carne de vaca hidrolizada. – Con 125 gramos de carbohidratos de alta impacto...', 'CARNIVORMA271', 275.00, 245.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/carnivor-mass-de-6-libras_main.jpg\",\"\\/uploads\\/images\\/products\\/carnivor-mass-de-6-libras_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/carnivor-mass-de-6-libras_gallery_2.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'CARNIVOR', 1, 1, 0, 0, 0.00, 0, '', '', NULL, '2025-07-06 04:01:09', '2025-07-07 23:56:26'),
(13, 11, 'Mutant Mass de 15 libras', 'mutant-mass-de-15-libras', '<strong>MUTANT MASS 15LB *DISPONIBLE EN SABOR VAINILLA Y CHOCOLATE*</strong> ¡EL GANADOR DE PESO MAS VENDIDO! <strong>1,100 calorías por servicio.</strong> Hecho con carbohidratos complejos de grano entero (cebada, batata, avena tostada, palta, aceite de coco, semilla de linaza y de zapallo, aceite de girasol) <ul> <li>56 g de pura proteína</li> <li>192 gramos de carbohidratos limpios</li> <li>12 g de grasa</li> <li>26.1 g de EEAs</li> <li>12.2g de BCAAs</li> <li>Acidos grasos escenciales.</li> <li>Aceites naturales de coco, aguacate, linaza, zapallo, girasol</li> <li>Sabor Gourmet</li> </ul> Diseñado específicamente para los físico-culturistas más fuertes y los Ectomorfos que disfrutan el ejercicio y desean ganar masa. Mutant Mass está en el mercado en más de 100 países. Este ganador de peso fue descubierto por atletas buscando los gainers más poderosos del mercado. Cada servicio alimenta tus músculos con 1100 calorías, 56 gramos de proteína pura, 192 gramos de carbohidratos limpios y 12 gramos de grasa. Bebe un batido de Mutant post entrenamiento, entre comidas o antes de dormir para maximizar los resultados. <strong>FORMA DE CONSUMO: como tomar mutant mass 15lb</strong> Añade 12 a 20 onzas fluidas (360ml a 600ml) de agua a un shaker o tomatodo, y añade un servicio de Mutant Mass; batir o licuar por 15 – 20 minutos (o hasta que el polvo de diluya por completo) y consume dos veces al día. Ajusta la cantidad de agua para conseguir el sabor y espesor de tu preferencia.', 'MUTANT MASS 15LB *DISPONIBLE EN SABOR VAINILLA Y CHOCOLATE* ¡EL GANADOR DE PESO MAS VENDIDO! 1,100 calorías por servicio. Hecho con carbohidratos complejos de grano entero (cebada, batata, avena...', 'MUTANTMASS878', 344.00, 314.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/mutant-mass-de-15-libras_main.jpg\",\"\\/uploads\\/images\\/products\\/mutant-mass-de-15-libras_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/mutant-mass-de-15-libras_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'MUTANT', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(14, 11, 'King Mass de 6 libras', 'king-mass-de-6-libras', '<strong>King Mass XL</strong> te provee 500 calorías para aceleración máxima de crecimiento con 30 gr de Proteína de Calidad y 90 gr de Carbohidratos. <ul> <li>60 gr de proteina</li> <li>180 gr de carbohidratos</li> <li>Creatina y Glutamina</li> <li>Mas de 1000 Calorías</li> <li>Puede reemplazar una comida</li> <li>146 scoops</li> </ul> Hacer que tu peso suba es sobre todo consumir más calorías de las que quemas, también es importante obtener las calorías adecuadas, <strong>King Mass XL de Ronnie Coleman</strong> te provee 500 calorías para aceleración máxima de crecimiento con 30 gr de Proteína de Calidad y 90 gr de Carbohidratos (solo 12.5 gr de azúcar) y grasas saludable. Obteniendo todas las calorías que necesitas en solo un batido, Con el mejor sabor desarrollado con el sistema Premium de saborización de <strong>Ronnie Coleman</strong>. <ul> <li>60 gr de proteina</li> <li>180 gr de carbohidratos</li> <li>Creatina y Glutamina</li> <li>Mas de 1000 Calorías</li> <li>Puede reemplazar una comida</li> <li>146 scoops</li> <li>Deliciosos sabores</li> </ul> De manera que tus músculos crezcan debes proveerles de proteínas suficientes para hacer el trabajo. King Mass XL provee 30gr de proteínas para mantener tus músculos alimentados y en estado anabólico. <strong>Como tomar King Mass 6 lb</strong>: Mezclar de 2 a 4 scoops de King mass XL por cada 250 a 400 ml de agua o leche descremada, mezclar por 30 segundos. &nbsp;', 'King Mass XL te provee 500 calorías para aceleración máxima de crecimiento con 30 gr de Proteína de Calidad y 90 gr de Carbohidratos. 60 gr de proteina 180 gr de carbohidratos Creatina y...', 'KINGMASSDE827', 201.00, 171.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/king-mass-de-6-libras_main.jpg\",\"\\/uploads\\/images\\/products\\/king-mass-de-6-libras_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/king-mass-de-6-libras_gallery_2.jpg\",\"\\/uploads\\/images\\/products\\/king-mass-de-6-libras_gallery_3.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'MASS', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(15, 12, 'ISOLATE HYDROLIZADA LAB 5 libras', 'isolate-hydrolizada-lab-5-libras', '<h4 class=\"labnutrition-labnutrition-menu-app-0-x-descriptionTextSubtitle\">La máxima calidad en proteína hidrolizada, acelera la recuperación muscular!! ¡Proteína hidrolizada: máxima calidad y absorción rápida!</h4> <p class=\"labnutrition-labnutrition-menu-app-0-x-descriptionTextParagraph\">Potencia tu rendimiento y recuperación La proteína hidrolizada es una forma pre-digerida e innovadora de proteína de suero, destacándose como uno de los suplementos nutricionales más vanguardistas en el mercado actual. Sometida a un proceso de hidrólisis que descompone sus largas cadenas de proteínas en péptidos más pequeños, esta proteína se destaca por su fácil digestión y rápida absorción por parte del organismo. En este texto, exploraremos los beneficios clave de la proteína hidrolizada y cómo su avanzada tecnología la convierte en una opción sobresaliente entre los suplementos de proteínas disponibles.</p> &nbsp; <p class=\"labnutrition-labnutrition-menu-app-0-x-descriptionTextParagraph\">La proteína hidrolizada es un tipo de proteína que ha sido sometida a un proceso de hidrólisis, que consiste en descomponer las grandes cadenas de proteínas en cadenas más pequeñas llamadas péptidos. Estos péptidos son más fáciles de digerir y absorber por el cuerpo en comparación con las proteínas intactas. En este texto, exploraremos los beneficios clave de consumir proteína hidrolizada y cómo puede ser una opción superior en comparación con otras proteínas. Beneficios potenciales de consumir proteína hidrolizada: 1. Absorción y digestión superior: Debido a su estructura más pequeña, la proteína hidrolizada se digiere y absorbe más rápidamente en comparación con otras proteínas. Esto significa que los aminoácidos se liberan más rápidamente en el torrente sanguíneo, lo que puede ser beneficioso para la recuperación muscular después del ejercicio o para la respuesta anabólica en general. 2. Mayor disponibilidad de aminoácidos: La hidrólisis de la proteína aumenta la disponibilidad de aminoácidos, que son los componentes básicos de las proteínas.</p> &nbsp; <p class=\"labnutrition-labnutrition-menu-app-0-x-descriptionTextParagraph\">Los aminoácidos son esenciales para la síntesis de nuevas proteínas en el cuerpo, así como para la reparación y construcción muscular. 3. Sin riesgo de alergias o intolerancias: La proteína hidrolizada es una alternativa para aquellos con sensibilidades, ya que el proceso de hidrólisis reduce la presencia de alérgenos y la hace más fácil de digerir, evitando problemas asociados con proteínas como la lactosa o la caseína. 4. Apoyo a la salud intestinal: Se ha sugerido que la proteína hidrolizada puede tener efectos positivos en la salud intestinal. Los péptidos resultantes de la hidrólisis pueden tener propiedades antiinflamatorias y promover la salud de la microbiota intestinal. 5. Versatilidad en la dieta diaria: La proteína hidrolizada se puede consumir en diferentes formas, como batidos y bebidas, lo que la hace fácilmente incorporable en la dieta diaria.</p> &nbsp; <p class=\"labnutrition-labnutrition-menu-app-0-x-descriptionTextParagraph\">Puede ser utilizada como suplemento para aumentar la ingesta de proteínas, especialmente para aquellos que tienen dificultades para consumir proteínas debido a problemas digestivos o de absorción. *La proteína hidrolizada destaca por su absorción y digestión superiores, su mayor disponibilidad de aminoácidos esenciales, su capacidad para evitar riesgos de alergias o intolerancias y su potencial apoyo a la salud intestinal. Además, su versatilidad en la dieta diaria la convierte en una opción ideal para aquellos que buscan maximizar los beneficios nutricionales de la proteína. Así, la proteína hidrolizada se considera superior a otras opciones debido a sus características que facilitan su absorción total, fácil digestión y menor trabajo digestivo.</p>', 'La máxima calidad en proteína hidrolizada, acelera la recuperación muscular!! ¡Proteína hidrolizada: máxima calidad y absorción rápida! Potencia tu rendimiento y recuperación La proteína...', 'ISOLATEHYD485', 339.00, 309.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/isolate-hidrolizada-lab-5-libras_main.png\",\"\\/uploads\\/images\\/products\\/isolate-hidrolizada-lab-5-libras_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/isolate-hidrolizada-lab-5-libras_gallery_2.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'LAB', 1, 1, 0, 0, 0.00, 0, '', '', NULL, '2025-07-06 04:01:09', '2025-07-07 23:56:26'),
(16, 12, 'Iso XP de 1.8 KL 72 tomas', 'iso-xp-de-1-8-kl-72-tomas', '<h1 class=\"product_title entry-title elementor-heading-title elementor-size-default\">ISO-XP 72 Servidas 1.8Kg</h1> ISO-XP es un suplemento rico en proteínas elaborado con suero de leche más limpio y de mayor calidad extremadamente fresco que se aísla y luego se seca por pulverización. ISO-XP no tiene SOJA agregada y en su lugar utiliza lecitina de girasol como emulsionante. ISO-XP es ideal para cualquier persona que busque aumentar la ingesta diaria de proteínas sin grasas, carbohidratos o azúcares adicionales. ISO-XP debe mezclarse con agua o leche sin lactosa para una fácil mezcla y un excelente sabor. <strong>Beneficios:</strong> <ul> <li>0 gr de azúcar (por porción de 25 gr)</li> <li>0 gr de carbohidratos (por porción de 25 gr)</li> <li>0 gr de lactosa (por porción de 25 gr)</li> <li>0 gr de soja</li> <li>0 gr de grasa (por porción de 25 gr)</li> <li>Sin gluten</li> <li>Sabores: Chocolate (22.5g de proteína por porción de 25 gr) y Vainilla (23.4g de proteína por porción de 25 gr)</li> </ul> Sugerencia de uso: <li><b></b>Mezcle 1 servicio (25 gramos) con 200 ml de agua o leche sin lactosa.</li> <li>Consuma 1-3 servicios diarios, dependiendo de sus requerimientos de proteínas. ISO-XP se absorbe rápidamente, por lo que los momentos óptimos para consumirlo son cuando necesita proteínas rápidamente. Esto incluye inmediatamente al despertar antes del desayuno e inmediatamente después del ejercicio.</li>', 'ISO-XP 72 Servidas 1.8Kg ISO-XP es un suplemento rico en proteínas elaborado con suero de leche más limpio y de mayor calidad extremadamente fresco que se aísla y luego se seca por pulverización....', 'ISOXPDE18K969', 360.00, 330.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/iso-xp-de-1-8-kl-72-tomas_main.jpg\",\"\\/uploads\\/images\\/products\\/iso-xp-de-1-8-kl-72-tomas_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/iso-xp-de-1-8-kl-72-tomas_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Iso', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(17, 12, 'Iso Sensation Ultimate Nutrition de 5 libras', 'iso-sensation-ultimate-nutrition-de-5-libras', 'Después de 5 años de investigación continua, Ultimate Nutrition se enorgullece de presentar <strong>Iso-Sensation 93</strong> que contiene 100% de proteína de suero de leche Isolada bajo la tecnologia IsoChill. IsoChill es una proteína de suero de leche Aislada. <h2>Iso Sensation 93, 2 lb y 5 lb</h2> Después de 5 años de investigación continua, Ultimate Nutrition se enorgullece de presentar <strong>Iso-Sensation 93</strong> que contiene 100% de proteína de suero de leche Isolada bajo la tecnologia IsoChill. IsoChill es una proteína de suero de leche Aislada, Procesada a través de una microfiltración a temperaturas muy bajas para obtener un equilibrio completo de proteínas bioactivas y no desnaturalizadas. <h2><strong>Iso-Sensation 93 Aporta</strong></h2> <ul> <li>93% de proteína por porción</li> <li>Lleva un añadido de glutamina</li> <li>Contiene 98% IsoChill Whey Isolate ®</li> <li>Enriquecido con Calostro</li> <li>1gr Carbohidratos, 0 Grasa y libre de lactosa</li> </ul>   <strong>Recomendaciones de uso para Iso sensation 93, </strong>(como tomar) Mezcle 1 scoop o medida en 200ml de agua fría o leche descremada. Consumir 1 a 3 porciones al día (dependiendo de sus necesidades de proteínas). Para obtener los mejores resultados consuma una porción inmediatamente después del entrenamiento y otra antes. <h2><b>Información Nutricional</b></h2> <b>5 libras de Cookies and Cream</b> Tamaño de la porción: 1 cucharada (33 g) Porciones por el envase: 69 <p align=\"center\"><b>Cantidad por porción</b></p> <p align=\"right\"><b>AMT</b></p> <p align=\"center\"><b>% DV</b></p> Calorías <p align=\"right\">130</p> Colesterol <p align=\"right\">2 mg</p> <p align=\"center\">1 %</p> Sodio <p align=\"right\">30 mg</p> <p align=\"center\">1 %</p> Hidratos de carbono <p align=\"right\">1 g</p> <p align=\"center\">0 %</p> Azúcar <p align=\"right\">< 1 g</p> Proteína <p align=\"right\">30 g</p> <p align=\"center\">60 %</p> Calcio <p align=\"right\">16 %</p> Perfil de aminoácidos (por porción) L-alanina <p align=\"right\">1530mg</p> L-arginina <p align=\"right\">630 mg</p> Ácido L-aspártico <p align=\"right\">3150mg</p> Cistina/L-cisteína <p align=\"right\">720 mg</p> Ácido L-glutámico <p align=\"right\">5130mg</p> L-glicina <p align=\"right\">480 mg</p> L-histidina <p align=\"right\">480 mg</p> L-Isolaucine <p align=\"right\">1860mg</p> L-leucina <p align=\"right\">3210mg</p> L-Lycine <p align=\"right\">3330mg</p> L-metionina <p align=\"right\">660 mg</p> L-Phenylaline <p align=\"right\">840 mg</p> L-Prolina <p align=\"right\">1680mg</p> L-serina <p align=\"right\">1320mg</p> L-treonina <p align=\"right\">1950mg</p> L-triptófano <p align=\"right\">510 mg</p> L-Tryosine <p align=\"right\">840 mg</p> L-valina <p align=\"right\">1680mg</p> * % Valor diario se basa en una dieta de 2,000 calorías. Sus valores diarios pueden ser superior o inferior basados en sus necesidades de calorías. † Valor diario (VD) no establecido. <b>Ingredientes</b> IsoChill (doble temperatura fría procesado Cross-Flow espectro completo Premium microfiltrado proteína de suero), calostro, complejo de glutamina (péptidos de glutamina, Glutapure, glutamina, N-acetil L-glutamina), SI complejo (ácido alfa lipoico, D-Pinitol, 4-Hydroxyisoleucine), complejo de D (proteasa, lactasa), sabores naturales y artificiales, lactoferrina, feldespato potásico, Sucralosa, lecitina de soja Contiene leche, soja y trigo', 'Después de 5 años de investigación continua, Ultimate Nutrition se enorgullece de presentar Iso-Sensation 93 que contiene 100% de proteína de suero de leche Isolada bajo la tecnologia IsoChill....', 'ISOSENSATI789', 385.00, 355.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/iso-sensation-ultimate-nutrition-de-5-libras_main.webp\",\"\\/uploads\\/images\\/products\\/iso-sensation-ultimate-nutrition-de-5-libras_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/iso-sensation-ultimate-nutrition-de-5-libras_gallery_2.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Iso', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-07 23:56:26'),
(18, 12, 'Iso Cool 5 lbs Whey Protein Isolate', 'iso-cool-5-lbs-whey-protein-isolate', '<strong>Iso Cool Whey Protein Isolate</strong> es una deliciosa bebida sin carbohidratos y sin grasas que contiene un 100% de proteína de suero de leche aislada. La proteina de suero ayuda a incrementar la masa muscular y eliminar grasa. No contiene lactosa. La tecnología avanza cada día y <strong>Iso Cool</strong> de Ultimate Nutrition es la que tienes que seguir. El suero bioactivo más eficiente, creado usando Coldpure Ultrafilteration, está ahora en el mercado para darte los mejores beneficios para que puedas modificar tu cuerpo de la manera más efectiva. <strong>Descripción de IsoCool</strong> Iso Cool  de Ultimate Nutrition es una mezcla de suero de leche aislado Premium, ácido cítrico, sabor de manzana natural y artificial, acesulfamo de potasio, sabor de vainilla natural y artificial, FD & C amarillo 5, sucralosa, FD & C azul 1. No contiene carbohidratos, azúcar o grasa. <strong>Detalles de IsoCool</strong> Iso Cool  de Ultimate Nutrition Iso Cool es preparado Coldpure. Este proceso es una técnica muy avanzada que aísla la proteína de suero de otros componentes no esenciales, con un nivel muy alto de precisión, dándole a tu cuerpo exactamente lo que necesita. Este suero de alta calidad que se obtiene tras el proceso, es muy rica fuente de aminoácidos esenciales, y BCAA’s, lo cual estimula la síntesis de proteína. La proteína de suero es la opción para aquellos deseando construir músculos. También el uso de suero de leche como fuente de aminoácidos en Iso Cool reduce el riesgo de enfermedades como: enfermedades del corazón, cáncer. Es la solución final para aquellos buscando garantía de calidad. <strong>Características y beneficios de IsoCool</strong> <ul> <li>Contiene proteína de suero de leche</li> <li>Contiene aminoácidos esenciales</li> <li>Ayuda a la construcción de músculo</li> <li>Mejora la inmunidad</li> </ul> <h2><strong>Como Tomar IsoCool</strong></h2> Agrega 1 cucharada (scoop) de <strong>Iso Cool</strong> a 1 taza de agua purificada, leche o jugo de frutas.  Puede ser tomada al inicio del día y después del entrenamiento duro. <strong>Información nutricional por toma de 26 gramos: (1 scoop)</strong> Energía 92 Kcal Proteínas 23 gr. Carbohidratos 0 gr Grasa 0 gr. <strong>Atención</strong> Debes consultar a tu médico antes de usar este producto si estás embarazada, lactando, bajo medicación, menor de 18 años o tienes alguna enfermedad. Se debe almacenar en un lugar fresco y seco y fuera del alcance de los niños.', 'Iso Cool Whey Protein Isolate es una deliciosa bebida sin carbohidratos y sin grasas que contiene un 100% de proteína de suero de leche aislada. La proteina de suero ayuda a incrementar la masa...', 'ISOCOOL5LB228', 408.00, 378.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/iso-cool-5-lbs-whey-protein-isolate_main.jpg\",\"\\/uploads\\/images\\/products\\/iso-cool-5-lbs-whey-protein-isolate_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/iso-cool-5-lbs-whey-protein-isolate_gallery_2.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'ISOLATE', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-07 23:56:26'),
(19, 12, 'ISO-XP - 100% Whey Protein 1kg - 40 Servicios', 'iso-xp-100-whey-protein', 'ISO-XP es un suplemento rico en proteínas y de lejos el aislado de proteína de suero de leche más limpio y de mayor calidad disponible en el mercado. Básicamente, no hay otra proteína, en ningún lugar, que exceda este nivel de proteína por proporción de 100 gramos. ISO-XP está hecho de suero de leche dulce extremadamente fresco de la Unión Europea que se aísla y luego se seca por pulverización. ISO-XP no tiene SOJA agregada y en su lugar utiliza lecitina de girasol como emulsionante. ISO-XP es ideal para cualquier persona que busque aumentar la ingesta diaria de proteínas sin grasas, carbohidratos o azúcares adicionales. ISO-XP debe mezclarse con agua o leche sin lactosa para una fácil mezcla y un excelente sabor. BENEFICIOS: <ul> <li>0 gr de azúcar (por porción de 25 gr)</li> <li>0 gr de carbohidratos (por porción de 25 gr)</li> <li>0 gr de lactosa (por porción de 25 gr)</li> <li>0 gr de soja</li> <li>0 gr de grasa (por porción de 25 gr)</li> <li>Sin gluten</li> <li>Sabores: Chocolate (22.5g de proteína por porción de 25 gr) y Vainilla (23.4g de proteína por porción de 25 gr)</li> </ul>', 'ISO-XP es un suplemento rico en proteínas y de lejos el aislado de proteína de suero de leche más limpio y de mayor calidad disponible en el mercado. Básicamente, no hay otra proteína, en...', 'ISOXP100WH427', 209.00, 179.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/iso-xp-100-whey-protein_main.png\",\"\\/uploads\\/images\\/products\\/iso-xp-100-whey-protein_gallery_1.jpeg\",\"\\/uploads\\/images\\/products\\/iso-xp-100-whey-protein_gallery_2.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'WHEY', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(20, 12, 'Levro iso Kevin levrone 2 kilos', 'levro-iso-kevin-levrone-2-kilos', '<strong>Levrone Levro ISO Whey * 100% aislado de proteína de suero *</strong> Aislado de proteína de suero de primera calidad para quienes se toman en serio los resultados LevroIsoWhey es una bebida proteica deliciosa y nutritiva a base de aislado de proteína de suero de alta calidad (Isolac®). Recomendado para quienes complementen su dieta con proteínas, y especialmente diseñado para deportistas profesionales. Cada porción de 30 g proporciona 25,5 g de proteínas, que contribuyen al crecimiento y mantenimiento de la masa muscular al tiempo que fortalecen los huesos; esto lo convierte en uno de los nutrientes más importantes, en particular para todos los que realizan entrenamientos de fuerza y ​​/ o resistencia a largo plazo. *. Además, el producto tiene un bajo contenido de carbohidratos y grasas, cumpliendo con los requisitos de los atletas profesionales y permitiéndote desarrollar masa muscular magra. Desarrollado por uno de los atletas deportivos más emblemáticos y legendarios, Kevin Levrone, LevroIsoWhey es la opción óptima para quienes participan en regímenes de ejercicio de alta intensidad. Aproveche esta potente mezcla de matriz de proteínas de la serie Signature de Levrone para sacar el máximo partido a su cuerpo y a su esfuerzo por esculpir *. Desde atletas experimentados, culturistas profesionales, practicantes de artes marciales mixtas y deportes extremos, hasta guerreros de fin de semana y cualquier persona interesada en optimizar su rendimiento atlético, LevroISOWhey es el suplemento de proteína hecho y utilizado por todos. <strong>¿Qué contiene el suero de leche ISO Kevin Levrone Levro?</strong> <strong>INFORMACIÓN NUTRICIONAL</strong> Tamaño de la porción: 1 cucharada (30 g) Declaración nutricional 30 g 100 g Energía 483 kJ / 114 kcal 1611 kJ / 380 kcal Grasas 0,9 g 3,0 g de las cuales saturadas 0,4 g 1,3 g Hidratos de carbono 0,9 g 3,0 g de los cuales azúcares 0,7 g 2,5 g Fibra 0,1 g 0,5 g Proteínas 25,5 g 85,0 g Sal 0,12 g 0,4 g <strong>Ingredientes:</strong> Aislado de proteína de suero (contiene lecitina de soja) Isolac®, aromas, agente antiaglomerante (dióxido de silicio), espesante (carboximetilcelulosa), edulcorante (sucralosa). ¿Cómo usar Kevin Levrone Levro ISO Whey? Mezclar 1 cucharada de polvo (30 g) con 300 ml de agua o leche desnatada. Beber antes o inmediatamente después del entrenamiento.', 'Levrone Levro ISO Whey * 100% aislado de proteína de suero * Aislado de proteína de suero de primera calidad para quienes se toman en serio los resultados LevroIsoWhey es una bebida proteica...', 'LEVROISOKE480', 298.00, 268.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/levro-iso-kevin-levrone-2-kilos_main.png\",\"\\/uploads\\/images\\/products\\/levro-iso-kevin-levrone-2-kilos_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/levro-iso-kevin-levrone-2-kilos_gallery_2.jpg\",\"\\/uploads\\/images\\/products\\/levro-iso-kevin-levrone-2-kilos_gallery_3.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Levro', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(21, 2, 'Maniac de 30 tomas', '303', 'MANIAC es un pre-entrenamiento extremadamente alto que está casi prohibido e ilegal antes del entrenamiento porque es el mejor pre-entrenamiento para energía extrema, concentración y bombas. Maniac te ayudará a lograr unas relaciones públicas que nunca pensaste posible. Maniac es ayudarte a conquistar cualquier objetivo que te hayas fijado para el día. Te ayudará a tirar de eso toda la noche. <ul> <li>El mejor trabajo de PRE para la comodidad, la energía y la fuerza. Maniac es el suplemento de preentrenamiento de máxima resistencia más avanzado de la industria. Sólo está pensado para los usuarios avanzados</li> <li>Mejor trabajo de preparación de grasa y musculación. El preentrenamiento de Maniac promueve la quema de grasa dándole un enfoque intenso y energía para llevar su entrenamiento al siguiente nivel.</li> </ul> Característica: Maniac cuenta en su composición de beta-alanina que aumenta significativamente los niveles de carnosina en el tejido del músculo esquelético. Por lo tanto, actúa como un amortiguador, evitando el aumento de la acidez muscular y la fatiga muscular. Maniac también contiene cafeína, un ingrediente que proporcionará una explosión de energía para tus entrenamientos más intensos. <b>Consumo:</b> Mezcle 1/2 cucharada de MANIAC con 236 ml de agua fría Consumir 15 minutos antes del ejercicio No tome otros productos que contengan cafeína o cualquier otro estimulante mientras esté tomando Maniac. <b>Ingredientes:</b> Vitamina B3, vitamina B12, cafeína anhidra, extracto de corteza de yohimbe, L-alfa-glicerilfosforilcolina; beta alanina, sulfato de agmatina, creatina HCL, citrulina malato, extracto de cítricos aurantium, hoja de efedra virida. Más Información TipoSumplemento en Polvo Rendimiento30 Porción OrigenU.S.A Código de Barras742880888551 MarcaLanderfit Volumen300g PesoProducto 360g DimensionesProducto 115 x 95 x 95 mm', 'MANIAC es un pre-entrenamiento extremadamente alto que está casi prohibido e ilegal antes del entrenamiento porque es el mejor pre-entrenamiento para energía extrema, concentración y bombas....', 'MANIACDE30910', 185.00, 155.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/303_main.webp\",\"\\/uploads\\/images\\/products\\/303_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/303_gallery_2.webp\",\"\\/uploads\\/images\\/products\\/303_gallery_3.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Maniac', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(22, 2, 'B-nox x 35 Servicios', 'bnox-x-35-servicios', 'B-nox la categoría de pre-entrenamiento ha dividido recientemente en dos clases principales: el pre-entrenamiento concentrado, y el pre-entrenamiento basado mayor rendimiento durante el entrenamiento. Con ambas categorías encontrará los ingredientes entre la competencia sean similares, mas no idénticas. Así que si un cliente pide algo nuevo o se queja de una acumulación de tolerancia a los estimulantes <ul class=\"a-unordered-list a-vertical a-spacing-mini\"> <li class=\"a-spacing-mini\">Energizante avanzado + óxido nítrico: B-NOX Androrush fue científicamente formulado para apuntar a energía rápida y eficiente para ayudar a maximizar la rutina de ejercicios. Combina 200 mg de cafeína, teobromina y polvo de raíz de remolacha para apoyar los niveles de energía, la producción de óxido nítrico y el máximo rendimiento en el ejercicio.</li> </ul> <ul class=\"a-unordered-list a-vertical a-spacing-mini\"> <li class=\"a-spacing-mini\">Encendedor de testosterona: cuando se trata de maximizar tu rutina de entrenamiento, Betancourt Nutrition sabe la importancia vital de los niveles saludables de testosterona. B-NOX Androrush proporciona productos botánicos 100% naturales que se han demostrado clínicamente para ayudar a mantener la testosterona saludable. Combinando raíz de maca, tribulus estandarizado para contener 40% de saponinas y extracto de raíz de ortiga para ayudar a promover, mantener y proteger los niveles saludables de testosterona en el cuerpo.</li> </ul> <ul class=\"a-unordered-list a-vertical a-spacing-mini\"> <li class=\"a-spacing-mini\">Fuerza y resistencia: polímeros de glucosa (maltodextrina), L-taurina, beta-alanina (como CarnoSyn), malato de dicreatina y glucuronolactona para proporcionar energía y fuerza sostenidas para todo tu entrenamiento. Nuestra mezcla de fuerza y resistencia fue formulada estratégicamente para ayudar a garantizar que los efectos de B-NOX duren toda tu rutina de ejercicios.</li> <li class=\"a-spacing-mini\">Músculos sólidos: suministro de monohidrato de creatina, creatina etiléster HCL y creatina AKG para apoyar músculos fuertes. Se ha demostrado científicamente que el monohidrato de creatina apoya la resistencia muscular, la fuerza y la salud cerebral. El éster etílico de creatina HCL proporciona beneficios similares, pero se ha demostrado que proporciona una biodisponibilidad mejorada. La creatina AKG está unida al alfa-cetoglutarato, que es un precursor de la producción de óxido nítrico. La creatina AKG ayuda a mejorar el flujo sanguíneo y aumentar los nutrientes clave.</li> </ul> <ul class=\"a-unordered-list a-vertical a-spacing-mini\"> <li class=\"a-spacing-mini\">Aminoácidos clave: utiliza aminoácidos clave, como L-glutamina, L-leucina, N-acetil L-tirosina y más para apoyar la síntesis de proteínas, la utilización de proteínas, la función cognitiva y una recuperación muscular más rápida para ayudar a maximizar la rutina de ejercicio.</li> </ul>', 'B-nox la categoría de pre-entrenamiento ha dividido recientemente en dos clases principales: el pre-entrenamiento concentrado, y el pre-entrenamiento basado mayor rendimiento durante el...', 'BNOXX35SER385', 173.00, 143.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/bnox-x-35-servicios_main.jpg\",\"\\/uploads\\/images\\/products\\/bnox-x-35-servicios_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/bnox-x-35-servicios_gallery_2.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'B-nox', 0, 1, 2, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-10 00:04:34'),
(23, 2, 'Citrulina POUT 402gr', 'citrulina-pout-402gr', '<h2 class=\"ui-pdp-description__title\">Descripción</h2> <p class=\"ui-pdp-description__content\">El Malato de Citrulina es una combinación de Ácido Málico y Citrulina, elementos que juegan un papel crucial en el ciclo de Krebs.</p> La suplementación con L-citrulina optimiza el proceso de reciclado de amoniaco y el metabolismo del óxido nítrico. Esto se debe a que la ingesta de L-citrulina incrementa la arginina en plasma durante un período de tiempo más prolongado que la ingesta directa de arginina, la cual tiene un pico más alto en plasma pero de menor duración. Beneficios: - Regulación del flujo sanguíneo - Suministro de oxígeno - Captación de glucosa - Recuperación y regeneración muscular - Mejora el rendimiento en ejercicios de resistencia y de alta intensidad - Mejora la salud cardiovascular - Acelera la recuperación - No presenta efectos secundarios en la mayoría de la población - Mejora las erecciones. Aviso legal • Edad mínima recomendada: 18 años. • Este producto es un suplemento dietario, no es un medicamento. Suplementa dietas insuficientes. Consulte a su médico y/o farmacéutico.', 'Descripción El Malato de Citrulina es una combinación de Ácido Málico y Citrulina, elementos que juegan un papel crucial en el ciclo de Krebs. La suplementación con L-citrulina optimiza el...', 'CITRULINAP983', 167.00, 137.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/citrulina-pout-402gr_main.webp\",\"\\/uploads\\/images\\/products\\/citrulina-pout-402gr_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Citrulina', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(24, 2, 'C4 extreme', 'c4-extreme', '<h2>C4 XTREME – ENEGÍA EXPLOSIVA</h2> C4 Extreme está formulado con ingredientes patentados y clínicamente estudiados para alimentar sus sesiones de entrenamiento más extremas: <ul class=\"list-unstyled\"> <li>Con exclusivo amplificador de óxido nítrico (NO) de doble vía está formulado con nitratos patentados para respaldar mejores bombas.</li> <li>Amplificador de bombeo y rendimiento 2 en 1, tanto un refuerzo de creatina como de NO, se forma cuando la creatina se une a los nitratos.</li> <li>El nitrato de creatina puede ayudar a mantener la fuerza, el tamaño y el bombeo.</li> <li>Combate la fatiga y mejora la resistencia muscular.</li> <li>Cafeína anhidra , 200 mg de cafeína anhidra apoya la energía, el estado de alerta mental y el rendimiento físico y mental.</li> <li> Apoya la salud mental y cognitiva durante actividades estresantes como el entrenamiento físico.</li> <li>Puede apoyar la memoria, el aprendizaje y la concentración, cualidades clave para una fuerte conexión entre la mente y los músculos y un entrenamiento productivo.</li> </ul>', 'C4 XTREME – ENEGÍA EXPLOSIVA C4 Extreme está formulado con ingredientes patentados y clínicamente estudiados para alimentar sus sesiones de entrenamiento más extremas: Con exclusivo...', 'C4EXTREME738', 179.00, 149.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/c4-extreme_main.png\",\"\\/uploads\\/images\\/products\\/c4-extreme_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'C4', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `sale_price`, `cost_price`, `stock_quantity`, `min_stock_level`, `weight`, `dimensions`, `images`, `gallery`, `specifications`, `nutritional_info`, `usage_instructions`, `ingredients`, `warnings`, `brand`, `is_featured`, `is_active`, `views_count`, `sales_count`, `avg_rating`, `reviews_count`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(25, 2, 'Nitraflex 30 Tomas', 'nitraflex-30-tomas', '<strong>NitraFlex</strong>™<strong> </strong>de <strong>GAT</strong>™<strong> </strong>es un suplemento en polvo que contiene una fórmula de preentrenamiento totalmente única que ha sido diseñada para aumentar tus niveles naturales de testosterona, la congestión muscular y mejorar tus niveles de fuerza muscular hasta tres veces. <strong>NitraFlex</strong>™ es el pre-entreno legal más potente del mundo, del que se han vendido más de 86 millones de dosis, que contiene el ingrediente patentado CFB para la mejora de la testosterona y que cuenta con cientos de \"reviews\" favorables escritas por usuarios de todo el mundo. Se trata de un pre-entreno concentrado de nueva generación tan potente que aniquilará a los pre-entrenos clásicos. Con <strong>NitraFlex</strong>™ generarás nueva masa muscular a un ritmo anabólicamente acelerado y con una intensidad más allá de lo que podrías suponer. Conseguirás sacar a la bestia depredadora que llevas dentro, ya sea cuando compitas, en el entrenamiento o cuando necesites convocar toda tu fuerza rápidamente. El resultado de <strong>NitraFlex</strong>™ es una intensidad en plena ebullición. Comienza a usarlo y pronto sabrás por qué tiene tanto éxito. <strong>NitraFlex</strong>™ es el pre-entreno que mejora la hiperemia y el anabolismo. Muy importante: <strong>NitraFlex</strong>™ es un producto muy potente solo recomendado para atletas experimentados. <p style=\"font-weight: 400;\">La hiperemia reactiva describe el aumento del flujo sanguíneo muscular que se produce durante el ejercicio de resistencia de alta intensidad que produce unos bombeos musculares asociados con un aumento del tamaño muscular.  </p> <p style=\"font-weight: 400;\">La fórmula patentada y nutracéutica de <b><strong style=\"font-style: inherit;\">NitraFlex</strong></b>™ contiene los ingredientes propuestos por los estudios in vitro, en animales y clínicos que tienen unas propiedades que pueden ayudar a los atletas avanzados a maximizar la fuerza de la contracción muscular, intensificar y prolongar la hiperemia reactiva (bombeo muscular), y fomentar el aumento del tamaño del músculo y las ganancias de fuerza. </p> <p style=\"font-weight: 400;\">Los expertos de GAT han monitoreado los resultados que los consumidores obtuvieron al usar <b><strong style=\"font-style: inherit;\">NitraFlex</strong></b>™. Después de 60 de uso podemos afirmar que tuvieron una increíble respuesta positiva. <b><strong style=\"font-style: inherit;\">NitraFlex</strong></b>™ te proporciona una intensidad de entrenamiento muy alta, un aumento del volumen corporal y de la fuerza. Por eso es un producto tan único. <b><strong style=\"font-style: inherit;\">NitraFlex</strong></b>™ combina en un solo producto una gran vasodilatación con un aumento de la testosterona. GAT ha hecho una infusión de nuevos componentes de sinergia precisa, que en las primeras tres horas de una dosis, aumentan los niveles de testosterona en personas saludables un promedio de 10%. En un periodo de 6 semanas de suplementación con este mismo componente, los niveles de testosterona se elevan cerca del 60%. Esta perfecta combinación de ciencias agresivamente formuladas en una ciencia nutraceútica es la primera de su tipo.</p> <p style=\"font-weight: 400;\"><b><strong style=\"font-style: inherit;\">Ingredientes:</strong></b></p> <p style=\"font-weight: 400;\">Complejo precursor de Arginasa Vasoactivo-Regulador NO (Citrulina, Malato de Citrulina, Malato de L-Arginina, L-Arginina alfa-cetoglutarato, Resveratrol, Pterostilbeno), complejo de energía, atención, intensidad, neuromodulación y resistencia (Beta-Alanina (como CarnoSyn®), Cafeína, Bitartrato de DMAE, N-Acetil-L-Tirosina, Teanina, Rauwolscine (Rauvolfia Canenscens L. (Extracto de Raíz))), complejo para la mejora de la testosterona estudiado clínicamente (Borato de fructopiranosa de calcio (CFB) Patente de EE.UU. Nº 5.962.049).</p> <p style=\"font-weight: 400;\">Otros ingredientes: Polvo de fruta de piña, Ácido cítrico, Dióxido de silicio, Acesulfame de potasio, Sabores naturales y artificiales, Sucralosa, FD&C Rojo # 40, FD&C Azul # 1.</p> <p style=\"font-weight: 400;\"><b><strong style=\"font-style: inherit;\">Modo de empleo</strong></b>:  </p> <p style=\"font-weight: 400;\">Como suplemento dietético para adultos sanos, mezclar un servicio de 10 gramos (un dosificador raso) con 150 - 200 ml de agua y tomar unos 30 minutos antes del entrenamiento.</p> <p style=\"font-weight: 400;\">Debido a que se trata de un suplemento extremadamente potente, se recomienda comenzar utilizando una dosis de 5 gramos (medio dosificador raso) para evaluar la tolerancia individual.</p> <p style=\"font-weight: 400;\">Los días de descanso, se recomienda tomar un servicio de 10 gramos (un dosificador raso) al levantarse por la mañana o antes de cualquier actividad física.</p> <p style=\"font-weight: 400;\">No tomar nunca más de un servicio de 10 gramos en un período de 24 horas.</p> <p style=\"font-weight: 400;\">No tomar dentro de las cinco horas antes de acostarse.</p>', 'NitraFlex™ de GAT™ es un suplemento en polvo que contiene una fórmula de preentrenamiento totalmente única que ha sido diseñada para aumentar tus niveles naturales de testosterona, la...', 'NITRAFLEX3755', 179.00, 149.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/nitraflex-30-tomas_main.jpg\",\"\\/uploads\\/images\\/products\\/nitraflex-30-tomas_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/nitraflex-30-tomas_gallery_2.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Nitraflex', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-07 23:56:26'),
(26, 2, 'Psychotic rojo de 35 tomas', '335', '<h2 class=\"ui-pdp-description__title\">Descripción</h2> <p class=\"ui-pdp-description__content\">Una nutrición balanceada juega un papel fundamental en la calidad de vida. Con el ritmo acelerado que llevan muchas personas, a veces resulta complejo prestar atención a todos los requerimientos que el cuerpo necesita para estar sano y fuerte. En ese sentido, los suplementos cumplen la función de complementar la alimentación y ayudan a obtener las vitaminas, minerales, proteínas y otros componentes indispensables para el correcto funcionamiento del organismo.</p> *Este producto es un suplemento, no es un medicamento. Ante cualquier duda, consulte a su médico. <p class=\"ui-pdp-description__content\">Psychotic de Insane Labz es el pre-entrenamiento en polvo mas poderoso.</p> <strong>BENEFICIOS:</strong> Para usuarios AVANZADOS Energía explosiva Energía sostenida Aumenta la fuerza Reduce la fatiga <p class=\"ui-pdp-description__content\"><strong>Aviso legal</strong> • Edad mínima recomendada: 16 años. • Este producto es un suplemento dietario, no es un medicamento. Suplementa dietas insuficientes. Consulte a su médico y/o farmacéutico.</p>', 'Descripción Una nutrición balanceada juega un papel fundamental en la calidad de vida. Con el ritmo acelerado que llevan muchas personas, a veces resulta complejo prestar atención a todos los...', 'PSYCHOTICR180', 195.00, 165.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/335_main.webp\",\"\\/uploads\\/images\\/products\\/335_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Psychotic', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(27, 2, 'Psychotic gold amarillo de 35 tomas', '341', '<h2>Psychotic gold amarillo de 35 tomas</h2> <ul> <li>Beta alanina, la cual reduce la acumulación de ácido láctico evitando calambres, lo cual te ayuda a entrenar mejor.</li> <li>Cafeína anhidra, un estimulante del sistema nervioso central.</li> <li>AMPiberry® o baya de enebro, diurético que aumenta los efectos de los estimulantes.</li> <li>Extracto de Rauwolfia o yohimbina un termogénico y estimulante.</li> </ul> &nbsp; <strong>Psychotic Gold  de insane labz | </strong> es uno de los mejores pre-entreno con alto octanaje y completo que ofrece una energía similar a la original al tiempo que agrega potenciadores de la vascularización para inundar los músculos con el oxígeno que necesitan para hacer más. Prepárate para una revolución en tu entrenamiento con PSYCHOTIC GOLD 35 SERV, el suplemento preentrenamiento de élite diseñado para llevar tus límites al extremo. Cada porción de PSYCHOTIC GOLD te ofrece: <ul> <li><strong>Energía Explosiva:</strong> Experimenta una oleada de energía pura y potente que te impulsa a superar tus récords personales y conquistar tus metas fitness.</li> <li><strong>Enfoque Mental Agudo:</strong> Optimiza tu concentración y claridad mental, permitiéndote enfocarte en cada repetición y desafiar tanto a tu mente como a tu cuerpo.</li> <li><strong>Rendimiento Insuperable:</strong> Saca el máximo provecho de cada sesión de entrenamiento, ya sea levantando pesas, corriendo o participando en deportes de alto rendimiento.</li> <li><strong>Fórmula Avanzada:</strong> PSYCHOTIC GOLD 35 SERV ha sido meticulosamente formulado con ingredientes de calidad superior respaldados por la ciencia, asegurando resultados reales y duraderos.</li> </ul> &nbsp; &nbsp;', 'Psychotic gold amarillo de 35 tomas Beta alanina, la cual reduce la acumulación de ácido láctico evitando calambres, lo cual te ayuda a entrenar mejor. Cafeína anhidra, un estimulante del sistema...', 'PSYCHOTICG265', 195.00, 165.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/341_main.webp\",\"\\/uploads\\/images\\/products\\/341_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/341_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Psychotic', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(28, 2, 'Shaboom pump Kevin levron 385gr - 44 Servicios', 'shaboom-pump-kevin-levron', '<h3>Shaaboom Pump</h3> <h4>Kevin Levrone Signature Series</h4> <ul> <li>3000mg Citrulina Malato</li> <li>2500mg Beta-Alaina</li> <li>1000mg Arginina AKG</li> <li>1500mg Creatina</li> <li>200mg Cafeina</li> </ul> <strong>SHAABOOM PUMP de Kevin Levrone</strong> fue creado por una razón simple: para producir energía anomalística y bombeo muscular durante cada entrenamiento. No hay nada como el sentimiento de un entrenamiento efectivo. Esta fórmula esta enriquecida con 5000 mg de beta alanina y 2000 mg de citrulina malato (por dos scoops). Desde el primer servicio la intensidad aumenta y tiene efecto inmediato. <strong>SHAABOOM PUMP</strong> provee todo lo necesario para un entrenamiento efectivo y un cuerpo legendario. <h3>Forma de consumo:</h3> Dependiendo de tu masa muscular mezcla 1 scoop (8,75 g) con 150 – 200 ml de agua o 2 scoops (17,5 g) con 300 – 400 ml de agua. Beber un servicio unos 20 minutos antes del entrenamiento. No usar si en caso no tolere estimulantes como la cafeína. Menos de 130 Kilos 1 scoop Mas de 130 Kilos 2 scoops', 'Shaaboom Pump Kevin Levrone Signature Series 3000mg Citrulina Malato 2500mg Beta-Alaina 1000mg Arginina AKG 1500mg Creatina 200mg Cafeina SHAABOOM PUMP de Kevin Levrone fue creado por una razón...', 'SHABOOMPUM713', 139.00, 109.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/shaboom-pump-kevin-levron_main.jpg\",\"\\/uploads\\/images\\/products\\/shaboom-pump-kevin-levron_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/shaboom-pump-kevin-levron_gallery_2.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Shaboom', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(29, 14, 'Tribulus NUTREX 120 cápsulas', 'tribulus-nutrex-120-capsulas', '<strong>Tribulus Black 1300</strong> de <strong>Nutrex</strong> está elaborado a base de extracto de Tribulus terrestris con un contenido de 45% de saponinas, su principio activo. El Tribulus Terrestris es una hierba utilizada durante siglos en la medicina china y ayurvérica de la India para innumerables patologías y finalidades,  como por ejemplo por su potente efecto afrodisiaco, que incrementa la potencia sexual. Por ello ha sido utilizado durante décadas por atletas debido a sus beneficios en la salud masculina, que incluye la virilidad y la vitalidad. <strong>Tribulus Black 1300</strong> tiene propiedades que mejoran la libido, aumentando de forma natural la producción de testosterona en hombres. Además, el Tribulus terrestris contiene un componente llamado Tribulosina, que parece ser un potente cardioprotector. Entre los beneficios del tribulus terrestris, además de los anteriores ya nombrados, también destaca sus propiedades diuréticas, que protegen el hígado y riñones de los daños oxidativos, y evitando que el estrés oxidativ pueda dañar la funcionalidad de estos dos órganos. <strong>Tribulus Black 1300</strong> contiene 1300 mg de Tribulus terrestris por dosis, una cantidad optima para poder beneficiarse de los numerosos beneficios de esta planta. <h3>Modo de empleo:</h3> Como suplemento dietético, tomar 2 cápsulas una vez al día con agua. Este producto puede tomarse con o sin alimentos. Use diariamente para obtener mejores resultados. <h2 class=\"cabecera2\">Información Nutricional: <strong><i>Tribulus Black 1300 - 120 Caps.</i></strong></h2> Ingredientes: Extracto de Tribulus terrestris (fruto) (45% de saponinas), Hidroxipropilmetilcelulosa, celulosa microcristalina, sílice, estearato de magnesio, FD&C blue no 1, FD & C red no. 40. Modo de empleo: Como suplemento dietético, tomar 2 cápsulas una vez al día con agua. Este producto puede tomarse con o sin alimentos. Use diariamente para obtener mejores resultados. Conservación: Manteener en un lugar fresco, seco y alejado de la luz solar directa. Advertencias: Este producto está destinado a ser usado por adultos sanos normales y no debe ser tomado por ninguna persona con afecciones médicas conocidas. No utilizar en caso de embarazo o la lactancia. Mantener fuera de los niños. Dosis 2 cápsulas Dosis diaria 2 cápsulas Servicios por envase 60 (aprox.)', 'Tribulus Black 1300 de Nutrex está elaborado a base de extracto de Tribulus terrestris con un contenido de 45% de saponinas, su principio activo. El Tribulus Terrestris es una hierba utilizada...', 'TRIBULUSNU198', 149.00, 119.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/tribulus-nutrex-120-capsulas_main.jpg\",\"\\/uploads\\/images\\/products\\/tribulus-nutrex-120-capsulas_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Tribulus', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-07 23:56:26'),
(30, 14, 'Testrol original GAT 60 cápsulas', 'testrol-original-gat-60-capsulas', 'Información del Producto: Testrol Original es el último producto de rendimiento de doble propósito que contiene potenciadores masculinos naturales. Tome Testrol si quiere una forma efectiva de desarrollar músculo y aumentar el rendimiento masculino. Instrucciones de Uso: Hombres adultos, tomar 2 tabletas todos los días en un estómago ligero a vacío. INSTRUCCIONES IMPORTANTES Beba 2 vasos llenos de jugo de uva o fruta dentro de 1 hora después de cada entrenamiento para optimizar la actividad de Insulina. Consuma 5 gramos de BCAAs (aminoácidos de cadena ramificada) después de cada entrenamiento. Tomar 5-10 gramos de Creatina al día. Tome L-Glutamina después del entrenamiento y antes de acostarse.', 'Información del Producto: Testrol Original es el último producto de rendimiento de doble propósito que contiene potenciadores masculinos naturales. Tome Testrol si quiere una forma efectiva de...', 'TESTROLORI941', 155.00, 125.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/testrol-original-gat-60-capsulas_main.jpg\",\"\\/uploads\\/images\\/products\\/testrol-original-gat-60-capsulas_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/testrol-original-gat-60-capsulas_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Testrol', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(31, 14, 'Testrol Próstate 90 cápsulas', 'testrol-prostate-90-capsulas', '<h2 class=\"ui-pdp-description__title\">Descripción</h2> <p class=\"ui-pdp-description__content\">El Testrol Prostata Gat Sports es un suplemento nutricional/deportivo diseñado para brindar un soporte óptimo a la próstata. Con ingredientes de alta calidad como extractos de Saw Palmetto y Zinc, este producto está formulado para ayudar a mantener la salud de la próstata y promover un funcionamiento adecuado. La marca GAT SPORTS es reconocida por su compromiso con la calidad y la eficacia de sus productos. Con el Testrol Prostata, puedes estar seguro de que estás obteniendo un suplemento confiable y efectivo para el cuidado de tu próstata. El formato en cápsulas facilita su consumo y el frasco de 90 cápsulas te brinda un suministro duradero. Además, su sabor sin sabor hace que sea fácil de tomar sin interferir con otros alimentos o bebidas. Recomendado para personas mayores de 18 años, el Testrol Prostata es ideal para aquellos que desean mantener una próstata saludable y prevenir posibles problemas en el futuro. Su peso neto de 200 g garantiza que estás obteniendo una cantidad adecuada de suplemento en cada cápsula. No pierdas la oportunidad de cuidar tu próstata de manera efectiva y segura. ¡Aprovecha los beneficios del Testrol Prostata Gat Sports y mantén tu salud en óptimas condiciones! Aviso legal • Edad mínima recomendada: 18 años. • Este producto es un suplemento dietario, no es un medicamento. Suplementa dietas insuficientes. Consulte a su médico y/o farmacéutico.</p>', 'Descripción El Testrol Prostata Gat Sports es un suplemento nutricional/deportivo diseñado para brindar un soporte óptimo a la próstata. Con ingredientes de alta calidad como extractos de Saw...', 'TESTROLPRS428', 179.00, 149.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/testrol-prostate-90-capsulas_main.webp\",\"\\/uploads\\/images\\/products\\/testrol-prostate-90-capsulas_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/testrol-prostate-90-capsulas_gallery_2.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Testrol', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(32, 14, 'Testrol Gold', 'testrol-gold', '<h2>Testrol Gold</h2> es el estimulador de la testosterona de los hombres de GAT . Esta fórmula avanzada naturalmente apoya rendimiento masculino, resistencia, fuerza y la testosterona, con el beneficio añadido de los reguladores hormonales. Promueve una intensa actividad anabólica. Promueve un aumento de los niveles de testosterona y de testosterona libre. Reduce los niveles de cortisol. Potencia la absorción de creatina por las células musculares. Es compatible con una reducción en la grasa corporal. Ayuda al mantenimiento de la masa muscular. Promueve la vitalidad masculina, un mejor rendimiento físico así como una mayor energía. <h2 class=\"w-100 ma0 pa3 f5 lh-copy normal undefined\" aria-hidden=\"false\">Especificaciones</h2> Vida Útil en Tienda (días) 234 días <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Modelo / Estilo / Tipo</h3> GAT TESTROL GOLD ES 60 PASTILLAS <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Grasa total por porción</h3> 0.1 g <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Contiene pesticida</h3> No <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Capacidad Litros</h3> 0.250 lt <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Palabras clave</h3> Deportes <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Es sensible a los cambios de temperatura</h3> No <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Etapa de actividad</h3> Pre-Entrenamiento <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Contiene sustancias químicas</h3> No <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Tamaño de la porción</h3> 2 tabletas (1.9gr) <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">País de Origen</h3> Estados Unidos <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">¿Contiene componentes electrónicos?</h3> No <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Carbohidratos totales</h3> 0.3 g <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Género</h3> Unisex <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Azúcar por porción</h3> 0 <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Contenido del Empaque</h3> SUPLEMENTO <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Grupo de Edad</h3> Adulto <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Ancho del Producto Armado</h3> 10 cm <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Porciones por envase</h3> 30 <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Proteína total por porción</h3> 0.95 g <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Contiene gas comprimido (aerosol)</h3> No <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Información de la Garantía</h3> SE RESPETA MIENTRAS NO ESTE ROTO O ABIERTO EL PRODUCTO <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Altura del Producto Armado</h3> 15 cm <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Largo del Producto Armado</h3> 20 cm <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Edad recomendada</h3> 18 years <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Sabor</h3> Sin sabor <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Usos Recomendados</h3> Testosterona <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Peso del Producto Armado</h3> 1 kg <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Condición</h3> New <h3 class=\"flex items-center mv0 lh-copy f5 pb1 dark-gray\">Calorías por porción</h3> 25 Calorías', 'Testrol Gold es el estimulador de la testosterona de los hombres de GAT . Esta fórmula avanzada naturalmente apoya rendimiento masculino, resistencia, fuerza y la testosterona, con el beneficio...', 'TESTROLGOL293', 188.00, 158.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/testrol-gold_main.jpg\",\"\\/uploads\\/images\\/products\\/testrol-gold_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/testrol-gold_gallery_2.jpg\",\"\\/uploads\\/images\\/products\\/testrol-gold_gallery_3.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Testrol', 1, 1, 0, 0, 0.00, 0, '', '', NULL, '2025-07-06 04:01:09', '2025-07-07 23:56:26'),
(33, 15, 'Multites GAT', 'multites-gat', '<h3><strong>Descripción</strong></h3> <ul> <li>Tamaño conveniente.</li> <li>Producto para favorecer la testosterona en hombres.</li> <li>Productos básicos.</li> <li>Ayuda a tener un funcionamiento inmunológico saludable.</li> <li>Apoyo nutricional completo para atletas.</li> <li>GAT Authentic - Satisfaction Guaranteed: producto auténtico certificado por GAT; satisfacción garantizada.</li> <li>Fórmula para hombres con un estilo de vida activo.</li> <li>Suplemento alimentario.</li> <li>#CompitaMejor</li> </ul> <strong>Lo máximo en vitaminas para el rendimiento</strong> Este Suplemento multivitamínico con testosterona para hombres es un completo suplemento multivitamínico que contiene vitaminas, minerales, energía y el ingrediente agregado para la virilidad masculina Tribulus terristis. El Suplemento multivitamínico para hombres de GAT les ofrece a los atletas el refuerzo completo para cubrir las deficiencias de nutrientes que puedan tener con un agregado de testosterona y virilidad. <h3><strong>Uso Sugerido</strong></h3> Como suplemento dietario, tome una (1) porción de dos (2) comprimidos todos los días, con una comida. <h3><strong>Otros Ingredientes</strong></h3> Celulosa microcristalina, ácido esteárico, fosfato dicálcico, croscarmelosa sódica, estearato de magnesio, dióxido de silicio, glaseado farmacéutico (goma laca, povidona). <h3><strong>Advertencias</strong></h3> Consulte a su médico antes de usar este producto si usted es menor de 18 años, está embarazada, lactando o intentando quedar embarazada, toma medicamentos, tiene una afección médica o actualmente desconoce su estado de salud. El consumo de este producto puede provocar exposición al plomo, el cual se reconoce en el estado de California como causa de malformaciones congénitas u otros daños reproductivos. Almacene el producto en un lugar fresco y seco. Mantenga fuera del alcance de los niños. No use el producto si falta el sello de la boca del envase o este está roto.', 'Descripción Tamaño conveniente. Producto para favorecer la testosterona en hombres. Productos básicos. Ayuda a tener un funcionamiento inmunológico saludable. Apoyo nutricional completo para...', 'MULTITESGA180', 135.00, 105.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/multites-gat_main.jpg\",\"\\/uploads\\/images\\/products\\/multites-gat_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/multites-gat_gallery_2.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Multites', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(34, 15, 'Optimen 150 Tabletas – Optimum Nutrition', 'optimen-150-tabletas-optimum-nutrition', '<h1 class=\"product_title entry-title wd-entities-title\">Optimen 150 Tabletas – Optimum Nutrition</h1> Complemento alimenticio con vitaminas, minerales, aminoácidos y extractos de hierbas. Los complementos alimenticios no deben utilizarse como sustitutos de una dieta variada. No exceda la dosis diaria recomendada. Mantener fuera del alcance de los niños. 150 Tabletas <h2>OBJETIVOS</h2> MULTIVITAMINA PARA HOMBRES ACTIVOS. Cuando entrena duro, su cuerpo tiene mayores necesidades nutricionales. Las vitaminas y los minerales brindan un impulso nutricional vital para ayudarlo a enfrentar las demandas de un estilo de vida acelerado. Las vitaminas A, D, C, B6, B12, el ácido fólico, el cobre y el selenio contribuyen al funcionamiento normal del sistema inmunológico. Las vitaminas C, B2, B3, B5, B6, B12, el ácido fólico y el magnesio contribuyen a la reducción del cansancio y la fatiga. <h2>MODO DE EMPLEO</h2> Consumir 3 comprimidos con la comida. Diseñado para su uso en adultos sanos como parte de una dieta sana y equilibrada y un programa de ejercicio. <h2>ADVERTENCIAS</h2> OPTI-MEN (DE-2087). Suplemento dietético. No supere la dosis diaria expresamente recomendada para productos dietéticos. Los productos dietéticos no deben utilizarse como sustituto de una dieta equilibrada. Antes de consumir consulte a su médico. No utilizar en caso de embarazo, lactancia ni en niños. Hipersensibilidad a alguno de los componentes. &nbsp;', 'Optimen 150 Tabletas – Optimum Nutrition Complemento alimenticio con vitaminas, minerales, aminoácidos y extractos de hierbas. Los complementos alimenticios no deben utilizarse como sustitutos de...', 'OPTIMEN150718', 189.00, 159.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/optimen-150-tabletas-optimum-nutrition_main.webp\",\"\\/uploads\\/images\\/products\\/optimen-150-tabletas-optimum-nutrition_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/optimen-150-tabletas-optimum-nutrition_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Optimen', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(35, 15, 'Multivitaminico para mujer Forzagen Vital Women 60 pastillas', 'multivitaminico-para-mujer-forzagen-vital-women-60-pastillas', '<h2>Descripción</h2> – Fórmula de vitaminas y minerales especialmente diseñada para mujeres, balanceada para sus necesidades nutrimentales diarias – Con complejo de belleza a base de biotina, colágeno hidrolizado, ácido hialurónico y levadura para mejorar la salud de la piel, cabello y uñas – Protege la salud con poderosos antioxidantes que eliminan los radicales libres – Tomar 1 tableta diaria preferiblemente con un alimento, no exceder la dosis diaria – Forzagen Essentials, producto hecho en EUA 100% garantizado Te presentamos Women’s Multivitamin de Forzagen, el aliado perfecto para toda mujer que busca verse y sentirse bien. || Vitaminas y Minerales = Salud || 26 Vitaminas y Minerales presentes en esta increíble fórmula diseñada específicamente para aportar los nutrimentos necesarios y promover la salud integral día con día. || Increíble Complejo de Belleza || Con increíbles ingredientes naturales como Vitamina E, Biotina, Colágeno Hidrolizado, Ácido Hialurónico y Levadura, enfocados en hidratar y proteger piel, cabello y uñas para lucir radiante. >> SALUD INTERNA << Con Women’s Multivitamin aporta a tu cuerpo los nutrimentos diarios que necesitas para mantener tu salud en su máximo nivel. >> BELLEZA EXTERNA << Refleja lo que llevas dentro con los 4 ingredientes para belleza incluidos en Women’s Multivitamin. >> CABELLO, PIEL Y UÑAS << El aliado perfecto para mantener tu cabello brillante, uñas radiantes y piel hidratada. >> POR ENVASE << Un frasco de Women’s Multivitamin rinde para 60 porciones (1 tableta). ¡Suficiente para un consumo bimestral promedio!', 'Descripción – Fórmula de vitaminas y minerales especialmente diseñada para mujeres, balanceada para sus necesidades nutrimentales diarias – Con complejo de belleza a base de biotina, colágeno...', 'MULTIVITAM524', 109.00, 79.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/multivitaminico-para-mujer-forzagen-vital-women-60-pastillas_main.jpg\",\"\\/uploads\\/images\\/products\\/multivitaminico-para-mujer-forzagen-vital-women-60-pastillas_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/multivitaminico-para-mujer-forzagen-vital-women-60-pastillas_gallery_2.jpg\",\"\\/uploads\\/images\\/products\\/multivitaminico-para-mujer-forzagen-vital-women-60-pastillas_gallery_3.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Multivitaminico', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-07 23:56:26'),
(36, 2, 'Preentreno CBUM DE 30 TOMAS 405gr', 'preentreno-cbum-de-30-tomas-405gr', '<h3 class=\"text-start title-default\"><b>Descripción del Producto</b></h3> Información del Producto: Pre-entrenamiento esencial de naranja: el Essential Pre de RAW Nutrition es un pre-entrenamiento completo que es perfecto para todos los levantadores, desde principiantes hasta avanzados. Ya sea tu primer día en el gimnasio o si eres un veterano experimentado, estos ingredientes de alta potencia como L-citrulina y cafeína funcionan igual. 30 porciones, 14.29 oz Sin tonterías, todo sabor: Nos enorgullecemos de crear productos que equilibren perfectamente las escalas de calidad y sabor. Alcanza tu potencial con nuestro sabor a naranja. Beneficios de Pre: Cada cucharada se empaqueta en una dosis considerable de 0.14 oz de L-citrulina para maximizar el flujo sanguíneo y suministrar bombas serias. Hemos combinado esto con 0.11 oz de Beta Alanine para combatir la fatiga, lo que te permite empujar el sobre y la potencia a través de las mesetas. Finalmente, empaquetamos 200 mg de cafeína para alcanzar ese punto dulce de energía estimulante para durar desde la primera hasta la última repetición de tu entrenamiento. El nuevo estándar: Diseñamos Essential Pre para darte lo mejor de los 3 mundos: energía duradera, enfoque nítido y potencia bombeada. Fabricado en los Estados Unidos. Sin ingredientes artificiales, sin OMG, sin BS. Construido desde cero: RAW Nutrition fue creado para proporcionar a los atletas el mejor combustible para el máximo entrenamiento y rendimiento. Nuestra misión es hacer que la nutrición inteligente sea fácil y conveniente con suplementos elaborados por expertos hechos con los ingredientes más puros disponibles en la industria.', 'Descripción del Producto Información del Producto: Pre-entrenamiento esencial de naranja: el Essential Pre de RAW Nutrition es un pre-entrenamiento completo que es perfecto para todos los...', 'PREENTRENO236', 185.00, 155.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/preentreno-cbum-de-30-tomas-405gr_main.jpg\",\"\\/uploads\\/images\\/products\\/preentreno-cbum-de-30-tomas-405gr_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Preentreno', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(37, 2, 'Nox plood de 555gr 30 tomas', 'nox-plood-de-555gr-30-tomas', '<h2 class=\"ui-pdp-description__title\">Descripción</h2> <p class=\"ui-pdp-description__content\">Una nutrición balanceada juega un papel fundamental en la calidad de vida. Con el ritmo acelerado que llevan muchas personas, a veces resulta complejo prestar atención a todos los requerimientos que el cuerpo necesita para estar sano y fuerte. En ese sentido, los suplementos cumplen la función de complementar la alimentación y ayudan a obtener las vitaminas, minerales, proteínas y otros componentes indispensables para el correcto funcionamiento del organismo. *Este producto es un suplemento, no es un medicamento. Ante cualquier duda, consulte a su médico.</p>', 'Descripción Una nutrición balanceada juega un papel fundamental en la calidad de vida. Con el ritmo acelerado que llevan muchas personas, a veces resulta complejo prestar atención a todos los...', 'NOXPLOODDE177', 205.00, 175.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/nox-plood-de-555gr-30-tomas_main.webp\",\"\\/uploads\\/images\\/products\\/nox-plood-de-555gr-30-tomas_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Nox', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(38, 2, 'Psychotik blanco insane lab - 30 Tomas', 'psychotik-blanco-insane-lab', 'Psychotic SAW es el preentrenamiento más LOCO jamás realizado por Mad Chemist y su equipo en Insane Labz. ¡Han empaquetado la fórmula LLENA de ingredientes que trabajan juntos para brindarte todo lo que necesitas para MATAR en el gimnasio! <ul class=\"a-unordered-list a-vertical a-spacing-mini\"> <li class=\"a-spacing-mini\">CADA PIEZA TIENE UN ROMPECABEZAS. Psychotic SAW está repleto de ingredientes probados, incluida la beta alanina para reducir el ácido láctico, la cafeína para la energía y la concentración, así como DMAE para la función cognitiva y el estado de ánimo. Pero no nos detuvimos ahí, incluimos estos ingredientes patentados y registrados. TeaCrine y CognitIQ para mejorar la energía, el estado de ánimo y la función cognitiva, así como OxyGold y Ampiberry para ayudar en la biodisponibilidad y absorción.</li> <li class=\"a-spacing-mini\">ESCAPE DE LA TRAMPA. ¿Atrapado por marcas que hacen promesas pero no cumplen? Vea por qué más de 100,000 reseñas en línea de productos Insane Labz confirman lo que ya sabemos: ¡los ingredientes correctos trabajando juntos pueden hacer todo lo que necesita, y más! ¡Pruebe PSYCHOTIC SAW hoy y ESCAPE DE LA TRAMPA!</li> </ul>', 'Psychotic SAW es el preentrenamiento más LOCO jamás realizado por Mad Chemist y su equipo en Insane Labz. ¡Han empaquetado la fórmula LLENA de ingredientes que trabajan juntos para brindarte todo...', 'PSYCHOTIKB712', 195.00, 165.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/psychotik-blanco-insane-lab_main.jpg\",\"\\/uploads\\/images\\/products\\/psychotik-blanco-insane-lab_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/psychotik-blanco-insane-lab_gallery_2.jpg\",\"\\/uploads\\/images\\/products\\/psychotik-blanco-insane-lab_gallery_3.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'LAB', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(39, 2, 'Beta alanina NFS de 100 tomas 320gr', 'beta-alanina-nfs-de-100-tomas-320gr', 'BetaALA Powder de NFSPORTS® Nutrition es un suplemento en polvo que proporciona el 100% de beta alanina de gran pureza y calidad. La beta-alanina nfs es ideal para las personas que realizan programas de entrenamiento físico intensos y de larga duración. es un aminoácido no esencial que usan las células musculares para sintetizar la carnosina, un dipéptido que consiste en beta alanina más L-histidina. La beta alanina es un precursor de la carnosina. La carnosina se encuentra en altas concentraciones en el músculo esquelético, y se encuentra sobre todo en las fibras de tipo II de los músculos de contracción rápida. Aumentar la carnosina en los músculos a través de suplementos tiene una creciente popularidad entre los culturistas y los atletas entrenados. Los beneficios de la suplementación con Beta-Alanina radican principalmente en su capacidad para aumentar las concentraciones de carnosina muscular. De hecho, la beta-alanina es el aminoácido limitante en la síntesis de carnosina, lo que significa que su presencia en el torrente sanguíneo está directamente relacionada con los niveles de carnosina en los músculos. La forma más básica de mejorar su capacidad para retrasar este tipo de fatiga es entrenar dentro del umbral de lactato (empujar el límite de su capacidad para rendir a alta intensidad). Para la mayoría de nosotros, eso suena como una tarea desalentadora y probablemente más de lo que nos inscribimos. Pero para los atletas avanzados, encontrar un suplemento para mejorar el rendimiento durante el entrenamiento de alta intensidad es un cambio de juego. No importa cuál sea su nivel de condición física, este suplemento respalda su capacidad para realizar y mejorar el equilibrio del pH de sus músculos durante el ejercicio.', 'BetaALA Powder de NFSPORTS® Nutrition es un suplemento en polvo que proporciona el 100% de beta alanina de gran pureza y calidad. La beta-alanina nfs es ideal para las personas que realizan...', 'BETAALANIN830', 175.00, 145.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/beta-alanina-nfs-de-100-tomas-320gr_main.webp\",\"\\/uploads\\/images\\/products\\/beta-alanina-nfs-de-100-tomas-320gr_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Beta', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(40, 1, 'Psychotic de 35 tomas 328 grs', 'psychotic-de-25-tomas-328-grs', 'Una nutrición balanceada juega un papel fundamental en la calidad de vida. Con el ritmo acelerado que llevan muchas personas, a veces resulta complejo prestar atención a todos los requerimientos que el cuerpo necesita para estar sano y fuerte. En ese sentido, los suplementos cumplen la función de complementar la alimentación y ayudan a obtener las vitaminas, minerales, proteínas y otros componentes indispensables para el correcto funcionamiento del organismo. Recomendado para la actividad aeróbica La cafeína es un suplemento utilizado por muchas personas que realizan deporte, ya que tiene muchos beneficios: ayuda a reducir la sensación de cansancio y la fatiga en ejercicios prolongados, permite hacer más largos los entrenamientos y estimula el sistema nervioso central. Todo esto lo convierte en un suplemento ideal para actividades aeróbicas. *Este producto es un suplemento, no es un medicamento. Ante cualquier duda, consulte a su médico.', 'Una nutrición balanceada juega un papel fundamental en la calidad de vida. Con el ritmo acelerado que llevan muchas personas, a veces resulta complejo prestar atención a todos los requerimientos...', 'PSYCHOTICD896', 189.00, 159.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/psychotic-de-25-tomas-328-grs_main.jpg\",\"\\/uploads\\/images\\/products\\/psychotic-de-25-tomas-328-grs_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Psychotic', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(41, 14, 'Boom Stick REDCOM 300 cápsulas', 'boom-stick-redcom-300-capsulas', '<h3><strong>Descripción</strong></h3> <ul> <li>Apoya el equilibrio hormonal</li> <li>Promueve el músculo magro*</li> <li>Mejora el rendimiento</li> <li>Suplemento dietético</li> <li>Apoya niveles saludables de testosterona</li> <li>Mejora la fuerza y ​​la vitalidad*</li> <li>Aumenta el estado de ánimo y la energía</li> <li>Nutravigilance® Verified - Soluciones de seguridad suplementaria</li> <li>3 g de ácido D-aspártico</li> <li>1 g de ashwagandha</li> <li>30 mg de zinc</li> <li>100 mg de magnesio</li> </ul> <strong>Soporte avanzado de testosterona</strong> La testosterona es una hormona responsable de muchas funciones importantes en el cuerpo. Boom Stick está diseñado para apoyar la producción natural de testosterona de tu cuerpo, optimizar el máximo rendimiento y ayudarte a sentirte en tu mejor momento.* *Cuando se combina con entrenamiento de resistencia y una dieta rica en proteínas. <h3><strong>Uso sugerido</strong></h3> <strong>Para mejores resultados:</strong> Cómo tomar: Tomar (10) cápsulas al día. Cuándo tomar: en cualquier momento con una comida Las porciones pueden espaciarse igualmente a lo largo del día. Después de 8 semanas, se recomienda suspender su uso durante 2-4 semanas. <h3><strong>Otros ingredientes</strong></h3> Gelatina (cápsula), estearato de magnesio, dióxido de silicio. <h3><strong>Advertencias</strong></h3> Este producto está destinado a ser consumido por adultos sanos mayores de 18 años. No lo use si está embarazada, amamantando, tomando algún medicamento o suplemento recetado o de venta libre, o si tiene o sospecha que puede tener una condición médica, que incluye, entre otros: presión arterial alta o baja, arritmia cardíaca. , accidente cerebrovascular, enfermedad cardíaca, hepática o renal, trastorno convulsivo, enfermedad de la tiroides, enfermedad psiquiátrica, diabetes, agrandamiento de la próstata o si está tomando un inhibidor de la MAO. No lo use si es propenso a la deshidratación o está expuesto al calor excesivo. Como ocurre con cualquier complemento dietético, consulte con un profesional sanitario antes de utilizar este producto. Suspender 2 semanas antes de la cirugía. Suspenda su uso inmediatamente y consulte a un profesional de la salud si experimenta alguna reacción adversa. Mantener fuera del alcance de los niños Guardar en un lugar fresco y seco. Proteger de la luz y la humedad.', 'Descripción Apoya el equilibrio hormonal Promueve el músculo magro* Mejora el rendimiento Suplemento dietético Apoya niveles saludables de testosterona Mejora la fuerza y ​​la vitalidad*...', 'BOOMSTICKR929', 155.00, 125.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/boom-stick-redcom-300-capsulas_main.jpg\",\"\\/uploads\\/images\\/products\\/boom-stick-redcom-300-capsulas_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Boom', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(42, 15, 'EXPEL diuretico 80 cap', 'expel-diuretico-80-cap', '<ul class=\"a-unordered-list a-vertical a-spacing-mini\"> <li class=\"a-spacing-mini\">Diurético a base de hierbas totalmente natural para hombres y mujeres que favorece la rápida pérdida de agua y una mayor definición muscular</li> <li class=\"a-spacing-mini\">Reduce la hinchazón y apoya la pérdida de grasa</li> <li class=\"a-spacing-mini\">Potente complejo de electrolitos anticalambres</li> <li class=\"a-spacing-mini\">Cafeína natural de té verde y guaraná para favorecer la energía limpia y la quema de grasas</li> <li class=\"a-spacing-mini\">Probado en el tiempo, millones de botellas vendidas.</li> <li class=\"a-spacing-mini\">Sólo para adultos sanos. Consulte a su médico si está embarazada, amamantando, tomando medicamentos o tiene alguna condición médica. No lo use si el sello está roto. Si siente molestias, suspenda su uso y contáctenos para obtener un reembolso completo. Contiene cafeína: demasiada cafeína puede provocar nerviosismo, irritabilidad, insomnio y, ocasionalmente, taquicardia, mareos, nerviosismo, náuseas y dolor de estómago. Mantenga este producto y todos los suplementos fuera del alcance de los niños.</li> </ul>', ' Diurético a base de hierbas totalmente natural para hombres y mujeres que favorece la rápida pérdida de agua y una mayor definición muscular Reduce la hinchazón y apoya la pérdida de grasa...', 'EXPELDIURE245', 149.00, 119.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/expel-diuretico-80-cap_main.jpg\",\"\\/uploads\\/images\\/products\\/expel-diuretico-80-cap_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/expel-diuretico-80-cap_gallery_2.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Expel', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(43, 16, 'Lipodrene rojo 90 pastillas', '473', '<p class=\"a-product__paragraphProductDescriptionWeb d-none d-lg-block mb-1\">Descripción:</p> Lipodrene ROJO incluye una construcción de triple capa y polímeros de cuentas esféricas. Estas dos tecnologías son la razón por la que Lipodrene® XtremeV2.0 proporciona una sensación estimulante excepcionalmente suave, duradera y sin choques, Lipodrene ROJO incluye una construcción de triple capa y polímeros de cuentas esféricas. Estas dos tecnologías son la razón por la que Lipodrene® XtremeV2.0 proporciona una sensación estimulante excepcionalmente suave, duradera y sin choques, sin crash. <strong>Lipodrene Xtreme</strong> es la presentación que ha sido desarrollado para cumplir con su función a un nivel que va un poco más allá que en la versión normal de este quemagrasa. Así mismo, no hay que encasillar necesariamente las tabletas de Lipodrene Xtreme como una opción por la que se puede optar tan sólo si se quiere perder peso, sino que es igualmente un energizante de origen natural. BENEFICIOS – Máxima reducción de grasa – Penetración de grasa en zonas localizadas – Control del apetito MODO DE USO Consumir 1 cápsula 30-40 minutos antes del entrenamiento.', 'Descripción: Lipodrene ROJO incluye una construcción de triple capa y polímeros de cuentas esféricas. Estas dos tecnologías son la razón por la que Lipodrene® XtremeV2.0 proporciona una...', 'LIPODRENER573', 199.00, 169.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/473_main.webp\",\"\\/uploads\\/images\\/products\\/473_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Lipodrene', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(44, 16, 'Lipodrene hardcore negro 90 pastillas', 'lipodrene-hardcore-negro-90-pastillas', '<strong>Descripcion de Lipodrene Hardcore</strong> – Eviscera las células de grasa a través de la Apoptosis. – Termogénico Extremo y Agente de Repartición – Formulación Renovada para los fans leales de Lipodrene. – Quema de grasa extrema para una seria perdida de peso! – Estimulantes extremos para una dosis de energía abre-ojos, incluyendo el nuevo y potente estimulante 1,3 Dimetilamilamina (DMAA). <strong>Lipodrene Hardcore</strong> es exactamente lo que su nombre dice que es. Tomas<strong>Lipodrene Hardcore</strong> y luego lo ajustas con una tecnología en tabletas nueva para que te afecte más rápido y dure más tiempo. Es el nuevo <strong>Lipodrene Hardcore</strong>! <strong>Lipodrene Hardcore</strong> es un suplemento de pérdida de peso de alta tecnología y mejorador de energía diseñado para saciar la sed de los atletas “difíciles de complacer”. <strong>Lipodrene Hardcore</strong> incluye tecnologías de “rápida liberación” y “liberación prolongada”. Estas dos tecnologías son él porque<strong>Lipodrene Hardcore</strong> provee una sensación estimulante tan rápida, duradera y sin caída. Como puedes ver, el mejorador de energía y pérdida de peso <strong>Lipodrene Hardcore</strong> encontró un impecable perfil de ingredientes que contiene sólo ingredientes como de arte diseñados para proveerte con exactamente lo que necesitas para lanzar un ataque multi-direccional a la grasa. <strong>Lipodrene Hardcore</strong>es el quema grasa y estimulante con mejor dosis existente. La capa externa de la tableta de Lipodrene Hardcore contiene la tecnología Explotab, para una quema inmediata de ingredientes, produciendo una rápida fuente de energía! La capa interna libra una dosis sostenida de activos ingredientes en el sistema sanguíneo para una energía de larga duración y un efecto continuo de pérdida de peso por severas horas. Cuando tomas <strong>Lipodrene Hardcore</strong> ojalá estes listo para una súper dosis de energía de “ojos abiertos” que te llevará de 0 a 60 en un segundo! <strong>Lipodrene Hardcore</strong> te ayudará a perder peso y sentirte bien todo el día. Con <strong>Lipodrene Hardcore</strong>, prepárate para notar un humor espectacular, montar una ola ininterrumpida de energía desde que te lo tomas hasta que te vas a acostar, y, por último pero no por eso menos importante, por fin empezar a deshacerte de esa grasa indeseada! Ya sea que busques definirte y portar un buen abdomen de six pack, o una mujer buscando curvas sexys que hará que todos volteen a verte, <strong>Lipodrene Hardcore</strong> es tu solución!', 'Descripcion de Lipodrene Hardcore – Eviscera las células de grasa a través de la Apoptosis. – Termogénico Extremo y Agente de Repartición – Formulación Renovada para los fans leales de...', 'LIPODRENEH488', 199.00, 169.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/lipodrene-hardcore-negro-90-pastillas_main.webp\",\"\\/uploads\\/images\\/products\\/lipodrene-hardcore-negro-90-pastillas_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Lipodrene', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `sale_price`, `cost_price`, `stock_quantity`, `min_stock_level`, `weight`, `dimensions`, `images`, `gallery`, `specifications`, `nutritional_info`, `usage_instructions`, `ingredients`, `warnings`, `brand`, `is_featured`, `is_active`, `views_count`, `sales_count`, `avg_rating`, `reviews_count`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(45, 16, 'LIPODRENE AMARILLO - 90 Tabletas', 'lipodrene-amarillo', '<strong>LIPODRENE</strong> Es una mezcla de plantas naturales y lipotrópicos especialmente patentados como un sistema de quema de grasa de 3 vías que no sólo ayuda a quemar calorías y grasa, sino que también controla los antojos de azúcar y suprime el apetito. Ha sido clínicamente probado por ser un 29% más eficaz que Redux el quemador de grasa ¿Cómo funciona Lipodrene? Lipodrene se basa en el proceso de estimular la «lipólisis» (liberación de grasa) y sirve para inhibir la «lipogénesis» (acumulación de grasa). De hecho inhibe la secreción de las enzimas específicas que controlan el almacenamiento de grasa y también estimula la energía celular, causando que efectivamente las células grasas individuales liberen ácidos grasos indeseables. Lipodrene es para aquellos que están buscando un producto que sirva como quemador de grasa, estimulante energético, supresor del apetito, y diurético natural. Una propiedad única de este medicamento es su capacidad para atacar y quemar la grasa en las zonas más rebeldes (caderas, muslos y nalgas). Lipodrene es un producto que debe probar si le resulta difícil perder peso', 'LIPODRENE Es una mezcla de plantas naturales y lipotrópicos especialmente patentados como un sistema de quema de grasa de 3 vías que no sólo ayuda a quemar calorías y grasa, sino que también...', 'LIPODRENEA177', 209.00, 179.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/lipodrene-amarillo_main.png\",\"\\/uploads\\/images\\/products\\/lipodrene-amarillo_gallery_1.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Lipodrene', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(46, 16, 'Lipo Black ultra Nutrex de 60 capsulas', 'lipo-black-ultra-nutrex-de-60-capsulas', 'LIPO-6 más fuerte que jamás se ha lanzado. Es tan fuerte que nunca puedes tomar más de una pastilla. Este es un quemador de grasa de una sola pastilla superpotente ultra concentrado que está diseñado para ayudar a su cuerpo a destruir rápidamente los depósitos de grasa.* Para ayudar a garantizar que su dieta y sus objetivos de pérdida de peso se conviertan en un gran éxito, Lipo-6 Black Ultra Concentrate ejerce un poderoso efecto supresor del apetito. Además, enciende una sensación extrema de energía y alerta que lo mantendrá activo durante horas. Para garantizar efectos óptimos, los poderosos ingredientes de Lipo-6 Black Ultra Concentrate están integrados en cápsulas líquidas de rápida absorción. Solo una píldora preparará el escenario para quemar grasa por completo. Tenga cuidado: Lipo-6 Black Ultra Concentrate es un producto intenso: un destructor de grasa como ningún otro. Quema la grasa de manera intensa! Los ingredientes usados para formular el lipo 6 ultra-concentrado son de la más alta calidad ,que ofrecen una alta cantidad de nutrientes y con acción termogénica que apoyan los procesos de pérdida de peso y definición. <strong>BENEFICIOS:</strong> <ul> <li>Solo una cáosula diaria.</li> <li>Rápida perdida de peso.</li> <li>Mucho más energía.</li> <li>Efecto saciante, evitas pecar.</li> <li>Enfoque y vascularización extrema.</li> </ul>', 'LIPO-6 más fuerte que jamás se ha lanzado. Es tan fuerte que nunca puedes tomar más de una pastilla. Este es un quemador de grasa de una sola pastilla superpotente ultra concentrado que está...', 'LIPOBLACKU368', 145.00, 115.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/lipo-black-ultra-nutrex-de-60-capsulas_main.webp\",\"\\/uploads\\/images\\/products\\/lipo-black-ultra-nutrex-de-60-capsulas_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Lipo', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(47, 16, 'Black Mamba de 90 pastillas', 'black-mamba-de-90-pastillas', 'El más potente, el más cruel, el más intenso, HARDCORE Stack EVER! Contiene 3 nuevas formulaciones personalizadas altamente avanzadas, incluyendo la Matriz de Amine, Corex, Thermo-Extend & More. AMPLIFICADA «LARGA DURACIÓN» ENERGÍA ALERTA MENTAL ACCELLERATED Appetite Control AVANZADO la mordedura fatal en la grasa! IT’S HARDCORE! El HYPERRUSH es, sin duda, el hardcore más potente energizante / peso del producto de gestión en el mercado hoy en día. Mientras que otros quemadores de grasa requieren que tomar de 4 a 6 pastillas a sentir incluso un ligero efecto … HYPERRUSH es tan fuerte, con sus mezclas a pedido de los extractos de la PEA y la efedra, que todo lo que tiene que tomar es UNO y estará amplificada al máximo y quemar la grasa del cuerpo como nunca antes! Incluso los adictos incondicionales efedrina que estaban consumiendo las dosis mega de sus pilas de la CEPA amados por día, tenga en cuenta los efectos de este producto más allá de extrema! ES multifacética! La clave para el éxito HYPERRUSH es un enfoque de vía múltiple. A diferencia de otros productos que tratan de hacer frente a la grasa y energía a través de una vía bioquímica, la HYPERRUSH multiplica estas vías mediante el uso de múltiples formulaciones muy avanzadas, lo que resulta en una mayor eficacia. Estos personalizados patentados / marca registrada mezclas que consisten en la Matriz de Amine (análogos de la PEA de la energía), Corex (Brand Extracto de Efedrina para la energía), y Thermo Extend-(control del apetito avanzado, el enfoque mental y energía extendida), juegan su papel en las mambas negras diseñar para aprovecharse de grasa. ES OFICIAL! Los expertos científicos de los laboratorios innovadores ha dado un salto cuántico hacia adelante en una nueva dimensión de la energía, el estado de alerta mental, el control del apetito y pérdida de grasa, con la introducción de su producto HOTTEST BADDEST y el nuevo NEGRO MAMBA HYPERRUSH. Operando en una clase propia, la HYPERRUSH ha aventurado fuera hacia nuevos territorios sin quemador de grasa / energizante se ha atrevido a ir. Con su diseño de ingeniería para intensificar la energía y grasa ataque, el HYPPERRUSH desintegra la grasa en contacto con un instinto asesino! ES EUFÓRICO! Además de contener nueva India «Bad Boy» supresor del apetito, el Cactus Carralluma, que ha dominado en la actualidad el famoso cactus Hoodia por una milla … HYPERRUSH también cuenta con alcaloides que actúan y sienten como la adrenalina pura, creando una experiencia eufórica. The Matrix Amine que pertenece a una clase de aminas simpaticomiméticas conocidos como agonistas beta mesolímbico, estimula las vías metabólicas y mesolímbica, que es responsable no sólo de la termogénesis extrema «para quemar calorías» que se encuentra en HYPERRUSH, sino también la neurotrópico «sentirse bien» sensorial resultante en un estado de euforia mental elevada. Datos Datos del suplemento: Tamaño de la porción: 1 cápsula Porciones por envase: 90 Mezcla total: 555 mg La cafeína anhidra – 200mg Corex (Brand Extracto de Efedrina) – 65 mg Sida cordifolia (Estandarizado para 20% alcaloides) Tannis, Vasicinone, vasicina, Vasicinol, Fitoesteroles y mucinas Amine Matrix – 110mg B-Aminoethylamin HCL ,4-hidroxi-fenetilamina, DL-fenilalanina, O-metoxi-phenylethlamine ,4-2-dimetilaminoetilo Thermo-Extend Matrix – 180mg Carraluma extracto (estandarizado para pregnano, megastigmane), fructus Evodiae 98%, serratum licopodio, la yohimbina HCl, disulfuro de tiamina propil, para-sinefrina hcl Otros ingredientes: Gelatina (cápsula), estearato de magnesio, FD & C azul # 1 y # 40 rojo USO SUGERIDO: Uso Sugerido: Como un suplemento dietético, Pon a prueba tu nivel de tolerancia con el consumo de 1 cápsula por la mañana con 8 oz. de agua y alimentos durante 7-10 días. Después de realizar la prueba, usted puede considerar 1-2 cápsulas una vez al día. No exceda de 2 cápsulas al día.', 'El más potente, el más cruel, el más intenso, HARDCORE Stack EVER! Contiene 3 nuevas formulaciones personalizadas altamente avanzadas, incluyendo la Matriz de Amine, Corex, Thermo-Extend &...', 'BLACKMAMBA898', 189.00, 159.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/black-mamba-de-90-pastillas_main.webp\",\"\\/uploads\\/images\\/products\\/black-mamba-de-90-pastillas_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/black-mamba-de-90-pastillas_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Black', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-07 23:56:26'),
(48, 16, 'ECA EXTREME 90 pastillas', 'eca-extreme-90-pastillas', 'ECA Xtreme ha sido considerado como uno de los productos para bajar de peso más populares de la historia. Cuando se trata de la pérdida de peso, y mucho más ECA Xtreme con 25 mg de extracto de efedra es la respuesta, donde la pérdida de peso y otros suplementos dietéticos han fracasado. ECA Xtreme el más poderoso y eficaz disponible hoy en día para pulverizar la grasa! ECA Xtreme le ayudará a alcanzar sus objetivos de pérdida de peso mucho más rápidamente otros quemadores. se ha formulado con precisión con las proporciones más que le permiten impulsar la quema de grasa más óptima. BENEFICIOS: - Quemador de grasa y Supresor del apetito. - Mantenimiento de la masa corporal magra. - Alcanza al máximo tu energía. - Regula la insulina con el objetivo de reutilizar la grasa corporal almacenada en el cuerpo.', 'ECA Xtreme ha sido considerado como uno de los productos para bajar de peso más populares de la historia. Cuando se trata de la pérdida de peso, y mucho más ECA Xtreme con 25 mg de extracto de...', 'ECAEXTREME907', 189.00, 159.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/eca-extreme-90-pastillas_main.webp\",\"\\/uploads\\/images\\/products\\/eca-extreme-90-pastillas_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Eca', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(49, 16, 'Cafeína de 100 pastillas GAT', 'cafeina-de-100-pastillas-gat', '<h2>REDUCE LA GRASA CORPORAL NATURALMENTE</h2> Proporciona efectos energizantes con cero azúcar añadido o calorías para apoyar tus necesidades de entrenamiento sin comprometer tus objetivos dietéticos. La cafeína desencadena los siguientes beneficios específicos que apoyan a tu rendimiento, enfoque mejorado, alerta elevada, tiempo de reacción más rápida, menor fatiga y mayor resistencia. Ideal para acelerar rápidamente tu desempeño mental y físico con el fin de maximizar tu entrenamiento. <h5>BENEFICIOS DE GAT CAFFEINE</h5> <ul> <li>Apoya el metabolismo</li> <li>Restaura el estado mental</li> <li>Eleva la energía y la resistencia</li> <li>Promueve la concentración</li> </ul> Tabletas 200 mg', 'REDUCE LA GRASA CORPORAL NATURALMENTE Proporciona efectos energizantes con cero azúcar añadido o calorías para apoyar tus necesidades de entrenamiento sin comprometer tus objetivos dietéticos. La...', 'CAFENADE10133', 119.00, 89.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/cafeina-de-100-pastillas-gat_main.webp\",\"\\/uploads\\/images\\/products\\/cafeina-de-100-pastillas-gat_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/cafeina-de-100-pastillas-gat_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Cafeína', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(50, 17, 'BCAA 12mil de 60 tomas', 'bcaa-12mil-de-60-tomas', 'Descripción Reviews BCAA 12,000 Powder de Ultimate Nutrition 60 Servicios. Sabor Orange Durante el ejercicio fuerte y prolongado, los músculos utilizan cantidades grandes de aminoácidos como fuente de energía. El cuerpo prefiere utilizar los bcca (aminoácidos de cadena ramificada) para obtener energía, después que la energía del glucógeno haya sido agotada, Como los músculos comienzan a utilizar los BCAA es necesario tomar un suplemento que ayude a prevenir la fatiga y el cansancio. La investigación muestra que la suplementación con aminoácidos ramificados puede prevenir la fatiga central por regular estrictamente la cantidad de triptófano. Se ha demostrado que La suplementación con BCAA antes y durante el ejercicio ayuda a mejorar el rendimiento. Los aminoácidos ramificados o BCAA incluyen leucina, isoleucina y valina, Estos aminoácidos son necesarios para el mantenimiento del tejido muscular durante el estrés y los ejercicios físicos intensos. Desde el punto de vista de la nutrición dirigido a atletas, los BCAA funcionan como agentes anabólicos e induce a quemar grasa, lo que permite al cuerpo a quemar grasa y no quemar músculo. Ultimate Nutrition se ha comprometido a proporcionar a los atletas con los mejores productos para satisfacer exactamente sus necesidades. Ultimate Nutrition BCAA 100% Crystalline 12.000 Powder en polvo es un producto diseñado para aumentar la resistencia, prolongar el tiempo hasta la fatiga, prevenir el catabolismo, promover el anabolismo y mejorar el rendimiento. Instrucciones para Usar BCAA Powder 12000: Mezclar una cucharada (scoop) (aproximadamente 6 gramos) con 200 – 250 ml de agua fría o de su bebida favorita. Para un uso óptimo, tomar entre comidas e inmediatamente después de su entrenamiento. Para los culturistas serios se recomienda mezclar dos cucharadas.', 'Descripción Reviews BCAA 12,000 Powder de Ultimate Nutrition 60 Servicios. Sabor Orange Durante el ejercicio fuerte y prolongado, los músculos utilizan cantidades grandes de aminoácidos como...', 'BCAA12MILD672', 172.00, 142.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/bcaa-12mil-de-60-tomas_main.webp\",\"\\/uploads\\/images\\/products\\/bcaa-12mil-de-60-tomas_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/bcaa-12mil-de-60-tomas_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Bcaa', 0, 1, 14, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-07 00:40:16'),
(51, 17, 'EAA de 195 gr Kevin Levron', 'eaa-de-195-gr-kevin-levron', '<h2><strong>EAA BCAA</strong></h2> EAA BCAA de la marca Kevin Levrone, contiene aminoácidos EAA esenciales. La composición propuesta contiene 9 ingredientes extremadamente importantes que contribuyen a las proteínas de construcción muscular: los aminoácidos exógenos son un elemento muy importante de la dieta diaria de las personas físicamente activas. <h2><strong>Presentación</strong></h2> Peso: 195 gr Servicios: 31 <h2><strong>Beneficios</strong></h2> Un complejo de los 9 aminoácidos esenciales más importantes EAA. Cada porción proporciona hasta 1,8 g del valioso complejo BCAA. El suplemento no contiene azúcar. Recomendado en diversas formas de esfuerzo físico. Una opción ideal para deportistas recreativos y profesionales. Solubilidad perfecta y sabores extremadamente refinados. Funciona muy bien en combinación con suplementos de creatina, glutamina y proteínas. <h2><strong>Uso Sugerido</strong></h2> Mezclar ~1 cucharada de polvo (6,5 g) con 250 ml de agua fría. Consuma 1-2 porciones al día, una entre comidas y la otra antes de acostarse. Los días de entrenamiento consumir una ración antes, durante o después del entrenamiento. <h2><strong>Advertencia</strong></h2> Este producto está destinado a ser consumido solo por adultos saludables, de 18 años de edad o mayores. No use este producto si está embarazada o en período de lactancia. Antes de usar este producto, consulte a un médico si, sin limitarse a, toma algún medicamento con receta o de venta libre, o tiene alguna condición médica existente. Deja de usar el producto inmediatamente y ponte en contacto con un médico si experimentas una reacción adversa a este producto. Suspenda su uso 2 semanas antes de cualquier cirugía. No use el producto si el sello de seguridad está roto o no está presente. Conserve el producto en un lugar fresco y seco. Mantenga el producto fuera del alcance de los niños. Se puede producir sedimentación del contenido. No exceda el uso sugerido. <h2><strong>Exclusión de garantía y responsabilidad</strong></h2> En KeepFit trabajamos para garantizar que la información sobre el producto es correcta, en ocasiones los fabricantes pueden alterar sus listas de ingredientes. Los envases y materiales del producto reales pueden contener más y/o distinta información que la que aparece en nuestra página web. Le recomendamos que no confíe tan solo en la información presentada y que lea siempre las etiquetas, advertencias e instrucciones antes de usar o consumir un producto. Para más información sobre un producto, por favor, póngase en contacto con el fabricante. El contenido de este sitio tiene fines de consulta y no pretende sustituir los consejos dados por un médico, farmacéutico u otro profesional de la salud con licencia. No debería utilizar esta información para auto-diagnosticarse o tratar un problema de salud o una enfermedad. Póngase en contacto inmediatamente con su proveedor de cuidados de la salud si sospecha que tiene un problema médico. La información y las declaraciones concernientes a los suplementos dietarios no han sido evaluados por la Administración de Alimentación y Medicamentos (Food and Drug Administration) y no pretenden diagnosticar, curar o prevenir ninguna enfermedad o problema de salud. KeppFit no asume ninguna responsabilidad por información o declaraciones inexactas sobre los productos.', 'EAA BCAA EAA BCAA de la marca Kevin Levrone, contiene aminoácidos EAA esenciales. La composición propuesta contiene 9 ingredientes extremadamente importantes que contribuyen a las proteínas de...', 'EAADE195GR524', 108.50, 78.50, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/eaa-de-195-gr-kevin-levron_main.webp\",\"\\/uploads\\/images\\/products\\/eaa-de-195-gr-kevin-levron_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Eaa', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(52, 18, 'Creatina NUTREX DE 1 KILO', 'creatina-nutrex-de-1-kilo', 'CREATINE DRIVE™ contiene monohidrato de creatina puro, seguro y eficaz. El monohidrato de creatina suele tomarse diariamente en cantidades de varios gramos durante un período bastante prolongado. Por esta razón, la calidad de su creatina debe ser un factor crítico en la selección de un producto. Su creatina puede parecer puro, polvo blanco, pero todavía puede ser de calidad inferior, que contiene impurezas toxicológicamente perjudiciales. Nuestro monohidrato de creatina satisface incluso las exigencias de calidad, seguridad y eficacia de los consumidores más exigentes. CREATINE DRIVE no tiene sabor y puede añadirse fácilmente a cualquier bebida de su elección. Especificaciones Condicion del producto Nuevo Detalle de la garantía 1 día de garantía por falla de fábrica después de entregado el producto Modelo Creatina Características Tónicos Unidad de medida Unidad País de origen Estados Unidos Forma farmacéutica Polvo Cantidad contenida en el empaque 1 KG Peso del producto 1 Medida/volumen 1 Garantía 3 meses Tipo de Suplemento Creatina Incluye Creatina &nbsp; &nbsp;', 'CREATINE DRIVE™ contiene monohidrato de creatina puro, seguro y eficaz. El monohidrato de creatina suele tomarse diariamente en cantidades de varios gramos durante un período bastante prolongado....', 'CREATINANU878', 219.00, 189.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/creatina-nutrex-de-1-kilo_main.jpg\",\"\\/uploads\\/images\\/products\\/creatina-nutrex-de-1-kilo_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/creatina-nutrex-de-1-kilo_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Creatina', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(53, 18, 'Creatina POUT de 1 kilo', 'creatina-pout-de-1-kilo-2', '<strong>¿Qué es P-Out Creatina?</strong> P-Out Creatina La creatina de Pumping-Out proporciona monohidrato de creatina para respaldar tus objetivos de entrenamiento y resistencia. La creatina es uno de los suplementos más eficaces para el rendimiento deportivo de alta intensidad, fuerza, resistencia y construcción muscular. El monohidrato de creatina es una forma altamente investigada de creatina que se muestra para maximizar los niveles de fosfato de creatina muscular, un recurso de energia crítico durante las acciones musculares de alta intensidad, como el entrenamiento con pesas y la carrera de velocidad. <strong>¿Para qué sirve P-Out Creatina?</strong> P-Out Creatina, sirve para Aumentar la fuerza muscular y mejorar el rendimiento físico. Favorece el desarrollo muscular y el volumen celular. Mejora la recuperación entre esfuerzos repetidos de corta duración. Ayuda a la recuperación muscular y puede prevenir lesiones. CREATINA P-OUT está formulado para suministrar 3000mg de monohidrato de creatina por porción para apoyar sus objetivos de entrenamiento de resistencia. <strong>Especificaciones</strong> Condicion del producto Nuevo Modelo POLVO País de origen Estados Unidos Unidad de medida Kilogramo Características Natural Forma farmacéutica Polvo Peso del producto 1 Cantidad contenida en el empaque 453 gr Medida/volumen 1 Tipo de Suplemento Creatina <strong>Modo de empleo:</strong> Tomar 1 y 2/3 de scoop (5 gr.) mezclado con agua, jugo o bebida preferida. Puede tomarlo antes del entrenamiento, con una comida o directamente después del entrenamiento con su batido de proteínas post entrenamiento para mejorar la efectividad.', '¿Qué es P-Out Creatina? P-Out Creatina La creatina de Pumping-Out proporciona monohidrato de creatina para respaldar tus objetivos de entrenamiento y resistencia. La creatina es uno de los...', 'CREATINAPO686', 255.00, 225.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/creatina-pout-de-1-kilo-2_main.jpeg\",\"\\/uploads\\/images\\/products\\/creatina-pout-de-1-kilo-2_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Creatina', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(54, 18, 'Creatina ultimáte nutrition de 1 kilo', '585', '<h1 class=\"product_title entry-title\">Creatina Ultimate Nutrition – 1 Kg</h1> Cada envasé contiene 100% Creatina Monohidratada de calidad premium. Pureza del 100%.  A mayor pureza mayor absorción intestinal y mayores beneficios. <strong>BENEFICIOS:</strong> <ul> <li>Presentación de 1 Kg</li> <li>Favorecer la hipertrofia muscular</li> <li>Aumenta la fuerza muscular</li> <li>Actúa como ayuda ergogénica en deportes de alta intensidad y corta duración</li> <li>No presenta olor ni sabor.</li> <li>Micronizada para una mejor disolución.</li> </ul> &nbsp; <strong>DOSIS RECOMENDADADA</strong> Como suplemento dietético, tome una cucharadita redondeada (aproximadamente 5 gramos) dos veces al día, espaciadas uniformemente, con el estómago vacío.', 'Creatina Ultimate Nutrition – 1 Kg Cada envasé contiene 100% Creatina Monohidratada de calidad premium. Pureza del 100%.  A mayor pureza mayor absorción intestinal y mayores beneficios....', 'CREATINAUL961', 319.00, 289.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/585_main.webp\",\"\\/uploads\\/images\\/products\\/585_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Creatina', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(55, 19, 'Hepalivio ( silimarina + complejo B)  100 cap.', 'hepalivio-silimarina-complejo-b-100-cap', '<strong>INGREDIENTES</strong> Silimarina 150mg Tiamina mononitrato 2.0 mg Riboflavina 2.0mg Nicotinamida 10.0mg Piridoxina clorhidrato 0.125 mg Calcio pantotenato 2.0 mg <strong>CONTRA INDICACIONES</strong> Hipersensibilidad la silimarina o a cualquiera de sus componentes. Obstrucción de las vía biliares u otras patologías de la vesicula biliar. <strong>PRECAUCIONES:</strong> No hay información sobre precauciones generales para el uso de HEPALIVIO B. Embarazo : No hay información sobre su uso en gestantes.No administrar durante el embarazo. Lactancia : No se dispone de datos acerca de su posible aparición en la leche materna. Pediátricas: no hay informacion disponible sobre su uso en niños. Geriátricas: No se ha reportado efectos adversos sobre su uso en la población geriátrica. Si se presentan reacciones adversas se debe reducir la dosis, caso contrario suspender su uso, según indicación médica.', 'INGREDIENTES Silimarina 150mg Tiamina mononitrato 2.0 mg Riboflavina 2.0mg Nicotinamida 10.0mg Piridoxina clorhidrato 0.125 mg Calcio pantotenato 2.0 mg CONTRA INDICACIONES Hipersensibilidad la...', 'HEPALIVIOS173', 139.00, 109.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/hepalivio-silimarina-complejo-b-100-cap_main.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Hepalivio', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(56, 10, 'Gold Standard 100% Whey - 5 Libras', 'gold-standard-100-whey-5-libras', '<strong>Con la Gold Standard Whey 100% de ON</strong> le das a tu cuerpo la mejor proteína. Ideal para cualquier persona que lleva un estilo de vida activo y quiere ganar masa muscular de calidad, este suplemento nutricional viene fortificado con BCAAs, Glutamina y precursores de glutamina, junto con una gran cantidad de vitaminas y minerales, además ofrece una mayor síntesis de proteínas. <strong>Para soporte y recuperación muscular:</strong> La proteína de suero de leche aislada (whey protein isolate) es la forma más pura de proteína que existe actualmente. La proteína asilada/isolate es costosa, pero es la mejor proteína que el dinero puede comprar. Por eso es que es el primer ingrediente que encontraras en la Gold Standard 100% Whey. Usando la proteína de suero de leche aislada combinada con la proteína de suero premium concentrada ultra filtrada, podemos aportar 24g de proteína en cada servicio para soporte muscular luego del entrenamiento. <strong>Gold Standard</strong> también está enfocada en la dilución fácil, este polvo de superior calidad esta formulado para mezclar de manera instantánea usando un vaso shaker/ tomatodo, o solo usando una cuchara y un vaso de vidrio. <ul> <li>Proteína Whey Aislada/Isolatada</li> <li>Proteína Whey concentrado</li> <li>Más de 4g de Glutamina y Acido Glutámico en cada servicio</li> <li>Más de 5g de aminoácidos ramificados (BCAAs) naturales (Leucina, valina e isoleucina).</li> <li>La proteína estándar de calidad.</li> </ul> <strong>Ingredientes:</strong> mezcla de proteínas (aislados de proteína de suero, concentrados de proteína, péptidos del suero de la leche), cacao, sabores artificiales, lecitina y acesulfame de potasio. <strong>Forma de Consumo/Como tomar Gold Standard whey de ON</strong>: mezcla 1 scoop/cucharada en 6 a 8 onzas de agua (180ml a 240ml) en agua fría, leche o bebida de preferencia, mezclar en shaker o tomatodo y toma 30 minutos luego del entrenamiento. O tómalo en cualquier momento del día como merienda.', 'Con la Gold Standard Whey 100% de ON le das a tu cuerpo la mejor proteína. Ideal para cualquier persona que lleva un estilo de vida activo y quiere ganar masa muscular de calidad, este suplemento...', 'GOLDSTANDA730', 369.00, 339.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/gold-standard-100-whey-5-libras_main.webp\",\"\\/uploads\\/images\\/products\\/gold-standard-100-whey-5-libras_gallery_1.jpg\",\"\\/uploads\\/images\\/products\\/gold-standard-100-whey-5-libras_gallery_2.png\",\"\\/uploads\\/images\\/products\\/gold-standard-100-whey-5-libras_gallery_3.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'WHEY', 0, 1, 8, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(57, 10, 'Nitro whey 1.100 kg - UN', 'nitro-whey-1-100-kg-un', '<strong>DESCRIPCIÓN:</strong> El post-entrenamiento es el momento perfecto para alimentar sus músculos con los nutrientes que necesitas para reconstruir adecuadamente las fibras musculares destruidas, y con NITRO WHEY, obtiene la digestibilidad y absorción más rápidas en comparación con muchas otras fuentes de proteínas disponibles. NITRO WHEY ayuda a optimizar la retención de nitrógeno, apoya la síntesis de proteínas y le brinda la oportunidad de agregar la masa muscular magra de calidad que desea. <strong>BENEFICIOS:</strong> <ul> <li>Rápida absorción.</li> <li>Apoya con la recuperación.</li> <li>Alto contenido de aminoácidos.</li> <li>Ayudan a complementar los requerimientos diarios.</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Masa muscular magra y mejor recuperación</li> </ul>', 'DESCRIPCIÓN: El post-entrenamiento es el momento perfecto para alimentar sus músculos con los nutrientes que necesitas para reconstruir adecuadamente las fibras musculares destruidas, y con NITRO...', 'NITROWHEY1368', 119.00, 89.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/nitro-whey-1-100-kg-un_main.png\",\"\\/uploads\\/images\\/products\\/nitro-whey-1-100-kg-un_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'WHEY', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(58, 10, 'Nitro whey 3kg- UN', 'nitro-whey-3kg-un', '<strong>NITRO WHEY</strong> <p class=\"ql-align-justify\">NITRO WHEY es un producto de alta calidad elaborado a base del MEJOR SUERO DE LECHE INSTANTÁNEO de fácil digestión; nos brindará las proteínas y aminoácidos esenciales que tus músculos necesitan para recuperarse después del entrenamiento.</p> <p class=\"ql-align-justify\">NITRO WHEY contiene L-GLUTAMINA, importante para la recuperación muscular tras la práctica deportiva de alta intensidad.</p> <p class=\"ql-align-justify\">Este producto está diseñado para todo tipo de atletas o personas que tengan gran desgaste físico, deportistas de alto rendimiento, culturistas y fitness.</p> <p class=\"ql-align-justify\"><strong>Usos Sugeridos</strong></p> <p class=\"ql-align-justify\">Ideal para personas que deseen mantener o subir su peso (periodo de volumen), tanto para hombres, mujeres, atletas de alto rendimiento físico, entre otros.</p> <p class=\"ql-align-justify\"><strong>Instrucciones</strong></p> <p class=\"ql-align-justify\">Se recomienda consumir NITRO WHEY una o dos veces al día; puede ser consumido en cualquier acción que usted lo requiera.</p> <p class=\"ql-align-justify\">Para un delicioso batido de proteína, mezcle un servicio de NITRO WHEY en 200 a 300 ml de agua fría en un shaker o coctelera.</p>', 'NITRO WHEY NITRO WHEY es un producto de alta calidad elaborado a base del MEJOR SUERO DE LECHE INSTANTÁNEO de fácil digestión; nos brindará las proteínas y aminoácidos esenciales que tus...', 'NITROWHEY3742', 202.00, 172.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/nitro-whey-3kg-un_main.webp\",\"\\/uploads\\/images\\/products\\/nitro-whey-3kg-un_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'WHEY', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(59, 10, 'Nitro whey 5kg -UN', 'nitro-whey-5kg-un', '<strong>DESCRIPCIÓN:</strong> El post-entrenamiento es el momento perfecto para alimentar sus músculos con los nutrientes que necesitas para reconstruir adecuadamente las fibras musculares destruidas, y con NITRO WHEY, obtiene la digestibilidad y absorción más rápidas en comparación con muchas otras fuentes de proteínas disponibles. NITRO WHEY ayuda a optimizar la retención de nitrógeno, apoya la síntesis de proteínas y le brinda la oportunidad de agregar la masa muscular magra de calidad que desea. <strong>BENEFICIOS:</strong> <ul> <li>Rápida absorción.</li> <li>Apoya con la recuperación.</li> <li>Alto contenido de aminoácidos.</li> <li>Ayudan a complementar los requerimientos diarios.</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Masa muscular magra y mejor recuperación</li> </ul>', 'DESCRIPCIÓN: El post-entrenamiento es el momento perfecto para alimentar sus músculos con los nutrientes que necesitas para reconstruir adecuadamente las fibras musculares destruidas, y con NITRO...', 'NITROWHEY5802', 272.00, 242.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/nitro-whey-5kg-un_main.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'WHEY', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(60, 10, 'Whey pro 3kg- UN', 'whey-pro-3kg-un', '<strong>DESCRIPCIÓN:</strong> La fuente principal de WHEY PRO es la proteína de suero de leche, una de las más rápidas de absorber, el mejor combustible para el cuerpo y los músculos antes de un entrenamiento debido a los aminoácidos esenciales y después del entrenamiento para recuperarse más rápido y más fuerte. Además, utilizamos ingredientes de alta calidad para garantizar que nuestros productos generen excelentes resultados. <strong>BENEFICIOS:</strong> <ul> <li>Complemento nutricional.</li> <li>Alto contenido de aminoácidos.</li> <li>Previene la aparición de fatiga durante la práctica deportiva.</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Ganar masa muscular y/o mejorar la recuperación</li> </ul>', 'DESCRIPCIÓN: La fuente principal de WHEY PRO es la proteína de suero de leche, una de las más rápidas de absorber, el mejor combustible para el cuerpo y los músculos antes de un entrenamiento...', 'WHEYPRO3KG117', 147.00, 117.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/whey-pro-3kg-un_main.jpeg\",\"\\/uploads\\/images\\/products\\/whey-pro-3kg-un_gallery_1.jpeg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'WHEY', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(61, 10, 'Whey pro 5kg - UN', 'whey-pro-5kg-un', '<strong>DESCRIPCIÓN:</strong> La fuente principal de WHEY PRO es la proteína de suero de leche, una de las más rápidas de absorber, el mejor combustible para el cuerpo y los músculos antes de un entrenamiento debido a los aminoácidos esenciales y después del entrenamiento para recuperarse más rápido y más fuerte. Además, utilizamos ingredientes de alta calidad para garantizar que nuestros productos generen excelentes resultados. <strong>BENEFICIOS:</strong> <ul> <li>Complemento nutricional.</li> <li>Alto contenido de aminoácidos.</li> <li>Previene la aparición de fatiga durante la práctica deportiva.</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Ganar masa muscular y/o mejorar la recuperación</li> </ul>', 'DESCRIPCIÓN: La fuente principal de WHEY PRO es la proteína de suero de leche, una de las más rápidas de absorber, el mejor combustible para el cuerpo y los músculos antes de un entrenamiento...', 'WHEYPRO5KG777', 184.00, 154.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/whey-pro-5kg-un_main.avif\",\"\\/uploads\\/images\\/products\\/whey-pro-5kg-un_gallery_1.jpeg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'WHEY', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(62, 11, 'Fit mass 2kg - FIT FEM', 'fit-mass-2kg-fit-fem', 'FIT MASS está creado para la mujer, cuenta con el balance ideal de proteínas y carbohidratos de mediana y rápida absorción que aportará la dosis extra de calorías necesarias en el logro de tus objetivos; con BCAA y COLÁGENO HIDROLIZADO, añadimos ÁCIDO FÓLICO necesario para la absorción de proteínas y vitaminas que toda mujer necesita. <ul> <li>40 gramos de proteína por servicio</li> <li>Carbohidratos</li> <li>Colágeno hidrolizado</li> <li>Ácido fólico</li> <li>Vitaminas del complejo B</li> <li>BCAA</li> </ul>', 'FIT MASS está creado para la mujer, cuenta con el balance ideal de proteínas y carbohidratos de mediana y rápida absorción que aportará la dosis extra de calorías necesarias en el logro de tus...', 'FITMASS2KG898', 119.00, 89.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/fit-mass-2kg-fit-fem_main.png\",\"\\/uploads\\/images\\/products\\/fit-mass-2kg-fit-fem_gallery_1.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'MASS', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(63, 11, 'BigM 2kg- UN', 'bigm-2kg-un', '<strong>DESCRIPCIÓN:</strong> BIGM ayuda en su objetivo para optimizar la recuperación y aumentar la masa muscular, con altas dosis de proteínas, carbohidratos y calorías para la construcción de masa, BIGM seguramente lo ayudará a comenzar a ver aumentos en su musculatura y consecuentemente en su peso. Cada servicio proporciona una gran cantidad de proteínas, carbohidratos, vitaminas y minerales para aumentar la cantidad que está obteniendo a través de una dieta equilibrada de alimentos. <strong>BENEFICIOS:</strong> <ul> <li>Te brinda todos los nutrientes para la recuperación post-entrenamiento.</li> <li>Alta dosis de proteína.</li> <li>Perfecto si te encuentras en etapa de ganancia.</li> <li>Ayuda a alcanzar tus necesidades calóricas.</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Ganar masa muscular y/o peso</li> </ul>', 'DESCRIPCIÓN: BIGM ayuda en su objetivo para optimizar la recuperación y aumentar la masa muscular, con altas dosis de proteínas, carbohidratos y calorías para la construcción de masa, BIGM...', 'BIGM2KGUN665', 119.00, 89.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/bigm-2kg-un_main.png\",\"\\/uploads\\/images\\/products\\/bigm-2kg-un_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Bigm', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(64, 11, 'BigM 3 kg -UN', 'bigm-3-kg-un', '<strong>DESCRIPCIÓN:</strong> BIGM ayuda en su objetivo para optimizar la recuperación y aumentar la masa muscular, con altas dosis de proteínas, carbohidratos y calorías para la construcción de masa, BIGM seguramente lo ayudará a comenzar a ver aumentos en su musculatura y consecuentemente en su peso. Cada servicio proporciona una gran cantidad de proteínas, carbohidratos, vitaminas y minerales para aumentar la cantidad que está obteniendo a través de una dieta equilibrada de alimentos. <strong>BENEFICIOS:</strong> <ul> <li>Te brinda todos los nutrientes para la recuperación post-entrenamiento.</li> <li>Alta dosis de proteína.</li> <li>Perfecto si te encuentras en etapa de ganancia.</li> <li>Ayuda a alcanzar tus necesidades calóricas.</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Ganar masa muscular y/o peso</li> </ul>', 'DESCRIPCIÓN: BIGM ayuda en su objetivo para optimizar la recuperación y aumentar la masa muscular, con altas dosis de proteínas, carbohidratos y calorías para la construcción de masa, BIGM...', 'BIGM3KGUN348', 149.00, 119.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/bigm-3-kg-un_main.webp\",\"\\/uploads\\/images\\/products\\/bigm-3-kg-un_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Bigm', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(65, 11, 'BigM 5kg -UN', 'bigm-5kg-un', '<strong>DESCRIPCIÓN:</strong> BIGM ayuda en su objetivo para optimizar la recuperación y aumentar la masa muscular, con altas dosis de proteínas, carbohidratos y calorías para la construcción de masa, BIGM seguramente lo ayudará a comenzar a ver aumentos en su musculatura y consecuentemente en su peso. Cada servicio proporciona una gran cantidad de proteínas, carbohidratos, vitaminas y minerales para aumentar la cantidad que está obteniendo a través de una dieta equilibrada de alimentos. <strong>BENEFICIOS:</strong> <ul> <li>Te brinda todos los nutrientes para la recuperación post-entrenamiento.</li> <li>Alta dosis de proteína.</li> <li>Perfecto si te encuentras en etapa de ganancia.</li> <li>Ayuda a alcanzar tus necesidades calóricas.</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Ganar masa muscular y/o peso</li> </ul>', 'DESCRIPCIÓN: BIGM ayuda en su objetivo para optimizar la recuperación y aumentar la masa muscular, con altas dosis de proteínas, carbohidratos y calorías para la construcción de masa, BIGM...', 'BIGM5KGUN980', 169.00, 139.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/bigm-5kg-un_main.webp\",\"\\/uploads\\/images\\/products\\/bigm-5kg-un_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Bigm', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(66, 2, 'One prenox 500mg -INN', 'one-prenox-500mg-inn', '<strong>ONE PRE NOX</strong> es un conjunto de aminoácidos que potencia tu modo de entrenar. Sentirás un impulso energético durante el entrenamiento. Entonces <strong>conseguirás recuperarte mejor</strong> y disfrutarás de una <strong>mejoría más rápida tras el ejercicio físico</strong>. <h3>¿Qué contiene?</h3> <ul> <li><strong>Creatina monohidratada: </strong>Mejora el rendimiento físico y aumenta la masa muscular en los atletas.</li> <li><strong>L-citrulina de malato: </strong>Es un vasodilatador<strong>,</strong> precursor de óxido nítrico en el cuerpo.</li> <li><strong>Beta alanina: </strong>Mejora tu capacidad de ejercicio y rendimiento.</li> </ul> <h3>Beneficios de consumir ONE PRE NOX</h3> <ul> <li>Brinda energía, fuerza y resistencia para tu entrenamiento.</li> <li><strong>Disminuye el tiempo de recuperación</strong> entre series.</li> <li>Mejora notablemente el entrenamiento.</li> <li>Incrementa la fuerza explosiva.</li> <li>Maximiza el desempeño físico.</li> <li><strong>Mayor energía</strong> al entrenar.</li> <li>Aumenta tu resistencia.</li> </ul> &nbsp;', 'ONE PRE NOX es un conjunto de aminoácidos que potencia tu modo de entrenar. Sentirás un impulso energético durante el entrenamiento. Entonces conseguirás recuperarte mejor y disfrutarás de...', 'ONEPRENOX5980', 112.00, 82.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/one-prenox-500mg-inn_main.webp\",\"\\/uploads\\/images\\/products\\/one-prenox-500mg-inn_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'One', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(67, 18, 'Creabol 1kg- WINNER', 'creabol-1kg-winner', 'CREABOL es un producto de uso exclusivo para deportistas cuyo desempeño demanda gran esfuerzo explosivo como natación, futbol levantamiento de pesas, corredores de fondo, etc, facilitando las contracciones musculares y el desarrollo de fuerza. Una buena opción para salir del estancamiento. Como dato extra, hoy en día hay estudios que demuestran que la L-Creatina también mejora la agilidad mental y mejora la oxigenación cerebral. La ingesta complementaria de esta vitamina nos <b>ayuda a aumentar la fuerza y la masa muscular</b>.', 'CREABOL es un producto de uso exclusivo para deportistas cuyo desempeño demanda gran esfuerzo explosivo como natación, futbol levantamiento de pesas, corredores de fondo, etc, facilitando las...', 'CREABOL1KG959', 183.00, 153.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/creabol-1kg-winner_main.webp\",\"\\/uploads\\/images\\/products\\/creabol-1kg-winner_gallery_1.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Creabol', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(68, 18, 'Creabolic 1kg - UN', 'creabolic-1kg-un', 'LA CREATINA ES UNA MOLÉCULA NATURAL EN EL CUERPO QUE JUEGA UN PAPEL CLAVE EN EL METABOLISMO ENERGÉTICO. CREABOLIC AYUDA A SOBRESATURAR LOS MÚSCULOS CON MAYORES RESERVAS DE CREATINA DISPONIBLE, IMPULSANDO UNA MEJOR PRODUCCIÓN DE POTENCIA MUSCULAR, FUERZA EXPLOSIVA Y UNA RÁPIDA RECUPERACIÓN DURANTE EL ENTRENAMIENTO. BENEFICIOS: <ul> <li> MAYOR FUERZA.</li> <li>INCREMENTA LOS NIVELES DE RESISTENCIA.</li> <li>PROMUEVE LA GANANCIA MUSCULAR.</li> </ul> OBJETIVO: <ul> <li>MEJORAR LA FUERZA Y RESISTENCIA DURANTE EL ENTRENAMIENTO.</li> </ul>', 'LA CREATINA ES UNA MOLÉCULA NATURAL EN EL CUERPO QUE JUEGA UN PAPEL CLAVE EN EL METABOLISMO ENERGÉTICO. CREABOLIC AYUDA A SOBRESATURAR LOS MÚSCULOS CON MAYORES RESERVAS DE CREATINA DISPONIBLE,...', 'CREABOLIC1490', 189.00, 159.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/creabolic-1kg-un_main.webp\",\"\\/uploads\\/images\\/products\\/creabolic-1kg-un_gallery_1.avif\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Creabolic', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(69, 18, 'Glutabolic 500 gr -UN', 'glutabolic-500-gr-un', '<b>GLUTABOLIC</b> La L-glutamina es un aminoácido condicionalmente esencial que ayuda a reconstruir y reparar la masa muscular después de un entrenamiento de alta intensidad. GLUTABOLIC puede ayudarlo a optimizar la recuperación después del entrenamiento al reducir la degradación muscular y el dolor muscular inducido por el ejercicio. Tiempos de recuperación más rápidos y menos dolor muscular se traducen en un mayor volumen de entrenamiento y un mejor rendimiento en el entrenamiento. <b>Usos Sugeridos</b> Consumir 5g al despertar, 5g después de entrenar y 5g antes de acostarse.', 'GLUTABOLIC La L-glutamina es un aminoácido condicionalmente esencial que ayuda a reconstruir y reparar la masa muscular después de un entrenamiento de alta intensidad. GLUTABOLIC puede ayudarlo a...', 'GLUTABOLIC167', 110.00, 80.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/glutabolic-500-gr-un_main.webp\",\"\\/uploads\\/images\\/products\\/glutabolic-500-gr-un_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/glutabolic-500-gr-un_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Glutabolic', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(70, 15, 'Collagen pro 500 gr -UN', 'collagen-pro-500-gr-un', 'COLLAGEN PRO rico en COLÁGENO, la proteína más abundante del cuerpo y elemento principal de la piel, huesos, tendones, cartílagos, vasos sanguíneos y dientes. “UN” elaboró un excelente producto a base de COLÁGENO HIDROLIZADO, VITAMINA B6,VITAMINA C, MAGNESIO Y ZINC. Consumir colágeno puede ser bueno para la salud de muchas maneras, desde aliviar el dolor articular hasta mejorar la salud de la piel. Algunos beneficios incluyen: <ul> <li>Mejorar la salud de la piel</li> <li>Alivio del dolor en las articulaciones</li> <li>Puede prevenir la perdida ósea</li> <li>Posible aumento de masa muscular</li> <li>Promueve la salud cardíaca</li> </ul>', 'COLLAGEN PRO rico en COLÁGENO, la proteína más abundante del cuerpo y elemento principal de la piel, huesos, tendones, cartílagos, vasos sanguíneos y dientes. “UN” elaboró un excelente...', 'COLLAGENPR313', 110.00, 80.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/collagen-pro-500-gr-un_main.jpg\",\"\\/uploads\\/images\\/products\\/collagen-pro-500-gr-un_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Collagen', 0, 1, 3, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(71, 15, 'Collagen Fem 500gr -FIT FEM', 'collagen-fem-500gr-fit-fem', 'COLLAGEN FEM es un complemento rico en colágeno, la proteína más abundante del cuerpo y elemento principal de la piel, huesos, tendones, cartílagos, vasos sanguíneos y dientes. Un 25% del peso de las proteínas del cuerpo está compuesto por COLÁGENO, el 75% de nuestra piel y en total el 30% de nuestro organismo está constituido por esta proteína. Cuando hay falta de colágeno todo el organismo se ve afectado, los síntomas más frecuentes por la falta de colágeno son: debilidad, fatiga, malestar, dolor, y una disminución general de energía. Es por ello que Fitfem elaboró un excelente producto a base de COLÁGENO HIDROLIZADO, VITAMINA B6,VITAMINA C.', 'COLLAGEN FEM es un complemento rico en colágeno, la proteína más abundante del cuerpo y elemento principal de la piel, huesos, tendones, cartílagos, vasos sanguíneos y dientes. Un 25% del peso...', 'COLLAGENFE374', 100.00, 70.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/collagen-fem-500gr-fit-fem_main.avif\",\"\\/uploads\\/images\\/products\\/collagen-fem-500gr-fit-fem_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Collagen', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(72, 16, 'QUEMADOR XB - UN (15 unidades)', 'quemador-xb-un-15-unidades', '<strong>DESCRIPCIÓN:</strong> X-B apoya a mantener un metabolismo saludable, aumenta la producción de energía, la resistencia, el enfoque mental y contiene un delicioso sabor. X-B lo ayudará a incinerar esa grasa corporal obstinada y lo ayudará a acercarse aún más a lograr su objetivo. <strong>BENEFICIOS:</strong> <ul> <li>Mayor energía</li> <li>Mayor resistencia</li> <li>Acelera el metabolismo</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Obtener más energía</li> </ul>', 'DESCRIPCIÓN: X-B apoya a mantener un metabolismo saludable, aumenta la producción de energía, la resistencia, el enfoque mental y contiene un delicioso sabor. X-B lo ayudará a incinerar esa grasa...', 'QUEMADORXB992', 60.00, 30.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/quemador-xb-un-15-unidades_main.webp\",\"\\/uploads\\/images\\/products\\/quemador-xb-un-15-unidades_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/quemador-xb-un-15-unidades_gallery_2.webp\",\"\\/uploads\\/images\\/products\\/quemador-xb-un-15-unidades_gallery_3.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Quemador', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `sale_price`, `cost_price`, `stock_quantity`, `min_stock_level`, `weight`, `dimensions`, `images`, `gallery`, `specifications`, `nutritional_info`, `usage_instructions`, `ingredients`, `warnings`, `brand`, `is_featured`, `is_active`, `views_count`, `sales_count`, `avg_rating`, `reviews_count`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(73, 16, '5 PACK QUEMADOR XB -UN', '5-pack-quemador-xb-un', '<strong>DESCRIPCIÓN:</strong> X-B apoya a mantener un metabolismo saludable, aumenta la producción de energía, la resistencia, el enfoque mental y contiene un delicioso sabor. X-B lo ayudará a incinerar esa grasa corporal obstinada y lo ayudará a acercarse aún más a lograr su objetivo. <strong>BENEFICIOS:</strong> <ul> <li>Mayor energía</li> <li>Mayor resistencia</li> <li>Acelera el metabolismo</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Obtener más energía</li> </ul>', 'DESCRIPCIÓN: X-B apoya a mantener un metabolismo saludable, aumenta la producción de energía, la resistencia, el enfoque mental y contiene un delicioso sabor. X-B lo ayudará a incinerar esa grasa...', '5PACKQUEMA984', 130.00, 100.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/5-pack-quemador-xb-un_main.webp\",\"\\/uploads\\/images\\/products\\/5-pack-quemador-xb-un_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, '5', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(74, 11, 'BigM caja (10 sobres)- UN', 'bigm-caja-10-sobres-un', '<strong>BENEFICIOS:</strong> <ul> <li>Te brinda todos los nutrientes para la recuperación post-entrenamiento.</li> <li>Alta dosis de proteína.</li> <li>Perfecto si te encuentras en etapa de ganancia.</li> <li>Ayuda a alcanzar tus necesidades calóricas.</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Ganar masa muscular y/o peso</li> </ul> <strong>DESCRIPCIÓN:</strong> BIGM ayuda en su objetivo para optimizar la recuperación y aumentar la masa muscular, con altas dosis de proteínas, carbohidratos y calorías para la construcción de masa, BIGM seguramente lo ayudará a comenzar a ver aumentos en su musculatura y consecuentemente en su peso. Cada servicio proporciona una gran cantidad de proteínas, carbohidratos, vitaminas y minerales para aumentar la cantidad que está obteniendo a través de una dieta equilibrada de alimentos', 'BENEFICIOS: Te brinda todos los nutrientes para la recuperación post-entrenamiento. Alta dosis de proteína. Perfecto si te encuentras en etapa de ganancia. Ayuda a alcanzar tus necesidades...', 'BIGMCAJA10746', 60.00, 30.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/bigm-caja-10-sobres-un_main.webp\",\"\\/uploads\\/images\\/products\\/bigm-caja-10-sobres-un_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Bigm', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(75, 10, 'Nitro whey caja (10 sobres)- UN', 'nitro-whey-caja-10-sobres-un', '<strong>BENEFICIOS:</strong> <ul> <li>Rápida absorción.</li> <li>Apoya con la recuperación.</li> <li>Alto contenido de aminoácidos.</li> <li>Ayudan a complementar los requerimientos diarios.</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Masa muscular magra y mejor recuperación</li> </ul> <strong>DESCRIPCIÓN:</strong> El post-entrenamiento es el momento perfecto para alimentar sus músculos con los nutrientes que necesitas para reconstruir adecuadamente las fibras musculares destruidas, y con NITRO WHEY, obtiene la digestibilidad y absorción más rápidas en comparación con muchas otras fuentes de proteínas disponibles. NITRO WHEY ayuda a optimizar la retención de nitrógeno, apoya la síntesis de proteínas y le brinda la oportunidad de agregar la masa muscular magra de calidad que desea.', 'BENEFICIOS: Rápida absorción. Apoya con la recuperación. Alto contenido de aminoácidos. Ayudan a complementar los requerimientos diarios. OBJETIVO: Masa muscular magra y mejor recuperación...', 'NITROWHEYC208', 60.00, 30.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/nitro-whey-caja-10-sobres-un_main.png\",\"\\/uploads\\/images\\/products\\/nitro-whey-caja-10-sobres-un_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/nitro-whey-caja-10-sobres-un_gallery_2.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'WHEY', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(76, 10, 'Fem pro caja (10 sobres) -UN', 'fem-pro-caja-10-sobres-un', 'FEM PRO es la proteína de alta calidad diseñada especialmente para la mujer, contiene un alto porcentaje de proteínas gracias a sus fuentes puras como el SUERO DE LECHE Y PROTEÍNA AISLADA DE SOYA. Su contenido en COLÁGENO HIDROLIZADO te ayuda a tener una piel más tersa y luminosa, además contiene L-GLUTAMINA. <ul> <li>Proteína de suero de leche</li> <li>29 g de proteína por servicio.</li> <li>Con colágeno hidrolizado.</li> <li>Contiene vitaminas y minerales.</li> <li>Perfecta mezcla de aminoácidos.</li> <li>Conseguirás tener un cuerpo más firme y tonificado</li> </ul>', 'FEM PRO es la proteína de alta calidad diseñada especialmente para la mujer, contiene un alto porcentaje de proteínas gracias a sus fuentes puras como el SUERO DE LECHE Y PROTEÍNA AISLADA DE...', 'FEMPROCAJA162', 60.00, 30.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/fem-pro-caja-10-sobres-un_main.png\",\"\\/uploads\\/images\\/products\\/fem-pro-caja-10-sobres-un_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Fem', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(77, 12, 'Iso whey caja (10 sobres)- UN', 'iso-whey-caja-10-sobres-un', '<strong>BENEFICIOS:</strong> <ul> <li>Es baja en calorías</li> <li>Apoya al sistema inmune.</li> <li>Evita el catabolismo.</li> <li>Se adapta a cualquier régimen alimenticio.</li> </ul> <strong>OBJETIVO:</strong> <ul> <li>Masa muscular magra y/o definición</li> </ul> <strong>DESCRIPCIÓN:</strong> ISO WHEY 90 es deliciosa y ligera, proporciona un golpe rápido de proteína fácil de beber cuando lo desee, a primera hora de la mañana, con comidas bajas en proteínas para aumentar el contenido de las mismas, como un snack, y claro está, después de los entrenamientos. Promueve la ganancia de masa muscular magra, la recuperación después del entrenamiento y numerosos beneficios para la salud. ISO WHEY 90 también es la proteína en polvo perfecta para quienes siguen dietas bajas en carbohidratos para así maximizar la pérdida de grasa.', 'BENEFICIOS: Es baja en calorías Apoya al sistema inmune. Evita el catabolismo. Se adapta a cualquier régimen alimenticio. OBJETIVO: Masa muscular magra y/o definición DESCRIPCIÓN: ISO WHEY 90 es...', 'ISOWHEYCAJ652', 80.00, 50.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/iso-whey-caja-10-sobres-un_main.png\",\"\\/uploads\\/images\\/products\\/iso-whey-caja-10-sobres-un_gallery_1.webp\",\"\\/uploads\\/images\\/products\\/iso-whey-caja-10-sobres-un_gallery_2.webp\",\"\\/uploads\\/images\\/products\\/iso-whey-caja-10-sobres-un_gallery_3.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'WHEY', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(78, 16, 'Diabolus caja por 28 unidades- UN', 'diabolus-caja-por-28-unidades-un', '<strong>DIABOLUS</strong> <p class=\"ql-align-justify\">“DIABOLUS” con un delicioso sabor a maracuyá, contiene L-CARNITINA, CAFEÍNA Y TAURINA, ingredientes que van a activar tu energía para el desarrollo de tus actividades. DIABOLUS está enriquecido con VITAMINA C, VITAMINA B3 Y VITAMINA B6.</p> <p class=\"ql-align-justify\"><strong>Usos Sugeridos</strong></p> <p class=\"ql-align-justify\">Tomar 1 sachet 15 min. antes de la actividad física. Conservar el envase en un lugar fresco y seco. Proteger del calor, la luz y la humedad.</p>', 'DIABOLUS “DIABOLUS” con un delicioso sabor a maracuyá, contiene L-CARNITINA, CAFEÍNA Y TAURINA, ingredientes que van a activar tu energía para el desarrollo de tus actividades. DIABOLUS está...', 'DIABOLUSCA236', 90.00, 60.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/diabolus-caja-por-28-unidades-un_main.webp\",\"\\/uploads\\/images\\/products\\/diabolus-caja-por-28-unidades-un_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Diabolus', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(79, 16, 'LIPO BLACK INTENSIVE AMARILLO ( 60 PASTILLAS)', 'lipo-black-intensive-amarillo-60-pastillas', '<ul> <li>DESCRIPCION Encienda su pérdida de grasa con el quemador de grasa EXTREME: LIPO-6 INTENSE UC. Este potente producto está diseñado para aquellos que se toman en serio la conquista de la grasa rebelde. LIPO-6 INTENSE UC es una fórmula ultra concentrada que proporciona una oleada de calor termogénico intenso que reactiva su metabolismo. Experimente un aumento intenso de energía y concentración, lo que le permitirá superar incluso las sesiones de entrenamiento más brutales. Ayuda a quemar grasas, mejorar el metabolismo, controlar el apetito y brindar un impulso de energía. La fórmula contiene ingredientes potentes destinados a mejorar la capacidad del cuerpo para quemar grasas de manera efectiva.</li> <li>OBJETIVOS :</li> <li>Ayuda a activar el metabolismo para estimular la quema de calorías (Mayor pérdida de grasa).</li> <li>Promueve la energía y el estado de alerta, por lo que puede mantener alto su nivel de actividad mientras hace dieta (Energía intensa + concentración).</li> <li>Controla tu apetito de manera más efectiva (Apetito suprimido).</li> </ul>', ' DESCRIPCION Encienda su pérdida de grasa con el quemador de grasa EXTREME: LIPO-6 INTENSE UC. Este potente producto está diseñado para aquellos que se toman en serio la conquista de la grasa...', 'LIPOBLACKI547', 149.00, 119.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/lipo-black-intensive-amarillo-60-pastillas_main.jpeg\",\"\\/uploads\\/images\\/products\\/lipo-black-intensive-amarillo-60-pastillas_gallery_1.jpeg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Lipo', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(80, 17, 'HIT EAA DORIAN YATES (360 GR)', 'hit-eaa-dorian-yates-360-gr', '<strong>HIT EAA</strong> garantiza que obtenga una dosis considerable de los 9 aminoácidos esenciales en una forma biodisponible de primera calidad. Utilizados a menudo como suplemento intra-entrenamiento, nuestros EAA HIT pueden ser el suplemento perfecto para tomar durante tu entrenamiento o también puedes consumirlos entre comidas. La importancia y el papel de los AAE no se limitan al crecimiento y la reparación del tejido muscular, como muchos podrían suponer, ni mucho menos. De hecho, son fundamentales para una gran cantidad de otros procesos, como la neurotransmisión, la absorción de glucosa en las células para aumentar los niveles de energía, la función inmunológica, la producción de enzimas y el suministro de oxígeno a través de los glóbulos rojos. Todo lo cual puede influir en el estado de ánimo, la felicidad, el bienestar y los niveles generales de energía. Uno de los mayores desafíos al hacer ejercicio y levantar pesas es obtener la cantidad adecuada de proteínas a través de la dieta. Para obtener el máximo beneficio de las proteínas, se necesita una fuente de proteínas «completa», es decir, que contenga todos los aminoácidos esenciales. Hay 20 aminoácidos que intervienen en la síntesis de proteínas. De ellos, hay nueve tipos esenciales (EAA) que nuestro cuerpo no puede producir, por lo que requieren una dieta equilibrada y suplementos. Los 9 EAA son histidina, isoleucina, leucina, lisina, metionina, fenilalanina, treonina, triptófano y valina. DY Nutrition HIT EAA aporta estos EAA vitales, lo que ayuda al cuerpo a cumplir una variedad de funciones esenciales. Los EAA influyen en una variedad de funciones corporales, incluidas las de los órganos, las glándulas, los tendones y las arterias, y también desempeñan un papel fundamental en la cicatrización de heridas y la reparación de tejidos, en particular de músculos y huesos, pero también de la piel y el cabello.', 'HIT EAA garantiza que obtenga una dosis considerable de los 9 aminoácidos esenciales en una forma biodisponible de primera calidad. Utilizados a menudo como suplemento intra-entrenamiento, nuestros...', 'HITEAADORI765', 146.00, 116.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/hit-eaa-dorian-yates-360-gr_main.jpeg\",\"\\/uploads\\/images\\/products\\/hit-eaa-dorian-yates-360-gr_gallery_1.jpeg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Hit', 0, 1, 2, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-07 01:08:24'),
(81, 18, 'CREATINE optimun nutrition 300g', 'creatine-micronized-300g', 'Optimum Nutrition Creatina Micronizada 300g es una creatina en polvo diseñada para quienes buscan un complemento confiable y de alta calidad. Su fórmula micronizada permite una disolución rápida, ideal para incluirla en tu rutina diaria con facilidad. <strong>MODO DE USO</strong> <ul> <li>Tomar 5g por servicio (por cada toma)</li> <li>Como suplemento dietético, tomar una cucharadita de té al raz (Aproximadamente 5gr.) una o dos veces al día en cualquier momento del día.</li> <li>Mezclar con agua de 150ml a 200ml con agua, jugo de frutas o con tu proteína de preferencia.</li> </ul>', 'Optimum Nutrition Creatina Micronizada 300g es una creatina en polvo diseñada para quienes buscan un complemento confiable y de alta calidad. Su fórmula micronizada permite una disolución rápida,...', 'CREATINEOP276', 155.00, 125.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/creatine-micronized-300g_main.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Creatine', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(82, 17, 'FLEXX- EAAS-354.9G', 'flexx-eaas-354-9g', 'Mientras que los aminoácidos y la hidratación son importantes, los adaptógenos pueden ayudar a impulsar el rendimiento un paso más allá. Flexx EAA contiene Sensoril Ashwagandha para ayudar a mejorar la vitalidad y la resistencia general para permitirle empujar más y romper las mesetas. <strong>MODO DE USO </strong> <b>Mezcle 1 cucharada medidora de EAA FLEXX con 8-10 oz. de agua fría</b>. se pueden consumir antes del entrenamiento, durante y después del entrenamiento para lograr la máxima eficacia. También se puede usar en días sin entrenamiento.', 'Mientras que los aminoácidos y la hidratación son importantes, los adaptógenos pueden ayudar a impulsar el rendimiento un paso más allá. Flexx EAA contiene Sensoril Ashwagandha para ayudar a...', 'FLEXXEAAS3662', 169.00, 139.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/flexx-eaas-354-9g_main.avif\",\"\\/uploads\\/images\\/products\\/flexx-eaas-354-9g_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Flexx-', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(83, 17, 'L-ARGININE 180 tabletas', 'l-arginine-180-tabletas', 'La L-arginina es un aminoácido que ayuda al cuerpo a generar proteína. El cuerpo suele generar toda la L-arginina que necesita. La L-arginina también se encuentra en la mayoría de los alimentos ricos en proteína, incluidos pescado, carne roja, carne de aves, soja, granos enteros, frijoles y lácteos. Como suplemento, la L-arginina también se puede usar de forma oral y tópica. También se puede administrar de forma intravenosa (IV). Debido a que la L-arginina actúa como vasodilatador y abre (dilata) los vasos sanguíneos, muchas personas toman L-arginina oral para tratar afecciones cardíacas y la disfunción eréctil. <strong>MODO DE USO</strong> Se puede tomar en cualquier momento del día, independientemente de las comidas. Si se realizan entrenamientos de fuerza, el resultado óptimo se consigue tomándola unos 30 minutos antes. La L-arginina también se puede tomar inmediatamente después de una comida para mejorar su tolerancia.', 'La L-arginina es un aminoácido que ayuda al cuerpo a generar proteína. El cuerpo suele generar toda la L-arginina que necesita. La L-arginina también se encuentra en la mayoría de los alimentos...', 'LARGININE1526', 180.00, 150.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/l-arginine-180-tabletas_main.avif\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'L-arginine', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(84, 1, 'L – Carnitina XL – 3000mg', 'l-carnitina-xl-3000mg', 'La L-carnitina ha sido un suplemento perenne durante décadas, comúnmente utilizado por muchos por el supuesto beneficio de la oxidación de ácidos grasos (quema de grasas) y un mejor rendimiento durante el ejercicio. Nuestro líquido L-Carnitine XL llevó los suplementos de L-carnitina al siguiente nivel con una combinación de L-tartrato de L-carnitina, vitamina C, ácido pantoténico, vitamina B6 y cromo. Nuestra forma de L-carnitina L-tartrato se considera ampliamente la forma óptima de L-carnitina debido a su mayor efecto general; agregar L-tartrato a la L-carnitina trae consigo un poderoso beneficio antioxidante que de otro modo se perdería en la L-carnitina básica. Al combinar los demás ingredientes, vemos un beneficio sinérgico (combinado) que parece apoyar funciones como la producción de energía celular, aumento de la capacidad antioxidante, preservación de la membrana celular, compensación del estrés oxidativo, etc., reducir la inflamación e incluso aumentar los niveles de óxido nítrico. <strong>MODO DE USO</strong> Como suplemento dietario, tomar 1 dosis (25ml) 30 minutos antes de iniciar la actividad.', 'La L-carnitina ha sido un suplemento perenne durante décadas, comúnmente utilizado por muchos por el supuesto beneficio de la oxidación de ácidos grasos (quema de grasas) y un mejor rendimiento...', 'LCARNITINA694', 142.00, 112.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/l-carnitina-xl-3000mg_main.jpg\",\"\\/uploads\\/images\\/products\\/l-carnitina-xl-3000mg_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'L', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(85, 1, 'OMEGA-3  60 CAPS', 'omega-3-60-caps', 'Tomar Omega-3 ofrece varios beneficios, especialmente para la salud cardiovascular. Ayuda a reducir los triglicéridos, disminuye el riesgo de latidos cardíacos irregulares, reduce la acumulación de placa en las arterias y puede ayudar a bajar la presión arterial,Además, el Omega-3 tiene propiedades antiinflamatorias y antioxidantes, lo que lo convierte en un suplemento útil para la salud en general. <strong>MODO DE USO </strong> tome 1 cápsula blanda dos veces al día con una comida o según lo indique el profesional de la salud.', 'Tomar Omega-3 ofrece varios beneficios, especialmente para la salud cardiovascular. Ayuda a reducir los triglicéridos, disminuye el riesgo de latidos cardíacos irregulares, reduce la acumulación...', 'OMEGA360CA678', 159.00, 129.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/omega-3-60-caps_main.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Omega-3', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(86, 1, 'ONE MASS INN 2 KILOS', 'one-mass-inn-2-kilos', 'One Mass es un ganador de peso, un suplemento alimenticio en polvo diseñado para aunmentar masa muscular especialmente para personas que tienen dificultades para subir de peso. Se caracteriza por su alto contenido de calorías, proteínas, carbohidratos y otros nutrientes que apoyan el crecimiento muscular y la recuperación después del ejercicio.  MODO DE USO: Mezclar una porción de One Mass con agua fría en un shaker o coctelera. ', 'One Mass es un ganador de peso, un suplemento alimenticio en polvo diseñado para aunmentar masa muscular especialmente para personas que tienen dificultades para subir de peso. Se caracteriza por su...', 'ONEMASSINN194', 119.00, 89.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/one-mass-inn-2-kilos_gallery_1.pdf\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'MASS', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(87, 18, 'CREATINA DORIAN 1KG', 'creatina-dorian-1kg', 'La <strong>Creatina Monohidratada</strong> de <strong>Dorian Yates</strong> es un suplemento de alta calidad diseñado para mejorar el rendimiento físico y apoyar el desarrollo muscular. Este producto ofrece creatina monohidratada pura, reconocida por su eficacia en aumentar la fuerza, la resistencia y acelerar la recuperación muscular. BENEFICIOS <ul> <li class=\"labnutrition-labnutrition-menu-app-0-x-productInfoItem\">Aumenta la fuerza.</li> <li class=\"labnutrition-labnutrition-menu-app-0-x-productInfoItem\">Aumenta la resistencia física.</li> <li class=\"labnutrition-labnutrition-menu-app-0-x-productInfoItem\">Retrasa la fatiga y cansancio.</li> <li class=\"labnutrition-labnutrition-menu-app-0-x-productInfoItem\">Beneficia la producción de energía.</li> <li class=\"labnutrition-labnutrition-menu-app-0-x-productInfoItem\">Mejora la recuperación.</li> </ul> A QUE HORA SE PUEDE CONSUMIR Como suplemento dietético, tome 6 gramos al día, espaciadas uniformemente, con el estómago vacío.', 'La Creatina Monohidratada de Dorian Yates es un suplemento de alta calidad diseñado para mejorar el rendimiento físico y apoyar el desarrollo muscular. Este producto ofrece creatina...', 'CREATINADO782', 219.00, 189.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/creatina-dorian-1kg_main.webp\",\"\\/uploads\\/images\\/products\\/creatina-dorian-1kg_gallery_1.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Creatina', 0, 1, 2, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(88, 17, 'HIT BCAA 10:11- 1200g', 'hit-bcaa-1011-1200g', 'Es una fórmula única que contiene 6 gramos masivos de aminoácidos de cadena ramificada por porción. Está diseñada para minimizar la degradación de los tejidos durante el entrenamiento y mejorar el tiempo de recuperación entre series. <strong>MODO DE USO:</strong> Durante el entrenamiento: <b>Mezcla dos scoops en agua fría y consume durante la sesión para mantener la intensidad y facilitar la recuperación muscular</b>. Post-entrenamiento: También puedes tomar una dosis después del ejercicio para optimizar la recuperación y potenciar la síntesis de proteínas.', 'Es una fórmula única que contiene 6 gramos masivos de aminoácidos de cadena ramificada por porción. Está diseñada para minimizar la degradación de los tejidos durante el entrenamiento y...', 'HITBCAA101480', 233.00, 203.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/hit-bcaa-1011-1200g_main.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Hit', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(89, 18, 'CREATINA ULTIMATE NUTRITION 300G', 'creatina-ultimate-nutrition-300g', 'Maximiza tu rendimiento y lleva tus entrenamientos al siguiente nivel con la Creatina Monohidratada Ultimate Nutrition! Esta creatina micronizada te proporciona 300gr de pura potencia para ayudarte a alcanzar tus objetivos fitness. <ul> <li>Aumenta la fuerza y la potencia muscular.</li> <li>Mejora el rendimiento deportivo.</li> <li>Favorece la recuperación muscular.</li> <li>Producto 100% natural</li> </ul> <strong>MODO DE USO: </strong>Mezcla una cucharadita (5g) de creatina con agua o tu bebida favorita antes o después de entrenar. Ideal para deportistas de alta intensidad, fisicoculturistas y atletas que buscan mejorar su rendimiento.<strong> </strong>', 'Maximiza tu rendimiento y lleva tus entrenamientos al siguiente nivel con la Creatina Monohidratada Ultimate Nutrition! Esta creatina micronizada te proporciona 300gr de pura potencia para ayudarte a...', 'CREATINAUL644', 142.00, 112.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/creatina-ultimate-nutrition-300g_main.webp\",\"\\/uploads\\/images\\/products\\/creatina-ultimate-nutrition-300g_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Creatina', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(90, 1, 'OMEGA 3 NUTRABIO FISH OIL 150 CAPSULAS', 'omega-3-fish-oil-150-capsulas-00', 'BENEFICIOS: <ul> <li>Apoya la función cerebral.</li> <li>Promueve la salud cardiovascular.</li> <li>Ayuda a promover la salud de las articulaciones.</li> <li>Apoya la visión.</li> <li>Mejora el estado de ánimo.</li> <li>Puede ayudar al aumento de masa corporal magra</li> </ul> <strong>MODO DE USO: </strong>Adultos: 2 capsulas/dia. No superar la dosis diaria expresamente recomendada recomendada para productos dieteticos.', 'BENEFICIOS: Apoya la función cerebral. Promueve la salud cardiovascular. Ayuda a promover la salud de las articulaciones. Apoya la visión. Mejora el estado de ánimo. Puede ayudar al aumento de...', 'OMEGA3NUTR681', 135.00, 105.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/omega-3-fish-oil-150-capsulas-00_main.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Omega', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(91, 16, 'Kevin Levrone Gold Levrolean 90 Cápsulas', 'kevin-levrone-gold-levrolean-90-capsulas', 'Transforma tu cuerpo con Gold Levro Lean de Kevin Levrone, un termogénico avanzado formulado para ayudarte a alcanzar tus objetivos. Esta potente fórmula combina ingredientes efectivos que aceleran el metabolismo, aumentan la quema de calorías y mejoran los niveles de energía. Gold Levro Lean también ayuda a controlar el apetito, facilitando la adherencia a tu dieta y plan de entrenamiento. MODO DE USO: Tomar 1-3 comprimidos al día con abundante agua: antes del desayuno, comida y entrenamiento. No exceda la dosis diaria recomendada. Los complementos alimenticios no deben utilizarse como sustitutos de una dieta variada y equilibrada.', 'Transforma tu cuerpo con Gold Levro Lean de Kevin Levrone, un termogénico avanzado formulado para ayudarte a alcanzar tus objetivos. Esta potente fórmula combina ingredientes efectivos que aceleran...', 'KEVINLEVRO321', 125.00, 95.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/kevin-levrone-gold-levrolean-90-capsulas_main.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Kevin', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(92, 2, 'C4 SUPERSPORT 213g', 'c4-supersport-213g', 'Desarrolla músculo y fuerza - Formulado con un compuesto que contiene nitrógeno diseñado para desarrollar masa muscular magra, maximizar el rendimiento y aumentar la fuerza en todos los atletas, desde profesionales hasta aficionados. <strong>MODO DE USO : </strong>Tome cada porción (1 cacito raso) de C4 SuperSport™ con 237 mldeagua y consúmalo 20-30 minutos antes del entrenamiento . Una vez evaluada la tolerancia, tome una porción adicional (1 cacito raso) junto con la que tomó antes del entrenamiento.<strong> </strong>', 'Desarrolla músculo y fuerza - Formulado con un compuesto que contiene nitrógeno diseñado para desarrollar masa muscular magra, maximizar el rendimiento y aumentar la fuerza en todos los atletas,...', 'C4SUPERSPO366', 157.00, 127.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/c4-supersport-213g_main.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'C4', 0, 1, 1, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(93, 18, 'Creatina Kevin levron de 500 gr', 'creatina-kevin-levron-de-500-gr', 'La creatina monohidratada de Kevin Levrone, al igual que otros productos similares, ofrece beneficios para el rendimiento físico como: aumento de la fuerza y potencia muscular mejoria en el rendimiento atletico y apoyo a la recuperacion muscular. <strong>MODO DE USO: </strong>como suplemento dietetico para adultos: Mezcle 1 porcion (2.5g) con 125ml de adua una vez al dia', 'La creatina monohidratada de Kevin Levrone, al igual que otros productos similares, ofrece beneficios para el rendimiento físico como: aumento de la fuerza y potencia muscular mejoria en el...', 'CREATINAKE276', 135.00, 105.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/creatina-kevin-levron-de-500-gr_main.webp\",\"\\/uploads\\/images\\/products\\/creatina-kevin-levron-de-500-gr_gallery_1.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Creatina', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(94, 1, 'C4 original de 30 tomas 168gr', 'c4-original-de-30-tomas-168gr', 'El C4 Original es nuestro explosivo suplemento preentrenamiento original. está constituido por una fórmula clásica basada en la energía, resistencia, repeticiones y rendimiento clásicos. <strong>MODO DE USO: </strong>Te recomendamos tomar 1 scoop (6,5 g) al día disuelta en 150-200 ml de agua unos 20-30 minutos antes de entrenar.<strong> </strong>', 'El C4 Original es nuestro explosivo suplemento preentrenamiento original. está constituido por una fórmula clásica basada en la energía, resistencia, repeticiones y rendimiento clásicos. MODO DE...', 'C4ORIGINAL612', 162.00, 132.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/c4-original-de-30-tomas-168gr_main.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'C4', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(95, 2, 'PRE ENTRENO JOCKER 30 TOMAS', 'pre-entreno-jocker-30-tomas', 'Una nutrición balanceada juega un papel fundamental en la calidad de vida. Con el ritmo acelerado que llevan muchas personas, a veces resulta complejo prestar atención a todos los requerimientos que el cuerpo necesita para estar sano y fuerte. En ese sentido, los suplementos cumplen la función de complementar la alimentación y ayudan a obtener las vitaminas, minerales, proteínas y otros componentes indispensables para el correcto funcionamiento del organismo. Recomendado para la actividad aeróbica La cafeína es un suplemento utilizado por muchas personas que realizan deporte, ya que tiene muchos beneficios: ayuda a reducir la sensación de cansancio y la fatiga en ejercicios prolongados, permite hacer más largos los entrenamientos y estimula el sistema nervioso central. Todo esto lo convierte en un suplemento ideal para actividades aeróbicas. MODO DE USO: Mezcle 1 scoop con agua fría y consuma 20-30 minutos antes del entrenamiento.', 'Una nutrición balanceada juega un papel fundamental en la calidad de vida. Con el ritmo acelerado que llevan muchas personas, a veces resulta complejo prestar atención a todos los requerimientos...', 'PREENTRENO189', 195.00, 165.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/pre-entreno-jocker-30-tomas_main.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Pre', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(96, 1, 'CREATINA UN 250G', '', '', '', 'CREATINAUN207', 60.00, 30.00, NULL, 100, 5, 0.00, NULL, '[\"/uploads/images/placeholder.jpg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Creatina', 0, 0, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(97, 2, 'PRE ENTRENO LEVEL PRO 352g', 'pre-entreno-level-pro-352g', '<ul> <li>Fórmula de pre-entreno para la energía y rendimiento.</li> <li>Tiene ingredientes como: creatina monohidratada, arginina, beta alanina y citrulina.</li> <li>Enriquecido con vitaminas y minerales.</li> <li>Ideal para potenciar la fuerza y rendimiento.</li> <li>Aumenta el foco y claridad mental.</li> <li>Ideal para entrenamientos intensos.</li> </ul> <strong>MODO DE USO: </strong> <ul> <li>Consumir 1 scoop (8g) en 250 ml. de agua. Agitar y disfrutar.</li> <li>Consumir 2 scoop (16g) en 250 ml. de agua. Agitar y disfrutar.</li> <li>Se recomienda consumirlo 15 min. antes de los entrenamientos.</li> </ul>', ' Fórmula de pre-entreno para la energía y rendimiento. Tiene ingredientes como: creatina monohidratada, arginina, beta alanina y citrulina. Enriquecido con vitaminas y minerales. Ideal para...', 'PREENTRENO426', 130.00, 100.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/pre-entreno-level-pro-352g_main.webp\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Pre', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38'),
(98, 17, 'EAA KEVIN LEVRONE 390gr', 'eaa-kevin-levrone-390gr', 'EAA de Kevin Levrone es una combinación de aminoácidos esenciales EAA y BCAA. La preparación contiene un rico perfil de aminoácidos. Cada porción proporciona una alta dosis de aminoácidos que es un apoyo para la construcción del músculo. EAA de Kevin Levrone es rico en aminoácidos de cadena ramificada, aminoácidos exógenos y endógenos que apoyan al máximo al cuerpo del atleta para obtener mejores resultados. MODO DE USO: <b>Disolver 1 scoop en 250-300 ml de agua fría</b>. Consumir antes, durante o después del entrenamiento. Este artículo es una compra recurrente o diferida.', 'EAA de Kevin Levrone es una combinación de aminoácidos esenciales EAA y BCAA. La preparación contiene un rico perfil de aminoácidos. Cada porción proporciona una alta dosis de aminoácidos que...', 'EAAKEVINLE820', 160.00, 130.00, NULL, 100, 5, 0.00, NULL, '[\"\\/uploads\\/images\\/products\\/eaa-kevin-levrone-390gr_main.webp\",\"\\/uploads\\/images\\/products\\/eaa-kevin-levrone-390gr_gallery_1.jpeg\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Eaa', 0, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-07-06 04:01:09', '2025-07-06 23:58:38');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`, `parent_id`, `image_url`, `banner_image`, `is_active`, `sort_order`, `meta_title`, `meta_description`, `created_at`) VALUES
(1, 'Proteínas', 'proteinas', 'Suplementos proteicos para desarrollo muscular y recuperación', NULL, '/images/categories/proteins.jpg', NULL, 1, 1, NULL, NULL, '2025-06-16 16:04:17'),
(2, 'PRE ENTRENOS Y ÓXIDO NITRICO', 'pre-entrenos', 'Suplementos pre-entrenamiento para máximo rendimiento.', NULL, '/images/categories/pre-workout.jpg', NULL, 1, 2, NULL, NULL, '2025-06-16 16:04:17'),
(3, 'Vitaminas y Minerales', 'vitaminas-minerales', 'Vitaminas, minerales y micronutrientes esenciales', NULL, '/images/categories/vitamins.jpg', NULL, 1, 3, NULL, NULL, '2025-06-16 16:04:17'),
(4, 'Creatina', 'creatina', 'Suplementos de creatina para fuerza, potencia y volumen muscular', NULL, '/images/categories/creatine.jpg', NULL, 1, 4, NULL, NULL, '2025-06-16 16:04:17'),
(5, 'Quemadores de Grasa', 'quemadores-grasa', 'Suplementos termogénicos para pérdida de peso', NULL, '/images/categories/fat-burners.jpg', NULL, 1, 5, NULL, NULL, '2025-06-16 16:04:17'),
(6, 'Aminoácidos', 'aminoacidos', 'BCAA, EAA y aminoácidos esenciales para recuperación', NULL, '/images/categories/amino-acids.jpg', NULL, 1, 6, NULL, NULL, '2025-06-16 16:04:17'),
(7, 'Accesorios de Entrenamiento', 'accesorios-entrenamiento', 'Accesorios y equipos para optimizar tu entrenamiento', NULL, '/images/categories/accessories.jpg', NULL, 1, 7, NULL, NULL, '2025-06-16 16:04:17'),
(8, 'Ropa Deportiva', 'ropa-deportiva', 'Ropa y calzado deportivo de alta calidad', NULL, '/images/categories/clothing.jpg', NULL, 1, 8, NULL, NULL, '2025-06-16 16:04:17'),
(10, 'PROTEÍNAS WHEY', 'whey', 'Proteínas de suero de leche de alta calidad para el desarrollo muscular.', NULL, '/images/categories/proteins.jpg', NULL, 1, 9, NULL, NULL, '2025-07-06 04:01:09'),
(11, 'GANADORES DE MASA', 'ganadores-de-masa', 'Suplementos hipercalóricos para ganar peso y masa muscular.', NULL, '/images/categories/clothing.jpg', NULL, 1, 10, NULL, NULL, '2025-07-06 04:01:09'),
(12, 'PROTEINAS ISOLATADAS', 'proteinas-isolatadas', 'Proteínas aisladas de máxima pureza y absorción rápida.', NULL, NULL, NULL, 1, 11, NULL, NULL, '2025-07-06 04:01:09'),
(14, 'PRECURSOR DE LA TESTO', 'testo', 'Suplementos naturales para optimizar niveles hormonales.', NULL, NULL, NULL, 1, 12, NULL, NULL, '2025-07-06 04:01:09'),
(15, 'MULTIVITAMINICO Colágenos OMEGAS', 'multivitaminico', 'Vitaminas, minerales y suplementos para salud general.', NULL, '/images/categories/vitamins.jpg', NULL, 1, 13, NULL, NULL, '2025-07-06 04:01:09'),
(16, 'QUEMADORES DE GRASA', 'quemadores', 'Suplementos termogénicos para pérdida de grasa.', NULL, '/images/categories/fat-burners.jpg', NULL, 1, 14, NULL, NULL, '2025-07-06 04:01:09'),
(17, 'AMINOÁCIDOS Y BCAA', 'aminoacidos-y-bcaa', 'Aminoácidos esenciales para recuperación muscular.', NULL, '/images/categories/amino-acids.jpg', NULL, 1, 15, NULL, NULL, '2025-07-06 04:01:09'),
(18, 'CREATINAS Y GLUTAMINAS', 'creatinas-y-glutaminas', 'Suplementos para fuerza, potencia y recuperación.', NULL, '/images/categories/creatine.jpg', NULL, 1, 16, NULL, NULL, '2025-07-06 04:01:09'),
(19, 'PROTECTOR HEPÁTICO', 'protector-hepatico', 'Suplementos para protección y salud hepática.', NULL, NULL, NULL, 1, 17, NULL, NULL, '2025-07-06 04:01:09');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `pros` text DEFAULT NULL,
  `cons` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `is_approved` tinyint(1) DEFAULT 0,
  `is_verified_purchase` tinyint(1) DEFAULT 0,
  `helpful_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `product_reviews`
--
DELIMITER $$
CREATE TRIGGER `trg_update_product_rating` AFTER INSERT ON `product_reviews` FOR EACH ROW BEGIN
    UPDATE products 
    SET avg_rating = (
        SELECT AVG(rating) FROM product_reviews 
        WHERE product_id = NEW.product_id AND is_approved = 1
    ),
    reviews_count = (
        SELECT COUNT(*) FROM product_reviews 
        WHERE product_id = NEW.product_id AND is_approved = 1
    )
    WHERE id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_variations`
--

CREATE TABLE `product_variations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `weight` decimal(8,2) DEFAULT NULL,
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attributes`)),
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `routines`
--

CREATE TABLE `routines` (
  `id` int(11) NOT NULL,
  `gym_id` int(11) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `objective` enum('weight_loss','muscle_gain','strength','endurance','flexibility','rehabilitation') DEFAULT 'muscle_gain',
  `difficulty_level` enum('beginner','intermediate','advanced') DEFAULT 'intermediate',
  `duration_weeks` int(11) DEFAULT 4,
  `sessions_per_week` int(11) DEFAULT 3,
  `estimated_duration_minutes` int(11) DEFAULT 60,
  `is_template` tinyint(1) DEFAULT 0,
  `is_public` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `views_count` int(11) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `rating_count` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `equipment_needed` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`equipment_needed`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `routines`
--

INSERT INTO `routines` (`id`, `gym_id`, `instructor_id`, `client_id`, `name`, `description`, `objective`, `difficulty_level`, `duration_weeks`, `sessions_per_week`, `estimated_duration_minutes`, `is_template`, `is_public`, `is_active`, `views_count`, `likes_count`, `rating`, `rating_count`, `image_url`, `tags`, `equipment_needed`, `created_at`, `updated_at`) VALUES
(1, 1, 2, NULL, 'FullBody Principiante', 'Rutina completa de cuerpo para principiantes. Incluye los ejercicios fundamentales para desarrollar fuerza base, técnica correcta y crear el hábito de entrenamiento.', 'muscle_gain', 'beginner', 8, 3, 60, 1, 1, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(2, 1, 2, NULL, 'HIIT Quema Grasa Extrema', 'Rutina de alta intensidad diseñada para máxima quema de calorías y pérdida de grasa. Combina entrenamiento de fuerza con cardio intensivo.', 'weight_loss', 'intermediate', 12, 5, 45, 1, 1, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(3, 1, 2, NULL, 'Fuerza Máxima Avanzada', 'Rutina de fuerza para atletas experimentados. Enfocada en desarrollo de fuerza máxima mediante trabajo pesado en movimientos fundamentales.', 'strength', 'advanced', 16, 4, 90, 1, 1, 1, 0, 0, 0.00, 0, NULL, NULL, NULL, '2025-06-16 16:04:17', '2025-06-16 16:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `routine_exercises`
--

CREATE TABLE `routine_exercises` (
  `id` int(11) NOT NULL,
  `routine_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `order_index` int(11) NOT NULL,
  `sets` int(11) DEFAULT 3,
  `reps` varchar(20) DEFAULT '10',
  `weight` varchar(20) DEFAULT NULL,
  `rest_seconds` int(11) DEFAULT 60,
  `tempo` varchar(10) DEFAULT NULL,
  `rpe` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `superset_group` int(11) DEFAULT NULL,
  `is_warmup` tinyint(1) DEFAULT 0,
  `is_cooldown` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `routine_exercises`
--

INSERT INTO `routine_exercises` (`id`, `routine_id`, `exercise_id`, `day_number`, `order_index`, `sets`, `reps`, `weight`, `rest_seconds`, `tempo`, `rpe`, `notes`, `superset_group`, `is_warmup`, `is_cooldown`, `created_at`) VALUES
(1, 1, 1, 1, 1, 3, '8-10', NULL, 90, NULL, NULL, 'Ejercicio principal. Enfócate en la técnica correcta.', NULL, 0, 0, '2025-06-16 16:04:17'),
(2, 1, 4, 1, 2, 3, '5-8', NULL, 90, NULL, NULL, 'Usa banda elástica de asistencia si es necesario.', NULL, 0, 0, '2025-06-16 16:04:17'),
(3, 1, 5, 1, 3, 3, '8-10', NULL, 75, NULL, NULL, 'Mantén el core activado durante el movimiento.', NULL, 0, 0, '2025-06-16 16:04:17'),
(4, 1, 13, 1, 4, 3, '30-45 seg', NULL, 60, NULL, NULL, 'Progresa gradualmente en tiempo.', NULL, 0, 0, '2025-06-16 16:04:17'),
(5, 2, 6, 1, 1, 4, '30 seg', NULL, 15, NULL, NULL, 'Máxima intensidad, trabajo-descanso 30:15', NULL, 0, 0, '2025-06-16 16:04:17'),
(6, 2, 1, 1, 2, 4, '12-15', NULL, 15, NULL, NULL, 'Peso moderado, alta velocidad', NULL, 0, 0, '2025-06-16 16:04:17'),
(7, 2, 7, 1, 3, 4, '20 seg', NULL, 15, NULL, NULL, 'Alternación rápida', NULL, 0, 0, '2025-06-16 16:04:17'),
(8, 2, 4, 1, 4, 4, '8-12', NULL, 15, NULL, NULL, 'Asistidas si es necesario', NULL, 0, 0, '2025-06-16 16:04:17'),
(9, 3, 1, 1, 1, 5, '3-5', NULL, 180, NULL, NULL, 'Fuerza máxima: 85-95% 1RM. Descansos largos.', NULL, 0, 0, '2025-06-16 16:04:17'),
(10, 3, 5, 1, 2, 4, '3-5', NULL, 150, NULL, NULL, 'Trabajo auxiliar pesado: 80-90% 1RM.', NULL, 0, 0, '2025-06-16 16:04:17'),
(11, 3, 4, 1, 3, 3, '6-8', NULL, 120, NULL, NULL, 'Con peso adicional si es posible.', NULL, 0, 0, '2025-06-16 16:04:17'),
(12, 3, 3, 2, 1, 5, '1-3', NULL, 240, NULL, NULL, 'Máxima carga: 90-100% 1RM.', NULL, 0, 0, '2025-06-16 16:04:17'),
(13, 3, 2, 3, 1, 5, '1-5', NULL, 180, NULL, NULL, 'Sentadilla máxima: 85-100% 1RM.', NULL, 0, 0, '2025-06-16 16:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `security_tokens`
--

CREATE TABLE `security_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `type` enum('password_reset','email_verification','remember_me') NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_tokens`
--

INSERT INTO `security_tokens` (`id`, `user_id`, `token`, `type`, `expires_at`, `used_at`, `created_at`) VALUES
(2, 1, 'e7d6ff4740898ac0c48217a277f670a6eb81141fba5206623ebe917868775bfe', 'remember_me', '2025-08-09 00:02:54', NULL, '2025-07-10 00:02:54');

-- --------------------------------------------------------

--
-- Table structure for table `special_offers`
--

CREATE TABLE `special_offers` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `background_color` varchar(7) DEFAULT '#ff6b35',
  `text_color` varchar(7) DEFAULT '#ffffff',
  `button_text` varchar(100) DEFAULT 'Ver Oferta',
  `button_link` varchar(500) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `special_offers`
--

INSERT INTO `special_offers` (`id`, `title`, `subtitle`, `description`, `discount_percentage`, `discount_amount`, `image`, `background_color`, `text_color`, `button_text`, `button_link`, `start_date`, `end_date`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, '¡MEGA DESCUENTO!', 'Hasta 50% OFF en Suplementos', 'Aprovecha esta oferta limitada en nuestra selección premium de suplementos deportivos', 50.00, NULL, 'offer-supplements.jpg', '#ff6b35', '#ffffff', 'Ver Ofertas', '/store?category=suplementos', '2025-06-29 22:35:55', '2025-07-29 22:35:55', 1, 1, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(2, 'MEMBRESÍA PREMIUM', '3 Meses por el precio de 2', 'Acceso completo a todas nuestras instalaciones y clases grupales', 33.33, NULL, 'offer-membership.jpg', '#ff6b35', '#ffffff', 'Suscribirse', '/membership', '2025-06-29 22:35:55', '2025-07-14 22:35:55', 1, 2, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(3, 'RUTINAS PERSONALIZADAS', 'Gratis con tu primera compra', 'Recibe una rutina personalizada diseñada por nuestros expertos', 0.00, NULL, 'offer-routine.jpg', '#ff6b35', '#ffffff', 'Empezar Ahora', '/routines', '2025-06-29 22:35:55', '2025-08-13 22:35:55', 1, 3, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(4, '¡MEGA DESCUENTO!', 'Hasta 50% OFF en Suplementos', 'Aprovecha esta oferta limitada en nuestra selección premium de suplementos deportivos', 50.00, NULL, 'offer-supplements.jpg', '#ff6b35', '#ffffff', 'Ver Ofertas', '/store?category=suplementos', '2025-06-29 22:36:28', '2025-07-29 22:36:28', 1, 1, '2025-06-30 03:36:28', '2025-06-30 03:36:28'),
(5, 'MEMBRESÍA PREMIUM', '3 Meses por el precio de 2', 'Acceso completo a todas nuestras instalaciones y clases grupales', 33.33, NULL, 'offer-membership.jpg', '#ff6b35', '#ffffff', 'Suscribirse', '/membership', '2025-06-29 22:36:28', '2025-07-14 22:36:28', 1, 2, '2025-06-30 03:36:28', '2025-06-30 03:36:28'),
(6, 'RUTINAS PERSONALIZADAS', 'Gratis con tu primera compra', 'Recibe una rutina personalizada diseñada por nuestros expertos', 0.00, NULL, 'offer-routine.jpg', '#ff6b35', '#ffffff', 'Empezar Ahora', '/routines', '2025-06-29 22:36:28', '2025-08-13 22:36:28', 1, 3, '2025-06-30 03:36:28', '2025-06-30 03:36:28'),
(7, '¡MEGA DESCUENTO!', 'Hasta 50% OFF en Suplementos', 'Aprovecha esta oferta limitada en nuestra selección premium de suplementos deportivos', 50.00, NULL, 'offer-supplements.jpg', '#ff6b35', '#ffffff', 'Ver Ofertas', '/store?category=suplementos', '2025-06-29 23:06:37', '2025-07-29 23:06:37', 1, 1, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(8, 'MEMBRESÍA PREMIUM', '3 Meses por el precio de 2', 'Acceso completo a todas nuestras instalaciones y clases grupales', 33.33, NULL, 'offer-membership.jpg', '#ff6b35', '#ffffff', 'Suscribirse', '/membership', '2025-06-29 23:06:37', '2025-07-29 23:06:37', 1, 2, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(9, 'RUTINAS PERSONALIZADAS', 'Gratis con tu primera compra', 'Recibe una rutina personalizada diseñada por nuestros expertos', 0.00, NULL, 'offer-routine.jpg', '#ff6b35', '#ffffff', 'Empezar Ahora', '/routines', '2025-06-29 23:06:37', '2025-07-29 23:06:37', 1, 3, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(10, 'CLASES GRUPALES', 'Primera semana GRATIS', 'Prueba todas nuestras clases grupales', 100.00, NULL, 'offer-classes.jpg', '#ff6b35', '#ffffff', 'Reservar Clase', '/classes', '2025-06-29 23:06:37', '2025-07-29 23:06:37', 1, 4, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(11, 'EVALUACIÓN NUTRICIONAL', '50% de descuento', 'Consulta con nuestros nutricionistas especializados', 50.00, NULL, 'offer-nutrition.jpg', '#ff6b35', '#ffffff', 'Agendar Cita', '/nutrition', '2025-06-29 23:06:37', '2025-07-29 23:06:37', 1, 5, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(12, '¡MEGA DESCUENTO!', 'Hasta 50% OFF en Suplementos', 'Aprovecha esta oferta limitada en nuestra selección premium de suplementos deportivos', 50.00, NULL, 'offer-supplements.jpg', '#ff6b35', '#ffffff', 'Ver Ofertas', '/store?category=suplementos', '2025-06-29 23:07:02', '2025-07-29 23:07:02', 1, 1, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(13, 'MEMBRESÍA PREMIUM', '3 Meses por el precio de 2', 'Acceso completo a todas nuestras instalaciones y clases grupales', 33.33, NULL, 'offer-membership.jpg', '#ff6b35', '#ffffff', 'Suscribirse', '/membership', '2025-06-29 23:07:02', '2025-07-29 23:07:02', 1, 2, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(14, 'RUTINAS PERSONALIZADAS', 'Gratis con tu primera compra', 'Recibe una rutina personalizada diseñada por nuestros expertos', 0.00, NULL, 'offer-routine.jpg', '#ff6b35', '#ffffff', 'Empezar Ahora', '/routines', '2025-06-29 23:07:02', '2025-07-29 23:07:02', 1, 3, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(15, 'CLASES GRUPALES', 'Primera semana GRATIS', 'Prueba todas nuestras clases grupales', 100.00, NULL, 'offer-classes.jpg', '#ff6b35', '#ffffff', 'Reservar Clase', '/classes', '2025-06-29 23:07:02', '2025-07-29 23:07:02', 1, 4, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(16, 'EVALUACIÓN NUTRICIONAL', '50% de descuento', 'Consulta con nuestros nutricionistas especializados', 50.00, NULL, 'offer-nutrition.jpg', '#ff6b35', '#ffffff', 'Agendar Cita', '/nutrition', '2025-06-29 23:07:02', '2025-07-29 23:07:02', 1, 5, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(17, '¡MEGA DESCUENTO!', 'Hasta 50% OFF en Suplementos', 'Aprovecha esta oferta limitada en nuestra selección premium de suplementos deportivos', 50.00, NULL, 'offer-supplements.jpg', '#ff6b35', '#ffffff', 'Ver Ofertas', '/store?category=suplementos', '2025-06-29 23:07:36', '2025-07-29 23:07:36', 1, 1, '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(18, 'MEMBRESÍA PREMIUM', '3 Meses por el precio de 2', 'Acceso completo a todas nuestras instalaciones y clases grupales', 33.33, NULL, 'offer-membership.jpg', '#ff6b35', '#ffffff', 'Suscribirse', '/membership', '2025-06-29 23:07:36', '2025-07-29 23:07:36', 1, 2, '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(19, 'RUTINAS PERSONALIZADAS', 'Gratis con tu primera compra', 'Recibe una rutina personalizada diseñada por nuestros expertos', 0.00, NULL, 'offer-routine.jpg', '#ff6b35', '#ffffff', 'Empezar Ahora', '/routines', '2025-06-29 23:07:36', '2025-07-29 23:07:36', 1, 3, '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(20, 'CLASES GRUPALES', 'Primera semana GRATIS', 'Prueba todas nuestras clases grupales', 100.00, NULL, 'offer-classes.jpg', '#ff6b35', '#ffffff', 'Reservar Clase', '/classes', '2025-06-29 23:07:36', '2025-07-29 23:07:36', 1, 4, '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(21, 'EVALUACIÓN NUTRICIONAL', '50% de descuento', 'Consulta con nuestros nutricionistas especializados', 50.00, NULL, 'offer-nutrition.jpg', '#ff6b35', '#ffffff', 'Agendar Cita', '/nutrition', '2025-06-29 23:07:36', '2025-07-29 23:07:36', 1, 5, '2025-06-30 04:07:36', '2025-06-30 04:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `setting_type` enum('string','integer','boolean','json','text') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `setting_group`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'STYLOFITNESS', 'string', 'Nombre del sitio web', 'general', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(2, 'site_description', 'Gimnasio profesional con rutinas personalizadas y tienda de suplementos', 'string', 'Descripción del sitio', 'general', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(3, 'contact_email', 'info@stylofitness.com', 'string', 'Email de contacto principal', 'general', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(4, 'contact_phone', '+51 999 888 777', 'string', 'Teléfono de contacto', 'general', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(5, 'maintenance_mode', '0', 'boolean', 'Modo de mantenimiento', 'general', 0, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(6, 'registration_enabled', '1', 'boolean', 'Permitir registro de usuarios', 'users', 0, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(7, 'currency_symbol', 'S/', 'string', 'Símbolo de moneda', 'store', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(8, 'tax_rate', '0.18', 'string', 'Tasa de impuestos (IGV)', 'store', 0, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(9, 'free_shipping_minimum', '150', 'string', 'Monto mínimo para envío gratis', 'store', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(10, 'store_enabled', '1', 'boolean', 'Habilitar tienda online', 'store', 1, '2025-06-16 16:04:17', '2025-06-16 16:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `testimonial_text` text NOT NULL,
  `rating` tinyint(1) DEFAULT 5 CHECK (`rating` >= 1 and `rating` <= 5),
  `location` varchar(255) DEFAULT NULL,
  `date_given` date DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `social_proof` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_proof`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `role`, `company`, `image`, `testimonial_text`, `rating`, `location`, `date_given`, `is_featured`, `is_active`, `display_order`, `social_proof`, `created_at`, `updated_at`) VALUES
(1, 'María González', 'Cliente Premium', NULL, 'maria.jpg', 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord. Los videos explicativos son increíbles y el seguimiento es muy detallado.', 5, 'Lima, Perú', '2024-01-15', 1, 1, 1, NULL, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(2, 'Carlos Rodríguez', 'Atleta Profesional', NULL, 'carlos.jpg', 'La combinación de entrenamiento personalizado y suplementos recomendados ha transformado completamente mi rendimiento deportivo. Recomiendo STYLOFITNESS al 100%.', 5, 'Arequipa, Perú', '2024-01-20', 1, 1, 2, NULL, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(3, 'Ana Morales', 'Fitness Enthusiast', NULL, 'ana.jpg', 'Me encanta poder seguir mis rutinas desde casa con los videos HD. La tienda online tiene los mejores precios y la entrega es súper rápida.', 5, 'Cusco, Perú', '2024-01-25', 1, 1, 3, NULL, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(4, 'Diego Fernández', 'Cliente VIP', NULL, 'diego.jpg', 'El seguimiento personalizado y las clases grupales han hecho que entrenar sea adictivo. Los resultados están garantizados con este sistema.', 5, 'Trujillo, Perú', '2024-01-30', 1, 1, 4, NULL, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(5, 'Lucía Vargas', 'Entrenadora Personal', NULL, 'lucia.jpg', 'Como profesional del fitness, puedo decir que STYLOFITNESS tiene el mejor sistema de rutinas que he visto. La tecnología es impresionante.', 5, 'Chiclayo, Perú', '2024-02-05', 0, 1, 5, NULL, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(6, 'Roberto Silva', 'Empresario', NULL, 'roberto.jpg', 'Perfecto para personas ocupadas como yo. Las rutinas se adaptan a mi horario y los resultados son visibles desde la primera semana.', 5, 'Piura, Perú', '2024-02-10', 0, 1, 6, NULL, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(7, 'María González', 'Cliente Premium', NULL, 'maria.jpg', 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord. Los videos explicativos son increíbles y el seguimiento es muy detallado.', 5, 'Lima, Perú', '2024-01-15', 1, 1, 1, NULL, '2025-06-30 03:36:29', '2025-06-30 03:36:29'),
(8, 'Carlos Rodríguez', 'Atleta Profesional', NULL, 'carlos.jpg', 'La combinación de entrenamiento personalizado y suplementos recomendados ha transformado completamente mi rendimiento deportivo. Recomiendo STYLOFITNESS al 100%.', 5, 'Arequipa, Perú', '2024-01-20', 1, 1, 2, NULL, '2025-06-30 03:36:29', '2025-06-30 03:36:29'),
(9, 'Ana Morales', 'Fitness Enthusiast', NULL, 'ana.jpg', 'Me encanta poder seguir mis rutinas desde casa con los videos HD. La tienda online tiene los mejores precios y la entrega es súper rápida.', 5, 'Cusco, Perú', '2024-01-25', 1, 1, 3, NULL, '2025-06-30 03:36:29', '2025-06-30 03:36:29'),
(10, 'Diego Fernández', 'Cliente VIP', NULL, 'diego.jpg', 'El seguimiento personalizado y las clases grupales han hecho que entrenar sea adictivo. Los resultados están garantizados con este sistema.', 5, 'Trujillo, Perú', '2024-01-30', 1, 1, 4, NULL, '2025-06-30 03:36:29', '2025-06-30 03:36:29'),
(11, 'Lucía Vargas', 'Entrenadora Personal', NULL, 'lucia.jpg', 'Como profesional del fitness, puedo decir que STYLOFITNESS tiene el mejor sistema de rutinas que he visto. La tecnología es impresionante.', 5, 'Chiclayo, Perú', '2024-02-05', 0, 1, 5, NULL, '2025-06-30 03:36:29', '2025-06-30 03:36:29'),
(12, 'Roberto Silva', 'Empresario', NULL, 'roberto.jpg', 'Perfecto para personas ocupadas como yo. Las rutinas se adaptan a mi horario y los resultados son visibles desde la primera semana.', 5, 'Piura, Perú', '2024-02-10', 0, 1, 6, NULL, '2025-06-30 03:36:29', '2025-06-30 03:36:29'),
(13, 'María González', 'Cliente Premium', NULL, 'maria.jpg', 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord.', 5, 'Lima, Perú', '2024-01-15', 1, 1, 1, '{\"verified\":true,\"membership_duration\":\"8 meses\"}', '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(14, 'Carlos Rodríguez', 'Atleta Profesional', NULL, 'carlos.jpg', 'La combinación de entrenamiento personalizado y suplementos ha transformado mi rendimiento.', 5, 'Arequipa, Perú', '2024-01-20', 1, 1, 2, '{\"verified\":true,\"sport\":\"Powerlifting\"}', '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(15, 'Ana Morales', 'Fitness Enthusiast', NULL, 'ana.jpg', 'Me encanta poder seguir mis rutinas desde casa con los videos HD.', 5, 'Cusco, Perú', '2024-01-25', 1, 1, 3, '{\"verified\":true,\"followers\":\"15K\"}', '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(16, 'Diego Fernández', 'Cliente VIP', NULL, 'diego.jpg', 'El seguimiento personalizado ha hecho que entrenar sea adictivo.', 5, 'Trujillo, Perú', '2024-01-30', 1, 1, 4, '{\"verified\":true,\"profession\":\"CEO\"}', '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(17, 'María González', 'Cliente Premium', NULL, 'maria.jpg', 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord.', 5, 'Lima, Perú', '2024-01-15', 1, 1, 1, '{\"verified\":true,\"membership_duration\":\"8 meses\"}', '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(18, 'Carlos Rodríguez', 'Atleta Profesional', NULL, 'carlos.jpg', 'La combinación de entrenamiento personalizado y suplementos ha transformado mi rendimiento.', 5, 'Arequipa, Perú', '2024-01-20', 1, 1, 2, '{\"verified\":true,\"sport\":\"Powerlifting\"}', '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(19, 'Ana Morales', 'Fitness Enthusiast', NULL, 'ana.jpg', 'Me encanta poder seguir mis rutinas desde casa con los videos HD.', 5, 'Cusco, Perú', '2024-01-25', 1, 1, 3, '{\"verified\":true,\"followers\":\"15K\"}', '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(20, 'Diego Fernández', 'Cliente VIP', NULL, 'diego.jpg', 'El seguimiento personalizado ha hecho que entrenar sea adictivo.', 5, 'Trujillo, Perú', '2024-01-30', 1, 1, 4, '{\"verified\":true,\"profession\":\"CEO\"}', '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(21, 'María González', 'Cliente Premium', NULL, 'maria.jpg', 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord.', 5, 'Lima, Perú', '2024-01-15', 1, 1, 1, '{\"verified\":true,\"membership_duration\":\"8 meses\"}', '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(22, 'Carlos Rodríguez', 'Atleta Profesional', NULL, 'carlos.jpg', 'La combinación de entrenamiento personalizado y suplementos ha transformado mi rendimiento.', 5, 'Arequipa, Perú', '2024-01-20', 1, 1, 2, '{\"verified\":true,\"sport\":\"Powerlifting\"}', '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(23, 'Ana Morales', 'Fitness Enthusiast', NULL, 'ana.jpg', 'Me encanta poder seguir mis rutinas desde casa con los videos HD.', 5, 'Cusco, Perú', '2024-01-25', 1, 1, 3, '{\"verified\":true,\"followers\":\"15K\"}', '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(24, 'Diego Fernández', 'Cliente VIP', NULL, 'diego.jpg', 'El seguimiento personalizado ha hecho que entrenar sea adictivo.', 5, 'Trujillo, Perú', '2024-01-30', 1, 1, 4, '{\"verified\":true,\"profession\":\"CEO\"}', '2025-06-30 04:07:36', '2025-06-30 04:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `gym_id` int(11) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `role` enum('admin','instructor','client') DEFAULT 'client',
  `profile_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `membership_type` varchar(50) DEFAULT 'basic',
  `membership_expires` date DEFAULT NULL,
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferences`)),
  `emergency_contact` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`emergency_contact`)),
  `medical_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medical_info`)),
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `login_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `gym_id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone`, `date_of_birth`, `gender`, `role`, `profile_image`, `is_active`, `membership_type`, `membership_expires`, `preferences`, `emergency_contact`, `medical_info`, `email_verified_at`, `remember_token`, `last_login_at`, `login_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'admin@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Principal', NULL, NULL, NULL, 'admin', NULL, 1, 'premium', '2030-12-31', NULL, NULL, NULL, NULL, NULL, '2025-07-07 01:38:02', 2, '2025-06-16 16:04:17', '2025-07-07 01:38:02'),
(2, 1, 'instructor', 'instructor@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Fitness', NULL, NULL, NULL, 'instructor', NULL, 1, 'premium', '2030-12-31', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-06-16 16:04:17', '2025-06-16 16:04:17'),
(3, 1, 'cliente', 'cliente@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'González', NULL, NULL, NULL, 'client', NULL, 1, 'basic', '2025-07-31', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-06-16 16:04:17', '2025-06-16 16:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `resource_type` varchar(50) DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_activity_logs`
--

INSERT INTO `user_activity_logs` (`id`, `user_id`, `action`, `resource_type`, `resource_id`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'api_login', NULL, NULL, NULL, '::1', 'PostmanRuntime/7.44.1', '2025-07-07 01:34:42'),
(2, 1, 'api_login', NULL, NULL, NULL, '::1', 'PostmanRuntime/7.44.1', '2025-07-07 01:38:02'),
(3, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 01:42:00'),
(4, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:25:17'),
(5, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:25:44'),
(6, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:25:45'),
(7, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:25:45'),
(8, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:25:45'),
(9, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:25:45'),
(10, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:25:46'),
(11, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:25:46'),
(12, 1, 'logout', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:27:19'),
(13, 1, 'logout', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:34:10'),
(14, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:35:22'),
(15, 1, 'logout', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 02:36:33'),
(16, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 03:13:40'),
(17, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-07 10:55:31'),
(18, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-08 00:00:12'),
(19, 1, 'login', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-10 00:02:54');

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `routine_id` int(11) DEFAULT NULL,
  `measurement_date` date NOT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `body_fat_percentage` decimal(5,2) DEFAULT NULL,
  `muscle_mass_kg` decimal(5,2) DEFAULT NULL,
  `measurements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`measurements`)),
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photos`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `why_choose_us`
--

CREATE TABLE `why_choose_us` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `icon_color` varchar(7) DEFAULT '#ff6b35',
  `background_gradient` varchar(100) DEFAULT NULL,
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`highlights`)),
  `stats` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`stats`)),
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `why_choose_us`
--

INSERT INTO `why_choose_us` (`id`, `title`, `subtitle`, `description`, `icon`, `icon_color`, `background_gradient`, `highlights`, `stats`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Rutinas Personalizadas', 'Entrenamientos inteligentes con IA', 'Entrenamientos diseñados específicamente para tus objetivos, nivel y disponibilidad de tiempo', 'fas fa-dumbbell', '#ff6b35', NULL, '[\"Videos HD Explicativos\", \"Seguimiento en Tiempo Real\", \"Ajustes Automáticos IA\", \"Soporte 24/7\"]', '{\"exercises\": \"1000+\", \"success_rate\": \"95%\"}', 1, 1, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(2, 'Tienda Especializada', 'Los mejores suplementos del mercado', 'Productos certificados y de las marcas más reconocidas mundialmente', 'fas fa-store', '#ff6b35', NULL, '[\"Productos Certificados\", \"Envío Gratis\", \"Garantía Total\", \"Asesoría Nutricional\"]', '{\"products\": \"500+\", \"satisfaction\": \"98%\"}', 1, 2, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(3, 'Clases Grupales', 'Entrenamientos dinámicos y motivadores', 'Variedad de clases dirigidas por instructores certificados', 'fas fa-users', '#ff6b35', NULL, '[\"Instructores Certificados\", \"Horarios Flexibles\", \"Ambiente Motivador\", \"Todos los Niveles\"]', '{\"classes\": \"20+\", \"instructors\": \"15\"}', 1, 3, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(4, 'Tecnología Avanzada', 'Seguimiento y análisis en tiempo real', 'Monitoreo completo de tu progreso con tecnología de vanguardia', 'fas fa-chart-line', '#ff6b35', NULL, '[\"App Móvil\", \"Análisis Detallado\", \"Sincronización Cloud\", \"Reportes Personalizados\"]', '{\"accuracy\": \"99%\", \"users\": \"10000+\"}', 1, 4, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(5, 'Rutinas Personalizadas', 'Entrenamientos inteligentes con IA', 'Entrenamientos diseñados específicamente para tus objetivos, nivel y disponibilidad de tiempo', 'fas fa-dumbbell', '#ff6b35', NULL, '[\"Videos HD Explicativos\", \"Seguimiento en Tiempo Real\", \"Ajustes Automáticos IA\", \"Soporte 24/7\"]', '{\"exercises\": \"1000+\", \"success_rate\": \"95%\"}', 1, 1, '2025-06-30 03:36:29', '2025-06-30 03:36:29'),
(6, 'Tienda Especializada', 'Los mejores suplementos del mercado', 'Productos certificados y de las marcas más reconocidas mundialmente', 'fas fa-store', '#ff6b35', NULL, '[\"Productos Certificados\", \"Envío Gratis\", \"Garantía Total\", \"Asesoría Nutricional\"]', '{\"products\": \"500+\", \"satisfaction\": \"98%\"}', 1, 2, '2025-06-30 03:36:29', '2025-06-30 03:36:29'),
(7, 'Clases Grupales', 'Entrenamientos dinámicos y motivadores', 'Variedad de clases dirigidas por instructores certificados', 'fas fa-users', '#ff6b35', NULL, '[\"Instructores Certificados\", \"Horarios Flexibles\", \"Ambiente Motivador\", \"Todos los Niveles\"]', '{\"classes\": \"20+\", \"instructors\": \"15\"}', 1, 3, '2025-06-30 03:36:29', '2025-06-30 03:36:29'),
(8, 'Tecnología Avanzada', 'Seguimiento y análisis en tiempo real', 'Monitoreo completo de tu progreso con tecnología de vanguardia', 'fas fa-chart-line', '#ff6b35', NULL, '[\"App Móvil\", \"Análisis Detallado\", \"Sincronización Cloud\", \"Reportes Personalizados\"]', '{\"accuracy\": \"99%\", \"users\": \"10000+\"}', 1, 4, '2025-06-30 03:36:29', '2025-06-30 03:36:29'),
(9, 'Rutinas Personalizadas', 'Entrenamientos inteligentes con IA', 'Entrenamientos diseñados específicamente para tus objetivos', 'fas fa-dumbbell', '#ff6b35', NULL, '[\"Videos HD Explicativos\",\"Seguimiento en Tiempo Real\",\"Ajustes Autom\\u00e1ticos IA\",\"Soporte 24\\/7\"]', '{\"exercises\":\"1000+\",\"success_rate\":\"95%\"}', 1, 1, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(10, 'Tienda Especializada', 'Los mejores suplementos del mercado', 'Productos certificados y de las marcas más reconocidas', 'fas fa-store', '#2c3e50', NULL, '[\"Productos Certificados\",\"Env\\u00edo Gratis\",\"Garant\\u00eda Total\",\"Asesor\\u00eda Nutricional\"]', '{\"products\":\"500+\",\"satisfaction\":\"98%\"}', 1, 2, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(11, 'Clases Grupales', 'Entrenamientos dinámicos y motivadores', 'Variedad de clases dirigidas por instructores certificados', 'fas fa-users', '#e74c3c', NULL, '[\"Instructores Certificados\",\"Horarios Flexibles\",\"Ambiente Motivador\",\"Todos los Niveles\"]', '{\"classes\":\"20+\",\"instructors\":\"15\"}', 1, 3, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(12, 'Tecnología Avanzada', 'Seguimiento y análisis en tiempo real', 'Monitoreo completo de tu progreso con tecnología de vanguardia', 'fas fa-chart-line', '#f39c12', NULL, '[\"App M\\u00f3vil\",\"An\\u00e1lisis Detallado\",\"Sincronizaci\\u00f3n Cloud\",\"Reportes Personalizados\"]', '{\"accuracy\":\"99%\",\"users\":\"10000+\"}', 1, 4, '2025-06-30 04:06:37', '2025-06-30 04:06:37'),
(13, 'Rutinas Personalizadas', 'Entrenamientos inteligentes con IA', 'Entrenamientos diseñados específicamente para tus objetivos', 'fas fa-dumbbell', '#ff6b35', NULL, '[\"Videos HD Explicativos\",\"Seguimiento en Tiempo Real\",\"Ajustes Autom\\u00e1ticos IA\",\"Soporte 24\\/7\"]', '{\"exercises\":\"1000+\",\"success_rate\":\"95%\"}', 1, 1, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(14, 'Tienda Especializada', 'Los mejores suplementos del mercado', 'Productos certificados y de las marcas más reconocidas', 'fas fa-store', '#2c3e50', NULL, '[\"Productos Certificados\",\"Env\\u00edo Gratis\",\"Garant\\u00eda Total\",\"Asesor\\u00eda Nutricional\"]', '{\"products\":\"500+\",\"satisfaction\":\"98%\"}', 1, 2, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(15, 'Clases Grupales', 'Entrenamientos dinámicos y motivadores', 'Variedad de clases dirigidas por instructores certificados', 'fas fa-users', '#e74c3c', NULL, '[\"Instructores Certificados\",\"Horarios Flexibles\",\"Ambiente Motivador\",\"Todos los Niveles\"]', '{\"classes\":\"20+\",\"instructors\":\"15\"}', 1, 3, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(16, 'Tecnología Avanzada', 'Seguimiento y análisis en tiempo real', 'Monitoreo completo de tu progreso con tecnología de vanguardia', 'fas fa-chart-line', '#f39c12', NULL, '[\"App M\\u00f3vil\",\"An\\u00e1lisis Detallado\",\"Sincronizaci\\u00f3n Cloud\",\"Reportes Personalizados\"]', '{\"accuracy\":\"99%\",\"users\":\"10000+\"}', 1, 4, '2025-06-30 04:07:02', '2025-06-30 04:07:02'),
(17, 'Rutinas Personalizadas', 'Entrenamientos inteligentes con IA', 'Entrenamientos diseñados específicamente para tus objetivos', 'fas fa-dumbbell', '#ff6b35', NULL, '[\"Videos HD Explicativos\",\"Seguimiento en Tiempo Real\",\"Ajustes Autom\\u00e1ticos IA\",\"Soporte 24\\/7\"]', '{\"exercises\":\"1000+\",\"success_rate\":\"95%\"}', 1, 1, '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(18, 'Tienda Especializada', 'Los mejores suplementos del mercado', 'Productos certificados y de las marcas más reconocidas', 'fas fa-store', '#2c3e50', NULL, '[\"Productos Certificados\",\"Env\\u00edo Gratis\",\"Garant\\u00eda Total\",\"Asesor\\u00eda Nutricional\"]', '{\"products\":\"500+\",\"satisfaction\":\"98%\"}', 1, 2, '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(19, 'Clases Grupales', 'Entrenamientos dinámicos y motivadores', 'Variedad de clases dirigidas por instructores certificados', 'fas fa-users', '#e74c3c', NULL, '[\"Instructores Certificados\",\"Horarios Flexibles\",\"Ambiente Motivador\",\"Todos los Niveles\"]', '{\"classes\":\"20+\",\"instructors\":\"15\"}', 1, 3, '2025-06-30 04:07:36', '2025-06-30 04:07:36'),
(20, 'Tecnología Avanzada', 'Seguimiento y análisis en tiempo real', 'Monitoreo completo de tu progreso con tecnología de vanguardia', 'fas fa-chart-line', '#f39c12', NULL, '[\"App M\\u00f3vil\",\"An\\u00e1lisis Detallado\",\"Sincronizaci\\u00f3n Cloud\",\"Reportes Personalizados\"]', '{\"accuracy\":\"99%\",\"users\":\"10000+\"}', 1, 4, '2025-06-30 04:07:36', '2025-06-30 04:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workout_logs`
--

CREATE TABLE `workout_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `routine_id` int(11) DEFAULT NULL,
  `exercise_id` int(11) NOT NULL,
  `workout_date` date NOT NULL,
  `sets_completed` int(11) DEFAULT 0,
  `reps` varchar(20) DEFAULT NULL,
  `weight_used` varchar(20) DEFAULT NULL,
  `duration_seconds` int(11) DEFAULT NULL,
  `calories_burned` int(11) DEFAULT NULL,
  `rpe` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_variation_id` (`variation_id`);

--
-- Indexes for table `class_bookings`
--
ALTER TABLE `class_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_unique_booking` (`schedule_id`,`user_id`,`booking_date`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_booking_date` (`booking_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_class_bookings_date_status` (`booking_date`,`status`);

--
-- Indexes for table `class_reviews`
--
ALTER TABLE `class_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_unique_review` (`booking_id`),
  ADD KEY `idx_class_id` (`class_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_approved` (`is_approved`);

--
-- Indexes for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_class_id` (`class_id`),
  ADD KEY `idx_day_of_week` (`day_of_week`),
  ADD KEY `idx_start_time` (`start_time`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `cms_pages`
--
ALTER TABLE `cms_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_author_id` (`author_id`),
  ADD KEY `idx_published_at` (`published_at`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_code` (`code`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_valid_from` (`valid_from`),
  ADD KEY `idx_valid_until` (`valid_until`);

--
-- Indexes for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_coupon_id` (`coupon_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_difficulty_level` (`difficulty_level`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_exercises_category_difficulty_active` (`category_id`,`difficulty_level`,`is_active`);
ALTER TABLE `exercises` ADD FULLTEXT KEY `idx_search` (`name`,`description`,`instructions`);

--
-- Indexes for table `exercise_categories`
--
ALTER TABLE `exercise_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort_order` (`sort_order`);

--
-- Indexes for table `featured_products_config`
--
ALTER TABLE `featured_products_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_classes`
--
ALTER TABLE `group_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gym_id` (`gym_id`),
  ADD KEY `idx_instructor_id` (`instructor_id`),
  ADD KEY `idx_class_type` (`class_type`),
  ADD KEY `idx_difficulty_level` (`difficulty_level`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `gyms`
--
ALTER TABLE `gyms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gym_active` (`is_active`);

--
-- Indexes for table `landing_page_config`
--
ALTER TABLE `landing_page_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_name` (`section_name`),
  ADD UNIQUE KEY `unique_section` (`section_name`),
  ADD KEY `idx_landing_enabled` (`is_enabled`),
  ADD KEY `idx_landing_order` (`display_order`);

--
-- Indexes for table `media_files`
--
ALTER TABLE `media_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`),
  ADD KEY `idx_file_type` (`file_type`),
  ADD KEY `idx_mime_type` (`mime_type`),
  ADD KEY `idx_is_public` (`is_public`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_orders_user_status_created` (`user_id`,`status`,`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_variation_id` (`variation_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_slug` (`slug`),
  ADD UNIQUE KEY `idx_sku` (`sku`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_price` (`price`),
  ADD KEY `idx_stock_quantity` (`stock_quantity`),
  ADD KEY `idx_brand` (`brand`),
  ADD KEY `idx_avg_rating` (`avg_rating`),
  ADD KEY `idx_products_category_featured_active` (`category_id`,`is_featured`,`is_active`);
ALTER TABLE `products` ADD FULLTEXT KEY `idx_search` (`name`,`description`,`short_description`,`brand`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_slug` (`slug`),
  ADD KEY `idx_parent_id` (`parent_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort_order` (`sort_order`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_approved` (`is_approved`),
  ADD KEY `idx_verified_purchase` (`is_verified_purchase`);

--
-- Indexes for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_sku` (`sku`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `routines`
--
ALTER TABLE `routines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gym_id` (`gym_id`),
  ADD KEY `idx_instructor_id` (`instructor_id`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_objective` (`objective`),
  ADD KEY `idx_difficulty_level` (`difficulty_level`),
  ADD KEY `idx_template` (`is_template`),
  ADD KEY `idx_public` (`is_public`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_routines_template_public_active` (`is_template`,`is_public`,`is_active`);
ALTER TABLE `routines` ADD FULLTEXT KEY `idx_search` (`name`,`description`);

--
-- Indexes for table `routine_exercises`
--
ALTER TABLE `routine_exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_routine_id` (`routine_id`),
  ADD KEY `idx_exercise_id` (`exercise_id`),
  ADD KEY `idx_day_number` (`day_number`),
  ADD KEY `idx_order_index` (`order_index`),
  ADD KEY `idx_superset_group` (`superset_group`);

--
-- Indexes for table `security_tokens`
--
ALTER TABLE `security_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_token` (`token`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `special_offers`
--
ALTER TABLE `special_offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_offers_active` (`is_active`),
  ADD KEY `idx_offers_dates` (`start_date`,`end_date`),
  ADD KEY `idx_offers_order` (`display_order`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_setting_key` (`setting_key`),
  ADD KEY `idx_setting_group` (`setting_group`),
  ADD KEY `idx_is_public` (`is_public`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_testimonials_active` (`is_active`),
  ADD KEY `idx_testimonials_featured` (`is_featured`),
  ADD KEY `idx_testimonials_order` (`display_order`),
  ADD KEY `idx_testimonials_rating` (`rating`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_username` (`username`),
  ADD UNIQUE KEY `idx_email` (`email`),
  ADD KEY `idx_gym_id` (`gym_id`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_membership_expires` (`membership_expires`),
  ADD KEY `idx_users_gym_role_active` (`gym_id`,`role`,`is_active`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_resource_type` (`resource_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_routine_id` (`routine_id`),
  ADD KEY `idx_measurement_date` (`measurement_date`),
  ADD KEY `idx_user_progress_user_date` (`user_id`,`measurement_date`);

--
-- Indexes for table `why_choose_us`
--
ALTER TABLE `why_choose_us`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_features_active` (`is_active`),
  ADD KEY `idx_features_order` (`display_order`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_user_product` (`user_id`,`product_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `workout_logs`
--
ALTER TABLE `workout_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_routine_id` (`routine_id`),
  ADD KEY `idx_exercise_id` (`exercise_id`),
  ADD KEY `idx_workout_date` (`workout_date`),
  ADD KEY `idx_completed_at` (`completed_at`),
  ADD KEY `idx_workout_logs_user_date` (`user_id`,`workout_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_bookings`
--
ALTER TABLE `class_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_reviews`
--
ALTER TABLE `class_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cms_pages`
--
ALTER TABLE `cms_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `exercise_categories`
--
ALTER TABLE `exercise_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `featured_products_config`
--
ALTER TABLE `featured_products_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `group_classes`
--
ALTER TABLE `group_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gyms`
--
ALTER TABLE `gyms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `landing_page_config`
--
ALTER TABLE `landing_page_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `media_files`
--
ALTER TABLE `media_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variations`
--
ALTER TABLE `product_variations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `routines`
--
ALTER TABLE `routines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `routine_exercises`
--
ALTER TABLE `routine_exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `security_tokens`
--
ALTER TABLE `security_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `special_offers`
--
ALTER TABLE `special_offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `why_choose_us`
--
ALTER TABLE `why_choose_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workout_logs`
--
ALTER TABLE `workout_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_items_variation` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_bookings`
--
ALTER TABLE `class_bookings`
  ADD CONSTRAINT `fk_class_bookings_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_reviews`
--
ALTER TABLE `class_reviews`
  ADD CONSTRAINT `fk_class_reviews_booking` FOREIGN KEY (`booking_id`) REFERENCES `class_bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_reviews_class` FOREIGN KEY (`class_id`) REFERENCES `group_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD CONSTRAINT `fk_class_schedules_class` FOREIGN KEY (`class_id`) REFERENCES `group_classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cms_pages`
--
ALTER TABLE `cms_pages`
  ADD CONSTRAINT `fk_cms_pages_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD CONSTRAINT `fk_coupon_usage_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_coupon_usage_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_coupon_usage_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exercises`
--
ALTER TABLE `exercises`
  ADD CONSTRAINT `fk_exercises_category` FOREIGN KEY (`category_id`) REFERENCES `exercise_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_exercises_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `group_classes`
--
ALTER TABLE `group_classes`
  ADD CONSTRAINT `fk_group_classes_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_group_classes_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `media_files`
--
ALTER TABLE `media_files`
  ADD CONSTRAINT `fk_media_files_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_items_variation` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `fk_product_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `fk_product_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD CONSTRAINT `fk_product_variations_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `routines`
--
ALTER TABLE `routines`
  ADD CONSTRAINT `fk_routines_client` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_routines_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_routines_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `routine_exercises`
--
ALTER TABLE `routine_exercises`
  ADD CONSTRAINT `fk_routine_exercises_exercise` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_routine_exercises_routine` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `security_tokens`
--
ALTER TABLE `security_tokens`
  ADD CONSTRAINT `fk_security_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD CONSTRAINT `fk_user_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `fk_user_progress_routine` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `fk_wishlists_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wishlists_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_logs`
--
ALTER TABLE `workout_logs`
  ADD CONSTRAINT `fk_workout_logs_exercise` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_workout_logs_routine` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_workout_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
