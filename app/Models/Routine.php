<?php

namespace StyleFitness\Models;

use StyleFitness\Config\Database;
use Exception;
use PDO;

/**
 * Modelo Routine - STYLOFITNESS
 * Maneja todas las operaciones relacionadas con rutinas de entrenamiento
 */

class Routine
{
    private $db;
    private $table = 'routines';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Crear una nueva rutina
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (gym_id, instructor_id, client_id, name, description, objective, difficulty_level, 
                 duration_weeks, sessions_per_week, estimated_duration_minutes, is_template, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $params = [
            $data['gym_id'],
            $data['instructor_id'],
            $data['client_id'],
            $data['name'],
            $data['description'],
            $data['objective'],
            $data['difficulty_level'],
            $data['duration_weeks'],
            $data['sessions_per_week'],
            $data['estimated_duration_minutes'],
            $data['is_template'] ? 1 : 0,
            $data['is_active'] ? 1 : 0,
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Buscar rutina por ID
     */
    public function findById($id)
    {
        $sql = "SELECT r.*, 
                       g.name as gym_name,
                       i.first_name as instructor_first_name, 
                       i.last_name as instructor_last_name,
                       c.first_name as client_first_name, 
                       c.last_name as client_last_name,
                       c.email as client_email
                FROM {$this->table} r 
                LEFT JOIN gyms g ON r.gym_id = g.id
                LEFT JOIN users i ON r.instructor_id = i.id 
                LEFT JOIN users c ON r.client_id = c.id 
                WHERE r.id = ?";

        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Obtener rutinas de un cliente
     */
    public function getClientRoutines($clientId, $filters = [])
    {
        $sql = "SELECT r.*, 
                       i.first_name as instructor_first_name, 
                       i.last_name as instructor_last_name,
                       COUNT(re.id) as exercise_count
                FROM {$this->table} r 
                LEFT JOIN users i ON r.instructor_id = i.id 
                LEFT JOIN routine_exercises re ON r.id = re.routine_id
                WHERE r.client_id = ? AND r.is_active = 1";

        $params = [$clientId];

        // Aplicar filtros
        if (!empty($filters['search'])) {
            $sql .= ' AND (r.name LIKE ? OR r.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['objective'])) {
            $sql .= ' AND r.objective = ?';
            $params[] = $filters['objective'];
        }

        if (!empty($filters['difficulty'])) {
            $sql .= ' AND r.difficulty_level = ?';
            $params[] = $filters['difficulty'];
        }

        $sql .= ' GROUP BY r.id ORDER BY r.created_at DESC';

        // Límite y offset
        if (isset($filters['limit'])) {
            $sql .= ' LIMIT ?';
            $params[] = (int)$filters['limit'];

            if (isset($filters['offset'])) {
                $sql .= ' OFFSET ?';
                $params[] = (int)$filters['offset'];
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Contar rutinas de un cliente
     */
    public function countClientRoutines($clientId, $filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE client_id = ? AND is_active = 1";
        $params = [$clientId];

        if (!empty($filters['search'])) {
            $sql .= ' AND (name LIKE ? OR description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        return $this->db->count($sql, $params);
    }

    /**
     * Obtener rutinas de un instructor
     */
    public function getInstructorRoutines($instructorId, $filters = [])
    {
        $sql = "SELECT r.*, 
                       c.first_name as client_first_name, 
                       c.last_name as client_last_name,
                       c.email as client_email,
                       COUNT(re.id) as exercise_count
                FROM {$this->table} r 
                LEFT JOIN users c ON r.client_id = c.id 
                LEFT JOIN routine_exercises re ON r.id = re.routine_id
                WHERE r.instructor_id = ? AND r.is_active = 1";

        $params = [$instructorId];

        // Aplicar filtros similares
        if (!empty($filters['search'])) {
            $sql .= ' AND (r.name LIKE ? OR r.description LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        $sql .= ' GROUP BY r.id ORDER BY r.created_at DESC';

        if (isset($filters['limit'])) {
            $sql .= ' LIMIT ? OFFSET ?';
            $params[] = (int)$filters['limit'];
            $params[] = (int)($filters['offset'] ?? 0);
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Contar rutinas de un instructor
     */
    public function countInstructorRoutines($instructorId, $filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} r 
                LEFT JOIN users c ON r.client_id = c.id 
                WHERE r.instructor_id = ? AND r.is_active = 1";
        $params = [$instructorId];

        if (!empty($filters['search'])) {
            $sql .= ' AND (r.name LIKE ? OR r.description LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        return $this->db->count($sql, $params);
    }

    /**
     * Obtener todas las rutinas (admin)
     */
    public function getAllRoutines($filters = [])
    {
        $sql = "SELECT r.*, 
                       g.name as gym_name,
                       i.first_name as instructor_first_name, 
                       i.last_name as instructor_last_name,
                       c.first_name as client_first_name, 
                       c.last_name as client_last_name,
                       COUNT(re.id) as exercise_count
                FROM {$this->table} r 
                LEFT JOIN gyms g ON r.gym_id = g.id
                LEFT JOIN users i ON r.instructor_id = i.id 
                LEFT JOIN users c ON r.client_id = c.id 
                LEFT JOIN routine_exercises re ON r.id = re.routine_id
                WHERE r.is_active = 1";

        $params = [];

        // Aplicar filtros
        if (!empty($filters['search'])) {
            $sql .= ' AND (r.name LIKE ? OR r.description LIKE ? OR i.first_name LIKE ? OR c.first_name LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        $sql .= ' GROUP BY r.id ORDER BY r.created_at DESC';

        if (isset($filters['limit'])) {
            $sql .= ' LIMIT ? OFFSET ?';
            $params[] = (int)$filters['limit'];
            $params[] = (int)($filters['offset'] ?? 0);
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Contar todas las rutinas
     */
    public function countAllRoutines($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} r 
                LEFT JOIN users i ON r.instructor_id = i.id 
                LEFT JOIN users c ON r.client_id = c.id 
                WHERE r.is_active = 1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND (r.name LIKE ? OR r.description LIKE ? OR i.first_name LIKE ? OR c.first_name LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        return $this->db->count($sql, $params);
    }

    /**
     * Obtener rutinas públicas/plantillas
     */
    public function getPublicRoutines($limit = 10)
    {
        $sql = "SELECT r.*, 
                       i.first_name as instructor_first_name, 
                       i.last_name as instructor_last_name,
                       COUNT(re.id) as exercise_count
                FROM {$this->table} r 
                LEFT JOIN users i ON r.instructor_id = i.id 
                LEFT JOIN routine_exercises re ON r.id = re.routine_id
                WHERE r.is_template = 1 AND r.is_active = 1 
                GROUP BY r.id 
                ORDER BY r.created_at DESC 
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Obtener plantillas de rutinas
     */
    public function getTemplates($limit = 20)
    {
        $sql = "SELECT r.*, 
                       i.first_name as instructor_first_name, 
                       i.last_name as instructor_last_name,
                       COUNT(re.id) as exercise_count
                FROM {$this->table} r 
                LEFT JOIN users i ON r.instructor_id = i.id 
                LEFT JOIN routine_exercises re ON r.id = re.routine_id
                WHERE r.is_template = 1 AND r.is_active = 1 
                GROUP BY r.id 
                ORDER BY r.created_at DESC 
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Contar plantillas de rutinas
     */
    public function countTemplates()
    {
        return $this->db->count(
            "SELECT COUNT(*) FROM {$this->table} WHERE is_template = 1 AND is_active = 1"
        );
    }

    /**
     * Crear plantilla de rutina
     */
    public function createTemplate($data)
    {
        $data['is_template'] = 1;
        $data['client_id'] = null;
        return $this->create($data);
    }

    /**
     * Obtener ejercicios de plantilla por zona corporal
     */
    public function getTemplateExercisesByZone($templateId, $zone)
    {
        $sql = 'SELECT re.*, 
                       e.name as exercise_name,
                       e.description as exercise_description,
                       e.muscle_groups,
                       e.equipment_needed,
                       ec.name as category_name
                FROM routine_exercises re
                JOIN exercises e ON re.exercise_id = e.id
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id
                WHERE re.routine_id = ? AND JSON_CONTAINS(e.muscle_groups, ?)
                ORDER BY re.day_number ASC, re.order_index ASC';

        return $this->db->fetchAll($sql, [$templateId, json_encode($zone)]);
    }

    /**
     * Agregar ejercicio a plantilla
     */
    public function addExerciseToTemplate($data)
    {
        return $this->addExerciseToRoutine($data);
    }

    /**
     * Eliminar todos los ejercicios de una plantilla
     */
    public function removeAllTemplateExercises($templateId)
    {
        return $this->removeAllExercisesFromRoutine($templateId);
    }

    /**
     * Obtener ejercicios de una rutina
     */
    public function getRoutineExercises($routineId)
    {
        $sql = 'SELECT re.*, 
                       e.name as exercise_name,
                       e.description as exercise_description,
                       e.instructions as exercise_instructions,
                       e.muscle_groups,
                       e.equipment_needed,
                       e.video_url,
                       e.image_url,
                       ec.name as category_name,
                       ec.color as category_color
                FROM routine_exercises re
                JOIN exercises e ON re.exercise_id = e.id
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id
                WHERE re.routine_id = ?
                ORDER BY re.day_number ASC, re.order_index ASC';

        $exercises = $this->db->fetchAll($sql, [$routineId]);

        // Decodificar JSON fields
        foreach ($exercises as &$exercise) {
            if ($exercise['muscle_groups']) {
                $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'], true) ?: [];
            }
        }

        return $exercises;
    }

    /**
     * Agregar ejercicio a rutina
     */
    public function addExerciseToRoutine($data)
    {
        $sql = 'INSERT INTO routine_exercises 
                (routine_id, exercise_id, day_number, order_index, sets, reps, weight, rest_seconds, tempo, notes, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';

        $params = [
            $data['routine_id'],
            $data['exercise_id'],
            $data['day_number'],
            $data['order_index'],
            $data['sets'],
            $data['reps'],
            $data['weight'],
            $data['rest_seconds'],
            $data['tempo'],
            $data['notes'],
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Eliminar todos los ejercicios de una rutina
     */
    public function removeAllExercisesFromRoutine($routineId)
    {
        $sql = 'DELETE FROM routine_exercises WHERE routine_id = ?';
        return $this->db->query($sql, [$routineId]);
    }

    /**
     * Actualizar rutina
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = [];

        $allowedFields = [
            'name', 'description', 'objective', 'difficulty_level',
            'duration_weeks', 'sessions_per_week', 'estimated_duration_minutes',
            'is_template', 'is_active', 'client_id',
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = 'updated_at = NOW()';
        $params[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . ' WHERE id = ?';

        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    /**
     * Eliminar rutina (soft delete)
     */
    public function delete($id)
    {
        return $this->update($id, ['is_active' => false]);
    }

    /**
     * Registrar workout completado
     */
    public function logWorkout($data)
    {
        // Verificar si ya existe un log para este ejercicio hoy
        $existingLog = $this->db->fetch(
            'SELECT id FROM workout_logs 
             WHERE user_id = ? AND routine_id = ? AND exercise_id = ? 
             AND DATE(completed_at) = CURDATE()',
            [$data['user_id'], $data['routine_id'], $data['exercise_id']]
        );

        if ($existingLog) {
            // Actualizar el log existente
            $sql = 'UPDATE workout_logs 
                    SET sets_completed = ?, reps = ?, weight_used = ?, notes = ?, completed_at = ?
                    WHERE id = ?';

            $params = [
                $data['sets_completed'],
                $data['reps'],
                $data['weight_used'],
                $data['notes'],
                $data['completed_at'],
                $existingLog['id'],
            ];

            $this->db->query($sql, $params);
            return $existingLog['id'];
        } else {
            // Crear nuevo log
            $sql = 'INSERT INTO workout_logs 
                    (user_id, routine_id, exercise_id, sets_completed, reps, weight_used, notes, completed_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

            $params = [
                $data['user_id'],
                $data['routine_id'],
                $data['exercise_id'],
                $data['sets_completed'],
                $data['reps'],
                $data['weight_used'],
                $data['notes'],
                $data['completed_at'],
            ];

            return $this->db->insert($sql, $params);
        }
    }

    /**
     * Obtener progreso del cliente
     */
    public function getClientProgress($clientId, $routineId)
    {
        $sql = 'SELECT 
                    COUNT(DISTINCT wl.exercise_id) as completed_exercises,
                    COUNT(DISTINCT re.exercise_id) as total_exercises,
                    MAX(wl.completed_at) as last_workout,
                    COUNT(DISTINCT DATE(wl.completed_at)) as workout_days
                FROM routine_exercises re
                LEFT JOIN workout_logs wl ON re.exercise_id = wl.exercise_id 
                    AND wl.routine_id = ? AND wl.user_id = ?
                WHERE re.routine_id = ?';

        $progress = $this->db->fetch($sql, [$routineId, $clientId, $routineId]);

        // Calcular porcentaje de completado
        if ($progress['total_exercises'] > 0) {
            $progress['completion_percentage'] = round(
                ($progress['completed_exercises'] / $progress['total_exercises']) * 100
            );
        } else {
            $progress['completion_percentage'] = 0;
        }

        return $progress;
    }

    /**
     * Obtener estadísticas de rutinas
     */
    public function getRoutineStats($filters = [])
    {
        $stats = [];

        // Total de rutinas activas
        $stats['total_active'] = $this->db->count(
            "SELECT COUNT(*) FROM {$this->table} WHERE is_active = 1"
        );

        // Rutinas por objetivo
        $objectives = $this->db->fetchAll(
            "SELECT objective, COUNT(*) as count 
             FROM {$this->table} 
             WHERE is_active = 1 
             GROUP BY objective"
        );

        $stats['by_objective'] = [];
        foreach ($objectives as $obj) {
            $stats['by_objective'][$obj['objective']] = $obj['count'];
        }

        // Rutinas más populares (más logs de workout)
        $stats['most_popular'] = $this->db->fetchAll(
            "SELECT r.id, r.name, COUNT(wl.id) as workout_count
             FROM {$this->table} r
             LEFT JOIN workout_logs wl ON r.id = wl.routine_id
             WHERE r.is_active = 1
             GROUP BY r.id
             ORDER BY workout_count DESC
             LIMIT 5"
        );

        return $stats;
    }

    /**
     * Obtener rutinas por objetivo
     */
    public function getRoutinesByObjective($objective, $limit = 10)
    {
        $sql = "SELECT r.*, 
                       i.first_name as instructor_first_name, 
                       i.last_name as instructor_last_name,
                       COUNT(re.id) as exercise_count
                FROM {$this->table} r 
                LEFT JOIN users i ON r.instructor_id = i.id 
                LEFT JOIN routine_exercises re ON r.id = re.routine_id
                WHERE r.objective = ? AND r.is_active = 1 AND (r.is_template = 1 OR r.client_id IS NULL)
                GROUP BY r.id 
                ORDER BY r.created_at DESC 
                LIMIT ?";

        return $this->db->fetchAll($sql, [$objective, $limit]);
    }

    /**
     * Duplicar rutina (crear copia)
     */
    public function duplicate($routineId, $newData = [])
    {
        $original = $this->findById($routineId);
        if (!$original) {
            return false;
        }

        // Datos para la nueva rutina
        $data = [
            'gym_id' => $original['gym_id'],
            'instructor_id' => $newData['instructor_id'] ?? $original['instructor_id'],
            'client_id' => $newData['client_id'] ?? null,
            'name' => $newData['name'] ?? ($original['name'] . ' (Copia)'),
            'description' => $original['description'],
            'objective' => $original['objective'],
            'difficulty_level' => $original['difficulty_level'],
            'duration_weeks' => $original['duration_weeks'],
            'sessions_per_week' => $original['sessions_per_week'],
            'estimated_duration_minutes' => $original['estimated_duration_minutes'],
            'is_template' => $newData['is_template'] ?? false,
            'is_active' => true,
        ];

        // Crear nueva rutina
        $newRoutineId = $this->create($data);

        if ($newRoutineId) {
            // Copiar ejercicios
            $exercises = $this->getRoutineExercises($routineId);
            foreach ($exercises as $exercise) {
                $exerciseData = [
                    'routine_id' => $newRoutineId,
                    'exercise_id' => $exercise['exercise_id'],
                    'day_number' => $exercise['day_number'],
                    'order_index' => $exercise['order_index'],
                    'sets' => $exercise['sets'],
                    'reps' => $exercise['reps'],
                    'weight' => $exercise['weight'],
                    'rest_seconds' => $exercise['rest_seconds'],
                    'tempo' => $exercise['tempo'],
                    'notes' => $exercise['notes'],
                ];

                $this->addExerciseToRoutine($exerciseData);
            }
        }

        return $newRoutineId;
    }

    /**
     * Validar datos de rutina
     */
    public function validateRoutine($data)
    {
        $errors = [];

        // Validar nombre
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre de la rutina es obligatorio';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'El nombre debe tener al menos 3 caracteres';
        }

        // Validar objetivo
        $validObjectives = ['weight_loss', 'muscle_gain', 'strength', 'endurance', 'flexibility', 'rehabilitation'];
        if (empty($data['objective']) || !in_array($data['objective'], $validObjectives)) {
            $errors['objective'] = 'Debe seleccionar un objetivo válido';
        }

        // Validar nivel de dificultad
        $validDifficulties = ['beginner', 'intermediate', 'advanced'];
        if (empty($data['difficulty_level']) || !in_array($data['difficulty_level'], $validDifficulties)) {
            $errors['difficulty_level'] = 'Debe seleccionar un nivel de dificultad válido';
        }

        // Validar duración en semanas
        if (!isset($data['duration_weeks']) || $data['duration_weeks'] < 1 || $data['duration_weeks'] > 52) {
            $errors['duration_weeks'] = 'La duración debe ser entre 1 y 52 semanas';
        }

        // Validar sesiones por semana
        if (!isset($data['sessions_per_week']) || $data['sessions_per_week'] < 1 || $data['sessions_per_week'] > 7) {
            $errors['sessions_per_week'] = 'Las sesiones por semana deben ser entre 1 y 7';
        }

        // Validar duración estimada
        if (!isset($data['estimated_duration_minutes']) || $data['estimated_duration_minutes'] < 15 || $data['estimated_duration_minutes'] > 300) {
            $errors['estimated_duration_minutes'] = 'La duración estimada debe ser entre 15 y 300 minutos';
        }

        // Validar instructor ID si está presente
        if (!empty($data['instructor_id'])) {
            $instructor = $this->db->fetch(
                "SELECT id FROM users WHERE id = ? AND role = 'instructor' AND is_active = 1",
                [$data['instructor_id']]
            );
            if (!$instructor) {
                $errors['instructor_id'] = 'El instructor seleccionado no es válido';
            }
        }

        // Validar client ID si está presente
        if (!empty($data['client_id'])) {
            $client = $this->db->fetch(
                "SELECT id FROM users WHERE id = ? AND role = 'client' AND is_active = 1",
                [$data['client_id']]
            );
            if (!$client) {
                $errors['client_id'] = 'El cliente seleccionado no es válido';
            }
        }

        return $errors;
    }

    /**
     * Obtener estadísticas de cliente
     */
    public function getClientStats($clientId)
    {
        $stats = [];

        // Total de rutinas asignadas
        $stats['total_routines'] = $this->db->count(
            "SELECT COUNT(*) FROM {$this->table} WHERE client_id = ? AND is_active = 1",
            [$clientId]
        );

        // Rutinas completadas (con al menos un workout log)
        $stats['completed_routines'] = $this->db->count(
            "SELECT COUNT(DISTINCT r.id) 
             FROM {$this->table} r 
             INNER JOIN workout_logs wl ON r.id = wl.routine_id 
             WHERE r.client_id = ? AND r.is_active = 1",
            [$clientId]
        );

        // Total de workouts realizados
        $stats['total_workouts'] = $this->db->count(
            "SELECT COUNT(DISTINCT DATE(wl.completed_at)) 
             FROM workout_logs wl 
             INNER JOIN {$this->table} r ON wl.routine_id = r.id 
             WHERE r.client_id = ?",
            [$clientId]
        );

        // Último workout
        $lastWorkout = $this->db->fetch(
            "SELECT MAX(wl.completed_at) as last_workout 
             FROM workout_logs wl 
             INNER JOIN {$this->table} r ON wl.routine_id = r.id 
             WHERE r.client_id = ?",
            [$clientId]
        );
        $stats['last_workout'] = $lastWorkout['last_workout'];

        // Rutinas por objetivo
        $stats['routines_by_objective'] = $this->db->fetchAll(
            "SELECT objective, COUNT(*) as count 
             FROM {$this->table} 
             WHERE client_id = ? AND is_active = 1 
             GROUP BY objective",
            [$clientId]
        );

        // Progreso semanal (últimas 4 semanas)
        $stats['weekly_progress'] = $this->db->fetchAll(
            "SELECT 
                YEARWEEK(wl.completed_at) as week,
                COUNT(DISTINCT DATE(wl.completed_at)) as workout_days,
                COUNT(wl.id) as total_exercises
             FROM workout_logs wl 
             INNER JOIN {$this->table} r ON wl.routine_id = r.id 
             WHERE r.client_id = ? AND wl.completed_at >= DATE_SUB(NOW(), INTERVAL 4 WEEK)
             GROUP BY YEARWEEK(wl.completed_at)
             ORDER BY week DESC",
            [$clientId]
        );

        return $stats;
    }

    /**
     * Obtener estadísticas de instructor
     */
    public function getInstructorStats($instructorId)
    {
        $stats = [];

        // Total de rutinas creadas
        $stats['total_routines'] = $this->db->count(
            "SELECT COUNT(*) FROM {$this->table} WHERE instructor_id = ? AND is_active = 1",
            [$instructorId]
        );

        // Total de clientes atendidos
        $stats['total_clients'] = $this->db->count(
            "SELECT COUNT(DISTINCT client_id) FROM {$this->table} 
             WHERE instructor_id = ? AND client_id IS NOT NULL AND is_active = 1",
            [$instructorId]
        );

        // Rutinas activas (con actividad reciente)
        $stats['active_routines'] = $this->db->count(
            "SELECT COUNT(DISTINCT r.id) 
             FROM {$this->table} r 
             INNER JOIN workout_logs wl ON r.id = wl.routine_id 
             WHERE r.instructor_id = ? AND r.is_active = 1 
             AND wl.completed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            [$instructorId]
        );

        // Plantillas creadas
        $stats['templates_created'] = $this->db->count(
            "SELECT COUNT(*) FROM {$this->table} 
             WHERE instructor_id = ? AND is_template = 1 AND is_active = 1",
            [$instructorId]
        );

        // Rutinas por objetivo
        $stats['routines_by_objective'] = $this->db->fetchAll(
            "SELECT objective, COUNT(*) as count 
             FROM {$this->table} 
             WHERE instructor_id = ? AND is_active = 1 
             GROUP BY objective",
            [$instructorId]
        );

        // Clientes más activos
        $stats['most_active_clients'] = $this->db->fetchAll(
            "SELECT 
                u.id, u.first_name, u.last_name, u.email,
                COUNT(DISTINCT DATE(wl.completed_at)) as workout_days,
                MAX(wl.completed_at) as last_workout
             FROM users u
             INNER JOIN {$this->table} r ON u.id = r.client_id
             INNER JOIN workout_logs wl ON r.id = wl.routine_id
             WHERE r.instructor_id = ? AND r.is_active = 1
             GROUP BY u.id
             ORDER BY workout_days DESC
             LIMIT 5",
            [$instructorId]
        );

        // Rendimiento mensual (últimos 6 meses)
        $stats['monthly_performance'] = $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(wl.completed_at, '%Y-%m') as month,
                COUNT(DISTINCT r.client_id) as active_clients,
                COUNT(DISTINCT DATE(wl.completed_at)) as total_workout_days,
                COUNT(wl.id) as total_exercises
             FROM workout_logs wl
             INNER JOIN {$this->table} r ON wl.routine_id = r.id
             WHERE r.instructor_id = ? AND wl.completed_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(wl.completed_at, '%Y-%m')
             ORDER BY month DESC",
            [$instructorId]
        );

        return $stats;
    }

    /**
     * Obtener estadísticas globales
     */
    public function getGlobalStats()
    {
        $stats = [];

        // Estadísticas generales
        $stats['total_routines'] = $this->db->count(
            "SELECT COUNT(*) FROM {$this->table} WHERE is_active = 1"
        );

        $stats['total_templates'] = $this->db->count(
            "SELECT COUNT(*) FROM {$this->table} WHERE is_template = 1 AND is_active = 1"
        );

        $stats['total_workouts'] = $this->db->count(
            'SELECT COUNT(*) FROM workout_logs'
        );

        $stats['active_clients'] = $this->db->count(
            "SELECT COUNT(DISTINCT client_id) FROM {$this->table} 
             WHERE client_id IS NOT NULL AND is_active = 1"
        );

        // Rutinas por objetivo
        $stats['routines_by_objective'] = [];
        $objectives = $this->db->fetchAll(
            "SELECT objective, COUNT(*) as count 
             FROM {$this->table} 
             WHERE is_active = 1 
             GROUP BY objective"
        );
        foreach ($objectives as $obj) {
            $stats['routines_by_objective'][$obj['objective']] = $obj['count'];
        }

        // Rutinas por nivel de dificultad
        $stats['routines_by_difficulty'] = [];
        $difficulties = $this->db->fetchAll(
            "SELECT difficulty_level, COUNT(*) as count 
             FROM {$this->table} 
             WHERE is_active = 1 
             GROUP BY difficulty_level"
        );
        foreach ($difficulties as $diff) {
            $stats['routines_by_difficulty'][$diff['difficulty_level']] = $diff['count'];
        }

        // Instructores más activos
        $stats['most_active_instructors'] = $this->db->fetchAll(
            "SELECT 
                u.id, u.first_name, u.last_name,
                COUNT(r.id) as routines_created,
                COUNT(DISTINCT r.client_id) as clients_served
             FROM users u
             INNER JOIN {$this->table} r ON u.id = r.instructor_id
             WHERE u.role = 'instructor' AND r.is_active = 1
             GROUP BY u.id
             ORDER BY routines_created DESC
             LIMIT 10"
        );

        // Actividad por mes (últimos 12 meses)
        $stats['monthly_activity'] = $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as routines_created
             FROM {$this->table}
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month DESC"
        );

        // Rutinas más populares (más utilizadas)
        $stats['most_popular_routines'] = $this->db->fetchAll(
            "SELECT 
                r.id, r.name, r.objective, r.difficulty_level,
                u.first_name as instructor_name,
                COUNT(DISTINCT wl.user_id) as users_count,
                COUNT(wl.id) as total_workouts
             FROM {$this->table} r
             LEFT JOIN users u ON r.instructor_id = u.id
             LEFT JOIN workout_logs wl ON r.id = wl.routine_id
             WHERE r.is_active = 1
             GROUP BY r.id
             HAVING users_count > 0
             ORDER BY users_count DESC, total_workouts DESC
             LIMIT 10"
        );

        return $stats;
    }
}
