-- =====================================================
-- SCHEMA COMPLETO DE BASE DE DATOS - STYLOFITNESS
-- =====================================================
-- Versión: 1.0
-- Fecha: Diciembre 2024
-- Descripción: Schema completo con todas las tablas, relaciones y datos de prueba
-- =====================================================

-- Configuración inicial
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS `stylofitness` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `stylofitness`;

-- =====================================================
-- TABLA: users (Usuarios del sistema)
-- =====================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `role` enum('admin','trainer','staff','client') NOT NULL DEFAULT 'client',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `specialties` text DEFAULT NULL COMMENT 'Para entrenadores',
  `certifications` text DEFAULT NULL COMMENT 'Para entrenadores',
  `hire_date` date DEFAULT NULL COMMENT 'Para staff y entrenadores',
  `membership_type` enum('basic','premium','vip') DEFAULT NULL COMMENT 'Para clientes',
  `membership_start` date DEFAULT NULL,
  `membership_end` date DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `fitness_goals` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_role` (`role`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: gyms (Gimnasios/Sucursales)
-- =====================================================
DROP TABLE IF EXISTS `gyms`;
CREATE TABLE `gyms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `opening_hours` json DEFAULT NULL,
  `amenities` json DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `status` enum('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_gym_manager` (`manager_id`),
  CONSTRAINT `fk_gym_manager` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: rooms (Salas/Espacios del gimnasio)
-- =====================================================
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gym_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('cardio','weights','functional','group_class','pool','sauna','other') NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `equipment_list` json DEFAULT NULL,
  `layout_data` json DEFAULT NULL COMMENT 'Datos del layout visual',
  `status` enum('available','occupied','maintenance','closed') NOT NULL DEFAULT 'available',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_room_gym` (`gym_id`),
  KEY `idx_room_type` (`type`),
  CONSTRAINT `fk_room_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: exercises (Ejercicios)
-- =====================================================
DROP TABLE IF EXISTS `exercises`;
CREATE TABLE `exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` enum('cardio','strength','flexibility','balance','sports','rehabilitation') NOT NULL,
  `muscle_groups` json DEFAULT NULL COMMENT 'Grupos musculares trabajados',
  `equipment_needed` json DEFAULT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `tips` text DEFAULT NULL,
  `warnings` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `duration_min` int(11) DEFAULT NULL COMMENT 'Duración mínima en segundos',
  `duration_max` int(11) DEFAULT NULL COMMENT 'Duración máxima en segundos',
  `calories_per_minute` decimal(4,2) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_public` boolean NOT NULL DEFAULT true,
  `status` enum('active','inactive','pending_review') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_exercise_creator` (`created_by`),
  KEY `idx_category` (`category`),
  KEY `idx_difficulty` (`difficulty_level`),
  CONSTRAINT `fk_exercise_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: routines (Rutinas de ejercicio)
-- =====================================================
DROP TABLE IF EXISTS `routines`;
CREATE TABLE `routines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL COMMENT 'Entrenador que creó la rutina',
  `assigned_to` int(11) DEFAULT NULL COMMENT 'Cliente asignado (NULL si es template)',
  `is_template` boolean NOT NULL DEFAULT false,
  `category` enum('strength','cardio','flexibility','mixed','rehabilitation','sports') NOT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') NOT NULL,
  `duration_weeks` int(11) DEFAULT NULL,
  `sessions_per_week` int(11) DEFAULT NULL,
  `estimated_duration_minutes` int(11) DEFAULT NULL,
  `goals` json DEFAULT NULL COMMENT 'Objetivos de la rutina',
  `notes` text DEFAULT NULL,
  `status` enum('draft','active','completed','archived') NOT NULL DEFAULT 'draft',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_routine_creator` (`created_by`),
  KEY `fk_routine_client` (`assigned_to`),
  KEY `idx_is_template` (`is_template`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_routine_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_routine_client` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: routine_exercises (Ejercicios en rutinas)
-- =====================================================
DROP TABLE IF EXISTS `routine_exercises`;
CREATE TABLE `routine_exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `routine_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL COMMENT 'Día de la semana (1-7)',
  `order_in_day` int(11) NOT NULL COMMENT 'Orden dentro del día',
  `sets` int(11) DEFAULT NULL,
  `reps` varchar(50) DEFAULT NULL COMMENT 'Puede ser número o rango',
  `weight` decimal(5,2) DEFAULT NULL,
  `duration_seconds` int(11) DEFAULT NULL,
  `rest_seconds` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_superset` boolean NOT NULL DEFAULT false,
  `superset_group` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_routine_exercise_routine` (`routine_id`),
  KEY `fk_routine_exercise_exercise` (`exercise_id`),
  KEY `idx_day_order` (`day_number`, `order_in_day`),
  CONSTRAINT `fk_routine_exercise_routine` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_routine_exercise_exercise` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: workout_logs (Registro de entrenamientos)
-- =====================================================
DROP TABLE IF EXISTS `workout_logs`;
CREATE TABLE `workout_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `routine_id` int(11) DEFAULT NULL,
  `workout_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `total_duration_minutes` int(11) DEFAULT NULL,
  `calories_burned` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL COMMENT 'Calificación del 1-5',
  `mood_before` enum('excellent','good','average','poor','terrible') DEFAULT NULL,
  `mood_after` enum('excellent','good','average','poor','terrible') DEFAULT NULL,
  `completed` boolean NOT NULL DEFAULT false,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_workout_user` (`user_id`),
  KEY `fk_workout_routine` (`routine_id`),
  KEY `idx_workout_date` (`workout_date`),
  CONSTRAINT `fk_workout_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_workout_routine` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: workout_exercise_logs (Registro detallado de ejercicios)
-- =====================================================
DROP TABLE IF EXISTS `workout_exercise_logs`;
CREATE TABLE `workout_exercise_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workout_log_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `set_number` int(11) NOT NULL,
  `reps_completed` int(11) DEFAULT NULL,
  `weight_used` decimal(5,2) DEFAULT NULL,
  `duration_seconds` int(11) DEFAULT NULL,
  `rest_seconds` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `completed` boolean NOT NULL DEFAULT false,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_exercise_log_workout` (`workout_log_id`),
  KEY `fk_exercise_log_exercise` (`exercise_id`),
  CONSTRAINT `fk_exercise_log_workout` FOREIGN KEY (`workout_log_id`) REFERENCES `workout_logs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_exercise_log_exercise` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: group_classes (Clases grupales)
-- =====================================================
DROP TABLE IF EXISTS `group_classes`;
CREATE TABLE `group_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `instructor_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `category` enum('yoga','pilates','spinning','zumba','crossfit','boxing','aqua','other') NOT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced','all_levels') NOT NULL,
  `max_participants` int(11) NOT NULL DEFAULT 20,
  `duration_minutes` int(11) NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `equipment_needed` json DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','cancelled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_class_instructor` (`instructor_id`),
  KEY `fk_class_room` (`room_id`),
  KEY `idx_category` (`category`),
  CONSTRAINT `fk_class_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: class_schedules (Horarios de clases)
-- =====================================================
DROP TABLE IF EXISTS `class_schedules`;
CREATE TABLE `class_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_recurring` boolean NOT NULL DEFAULT true,
  `status` enum('active','cancelled','rescheduled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_schedule_class` (`class_id`),
  KEY `idx_day_time` (`day_of_week`, `start_time`),
  CONSTRAINT `fk_schedule_class` FOREIGN KEY (`class_id`) REFERENCES `group_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: class_bookings (Reservas de clases)
-- =====================================================
DROP TABLE IF EXISTS `class_bookings`;
CREATE TABLE `class_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `status` enum('confirmed','cancelled','attended','no_show') NOT NULL DEFAULT 'confirmed',
  `payment_status` enum('pending','paid','refunded') NOT NULL DEFAULT 'pending',
  `booking_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cancellation_time` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_booking` (`user_id`, `schedule_id`, `booking_date`),
  KEY `fk_booking_user` (`user_id`),
  KEY `fk_booking_schedule` (`schedule_id`),
  KEY `idx_booking_date` (`booking_date`),
  CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_booking_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: product_categories (Categorías de productos)
-- =====================================================
DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_category_parent` (`parent_id`),
  KEY `idx_sort_order` (`sort_order`),
  CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: products (Productos de la tienda)
-- =====================================================
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `sku` varchar(50) UNIQUE DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 5,
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `warranty_months` int(11) DEFAULT NULL,
  `images` json DEFAULT NULL COMMENT 'Array de URLs de imágenes',
  `features` json DEFAULT NULL COMMENT 'Características del producto',
  `specifications` json DEFAULT NULL COMMENT 'Especificaciones técnicas',
  `is_featured` boolean NOT NULL DEFAULT false,
  `is_digital` boolean NOT NULL DEFAULT false,
  `requires_shipping` boolean NOT NULL DEFAULT true,
  `status` enum('active','inactive','out_of_stock','discontinued') NOT NULL DEFAULT 'active',
  `seo_title` varchar(200) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_product_category` (`category_id`),
  KEY `idx_sku` (`sku`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: orders (Órdenes de compra)
-- =====================================================
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) UNIQUE NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded','partial') NOT NULL DEFAULT 'pending',
  `payment_method` enum('credit_card','debit_card','paypal','bank_transfer','cash','other') DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `billing_address` json DEFAULT NULL,
  `shipping_address` json DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_order_user` (`user_id`),
  KEY `idx_order_number` (`order_number`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: order_items (Items de órdenes)
-- =====================================================
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `product_snapshot` json DEFAULT NULL COMMENT 'Snapshot del producto al momento de la compra',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_item_order` (`order_id`),
  KEY `fk_item_product` (`product_id`),
  CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: shopping_cart (Carrito de compras)
-- =====================================================
DROP TABLE IF EXISTS `shopping_cart`;
CREATE TABLE `shopping_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_item` (`user_id`, `product_id`),
  KEY `fk_cart_user` (`user_id`),
  KEY `fk_cart_product` (`product_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: wishlist (Lista de deseos)
-- =====================================================
DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_wishlist_item` (`user_id`, `product_id`),
  KEY `fk_wishlist_user` (`user_id`),
  KEY `fk_wishlist_product` (`product_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: special_offers (Ofertas especiales)
-- =====================================================
DROP TABLE IF EXISTS `special_offers`;
CREATE TABLE `special_offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed_amount','buy_x_get_y') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_purchase_amount` decimal(10,2) DEFAULT NULL,
  `max_discount_amount` decimal(10,2) DEFAULT NULL,
  `applicable_to` enum('all_products','specific_products','specific_categories') NOT NULL DEFAULT 'all_products',
  `product_ids` json DEFAULT NULL,
  `category_ids` json DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` boolean NOT NULL DEFAULT true,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_coupon_code` (`coupon_code`),
  KEY `idx_dates` (`start_date`, `end_date`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: testimonials (Testimonios)
-- =====================================================
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `title` varchar(200) DEFAULT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` boolean NOT NULL DEFAULT false,
  `is_approved` boolean NOT NULL DEFAULT false,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_testimonial_user` (`user_id`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_approved` (`is_approved`),
  CONSTRAINT `fk_testimonial_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: why_choose_us (Por qué elegirnos)
-- =====================================================
DROP TABLE IF EXISTS `why_choose_us`;
CREATE TABLE `why_choose_us` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` boolean NOT NULL DEFAULT true,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`display_order`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: landing_page_config (Configuración de landing page)
-- =====================================================
DROP TABLE IF EXISTS `landing_page_config`;
CREATE TABLE `landing_page_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `data_type` enum('text','number','boolean','json','image','url') NOT NULL DEFAULT 'text',
  `description` text DEFAULT NULL,
  `is_active` boolean NOT NULL DEFAULT true,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_section_key` (`section`, `key_name`),
  KEY `idx_section` (`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: notifications (Notificaciones)
-- =====================================================
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('info','success','warning','error','reminder') NOT NULL DEFAULT 'info',
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `action_text` varchar(100) DEFAULT NULL,
  `is_read` boolean NOT NULL DEFAULT false,
  `is_sent` boolean NOT NULL DEFAULT false,
  `send_email` boolean NOT NULL DEFAULT false,
  `send_push` boolean NOT NULL DEFAULT false,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_notification_user` (`user_id`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_scheduled` (`scheduled_at`),
  CONSTRAINT `fk_notification_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: user_progress (Progreso de usuarios)
-- =====================================================
DROP TABLE IF EXISTS `user_progress`;
CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `measurement_date` date NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `body_fat_percentage` decimal(4,1) DEFAULT NULL,
  `muscle_mass` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(4,1) DEFAULT NULL,
  `chest` decimal(5,2) DEFAULT NULL,
  `waist` decimal(5,2) DEFAULT NULL,
  `hips` decimal(5,2) DEFAULT NULL,
  `arms` decimal(5,2) DEFAULT NULL,
  `thighs` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `progress_photos` json DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Entrenador que registró',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_progress_user` (`user_id`),
  KEY `fk_progress_creator` (`created_by`),
  KEY `idx_measurement_date` (`measurement_date`),
  CONSTRAINT `fk_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_progress_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: check_ins (Check-ins al gimnasio)
-- =====================================================
DROP TABLE IF EXISTS `check_ins`;
CREATE TABLE `check_ins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gym_id` int(11) NOT NULL,
  `check_in_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `check_out_time` timestamp NULL DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `method` enum('card','app','manual','qr_code') NOT NULL DEFAULT 'card',
  `staff_id` int(11) DEFAULT NULL COMMENT 'Staff que registró manualmente',
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_checkin_user` (`user_id`),
  KEY `fk_checkin_gym` (`gym_id`),
  KEY `fk_checkin_staff` (`staff_id`),
  KEY `idx_checkin_time` (`check_in_time`),
  CONSTRAINT `fk_checkin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_checkin_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_checkin_staff` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: messages (Sistema de mensajería)
-- =====================================================
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `message_type` enum('text','image','file','system') NOT NULL DEFAULT 'text',
  `attachment_url` varchar(500) DEFAULT NULL,
  `is_read` boolean NOT NULL DEFAULT false,
  `read_at` timestamp NULL DEFAULT NULL,
  `parent_message_id` int(11) DEFAULT NULL COMMENT 'Para respuestas',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_message_sender` (`sender_id`),
  KEY `fk_message_receiver` (`receiver_id`),
  KEY `fk_message_parent` (`parent_message_id`),
  KEY `idx_is_read` (`is_read`),
  CONSTRAINT `fk_message_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_message_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_message_parent` FOREIGN KEY (`parent_message_id`) REFERENCES `messages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERTAR DATOS DE PRUEBA
-- =====================================================

-- Insertar gimnasios
INSERT INTO `gyms` (`name`, `address`, `city`, `state`, `postal_code`, `phone`, `email`, `opening_hours`, `amenities`, `capacity`) VALUES
('StyloFitness Centro', 'Av. Principal 123', 'Ciudad Central', 'Estado Central', '12345', '+1234567890', 'centro@stylofitness.com', 
'{"monday": "06:00-22:00", "tuesday": "06:00-22:00", "wednesday": "06:00-22:00", "thursday": "06:00-22:00", "friday": "06:00-22:00", "saturday": "08:00-20:00", "sunday": "08:00-18:00"}',
'["parking", "lockers", "showers", "wifi", "air_conditioning", "music_system"]', 200),
('StyloFitness Norte', 'Calle Norte 456', 'Ciudad Norte', 'Estado Norte', '67890', '+1234567891', 'norte@stylofitness.com',
'{"monday": "05:30-23:00", "tuesday": "05:30-23:00", "wednesday": "05:30-23:00", "thursday": "05:30-23:00", "friday": "05:30-23:00", "saturday": "07:00-21:00", "sunday": "07:00-19:00"}',
'["parking", "lockers", "showers", "wifi", "pool", "sauna", "juice_bar"]', 300);

-- Insertar usuarios de prueba
INSERT INTO `users` (`username`, `email`, `password`, `first_name`, `last_name`, `phone`, `date_of_birth`, `gender`, `role`, `bio`, `specialties`, `certifications`, `hire_date`, `membership_type`, `membership_start`, `membership_end`, `emergency_contact_name`, `emergency_contact_phone`, `fitness_goals`) VALUES
-- Administradores
('admin', 'admin@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Administrador', '+1234567890', '1985-01-15', 'male', 'admin', 'Administrador principal del sistema', NULL, NULL, '2020-01-01', NULL, NULL, NULL, NULL, NULL, NULL),
('manager', 'manager@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Gerente', '+1234567891', '1988-03-20', 'female', 'admin', 'Gerente general de operaciones', NULL, NULL, '2020-02-01', NULL, NULL, NULL, NULL, NULL, NULL),

-- Entrenadores
('trainer1', 'trainer1@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Miguel', 'Fitness', '+1234567892', '1990-05-10', 'male', 'trainer', 'Entrenador especializado en fuerza y acondicionamiento', '["strength_training", "powerlifting", "functional_training"]', '["NASM-CPT", "CSCS", "Functional Movement Screen"]', '2021-01-15', NULL, NULL, NULL, NULL, NULL, NULL),
('trainer2', 'trainer2@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sofia', 'Cardio', '+1234567893', '1992-08-25', 'female', 'trainer', 'Entrenadora especializada en cardio y pérdida de peso', '["cardio_training", "weight_loss", "group_fitness"]', '["ACE-CPT", "Spinning Instructor", "Zumba Instructor"]', '2021-03-01', NULL, NULL, NULL, NULL, NULL, NULL),
('trainer3', 'trainer3@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Roberto', 'Yoga', '+1234567894', '1987-12-05', 'male', 'trainer', 'Instructor de yoga y flexibilidad', '["yoga", "pilates", "flexibility", "meditation"]', '["RYT-200", "Pilates Instructor", "Meditation Teacher"]', '2021-06-01', NULL, NULL, NULL, NULL, NULL, NULL),

-- Staff
('staff1', 'staff1@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura', 'Recepción', '+1234567895', '1995-04-18', 'female', 'staff', 'Personal de recepción y atención al cliente', NULL, NULL, '2022-01-10', NULL, NULL, NULL, NULL, NULL, NULL),
('staff2', 'staff2@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Mantenimiento', '+1234567896', '1989-11-30', 'male', 'staff', 'Encargado de mantenimiento y limpieza', NULL, NULL, '2022-02-15', NULL, NULL, NULL, NULL, NULL, NULL),

-- Clientes
('client1', 'client1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Pérez', '+1234567897', '1985-07-12', 'male', 'client', NULL, NULL, NULL, NULL, 'premium', '2024-01-01', '2024-12-31', 'María Pérez', '+1234567898', '["muscle_gain", "strength_improvement"]'),
('client2', 'client2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'González', '+1234567899', '1990-02-28', 'female', 'client', NULL, NULL, NULL, NULL, 'basic', '2024-02-01', '2024-12-31', 'Carlos González', '+1234567900', '["weight_loss", "cardiovascular_health"]'),
('client3', 'client3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'Martínez', '+1234567901', '1988-09-15', 'male', 'client', NULL, NULL, NULL, NULL, 'vip', '2024-01-15', '2024-12-31', 'Ana Martínez', '+1234567902', '["general_fitness", "flexibility"]'),
('client4', 'client4@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carmen', 'López', '+1234567903', '1993-06-08', 'female', 'client', NULL, NULL, NULL, NULL, 'premium', '2024-03-01', '2024-12-31', 'Luis López', '+1234567904', '["muscle_toning", "stress_relief"]'),
('client5', 'client5@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Antonio', 'Rodríguez', '+1234567905', '1982-12-22', 'male', 'client', NULL, NULL, NULL, NULL, 'basic', '2024-04-01', '2024-12-31', 'Isabel Rodríguez', '+1234567906', '["weight_loss", "endurance"]');

-- Actualizar manager_id en gyms
UPDATE `gyms` SET `manager_id` = 2 WHERE `id` = 1;
UPDATE `gyms` SET `manager_id` = 1 WHERE `id` = 2;

-- Insertar salas
INSERT INTO `rooms` (`gym_id`, `name`, `type`, `capacity`, `equipment_list`, `description`) VALUES
(1, 'Sala de Pesas Principal', 'weights', 50, '["dumbbells", "barbells", "weight_machines", "benches", "squat_racks"]', 'Sala principal de entrenamiento con pesas'),
(1, 'Área Cardio', 'cardio', 30, '["treadmills", "ellipticals", "stationary_bikes", "rowing_machines"]', 'Zona dedicada al entrenamiento cardiovascular'),
(1, 'Sala Funcional', 'functional', 25, '["kettlebells", "medicine_balls", "resistance_bands", "suspension_trainers", "battle_ropes"]', 'Espacio para entrenamiento funcional'),
(1, 'Estudio de Clases', 'group_class', 40, '["yoga_mats", "sound_system", "mirrors", "blocks", "straps"]', 'Estudio para clases grupales'),
(2, 'Gimnasio Principal', 'weights', 80, '["dumbbells", "barbells", "weight_machines", "benches", "squat_racks", "cable_machines"]', 'Gimnasio principal con equipamiento completo'),
(2, 'Piscina', 'pool', 20, '["pool_lanes", "pool_equipment", "life_jackets"]', 'Piscina para natación y aqua fitness'),
(2, 'Sauna', 'sauna', 10, '["sauna_equipment", "towels", "water_station"]', 'Sauna finlandesa para relajación');

-- Insertar ejercicios
INSERT INTO `exercises` (`name`, `category`, `muscle_groups`, `equipment_needed`, `difficulty_level`, `description`, `instructions`, `created_by`) VALUES
('Press de Banca', 'strength', '["chest", "triceps", "shoulders"]', '["barbell", "bench"]', 'intermediate', 'Ejercicio fundamental para el desarrollo del pecho', 'Acuéstate en el banco, agarra la barra con las manos separadas al ancho de los hombros, baja controladamente hasta el pecho y empuja hacia arriba.', 3),
('Sentadillas', 'strength', '["quadriceps", "glutes", "hamstrings"]', '["barbell", "squat_rack"]', 'beginner', 'Ejercicio básico para piernas y glúteos', 'Coloca la barra en los hombros, separa los pies al ancho de los hombros, baja como si te fueras a sentar y regresa a la posición inicial.', 3),
('Peso Muerto', 'strength', '["hamstrings", "glutes", "lower_back", "traps"]', '["barbell"]', 'advanced', 'Ejercicio compuesto para la cadena posterior', 'Con los pies separados al ancho de las caderas, agarra la barra y levántala manteniendo la espalda recta.', 3),
('Flexiones', 'strength', '["chest", "triceps", "shoulders"]', '[]', 'beginner', 'Ejercicio de peso corporal para el tren superior', 'En posición de plancha, baja el cuerpo hasta casi tocar el suelo y empuja hacia arriba.', 3),
('Plancha', 'strength', '["core", "shoulders"]', '[]', 'beginner', 'Ejercicio isométrico para el core', 'Mantén el cuerpo recto apoyado en antebrazos y pies, contrae el abdomen.', 3),
('Burpees', 'cardio', '["full_body"]', '[]', 'intermediate', 'Ejercicio cardiovascular de cuerpo completo', 'Desde posición de pie, baja a cuclillas, salta hacia atrás a plancha, haz una flexión, salta hacia adelante y salta hacia arriba.', 4),
('Mountain Climbers', 'cardio', '["core", "shoulders", "legs"]', '[]', 'intermediate', 'Ejercicio cardiovascular dinámico', 'En posición de plancha, alterna llevando las rodillas hacia el pecho rápidamente.', 4),
('Yoga Flow Básico', 'flexibility', '["full_body"]', '["yoga_mat"]', 'beginner', 'Secuencia básica de yoga', 'Fluye entre posturas básicas de yoga manteniendo la respiración controlada.', 5),
('Estiramiento de Isquiotibiales', 'flexibility', '["hamstrings"]', '[]', 'beginner', 'Estiramiento para la parte posterior del muslo', 'Sentado, extiende una pierna y alcanza los dedos del pie manteniendo la espalda recta.', 5),
('Caminata en Cinta', 'cardio', '["legs", "cardiovascular"]', '["treadmill"]', 'beginner', 'Ejercicio cardiovascular de bajo impacto', 'Camina a ritmo constante en la cinta manteniendo una postura erguida.', 4);

-- Insertar rutinas template
INSERT INTO `routines` (`name`, `description`, `created_by`, `is_template`, `category`, `difficulty_level`, `duration_weeks`, `sessions_per_week`, `estimated_duration_minutes`, `goals`) VALUES
('Rutina Principiante Fuerza', 'Rutina básica para principiantes enfocada en fuerza', 3, true, 'strength', 'beginner', 8, 3, 60, '["muscle_gain", "strength_improvement", "technique_learning"]'),
('Cardio Intenso', 'Rutina cardiovascular de alta intensidad', 4, true, 'cardio', 'intermediate', 6, 4, 45, '["weight_loss", "cardiovascular_health", "endurance"]'),
('Flexibilidad y Movilidad', 'Rutina enfocada en flexibilidad y movilidad', 5, true, 'flexibility', 'beginner', 4, 3, 30, '["flexibility", "stress_relief", "injury_prevention"]'),
('Rutina Completa Intermedio', 'Rutina mixta para nivel intermedio', 3, true, 'mixed', 'intermediate', 12, 4, 75, '["general_fitness", "muscle_toning", "strength_improvement"]');

-- Insertar ejercicios en rutinas
INSERT INTO `routine_exercises` (`routine_id`, `exercise_id`, `day_number`, `order_in_day`, `sets`, `reps`, `weight`, `rest_seconds`) VALUES
-- Rutina Principiante Fuerza (Día 1)
(1, 2, 1, 1, 3, '8-12', 20.00, 90), -- Sentadillas
(1, 1, 1, 2, 3, '8-12', 40.00, 90), -- Press de Banca
(1, 4, 1, 3, 3, '8-12', NULL, 60), -- Flexiones
(1, 5, 1, 4, 3, '30 segundos', NULL, 60), -- Plancha
-- Rutina Principiante Fuerza (Día 3)
(1, 3, 3, 1, 3, '5-8', 60.00, 120), -- Peso Muerto
(1, 2, 3, 2, 3, '8-12', 25.00, 90), -- Sentadillas
(1, 4, 3, 3, 3, '10-15', NULL, 60), -- Flexiones
-- Rutina Principiante Fuerza (Día 5)
(1, 1, 5, 1, 3, '8-12', 45.00, 90), -- Press de Banca
(1, 2, 5, 2, 3, '8-12', 30.00, 90), -- Sentadillas
(1, 5, 5, 3, 3, '45 segundos', NULL, 60), -- Plancha

-- Rutina Cardio Intenso
(2, 6, 1, 1, 4, '10 reps', NULL, 30), -- Burpees
(2, 7, 1, 2, 4, '30 segundos', NULL, 30), -- Mountain Climbers
(2, 10, 1, 3, 1, '20 minutos', NULL, 0), -- Caminata en Cinta

-- Rutina Flexibilidad
(3, 8, 1, 1, 1, '20 minutos', NULL, 0), -- Yoga Flow
(3, 9, 1, 2, 3, '30 segundos', NULL, 30); -- Estiramiento Isquiotibiales

-- Asignar rutinas a clientes
INSERT INTO `routines` (`name`, `description`, `created_by`, `assigned_to`, `is_template`, `category`, `difficulty_level`, `duration_weeks`, `sessions_per_week`, `estimated_duration_minutes`, `goals`, `status`, `start_date`, `end_date`) VALUES
('Rutina Juan - Ganancia Muscular', 'Rutina personalizada para Juan enfocada en ganancia muscular', 3, 8, false, 'strength', 'intermediate', 12, 4, 75, '["muscle_gain", "strength_improvement"]', 'active', '2024-01-15', '2024-04-15'),
('Rutina María - Pérdida de Peso', 'Rutina personalizada para María enfocada en pérdida de peso', 4, 9, false, 'cardio', 'beginner', 8, 5, 45, '["weight_loss", "cardiovascular_health"]', 'active', '2024-02-01', '2024-04-01'),
('Rutina Pedro - Flexibilidad', 'Rutina personalizada para Pedro enfocada en flexibilidad', 5, 10, false, 'flexibility', 'beginner', 6, 3, 30, '["flexibility", "stress_relief"]', 'active', '2024-01-20', '2024-03-20');

-- Insertar logs de entrenamientos
INSERT INTO `workout_logs` (`user_id`, `routine_id`, `workout_date`, `start_time`, `end_time`, `total_duration_minutes`, `calories_burned`, `notes`, `rating`, `mood_before`, `mood_after`, `completed`) VALUES
(8, 5, '2024-12-01', '07:00:00', '08:15:00', 75, 350, 'Buen entrenamiento, me sentí fuerte', 4, 'good', 'excellent', true),
(8, 5, '2024-12-03', '07:00:00', '08:10:00', 70, 320, 'Un poco cansado pero completé todo', 3, 'average', 'good', true),
(9, 6, '2024-12-02', '18:00:00', '18:45:00', 45, 280, 'Excelente sesión de cardio', 5, 'good', 'excellent', true),
(9, 6, '2024-12-04', '18:00:00', '18:40:00', 40, 260, 'Me costó un poco más hoy', 3, 'poor', 'good', true),
(10, 7, '2024-12-01', '19:00:00', '19:30:00', 30, 150, 'Relajante sesión de yoga', 4, 'average', 'excellent', true);

-- Insertar clases grupales
INSERT INTO `group_classes` (`name`, `description`, `instructor_id`, `room_id`, `category`, `difficulty_level`, `max_participants`, `duration_minutes`, `price`) VALUES
('Yoga Matutino', 'Clase de yoga para comenzar el día con energía', 5, 4, 'yoga', 'all_levels', 20, 60, 15.00),
('Spinning Intenso', 'Clase de spinning de alta intensidad', 4, 2, 'spinning', 'intermediate', 15, 45, 20.00),
('Zumba Fitness', 'Baile fitness divertido y energético', 4, 4, 'zumba', 'all_levels', 25, 50, 18.00),
('CrossFit Principiantes', 'Introducción al CrossFit para principiantes', 3, 3, 'crossfit', 'beginner', 12, 60, 25.00),
('Aqua Fitness', 'Ejercicios en el agua de bajo impacto', 4, 6, 'aqua', 'all_levels', 15, 45, 22.00);

-- Insertar horarios de clases
INSERT INTO `class_schedules` (`class_id`, `day_of_week`, `start_time`, `end_time`, `start_date`, `is_recurring`) VALUES
(1, 'monday', '07:00:00', '08:00:00', '2024-01-01', true),
(1, 'wednesday', '07:00:00', '08:00:00', '2024-01-01', true),
(1, 'friday', '07:00:00', '08:00:00', '2024-01-01', true),
(2, 'tuesday', '18:00:00', '18:45:00', '2024-01-01', true),
(2, 'thursday', '18:00:00', '18:45:00', '2024-01-01', true),
(2, 'saturday', '09:00:00', '09:45:00', '2024-01-01', true),
(3, 'monday', '19:00:00', '19:50:00', '2024-01-01', true),
(3, 'wednesday', '19:00:00', '19:50:00', '2024-01-01', true),
(4, 'tuesday', '17:00:00', '18:00:00', '2024-01-01', true),
(4, 'thursday', '17:00:00', '18:00:00', '2024-01-01', true),
(5, 'monday', '10:00:00', '10:45:00', '2024-01-01', true),
(5, 'friday', '10:00:00', '10:45:00', '2024-01-01', true);

-- Insertar reservas de clases
INSERT INTO `class_bookings` (`user_id`, `schedule_id`, `booking_date`, `status`, `payment_status`) VALUES
(8, 1, '2024-12-09', 'confirmed', 'paid'),
(8, 2, '2024-12-11', 'confirmed', 'paid'),
(9, 4, '2024-12-10', 'confirmed', 'paid'),
(9, 5, '2024-12-12', 'confirmed', 'paid'),
(10, 1, '2024-12-09', 'confirmed', 'paid'),
(10, 7, '2024-12-09', 'confirmed', 'paid'),
(11, 3, '2024-12-10', 'confirmed', 'paid'),
(12, 6, '2024-12-14', 'confirmed', 'paid');

-- Insertar categorías de productos
INSERT INTO `product_categories` (`name`, `description`, `sort_order`) VALUES
('Suplementos', 'Suplementos nutricionales y proteínas', 1),
('Ropa Deportiva', 'Ropa y accesorios para entrenar', 2),
('Equipamiento', 'Equipos y accesorios de entrenamiento', 3),
('Accesorios', 'Accesorios diversos para fitness', 4),
('Nutrición', 'Productos de nutrición y alimentación', 5);

-- Insertar subcategorías
INSERT INTO `product_categories` (`name`, `description`, `parent_id`, `sort_order`) VALUES
('Proteínas', 'Proteínas en polvo y barras', 1, 1),
('Pre-entreno', 'Suplementos pre-entreno', 1, 2),
('Vitaminas', 'Vitaminas y minerales', 1, 3),
('Camisetas', 'Camisetas deportivas', 2, 1),
('Pantalones', 'Pantalones y shorts deportivos', 2, 2),
('Calzado', 'Zapatillas deportivas', 2, 3),
('Pesas', 'Mancuernas y pesas', 3, 1),
('Máquinas', 'Máquinas de ejercicio', 3, 2);

-- Insertar productos
INSERT INTO `products` (`name`, `description`, `category_id`, `sku`, `price`, `sale_price`, `stock_quantity`, `brand`, `images`, `is_featured`) VALUES
('Proteína Whey Premium 2kg', 'Proteína de suero de alta calidad con 25g de proteína por porción', 6, 'PROT-WHY-2KG', 89.99, 79.99, 50, 'StyloNutrition', '["whey-protein-1.jpg", "whey-protein-2.jpg"]', true),
('Pre-entreno Energía Máxima', 'Fórmula avanzada para máximo rendimiento', 7, 'PRE-ENE-500G', 45.99, NULL, 30, 'StyloNutrition', '["pre-workout-1.jpg"]', true),
('Camiseta Deportiva Hombre', 'Camiseta transpirable de alta tecnología', 9, 'CAM-HOM-001', 29.99, 24.99, 100, 'StyloWear', '["shirt-men-1.jpg", "shirt-men-2.jpg"]', false),
('Leggings Mujer Premium', 'Leggings de compresión para máximo confort', 10, 'LEG-MUJ-001', 49.99, NULL, 75, 'StyloWear', '["leggings-1.jpg", "leggings-2.jpg"]', true),
('Mancuernas Ajustables 20kg', 'Set de mancuernas ajustables de 5 a 20kg', 12, 'MAN-ADJ-20KG', 199.99, 179.99, 15, 'StyloEquip', '["dumbbells-1.jpg", "dumbbells-2.jpg"]', true),
('Zapatillas Running Pro', 'Zapatillas profesionales para running', 11, 'ZAP-RUN-PRO', 129.99, NULL, 40, 'StyloShoes', '["shoes-running-1.jpg"]', false),
('Multivitamínico Completo', 'Complejo vitamínico con 30 vitaminas y minerales', 8, 'VIT-COMP-90', 24.99, NULL, 80, 'StyloNutrition', '["vitamins-1.jpg"]', false),
('Esterilla Yoga Premium', 'Esterilla antideslizante de 6mm de grosor', 4, 'EST-YOGA-6MM', 39.99, 34.99, 60, 'StyloEquip', '["yoga-mat-1.jpg", "yoga-mat-2.jpg"]', false);

-- Insertar órdenes
INSERT INTO `orders` (`user_id`, `order_number`, `status`, `payment_status`, `payment_method`, `subtotal`, `tax_amount`, `shipping_amount`, `total_amount`, `billing_address`, `shipping_address`) VALUES
(8, 'ORD-2024-001', 'delivered', 'paid', 'credit_card', 169.98, 13.60, 10.00, 193.58, 
'{"name": "Juan Pérez", "address": "Calle Principal 123", "city": "Ciudad Central", "postal_code": "12345", "phone": "+1234567897"}',
'{"name": "Juan Pérez", "address": "Calle Principal 123", "city": "Ciudad Central", "postal_code": "12345", "phone": "+1234567897"}'),
(9, 'ORD-2024-002', 'shipped', 'paid', 'paypal', 74.98, 6.00, 8.00, 88.98,
'{"name": "María González", "address": "Av. Norte 456", "city": "Ciudad Norte", "postal_code": "67890", "phone": "+1234567899"}',
'{"name": "María González", "address": "Av. Norte 456", "city": "Ciudad Norte", "postal_code": "67890", "phone": "+1234567899"}');

-- Insertar items de órdenes
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 1, 2, 79.99, 159.98), -- 2 Proteínas
(1, 8, 1, 34.99, 34.99),  -- 1 Esterilla
(2, 3, 2, 24.99, 49.98),  -- 2 Camisetas
(2, 4, 1, 49.99, 49.99);  -- 1 Leggings

-- Insertar items en carrito
INSERT INTO `shopping_cart` (`user_id`, `product_id`, `quantity`) VALUES
(10, 2, 1), -- Pedro tiene pre-entreno en carrito
(10, 6, 1), -- Pedro tiene zapatillas en carrito
(11, 1, 1), -- Carmen tiene proteína en carrito
(12, 5, 1); -- Antonio tiene mancuernas en carrito

-- Insertar wishlist
INSERT INTO `wishlist` (`user_id`, `product_id`) VALUES
(8, 5), -- Juan quiere mancuernas
(8, 6), -- Juan quiere zapatillas
(9, 2), -- María quiere pre-entreno
(10, 1), -- Pedro quiere proteína
(11, 4); -- Carmen quiere leggings

-- Insertar ofertas especiales
INSERT INTO `special_offers` (`title`, `description`, `discount_type`, `discount_value`, `coupon_code`, `start_date`, `end_date`) VALUES
('Descuento Navidad', '20% de descuento en todos los suplementos', 'percentage', 20.00, 'NAVIDAD20', '2024-12-01 00:00:00', '2024-12-31 23:59:59'),
('Oferta Año Nuevo', '$50 de descuento en compras mayores a $200', 'fixed_amount', 50.00, 'NUEVO50', '2024-12-15 00:00:00', '2025-01-15 23:59:59'),
('2x1 en Ropa Deportiva', 'Compra 2 prendas y llévate la segunda gratis', 'buy_x_get_y', 100.00, '2X1ROPA', '2024-12-10 00:00:00', '2024-12-20 23:59:59');

-- Insertar testimonios
INSERT INTO `testimonials` (`user_id`, `name`, `email`, `rating`, `title`, `content`, `is_featured`, `is_approved`) VALUES
(8, 'Juan Pérez', 'client1@example.com', 5, 'Excelente gimnasio', 'He estado entrenando aquí por 6 meses y los resultados han sido increíbles. Los entrenadores son muy profesionales y el equipamiento es de primera calidad.', true, true),
(9, 'María González', 'client2@example.com', 5, 'Ambiente motivador', 'Me encanta la variedad de clases grupales. El ambiente es muy motivador y he logrado mis objetivos de pérdida de peso.', true, true),
(10, 'Pedro Martínez', 'client3@example.com', 4, 'Muy recomendado', 'Las instalaciones son excelentes y el personal siempre está dispuesto a ayudar. Definitivamente recomiendo StyloFitness.', false, true),
NULL, 'Ana Rodríguez', 'ana@example.com', 5, 'Cambió mi vida', 'StyloFitness cambió completamente mi estilo de vida. Ahora me siento más fuerte y saludable que nunca.', true, true);

-- Insertar características "Por qué elegirnos"
INSERT INTO `why_choose_us` (`title`, `description`, `icon`, `display_order`) VALUES
('Entrenadores Certificados', 'Nuestro equipo está formado por entrenadores certificados con amplia experiencia en fitness y nutrición.', 'fas fa-user-graduate', 1),
('Equipamiento de Última Generación', 'Contamos con las máquinas y equipos más modernos del mercado para garantizar entrenamientos efectivos.', 'fas fa-dumbbell', 2),
('Horarios Flexibles', 'Abrimos desde muy temprano hasta muy tarde para adaptarnos a tu horario y estilo de vida.', 'fas fa-clock', 3),
('Ambiente Motivador', 'Creamos un ambiente positivo y motivador donde te sentirás cómodo alcanzando tus objetivos.', 'fas fa-heart', 4),
('Planes Personalizados', 'Diseñamos rutinas y planes nutricionales personalizados según tus objetivos específicos.', 'fas fa-chart-line', 5),
('Comunidad Activa', 'Únete a nuestra comunidad de miembros activos que se apoyan mutuamente en su journey fitness.', 'fas fa-users', 6);

-- Insertar configuración de landing page
INSERT INTO `landing_page_config` (`section`, `key_name`, `value`, `data_type`, `description`) VALUES
('hero', 'title', 'Transforma Tu Cuerpo, Transforma Tu Vida', 'text', 'Título principal del hero'),
('hero', 'subtitle', 'Únete a StyloFitness y descubre el mejor gimnasio de la ciudad', 'text', 'Subtítulo del hero'),
('hero', 'cta_text', 'Comienza Hoy', 'text', 'Texto del botón principal'),
('hero', 'background_image', 'hero-bg.jpg', 'image', 'Imagen de fondo del hero'),
('stats', 'members_count', '2500', 'number', 'Número de miembros activos'),
('stats', 'trainers_count', '25', 'number', 'Número de entrenadores'),
('stats', 'classes_count', '50', 'number', 'Número de clases semanales'),
('stats', 'experience_years', '10', 'number', 'Años de experiencia'),
('contact', 'phone', '+1234567890', 'text', 'Teléfono de contacto'),
('contact', 'email', 'info@stylofitness.com', 'text', 'Email de contacto'),
('contact', 'address', 'Av. Principal 123, Ciudad Central', 'text', 'Dirección principal'),
('social', 'facebook', 'https://facebook.com/stylofitness', 'url', 'URL de Facebook'),
('social', 'instagram', 'https://instagram.com/stylofitness', 'url', 'URL de Instagram'),
('social', 'youtube', 'https://youtube.com/stylofitness', 'url', 'URL de YouTube');

-- Insertar notificaciones
INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `action_url`, `send_email`) VALUES
(8, 'reminder', 'Entrenamiento Programado', 'Tienes un entrenamiento programado para hoy a las 7:00 AM', '/routines/workout', true),
(9, 'success', 'Objetivo Alcanzado', '¡Felicidades! Has completado 10 entrenamientos este mes', '/progress', false),
(10, 'info', 'Nueva Clase Disponible', 'Se ha agregado una nueva clase de Pilates los sábados', '/classes', false),
(11, 'warning', 'Membresía por Vencer', 'Tu membresía vence en 7 días. Renueva para continuar disfrutando', '/membership', true),
(12, 'info', 'Producto en Oferta', 'La proteína que tienes en tu wishlist está en oferta 20% OFF', '/store/products/1', false);

-- Insertar progreso de usuarios
INSERT INTO `user_progress` (`user_id`, `measurement_date`, `weight`, `height`, `body_fat_percentage`, `muscle_mass`, `bmi`, `chest`, `waist`, `hips`, `arms`, `thighs`, `created_by`) VALUES
(8, '2024-01-15', 80.5, 175.0, 18.5, 65.2, 26.3, 102.0, 85.0, 95.0, 35.0, 58.0, 3),
(8, '2024-02-15', 79.8, 175.0, 17.8, 66.1, 26.1, 103.0, 83.0, 94.0, 35.5, 58.5, 3),
(8, '2024-03-15', 79.2, 175.0, 17.2, 67.0, 25.9, 104.0, 81.0, 93.0, 36.0, 59.0, 3),
(9, '2024-02-01', 68.5, 165.0, 25.2, 45.8, 25.2, 88.0, 75.0, 98.0, 28.0, 55.0, 4),
(9, '2024-03-01', 67.2, 165.0, 24.1, 46.5, 24.7, 87.0, 73.0, 96.0, 28.5, 54.0, 4),
(10, '2024-01-20', 75.0, 180.0, 20.5, 58.2, 23.1, 95.0, 80.0, 90.0, 32.0, 56.0, 5);

-- Insertar check-ins
INSERT INTO `check_ins` (`user_id`, `gym_id`, `check_in_time`, `check_out_time`, `duration_minutes`, `method`) VALUES
(8, 1, '2024-12-09 07:00:00', '2024-12-09 08:30:00', 90, 'app'),
(8, 1, '2024-12-11 07:00:00', '2024-12-11 08:15:00', 75, 'app'),
(9, 1, '2024-12-10 18:00:00', '2024-12-10 19:00:00', 60, 'card'),
(9, 1, '2024-12-12 18:00:00', '2024-12-12 18:45:00', 45, 'card'),
(10, 2, '2024-12-09 19:00:00', '2024-12-09 19:45:00', 45, 'qr_code'),
(11, 1, '2024-12-10 16:00:00', '2024-12-10 17:30:00', 90, 'app'),
(12, 2, '2024-12-11 08:00:00', '2024-12-11 09:15:00', 75, 'card');

-- Insertar mensajes
INSERT INTO `messages` (`sender_id`, `receiver_id`, `subject`, `message`, `message_type`) VALUES
(3, 8, 'Rutina Actualizada', 'He actualizado tu rutina con nuevos ejercicios. Revisa los cambios en tu panel.', 'text'),
(8, 3, 'RE: Rutina Actualizada', 'Perfecto, gracias Miguel. ¿Cuándo podemos revisar la técnica del peso muerto?', 'text'),
(4, 9, 'Progreso Excelente', '¡Felicidades María! Tu progreso en cardio ha sido excepcional este mes.', 'text'),
(1, 8, 'Bienvenido a StyloFitness', 'Bienvenido Juan. Esperamos que disfrutes tu experiencia con nosotros.', 'system'),
(9, 4, 'Consulta sobre Nutrición', 'Sofía, ¿podrías recomendarme algunos suplementos para complementar mi rutina?', 'text');

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

-- Índices para mejorar rendimiento en consultas frecuentes
CREATE INDEX idx_users_membership_dates ON users(membership_start, membership_end);
CREATE INDEX idx_workout_logs_user_date ON workout_logs(user_id, workout_date);
CREATE INDEX idx_class_bookings_date_status ON class_bookings(booking_date, status);
CREATE INDEX idx_orders_user_date ON orders(user_id, created_at);
CREATE INDEX idx_products_category_status ON products(category_id, status);
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);
CREATE INDEX idx_check_ins_gym_date ON check_ins(gym_id, check_in_time);
CREATE INDEX idx_messages_receiver_read ON messages(receiver_id, is_read);

-- =====================================================
-- TRIGGERS PARA AUTOMATIZACIÓN
-- =====================================================

-- Trigger para actualizar duración en check_ins
DELIMITER //
CREATE TRIGGER update_checkin_duration 
    BEFORE UPDATE ON check_ins
    FOR EACH ROW
BEGIN
    IF NEW.check_out_time IS NOT NULL AND OLD.check_out_time IS NULL THEN
        SET NEW.duration_minutes = TIMESTAMPDIFF(MINUTE, NEW.check_in_time, NEW.check_out_time);
    END IF;
END//
DELIMITER ;

-- Trigger para calcular BMI automáticamente
DELIMITER //
CREATE TRIGGER calculate_bmi 
    BEFORE INSERT ON user_progress
    FOR EACH ROW
BEGIN
    IF NEW.weight IS NOT NULL AND NEW.height IS NOT NULL THEN
        SET NEW.bmi = ROUND((NEW.weight / POWER(NEW.height / 100, 2)), 1);
    END IF;
END//
DELIMITER ;

-- Trigger para actualizar stock de productos
DELIMITER //
CREATE TRIGGER update_product_stock 
    AFTER INSERT ON order_items
    FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE id = NEW.product_id;
END//
DELIMITER ;

-- =====================================================
-- VISTAS PARA REPORTES COMUNES
-- =====================================================

-- Vista para estadísticas de usuarios por rol
CREATE VIEW user_stats_by_role AS
SELECT 
    role,
    COUNT(*) as total_users,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_users,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_users_30_days
FROM users 
GROUP BY role;

-- Vista para estadísticas de entrenamientos
CREATE VIEW workout_stats AS
SELECT 
    u.id as user_id,
    u.first_name,
    u.last_name,
    COUNT(wl.id) as total_workouts,
    AVG(wl.total_duration_minutes) as avg_duration,
    SUM(wl.calories_burned) as total_calories,
    AVG(wl.rating) as avg_rating,
    MAX(wl.workout_date) as last_workout
FROM users u
LEFT JOIN workout_logs wl ON u.id = wl.user_id
WHERE u.role = 'client'
GROUP BY u.id, u.first_name, u.last_name;

-- Vista para ingresos por productos
CREATE VIEW product_revenue AS
SELECT 
    p.id,
    p.name,
    p.category_id,
    pc.name as category_name,
    COUNT(oi.id) as times_sold,
    SUM(oi.quantity) as total_quantity_sold,
    SUM(oi.total_price) as total_revenue,
    AVG(oi.unit_price) as avg_selling_price
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN product_categories pc ON p.category_id = pc.id
GROUP BY p.id, p.name, p.category_id, pc.name;

-- Vista para ocupación de clases
CREATE VIEW class_occupancy AS
SELECT 
    gc.id,
    gc.name,
    gc.max_participants,
    cs.day_of_week,
    cs.start_time,
    COUNT(cb.id) as current_bookings,
    ROUND((COUNT(cb.id) / gc.max_participants) * 100, 2) as occupancy_percentage
FROM group_classes gc
JOIN class_schedules cs ON gc.id = cs.class_id
LEFT JOIN class_bookings cb ON cs.id = cb.schedule_id 
    AND cb.booking_date >= CURDATE() 
    AND cb.status = 'confirmed'
WHERE gc.status = 'active' AND cs.status = 'active'
GROUP BY gc.id, gc.name, gc.max_participants, cs.day_of_week, cs.start_time;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

-- Procedimiento para obtener estadísticas del dashboard
DELIMITER //
CREATE PROCEDURE GetDashboardStats()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM users WHERE role = 'client' AND status = 'active') as active_clients,
        (SELECT COUNT(*) FROM users WHERE role = 'trainer' AND status = 'active') as active_trainers,
        (SELECT COUNT(*) FROM workout_logs WHERE workout_date = CURDATE()) as todays_workouts,
        (SELECT COUNT(*) FROM class_bookings WHERE booking_date = CURDATE() AND status = 'confirmed') as todays_classes,
        (SELECT SUM(total_amount) FROM orders WHERE DATE(created_at) = CURDATE()) as todays_revenue,
        (SELECT COUNT(*) FROM check_ins WHERE DATE(check_in_time) = CURDATE()) as todays_checkins;
END//
DELIMITER ;

-- Procedimiento para generar reporte mensual de usuario
DELIMITER //
CREATE PROCEDURE GetUserMonthlyReport(IN user_id INT, IN report_month INT, IN report_year INT)
BEGIN
    DECLARE start_date DATE;
    DECLARE end_date DATE;
    
    SET start_date = DATE(CONCAT(report_year, '-', LPAD(report_month, 2, '0'), '-01'));
    SET end_date = LAST_DAY(start_date);
    
    SELECT 
        COUNT(wl.id) as total_workouts,
        SUM(wl.total_duration_minutes) as total_minutes,
        SUM(wl.calories_burned) as total_calories,
        AVG(wl.rating) as avg_rating,
        COUNT(cb.id) as classes_attended,
        COUNT(ci.id) as gym_visits
    FROM users u
    LEFT JOIN workout_logs wl ON u.id = wl.user_id 
        AND wl.workout_date BETWEEN start_date AND end_date
    LEFT JOIN class_bookings cb ON u.id = cb.user_id 
        AND cb.booking_date BETWEEN start_date AND end_date 
        AND cb.status = 'attended'
    LEFT JOIN check_ins ci ON u.id = ci.user_id 
        AND DATE(ci.check_in_time) BETWEEN start_date AND end_date
    WHERE u.id = user_id;
END//
DELIMITER ;

-- =====================================================
-- CONFIGURACIÓN FINAL
-- =====================================================

-- Habilitar claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- Confirmar transacción
COMMIT;

-- =====================================================
-- INFORMACIÓN DEL SCHEMA
-- =====================================================
/*
ESTE SCHEMA INCLUYE:

1. TABLAS PRINCIPALES:
   - users: Gestión completa de usuarios (admin, trainer, staff, client)
   - gyms: Múltiples sucursales
   - rooms: Salas y espacios del gimnasio
   - exercises: Biblioteca de ejercicios
   - routines: Rutinas personalizadas y templates
   - group_classes: Clases grupales
   - products: Tienda online
   - orders: Sistema de ventas
   - notifications: Sistema de notificaciones

2. FUNCIONALIDADES IMPLEMENTADAS:
   - Sistema de roles completo
   - Gestión de rutinas y entrenamientos
   - Registro de progreso
   - Sistema de clases grupales con reservas
   - E-commerce completo
   - Sistema de check-in/check-out
   - Mensajería interna
   - Landing page configurable

3. DATOS DE PRUEBA:
   - 2 Administradores
   - 3 Entrenadores
   - 2 Staff
   - 5 Clientes
   - 2 Gimnasios con salas
   - 10 Ejercicios base
   - Rutinas y entrenamientos
   - Clases grupales con horarios
   - Productos y órdenes
   - Progreso y check-ins

4. OPTIMIZACIONES:
   - Índices para consultas frecuentes
   - Triggers para automatización
   - Vistas para reportes
   - Procedimientos almacenados

5. CREDENCIALES DE PRUEBA:
   - Admin: admin@stylofitness.com / password
   - Trainer: trainer1@stylofitness.com / password
   - Staff: staff1@stylofitness.com / password
   - Client: client1@example.com / password
   
   (Todas las contraseñas están hasheadas con bcrypt)
*/