-- ==========================================
-- STYLOFITNESS - BASE DE DATOS COMPLETA
-- Sistema de Gestión de Gimnasios Profesional
-- Version: 3.0.0 - Actualizada y Optimizada
-- Fecha: Diciembre 2024
-- ==========================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Configuración de charset
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ==========================================
-- CREAR BASE DE DATOS SI NO EXISTE
-- ==========================================

-- CREATE DATABASE IF NOT EXISTS `stylofitness_gym` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `stylofitness_gym`;

-- ==========================================
-- TABLAS PRINCIPALES DEL SISTEMA
-- ==========================================

-- Tabla de sedes de gimnasios
CREATE TABLE IF NOT EXISTS `gyms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` text,
  `phone` varchar(20),
  `email` varchar(255),
  `logo` varchar(255),
  `theme_colors` json,
  `settings` json,
  `operating_hours` json,
  `social_media` json,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gym_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios del sistema
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gym_id` int(11) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20),
  `date_of_birth` date,
  `gender` enum('male','female','other') DEFAULT NULL,
  `role` enum('admin','instructor','client') DEFAULT 'client',
  `profile_image` varchar(255),
  `is_active` tinyint(1) DEFAULT 1,
  `membership_type` varchar(50) DEFAULT 'basic',
  `membership_expires` date,
  `preferences` json,
  `emergency_contact` json,
  `medical_info` json,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(255),
  `last_login_at` timestamp NULL DEFAULT NULL,
  `login_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`),
  UNIQUE KEY `idx_email` (`email`),
  KEY `idx_gym_id` (`gym_id`),
  KEY `idx_role` (`role`),
  KEY `idx_active` (`is_active`),
  KEY `idx_membership_expires` (`membership_expires`),
  CONSTRAINT `fk_users_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de tokens de seguridad
CREATE TABLE IF NOT EXISTS `security_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `type` enum('password_reset','email_verification','remember_me') NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_token` (`token`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_type` (`type`),
  KEY `idx_expires_at` (`expires_at`),
  CONSTRAINT `fk_security_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLAS DE EJERCICIOS Y RUTINAS
-- ==========================================

-- Tabla de categorías de ejercicios
CREATE TABLE IF NOT EXISTS `exercise_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(100),
  `color` varchar(7) DEFAULT '#FF6B00',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de ejercicios
CREATE TABLE IF NOT EXISTS `exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11),
  `name` varchar(255) NOT NULL,
  `description` text,
  `instructions` text,
  `muscle_groups` json,
  `difficulty_level` enum('beginner','intermediate','advanced') DEFAULT 'intermediate',
  `equipment_needed` varchar(255),
  `video_url` varchar(255),
  `video_thumbnail` varchar(255),
  `image_url` varchar(255),
  `duration_minutes` int(11),
  `calories_burned` int(11),
  `tags` json,
  `views_count` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `rating_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11),
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_difficulty_level` (`difficulty_level`),
  KEY `idx_active` (`is_active`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_rating` (`rating`),
  FULLTEXT KEY `idx_search` (`name`,`description`,`instructions`),
  CONSTRAINT `fk_exercises_category` FOREIGN KEY (`category_id`) REFERENCES `exercise_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_exercises_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de rutinas
CREATE TABLE IF NOT EXISTS `routines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gym_id` int(11),
  `instructor_id` int(11),
  `client_id` int(11),
  `name` varchar(255) NOT NULL,
  `description` text,
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
  `image_url` varchar(255),
  `tags` json,
  `equipment_needed` json,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gym_id` (`gym_id`),
  KEY `idx_instructor_id` (`instructor_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_objective` (`objective`),
  KEY `idx_difficulty_level` (`difficulty_level`),
  KEY `idx_template` (`is_template`),
  KEY `idx_public` (`is_public`),
  KEY `idx_active` (`is_active`),
  KEY `idx_rating` (`rating`),
  FULLTEXT KEY `idx_search` (`name`,`description`),
  CONSTRAINT `fk_routines_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routines_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_routines_client` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de ejercicios en rutinas
CREATE TABLE IF NOT EXISTS `routine_exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `routine_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `order_index` int(11) NOT NULL,
  `sets` int(11) DEFAULT 3,
  `reps` varchar(20) DEFAULT '10',
  `weight` varchar(20),
  `rest_seconds` int(11) DEFAULT 60,
  `tempo` varchar(10),
  `rpe` int(11),
  `notes` text,
  `superset_group` int(11),
  `is_warmup` tinyint(1) DEFAULT 0,
  `is_cooldown` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_routine_id` (`routine_id`),
  KEY `idx_exercise_id` (`exercise_id`),
  KEY `idx_day_number` (`day_number`),
  KEY `idx_order_index` (`order_index`),
  KEY `idx_superset_group` (`superset_group`),
  CONSTRAINT `fk_routine_exercises_routine` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_routine_exercises_exercise` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de logs de entrenamientos
CREATE TABLE IF NOT EXISTS `workout_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `routine_id` int(11),
  `exercise_id` int(11) NOT NULL,
  `workout_date` date NOT NULL,
  `sets_completed` int(11) DEFAULT 0,
  `reps` varchar(20),
  `weight_used` varchar(20),
  `duration_seconds` int(11),
  `calories_burned` int(11),
  `rpe` int(11),
  `notes` text,
  `completed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_routine_id` (`routine_id`),
  KEY `idx_exercise_id` (`exercise_id`),
  KEY `idx_workout_date` (`workout_date`),
  KEY `idx_completed_at` (`completed_at`),
  CONSTRAINT `fk_workout_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_workout_logs_routine` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_workout_logs_exercise` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de progreso de usuarios
CREATE TABLE IF NOT EXISTS `user_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `routine_id` int(11),
  `measurement_date` date NOT NULL,
  `weight_kg` decimal(5,2),
  `body_fat_percentage` decimal(5,2),
  `muscle_mass_kg` decimal(5,2),
  `measurements` json,
  `photos` json,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_routine_id` (`routine_id`),
  KEY `idx_measurement_date` (`measurement_date`),
  CONSTRAINT `fk_user_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_progress_routine` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLAS DE TIENDA Y E-COMMERCE
-- ==========================================

-- Tabla de categorías de productos
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `parent_id` int(11),
  `image_url` varchar(255),
  `banner_image` varchar(255),
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `meta_title` varchar(255),
  `meta_description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_slug` (`slug`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort_order` (`sort_order`),
  CONSTRAINT `fk_product_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11),
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `short_description` text,
  `sku` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2),
  `cost_price` decimal(10,2),
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 5,
  `weight` decimal(8,2),
  `dimensions` varchar(100),
  `images` json,
  `gallery` json,
  `specifications` json,
  `nutritional_info` json,
  `usage_instructions` text,
  `ingredients` text,
  `warnings` text,
  `brand` varchar(100),
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `views_count` int(11) DEFAULT 0,
  `sales_count` int(11) DEFAULT 0,
  `avg_rating` decimal(3,2) DEFAULT 0.00,
  `reviews_count` int(11) DEFAULT 0,
  `meta_title` varchar(255),
  `meta_description` text,
  `meta_keywords` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_slug` (`slug`),
  UNIQUE KEY `idx_sku` (`sku`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_active` (`is_active`),
  KEY `idx_price` (`price`),
  KEY `idx_stock_quantity` (`stock_quantity`),
  KEY `idx_brand` (`brand`),
  KEY `idx_avg_rating` (`avg_rating`),
  FULLTEXT KEY `idx_search` (`name`,`description`,`short_description`,`brand`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de variaciones de productos
CREATE TABLE IF NOT EXISTS `product_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(100) NOT NULL,
    `price` decimal(10,2),
  `sale_price` decimal(10,2),
  `stock_quantity` int(11) DEFAULT 0,
  `weight` decimal(8,2),
  `attributes` json,
  `image_url` varchar(255),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_sku` (`sku`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_product_variations_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de reseñas de productos
CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `title` varchar(255),
  `comment` text,
  `pros` text,
  `cons` text,
  `images` json,
  `is_approved` tinyint(1) DEFAULT 0,
  `is_verified_purchase` tinyint(1) DEFAULT 0,
  `helpful_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_rating` (`rating`),
  KEY `idx_approved` (`is_approved`),
  KEY `idx_verified_purchase` (`is_verified_purchase`),
  CONSTRAINT `fk_product_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_product_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de lista de deseos
CREATE TABLE IF NOT EXISTS `wishlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_product` (`user_id`,`product_id`),
  KEY `idx_product_id` (`product_id`),
  CONSTRAINT `fk_wishlists_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlists_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de carrito de compras
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variation_id` int(11),
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_variation_id` (`variation_id`),
  CONSTRAINT `fk_cart_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_items_variation` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de órdenes
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  `payment_status` enum('pending','paid','partial_paid','refunded','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50),
  `payment_reference` varchar(255),
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'PEN',
  `billing_address` json,
  `shipping_address` json,
  `shipping_method` varchar(100),
  `tracking_number` varchar(100),
  `notes` text,
  `internal_notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_order_number` (`order_number`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de items de órdenes
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variation_id` int(11),
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `product_data` json,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_variation_id` (`variation_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_variation` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de cupones de descuento
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `type` enum('fixed','percentage') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `minimum_amount` decimal(10,2) DEFAULT 0.00,
  `maximum_discount` decimal(10,2),
  `usage_limit` int(11),
  `used_count` int(11) DEFAULT 0,
  `user_limit` int(11) DEFAULT 1,
  `valid_from` timestamp NOT NULL,
  `valid_until` timestamp NOT NULL,
  `applicable_products` json,
  `applicable_categories` json,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`),
  KEY `idx_active` (`is_active`),
  KEY `idx_valid_from` (`valid_from`),
  KEY `idx_valid_until` (`valid_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de uso de cupones
CREATE TABLE IF NOT EXISTS `coupon_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_coupon_id` (`coupon_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_order_id` (`order_id`),
  CONSTRAINT `fk_coupon_usage_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_coupon_usage_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_coupon_usage_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLAS DE CLASES GRUPALES
-- ==========================================

-- Tabla de clases grupales
CREATE TABLE IF NOT EXISTS `group_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gym_id` int(11),
  `instructor_id` int(11),
  `name` varchar(255) NOT NULL,
  `description` text,
  `class_type` enum('cardio','strength','flexibility','dance','martial_arts','aqua','yoga','pilates','crossfit','hiit','spinning') DEFAULT 'cardio',
  `duration_minutes` int(11) DEFAULT 60,
  `max_participants` int(11) DEFAULT 20,
  `room` varchar(100),
  `equipment_needed` text,
  `difficulty_level` enum('beginner','intermediate','advanced') DEFAULT 'intermediate',
  `price` decimal(8,2) DEFAULT 0.00,
  `image_url` varchar(255),
  `requirements` text,
  `benefits` text,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gym_id` (`gym_id`),
  KEY `idx_instructor_id` (`instructor_id`),
  KEY `idx_class_type` (`class_type`),
  KEY `idx_difficulty_level` (`difficulty_level`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_group_classes_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_group_classes_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de horarios de clases
CREATE TABLE IF NOT EXISTS `class_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `start_date` date,
  `end_date` date,
  `is_recurring` tinyint(1) DEFAULT 1,
  `exceptions` json,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_class_id` (`class_id`),
  KEY `idx_day_of_week` (`day_of_week`),
  KEY `idx_start_time` (`start_time`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_class_schedules_class` FOREIGN KEY (`class_id`) REFERENCES `group_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de reservas de clases
CREATE TABLE IF NOT EXISTS `class_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `status` enum('booked','confirmed','attended','no_show','cancelled','waitlist') DEFAULT 'booked',
  `payment_status` enum('pending','paid','refunded','free') DEFAULT 'free',
  `amount_paid` decimal(8,2) DEFAULT 0.00,
  `booking_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `check_in_time` timestamp NULL DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_booking` (`schedule_id`,`user_id`,`booking_date`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_booking_date` (`booking_date`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`),
  CONSTRAINT `fk_class_bookings_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de evaluaciones de clases
CREATE TABLE IF NOT EXISTS `class_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_class_id` (`class_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_rating` (`rating`),
  CONSTRAINT `fk_class_reviews_class` FOREIGN KEY (`class_id`) REFERENCES `group_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLAS DE SALAS Y POSICIONES
-- ==========================================

-- Tabla de salas
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gym_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `capacity` int(11) DEFAULT 1,
  `room_type` enum('individual','group','functional','cardio','strength') DEFAULT 'individual',
  `equipment_available` json,
  `amenities` json,
  `hourly_rate` decimal(8,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gym_id` (`gym_id`),
  KEY `idx_room_type` (`room_type`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_rooms_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de posiciones en salas
CREATE TABLE IF NOT EXISTS `room_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `position_number` int(11) NOT NULL,
  `position_name` varchar(100),
  `x_coordinate` decimal(5,2),
  `y_coordinate` decimal(5,2),
  `is_available` tinyint(1) DEFAULT 1,
  `equipment_type` varchar(100),
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_room_position` (`room_id`,`position_number`),
  KEY `idx_available` (`is_available`),
  CONSTRAINT `fk_room_positions_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de reservas de posiciones
CREATE TABLE IF NOT EXISTS `position_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('booked','confirmed','in_use','completed','cancelled') DEFAULT 'booked',
  `payment_status` enum('pending','paid','refunded','free') DEFAULT 'free',
  `amount_paid` decimal(8,2) DEFAULT 0.00,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_position_id` (`position_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_booking_date` (`booking_date`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_position_bookings_position` FOREIGN KEY (`position_id`) REFERENCES `room_positions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_position_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLAS DE SEGUIMIENTO DE PROGRESO
-- ==========================================

-- Tabla para notas del entrenador sobre clientes
CREATE TABLE IF NOT EXISTS `trainer_client_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `note_text` text NOT NULL,
  `note_type` enum('general','progress','goal','concern','achievement') DEFAULT 'general',
  `is_private` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_trainer_client` (`trainer_id`,`client_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_note_type` (`note_type`),
  CONSTRAINT `fk_trainer_notes_trainer` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_trainer_notes_client` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de métricas personalizadas
CREATE TABLE IF NOT EXISTS `custom_progress_metrics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer_id` int(11) NOT NULL,
  `metric_name` varchar(255) NOT NULL,
  `metric_type` enum('number','percentage','time','distance','weight') DEFAULT 'number',
  `unit` varchar(50),
  `description` text,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_trainer_id` (`trainer_id`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_custom_metrics_trainer` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de valores de métricas personalizadas
CREATE TABLE IF NOT EXISTS `custom_metric_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `metric_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `measurement_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_metric_user` (`metric_id`,`user_id`),
  KEY `idx_measurement_date` (`measurement_date`),
  CONSTRAINT `fk_metric_values_metric` FOREIGN KEY (`metric_id`) REFERENCES `custom_progress_metrics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_metric_values_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLAS DE LANDING PAGE Y CMS
-- ==========================================

-- Tabla para ofertas especiales
CREATE TABLE IF NOT EXISTS `special_offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(500),
  `description` text,
  `discount_percentage` decimal(5,2),
  `discount_amount` decimal(10,2),
  `image` varchar(255),
  `button_text` varchar(100),
  `button_link` varchar(255),
  `valid_from` timestamp NULL DEFAULT NULL,
  `valid_until` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort_order` (`sort_order`),
  KEY `idx_valid_dates` (`valid_from`,`valid_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para sección "Por qué elegirnos"
CREATE TABLE IF NOT EXISTS `why_choose_us` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `icon` varchar(100),
  `image` varchar(255),
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de testimonios
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role` varchar(255),
  `image` varchar(255),
  `text` text NOT NULL,
  `rating` tinyint(1) DEFAULT 5,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort_order` (`sort_order`),
  KEY `idx_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de configuración de productos destacados
CREATE TABLE IF NOT EXISTS `featured_products_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(255) NOT NULL,
  `title` varchar(255),
  `subtitle` varchar(500),
  `max_products` int(11) DEFAULT 8,
  `filter_criteria` json,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de configuración de landing page
CREATE TABLE IF NOT EXISTS `landing_page_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(255) NOT NULL,
  `title` varchar(255),
  `subtitle` varchar(500),
  `content` text,
  `image` varchar(255),
  `background_image` varchar(255),
  `button_text` varchar(100),
  `button_link` varchar(255),
  `settings` json,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_section_name` (`section_name`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de páginas CMS
CREATE TABLE IF NOT EXISTS `cms_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext,
  `excerpt` text,
  `featured_image` varchar(255),
  `meta_title` varchar(255),
  `meta_description` text,
  `meta_keywords` text,
  `status` enum('draft','published','private') DEFAULT 'draft',
  `author_id` int(11),
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `idx_author_id` (`author_id`),
  KEY `idx_published_at` (`published_at`),
  CONSTRAINT `fk_cms_pages_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLAS DE SISTEMA Y CONFIGURACIÓN
-- ==========================================

-- Tabla de configuraciones del sistema
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text,
  `setting_type` enum('string','integer','boolean','json','text') DEFAULT 'string',
  `description` text,
  `setting_group` varchar(100) DEFAULT 'general',
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_setting_key` (`setting_key`),
  KEY `idx_setting_group` (`setting_group`),
  KEY `idx_is_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de logs de actividad
CREATE TABLE IF NOT EXISTS `user_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11),
  `action` varchar(255) NOT NULL,
  `entity_type` varchar(100),
  `entity_id` int(11),
  `ip_address` varchar(45),
  `user_agent` text,
  `details` json,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_entity` (`entity_type`,`entity_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `data` json,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_type` (`type`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- INSERTAR DATOS INICIALES
-- ==========================================

-- Insertar gimnasio principal
INSERT INTO `gyms` (`id`, `name`, `address`, `phone`, `email`, `logo`, `theme_colors`, `operating_hours`, `social_media`, `is_active`) VALUES
(1, 'STYLOFITNESS Central', 'Av. Javier Prado Este 4200, San Borja, Lima', '+51 999 888 777', 'info@stylofitness.com', '/images/logo-stylofitness.png', 
 JSON_OBJECT('primary', '#FF6B00', 'secondary', '#E55A00', 'accent', '#FFA500', 'dark', '#1A1A1A', 'light', '#F8F9FA'),
 JSON_OBJECT('monday', '06:00-23:00', 'tuesday', '06:00-23:00', 'wednesday', '06:00-23:00', 'thursday', '06:00-23:00', 'friday', '06:00-23:00', 'saturday', '07:00-22:00', 'sunday', '08:00-20:00'),
 JSON_OBJECT('facebook', 'https://facebook.com/stylofitness', 'instagram', 'https://instagram.com/stylofitness', 'youtube', 'https://youtube.com/stylofitness', 'tiktok', 'https://tiktok.com/@stylofitness'),
 1);

-- Insertar usuarios del sistema
INSERT INTO `users` (`id`, `gym_id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone`, `date_of_birth`, `gender`, `role`, `membership_type`, `membership_expires`, `is_active`) VALUES
(1, 1, 'admin', 'admin@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', '+51 999 888 777', '1985-01-15', 'male', 'admin', 'premium', '2025-12-31', 1),
(2, 1, 'trainer_carlos', 'carlos@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Rodríguez', '+51 987 654 321', '1990-03-22', 'male', 'instructor', 'staff', '2025-12-31', 1),
(3, 1, 'trainer_ana', 'ana@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Morales', '+51 976 543 210', '1988-07-10', 'female', 'instructor', 'staff', '2025-12-31', 1),
(4, 1, 'maria_gonzalez', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'González', '+51 965 432 109', '1992-11-05', 'female', 'client', 'premium', '2025-06-30', 1),
(5, 1, 'diego_fernandez', 'diego@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diego', 'Fernández', '+51 954 321 098', '1995-04-18', 'male', 'client', 'basic', '2025-03-31', 1),
(6, 1, 'lucia_torres', 'lucia@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lucía', 'Torres', '+51 943 210 987', '1993-09-12', 'female', 'client', 'premium', '2025-08-15', 1),
(7, 1, 'javier_silva', 'javier@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Javier', 'Silva', '+51 932 109 876', '1987-12-03', 'male', 'client', 'basic', '2025-05-20', 1),
(8, 1, 'sofia_ramirez', 'sofia@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sofía', 'Ramírez', '+51 921 098 765', '1996-02-28', 'female', 'client', 'premium', '2025-07-10', 1),
(9, 1, 'staff_admin', 'staff@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Personal', 'Administrativo', '+51 910 987 654', '1989-06-15', 'female', 'admin', 'staff', '2025-12-31', 1);

-- Insertar categorías de ejercicios
INSERT INTO `exercise_categories` (`id`, `name`, `description`, `icon`, `color`, `sort_order`) VALUES
(1, 'Fuerza', 'Ejercicios de entrenamiento de fuerza y resistencia', 'fas fa-dumbbell', '#FF6B00', 1),
(2, 'Cardio', 'Ejercicios cardiovasculares y de resistencia aeróbica', 'fas fa-heartbeat', '#E74C3C', 2),
(3, 'Flexibilidad', 'Ejercicios de estiramiento y movilidad', 'fas fa-leaf', '#27AE60', 3),
(4, 'Funcional', 'Ejercicios funcionales y de movimiento natural', 'fas fa-running', '#3498DB', 4),
(5, 'Core', 'Ejercicios para fortalecer el núcleo corporal', 'fas fa-circle', '#9B59B6', 5),
(6, 'Pliométrico', 'Ejercicios explosivos y de potencia', 'fas fa-bolt', '#F39C12', 6);

-- Insertar ejercicios de ejemplo
INSERT INTO `exercises` (`id`, `category_id`, `name`, `description`, `instructions`, `muscle_groups`, `difficulty_level`, `equipment_needed`, `duration_minutes`, `calories_burned`, `tags`, `is_active`, `created_by`) VALUES
(1, 1, 'Press de Banca', 'Ejercicio fundamental para el desarrollo del pecho', 'Acuéstate en el banco, agarra la barra con las manos separadas al ancho de los hombros, baja controladamente hasta el pecho y empuja hacia arriba.', JSON_ARRAY('pecho', 'tríceps', 'hombros'), 'intermediate', 'Banca, barra, discos', 3, 15, JSON_ARRAY('fuerza', 'pecho', 'básico'), 1, 2),
(2, 1, 'Sentadilla', 'Ejercicio rey para el tren inferior', 'Coloca la barra sobre los hombros, separa los pies al ancho de hombros, baja manteniendo la espalda recta hasta que los muslos estén paralelos al suelo.', JSON_ARRAY('cuádriceps', 'glúteos', 'isquiotibiales'), 'beginner', 'Barra, rack de sentadillas', 4, 20, JSON_ARRAY('fuerza', 'piernas', 'básico'), 1, 2),
(3, 1, 'Peso Muerto', 'Ejercicio compuesto para toda la cadena posterior', 'Con los pies separados al ancho de hombros, agarra la barra y levántala manteniendo la espalda recta hasta estar completamente erguido.', JSON_ARRAY('espalda', 'glúteos', 'isquiotibiales'), 'advanced', 'Barra, discos', 5, 25, JSON_ARRAY('fuerza', 'espalda', 'compuesto'), 1, 2),
(4, 2, 'Burpees', 'Ejercicio cardiovascular de cuerpo completo', 'Desde posición de pie, baja a cuclillas, salta hacia atrás a plancha, haz una flexión, salta hacia adelante y salta verticalmente.', JSON_ARRAY('cuerpo completo'), 'intermediate', 'Ninguno', 1, 12, JSON_ARRAY('cardio', 'hiit', 'funcional'), 1, 3),
(5, 2, 'Mountain Climbers', 'Ejercicio cardiovascular intenso', 'En posición de plancha, alterna llevando las rodillas hacia el pecho de forma rápida y controlada.', JSON_ARRAY('core', 'hombros', 'piernas'), 'beginner', 'Ninguno', 1, 10, JSON_ARRAY('cardio', 'core', 'hiit'), 1, 3),
(6, 3, 'Estiramiento de Isquiotibiales', 'Estiramiento para la parte posterior del muslo', 'Sentado con una pierna extendida, inclínate hacia adelante manteniendo la espalda recta.', JSON_ARRAY('isquiotibiales'), 'beginner', 'Esterilla', 2, 3, JSON_ARRAY('flexibilidad', 'estiramiento'), 1, 3),
(7, 4, 'Kettlebell Swing', 'Ejercicio funcional con kettlebell', 'Con los pies separados, agarra la kettlebell con ambas manos y realiza un movimiento de bisagra de cadera.', JSON_ARRAY('glúteos', 'core', 'hombros'), 'intermediate', 'Kettlebell', 3, 18, JSON_ARRAY('funcional', 'potencia', 'cardio'), 1, 2),
(8, 5, 'Plancha', 'Ejercicio isométrico para el core', 'Mantén el cuerpo recto apoyado en antebrazos y pies, contrayendo el abdomen.', JSON_ARRAY('core', 'hombros'), 'beginner', 'Esterilla', 2, 8, JSON_ARRAY('core', 'isométrico', 'estabilidad'), 1, 3),
(9, 6, 'Box Jumps', 'Saltos explosivos sobre cajón', 'Salta sobre el cajón con ambos pies, aterriza suavemente y baja controladamente.', JSON_ARRAY('piernas', 'glúteos'), 'intermediate', 'Cajón pliométrico', 2, 15, JSON_ARRAY('pliométrico', 'potencia', 'explosivo'), 1, 2),
(10, 1, 'Dominadas', 'Ejercicio de tracción para espalda', 'Cuelga de la barra con agarre prono, tira hacia arriba hasta que el mentón supere la barra.', JSON_ARRAY('espalda', 'bíceps'), 'advanced', 'Barra de dominadas', 3, 12, JSON_ARRAY('fuerza', 'espalda', 'tracción'), 1, 2);

-- Insertar categorías de productos
INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`, `image_url`, `is_active`, `sort_order`) VALUES
(1, 'Proteínas', 'proteinas', 'Suplementos proteicos para el desarrollo muscular', '/uploads/images/categories/proteinas.jpg', 1, 1),
(2, 'Pre-Entreno', 'pre-entreno', 'Suplementos para maximizar el rendimiento', '/uploads/images/categories/pre-entreno.jpg', 1, 2),
(3, 'Vitaminas', 'vitaminas', 'Vitaminas y minerales esenciales', '/uploads/images/categories/vitaminas.jpg', 1, 3),
(4, 'Accesorios', 'accesorios', 'Accesorios y equipamiento deportivo', '/uploads/images/categories/accesorios.jpg', 1, 4),
(5, 'Ropa Deportiva', 'ropa-deportiva', 'Indumentaria deportiva de alta calidad', '/uploads/images/categories/ropa.jpg', 1, 5),
(6, 'Equipamiento', 'equipamiento', 'Equipos y máquinas para entrenamiento', '/uploads/images/categories/equipamiento.jpg', 1, 6);

-- Insertar productos de ejemplo
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `sale_price`, `stock_quantity`, `weight`, `images`, `brand`, `is_featured`, `is_active`, `views_count`, `sales_count`) VALUES
(1, 1, 'Whey Protein Gold Standard', 'whey-protein-gold-standard', 'Proteína de suero de alta calidad con 24g de proteína por porción. Ideal para el desarrollo muscular y recuperación post-entreno.', 'Proteína premium con 24g por porción', 'WPG-2LB-VAN', 89.90, 79.90, 50, 0.91, JSON_ARRAY('/uploads/products/whey-gold-1.jpg', '/uploads/products/whey-gold-2.jpg'), 'Optimum Nutrition', 1, 1, 245, 18),
(2, 1, 'Caseína Micellar', 'caseina-micellar', 'Proteína de absorción lenta ideal para tomar antes de dormir. Proporciona aminoácidos durante toda la noche.', 'Proteína de liberación lenta', 'CAS-2LB-CHO', 95.90, NULL, 30, 0.91, JSON_ARRAY('/uploads/products/casein-1.jpg'), 'Dymatize', 0, 1, 156, 12),
(3, 2, 'C4 Original Pre-Workout', 'c4-original-pre-workout', 'Pre-entreno con cafeína, beta-alanina y creatina para máxima energía y rendimiento en tus entrenamientos.', 'Pre-entreno con cafeína y creatina', 'C4-30SERV-FRU', 69.90, 59.90, 75, 0.39, JSON_ARRAY('/uploads/products/c4-original-1.jpg', '/uploads/products/c4-original-2.jpg'), 'Cellucor', 1, 1, 189, 25),
(4, 3, 'Multivitamínico Complete', 'multivitaminico-complete', 'Complejo vitamínico y mineral completo para deportistas. Contiene todas las vitaminas esenciales.', 'Multivitamínico para deportistas', 'MULTI-90CAPS', 45.90, NULL, 100, 0.15, JSON_ARRAY('/uploads/products/multivitamin-1.jpg'), 'Universal Nutrition', 0, 1, 98, 8),
(5, 4, 'Shaker Premium 600ml', 'shaker-premium-600ml', 'Shaker de alta calidad con compartimento para suplementos y rejilla mezcladora. Libre de BPA.', 'Shaker premium con compartimentos', 'SHAK-600-BLK', 25.90, 19.90, 200, 0.18, JSON_ARRAY('/uploads/products/shaker-1.jpg'), 'BlenderBottle', 1, 1, 312, 45),
(6, 5, 'Camiseta Dri-Fit Training', 'camiseta-dri-fit-training', 'Camiseta deportiva con tecnología Dri-Fit que mantiene la piel seca durante el entrenamiento.', 'Camiseta deportiva transpirable', 'SHIRT-M-BLK', 39.90, 34.90, 80, 0.20, JSON_ARRAY('/uploads/products/shirt-1.jpg'), 'Nike', 1, 1, 167, 22),
(7, 1, 'Proteína Vegana', 'proteina-vegana', 'Proteína 100% vegetal a base de guisantes y arroz. Perfecta para veganos y vegetarianos.', 'Proteína plant-based', 'VEGAN-2LB-VAN', 79.90, NULL, 25, 0.91, JSON_ARRAY('/uploads/products/vegan-protein-1.jpg'), 'Garden of Life', 0, 1, 89, 6),
(8, 6, 'Mancuernas Ajustables 20kg', 'mancuernas-ajustables-20kg', 'Set de mancuernas ajustables de 5 a 20kg cada una. Ideales para entrenamiento en casa.', 'Mancuernas ajustables profesionales', 'DUMB-ADJ-20KG', 299.90, 269.90, 15, 20.00, JSON_ARRAY('/uploads/products/dumbbells-1.jpg'), 'PowerBlock', 1, 1, 234, 8);

-- Insertar rutinas de ejemplo
INSERT INTO `routines` (`id`, `gym_id`, `instructor_id`, `client_id`, `name`, `description`, `objective`, `difficulty_level`, `duration_weeks`, `sessions_per_week`, `estimated_duration_minutes`, `is_template`, `is_public`, `is_active`, `tags`, `equipment_needed`) VALUES
(1, 1, 2, NULL, 'FullBody Principiante', 'Rutina completa para principiantes que trabaja todo el cuerpo en cada sesión', 'muscle_gain', 'beginner', 8, 3, 60, 1, 1, 1, JSON_ARRAY('principiante', 'fullbody', 'básico'), JSON_ARRAY('mancuernas', 'banca', 'barra')),
(2, 1, 2, NULL, 'HIIT Quema Grasa', 'Rutina de alta intensidad para quemar grasa y mejorar la condición cardiovascular', 'weight_loss', 'intermediate', 6, 4, 45, 1, 1, 1, JSON_ARRAY('hiit', 'cardio', 'quema grasa'), JSON_ARRAY('ninguno')),
(3, 1, 3, NULL, 'Fuerza Máxima Avanzado', 'Rutina especializada para desarrollo de fuerza máxima en los movimientos básicos', 'strength', 'advanced', 12, 4, 90, 1, 1, 1, JSON_ARRAY('fuerza', 'powerlifting', 'avanzado'), JSON_ARRAY('barra', 'discos', 'rack')),
(4, 1, 2, 4, 'Rutina Personal María', 'Rutina personalizada para María González enfocada en tonificación', 'muscle_gain', 'intermediate', 8, 3, 75, 0, 0, 1, JSON_ARRAY('personalizada', 'tonificación'), JSON_ARRAY('mancuernas', 'máquinas')),
(5, 1, 3, 5, 'Rutina Personal Diego', 'Rutina personalizada para Diego Fernández enfocada en fuerza', 'strength', 'beginner', 10, 3, 60, 0, 0, 1, JSON_ARRAY('personalizada', 'fuerza'), JSON_ARRAY('barra', 'mancuernas'));

-- Insertar ejercicios en rutinas
INSERT INTO `routine_exercises` (`routine_id`, `exercise_id`, `day_number`, `order_index`, `sets`, `reps`, `rest_seconds`, `notes`) VALUES
-- Rutina FullBody Principiante (Día 1)
(1, 2, 1, 1, 3, '12-15', 90, 'Enfócate en la técnica'),
(1, 1, 1, 2, 3, '10-12', 90, 'Controla el peso en la bajada'),
(1, 10, 1, 3, 3, '5-8', 120, 'Usa asistencia si es necesario'),
(1, 8, 1, 4, 3, '30-45 seg', 60, 'Mantén el cuerpo recto'),
-- Rutina HIIT Quema Grasa (Día 1)
(2, 4, 1, 1, 4, '30 seg', 30, 'Máxima intensidad'),
(2, 5, 1, 2, 4, '30 seg', 30, 'Mantén el ritmo alto'),
(2, 9, 1, 3, 4, '30 seg', 30, 'Aterriza suavemente'),
-- Rutina Fuerza Máxima (Día 1)
(3, 2, 1, 1, 5, '3-5', 180, 'Peso máximo con buena técnica'),
(3, 1, 1, 2, 5, '3-5', 180, 'Controla la barra'),
(3, 3, 1, 3, 5, '3-5', 180, 'Mantén la espalda neutra');

-- Insertar clases grupales
INSERT INTO `group_classes` (`id`, `gym_id`, `instructor_id`, `name`, `description`, `class_type`, `duration_minutes`, `max_participants`, `room`, `difficulty_level`, `price`, `is_active`) VALUES
(1, 1, 2, 'CrossFit Matutino', 'Clase de CrossFit de alta intensidad para empezar el día con energía', 'crossfit', 60, 15, 'Sala Funcional A', 'intermediate', 25.00, 1),
(2, 1, 3, 'Yoga Relajante', 'Sesión de yoga para relajar cuerpo y mente', 'yoga', 75, 20, 'Sala Yoga', 'beginner', 20.00, 1),
(3, 1, 2, 'HIIT Explosivo', 'Entrenamiento de intervalos de alta intensidad', 'hiit', 45, 12, 'Sala Cardio', 'advanced', 30.00, 1),
(4, 1, 3, 'Pilates Core', 'Fortalecimiento del core con ejercicios de Pilates', 'pilates', 50, 15, 'Sala Pilates', 'intermediate', 22.00, 1),
(5, 1, 2, 'Spinning Power', 'Clase de spinning de alta intensidad', 'spinning', 45, 25, 'Sala Spinning', 'intermediate', 18.00, 1);

-- Insertar horarios de clases
INSERT INTO `class_schedules` (`class_id`, `day_of_week`, `start_time`, `end_time`, `is_active`) VALUES
(1, 'monday', '07:00:00', '08:00:00', 1),
(1, 'wednesday', '07:00:00', '08:00:00', 1),
(1, 'friday', '07:00:00', '08:00:00', 1),
(2, 'tuesday', '19:00:00', '20:15:00', 1),
(2, 'thursday', '19:00:00', '20:15:00', 1),
(2, 'saturday', '10:00:00', '11:15:00', 1),
(3, 'monday', '18:00:00', '18:45:00', 1),
(3, 'wednesday', '18:00:00', '18:45:00', 1),
(3, 'friday', '18:00:00', '18:45:00', 1),
(4, 'tuesday', '17:00:00', '17:50:00', 1),
(4, 'thursday', '17:00:00', '17:50:00', 1),
(5, 'monday', '19:30:00', '20:15:00', 1),
(5, 'wednesday', '19:30:00', '20:15:00', 1),
(5, 'friday', '19:30:00', '20:15:00', 1),
(5, 'saturday', '09:00:00', '09:45:00', 1);

-- Insertar cupones de descuento
INSERT INTO `coupons` (`code`, `type`, `value`, `minimum_amount`, `usage_limit`, `valid_from`, `valid_until`, `is_active`) VALUES
('BIENVENIDO20', 'percentage', 20.00, 50.00, 100, '2024-01-01 00:00:00', '2025-12-31 23:59:59', 1),
('PRIMERACOMPRA', 'fixed', 15.00, 30.00, 200, '2024-01-01 00:00:00', '2025-12-31 23:59:59', 1),
('VERANO2024', 'percentage', 15.00, 100.00, 50, '2024-12-01 00:00:00', '2025-03-31 23:59:59', 1),
('PROTEINA10', 'percentage', 10.00, 0.00, NULL, '2024-01-01 00:00:00', '2025-12-31 23:59:59', 1);

-- Insertar testimonios
INSERT INTO `testimonials` (`name`, `role`, `text`, `rating`, `is_active`, `sort_order`) VALUES
('María González', 'Miembro Premium', 'StyloFitness cambió mi vida completamente. Los entrenadores son excepcionales y las instalaciones de primera calidad. He logrado todos mis objetivos fitness.', 5, 1, 1),
('Diego Fernández', 'Atleta Amateur', 'El mejor gimnasio de Lima. La variedad de clases y el equipamiento moderno hacen que cada entrenamiento sea perfecto. Totalmente recomendado.', 5, 1, 2),
('Lucía Torres', 'Instructora de Yoga', 'Como instructora, puedo decir que StyloFitness tiene los estándares más altos. La comunidad es increíble y el ambiente muy motivador.', 5, 1, 3),
('Carlos Mendoza', 'Empresario', 'Llevo 2 años entrenando aquí y no cambiaría por nada. La atención personalizada y los resultados hablan por sí solos.', 5, 1, 4);

-- Insertar ofertas especiales
INSERT INTO `special_offers` (`title`, `subtitle`, `description`, `discount_percentage`, `image`, `button_text`, `button_link`, `valid_from`, `valid_until`, `is_active`, `sort_order`) VALUES
('¡Membresía Premium 50% OFF!', 'Únete ahora y transforma tu cuerpo', 'Acceso completo a todas las instalaciones, clases grupales ilimitadas y entrenamiento personalizado incluido.', 50.00, '/uploads/offers/premium-offer.jpg', 'Obtener Oferta', '/memberships', '2024-12-01 00:00:00', '2025-01-31 23:59:59', 1, 1),
('Pack Suplementos Starter', 'Todo lo que necesitas para empezar', 'Proteína + Pre-entreno + Multivitamínico con descuento especial para nuevos miembros.', 30.00, '/uploads/offers/supplements-pack.jpg', 'Ver Pack', '/store/category/proteinas', '2024-12-01 00:00:00', '2025-02-28 23:59:59', 1, 2);

-- Insertar sección "Por qué elegirnos"
INSERT INTO `why_choose_us` (`title`, `description`, `icon`, `is_active`, `sort_order`) VALUES
('Equipamiento de Última Generación', 'Contamos con las máquinas más modernas y tecnología de punta para maximizar tus resultados.', 'fas fa-cogs', 1, 1),
('Entrenadores Certificados', 'Nuestro equipo de profesionales certificados te guiará en cada paso de tu transformación.', 'fas fa-user-graduate', 1, 2),
('Clases Grupales Variadas', 'Más de 20 tipos de clases diferentes para que nunca te aburras y siempre encuentres motivación.', 'fas fa-users', 1, 3),
('Horarios Flexibles', 'Abierto desde las 6:00 AM hasta las 11:00 PM para adaptarnos a tu estilo de vida.', 'fas fa-clock', 1, 4),
('Nutrición Personalizada', 'Planes nutricionales diseñados específicamente para tus objetivos y estilo de vida.', 'fas fa-apple-alt', 1, 5),
('Comunidad Motivadora', 'Únete a una comunidad de personas comprometidas con un estilo de vida saludable.', 'fas fa-heart', 1, 6);

-- Insertar configuración de landing page
INSERT INTO `landing_page_config` (`section_name`, `title`, `subtitle`, `content`, `button_text`, `button_link`, `is_active`, `sort_order`) VALUES
('hero', 'TRANSFORMA TU CUERPO, TRANSFORMA TU VIDA', 'El gimnasio más completo de Lima con tecnología de punta y entrenadores certificados', 'Únete a la revolución fitness y descubre tu mejor versión con nuestros programas personalizados.', 'Comenzar Ahora', '/register', 1, 1),
('about', 'Más de 10 años transformando vidas', 'StyloFitness es el líder en fitness premium en Perú', 'Con más de 10,000 miembros satisfechos, somos el gimnasio de confianza para quienes buscan resultados reales y duraderos.', 'Conocer Más', '/about', 1, 2),
('cta', '¿Listo para el cambio?', 'Comienza tu transformación hoy mismo', 'No esperes más. Tu mejor versión te está esperando.', 'Únete Ahora', '/memberships', 1, 3);

-- Insertar configuraciones del sistema
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `setting_group`, `is_public`) VALUES
('site_name', 'StyloFitness', 'string', 'Nombre del sitio web', 'general', 1),
('site_description', 'El gimnasio más completo de Lima', 'string', 'Descripción del sitio', 'general', 1),
('contact_email', 'info@stylofitness.com', 'string', 'Email de contacto principal', 'contact', 1),
('contact_phone', '+51 999 888 777', 'string', 'Teléfono de contacto', 'contact', 1),
('address', 'Av. Javier Prado Este 4200, San Borja, Lima', 'string', 'Dirección principal', 'contact', 1),
('currency', 'PEN', 'string', 'Moneda del sistema', 'ecommerce', 1),
('tax_rate', '18.00', 'string', 'Tasa de impuestos (%)', 'ecommerce', 0),
('shipping_cost', '15.00', 'string', 'Costo de envío estándar', 'ecommerce', 1),
('free_shipping_minimum', '100.00', 'string', 'Monto mínimo para envío gratis', 'ecommerce', 1),
('maintenance_mode', 'false', 'boolean', 'Modo de mantenimiento', 'system', 0),
('registration_enabled', 'true', 'boolean', 'Permitir registro de usuarios', 'system', 0),
('max_file_upload_size', '10', 'integer', 'Tamaño máximo de archivo (MB)', 'system', 0);

-- ==========================================
-- TRIGGERS Y PROCEDIMIENTOS
-- ==========================================

-- Trigger para actualizar rating promedio de productos
DELIMITER //
CREATE TRIGGER update_product_rating AFTER INSERT ON product_reviews
FOR EACH ROW
BEGIN
    UPDATE products 
    SET avg_rating = (
        SELECT AVG(rating) 
        FROM product_reviews 
        WHERE product_id = NEW.product_id AND is_approved = 1
    ),
    reviews_count = (
        SELECT COUNT(*) 
        FROM product_reviews 
        WHERE product_id = NEW.product_id AND is_approved = 1
    )
    WHERE id = NEW.product_id;
END//
DELIMITER ;

-- Trigger para actualizar stock después de una orden
DELIMITER //
CREATE TRIGGER update_stock_after_order AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock_quantity = stock_quantity - NEW.quantity,
        sales_count = sales_count + NEW.quantity
    WHERE id = NEW.product_id;
END//
DELIMITER ;

-- Procedimiento para obtener estadísticas del dashboard
DELIMITER //
CREATE PROCEDURE GetDashboardStats()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM users WHERE role = 'client' AND is_active = 1) as total_clients,
        (SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()) as today_orders,
        (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE DATE(created_at) = CURDATE()) as today_revenue,
        (SELECT COUNT(*) FROM class_bookings WHERE booking_date = CURDATE()) as today_bookings,
        (SELECT COUNT(*) FROM products WHERE stock_quantity <= min_stock_level) as low_stock_products;
END//
DELIMITER ;

-- Procedimiento para limpiar datos antiguos
DELIMITER //
CREATE PROCEDURE CleanOldData()
BEGIN
    -- Eliminar tokens expirados
    DELETE FROM security_tokens WHERE expires_at < NOW();
    
    -- Eliminar logs de actividad antiguos (más de 6 meses)
    DELETE FROM user_activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
    
    -- Eliminar notificaciones leídas antiguas (más de 3 meses)
    DELETE FROM notifications WHERE is_read = 1 AND read_at < DATE_SUB(NOW(), INTERVAL 3 MONTH);
END//
DELIMITER ;

-- ==========================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ==========================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_products_category_featured ON products(category_id, is_featured, is_active);
CREATE INDEX idx_products_price_range ON products(price, is_active);
CREATE INDEX idx_orders_user_status ON orders(user_id, status, created_at);
CREATE INDEX idx_workout_logs_user_date ON workout_logs(user_id, workout_date);
CREATE INDEX idx_class_bookings_user_date ON class_bookings(user_id, booking_date, status);
CREATE INDEX idx_routine_exercises_routine_day ON routine_exercises(routine_id, day_number, order_index);

-- ==========================================
-- VISTAS PARA CONSULTAS COMUNES
-- ==========================================

-- Vista para productos con información de categoría
CREATE VIEW product_details AS
SELECT 
    p.*,
    pc.name as category_name,
    pc.slug as category_slug,
    CASE 
        WHEN p.sale_price IS NOT NULL AND p.sale_price > 0 
        THEN p.sale_price 
        ELSE p.price 
    END as final_price,
    CASE 
        WHEN p.sale_price IS NOT NULL AND p.sale_price > 0 
        THEN ROUND(((p.price - p.sale_price) / p.price) * 100, 0)
        ELSE 0 
    END as discount_percentage
FROM products p
LEFT JOIN product_categories pc ON p.category_id = pc.id;

-- Vista para rutinas con información del instructor
CREATE VIEW routine_details AS
SELECT 
    r.*,
    CONCAT(u.first_name, ' ', u.last_name) as instructor_name,
    u.profile_image as instructor_image,
    g.name as gym_name,
    (SELECT COUNT(*) FROM routine_exercises re WHERE re.routine_id = r.id) as total_exercises
FROM routines r
LEFT JOIN users u ON r.instructor_id = u.id
LEFT JOIN gyms g ON r.gym_id = g.id;

-- Vista para clases con información del instructor
CREATE VIEW class_details AS
SELECT 
    gc.*,
    CONCAT(u.first_name, ' ', u.last_name) as instructor_name,
    u.profile_image as instructor_image,
    g.name as gym_name
FROM group_classes gc
LEFT JOIN users u ON gc.instructor_id = u.id
LEFT JOIN gyms g ON gc.gym_id = g.id;

-- ==========================================
-- FINALIZACIÓN
-- ==========================================

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ==========================================
-- NOTAS DE INSTALACIÓN
-- ==========================================
/*
PARA INSTALAR ESTA BASE DE DATOS:

1. Crear la base de datos:
   CREATE DATABASE stylofitness_gym CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

2. Seleccionar la base de datos:
   USE stylofitness_gym;

3. Ejecutar este archivo SQL completo

4. Verificar que todas las tablas se crearon correctamente:
   SHOW TABLES;

5. Verificar que los datos de prueba se insertaron:
   SELECT COUNT(*) FROM users;
   SELECT COUNT(*) FROM products;
   SELECT COUNT(*) FROM exercises;

CREDENCIALES DE ACCESO:
- Admin: admin@stylofitness.com / password
- Staff: staff@stylofitness.com / password
- Instructor: carlos@stylofitness.com / password
- Cliente: maria@email.com / password

NOTAS IMPORTANTES:
- Todas las contraseñas están hasheadas con bcrypt
- La contraseña por defecto es 'password' para todos los usuarios
- Los datos de prueba incluyen productos, ejercicios, rutinas y clases
- Las imágenes referenciadas deben subirse a las carpetas correspondientes
- Se recomienda cambiar las contraseñas en producción
*/

-- FIN DEL ARCHIVO