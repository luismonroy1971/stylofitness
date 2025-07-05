<?php

/**
 * Configuración del Sistema de Seguimiento de Progreso
 * StyloFitness - Configuraciones para tracking avanzado
 */

class ProgressConfig
{
    // =====================================================
    // CONFIGURACIONES GENERALES
    // =====================================================
    
    /**
     * Días de inactividad antes de generar alerta
     */
    const INACTIVITY_ALERT_DAYS = 7;
    
    /**
     * Días de inactividad para alerta crítica
     */
    const CRITICAL_INACTIVITY_DAYS = 14;
    
    /**
     * RPE máximo antes de generar alerta
     */
    const HIGH_RPE_THRESHOLD = 8;
    
    /**
     * Número máximo de clientes por comparación
     */
    const MAX_CLIENTS_COMPARISON = 10;
    
    /**
     * Días de retención de reportes temporales
     */
    const TEMP_REPORT_RETENTION_DAYS = 7;
    
    /**
     * Tamaño máximo de archivo de reporte (MB)
     */
    const MAX_REPORT_FILE_SIZE_MB = 50;
    
    // =====================================================
    // CONFIGURACIONES DE ALERTAS
    // =====================================================
    
    /**
     * Tipos de alertas disponibles
     */
    const ALERT_TYPES = [
        'inactive' => [
            'name' => 'Cliente Inactivo',
            'description' => 'Cliente sin actividad por varios días',
            'icon' => 'fas fa-user-clock',
            'color' => 'warning'
        ],
        'high_rpe' => [
            'name' => 'RPE Alto',
            'description' => 'Cliente reportando esfuerzo muy alto',
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'danger'
        ],
        'no_progress' => [
            'name' => 'Sin Progreso',
            'description' => 'Cliente sin progreso en objetivos',
            'icon' => 'fas fa-chart-line',
            'color' => 'info'
        ],
        'goal_achieved' => [
            'name' => 'Objetivo Alcanzado',
            'description' => 'Cliente ha completado un objetivo',
            'icon' => 'fas fa-trophy',
            'color' => 'success'
        ],
        'injury_risk' => [
            'name' => 'Riesgo de Lesión',
            'description' => 'Patrones que sugieren riesgo de lesión',
            'icon' => 'fas fa-first-aid',
            'color' => 'danger'
        ]
    ];
    
    /**
     * Niveles de severidad de alertas
     */
    const SEVERITY_LEVELS = [
        'low' => [
            'name' => 'Baja',
            'color' => 'info',
            'priority' => 1
        ],
        'medium' => [
            'name' => 'Media',
            'color' => 'warning',
            'priority' => 2
        ],
        'high' => [
            'name' => 'Alta',
            'color' => 'danger',
            'priority' => 3
        ],
        'critical' => [
            'name' => 'Crítica',
            'color' => 'dark',
            'priority' => 4
        ]
    ];
    
    // =====================================================
    // CONFIGURACIONES DE REPORTES
    // =====================================================
    
    /**
     * Tipos de reportes disponibles
     */
    const REPORT_TYPES = [
        'individual' => [
            'name' => 'Reporte Individual',
            'description' => 'Reporte detallado de un cliente específico',
            'max_clients' => 1
        ],
        'group' => [
            'name' => 'Reporte Grupal',
            'description' => 'Comparación de múltiples clientes',
            'max_clients' => 10
        ],
        'summary' => [
            'name' => 'Resumen Ejecutivo',
            'description' => 'Resumen general de todos los clientes',
            'max_clients' => null
        ]
    ];
    
    /**
     * Formatos de salida disponibles
     */
    const OUTPUT_FORMATS = [
        'pdf' => [
            'name' => 'PDF',
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'icon' => 'fas fa-file-pdf'
        ],
        'excel' => [
            'name' => 'Excel',
            'extension' => 'xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'icon' => 'fas fa-file-excel'
        ],
        'html' => [
            'name' => 'HTML',
            'extension' => 'html',
            'mime_type' => 'text/html',
            'icon' => 'fas fa-file-code'
        ]
    ];
    
    /**
     * Secciones disponibles para reportes
     */
    const REPORT_SECTIONS = [
        'summary' => [
            'name' => 'Resumen General',
            'description' => 'Estadísticas generales del período',
            'required' => true
        ],
        'progress_charts' => [
            'name' => 'Gráficos de Progreso',
            'description' => 'Gráficos de evolución y tendencias',
            'required' => false
        ],
        'workout_history' => [
            'name' => 'Historial de Entrenamientos',
            'description' => 'Detalle de sesiones realizadas',
            'required' => false
        ],
        'exercise_analysis' => [
            'name' => 'Análisis de Ejercicios',
            'description' => 'Análisis por ejercicio y grupo muscular',
            'required' => false
        ],
        'physical_progress' => [
            'name' => 'Progreso Físico',
            'description' => 'Mediciones corporales y composición',
            'required' => false
        ],
        'goals_tracking' => [
            'name' => 'Seguimiento de Objetivos',
            'description' => 'Estado y progreso de objetivos',
            'required' => false
        ],
        'recommendations' => [
            'name' => 'Recomendaciones',
            'description' => 'Sugerencias y próximos pasos',
            'required' => false
        ]
    ];
    
    // =====================================================
    // CONFIGURACIONES DE MÉTRICAS
    // =====================================================
    
    /**
     * Tipos de métricas personalizadas
     */
    const METRIC_TYPES = [
        'numeric' => [
            'name' => 'Numérico',
            'description' => 'Valor numérico simple',
            'format' => 'number'
        ],
        'percentage' => [
            'name' => 'Porcentaje',
            'description' => 'Valor en porcentaje (0-100)',
            'format' => 'percentage'
        ],
        'time' => [
            'name' => 'Tiempo',
            'description' => 'Duración en minutos o segundos',
            'format' => 'time'
        ],
        'distance' => [
            'name' => 'Distancia',
            'description' => 'Distancia en metros o kilómetros',
            'format' => 'distance'
        ],
        'weight' => [
            'name' => 'Peso',
            'description' => 'Peso en kilogramos',
            'format' => 'weight'
        ]
    ];
    
    /**
     * Métricas predefinidas del sistema
     */
    const DEFAULT_METRICS = [
        'flexibility' => [
            'name' => 'Flexibilidad General',
            'description' => 'Medición general de flexibilidad corporal',
            'unit' => 'puntos',
            'type' => 'numeric',
            'higher_better' => true
        ],
        'cardio_endurance' => [
            'name' => 'Resistencia Cardiovascular',
            'description' => 'Tiempo en prueba de resistencia cardiovascular',
            'unit' => 'minutos',
            'type' => 'time',
            'higher_better' => true
        ],
        'body_fat' => [
            'name' => 'Porcentaje de Grasa Corporal',
            'description' => 'Medición del porcentaje de grasa corporal',
            'unit' => '%',
            'type' => 'percentage',
            'higher_better' => false
        ],
        'vo2_max' => [
            'name' => 'VO2 Máximo',
            'description' => 'Consumo máximo de oxígeno',
            'unit' => 'ml/kg/min',
            'type' => 'numeric',
            'higher_better' => true
        ],
        'resting_hr' => [
            'name' => 'Frecuencia Cardíaca en Reposo',
            'description' => 'Pulsaciones por minuto en reposo',
            'unit' => 'bpm',
            'type' => 'numeric',
            'higher_better' => false
        ]
    ];
    
    // =====================================================
    // CONFIGURACIONES DE OBJETIVOS
    // =====================================================
    
    /**
     * Tipos de objetivos disponibles
     */
    const GOAL_TYPES = [
        'weight_loss' => [
            'name' => 'Pérdida de Peso',
            'description' => 'Reducir peso corporal',
            'unit' => 'kg',
            'icon' => 'fas fa-weight',
            'color' => 'primary'
        ],
        'muscle_gain' => [
            'name' => 'Ganancia Muscular',
            'description' => 'Aumentar masa muscular',
            'unit' => 'kg',
            'icon' => 'fas fa-dumbbell',
            'color' => 'success'
        ],
        'strength' => [
            'name' => 'Fuerza',
            'description' => 'Mejorar fuerza máxima',
            'unit' => 'kg',
            'icon' => 'fas fa-fist-raised',
            'color' => 'danger'
        ],
        'endurance' => [
            'name' => 'Resistencia',
            'description' => 'Mejorar resistencia cardiovascular',
            'unit' => 'minutos',
            'icon' => 'fas fa-running',
            'color' => 'info'
        ],
        'flexibility' => [
            'name' => 'Flexibilidad',
            'description' => 'Mejorar flexibilidad general',
            'unit' => 'cm',
            'icon' => 'fas fa-expand-arrows-alt',
            'color' => 'warning'
        ],
        'custom' => [
            'name' => 'Personalizado',
            'description' => 'Objetivo personalizado',
            'unit' => 'variable',
            'icon' => 'fas fa-bullseye',
            'color' => 'secondary'
        ]
    ];
    
    /**
     * Estados de objetivos
     */
    const GOAL_STATUSES = [
        'active' => [
            'name' => 'Activo',
            'color' => 'success',
            'icon' => 'fas fa-play'
        ],
        'paused' => [
            'name' => 'Pausado',
            'color' => 'warning',
            'icon' => 'fas fa-pause'
        ],
        'completed' => [
            'name' => 'Completado',
            'color' => 'primary',
            'icon' => 'fas fa-check'
        ],
        'cancelled' => [
            'name' => 'Cancelado',
            'color' => 'danger',
            'icon' => 'fas fa-times'
        ]
    ];
    
    // =====================================================
    // CONFIGURACIONES DE GRÁFICOS
    // =====================================================
    
    /**
     * Colores para gráficos
     */
    const CHART_COLORS = [
        'primary' => '#007bff',
        'secondary' => '#6c757d',
        'success' => '#28a745',
        'danger' => '#dc3545',
        'warning' => '#ffc107',
        'info' => '#17a2b8',
        'light' => '#f8f9fa',
        'dark' => '#343a40'
    ];
    
    /**
     * Configuraciones de períodos para gráficos
     */
    const CHART_PERIODS = [
        '7d' => [
            'name' => 'Última Semana',
            'days' => 7,
            'format' => 'D M j'
        ],
        '30d' => [
            'name' => 'Último Mes',
            'days' => 30,
            'format' => 'M j'
        ],
        '90d' => [
            'name' => 'Últimos 3 Meses',
            'days' => 90,
            'format' => 'M j'
        ],
        '1y' => [
            'name' => 'Último Año',
            'days' => 365,
            'format' => 'M Y'
        ]
    ];
    
    // =====================================================
    // CONFIGURACIONES DE NOTIFICACIONES
    // =====================================================
    
    /**
     * Tipos de notificaciones
     */
    const NOTIFICATION_TYPES = [
        'goal_reminder' => [
            'name' => 'Recordatorio de Objetivo',
            'icon' => 'fas fa-bullseye',
            'color' => 'info'
        ],
        'progress_update' => [
            'name' => 'Actualización de Progreso',
            'icon' => 'fas fa-chart-line',
            'color' => 'success'
        ],
        'session_reminder' => [
            'name' => 'Recordatorio de Sesión',
            'icon' => 'fas fa-calendar',
            'color' => 'warning'
        ],
        'achievement' => [
            'name' => 'Logro Alcanzado',
            'icon' => 'fas fa-trophy',
            'color' => 'success'
        ],
        'alert' => [
            'name' => 'Alerta',
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'danger'
        ]
    ];
    
    // =====================================================
    // MÉTODOS AUXILIARES
    // =====================================================
    
    /**
     * Obtener configuración de tipo de alerta
     */
    public static function getAlertType($type)
    {
        return self::ALERT_TYPES[$type] ?? null;
    }
    
    /**
     * Obtener configuración de nivel de severidad
     */
    public static function getSeverityLevel($level)
    {
        return self::SEVERITY_LEVELS[$level] ?? null;
    }
    
    /**
     * Obtener configuración de tipo de reporte
     */
    public static function getReportType($type)
    {
        return self::REPORT_TYPES[$type] ?? null;
    }
    
    /**
     * Obtener configuración de formato de salida
     */
    public static function getOutputFormat($format)
    {
        return self::OUTPUT_FORMATS[$format] ?? null;
    }
    
    /**
     * Obtener configuración de tipo de métrica
     */
    public static function getMetricType($type)
    {
        return self::METRIC_TYPES[$type] ?? null;
    }
    
    /**
     * Obtener configuración de tipo de objetivo
     */
    public static function getGoalType($type)
    {
        return self::GOAL_TYPES[$type] ?? null;
    }
    
    /**
     * Obtener configuración de estado de objetivo
     */
    public static function getGoalStatus($status)
    {
        return self::GOAL_STATUSES[$status] ?? null;
    }
    
    /**
     * Obtener color para gráfico
     */
    public static function getChartColor($color)
    {
        return self::CHART_COLORS[$color] ?? '#007bff';
    }
    
    /**
     * Obtener configuración de período para gráfico
     */
    public static function getChartPeriod($period)
    {
        return self::CHART_PERIODS[$period] ?? self::CHART_PERIODS['30d'];
    }
    
    /**
     * Obtener configuración de tipo de notificación
     */
    public static function getNotificationType($type)
    {
        return self::NOTIFICATION_TYPES[$type] ?? null;
    }
    
    /**
     * Validar si un usuario puede acceder a funciones de progreso
     */
    public static function canAccessProgress($userRole)
    {
        return in_array($userRole, ['instructor', 'admin']);
    }
    
    /**
     * Obtener límite de clientes para comparación según rol
     */
    public static function getComparisonLimit($userRole)
    {
        switch ($userRole) {
            case 'admin':
                return self::MAX_CLIENTS_COMPARISON;
            case 'instructor':
                return self::MAX_CLIENTS_COMPARISON;
            default:
                return 0;
        }
    }
    
    /**
     * Generar configuración para gráfico de progreso
     */
    public static function getProgressChartConfig($type = 'line')
    {
        return [
            'type' => $type,
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0,0,0,0.1)'
                    ]
                ],
                'x' => [
                    'grid' => [
                        'color' => 'rgba(0,0,0,0.1)'
                    ]
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top'
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false
                ]
            ]
        ];
    }
    
    /**
     * Obtener configuración de exportación por defecto
     */
    public static function getDefaultExportConfig()
    {
        return [
            'include_charts' => true,
            'include_photos' => false,
            'include_notes' => true,
            'include_goals' => true,
            'chart_quality' => 'high',
            'page_orientation' => 'portrait',
            'page_size' => 'A4'
        ];
    }
}

?>