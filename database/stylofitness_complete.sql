-- ==========================================
-- STYLOFITNESS - ESQUEMA COMPLETO DE BASE DE DATOS
-- Aplicación Web Profesional para Gimnasios
-- Version: 2.1.0 - Corregido y Optimizado
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

CREATE DATABASE IF NOT EXISTS `stylofitness_gym` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `stylofitness_gym`;

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
  `booking_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text,
  `instructor_rating` tinyint(1),
  `difficulty_rating` tinyint(1),
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_review` (`booking_id`),
  KEY `idx_class_id` (`class_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_rating` (`rating`),
  KEY `idx_approved` (`is_approved`),
  CONSTRAINT `fk_class_reviews_class` FOREIGN KEY (`class_id`) REFERENCES `group_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_reviews_booking` FOREIGN KEY (`booking_id`) REFERENCES `class_bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLAS DEL SISTEMA
-- ==========================================

-- Tabla de logs de actividad
CREATE TABLE IF NOT EXISTS `user_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11),
  `action` varchar(100) NOT NULL,
  `resource_type` varchar(50),
  `resource_id` int(11),
  `details` json,
  `ip_address` varchar(45),
  `user_agent` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_resource_type` (`resource_type`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_user_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de configuraciones del sistema
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext,
  `setting_type` enum('string','integer','boolean','json','text') DEFAULT 'string',
  `description` text,
  `setting_group` varchar(50) DEFAULT 'general',
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_setting_key` (`setting_key`),
  KEY `idx_setting_group` (`setting_group`),
  KEY `idx_is_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` json,
  `action_url` varchar(255),
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_type` (`type`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_expires_at` (`expires_at`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de archivos multimedia
CREATE TABLE IF NOT EXISTS `media_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_type` enum('image','video','audio','document','other') NOT NULL,
  `dimensions` varchar(20),
  `duration` int(11),
  `description` text,
  `alt_text` varchar(255),
  `uploaded_by` int(11),
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  KEY `idx_file_type` (`file_type`),
  KEY `idx_mime_type` (`mime_type`),
  KEY `idx_is_public` (`is_public`),
  CONSTRAINT `fk_media_files_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
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
  `template` varchar(100),
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
-- DATOS INICIALES COMPLETOS
-- ==========================================

-- Insertar sede principal
INSERT INTO `gyms` (`id`, `name`, `address`, `phone`, `email`, `theme_colors`, `settings`, `operating_hours`, `social_media`) VALUES
(1, 'STYLOFITNESS Principal', 'Av. Principal 123, San Isidro, Lima', '+51 999 888 777', 'info@stylofitness.com', 
 JSON_OBJECT(
   'primary', '#FF6B00',
   'secondary', '#E55A00', 
   'accent', '#FFB366',
   'dark', '#2C2C2C',
   'light', '#F8F9FA',
   'success', '#28A745',
   'warning', '#FFC107',
   'error', '#DC3545'
 ),
 JSON_OBJECT(
   'currency', 'PEN',
   'timezone', 'America/Lima',
   'language', 'es',
   'tax_rate', 0.18,
   'membership_duration_days', 30
 ),
 JSON_OBJECT(
   'monday', JSON_OBJECT('open', '06:00', 'close', '23:00'),
   'tuesday', JSON_OBJECT('open', '06:00', 'close', '23:00'),
   'wednesday', JSON_OBJECT('open', '06:00', 'close', '23:00'),
   'thursday', JSON_OBJECT('open', '06:00', 'close', '23:00'),
   'friday', JSON_OBJECT('open', '06:00', 'close', '23:00'),
   'saturday', JSON_OBJECT('open', '07:00', 'close', '22:00'),
   'sunday', JSON_OBJECT('open', '08:00', 'close', '20:00')
 ),
 JSON_OBJECT(
   'facebook', 'https://facebook.com/stylofitness',
   'instagram', 'https://instagram.com/stylofitness',
   'youtube', 'https://youtube.com/stylofitness',
   'tiktok', 'https://tiktok.com/@stylofitness'
 ));

-- Insertar usuarios de ejemplo
INSERT INTO `users` (`id`, `gym_id`, `username`, `email`, `password`, `first_name`, `last_name`, `role`, `is_active`, `membership_type`, `membership_expires`) VALUES
(1, 1, 'admin', 'admin@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Principal', 'admin', 1, 'premium', '2030-12-31'),
(2, 1, 'instructor', 'instructor@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Fitness', 'instructor', 1, 'premium', '2030-12-31'),
(3, 1, 'cliente', 'cliente@stylofitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'González', 'client', 1, 'basic', '2025-07-31');

-- Insertar categorías de ejercicios
INSERT INTO `exercise_categories` (`id`, `name`, `description`, `icon`, `color`, `sort_order`) VALUES
(1, 'Fuerza', 'Ejercicios de fuerza y resistencia muscular para desarrollo y tonificación', 'dumbbell', '#FF6B00', 1),
(2, 'Cardio', 'Ejercicios cardiovasculares para mejorar resistencia y quema de calorías', 'heart', '#E55A00', 2),
(3, 'Flexibilidad', 'Estiramientos, yoga y ejercicios de movilidad articular', 'wind', '#FFB366', 3),
(4, 'Funcional', 'Entrenamiento funcional y movimientos compuestos naturales', 'activity', '#FF8533', 4),
(5, 'Core', 'Ejercicios específicos para fortalecer el núcleo y estabilidad', 'target', '#CC5500', 5),
(6, 'Pliométricos', 'Ejercicios explosivos para desarrollo de potencia y agilidad', 'zap', '#FF4500', 6),
(7, 'Rehabilitación', 'Ejercicios terapéuticos para recuperación y prevención de lesiones', 'shield', '#28A745', 7);

-- Insertar ejercicios completos
INSERT INTO `exercises` (`category_id`, `name`, `description`, `instructions`, `muscle_groups`, `difficulty_level`, `equipment_needed`, `duration_minutes`, `calories_burned`, `tags`, `created_by`) VALUES
-- EJERCICIOS DE FUERZA
(1, 'Press de Banca Plano', 'Ejercicio fundamental para el desarrollo del pectoral mayor, triceps y deltoides anterior', 'Acuéstate en el banco con los pies firmes en el suelo. Agarra la barra con las manos ligeramente más separadas que el ancho de los hombros. Baja la barra controladamente hasta tocar el pecho, luego empuja hacia arriba hasta extensión completa de los brazos.', JSON_ARRAY('pectorales', 'triceps', 'deltoides anteriores'), 'intermediate', 'Banca, Barra, Discos', 3, 15, JSON_ARRAY('pecho', 'fuerza', 'básico', 'compound'), 2),
(1, 'Sentadilla Trasera', 'Ejercicio rey para el desarrollo de piernas y glúteos', 'Coloca la barra en tus trapecios, mantén los pies separados al ancho de hombros. Desciende flexionando las rodillas hasta que los muslos estén paralelos al suelo, manteniendo la espalda recta. Sube empujando con los talones.', JSON_ARRAY('cuádriceps', 'glúteos', 'isquiotibiales', 'core'), 'intermediate', 'Barra, Discos, Rack de Sentadillas', 4, 25, JSON_ARRAY('piernas', 'glúteos', 'compound', 'básico'), 2),
(1, 'Peso Muerto Convencional', 'Ejercicio compuesto para toda la cadena posterior', 'Con los pies separados al ancho de cadera, agarra la barra con agarre mixto. Mantén la espalda recta y levanta la barra extendiendo las caderas y rodillas simultáneamente. Contrae los glúteos en la parte superior.', JSON_ARRAY('espalda baja', 'glúteos', 'isquiotibiales', 'trapecios'), 'advanced', 'Barra, Discos', 5, 30, JSON_ARRAY('espalda', 'glúteos', 'compound', 'potencia'), 2),
(1, 'Dominadas', 'Ejercicio de tracción vertical con peso corporal', 'Cuélgate de la barra con agarre prono, manos separadas al ancho de hombros. Tira de tu cuerpo hacia arriba hasta que la barbilla pase la barra. Baja controladamente hasta extensión completa.', JSON_ARRAY('dorsales', 'bíceps', 'romboides', 'trapecio medio'), 'advanced', 'Barra de Dominadas', 2, 12, JSON_ARRAY('espalda', 'calistenia', 'tracción'), 2),
(1, 'Press Militar', 'Desarrollo de hombros de pie con barra', 'De pie con los pies separados al ancho de hombros, coloca la barra a la altura de los hombros. Empuja la barra hacia arriba manteniéndola en línea con tu cabeza. Baja controladamente.', JSON_ARRAY('deltoides', 'triceps', 'core'), 'intermediate', 'Barra, Discos', 3, 18, JSON_ARRAY('hombros', 'core', 'estabilidad'), 2),
-- EJERCICIOS DE CARDIO
(2, 'Burpees', 'Ejercicio de cuerpo completo de alta intensidad', 'Desde posición de pie, baja a cuclillas y coloca las manos en el suelo. Lleva los pies hacia atrás a posición de plancha, haz una flexión, regresa a cuclillas y salta hacia arriba con los brazos extendidos.', JSON_ARRAY('cuerpo completo'), 'intermediate', 'Ninguno', 1, 15, JSON_ARRAY('hiit', 'funcional', 'cardio', 'quema grasa'), 2),
(2, 'Mountain Climbers', 'Ejercicio cardiovascular dinámico', 'En posición de plancha alta, alterna llevando las rodillas hacia el pecho de forma rápida y controlada. Mantén las caderas estables y el core activado durante todo el movimiento.', JSON_ARRAY('core', 'hombros', 'piernas'), 'beginner', 'Ninguno', 1, 12, JSON_ARRAY('cardio', 'core', 'agilidad'), 2),
(2, 'Sprint en Cinta', 'Carrera de alta intensidad', 'Corre a máxima velocidad durante intervalos cortos, manteniendo una postura corporal correcta. Alterna con períodos de recuperación activa.', JSON_ARRAY('piernas', 'sistema cardiovascular'), 'advanced', 'Cinta de Correr', 1, 20, JSON_ARRAY('velocidad', 'intervalos', 'hiit'), 2),
-- EJERCICIOS DE FLEXIBILIDAD
(3, 'Estiramiento de Isquiotibiales', 'Estiramiento para la parte posterior del muslo', 'Sentado en el suelo, extiende una pierna y flexiona la otra. Inclínate hacia adelante sobre la pierna extendida, manteniendo la espalda recta. Sostén el estiramiento.', JSON_ARRAY('isquiotibiales', 'espalda baja'), 'beginner', 'Colchoneta', 2, 3, JSON_ARRAY('flexibilidad', 'recuperación', 'estiramiento'), 2),
(3, 'Cobra Yoga', 'Postura de yoga para flexibilidad de espalda', 'Acuéstate boca abajo, coloca las palmas bajo los hombros. Empuja el torso hacia arriba arqueando la espalda, manteniendo las caderas en el suelo.', JSON_ARRAY('espalda', 'core', 'hombros'), 'beginner', 'Colchoneta', 2, 5, JSON_ARRAY('yoga', 'movilidad', 'espalda'), 2),
-- EJERCICIOS FUNCIONALES
(4, 'Kettlebell Swing', 'Movimiento balístico con kettlebell', 'Con los pies separados, agarra la kettlebell con ambas manos. Flexiona las caderas hacia atrás y balancea la kettlebell entre las piernas. Extiende las caderas explosivamente para balancear la kettlebell a la altura del pecho.', JSON_ARRAY('glúteos', 'core', 'hombros'), 'intermediate', 'Kettlebell', 1, 18, JSON_ARRAY('funcional', 'potencia', 'cardio'), 2),
(4, 'Farmer\'s Walk', 'Caminata funcional con peso', 'Agarra pesos pesados en cada mano y camina manteniendo una postura erguida, hombros hacia atrás y core activado. Da pasos controlados y respira normalmente.', JSON_ARRAY('core', 'trapecios', 'antebrazos', 'piernas'), 'intermediate', 'Mancuernas o Kettlebells', 3, 12, JSON_ARRAY('funcional', 'grip', 'core'), 2),
-- EJERCICIOS DE CORE
(5, 'Plancha Frontal', 'Ejercicio isométrico fundamental para core', 'Apóyate en antebrazos y pies, manteniendo el cuerpo en línea recta desde la cabeza hasta los talones. Contrae el abdomen y glúteos. Respira normalmente.', JSON_ARRAY('core', 'hombros', 'glúteos'), 'beginner', 'Colchoneta', 2, 8, JSON_ARRAY('core', 'estabilidad', 'isométrico'), 2),
(5, 'Dead Bug', 'Ejercicio de control motor para core', 'Acuéstate boca arriba con brazos extendidos y rodillas flexionadas a 90°. Extiende lentamente brazo opuesto y pierna, mantén la posición y regresa controladamente.', JSON_ARRAY('core', 'estabilizadores'), 'beginner', 'Colchoneta', 2, 6, JSON_ARRAY('core', 'estabilidad', 'control'), 2),
-- EJERCICIOS PLIOMÉTRICOS
(6, 'Saltos al Cajón', 'Ejercicio pliométrico para potencia de piernas', 'Desde posición de pie frente a un cajón, salta explosivamente aterrizando suavemente con ambos pies en el cajón. Baja controladamente y repite.', JSON_ARRAY('piernas', 'glúteos', 'potencia'), 'intermediate', 'Cajón Pliométrico', 1, 15, JSON_ARRAY('pliométrico', 'potencia', 'salto'), 2),
(6, 'Flexiones Explosivas', 'Flexiones con componente pliométrico', 'Realiza una flexión normal pero en la fase concéntrica empuja explosivamente para que las manos se separen del suelo. Aterriza suavemente y repite.', JSON_ARRAY('pectorales', 'triceps', 'core'), 'advanced', 'Ninguno', 1, 12, JSON_ARRAY('pliométrico', 'potencia', 'upper body'), 2);

-- Insertar categorías de productos
INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`, `image_url`, `sort_order`) VALUES
(1, 'Proteínas', 'proteinas', 'Suplementos proteicos para desarrollo muscular y recuperación', '/images/categories/proteins.jpg', 1),
(2, 'Pre-entrenos', 'pre-entrenos', 'Suplementos pre-entreno para energía y rendimiento óptimo', '/images/categories/pre-workout.jpg', 2),
(3, 'Vitaminas y Minerales', 'vitaminas-minerales', 'Vitaminas, minerales y micronutrientes esenciales', '/images/categories/vitamins.jpg', 3),
(4, 'Creatina', 'creatina', 'Suplementos de creatina para fuerza, potencia y volumen muscular', '/images/categories/creatine.jpg', 4),
(5, 'Quemadores de Grasa', 'quemadores-grasa', 'Suplementos termogénicos para pérdida de peso', '/images/categories/fat-burners.jpg', 5),
(6, 'Aminoácidos', 'aminoacidos', 'BCAA, EAA y aminoácidos esenciales para recuperación', '/images/categories/amino-acids.jpg', 6),
(7, 'Accesorios de Entrenamiento', 'accesorios-entrenamiento', 'Accesorios y equipos para optimizar tu entrenamiento', '/images/categories/accessories.jpg', 7),
(8, 'Ropa Deportiva', 'ropa-deportiva', 'Ropa y calzado deportivo de alta calidad', '/images/categories/clothing.jpg', 8);

-- Insertar productos destacados
INSERT INTO `products` (`category_id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `sale_price`, `stock_quantity`, `weight`, `images`, `brand`, `is_featured`) VALUES
(1, 'Whey Protein Gold Standard 2.5kg', 'whey-protein-gold-standard-2-5kg', 'La proteína de suero más vendida del mundo. Whey Protein Gold Standard ofrece 24g de proteína de alta calidad por porción, con excelente sabor y disolución.', 'Proteína whey premium con 24g por porción - Sabor Chocolate', 'WP-GS-001', 289.90, 259.90, 45, 2.50, JSON_ARRAY('/images/products/whey-gold-1.jpg'), 'Optimum Nutrition', 1),
(2, 'C4 Original Pre-Workout 390g', 'c4-original-pre-workout-390g', 'El pre-entreno más popular del mundo. C4 Original combina ingredientes científicamente probados como beta-alanina, creatina y cafeína.', 'Pre-entreno #1 mundial - Energía explosiva garantizada', 'PRE-C4-001', 149.90, 129.90, 25, 0.39, JSON_ARRAY('/images/products/c4-original-1.jpg'), 'Cellucor', 1),
(3, 'Multivitamínico Complete Sport', 'multivitaminico-complete-sport', 'Complejo vitamínico completo diseñado específicamente para deportistas. Contiene 25 vitaminas y minerales esenciales plus antioxidantes.', 'Multivitamínico específico para deportistas - 90 cápsulas', 'VIT-COMP-001', 89.90, 79.90, 80, 0.15, JSON_ARRAY('/images/products/multivit-1.jpg'), 'Universal Nutrition', 0),
(4, 'Creatina Monohidrato Micronizada 500g', 'creatina-monohidrato-micronizada-500g', 'Creatina monohidrato pura micronizada para mejor disolución. Aumenta fuerza, potencia y volumen muscular. Sin sabor, sin aditivos.', 'Creatina pura micronizada - Sin sabor, máxima pureza', 'CREAT-MONO-001', 79.90, NULL, 90, 0.50, JSON_ARRAY('/images/products/creatine-1.jpg'), 'Creapure', 1);

-- Insertar clases grupales
INSERT INTO `group_classes` (`gym_id`, `instructor_id`, `name`, `description`, `class_type`, `duration_minutes`, `max_participants`, `room`, `difficulty_level`, `price`, `benefits`) VALUES
(1, 2, 'CrossFit WOD', 'Entrenamiento funcional de alta intensidad que combina ejercicios de gimnasia, halterofilia y cardio', 'crossfit', 60, 15, 'Sala CrossFit', 'intermediate', 25.00, 'Mejora fuerza, resistencia, coordinación y composición corporal'),
(1, 2, 'Yoga Flow', 'Clase de yoga dinámico que conecta movimiento con respiración', 'yoga', 75, 20, 'Sala Yoga', 'beginner', 20.00, 'Flexibilidad, relajación, equilibrio mental y físico'),
(1, 2, 'HIIT Cardio', 'Entrenamiento cardiovascular de intervalos de alta intensidad', 'hiit', 45, 25, 'Sala Cardio', 'intermediate', 22.00, 'Quema de grasa, mejora cardiovascular, tonificación'),
(1, 2, 'Spinning Power', 'Clase de ciclismo indoor con música motivadora y diferentes intensidades', 'spinning', 50, 30, 'Sala Spinning', 'intermediate', 18.00, 'Resistencia cardiovascular, quema de calorías, fortalecimiento de piernas');

-- Insertar horarios de clases
INSERT INTO `class_schedules` (`class_id`, `day_of_week`, `start_time`, `end_time`, `is_recurring`) VALUES
(1, 'monday', '07:00:00', '08:00:00', 1),
(1, 'wednesday', '07:00:00', '08:00:00', 1),
(1, 'friday', '07:00:00', '08:00:00', 1),
(2, 'tuesday', '18:30:00', '19:45:00', 1),
(2, 'thursday', '18:30:00', '19:45:00', 1),
(3, 'monday', '06:30:00', '07:15:00', 1),
(3, 'wednesday', '06:30:00', '07:15:00', 1),
(3, 'friday', '06:30:00', '07:15:00', 1),
(4, 'monday', '08:00:00', '08:50:00', 1),
(4, 'wednesday', '08:00:00', '08:50:00', 1),
(4, 'friday', '08:00:00', '08:50:00', 1);

-- Insertar configuraciones del sistema
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `setting_group`, `is_public`) VALUES
('site_name', 'STYLOFITNESS', 'string', 'Nombre del sitio web', 'general', 1),
('site_description', 'Gimnasio profesional con rutinas personalizadas y tienda de suplementos', 'string', 'Descripción del sitio', 'general', 1),
('contact_email', 'info@stylofitness.com', 'string', 'Email de contacto principal', 'general', 1),
('contact_phone', '+51 999 888 777', 'string', 'Teléfono de contacto', 'general', 1),
('maintenance_mode', '0', 'boolean', 'Modo de mantenimiento', 'general', 0),
('registration_enabled', '1', 'boolean', 'Permitir registro de usuarios', 'users', 0),
('currency_symbol', 'S/', 'string', 'Símbolo de moneda', 'store', 1),
('tax_rate', '0.18', 'string', 'Tasa de impuestos (IGV)', 'store', 0),
('free_shipping_minimum', '150', 'string', 'Monto mínimo para envío gratis', 'store', 1),
('store_enabled', '1', 'boolean', 'Habilitar tienda online', 'store', 1);

-- ==========================================
-- RUTINAS PREDEFINIDAS PROFESIONALES
-- ==========================================

-- RUTINA 1: FULLBODY PRINCIPIANTE
INSERT INTO `routines` (`gym_id`, `instructor_id`, `name`, `description`, `objective`, `difficulty_level`, `duration_weeks`, `sessions_per_week`, `estimated_duration_minutes`, `is_template`, `is_public`) VALUES
(1, 2, 'FullBody Principiante', 'Rutina completa de cuerpo para principiantes. Incluye los ejercicios fundamentales para desarrollar fuerza base, técnica correcta y crear el hábito de entrenamiento.', 'muscle_gain', 'beginner', 8, 3, 60, 1, 1);

SET @fullbody_beginner_id = LAST_INSERT_ID();

-- Ejercicios para FullBody Principiante
INSERT INTO `routine_exercises` (`routine_id`, `exercise_id`, `day_number`, `order_index`, `sets`, `reps`, `rest_seconds`, `notes`) VALUES
(@fullbody_beginner_id, 1, 1, 1, 3, '8-10', 90, 'Ejercicio principal. Enfócate en la técnica correcta.'),
(@fullbody_beginner_id, 4, 1, 2, 3, '5-8', 90, 'Usa banda elástica de asistencia si es necesario.'),
(@fullbody_beginner_id, 5, 1, 3, 3, '8-10', 75, 'Mantén el core activado durante el movimiento.'),
(@fullbody_beginner_id, 13, 1, 4, 3, '30-45 seg', 60, 'Progresa gradualmente en tiempo.');

-- RUTINA 2: HIIT QUEMA GRASA
INSERT INTO `routines` (`gym_id`, `instructor_id`, `name`, `description`, `objective`, `difficulty_level`, `duration_weeks`, `sessions_per_week`, `estimated_duration_minutes`, `is_template`, `is_public`) VALUES
(1, 2, 'HIIT Quema Grasa Extrema', 'Rutina de alta intensidad diseñada para máxima quema de calorías y pérdida de grasa. Combina entrenamiento de fuerza con cardio intensivo.', 'weight_loss', 'intermediate', 12, 5, 45, 1, 1);

SET @hiit_fat_burn_id = LAST_INSERT_ID();

-- Ejercicios para HIIT Quema Grasa
INSERT INTO `routine_exercises` (`routine_id`, `exercise_id`, `day_number`, `order_index`, `sets`, `reps`, `rest_seconds`, `notes`) VALUES
(@hiit_fat_burn_id, 6, 1, 1, 4, '30 seg', 15, 'Máxima intensidad, trabajo-descanso 30:15'),
(@hiit_fat_burn_id, 1, 1, 2, 4, '12-15', 15, 'Peso moderado, alta velocidad'),
(@hiit_fat_burn_id, 7, 1, 3, 4, '20 seg', 15, 'Alternación rápida'),
(@hiit_fat_burn_id, 4, 1, 4, 4, '8-12', 15, 'Asistidas si es necesario');

-- RUTINA 3: FUERZA MÁXIMA
INSERT INTO `routines` (`gym_id`, `instructor_id`, `name`, `description`, `objective`, `difficulty_level`, `duration_weeks`, `sessions_per_week`, `estimated_duration_minutes`, `is_template`, `is_public`) VALUES
(1, 2, 'Fuerza Máxima Avanzada', 'Rutina de fuerza para atletas experimentados. Enfocada en desarrollo de fuerza máxima mediante trabajo pesado en movimientos fundamentales.', 'strength', 'advanced', 16, 4, 90, 1, 1);

SET @strength_advanced_id = LAST_INSERT_ID();

-- Ejercicios para Fuerza Máxima
INSERT INTO `routine_exercises` (`routine_id`, `exercise_id`, `day_number`, `order_index`, `sets`, `reps`, `rest_seconds`, `notes`) VALUES
(@strength_advanced_id, 1, 1, 1, 5, '3-5', 180, 'Fuerza máxima: 85-95% 1RM. Descansos largos.'),
(@strength_advanced_id, 5, 1, 2, 4, '3-5', 150, 'Trabajo auxiliar pesado: 80-90% 1RM.'),
(@strength_advanced_id, 4, 1, 3, 3, '6-8', 120, 'Con peso adicional si es posible.'),
(@strength_advanced_id, 3, 2, 1, 5, '1-3', 240, 'Máxima carga: 90-100% 1RM.'),
(@strength_advanced_id, 2, 3, 1, 5, '1-5', 180, 'Sentadilla máxima: 85-100% 1RM.');

-- Insertar cupones promocionales
INSERT INTO `coupons` (`code`, `type`, `value`, `minimum_amount`, `usage_limit`, `valid_from`, `valid_until`, `is_active`) VALUES
('BIENVENIDO20', 'percentage', 20.00, 100.00, 100, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), 1),
('ENVIOGRATIS', 'fixed', 15.00, 80.00, 500, NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH), 1),
('PRIMERACOMPRA', 'percentage', 15.00, 50.00, 1000, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 1);

-- Insertar páginas CMS básicas
INSERT INTO `cms_pages` (`title`, `slug`, `content`, `excerpt`, `status`, `author_id`, `published_at`) VALUES
('Acerca de STYLOFITNESS', 'acerca-de', '<h1>Bienvenido a STYLOFITNESS</h1><p>Somos tu partner en el camino hacia una vida más saludable y activa.</p>', 'Conoce más sobre STYLOFITNESS', 'published', 1, NOW()),
('Política de Privacidad', 'politica-privacidad', '<h1>Política de Privacidad</h1><p>Tu privacidad es importante para nosotros.</p>', 'Política de privacidad y protección de datos', 'published', 1, NOW()),
('Términos y Condiciones', 'terminos-condiciones', '<h1>Términos y Condiciones</h1><p>Al usar nuestros servicios, aceptas estos términos.</p>', 'Términos y condiciones de uso', 'published', 1, NOW());

-- ==========================================
-- TRIGGERS Y PROCEDIMIENTOS ALMACENADOS
-- ==========================================

-- Trigger para actualizar rating de productos
DELIMITER //
CREATE TRIGGER `trg_update_product_rating` AFTER INSERT ON `product_reviews`
FOR EACH ROW BEGIN
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
END//

-- Trigger para actualizar stock de productos
CREATE TRIGGER `trg_update_product_stock` AFTER INSERT ON `order_items`
FOR EACH ROW BEGIN
    UPDATE products 
    SET stock_quantity = stock_quantity - NEW.quantity,
        sales_count = sales_count + NEW.quantity
    WHERE id = NEW.product_id;
END//

-- Procedimiento para limpiar datos antiguos
CREATE PROCEDURE `sp_clean_old_data`()
BEGIN
    DELETE FROM security_tokens WHERE expires_at < NOW();
    DELETE FROM user_activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
    DELETE FROM notifications WHERE is_read = 1 AND read_at < DATE_SUB(NOW(), INTERVAL 3 MONTH);
    DELETE FROM cart_items WHERE updated_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
END//

-- Procedimiento para obtener estadísticas del dashboard
CREATE PROCEDURE `sp_get_dashboard_stats`(IN p_user_id INT)
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_users,
        (SELECT COUNT(*) FROM routines WHERE is_active = 1) as total_routines,
        (SELECT COUNT(*) FROM products WHERE is_active = 1) as total_products,
        (SELECT COUNT(*) FROM orders WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())) as monthly_orders,
        (SELECT SUM(total_amount) FROM orders WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) AND payment_status = 'paid') as monthly_revenue;
END//

DELIMITER ;

-- ==========================================
-- ÍNDICES OPTIMIZADOS ADICIONALES
-- ==========================================

CREATE INDEX idx_users_gym_role_active ON users(gym_id, role, is_active);
CREATE INDEX idx_routines_template_public_active ON routines(is_template, is_public, is_active);
CREATE INDEX idx_products_category_featured_active ON products(category_id, is_featured, is_active);
CREATE INDEX idx_exercises_category_difficulty_active ON exercises(category_id, difficulty_level, is_active);
CREATE INDEX idx_class_bookings_date_status ON class_bookings(booking_date, status);
CREATE INDEX idx_orders_user_status_created ON orders(user_id, status, created_at);
CREATE INDEX idx_workout_logs_user_date ON workout_logs(user_id, workout_date);
CREATE INDEX idx_user_progress_user_date ON user_progress(user_id, measurement_date);

-- ==========================================
-- FINALIZACIÓN
-- ==========================================

-- Restaurar configuración original
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Confirmar transacción
COMMIT;

-- Habilitar autocommit
SET AUTOCOMMIT = 1;

-- Mensaje de finalización
SELECT 
    'Base de datos STYLOFITNESS creada exitosamente' as status,
    'Esquema completo con datos iniciales y rutinas predefinidas' as description,
    '28 tablas creadas con índices optimizados' as details,
    NOW() as created_at;
