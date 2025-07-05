<?php

namespace StyleFitness\Models;

use StyleFitness\Config\Database;
use StyleFitness\Helpers\AppHelper;
use Exception;
use PDO;

/**
 * Modelo Exercise - STYLOFITNESS
 * Maneja todas las operaciones relacionadas con ejercicios
 */

class Exercise
{
    private $db;
    private $table = 'exercises';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Crear un nuevo ejercicio
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (category_id, name, description, instructions, muscle_groups, difficulty_level, 
                 equipment_needed, video_url, image_url, duration_minutes, calories_burned, tags, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $params = [
            $data['category_id'],
            $data['name'],
            $data['description'] ?? '',
            $data['instructions'] ?? '',
            json_encode($data['muscle_groups'] ?? []),
            $data['difficulty_level'] ?? 'intermediate',
            $data['equipment_needed'] ?? '',
            $data['video_url'] ?? null,
            $data['image_url'] ?? null,
            $data['duration_minutes'] ?? null,
            $data['calories_burned'] ?? null,
            json_encode($data['tags'] ?? []),
            $data['is_active'] ?? true,
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Buscar ejercicio por ID
     */
    public function findById($id)
    {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color 
                FROM {$this->table} e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE e.id = ?";

        $exercise = $this->db->fetch($sql, [$id]);

        if ($exercise) {
            $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'], true) ?: [];
            $exercise['tags'] = json_decode($exercise['tags'], true) ?: [];
        }

        return $exercise;
    }

    /**
     * Obtener ejercicios con filtros
     */
    public function getExercises($filters = [])
    {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color 
                FROM {$this->table} e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE 1=1";

        $params = [];

        // Filtro por categoría
        if (!empty($filters['category_id'])) {
            $sql .= ' AND e.category_id = ?';
            $params[] = $filters['category_id'];
        }

        // Filtro por estado activo
        if (isset($filters['is_active'])) {
            $sql .= ' AND e.is_active = ?';
            $params[] = $filters['is_active'];
        }

        // Filtro por nivel de dificultad
        if (!empty($filters['difficulty_level'])) {
            $sql .= ' AND e.difficulty_level = ?';
            $params[] = $filters['difficulty_level'];
        }

        // Filtro de búsqueda
        if (!empty($filters['search'])) {
            $sql .= ' AND (e.name LIKE ? OR e.description LIKE ? OR e.instructions LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }

        // Filtro por grupo muscular
        if (!empty($filters['muscle_group'])) {
            $sql .= ' AND JSON_CONTAINS(e.muscle_groups, ?)';
            $params[] = json_encode($filters['muscle_group']);
        }

        // Filtro por equipo necesario
        if (!empty($filters['equipment'])) {
            $sql .= ' AND e.equipment_needed LIKE ?';
            $params[] = '%' . $filters['equipment'] . '%';
        }

        // Ordenamiento
        $orderBy = $filters['order_by'] ?? 'name';
        $orderDir = strtoupper($filters['order_dir'] ?? 'ASC');
        $sql .= " ORDER BY e.{$orderBy} {$orderDir}";

        // Límite y offset
        if (isset($filters['limit'])) {
            $sql .= ' LIMIT ?';
            $params[] = (int)$filters['limit'];

            if (isset($filters['offset'])) {
                $sql .= ' OFFSET ?';
                $params[] = (int)$filters['offset'];
            }
        }

        $exercises = $this->db->fetchAll($sql, $params);

        // Decodificar campos JSON
        foreach ($exercises as &$exercise) {
            $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'], true) ?: [];
            $exercise['tags'] = json_decode($exercise['tags'], true) ?: [];
        }

        return $exercises;
    }

    /**
     * Contar ejercicios con filtros
     */
    public function countExercises($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} e WHERE 1=1";
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= ' AND e.category_id = ?';
            $params[] = $filters['category_id'];
        }

        if (isset($filters['is_active'])) {
            $sql .= ' AND e.is_active = ?';
            $params[] = $filters['is_active'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (e.name LIKE ? OR e.description LIKE ? OR e.instructions LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }

        return $this->db->count($sql, $params);
    }

    /**
     * Obtener categorías de ejercicios
     */
    public function getCategories()
    {
        $sql = 'SELECT * FROM exercise_categories ORDER BY name ASC';
        return $this->db->fetchAll($sql);
    }

    /**
     * Obtener ejercicios por categoría
     */
    public function getExercisesByCategory($categoryId, $limit = null)
    {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color 
                FROM {$this->table} e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE e.category_id = ? AND e.is_active = 1 
                ORDER BY e.name ASC";

        $params = [$categoryId];

        if ($limit) {
            $sql .= ' LIMIT ?';
            $params[] = $limit;
        }

        $exercises = $this->db->fetchAll($sql, $params);

        foreach ($exercises as &$exercise) {
            $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'], true) ?: [];
            $exercise['tags'] = json_decode($exercise['tags'], true) ?: [];
        }

        return $exercises;
    }

    /**
     * Buscar ejercicios
     */
    public function searchExercises($query, $limit = 20)
    {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color,
                MATCH(e.name, e.description) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
                FROM {$this->table} e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE e.is_active = 1 AND (
                    MATCH(e.name, e.description) AGAINST(? IN NATURAL LANGUAGE MODE) > 0
                    OR e.name LIKE ?
                    OR e.description LIKE ?
                )
                ORDER BY relevance DESC, e.name ASC
                LIMIT ?";

        $searchTerm = '%' . $query . '%';
        $params = [$query, $query, $searchTerm, $searchTerm, $limit];

        $exercises = $this->db->fetchAll($sql, $params);

        foreach ($exercises as &$exercise) {
            $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'], true) ?: [];
            $exercise['tags'] = json_decode($exercise['tags'], true) ?: [];
        }

        return $exercises;
    }

    /**
     * Obtener ejercicios recomendados
     */
    public function getRecommendedExercises($userId, $limit = 6)
    {
        // Basado en rutinas anteriores del usuario
        $sql = "SELECT DISTINCT e.*, ec.name as category_name, ec.color as category_color 
                FROM {$this->table} e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                LEFT JOIN routine_exercises re ON e.id = re.exercise_id
                LEFT JOIN routines r ON re.routine_id = r.id
                WHERE r.client_id = ? AND e.is_active = 1
                ORDER BY RAND()
                LIMIT ?";

        $exercises = $this->db->fetchAll($sql, [$userId, $limit]);

        foreach ($exercises as &$exercise) {
            $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'], true) ?: [];
            $exercise['tags'] = json_decode($exercise['tags'], true) ?: [];
        }

        return $exercises;
    }

    /**
     * Obtener ejercicios populares
     */
    public function getPopularExercises($limit = 10)
    {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color, 
                COUNT(re.id) as usage_count
                FROM {$this->table} e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                LEFT JOIN routine_exercises re ON e.id = re.exercise_id
                WHERE e.is_active = 1
                GROUP BY e.id
                ORDER BY usage_count DESC, e.name ASC
                LIMIT ?";

        $exercises = $this->db->fetchAll($sql, [$limit]);

        foreach ($exercises as &$exercise) {
            $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'], true) ?: [];
            $exercise['tags'] = json_decode($exercise['tags'], true) ?: [];
        }

        return $exercises;
    }

    /**
     * Actualizar ejercicio
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = [];

        $allowedFields = [
            'category_id', 'name', 'description', 'instructions', 'muscle_groups',
            'difficulty_level', 'equipment_needed', 'video_url', 'image_url',
            'duration_minutes', 'calories_burned', 'tags', 'is_active',
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";

                if (in_array($field, ['muscle_groups', 'tags']) && is_array($data[$field])) {
                    $params[] = json_encode($data[$field]);
                } else {
                    $params[] = $data[$field];
                }
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
     * Eliminar ejercicio (soft delete)
     */
    public function delete($id)
    {
        return $this->update($id, ['is_active' => false]);
    }

    /**
     * Obtener ejercicios por zona corporal
     */
    public function getExercisesByBodyZone($zone, $limit = null)
    {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color 
                FROM {$this->table} e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE e.is_active = 1 AND JSON_CONTAINS(e.muscle_groups, ?)
                ORDER BY e.name ASC";

        $params = [json_encode($zone)];

        if ($limit) {
            $sql .= ' LIMIT ?';
            $params[] = $limit;
        }

        $exercises = $this->db->fetchAll($sql, $params);

        foreach ($exercises as &$exercise) {
            $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'], true) ?: [];
            $exercise['tags'] = json_decode($exercise['tags'], true) ?: [];
        }

        return $exercises;
    }

    /**
     * Obtener grupos musculares únicos
     */
    public function getMuscleGroups()
    {
        $sql = "SELECT DISTINCT muscle_groups FROM {$this->table} WHERE is_active = 1 AND muscle_groups IS NOT NULL";
        $results = $this->db->fetchAll($sql);

        $muscleGroups = [];
        foreach ($results as $result) {
            $groups = json_decode($result['muscle_groups'], true);
            if (is_array($groups)) {
                $muscleGroups = array_merge($muscleGroups, $groups);
            }
        }

        return array_unique($muscleGroups);
    }

    /**
     * Obtener equipos únicos
     */
    public function getEquipmentTypes()
    {
        $sql = "SELECT DISTINCT equipment_needed FROM {$this->table} 
                WHERE is_active = 1 AND equipment_needed IS NOT NULL AND equipment_needed != ''
                ORDER BY equipment_needed ASC";

        $results = $this->db->fetchAll($sql);

        $equipment = [];
        foreach ($results as $result) {
            if ($result['equipment_needed']) {
                // Separar por comas y limpiar
                $items = array_map('trim', explode(',', $result['equipment_needed']));
                $equipment = array_merge($equipment, $items);
            }
        }

        return array_unique($equipment);
    }

    /**
     * Subir video de ejercicio
     */
    public function uploadVideo($exerciseId, $file)
    {
        $allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido');
        }

        if ($file['size'] > 100 * 1024 * 1024) { // 100MB máximo
            throw new Exception('El archivo es demasiado grande');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'exercise_' . $exerciseId . '_' . time() . '.' . $extension;
        $uploadPath = UPLOAD_PATH . '/videos/exercises/' . $filename;

        // Crear directorio si no existe
        $videoDir = UPLOAD_PATH . '/videos/exercises';
        if (!file_exists($videoDir)) {
            mkdir($videoDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Actualizar base de datos
            $this->update($exerciseId, ['video_url' => '/uploads/videos/exercises/' . $filename]);

            return '/uploads/videos/exercises/' . $filename;
        }

        throw new Exception('Error al subir el video');
    }

    /**
     * Subir imagen de ejercicio
     */
    public function uploadImage($exerciseId, $file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido');
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB máximo
            throw new Exception('El archivo es demasiado grande');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'exercise_' . $exerciseId . '_' . time() . '.' . $extension;
        $uploadPath = UPLOAD_PATH . '/images/exercises/' . $filename;

        // Crear directorio si no existe
        $imageDir = UPLOAD_PATH . '/images/exercises';
        if (!file_exists($imageDir)) {
            mkdir($imageDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Comprimir imagen
            AppHelper::compressImage($uploadPath, $uploadPath, 85);

            // Actualizar base de datos
            $this->update($exerciseId, ['image_url' => '/uploads/images/exercises/' . $filename]);

            return '/uploads/images/exercises/' . $filename;
        }

        throw new Exception('Error al subir la imagen');
    }

    /**
     * Obtener ID de ejercicio por nombre
     */
    public function getExerciseIdByName($name)
    {
        $sql = "SELECT id FROM {$this->table} WHERE name = ? AND is_active = 1 LIMIT 1";
        $result = $this->db->fetch($sql, [$name]);
        return $result ? $result['id'] : null;
    }

    /**
     * Buscar ejercicio por nombre
     */
    public function findByName($name)
    {
        $sql = "SELECT e.*, ec.name as category_name, ec.color as category_color 
                FROM {$this->table} e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE e.name = ? AND e.is_active = 1 LIMIT 1";

        $exercise = $this->db->fetch($sql, [$name]);

        if ($exercise) {
            $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'], true) ?: [];
            $exercise['tags'] = json_decode($exercise['tags'], true) ?: [];
        }

        return $exercise;
    }

    /**
     * Obtener estadísticas de ejercicios
     */
    public function getExerciseStats()
    {
        $stats = [];

        // Total de ejercicios activos
        $stats['total_active'] = $this->db->count(
            "SELECT COUNT(*) FROM {$this->table} WHERE is_active = 1"
        );

        // Ejercicios por categoría
        $categories = $this->db->fetchAll(
            "SELECT ec.name, COUNT(e.id) as count 
             FROM exercise_categories ec
             LEFT JOIN {$this->table} e ON ec.id = e.category_id AND e.is_active = 1
             GROUP BY ec.id, ec.name
             ORDER BY count DESC"
        );

        $stats['by_category'] = [];
        foreach ($categories as $cat) {
            $stats['by_category'][$cat['name']] = $cat['count'];
        }

        // Ejercicios por nivel de dificultad
        $difficulties = $this->db->fetchAll(
            "SELECT difficulty_level, COUNT(*) as count 
             FROM {$this->table} 
             WHERE is_active = 1 
             GROUP BY difficulty_level"
        );

        $stats['by_difficulty'] = [];
        foreach ($difficulties as $diff) {
            $stats['by_difficulty'][$diff['difficulty_level']] = $diff['count'];
        }

        // Ejercicios más utilizados
        $stats['most_used'] = $this->db->fetchAll(
            "SELECT e.id, e.name, COUNT(re.id) as usage_count
             FROM {$this->table} e
             LEFT JOIN routine_exercises re ON e.id = re.exercise_id
             WHERE e.is_active = 1
             GROUP BY e.id
             ORDER BY usage_count DESC
             LIMIT 10"
        );

        return $stats;
    }
}
