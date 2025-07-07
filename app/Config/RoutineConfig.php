<?php

/**
 * STYLOFITNESS - Constantes y Configuraciones
 * Definiciones globales para el sistema de rutinas
 */

// Configuración de rutinas
if (!defined('ROUTINES_PER_PAGE')) {
    define('ROUTINES_PER_PAGE', 12);
}
if (!defined('EXERCISES_PER_PAGE')) {
    define('EXERCISES_PER_PAGE', 20);
}
define('MAX_EXERCISES_PER_DAY', 10);
define('MAX_ROUTINE_DAYS', 7);
define('DEFAULT_REST_SECONDS', 60);
define('DEFAULT_SETS', 3);
define('DEFAULT_REPS', '10');

// Niveles de dificultad
define('DIFFICULTY_LEVELS', [
    'beginner' => 'Principiante',
    'intermediate' => 'Intermedio',
    'advanced' => 'Avanzado',
]);

// Objetivos de entrenamiento
define('TRAINING_OBJECTIVES', [
    'weight_loss' => 'Pérdida de Peso',
    'muscle_gain' => 'Ganancia Muscular',
    'strength' => 'Fuerza',
    'endurance' => 'Resistencia',
    'flexibility' => 'Flexibilidad',
]);

// Tipos de ejercicios
define('EXERCISE_TYPES', [
    'compound' => 'Compuesto',
    'isolation' => 'Aislamiento',
    'cardio' => 'Cardiovascular',
    'functional' => 'Funcional',
    'plyometric' => 'Pliométrico',
    'isometric' => 'Isométrico',
]);

// Grupos musculares
define('MUSCLE_GROUPS', [
    'pectorales' => 'Pectorales',
    'dorsales' => 'Dorsales',
    'deltoides' => 'Deltoides',
    'biceps' => 'Biceps',
    'triceps' => 'Triceps',
    'cuadriceps' => 'Cuádriceps',
    'isquiotibiales' => 'Isquiotibiales',
    'gluteos' => 'Glúteos',
    'gemelos' => 'Gemelos',
    'core' => 'Core',
    'trapecio' => 'Trapecio',
    'romboides' => 'Romboides',
    'espalda_baja' => 'Espalda Baja',
    'antebrazos' => 'Antebrazos',
]);

// Equipamiento común
define('EQUIPMENT_TYPES', [
    'peso_corporal' => 'Peso Corporal',
    'mancuernas' => 'Mancuernas',
    'barra' => 'Barra',
    'banco' => 'Banco',
    'polea' => 'Polea',
    'maquina' => 'Máquina',
    'kettlebell' => 'Kettlebell',
    'banda_elastica' => 'Banda Elástica',
    'pelota_suiza' => 'Pelota Suiza',
    'trx' => 'TRX',
    'bosu' => 'Bosu',
    'step' => 'Step',
    'cinta' => 'Cinta de Correr',
    'bicicleta' => 'Bicicleta Estática',
    'eliptica' => 'Elíptica',
    'remo' => 'Máquina de Remo',
]);

// Configuración de video
define('VIDEO_MAX_SIZE', 100 * 1024 * 1024); // 100MB
define('VIDEO_ALLOWED_TYPES', ['video/mp4', 'video/webm', 'video/ogg']);
define('VIDEO_RESOLUTIONS', [
    '360p' => ['width' => 640, 'height' => 360],
    '720p' => ['width' => 1280, 'height' => 720],
    '1080p' => ['width' => 1920, 'height' => 1080],
]);

// Configuración de imágenes
define('IMAGE_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('IMAGE_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('IMAGE_QUALITY', 85);

// Configuración de progreso
define('PROGRESS_WEIGHTS', [
    'completion' => 0.4,
    'consistency' => 0.3,
    'progression' => 0.2,
    'notes' => 0.1,
]);

// Configuración de notificaciones
define('NOTIFICATION_TYPES', [
    'routine_assigned' => 'Rutina Asignada',
    'routine_updated' => 'Rutina Actualizada',
    'workout_reminder' => 'Recordatorio de Entrenamiento',
    'progress_milestone' => 'Logro de Progreso',
    'new_exercise' => 'Nuevo Ejercicio',
    'class_reminder' => 'Recordatorio de Clase',
]);

// Configuración de exportación
define('EXPORT_FORMATS', [
    'pdf' => 'PDF',
    'excel' => 'Excel',
    'csv' => 'CSV',
]);

// Configuración de plantillas
define('TEMPLATE_CATEGORIES', [
    'beginner' => 'Principiantes',
    'intermediate' => 'Intermedios',
    'advanced' => 'Avanzados',
    'specialized' => 'Especializadas',
    'rehabilitation' => 'Rehabilitación',
    'sport_specific' => 'Deporte Específico',
]);

// Configuración de métricas
define('METRIC_TYPES', [
    'weight' => 'Peso',
    'body_fat' => 'Grasa Corporal',
    'muscle_mass' => 'Masa Muscular',
    'measurements' => 'Medidas',
    'performance' => 'Rendimiento',
    'endurance' => 'Resistencia',
]);

// Configuración de recomendaciones
define('RECOMMENDATION_RULES', [
    'weight_loss' => [
        'cardio_percentage' => 40,
        'strength_percentage' => 60,
        'rest_time_max' => 60,
        'reps_min' => 12,
        'sets_min' => 3,
    ],
    'muscle_gain' => [
        'cardio_percentage' => 20,
        'strength_percentage' => 80,
        'rest_time_max' => 120,
        'reps_range' => '8-12',
        'sets_min' => 3,
    ],
    'strength' => [
        'cardio_percentage' => 10,
        'strength_percentage' => 90,
        'rest_time_max' => 300,
        'reps_max' => 6,
        'sets_min' => 3,
    ],
    'endurance' => [
        'cardio_percentage' => 60,
        'strength_percentage' => 40,
        'rest_time_max' => 45,
        'reps_min' => 15,
        'sets_min' => 2,
    ],
]);

// Configuración de análisis
define('ANALYSIS_PERIODS', [
    'weekly' => 'Semanal',
    'monthly' => 'Mensual',
    'quarterly' => 'Trimestral',
    'yearly' => 'Anual',
]);

// Configuración de integraciones
define('INTEGRATION_APIS', [
    'fitness_trackers' => [
        'fitbit' => 'Fitbit',
        'garmin' => 'Garmin',
        'apple_health' => 'Apple Health',
        'google_fit' => 'Google Fit',
    ],
    'nutrition' => [
        'myfitnesspal' => 'MyFitnessPal',
        'cronometer' => 'Cronometer',
        'fatsecret' => 'FatSecret',
    ],
    'payment' => [
        'stripe' => 'Stripe',
        'paypal' => 'PayPal',
        'mercadopago' => 'MercadoPago',
    ],
]);

// Configuración de roles y permisos
define('ROUTINE_PERMISSIONS', [
    'admin' => [
        'create' => true,
        'edit' => true,
        'delete' => true,
        'view_all' => true,
        'assign' => true,
        'duplicate' => true,
        'export' => true,
        'analytics' => true,
    ],
    'instructor' => [
        'create' => true,
        'edit' => true,
        'delete' => true,
        'view_all' => false,
        'assign' => true,
        'duplicate' => true,
        'export' => true,
        'analytics' => true,
    ],
    'client' => [
        'create' => false,
        'edit' => false,
        'delete' => false,
        'view_all' => false,
        'assign' => false,
        'duplicate' => false,
        'export' => true,
        'analytics' => false,
    ],
]);

// Configuración de validación
define('VALIDATION_RULES', [
    'routine_name' => [
        'required' => true,
        'min_length' => 3,
        'max_length' => 100,
    ],
    'routine_description' => [
        'required' => false,
        'max_length' => 1000,
    ],
    'exercise_name' => [
        'required' => true,
        'min_length' => 3,
        'max_length' => 100,
    ],
    'sets' => [
        'required' => true,
        'min' => 1,
        'max' => 10,
    ],
    'reps' => [
        'required' => true,
        'max_length' => 20,
    ],
    'rest_seconds' => [
        'required' => true,
        'min' => 15,
        'max' => 600,
    ],
    'duration_weeks' => [
        'required' => true,
        'min' => 1,
        'max' => 52,
    ],
    'sessions_per_week' => [
        'required' => true,
        'min' => 1,
        'max' => 7,
    ],
]);

// Configuración de cache
define('CACHE_DURATIONS', [
    'routine_list' => 3600,      // 1 hora
    'exercise_list' => 7200,     // 2 horas
    'user_stats' => 1800,        // 30 minutos
    'popular_routines' => 3600,  // 1 hora
    'categories' => 86400,        // 24 horas
]);

// Configuración de seguridad
define('SECURITY_SETTINGS', [
    'max_login_attempts' => 5,
    'lockout_duration' => 900,  // 15 minutos
    'session_timeout' => 7200,  // 2 horas
    'password_min_length' => 8,
    'require_strong_password' => true,
    'enable_2fa' => false,
    'max_file_uploads' => 10,
    'allowed_file_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'pdf'],
]);

// Configuración de email
define('EMAIL_TEMPLATES', [
    'routine_assigned' => [
        'subject' => 'Nueva rutina asignada - STYLOFITNESS',
        'template' => 'routine_assigned.html',
    ],
    'workout_reminder' => [
        'subject' => 'Recordatorio de entrenamiento - STYLOFITNESS',
        'template' => 'workout_reminder.html',
    ],
    'progress_update' => [
        'subject' => 'Actualización de progreso - STYLOFITNESS',
        'template' => 'progress_update.html',
    ],
]);

// Configuración de API
define('API_SETTINGS', [
    'version' => '1.0',
    'rate_limit' => 1000,       // Requests per hour
    'rate_limit_period' => 3600, // 1 hour
    'timeout' => 30,             // 30 seconds
    'max_request_size' => '10MB',
    'supported_formats' => ['json', 'xml'],
    'authentication' => 'bearer_token',
]);

// Configuración de logging
define('LOG_LEVELS', [
    'emergency' => 0,
    'alert' => 1,
    'critical' => 2,
    'error' => 3,
    'warning' => 4,
    'notice' => 5,
    'info' => 6,
    'debug' => 7,
]);

// Configuración de backup
define('BACKUP_SETTINGS', [
    'auto_backup' => true,
    'backup_frequency' => 'daily',
    'retention_days' => 30,
    'backup_location' => '/backups/',
    'include_uploads' => true,
    'compression' => true,
]);

// Configuración de performance
define('PERFORMANCE_SETTINGS', [
    'enable_caching' => true,
    'cache_driver' => 'file',
    'enable_compression' => true,
    'minify_assets' => true,
    'lazy_load_images' => true,
    'cdn_enabled' => false,
    'database_connection_pool' => 10,
]);

// Configuración de monitoreo
define('MONITORING_SETTINGS', [
    'enable_analytics' => true,
    'track_user_activity' => true,
    'track_performance' => true,
    'alert_thresholds' => [
        'response_time' => 2000,    // 2 seconds
        'error_rate' => 0.05,       // 5%
        'memory_usage' => 0.8,       // 80%
    ],
]);

// Configuración de desarrollo
define('DEVELOPMENT_SETTINGS', [
    'debug_mode' => false,
    'show_errors' => false,
    'log_queries' => false,
    'profiling_enabled' => false,
    'test_mode' => false,
]);

// URLs de recursos
define('RESOURCE_URLS', [
    'documentation' => 'https://docs.stylofitness.com',
    'support' => 'https://support.stylofitness.com',
    'api_docs' => 'https://api.stylofitness.com/docs',
    'status_page' => 'https://status.stylofitness.com',
]);

// Configuración de idiomas
define('SUPPORTED_LANGUAGES', [
    'es' => 'Español',
    'en' => 'English',
    'pt' => 'Português',
]);

// Configuración de timezone
define('DEFAULT_TIMEZONE', 'America/Lima');
define('SUPPORTED_TIMEZONES', [
    'America/Lima' => 'Lima (UTC-5)',
    'America/Bogota' => 'Bogotá (UTC-5)',
    'America/Mexico_City' => 'Ciudad de México (UTC-6)',
    'America/New_York' => 'Nueva York (UTC-5)',
    'Europe/Madrid' => 'Madrid (UTC+1)',
    'UTC' => 'UTC',
]);

// Configuración de formato de fechas
define('DATE_FORMATS', [
    'default' => 'd/m/Y',
    'long' => 'd/m/Y H:i:s',
    'short' => 'd/m',
    'iso' => 'Y-m-d',
    'iso_time' => 'Y-m-d H:i:s',
]);

// Configuración de monedas
define('SUPPORTED_CURRENCIES', [
    'PEN' => ['symbol' => 'S/', 'name' => 'Sol Peruano'],
    'USD' => ['symbol' => '$', 'name' => 'Dólar Americano'],
    'EUR' => ['symbol' => '€', 'name' => 'Euro'],
    'COP' => ['symbol' => '$', 'name' => 'Peso Colombiano'],
    'MXN' => ['symbol' => '$', 'name' => 'Peso Mexicano'],
]);

// Configuración de unidades de medida
define('MEASUREMENT_UNITS', [
    'weight' => [
        'kg' => 'Kilogramos',
        'lb' => 'Libras',
        'g' => 'Gramos',
    ],
    'distance' => [
        'km' => 'Kilómetros',
        'mi' => 'Millas',
        'm' => 'Metros',
    ],
    'time' => [
        'min' => 'Minutos',
        'sec' => 'Segundos',
        'hr' => 'Horas',
    ],
]);

// Configuración de colores del tema
if (!defined('THEME_COLORS')) {
    define('THEME_COLORS', [
        'primary' => '#FF6B00',
        'secondary' => '#E55A00',
        'accent' => '#FFB366',
        'success' => '#28a745',
        'warning' => '#ffc107',
        'danger' => '#dc3545',
        'info' => '#17a2b8',
        'light' => '#f8f9fa',
        'dark' => '#2c2c2c',
    ]);
}

/**
 * Función para obtener configuración de rutinas
 */
function getRoutineConfig($key, $default = null)
{
    $config = [
        'app_name' => 'STYLOFITNESS',
        'app_version' => '1.0.0',
        'app_description' => 'Sistema de gestión de rutinas y gimnasios',
        'app_url' => 'https://stylofitness.com',
        'support_email' => 'soporte@stylofitness.com',
        'admin_email' => 'admin@stylofitness.com',
        'default_language' => 'es',
        'default_timezone' => 'America/Lima',
        'default_currency' => 'PEN',
        'items_per_page' => 12,
        'max_upload_size' => '10MB',
        'session_lifetime' => 7200,
        'cache_enabled' => true,
        'debug_enabled' => false,
    ];

    return isset($config[$key]) ? $config[$key] : $default;
}

/**
 * Función para validar permisos de rutinas
 */
function hasRoutinePermission($user, $action, $routine = null)
{
    $role = $user['role'] ?? 'client';
    $permissions = ROUTINE_PERMISSIONS[$role] ?? [];

    if (!isset($permissions[$action])) {
        return false;
    }

    $hasPermission = $permissions[$action];

    // Verificaciones adicionales para instructores
    if ($role === 'instructor' && $routine) {
        switch ($action) {
            case 'edit':
            case 'delete':
                return $hasPermission && ($routine['instructor_id'] == $user['id']);
            case 'view':
                return $hasPermission || ($routine['instructor_id'] == $user['id']) || ($routine['client_id'] == $user['id']);
        }
    }

    // Verificaciones adicionales para clientes
    if ($role === 'client' && $routine) {
        switch ($action) {
            case 'view':
                return $routine['client_id'] == $user['id'] || $routine['is_template'];
            case 'export':
                return $routine['client_id'] == $user['id'];
        }
    }

    return $hasPermission;
}

/**
 * Función para validar datos de rutina
 */
function validateRoutineData($data)
{
    $errors = [];

    foreach (VALIDATION_RULES as $field => $rules) {
        $value = $data[$field] ?? null;

        // Validar campo requerido
        if (isset($rules['required']) && $rules['required'] && empty($value)) {
            $errors[$field] = "El campo {$field} es obligatorio";
            continue;
        }

        // Validar longitud mínima
        if (isset($rules['min_length']) && strlen($value) < $rules['min_length']) {
            $errors[$field] = "El campo {$field} debe tener al menos {$rules['min_length']} caracteres";
        }

        // Validar longitud máxima
        if (isset($rules['max_length']) && strlen($value) > $rules['max_length']) {
            $errors[$field] = "El campo {$field} no puede tener más de {$rules['max_length']} caracteres";
        }

        // Validar valor mínimo
        if (isset($rules['min']) && is_numeric($value) && $value < $rules['min']) {
            $errors[$field] = "El campo {$field} debe ser mayor o igual a {$rules['min']}";
        }

        // Validar valor máximo
        if (isset($rules['max']) && is_numeric($value) && $value > $rules['max']) {
            $errors[$field] = "El campo {$field} debe ser menor o igual a {$rules['max']}";
        }
    }

    return $errors;
}

/**
 * Función para formatear duración
 */
function formatDuration($minutes)
{
    if ($minutes < 60) {
        return $minutes . ' min';
    }

    $hours = floor($minutes / 60);
    $mins = $minutes % 60;

    if ($mins == 0) {
        return $hours . ' h';
    }

    return $hours . ' h ' . $mins . ' min';
}

/**
 * Función para calcular calorías quemadas
 */
function calculateCalories($exercise, $duration, $weight, $intensity = 'moderate')
{
    $metValues = [
        'cardio' => ['low' => 3.5, 'moderate' => 7.0, 'high' => 10.0],
        'strength' => ['low' => 2.5, 'moderate' => 5.0, 'high' => 7.5],
        'flexibility' => ['low' => 2.0, 'moderate' => 3.0, 'high' => 4.0],
    ];

    $exerciseType = $exercise['type'] ?? 'strength';
    $met = $metValues[$exerciseType][$intensity] ?? 5.0;

    // Fórmula: Calorías = MET × peso(kg) × tiempo(horas)
    $calories = $met * $weight * ($duration / 60);

    return round($calories);
}

/**
 * Función para generar recomendaciones
 */
function generateRecommendations($user, $routine)
{
    $recommendations = [];
    $objective = $routine['objective'];
    $rules = RECOMMENDATION_RULES[$objective] ?? [];

    // Análisis de la rutina actual
    $analysis = analyzeRoutine($routine);

    // Generar recomendaciones basadas en el análisis
    if (isset($rules['cardio_percentage']) && $analysis['cardio_percentage'] < $rules['cardio_percentage']) {
        $recommendations[] = [
            'type' => 'cardio',
            'message' => 'Considera agregar más ejercicios cardiovasculares',
            'priority' => 'medium',
        ];
    }

    if (isset($rules['rest_time_max']) && $analysis['avg_rest_time'] > $rules['rest_time_max']) {
        $recommendations[] = [
            'type' => 'rest',
            'message' => 'Reduce los tiempos de descanso para mejorar la intensidad',
            'priority' => 'low',
        ];
    }

    return $recommendations;
}

/**
 * Función para analizar rutina
 */
function analyzeRoutine($routine)
{
    // Esta función analizaría los ejercicios de la rutina
    // y devolvería estadísticas útiles
    return [
        'total_exercises' => 0,
        'cardio_percentage' => 0,
        'strength_percentage' => 0,
        'avg_rest_time' => 0,
        'muscle_groups_covered' => [],
        'estimated_calories' => 0,
    ];
}

/**
 * Función para logging de actividades
 */
function logActivity($userId, $action, $details = null)
{
    $logEntry = [
        'user_id' => $userId,
        'action' => $action,
        'details' => $details,
        'timestamp' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    ];

    // Aquí se guardaría en la tabla de logs
    // Database::getInstance()->insert('user_activity_logs', $logEntry);
}

/**
 * Función para obtener métricas de rendimiento
 */
function getPerformanceMetrics()
{
    return [
        'memory_usage' => memory_get_usage(true),
        'memory_peak' => memory_get_peak_usage(true),
        'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
        'queries_count' => 0, // Se actualizaría desde la clase Database
        'cache_hits' => 0,
        'cache_misses' => 0,
    ];
}
