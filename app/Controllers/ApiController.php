<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\User;
use StyleFitness\Models\Product;
use StyleFitness\Models\Order;
use StyleFitness\Models\GroupClass;
use StyleFitness\Models\Routine;
use StyleFitness\Models\Exercise;
use StyleFitness\Helpers\AppHelper;
use Exception;

/**
 * Controlador API - STYLOFITNESS
 * Endpoints para aplicaciones móviles e integraciones
 */

class ApiController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();

        // Configurar headers para API
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        // Manejar preflight OPTIONS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    // ==========================================
    // ENDPOINTS DE RUTINAS
    // ==========================================

    public function routines()
    {
        $this->requireAuth();

        $user = AppHelper::getCurrentUser();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 50) : 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'objective' => AppHelper::sanitize($_GET['objective'] ?? ''),
            'difficulty' => AppHelper::sanitize($_GET['difficulty'] ?? ''),
            'limit' => $limit,
            'offset' => $offset,
        ];

        $routineModel = new Routine();

        if ($user['role'] === 'client') {
            $routines = $routineModel->getClientRoutines($user['id'], $filters);
            $total = $routineModel->countClientRoutines($user['id'], $filters);
        } elseif ($user['role'] === 'instructor') {
            $routines = $routineModel->getInstructorRoutines($user['id'], $filters);
            $total = $routineModel->countInstructorRoutines($user['id'], $filters);
        } else {
            $routines = $routineModel->getAllRoutines($filters);
            $total = $routineModel->countAllRoutines($filters);
        }

        $this->jsonResponse([
            'success' => true,
            'data' => $routines,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
            ],
        ]);
    }

    public function routine($id)
    {
        $this->requireAuth();

        $routineModel = new Routine();
        $routine = $routineModel->findById($id);

        if (!$routine) {
            $this->jsonResponse(['error' => 'Rutina no encontrada'], 404);
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!$this->canViewRoutine($routine, $user)) {
            $this->jsonResponse(['error' => 'Sin permisos'], 403);
            return;
        }

        // Obtener ejercicios de la rutina
        $exercises = $routineModel->getRoutineExercises($id);
        $routine['exercises'] = $exercises;

        // Obtener progreso si es cliente
        if ($user['role'] === 'client' && $routine['client_id'] == $user['id']) {
            $routine['progress'] = $routineModel->getClientProgress($user['id'], $id);
        }

        $this->jsonResponse([
            'success' => true,
            'data' => $routine,
        ]);
    }

    public function createRoutine()
    {
        $this->requireAuth(['instructor', 'admin']);

        $input = $this->getJsonInput();
        $user = AppHelper::getCurrentUser();

        $data = [
            'gym_id' => $user['gym_id'],
            'instructor_id' => $user['id'],
            'client_id' => $input['client_id'] ?? null,
            'name' => AppHelper::sanitize($input['name'] ?? ''),
            'description' => AppHelper::sanitize($input['description'] ?? ''),
            'objective' => AppHelper::sanitize($input['objective'] ?? ''),
            'difficulty_level' => AppHelper::sanitize($input['difficulty_level'] ?? ''),
            'duration_weeks' => (int)($input['duration_weeks'] ?? 4),
            'sessions_per_week' => (int)($input['sessions_per_week'] ?? 3),
            'estimated_duration_minutes' => (int)($input['estimated_duration_minutes'] ?? 60),
            'is_template' => $input['is_template'] ?? false,
            'is_active' => true,
        ];

        $routineModel = new Routine();
        $errors = $routineModel->validateRoutine($data);

        if (!empty($errors)) {
            $this->jsonResponse(['error' => 'Datos inválidos', 'validation_errors' => $errors], 400);
            return;
        }

        $routineId = $routineModel->create($data);

        if ($routineId) {
            // Procesar ejercicios
            if (!empty($input['exercises'])) {
                $this->processRoutineExercises($routineId, $input['exercises']);
            }

            $routine = $routineModel->findById($routineId);
            $this->jsonResponse([
                'success' => true,
                'message' => 'Rutina creada exitosamente',
                'data' => $routine,
            ], 201);
        } else {
            $this->jsonResponse(['error' => 'Error al crear la rutina'], 500);
        }
    }

    public function updateRoutine($id)
    {
        $this->requireAuth(['instructor', 'admin']);

        $input = $this->getJsonInput();
        $user = AppHelper::getCurrentUser();

        $routineModel = new Routine();
        $routine = $routineModel->findById($id);

        if (!$routine) {
            $this->jsonResponse(['error' => 'Rutina no encontrada'], 404);
            return;
        }

        if (!$this->canEditRoutine($routine, $user)) {
            $this->jsonResponse(['error' => 'Sin permisos'], 403);
            return;
        }

        $data = [
            'name' => AppHelper::sanitize($input['name'] ?? ''),
            'description' => AppHelper::sanitize($input['description'] ?? ''),
            'objective' => AppHelper::sanitize($input['objective'] ?? ''),
            'difficulty_level' => AppHelper::sanitize($input['difficulty_level'] ?? ''),
            'duration_weeks' => (int)($input['duration_weeks'] ?? 4),
            'sessions_per_week' => (int)($input['sessions_per_week'] ?? 3),
            'estimated_duration_minutes' => (int)($input['estimated_duration_minutes'] ?? 60),
            'is_template' => $input['is_template'] ?? false,
        ];

        if ($routineModel->update($id, $data)) {
            // Actualizar ejercicios si se proporcionaron
            if (isset($input['exercises'])) {
                $routineModel->removeAllExercisesFromRoutine($id);
                $this->processRoutineExercises($id, $input['exercises']);
            }

            $updatedRoutine = $routineModel->findById($id);
            $this->jsonResponse([
                'success' => true,
                'message' => 'Rutina actualizada exitosamente',
                'data' => $updatedRoutine,
            ]);
        } else {
            $this->jsonResponse(['error' => 'Error al actualizar la rutina'], 500);
        }
    }

    public function deleteRoutine($id)
    {
        $this->requireAuth(['instructor', 'admin']);

        $user = AppHelper::getCurrentUser();
        $routineModel = new Routine();
        $routine = $routineModel->findById($id);

        if (!$routine) {
            $this->jsonResponse(['error' => 'Rutina no encontrada'], 404);
            return;
        }

        if (!$this->canEditRoutine($routine, $user)) {
            $this->jsonResponse(['error' => 'Sin permisos'], 403);
            return;
        }

        if ($routineModel->delete($id)) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Rutina eliminada exitosamente',
            ]);
        } else {
            $this->jsonResponse(['error' => 'Error al eliminar la rutina'], 500);
        }
    }

    // ==========================================
    // ENDPOINTS DE EJERCICIOS
    // ==========================================

    public function exercises()
    {
        $this->requireAuth();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 50) : 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'category_id' => (int)($_GET['category_id'] ?? 0),
            'difficulty' => AppHelper::sanitize($_GET['difficulty'] ?? ''),
            'muscle_group' => AppHelper::sanitize($_GET['muscle_group'] ?? ''),
            'is_active' => true,
            'limit' => $limit,
            'offset' => $offset,
        ];

        $exerciseModel = new Exercise();
        $exercises = $exerciseModel->getExercises($filters);
        $total = $exerciseModel->countExercises($filters);

        $this->jsonResponse([
            'success' => true,
            'data' => $exercises,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
            ],
        ]);
    }

    public function exerciseCategories()
    {
        $this->requireAuth();

        $exerciseModel = new Exercise();
        $categories = $exerciseModel->getCategories();

        $this->jsonResponse([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function searchExercises()
    {
        $this->requireAuth();

        $query = AppHelper::sanitize($_GET['q'] ?? '');
        if (empty($query)) {
            $this->jsonResponse(['error' => 'Query is required'], 400);
            return;
        }

        $exerciseModel = new Exercise();
        $exercises = $exerciseModel->searchExercises($query, 10);

        $this->jsonResponse([
            'success' => true,
            'data' => $exercises,
        ]);
    }

    // ==========================================
    // ENDPOINTS DE PRODUCTOS
    // ==========================================

    public function products()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 50) : 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'category_id' => (int)($_GET['category_id'] ?? 0),
            'is_featured' => $_GET['featured'] ?? '',
            'price_min' => (float)($_GET['price_min'] ?? 0),
            'price_max' => (float)($_GET['price_max'] ?? 0),
            'is_active' => true,
            'limit' => $limit,
            'offset' => $offset,
        ];

        $productModel = new Product();
        $products = $productModel->getProducts($filters);
        $total = $productModel->countProducts($filters);

        $this->jsonResponse([
            'success' => true,
            'data' => $products,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
            ],
        ]);
    }

    public function featuredProducts()
    {
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 20) : 8;

        $productModel = new Product();
        $products = $productModel->getFeaturedProducts($limit);

        $this->jsonResponse([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function productRecommendations()
    {
        $this->requireAuth();

        $user = AppHelper::getCurrentUser();
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 10) : 5;

        // Obtener recomendaciones basadas en rutinas del usuario
        $productModel = new Product();
        $recommendations = $productModel->getRecommendationsForUser($user['id'], $limit);

        $this->jsonResponse([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    // ==========================================
    // ENDPOINTS DE USUARIOS
    // ==========================================

    public function users()
    {
        $this->requireAuth(['admin']);

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 50) : 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'role' => AppHelper::sanitize($_GET['role'] ?? ''),
            'is_active' => $_GET['active'] ?? '',
            'limit' => $limit,
            'offset' => $offset,
        ];

        $userModel = new User();
        $users = $userModel->getUsers($filters);
        $total = $userModel->countUsers($filters);

        // Ocultar contraseñas
        foreach ($users as &$user) {
            unset($user['password']);
        }

        $this->jsonResponse([
            'success' => true,
            'data' => $users,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
            ],
        ]);
    }

    public function clients()
    {
        $this->requireAuth(['instructor', 'admin']);

        $user = AppHelper::getCurrentUser();
        $userModel = new User();

        if ($user['role'] === 'instructor') {
            $clients = $userModel->getInstructorClients($user['id']);
        } else {
            $clients = $userModel->getUsers(['role' => 'client', 'is_active' => true]);
        }

        $this->jsonResponse([
            'success' => true,
            'data' => $clients,
        ]);
    }

    public function instructors()
    {
        $this->requireAuth();

        $userModel = new User();
        $instructors = $userModel->getUsers(['role' => 'instructor', 'is_active' => true]);

        $this->jsonResponse([
            'success' => true,
            'data' => $instructors,
        ]);
    }

    // ==========================================
    // ENDPOINTS DE ESTADÍSTICAS
    // ==========================================

    public function dashboardStats()
    {
        $this->requireAuth();

        $user = AppHelper::getCurrentUser();
        $stats = [];

        switch ($user['role']) {
            case 'admin':
                $stats = $this->getAdminStats();
                break;
            case 'instructor':
                $stats = $this->getInstructorStats($user['id']);
                break;
            case 'client':
                $stats = $this->getClientStats($user['id']);
                break;
        }

        $this->jsonResponse([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function routineStats()
    {
        $this->requireAuth(['instructor', 'admin']);

        $user = AppHelper::getCurrentUser();
        $routineModel = new Routine();

        if ($user['role'] === 'instructor') {
            $stats = $routineModel->getInstructorStats($user['id']);
        } else {
            $stats = $routineModel->getGlobalStats();
        }

        $this->jsonResponse([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function salesStats()
    {
        $this->requireAuth(['admin']);

        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');

        $orderModel = new Order();
        $stats = $orderModel->getSalesStats($dateFrom, $dateTo);

        $this->jsonResponse([
            'success' => true,
            'data' => $stats,
        ]);
    }

    // ==========================================
    // ENDPOINTS DE UPLOAD
    // ==========================================

    public function uploadImage()
    {
        $this->requireAuth(['instructor', 'admin']);

        if (!isset($_FILES['image'])) {
            $this->jsonResponse(['error' => 'No image provided'], 400);
            return;
        }

        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($file['type'], $allowedTypes)) {
            $this->jsonResponse(['error' => 'Invalid file type'], 400);
            return;
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            $this->jsonResponse(['error' => 'File too large'], 400);
            return;
        }

        $uploadDir = UPLOAD_PATH . '/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'filename' => $fileName,
                    'url' => AppHelper::uploadUrl('/images/' . $fileName),
                    'size' => $file['size'],
                ],
            ]);
        } else {
            $this->jsonResponse(['error' => 'Upload failed'], 500);
        }
    }

    public function uploadVideo()
    {
        $this->requireAuth(['instructor', 'admin']);

        if (!isset($_FILES['video'])) {
            $this->jsonResponse(['error' => 'No video provided'], 400);
            return;
        }

        $file = $_FILES['video'];
        $allowedTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo'];

        if (!in_array($file['type'], $allowedTypes)) {
            $this->jsonResponse(['error' => 'Invalid file type'], 400);
            return;
        }

        if ($file['size'] > 50 * 1024 * 1024) { // 50MB
            $this->jsonResponse(['error' => 'File too large'], 400);
            return;
        }

        $uploadDir = UPLOAD_PATH . '/videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'filename' => $fileName,
                    'url' => AppHelper::uploadUrl('/videos/' . $fileName),
                    'size' => $file['size'],
                ],
            ]);
        } else {
            $this->jsonResponse(['error' => 'Upload failed'], 500);
        }
    }

    public function uploadDocument()
    {
        $this->requireAuth(['admin']);

        if (!isset($_FILES['document'])) {
            $this->jsonResponse(['error' => 'No document provided'], 400);
            return;
        }

        $file = $_FILES['document'];
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        if (!in_array($file['type'], $allowedTypes)) {
            $this->jsonResponse(['error' => 'Invalid file type'], 400);
            return;
        }

        if ($file['size'] > 10 * 1024 * 1024) { // 10MB
            $this->jsonResponse(['error' => 'File too large'], 400);
            return;
        }

        $uploadDir = UPLOAD_PATH . '/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'filename' => $fileName,
                    'url' => AppHelper::uploadUrl('/documents/' . $fileName),
                    'size' => $file['size'],
                ],
            ]);
        } else {
            $this->jsonResponse(['error' => 'Upload failed'], 500);
        }
    }

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================

    private function requireAuth($roles = null)
    {
        if (!AppHelper::isLoggedIn()) {
            $this->jsonResponse(['error' => 'Authentication required'], 401);
            exit();
        }

        if ($roles !== null) {
            $user = AppHelper::getCurrentUser();
            $userRole = $user['role'];

            if (!in_array($userRole, (array)$roles)) {
                $this->jsonResponse(['error' => 'Insufficient permissions'], 403);
                exit();
            }
        }
    }

    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    private function getJsonInput()
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?: [];
    }

    private function canViewRoutine($routine, $user)
    {
        if ($user['role'] === 'admin') {
            return true;
        }

        if ($user['role'] === 'instructor') {
            return $routine['instructor_id'] == $user['id'];
        }

        if ($user['role'] === 'client') {
            return $routine['client_id'] == $user['id'] || $routine['is_template'];
        }

        return false;
    }

    private function canEditRoutine($routine, $user)
    {
        if ($user['role'] === 'client') {
            return false;
        }

        if ($user['role'] === 'admin') {
            return true;
        }

        return $routine['instructor_id'] == $user['id'];
    }

    private function processRoutineExercises($routineId, $exercises)
    {
        foreach ($exercises as $exercise) {
            $exerciseData = [
                'routine_id' => $routineId,
                'exercise_id' => (int)$exercise['exercise_id'],
                'day_number' => (int)$exercise['day_number'],
                'order_index' => (int)$exercise['order_index'],
                'sets' => (int)($exercise['sets'] ?? 3),
                'reps' => AppHelper::sanitize($exercise['reps'] ?? '10'),
                'weight' => AppHelper::sanitize($exercise['weight'] ?? ''),
                'rest_seconds' => (int)($exercise['rest_seconds'] ?? 60),
                'tempo' => AppHelper::sanitize($exercise['tempo'] ?? ''),
                'notes' => AppHelper::sanitize($exercise['notes'] ?? ''),
            ];

            $routineModel = new Routine();
            $routineModel->addExerciseToRoutine($exerciseData);
        }
    }

    private function getAdminStats()
    {
        return [
            'total_users' => $this->db->count('SELECT COUNT(*) FROM users WHERE is_active = 1'),
            'total_clients' => $this->db->count("SELECT COUNT(*) FROM users WHERE role = 'client' AND is_active = 1"),
            'total_instructors' => $this->db->count("SELECT COUNT(*) FROM users WHERE role = 'instructor' AND is_active = 1"),
            'total_products' => $this->db->count('SELECT COUNT(*) FROM products WHERE is_active = 1'),
            'total_routines' => $this->db->count('SELECT COUNT(*) FROM routines WHERE is_active = 1'),
            'monthly_revenue' => $this->db->fetch(
                "SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders 
                 WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
                 AND YEAR(created_at) = YEAR(CURRENT_DATE()) 
                 AND payment_status = 'paid'"
            )['revenue'] ?? 0,
            'pending_orders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'pending'"),
        ];
    }

    private function getInstructorStats($instructorId)
    {
        return [
            'assigned_clients' => $this->db->count(
                'SELECT COUNT(DISTINCT client_id) FROM routines WHERE instructor_id = ?',
                [$instructorId]
            ),
            'active_routines' => $this->db->count(
                'SELECT COUNT(*) FROM routines WHERE instructor_id = ? AND is_active = 1',
                [$instructorId]
            ),
            'scheduled_classes' => $this->db->count(
                'SELECT COUNT(*) FROM group_classes gc
                 JOIN class_schedules cs ON gc.id = cs.class_id
                 WHERE gc.instructor_id = ? AND cs.is_active = 1',
                [$instructorId]
            ),
        ];
    }

    private function getClientStats($clientId)
    {
        return [
            'active_routines' => $this->db->count(
                'SELECT COUNT(*) FROM routines WHERE client_id = ? AND is_active = 1',
                [$clientId]
            ),
            'completed_workouts' => $this->db->count(
                'SELECT COUNT(*) FROM workout_logs WHERE user_id = ?',
                [$clientId]
            ),
            'total_orders' => $this->db->count(
                'SELECT COUNT(*) FROM orders WHERE user_id = ?',
                [$clientId]
            ),
        ];
    }
}
