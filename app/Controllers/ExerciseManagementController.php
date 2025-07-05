<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\Exercise;
use StyleFitness\Models\User;
use StyleFitness\Helpers\AppHelper;
use Exception;
use PDO;

/**
 * Controlador de Gestión de Ejercicios - STYLOFITNESS
 * Maneja la administración completa de ejercicios y videos por parte de administradores
 */
class ExerciseManagementController
{
    private $db;
    private $exerciseModel;
    private $userModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->exerciseModel = new Exercise();
        $this->userModel = new User();
    }

    /**
     * Página principal de gestión de ejercicios
     */
    public function index()
    {
        // Verificar permisos de administrador
        if (!$this->checkAdminPermissions()) {
            return;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'category_id' => isset($_GET['category']) ? (int)$_GET['category'] : null,
            'difficulty' => AppHelper::sanitize($_GET['difficulty'] ?? ''),
            'muscle_group' => AppHelper::sanitize($_GET['muscle_group'] ?? ''),
            'is_active' => isset($_GET['status']) ? (bool)$_GET['status'] : null,
            'limit' => $perPage,
            'offset' => $offset
        ];

        $exercises = $this->exerciseModel->getExercises($filters);
        $totalExercises = $this->exerciseModel->countExercises($filters);
        $categories = $this->exerciseModel->getCategories();
        $muscleGroups = $this->exerciseModel->getMuscleGroups();

        // Calcular paginación
        $totalPages = ceil($totalExercises / $perPage);
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalExercises,
            'per_page' => $perPage,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
        ];

        $pageTitle = 'Gestión de Ejercicios - STYLOFITNESS';
        $additionalCSS = ['exercise-management.css'];
        $additionalJS = ['exercise-management.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/exercises/index.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Formulario para crear nuevo ejercicio
     */
    public function create()
    {
        if (!$this->checkAdminPermissions()) {
            return;
        }

        $categories = $this->exerciseModel->getCategories();
        $muscleGroups = $this->exerciseModel->getMuscleGroups();
        
        // Obtener datos de sesión si hay errores
        $errors = $_SESSION['exercise_errors'] ?? [];
        $oldData = $_SESSION['exercise_data'] ?? [];
        unset($_SESSION['exercise_errors'], $_SESSION['exercise_data']);

        $pageTitle = 'Crear Ejercicio - STYLOFITNESS';
        $additionalCSS = ['exercise-form.css'];
        $additionalJS = ['exercise-form.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/exercises/create.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Procesar creación de ejercicio
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/admin/exercises');
            return;
        }

        if (!$this->checkAdminPermissions()) {
            return;
        }

        // Validar token CSRF
        if (!AppHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            AppHelper::setFlashMessage('error', 'Token de seguridad inválido');
            AppHelper::redirect('/admin/exercises/create');
            return;
        }

        $user = AppHelper::getCurrentUser();
        
        $data = [
            'category_id' => isset($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'name' => AppHelper::sanitize($_POST['name'] ?? ''),
            'description' => AppHelper::sanitize($_POST['description'] ?? ''),
            'instructions' => AppHelper::sanitize($_POST['instructions'] ?? ''),
            'muscle_groups' => isset($_POST['muscle_groups']) ? $_POST['muscle_groups'] : [],
            'difficulty_level' => AppHelper::sanitize($_POST['difficulty_level'] ?? 'intermediate'),
            'equipment_needed' => AppHelper::sanitize($_POST['equipment_needed'] ?? ''),
            'duration_minutes' => isset($_POST['duration_minutes']) ? (int)$_POST['duration_minutes'] : null,
            'calories_burned' => isset($_POST['calories_burned']) ? (int)$_POST['calories_burned'] : null,
            'tags' => isset($_POST['tags']) ? explode(',', $_POST['tags']) : [],
            'created_by' => $user['id'],
            'is_active' => true
        ];

        // Validar datos
        $errors = $this->validateExerciseData($data);

        // Procesar video si se subió
        if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            $videoResult = $this->processVideoUpload($_FILES['video']);
            if ($videoResult['success']) {
                $data['video_url'] = $videoResult['video_url'];
                $data['video_thumbnail'] = $videoResult['thumbnail_url'];
            } else {
                $errors['video'] = $videoResult['error'];
            }
        }

        // Procesar imagen si se subió
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageResult = $this->processImageUpload($_FILES['image']);
            if ($imageResult['success']) {
                $data['image_url'] = $imageResult['image_url'];
            } else {
                $errors['image'] = $imageResult['error'];
            }
        }

        if (!empty($errors)) {
            $_SESSION['exercise_errors'] = $errors;
            $_SESSION['exercise_data'] = $data;
            AppHelper::redirect('/admin/exercises/create');
            return;
        }

        // Crear ejercicio
        $exerciseId = $this->exerciseModel->create($data);

        if ($exerciseId) {
            AppHelper::setFlashMessage('success', 'Ejercicio creado exitosamente');
            AppHelper::redirect('/admin/exercises/view/' . $exerciseId);
        } else {
            AppHelper::setFlashMessage('error', 'Error al crear el ejercicio');
            AppHelper::redirect('/admin/exercises/create');
        }
    }

    /**
     * Ver detalles de un ejercicio
     */
    public function show($id)
    {
        if (!$this->checkAdminPermissions()) {
            return;
        }

        $exercise = $this->exerciseModel->findById($id);
        
        if (!$exercise) {
            AppHelper::setFlashMessage('error', 'Ejercicio no encontrado');
            AppHelper::redirect('/admin/exercises');
            return;
        }

        // Obtener estadísticas de uso
        $usageStats = $this->getExerciseUsageStats($id);
        
        $pageTitle = $exercise['name'] . ' - Gestión de Ejercicios';
        $additionalCSS = ['exercise-view.css'];
        $additionalJS = ['exercise-view.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/exercises/show.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Formulario para editar ejercicio
     */
    public function edit($id)
    {
        if (!$this->checkAdminPermissions()) {
            return;
        }

        $exercise = $this->exerciseModel->findById($id);
        
        if (!$exercise) {
            AppHelper::setFlashMessage('error', 'Ejercicio no encontrado');
            AppHelper::redirect('/admin/exercises');
            return;
        }

        $categories = $this->exerciseModel->getCategories();
        $muscleGroups = $this->exerciseModel->getMuscleGroups();
        
        // Obtener datos de sesión si hay errores
        $errors = $_SESSION['exercise_errors'] ?? [];
        $oldData = $_SESSION['exercise_data'] ?? $exercise;
        unset($_SESSION['exercise_errors'], $_SESSION['exercise_data']);

        $pageTitle = 'Editar: ' . $exercise['name'] . ' - STYLOFITNESS';
        $additionalCSS = ['exercise-form.css'];
        $additionalJS = ['exercise-form.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/exercises/edit.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Procesar actualización de ejercicio
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/admin/exercises');
            return;
        }

        if (!$this->checkAdminPermissions()) {
            return;
        }

        $exercise = $this->exerciseModel->findById($id);
        if (!$exercise) {
            AppHelper::setFlashMessage('error', 'Ejercicio no encontrado');
            AppHelper::redirect('/admin/exercises');
            return;
        }

        // Validar token CSRF
        if (!AppHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            AppHelper::setFlashMessage('error', 'Token de seguridad inválido');
            AppHelper::redirect('/admin/exercises/edit/' . $id);
            return;
        }

        $data = [
            'category_id' => isset($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'name' => AppHelper::sanitize($_POST['name'] ?? ''),
            'description' => AppHelper::sanitize($_POST['description'] ?? ''),
            'instructions' => AppHelper::sanitize($_POST['instructions'] ?? ''),
            'muscle_groups' => isset($_POST['muscle_groups']) ? $_POST['muscle_groups'] : [],
            'difficulty_level' => AppHelper::sanitize($_POST['difficulty_level'] ?? 'intermediate'),
            'equipment_needed' => AppHelper::sanitize($_POST['equipment_needed'] ?? ''),
            'duration_minutes' => isset($_POST['duration_minutes']) ? (int)$_POST['duration_minutes'] : null,
            'calories_burned' => isset($_POST['calories_burned']) ? (int)$_POST['calories_burned'] : null,
            'tags' => isset($_POST['tags']) ? explode(',', $_POST['tags']) : [],
            'is_active' => isset($_POST['is_active'])
        ];

        // Validar datos
        $errors = $this->validateExerciseData($data);

        // Procesar nuevo video si se subió
        if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            $videoResult = $this->processVideoUpload($_FILES['video']);
            if ($videoResult['success']) {
                // Eliminar video anterior si existe
                if ($exercise['video_url']) {
                    $this->deleteVideoFile($exercise['video_url']);
                }
                $data['video_url'] = $videoResult['video_url'];
                $data['video_thumbnail'] = $videoResult['thumbnail_url'];
            } else {
                $errors['video'] = $videoResult['error'];
            }
        }

        // Procesar nueva imagen si se subió
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageResult = $this->processImageUpload($_FILES['image']);
            if ($imageResult['success']) {
                // Eliminar imagen anterior si existe
                if ($exercise['image_url']) {
                    $this->deleteImageFile($exercise['image_url']);
                }
                $data['image_url'] = $imageResult['image_url'];
            } else {
                $errors['image'] = $imageResult['error'];
            }
        }

        if (!empty($errors)) {
            $_SESSION['exercise_errors'] = $errors;
            $_SESSION['exercise_data'] = array_merge($exercise, $data);
            AppHelper::redirect('/admin/exercises/edit/' . $id);
            return;
        }

        // Actualizar ejercicio
        if ($this->exerciseModel->update($id, $data)) {
            AppHelper::setFlashMessage('success', 'Ejercicio actualizado exitosamente');
            AppHelper::redirect('/admin/exercises/view/' . $id);
        } else {
            AppHelper::setFlashMessage('error', 'Error al actualizar el ejercicio');
            AppHelper::redirect('/admin/exercises/edit/' . $id);
        }
    }

    /**
     * Eliminar ejercicio
     */
    public function delete($id)
    {
        if (!$this->checkAdminPermissions()) {
            return;
        }

        $exercise = $this->exerciseModel->findById($id);
        if (!$exercise) {
            AppHelper::setFlashMessage('error', 'Ejercicio no encontrado');
            AppHelper::redirect('/admin/exercises');
            return;
        }

        // Verificar si el ejercicio está siendo usado en rutinas activas
        if ($this->isExerciseInUse($id)) {
            AppHelper::setFlashMessage('error', 'No se puede eliminar el ejercicio porque está siendo usado en rutinas activas');
            AppHelper::redirect('/admin/exercises');
            return;
        }

        if ($this->exerciseModel->delete($id)) {
            // Eliminar archivos asociados
            if ($exercise['video_url']) {
                $this->deleteVideoFile($exercise['video_url']);
            }
            if ($exercise['image_url']) {
                $this->deleteImageFile($exercise['image_url']);
            }
            
            AppHelper::setFlashMessage('success', 'Ejercicio eliminado exitosamente');
        } else {
            AppHelper::setFlashMessage('error', 'Error al eliminar el ejercicio');
        }

        AppHelper::redirect('/admin/exercises');
    }

    /**
     * Gestión de categorías de ejercicios
     */
    public function categories()
    {
        if (!$this->checkAdminPermissions()) {
            return;
        }

        $categories = $this->exerciseModel->getCategories();
        
        $pageTitle = 'Categorías de Ejercicios - STYLOFITNESS';
        $additionalCSS = ['categories-management.css'];
        $additionalJS = ['categories-management.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/exercises/categories.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * API para obtener ejercicios por categoría (AJAX)
     */
    public function getExercisesByCategory()
    {
        if (!$this->checkAdminPermissions()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        $categoryId = $_GET['category_id'] ?? '';
        
        if (empty($categoryId)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de categoría requerido']);
            return;
        }

        try {
            $db = Database::getInstance();
            
            $stmt = $db->prepare("
                SELECT e.*, ec.name as category_name 
                FROM exercises e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE e.category_id = ? AND e.is_active = 1
                ORDER BY e.name
            ");
            
            $stmt->execute([$categoryId]);
            $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar datos
            foreach ($exercises as &$exercise) {
                $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'] ?? '[]', true);
                $exercise['tags'] = json_decode($exercise['tags'] ?? '[]', true);
            }
            
            echo json_encode([
                'success' => true,
                'exercises' => $exercises
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting exercises by category: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Obtener estadísticas de uso
     */
    public function getUsageStats()
    {
        if (!$this->checkAdminPermissions()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        $exerciseId = $_GET['exercise_id'] ?? '';
        
        if (empty($exerciseId)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de ejercicio requerido']);
            return;
        }

        try {
            $db = Database::getInstance();
            
            // Obtener estadísticas básicas
            $stmt = $db->prepare("
                SELECT 
                    COUNT(DISTINCT re.routine_id) as total_routines,
                    COUNT(DISTINCT wl.user_id) as active_clients,
                    COUNT(wl.id) as total_workouts,
                    AVG(wl.rpe) as average_rating
                FROM routine_exercises re
                LEFT JOIN workout_logs wl ON re.exercise_id = wl.exercise_id
                WHERE re.exercise_id = ?
            ");
            
            $stmt->execute([$exerciseId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Obtener uso reciente (últimos 30 días)
            $stmt = $db->prepare("
                SELECT DATE(wl.workout_date) as date, COUNT(*) as workouts
                FROM workout_logs wl
                WHERE wl.exercise_id = ? AND wl.workout_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(wl.workout_date)
                ORDER BY date DESC
                LIMIT 30
            ");
            
            $stmt->execute([$exerciseId]);
            $recentUsage = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'stats' => [
                    'total_routines' => (int)($stats['total_routines'] ?? 0),
                    'active_clients' => (int)($stats['active_clients'] ?? 0),
                    'total_workouts' => (int)($stats['total_workouts'] ?? 0),
                    'average_rating' => round((float)($stats['average_rating'] ?? 0), 1),
                    'recent_usage' => $recentUsage
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting usage stats: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Verificar permisos de administrador
     */
    private function checkAdminPermissions()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return false;
        }

        $user = AppHelper::getCurrentUser();
        if ($user['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            AppHelper::redirect('/dashboard');
            return false;
        }

        return true;
    }

    /**
     * Obtener todas las categorías de ejercicios
     */
    public function getCategories()
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM exercise_categories WHERE is_active = 1 ORDER BY name");
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $categories;
        } catch (Exception $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas generales de ejercicios
     */
    public function getGeneralStats()
    {
        try {
            $db = Database::getInstance();
            
            // Total de ejercicios
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM exercises WHERE is_active = 1");
            $stmt->execute();
            $totalExercises = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Ejercicios con video
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM exercises WHERE is_active = 1 AND video_url IS NOT NULL AND video_url != ''");
            $stmt->execute();
            $withVideo = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total de categorías
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM exercise_categories WHERE is_active = 1");
            $stmt->execute();
            $totalCategories = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Ejercicio más usado
            $stmt = $db->prepare("
                SELECT e.name, COUNT(re.exercise_id) as usage_count
                FROM exercises e
                LEFT JOIN routine_exercises re ON e.id = re.exercise_id
                WHERE e.is_active = 1
                GROUP BY e.id, e.name
                ORDER BY usage_count DESC
                LIMIT 1
            ");
            $stmt->execute();
            $mostUsed = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_exercises' => (int)$totalExercises,
                'with_video' => (int)$withVideo,
                'total_categories' => (int)$totalCategories,
                'most_used' => $mostUsed ? $mostUsed['name'] : 'N/A'
            ];
            
        } catch (Exception $e) {
            error_log("Error getting general stats: " . $e->getMessage());
            return [
                'total_exercises' => 0,
                'with_video' => 0,
                'total_categories' => 0,
                'most_used' => 'N/A'
            ];
        }
    }

    /**
     * Obtener un ejercicio por ID
     */
    public function getExercise($id)
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT e.*, ec.name as category_name 
                FROM exercises e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE e.id = ? AND e.is_active = 1
            ");
            $stmt->execute([$id]);
            $exercise = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($exercise) {
                $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'] ?? '[]', true);
                $exercise['tags'] = json_decode($exercise['tags'] ?? '[]', true);
            }
            
            return $exercise;
        } catch (Exception $e) {
            error_log("Error getting exercise: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener lista de ejercicios con filtros
     */
    public function getExercises($filters = [])
    {
        try {
            $db = Database::getInstance();
            
            $where = ['e.is_active = 1'];
            $params = [];
            
            // Aplicar filtros
            if (!empty($filters['search'])) {
                $where[] = '(e.name LIKE ? OR e.description LIKE ?)';
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (!empty($filters['category_id'])) {
                $where[] = 'e.category_id = ?';
                $params[] = $filters['category_id'];
            }
            
            if (!empty($filters['difficulty'])) {
                $where[] = 'e.difficulty = ?';
                $params[] = $filters['difficulty'];
            }
            
            $whereClause = implode(' AND ', $where);
            
            $stmt = $db->prepare("
                SELECT e.*, ec.name as category_name 
                FROM exercises e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE {$whereClause}
                ORDER BY e.name
            ");
            
            $stmt->execute($params);
            $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar datos
            foreach ($exercises as &$exercise) {
                $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'] ?? '[]', true);
                $exercise['tags'] = json_decode($exercise['tags'] ?? '[]', true);
            }
            
            return $exercises;
        } catch (Exception $e) {
            error_log("Error getting exercises: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener ejercicios relacionados
     */
    public function getRelatedExercises($exerciseId, $limit = 5)
    {
        try {
            $db = Database::getInstance();
            
            // Obtener ejercicios de la misma categoría
            $stmt = $db->prepare("
                SELECT e2.*, ec.name as category_name
                FROM exercises e1
                JOIN exercises e2 ON e1.category_id = e2.category_id
                LEFT JOIN exercise_categories ec ON e2.category_id = ec.id
                WHERE e1.id = ? AND e2.id != ? AND e2.is_active = 1
                ORDER BY RAND()
                LIMIT ?
            ");
            
            $stmt->execute([$exerciseId, $exerciseId, $limit]);
            $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar datos
            foreach ($exercises as &$exercise) {
                $exercise['muscle_groups'] = json_decode($exercise['muscle_groups'] ?? '[]', true);
                $exercise['tags'] = json_decode($exercise['tags'] ?? '[]', true);
            }
            
            return $exercises;
        } catch (Exception $e) {
            error_log("Error getting related exercises: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Validar datos del ejercicio
     */
    private function validateExerciseData($data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'El nombre del ejercicio es requerido';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'El nombre debe tener al menos 3 caracteres';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'La descripción es requerida';
        }

        if (empty($data['instructions'])) {
            $errors['instructions'] = 'Las instrucciones son requeridas';
        }

        if (empty($data['muscle_groups']) || !is_array($data['muscle_groups'])) {
            $errors['muscle_groups'] = 'Debe seleccionar al menos un grupo muscular';
        }

        if (!in_array($data['difficulty_level'], ['beginner', 'intermediate', 'advanced'])) {
            $errors['difficulty_level'] = 'Nivel de dificultad inválido';
        }

        return $errors;
    }

    /**
     * Procesar subida de video
     */
    private function processVideoUpload($file)
    {
        $allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        $maxSize = 100 * 1024 * 1024; // 100MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Tipo de archivo no permitido. Solo se permiten videos MP4, WebM y OGG'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'El archivo es demasiado grande. Máximo 100MB'];
        }
        
        $uploadDir = PUBLIC_PATH . '/uploads/videos/exercises/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = uniqid() . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Generar thumbnail del video (requiere FFmpeg)
            $thumbnailUrl = $this->generateVideoThumbnail($filePath, $fileName);
            
            return [
                'success' => true,
                'video_url' => '/uploads/videos/exercises/' . $fileName,
                'thumbnail_url' => $thumbnailUrl
            ];
        }
        
        return ['success' => false, 'error' => 'Error al subir el archivo'];
    }

    /**
     * Procesar subida de imagen
     */
    private function processImageUpload($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Tipo de archivo no permitido. Solo se permiten imágenes JPEG, PNG y WebP'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'El archivo es demasiado grande. Máximo 5MB'];
        }
        
        $uploadDir = PUBLIC_PATH . '/uploads/images/exercises/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = uniqid() . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => true,
                'image_url' => '/uploads/images/exercises/' . $fileName
            ];
        }
        
        return ['success' => false, 'error' => 'Error al subir el archivo'];
    }

    /**
     * Generar thumbnail de video
     */
    private function generateVideoThumbnail($videoPath, $videoFileName)
    {
        // Esta función requiere FFmpeg instalado en el servidor
        // Por ahora retornamos una imagen por defecto
        return '/assets/images/video-placeholder.jpg';
    }

    /**
     * Eliminar archivo de video
     */
    private function deleteVideoFile($videoUrl)
    {
        $filePath = PUBLIC_PATH . $videoUrl;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Eliminar archivo de imagen
     */
    private function deleteImageFile($imageUrl)
    {
        $filePath = PUBLIC_PATH . $imageUrl;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Verificar si el ejercicio está siendo usado
     */
    private function isExerciseInUse($exerciseId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM routine_exercises re 
            JOIN routines r ON re.routine_id = r.id 
            WHERE re.exercise_id = ? AND r.is_active = 1
        ");
        $stmt->execute([$exerciseId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    /**
     * Obtener estadísticas de uso del ejercicio
     */
    private function getExerciseUsageStats($exerciseId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(DISTINCT re.routine_id) as routines_count,
                COUNT(DISTINCT r.client_id) as clients_count,
                AVG(wl.rpe) as avg_rpe,
                COUNT(wl.id) as total_workouts
            FROM routine_exercises re
            LEFT JOIN routines r ON re.routine_id = r.id
            LEFT JOIN workout_logs wl ON re.exercise_id = wl.exercise_id
            WHERE re.exercise_id = ?
        ");
        $stmt->execute([$exerciseId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}