-- ==========================================
-- MIGRACIÓN: GESTIÓN DE SALAS Y POSICIONES
-- Fecha: 2024
-- Descripción: Implementa la funcionalidad para gestionar salas
-- con posiciones específicas y control de aforo
-- ==========================================

-- Tabla de salas
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gym_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `room_type` enum('positioned','capacity_only') NOT NULL DEFAULT 'capacity_only',
  `total_capacity` int(11) NOT NULL,
  `floor_plan_image` varchar(255),
  `equipment_available` json,
  `amenities` json,
  `dimensions` varchar(100),
  `location_notes` text,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gym_id` (`gym_id`),
  KEY `idx_room_type` (`room_type`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_rooms_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de posiciones específicas en salas
CREATE TABLE IF NOT EXISTS `room_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `position_number` varchar(10) NOT NULL,
  `row_number` varchar(5),
  `seat_number` varchar(5),
  `x_coordinate` decimal(8,2),
  `y_coordinate` decimal(8,2),
  `position_type` enum('regular','premium','accessible','restricted') DEFAULT 'regular',
  `is_available` tinyint(1) DEFAULT 1,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_position` (`room_id`,`position_number`),
  KEY `idx_room_id` (`room_id`),
  KEY `idx_position_type` (`position_type`),
  KEY `idx_available` (`is_available`),
  CONSTRAINT `fk_room_positions_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Modificar tabla group_classes para usar room_id en lugar de room
ALTER TABLE `group_classes` 
ADD COLUMN `room_id` int(11) AFTER `max_participants`,
ADD KEY `idx_room_id` (`room_id`),
ADD CONSTRAINT `fk_group_classes_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL;

-- Tabla de reservas de posiciones específicas
CREATE TABLE IF NOT EXISTS `class_position_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `status` enum('reserved','confirmed','cancelled') DEFAULT 'reserved',
  `reserved_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_position_booking` (`booking_id`,`position_id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_position_id` (`position_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_class_position_bookings_booking` FOREIGN KEY (`booking_id`) REFERENCES `class_bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_position_bookings_position` FOREIGN KEY (`position_id`) REFERENCES `room_positions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para reservas temporales de posiciones (5 minutos)
CREATE TABLE IF NOT EXISTS `temp_position_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_position_schedule_date` (`position_id`,`schedule_id`,`booking_date`),
  KEY `idx_position_id` (`position_id`),
  KEY `idx_schedule_id` (`schedule_id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_temp_position_reservations_position` FOREIGN KEY (`position_id`) REFERENCES `room_positions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_temp_position_reservations_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_temp_position_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de lista de espera para clases
CREATE TABLE IF NOT EXISTS `class_waitlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `position` int(11) NOT NULL,
  `status` enum('waiting','notified','converted','expired','cancelled') DEFAULT 'waiting',
  `notified_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_waitlist` (`schedule_id`,`user_id`,`booking_date`),
  KEY `idx_schedule_id` (`schedule_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_booking_date` (`booking_date`),
  KEY `idx_position` (`position`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_class_waitlist_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_class_waitlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos de ejemplo para salas
INSERT INTO `rooms` (`gym_id`, `name`, `description`, `room_type`, `total_capacity`, `equipment_available`, `amenities`) VALUES
(1, 'Sala de Yoga Principal', 'Sala amplia con espejos y suelo de madera para clases de yoga y pilates', 'positioned', 30, '["colchonetas", "bloques_yoga", "correas", "mantas"]', '["aire_acondicionado", "espejos", "sonido", "iluminacion_regulable"]'),
(1, 'Sala de Spinning', 'Sala especializada para clases de spinning con bicicletas estáticas', 'positioned', 25, '["bicicletas_spinning", "sistema_sonido", "ventilacion"]', '["aire_acondicionado", "espejos", "sonido_premium", "iluminacion_led"]'),
(1, 'Sala CrossFit', 'Espacio amplio para entrenamientos funcionales y CrossFit', 'capacity_only', 20, '["barras", "discos", "kettlebells", "cajas_salto", "cuerdas"]', '["suelo_especializado", "ventilacion_industrial", "cronometros"]'),
(1, 'Sala de Baile', 'Sala con espejos y suelo de madera para clases de baile', 'positioned', 35, '["sistema_sonido", "espejos", "barras_ballet"]', '["aire_acondicionado", "espejos_completos", "sonido_profesional"]');

-- Insertar posiciones para la Sala de Yoga Principal (ID: 1)
INSERT INTO `room_positions` (`room_id`, `position_number`, `row_number`, `seat_number`, `x_coordinate`, `y_coordinate`, `position_type`) VALUES
-- Fila A (frente)
(1, 'A1', 'A', '1', 50.00, 100.00, 'regular'),
(1, 'A2', 'A', '2', 150.00, 100.00, 'regular'),
(1, 'A3', 'A', '3', 250.00, 100.00, 'regular'),
(1, 'A4', 'A', '4', 350.00, 100.00, 'regular'),
(1, 'A5', 'A', '5', 450.00, 100.00, 'regular'),
(1, 'A6', 'A', '6', 550.00, 100.00, 'regular'),
-- Fila B
(1, 'B1', 'B', '1', 50.00, 200.00, 'regular'),
(1, 'B2', 'B', '2', 150.00, 200.00, 'regular'),
(1, 'B3', 'B', '3', 250.00, 200.00, 'regular'),
(1, 'B4', 'B', '4', 350.00, 200.00, 'regular'),
(1, 'B5', 'B', '5', 450.00, 200.00, 'regular'),
(1, 'B6', 'B', '6', 550.00, 200.00, 'regular'),
-- Fila C
(1, 'C1', 'C', '1', 50.00, 300.00, 'regular'),
(1, 'C2', 'C', '2', 150.00, 300.00, 'regular'),
(1, 'C3', 'C', '3', 250.00, 300.00, 'regular'),
(1, 'C4', 'C', '4', 350.00, 300.00, 'regular'),
(1, 'C5', 'C', '5', 450.00, 300.00, 'regular'),
(1, 'C6', 'C', '6', 550.00, 300.00, 'regular'),
-- Fila D
(1, 'D1', 'D', '1', 50.00, 400.00, 'regular'),
(1, 'D2', 'D', '2', 150.00, 400.00, 'regular'),
(1, 'D3', 'D', '3', 250.00, 400.00, 'regular'),
(1, 'D4', 'D', '4', 350.00, 400.00, 'regular'),
(1, 'D5', 'D', '5', 450.00, 400.00, 'regular'),
(1, 'D6', 'D', '6', 550.00, 400.00, 'regular'),
-- Fila E (fondo)
(1, 'E1', 'E', '1', 50.00, 500.00, 'regular'),
(1, 'E2', 'E', '2', 150.00, 500.00, 'regular'),
(1, 'E3', 'E', '3', 250.00, 500.00, 'regular'),
(1, 'E4', 'E', '4', 350.00, 500.00, 'regular'),
(1, 'E5', 'E', '5', 450.00, 500.00, 'regular'),
(1, 'E6', 'E', '6', 550.00, 500.00, 'regular');

-- Insertar posiciones para la Sala de Spinning (ID: 2)
INSERT INTO `room_positions` (`room_id`, `position_number`, `row_number`, `seat_number`, `x_coordinate`, `y_coordinate`, `position_type`) VALUES
-- Fila 1 (frente)
(2, '1A', '1', 'A', 100.00, 100.00, 'regular'),
(2, '1B', '1', 'B', 200.00, 100.00, 'regular'),
(2, '1C', '1', 'C', 300.00, 100.00, 'regular'),
(2, '1D', '1', 'D', 400.00, 100.00, 'regular'),
(2, '1E', '1', 'E', 500.00, 100.00, 'regular'),
-- Fila 2
(2, '2A', '2', 'A', 100.00, 200.00, 'regular'),
(2, '2B', '2', 'B', 200.00, 200.00, 'regular'),
(2, '2C', '2', 'C', 300.00, 200.00, 'regular'),
(2, '2D', '2', 'D', 400.00, 200.00, 'regular'),
(2, '2E', '2', 'E', 500.00, 200.00, 'regular'),
-- Fila 3
(2, '3A', '3', 'A', 100.00, 300.00, 'regular'),
(2, '3B', '3', 'B', 200.00, 300.00, 'regular'),
(2, '3C', '3', 'C', 300.00, 300.00, 'regular'),
(2, '3D', '3', 'D', 400.00, 300.00, 'regular'),
(2, '3E', '3', 'E', 500.00, 300.00, 'regular'),
-- Fila 4
(2, '4A', '4', 'A', 100.00, 400.00, 'regular'),
(2, '4B', '4', 'B', 200.00, 400.00, 'regular'),
(2, '4C', '4', 'C', 300.00, 400.00, 'regular'),
(2, '4D', '4', 'D', 400.00, 400.00, 'regular'),
(2, '4E', '4', 'E', 500.00, 400.00, 'regular'),
-- Fila 5 (fondo)
(2, '5A', '5', 'A', 100.00, 500.00, 'regular'),
(2, '5B', '5', 'B', 200.00, 500.00, 'regular'),
(2, '5C', '5', 'C', 300.00, 500.00, 'regular'),
(2, '5D', '5', 'D', 400.00, 500.00, 'regular'),
(2, '5E', '5', 'E', 500.00, 500.00, 'regular');

-- Insertar posiciones para la Sala de Baile (ID: 4)
INSERT INTO `room_positions` (`room_id`, `position_number`, `row_number`, `seat_number`, `x_coordinate`, `y_coordinate`, `position_type`) VALUES
-- Fila 1 (frente)
(4, '1-1', '1', '1', 80.00, 120.00, 'regular'),
(4, '1-2', '1', '2', 160.00, 120.00, 'regular'),
(4, '1-3', '1', '3', 240.00, 120.00, 'regular'),
(4, '1-4', '1', '4', 320.00, 120.00, 'regular'),
(4, '1-5', '1', '5', 400.00, 120.00, 'regular'),
(4, '1-6', '1', '6', 480.00, 120.00, 'regular'),
(4, '1-7', '1', '7', 560.00, 120.00, 'regular'),
-- Fila 2
(4, '2-1', '2', '1', 80.00, 200.00, 'regular'),
(4, '2-2', '2', '2', 160.00, 200.00, 'regular'),
(4, '2-3', '2', '3', 240.00, 200.00, 'regular'),
(4, '2-4', '2', '4', 320.00, 200.00, 'regular'),
(4, '2-5', '2', '5', 400.00, 200.00, 'regular'),
(4, '2-6', '2', '6', 480.00, 200.00, 'regular'),
(4, '2-7', '2', '7', 560.00, 200.00, 'regular'),
-- Fila 3
(4, '3-1', '3', '1', 80.00, 280.00, 'regular'),
(4, '3-2', '3', '2', 160.00, 280.00, 'regular'),
(4, '3-3', '3', '3', 240.00, 280.00, 'regular'),
(4, '3-4', '3', '4', 320.00, 280.00, 'regular'),
(4, '3-5', '3', '5', 400.00, 280.00, 'regular'),
(4, '3-6', '3', '6', 480.00, 280.00, 'regular'),
(4, '3-7', '3', '7', 560.00, 280.00, 'regular'),
-- Fila 4
(4, '4-1', '4', '1', 80.00, 360.00, 'regular'),
(4, '4-2', '4', '2', 160.00, 360.00, 'regular'),
(4, '4-3', '4', '3', 240.00, 360.00, 'regular'),
(4, '4-4', '4', '4', 320.00, 360.00, 'regular'),
(4, '4-5', '4', '5', 400.00, 360.00, 'regular'),
(4, '4-6', '4', '6', 480.00, 360.00, 'regular'),
(4, '4-7', '4', '7', 560.00, 360.00, 'regular'),
-- Fila 5 (fondo)
(4, '5-1', '5', '1', 80.00, 440.00, 'regular'),
(4, '5-2', '5', '2', 160.00, 440.00, 'regular'),
(4, '5-3', '5', '3', 240.00, 440.00, 'regular'),
(4, '5-4', '5', '4', 320.00, 440.00, 'regular'),
(4, '5-5', '5', '5', 400.00, 440.00, 'regular'),
(4, '5-6', '5', '6', 480.00, 440.00, 'regular'),
(4, '5-7', '5', '7', 560.00, 440.00, 'regular');

-- Actualizar clases existentes para usar las nuevas salas
UPDATE `group_classes` SET `room_id` = 1 WHERE `class_type` IN ('yoga', 'pilates') AND `room_id` IS NULL;
UPDATE `group_classes` SET `room_id` = 2 WHERE `class_type` = 'spinning' AND `room_id` IS NULL;
UPDATE `group_classes` SET `room_id` = 3 WHERE `class_type` = 'crossfit' AND `room_id` IS NULL;
UPDATE `group_classes` SET `room_id` = 4 WHERE `class_type` = 'dance' AND `room_id` IS NULL;

-- Comentario: El campo 'room' en group_classes se mantiene por compatibilidad
-- pero se recomienda usar 'room_id' para nuevas implementaciones