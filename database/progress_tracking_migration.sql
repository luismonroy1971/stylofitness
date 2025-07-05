-- =====================================================
-- MIGRACIÓN: Sistema de Seguimiento de Progreso
-- STYLOFITNESS - Tablas para tracking avanzado
-- =====================================================

-- Tabla para notas del entrenador sobre clientes
CREATE TABLE IF NOT EXISTS trainer_client_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_id INT NOT NULL,
    client_id INT NOT NULL,
    note_text TEXT NOT NULL,
    note_type ENUM('general', 'progress', 'goal', 'concern', 'achievement') DEFAULT 'general',
    is_private BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trainer_client (trainer_id, client_id),
    INDEX idx_created_at (created_at)
);

-- Tabla para alertas de progreso
CREATE TABLE IF NOT EXISTS progress_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_id INT NOT NULL,
    client_id INT NOT NULL,
    alert_type ENUM('inactive', 'high_rpe', 'no_progress', 'goal_achieved', 'injury_risk') NOT NULL,
    alert_message TEXT NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    is_read BOOLEAN DEFAULT FALSE,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolved_at TIMESTAMP NULL,
    resolved_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_trainer_unread (trainer_id, is_read),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at)
);

-- Tabla para reportes generados
CREATE TABLE IF NOT EXISTS progress_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    report_type ENUM('individual', 'group', 'summary') NOT NULL,
    output_format ENUM('pdf', 'excel', 'html') NOT NULL,
    file_path VARCHAR(500),
    file_size INT,
    client_count INT DEFAULT 0,
    period_start DATE,
    period_end DATE,
    sections_included JSON,
    options_used JSON,
    download_count INT DEFAULT 0,
    last_downloaded_at TIMESTAMP NULL,
    is_scheduled BOOLEAN DEFAULT FALSE,
    schedule_config JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trainer_type (trainer_id, report_type),
    INDEX idx_created_at (created_at)
);

-- Tabla para clientes incluidos en reportes
CREATE TABLE IF NOT EXISTS progress_report_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    client_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES progress_reports(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_report_client (report_id, client_id)
);

-- Tabla para plantillas de reportes
CREATE TABLE IF NOT EXISTS progress_report_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_id INT NOT NULL,
    template_name VARCHAR(255) NOT NULL,
    description TEXT,
    template_config JSON NOT NULL,
    usage_count INT DEFAULT 0,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trainer_public (trainer_id, is_public),
    INDEX idx_usage_count (usage_count DESC)
);

-- Tabla para objetivos de clientes
CREATE TABLE IF NOT EXISTS client_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    trainer_id INT,
    goal_type ENUM('weight_loss', 'muscle_gain', 'strength', 'endurance', 'flexibility', 'custom') NOT NULL,
    goal_title VARCHAR(255) NOT NULL,
    goal_description TEXT,
    target_value DECIMAL(10,2),
    target_unit VARCHAR(50),
    current_value DECIMAL(10,2) DEFAULT 0,
    start_date DATE NOT NULL,
    target_date DATE,
    achieved_date DATE NULL,
    is_achieved BOOLEAN DEFAULT FALSE,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('active', 'paused', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_client_status (client_id, status),
    INDEX idx_trainer_active (trainer_id, status),
    INDEX idx_target_date (target_date)
);

-- Tabla para seguimiento de objetivos
CREATE TABLE IF NOT EXISTS goal_progress_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    goal_id INT NOT NULL,
    recorded_value DECIMAL(10,2) NOT NULL,
    progress_percentage DECIMAL(5,2) GENERATED ALWAYS AS (
        CASE 
            WHEN (SELECT target_value FROM client_goals WHERE id = goal_id) > 0 
            THEN (recorded_value / (SELECT target_value FROM client_goals WHERE id = goal_id)) * 100
            ELSE 0
        END
    ) STORED,
    notes TEXT,
    recorded_by INT,
    recorded_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (goal_id) REFERENCES client_goals(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_goal_date (goal_id, recorded_date),
    INDEX idx_recorded_date (recorded_date)
);

-- Tabla para sesiones de entrenamiento programadas
CREATE TABLE IF NOT EXISTS training_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_id INT NOT NULL,
    client_id INT NOT NULL,
    routine_id INT,
    session_date DATE NOT NULL,
    session_time TIME,
    duration_minutes INT,
    session_type ENUM('personal', 'group', 'assessment', 'consultation') DEFAULT 'personal',
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    location VARCHAR(255),
    notes TEXT,
    trainer_notes TEXT,
    client_feedback TEXT,
    rating TINYINT CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (routine_id) REFERENCES routines(id) ON DELETE SET NULL,
    INDEX idx_trainer_date (trainer_id, session_date),
    INDEX idx_client_date (client_id, session_date),
    INDEX idx_status (status)
);

-- Tabla para métricas personalizadas de progreso
CREATE TABLE IF NOT EXISTS custom_progress_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_id INT NOT NULL,
    metric_name VARCHAR(255) NOT NULL,
    metric_description TEXT,
    metric_unit VARCHAR(50),
    metric_type ENUM('numeric', 'percentage', 'time', 'distance', 'weight') DEFAULT 'numeric',
    is_higher_better BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trainer_active (trainer_id, is_active)
);

-- Tabla para valores de métricas personalizadas
CREATE TABLE IF NOT EXISTS client_custom_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    metric_id INT NOT NULL,
    metric_value DECIMAL(10,2) NOT NULL,
    measurement_date DATE NOT NULL,
    notes TEXT,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (metric_id) REFERENCES custom_progress_metrics(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_client_metric_date (client_id, metric_id, measurement_date),
    INDEX idx_measurement_date (measurement_date)
);

-- Tabla para comparaciones de clientes guardadas
CREATE TABLE IF NOT EXISTS client_comparisons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_id INT NOT NULL,
    comparison_name VARCHAR(255) NOT NULL,
    client_ids JSON NOT NULL,
    comparison_config JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_accessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    access_count INT DEFAULT 0,
    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trainer_accessed (trainer_id, last_accessed_at)
);

-- Tabla para notificaciones del sistema de progreso
CREATE TABLE IF NOT EXISTS progress_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notification_type ENUM('goal_reminder', 'progress_update', 'session_reminder', 'achievement', 'alert') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_id INT,
    related_type VARCHAR(50),
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_unread (user_id, is_read),
    INDEX idx_priority (priority),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- VISTAS PARA CONSULTAS OPTIMIZADAS
-- =====================================================

-- Vista para estadísticas de progreso de clientes
CREATE OR REPLACE VIEW client_progress_summary AS
SELECT 
    u.id as client_id,
    u.first_name,
    u.last_name,
    u.email,
    u.avatar,
    COUNT(DISTINCT wl.id) as total_workouts,
    COUNT(DISTINCT DATE(wl.workout_date)) as workout_days,
    AVG(wl.rpe) as avg_rpe,
    SUM(wl.calories_burned) as total_calories,
    MAX(wl.workout_date) as last_workout_date,
    COUNT(DISTINCT r.id) as active_routines,
    COUNT(DISTINCT cg.id) as active_goals,
    COUNT(DISTINCT CASE WHEN cg.is_achieved = 1 THEN cg.id END) as achieved_goals,
    AVG(ts.rating) as avg_session_rating,
    COUNT(DISTINCT ts.id) as total_sessions
FROM users u
LEFT JOIN workout_logs wl ON u.id = wl.user_id AND wl.workout_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
LEFT JOIN routines r ON u.id = r.client_id AND r.status = 'active'
LEFT JOIN client_goals cg ON u.id = cg.client_id AND cg.status = 'active'
LEFT JOIN training_sessions ts ON u.id = ts.client_id AND ts.status = 'completed'
WHERE u.role = 'client'
GROUP BY u.id;

-- Vista para alertas activas de entrenadores
CREATE OR REPLACE VIEW trainer_active_alerts AS
SELECT 
    pa.id,
    pa.trainer_id,
    pa.client_id,
    pa.alert_type,
    pa.alert_message,
    pa.severity,
    pa.created_at,
    u.first_name as client_first_name,
    u.last_name as client_last_name,
    u.avatar as client_avatar,
    DATEDIFF(NOW(), pa.created_at) as days_old
FROM progress_alerts pa
JOIN users u ON pa.client_id = u.id
WHERE pa.is_read = FALSE AND pa.is_resolved = FALSE
ORDER BY pa.severity DESC, pa.created_at DESC;

-- Vista para estadísticas de entrenadores
CREATE OR REPLACE VIEW trainer_statistics AS
SELECT 
    t.id as trainer_id,
    t.first_name,
    t.last_name,
    COUNT(DISTINCT r.client_id) as active_clients,
    COUNT(DISTINCT r.id) as active_routines,
    COUNT(DISTINCT ts.id) as total_sessions,
    AVG(ts.rating) as avg_rating,
    COUNT(DISTINCT pa.id) as unread_alerts,
    COUNT(DISTINCT pr.id) as generated_reports,
    MAX(ts.session_date) as last_session_date
FROM users t
LEFT JOIN routines r ON t.id = r.instructor_id AND r.status = 'active'
LEFT JOIN training_sessions ts ON t.id = ts.trainer_id
LEFT JOIN progress_alerts pa ON t.id = pa.trainer_id AND pa.is_read = FALSE
LEFT JOIN progress_reports pr ON t.id = pr.trainer_id
WHERE t.role IN ('instructor', 'admin')
GROUP BY t.id;

-- =====================================================
-- TRIGGERS PARA AUTOMATIZACIÓN
-- =====================================================

-- Trigger para crear alertas automáticas por inactividad
DELIMITER //
CREATE TRIGGER check_client_inactivity
AFTER INSERT ON workout_logs
FOR EACH ROW
BEGIN
    DECLARE days_since_last INT;
    DECLARE trainer_id_var INT;
    
    -- Obtener el entrenador del cliente
    SELECT instructor_id INTO trainer_id_var
    FROM routines 
    WHERE client_id = NEW.user_id AND status = 'active'
    LIMIT 1;
    
    IF trainer_id_var IS NOT NULL THEN
        -- Verificar días desde el último entrenamiento anterior
        SELECT DATEDIFF(NEW.workout_date, MAX(workout_date)) INTO days_since_last
        FROM workout_logs 
        WHERE user_id = NEW.user_id AND id != NEW.id;
        
        -- Si han pasado más de 7 días, eliminar alertas de inactividad previas
        IF days_since_last > 7 THEN
            DELETE FROM progress_alerts 
            WHERE client_id = NEW.user_id 
            AND trainer_id = trainer_id_var 
            AND alert_type = 'inactive'
            AND is_resolved = FALSE;
        END IF;
    END IF;
END//
DELIMITER ;

-- Trigger para actualizar progreso de objetivos
DELIMITER //
CREATE TRIGGER update_goal_progress
AFTER INSERT ON goal_progress_tracking
FOR EACH ROW
BEGIN
    DECLARE target_val DECIMAL(10,2);
    DECLARE progress_pct DECIMAL(5,2);
    
    -- Obtener valor objetivo
    SELECT target_value INTO target_val
    FROM client_goals 
    WHERE id = NEW.goal_id;
    
    -- Calcular porcentaje de progreso
    IF target_val > 0 THEN
        SET progress_pct = (NEW.recorded_value / target_val) * 100;
        
        -- Si se alcanzó el objetivo (100% o más)
        IF progress_pct >= 100 THEN
            UPDATE client_goals 
            SET is_achieved = TRUE, 
                achieved_date = NEW.recorded_date,
                status = 'completed'
            WHERE id = NEW.goal_id;
            
            -- Crear notificación de logro
            INSERT INTO progress_notifications (
                user_id, 
                notification_type, 
                title, 
                message, 
                related_id, 
                related_type,
                priority
            )
            SELECT 
                cg.client_id,
                'achievement',
                '¡Objetivo Alcanzado!',
                CONCAT('Has completado tu objetivo: ', cg.goal_title),
                cg.id,
                'goal',
                'high'
            FROM client_goals cg
            WHERE cg.id = NEW.goal_id;
        END IF;
    END IF;
END//
DELIMITER ;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

-- Procedimiento para generar alertas de inactividad
DELIMITER //
CREATE PROCEDURE GenerateInactivityAlerts()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE client_id_var INT;
    DECLARE trainer_id_var INT;
    DECLARE days_inactive INT;
    DECLARE client_name VARCHAR(255);
    
    DECLARE client_cursor CURSOR FOR
        SELECT 
            u.id,
            r.instructor_id,
            DATEDIFF(NOW(), COALESCE(MAX(wl.workout_date), u.created_at)) as days_since_last,
            CONCAT(u.first_name, ' ', u.last_name) as full_name
        FROM users u
        JOIN routines r ON u.id = r.client_id AND r.status = 'active'
        LEFT JOIN workout_logs wl ON u.id = wl.user_id
        WHERE u.role = 'client'
        GROUP BY u.id, r.instructor_id
        HAVING days_since_last >= 7;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN client_cursor;
    
    read_loop: LOOP
        FETCH client_cursor INTO client_id_var, trainer_id_var, days_inactive, client_name;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Verificar si ya existe una alerta similar reciente
        IF NOT EXISTS (
            SELECT 1 FROM progress_alerts 
            WHERE client_id = client_id_var 
            AND trainer_id = trainer_id_var 
            AND alert_type = 'inactive'
            AND is_resolved = FALSE
            AND created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)
        ) THEN
            -- Crear nueva alerta
            INSERT INTO progress_alerts (
                trainer_id,
                client_id,
                alert_type,
                alert_message,
                severity
            ) VALUES (
                trainer_id_var,
                client_id_var,
                'inactive',
                CONCAT(client_name, ' lleva ', days_inactive, ' días sin entrenar'),
                CASE 
                    WHEN days_inactive >= 14 THEN 'high'
                    WHEN days_inactive >= 10 THEN 'medium'
                    ELSE 'low'
                END
            );
        END IF;
    END LOOP;
    
    CLOSE client_cursor;
END//
DELIMITER ;

-- Procedimiento para limpiar datos antiguos
DELIMITER //
CREATE PROCEDURE CleanOldProgressData()
BEGIN
    -- Eliminar alertas resueltas de más de 30 días
    DELETE FROM progress_alerts 
    WHERE is_resolved = TRUE 
    AND resolved_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
    
    -- Eliminar notificaciones leídas de más de 60 días
    DELETE FROM progress_notifications 
    WHERE is_read = TRUE 
    AND read_at < DATE_SUB(NOW(), INTERVAL 60 DAY);
    
    -- Eliminar reportes temporales de más de 90 días
    DELETE FROM progress_reports 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
    AND title LIKE '%temp%';
    
    -- Eliminar comparaciones no accedidas en 180 días
    DELETE FROM client_comparisons 
    WHERE last_accessed_at < DATE_SUB(NOW(), INTERVAL 180 DAY);
END//
DELIMITER ;

-- =====================================================
-- EVENTOS PROGRAMADOS
-- =====================================================

-- Evento para generar alertas de inactividad diariamente
CREATE EVENT IF NOT EXISTS daily_inactivity_check
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
  CALL GenerateInactivityAlerts();

-- Evento para limpiar datos antiguos semanalmente
CREATE EVENT IF NOT EXISTS weekly_data_cleanup
ON SCHEDULE EVERY 1 WEEK
STARTS CURRENT_TIMESTAMP
DO
  CALL CleanOldProgressData();

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_workout_logs_user_date_rpe ON workout_logs(user_id, workout_date, rpe);
CREATE INDEX idx_user_progress_user_date ON user_progress(user_id, measurement_date);
CREATE INDEX idx_routines_instructor_client_status ON routines(instructor_id, client_id, status);

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- Insertar métricas personalizadas por defecto
INSERT IGNORE INTO custom_progress_metrics (trainer_id, metric_name, metric_description, metric_unit, metric_type, is_higher_better) 
SELECT 
    id,
    'Flexibilidad General',
    'Medición general de flexibilidad corporal',
    'puntos',
    'numeric',
    TRUE
FROM users WHERE role IN ('instructor', 'admin');

INSERT IGNORE INTO custom_progress_metrics (trainer_id, metric_name, metric_description, metric_unit, metric_type, is_higher_better) 
SELECT 
    id,
    'Resistencia Cardiovascular',
    'Tiempo en prueba de resistencia cardiovascular',
    'minutos',
    'time',
    TRUE
FROM users WHERE role IN ('instructor', 'admin');

INSERT IGNORE INTO custom_progress_metrics (trainer_id, metric_name, metric_description, metric_unit, metric_type, is_higher_better) 
SELECT 
    id,
    'Porcentaje de Grasa Corporal',
    'Medición del porcentaje de grasa corporal',
    '%',
    'percentage',
    FALSE
FROM users WHERE role IN ('instructor', 'admin');

-- =====================================================
-- COMENTARIOS Y DOCUMENTACIÓN
-- =====================================================

/*
ESTRUCTURA DEL SISTEMA DE SEGUIMIENTO DE PROGRESO:

1. TABLAS PRINCIPALES:
   - trainer_client_notes: Notas del entrenador sobre clientes
   - progress_alerts: Sistema de alertas automáticas
   - progress_reports: Reportes generados
   - client_goals: Objetivos de los clientes
   - training_sessions: Sesiones de entrenamiento
   - custom_progress_metrics: Métricas personalizadas

2. FUNCIONALIDADES:
   - Seguimiento automático de inactividad
   - Generación de reportes personalizables
   - Sistema de alertas inteligentes
   - Métricas personalizadas por entrenador
   - Comparación entre clientes
   - Seguimiento de objetivos

3. AUTOMATIZACIÓN:
   - Triggers para alertas automáticas
   - Eventos programados para limpieza
   - Vistas optimizadas para consultas frecuentes
   - Procedimientos almacenados para tareas complejas

4. OPTIMIZACIÓN:
   - Índices compuestos para consultas rápidas
   - Vistas materializadas para estadísticas
   - Limpieza automática de datos antiguos
   - Estructura normalizada para escalabilidad
*/

-- =====================================================
-- FIN DE LA MIGRACIÓN
-- =====================================================

SELECT 'Migración del sistema de seguimiento de progreso completada exitosamente' as status;