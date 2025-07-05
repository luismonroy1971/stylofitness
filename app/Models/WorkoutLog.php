<?php

namespace StyleFitness\Models;

use StyleFitness\Config\Database;
use Exception;
use PDO;

/**
 * Modelo WorkoutLog - STYLOFITNESS
 * Maneja el seguimiento de entrenamientos y progreso de clientes
 */

class WorkoutLog
{
    private $db;
    private $table = 'workout_logs';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Registrar un ejercicio completado
     */
    public function logExercise($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, routine_id, exercise_id, workout_date, sets_completed, reps, weight_used, 
                 duration_seconds, calories_burned, rpe, notes, completed_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $params = [
            $data['user_id'],
            $data['routine_id'] ?? null,
            $data['exercise_id'],
            $data['workout_date'] ?? date('Y-m-d'),
            $data['sets_completed'] ?? 0,
            $data['reps'] ?? null,
            $data['weight_used'] ?? null,
            $data['duration_seconds'] ?? null,
            $data['calories_burned'] ?? null,
            $data['rpe'] ?? null,
            $data['notes'] ?? null
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Obtener historial de entrenamientos de un cliente
     */
    public function getClientWorkoutHistory($clientId, $filters = [])
    {
        $sql = "SELECT wl.*, 
                       e.name as exercise_name, 
                       e.category as exercise_category,
                       e.muscle_groups,
                       r.name as routine_name,
                       r.objective as routine_objective
                FROM {$this->table} wl
                LEFT JOIN exercises e ON wl.exercise_id = e.id
                LEFT JOIN routines r ON wl.routine_id = r.id
                WHERE wl.user_id = ?";

        $params = [$clientId];

        // Aplicar filtros
        if (!empty($filters['date_from'])) {
            $sql .= ' AND wl.workout_date >= ?';
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= ' AND wl.workout_date <= ?';
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['routine_id'])) {
            $sql .= ' AND wl.routine_id = ?';
            $params[] = $filters['routine_id'];
        }

        if (!empty($filters['exercise_category'])) {
            $sql .= ' AND e.category = ?';
            $params[] = $filters['exercise_category'];
        }

        $sql .= ' ORDER BY wl.workout_date DESC, wl.completed_at DESC';

        if (isset($filters['limit'])) {
            $sql .= ' LIMIT ?';
            $params[] = (int)$filters['limit'];
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Obtener progreso de un ejercicio específico
     */
    public function getExerciseProgress($clientId, $exerciseId, $days = 30)
    {
        $sql = "SELECT 
                    workout_date,
                    sets_completed,
                    reps,
                    weight_used,
                    duration_seconds,
                    rpe,
                    notes
                FROM {$this->table}
                WHERE user_id = ? AND exercise_id = ? 
                AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                ORDER BY workout_date ASC, completed_at ASC";

        return $this->db->fetchAll($sql, [$clientId, $exerciseId, $days]);
    }

    /**
     * Obtener estadísticas de progreso de un cliente
     */
    public function getClientProgressStats($clientId, $days = 30)
    {
        $stats = [];

        // Total de entrenamientos
        $stats['total_workouts'] = $this->db->count(
            "SELECT COUNT(DISTINCT workout_date) FROM {$this->table} 
             WHERE user_id = ? AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)",
            [$clientId, $days]
        );

        // Total de ejercicios completados
        $stats['total_exercises'] = $this->db->count(
            "SELECT COUNT(*) FROM {$this->table} 
             WHERE user_id = ? AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)",
            [$clientId, $days]
        );

        // Promedio de RPE
        $avgRpe = $this->db->fetch(
            "SELECT AVG(rpe) as avg_rpe FROM {$this->table} 
             WHERE user_id = ? AND rpe IS NOT NULL 
             AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)",
            [$clientId, $days]
        );
        $stats['avg_rpe'] = round($avgRpe['avg_rpe'] ?? 0, 1);

        // Calorías totales quemadas
        $totalCalories = $this->db->fetch(
            "SELECT SUM(calories_burned) as total_calories FROM {$this->table} 
             WHERE user_id = ? AND calories_burned IS NOT NULL 
             AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)",
            [$clientId, $days]
        );
        $stats['total_calories'] = $totalCalories['total_calories'] ?? 0;

        // Tiempo total de entrenamiento (en minutos)
        $totalDuration = $this->db->fetch(
            "SELECT SUM(duration_seconds) as total_seconds FROM {$this->table} 
             WHERE user_id = ? AND duration_seconds IS NOT NULL 
             AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)",
            [$clientId, $days]
        );
        $stats['total_duration_minutes'] = round(($totalDuration['total_seconds'] ?? 0) / 60, 0);

        // Ejercicios más frecuentes
        $stats['most_frequent_exercises'] = $this->db->fetchAll(
            "SELECT e.name, e.category, COUNT(*) as frequency
             FROM {$this->table} wl
             JOIN exercises e ON wl.exercise_id = e.id
             WHERE wl.user_id = ? AND wl.workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY wl.exercise_id
             ORDER BY frequency DESC
             LIMIT 5",
            [$clientId, $days]
        );

        // Progreso semanal
        $stats['weekly_progress'] = $this->db->fetchAll(
            "SELECT 
                YEARWEEK(workout_date) as week,
                COUNT(DISTINCT workout_date) as workout_days,
                COUNT(*) as total_exercises,
                AVG(rpe) as avg_rpe,
                SUM(calories_burned) as total_calories
             FROM {$this->table}
             WHERE user_id = ? AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY YEARWEEK(workout_date)
             ORDER BY week DESC",
            [$clientId, $days]
        );

        return $stats;
    }

    /**
     * Obtener resumen de actividad por día
     */
    public function getDailyActivitySummary($clientId, $days = 7)
    {
        $sql = "SELECT 
                    workout_date,
                    COUNT(*) as exercises_completed,
                    SUM(sets_completed) as total_sets,
                    AVG(rpe) as avg_rpe,
                    SUM(calories_burned) as total_calories,
                    SUM(duration_seconds) as total_duration
                FROM {$this->table}
                WHERE user_id = ? AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY workout_date
                ORDER BY workout_date DESC";

        return $this->db->fetchAll($sql, [$clientId, $days]);
    }

    /**
     * Obtener comparativa de progreso entre períodos
     */
    public function getProgressComparison($clientId, $currentDays = 30, $previousDays = 30)
    {
        // Período actual
        $currentStats = $this->getClientProgressStats($clientId, $currentDays);
        
        // Período anterior
        $sql = "SELECT 
                    COUNT(DISTINCT workout_date) as total_workouts,
                    COUNT(*) as total_exercises,
                    AVG(rpe) as avg_rpe,
                    SUM(calories_burned) as total_calories,
                    SUM(duration_seconds) as total_duration
                FROM {$this->table}
                WHERE user_id = ? 
                AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                AND workout_date < DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        
        $previousStats = $this->db->fetch($sql, [$clientId, $currentDays + $previousDays, $currentDays]);
        
        // Calcular cambios porcentuales
        $comparison = [
            'current' => $currentStats,
            'previous' => [
                'total_workouts' => $previousStats['total_workouts'] ?? 0,
                'total_exercises' => $previousStats['total_exercises'] ?? 0,
                'avg_rpe' => round($previousStats['avg_rpe'] ?? 0, 1),
                'total_calories' => $previousStats['total_calories'] ?? 0,
                'total_duration_minutes' => round(($previousStats['total_duration'] ?? 0) / 60, 0)
            ],
            'changes' => []
        ];
        
        // Calcular cambios porcentuales
        foreach (['total_workouts', 'total_exercises', 'total_calories', 'total_duration_minutes'] as $metric) {
            $current = $comparison['current'][$metric];
            $previous = $comparison['previous'][$metric];
            
            if ($previous > 0) {
                $comparison['changes'][$metric] = round((($current - $previous) / $previous) * 100, 1);
            } else {
                $comparison['changes'][$metric] = $current > 0 ? 100 : 0;
            }
        }
        
        return $comparison;
    }

    /**
     * Obtener clientes más activos de un entrenador
     */
    public function getTrainerActiveClients($trainerId, $days = 30)
    {
        $sql = "SELECT 
                    u.id, u.first_name, u.last_name, u.email, u.avatar,
                    COUNT(DISTINCT wl.workout_date) as workout_days,
                    COUNT(wl.id) as total_exercises,
                    MAX(wl.workout_date) as last_workout,
                    AVG(wl.rpe) as avg_rpe,
                    SUM(wl.calories_burned) as total_calories
                FROM users u
                INNER JOIN routines r ON u.id = r.client_id
                LEFT JOIN {$this->table} wl ON u.id = wl.user_id 
                    AND wl.workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                WHERE r.instructor_id = ? AND r.is_active = 1
                GROUP BY u.id
                ORDER BY workout_days DESC, total_exercises DESC";

        return $this->db->fetchAll($sql, [$days, $trainerId]);
    }

    /**
     * Obtener alertas de progreso para entrenadores
     */
    public function getProgressAlerts($trainerId)
    {
        $alerts = [];

        // Clientes inactivos (sin entrenamientos en 7 días)
        $inactiveClients = $this->db->fetchAll(
            "SELECT u.id, u.first_name, u.last_name, 
                    MAX(wl.workout_date) as last_workout,
                    DATEDIFF(CURDATE(), MAX(wl.workout_date)) as days_inactive
             FROM users u
             INNER JOIN routines r ON u.id = r.client_id
             LEFT JOIN {$this->table} wl ON u.id = wl.user_id
             WHERE r.instructor_id = ? AND r.is_active = 1
             GROUP BY u.id
             HAVING days_inactive >= 7 OR last_workout IS NULL
             ORDER BY days_inactive DESC",
            [$trainerId]
        );

        foreach ($inactiveClients as $client) {
            $alerts[] = [
                'type' => 'inactive_client',
                'priority' => $client['days_inactive'] >= 14 ? 'high' : 'medium',
                'client_id' => $client['id'],
                'client_name' => $client['first_name'] . ' ' . $client['last_name'],
                'message' => "Cliente inactivo por {$client['days_inactive']} días",
                'days_inactive' => $client['days_inactive']
            ];
        }

        // Clientes con RPE consistentemente alto (>8)
        $highRpeClients = $this->db->fetchAll(
            "SELECT u.id, u.first_name, u.last_name, AVG(wl.rpe) as avg_rpe
             FROM users u
             INNER JOIN routines r ON u.id = r.client_id
             INNER JOIN {$this->table} wl ON u.id = wl.user_id
             WHERE r.instructor_id = ? AND r.is_active = 1
             AND wl.workout_date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
             AND wl.rpe IS NOT NULL
             GROUP BY u.id
             HAVING avg_rpe >= 8
             ORDER BY avg_rpe DESC",
            [$trainerId]
        );

        foreach ($highRpeClients as $client) {
            $alerts[] = [
                'type' => 'high_rpe',
                'priority' => 'medium',
                'client_id' => $client['id'],
                'client_name' => $client['first_name'] . ' ' . $client['last_name'],
                'message' => "RPE promedio alto: " . round($client['avg_rpe'], 1),
                'avg_rpe' => round($client['avg_rpe'], 1)
            ];
        }

        return $alerts;
    }

    /**
     * Eliminar logs antiguos (limpieza de datos)
     */
    public function cleanOldLogs($daysToKeep = 365)
    {
        $sql = "DELETE FROM {$this->table} 
                WHERE workout_date < DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        
        return $this->db->query($sql, [$daysToKeep]);
    }

    /**
     * Obtener estadísticas de un ejercicio específico
     */
    public function getExerciseStats($exerciseId, $clientId = null, $days = 90)
    {
        $sql = "SELECT 
                    COUNT(*) as total_sessions,
                    AVG(sets_completed) as avg_sets,
                    AVG(CAST(reps AS UNSIGNED)) as avg_reps,
                    AVG(CAST(weight_used AS DECIMAL(8,2))) as avg_weight,
                    AVG(rpe) as avg_rpe,
                    MIN(workout_date) as first_session,
                    MAX(workout_date) as last_session
                FROM {$this->table}
                WHERE exercise_id = ? 
                AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        
        $params = [$exerciseId, $days];
        
        if ($clientId) {
            $sql .= ' AND user_id = ?';
            $params[] = $clientId;
        }
        
        return $this->db->fetch($sql, $params);
    }

    /**
     * Obtener entrenamientos recientes de un cliente
     */
    public function getClientRecentWorkouts($clientId, $limit = 10)
    {
        $sql = "SELECT 
                    wl.*,
                    e.name as exercise_name,
                    e.category as exercise_category,
                    r.name as routine_name
                FROM {$this->table} wl
                LEFT JOIN exercises e ON wl.exercise_id = e.id
                LEFT JOIN routines r ON wl.routine_id = r.id
                WHERE wl.user_id = ?
                ORDER BY wl.workout_date DESC, wl.completed_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$clientId, $limit]);
    }

    /**
     * Obtener estadísticas de ejercicios de un cliente
     */
    public function getClientExerciseStats($clientId, $days = 30)
    {
        $sql = "SELECT 
                    e.id,
                    e.name,
                    e.category,
                    COUNT(*) as sessions_count,
                    AVG(wl.sets_completed) as avg_sets,
                    AVG(CAST(wl.reps AS UNSIGNED)) as avg_reps,
                    AVG(CAST(wl.weight_used AS DECIMAL(8,2))) as avg_weight,
                    AVG(wl.rpe) as avg_rpe,
                    MAX(wl.workout_date) as last_session
                FROM {$this->table} wl
                INNER JOIN exercises e ON wl.exercise_id = e.id
                WHERE wl.user_id = ? 
                AND wl.workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY e.id
                ORDER BY sessions_count DESC";

        return $this->db->fetchAll($sql, [$clientId, $days]);
    }
}