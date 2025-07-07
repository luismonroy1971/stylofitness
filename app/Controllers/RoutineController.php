<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\Routine;
use StyleFitness\Models\Exercise;
use StyleFitness\Models\User;
use StyleFitness\Models\Product;
use StyleFitness\Helpers\AppHelper;
use StyleFitness\Helpers\RoutineHelper;
use Exception;

/**
 * Controlador de Rutinas - STYLOFITNESS
 * Maneja creación, edición y visualización de rutinas
 */

class RoutineController
{
    private $db;
    private $routineModel;
    private $exerciseModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->routineModel = new Routine();
        $this->exerciseModel = new Exercise();
    }

    public function index()
    {
        // Verificar si el usuario está logueado
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = ROUTINES_PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'objective' => AppHelper::sanitize($_GET['objective'] ?? ''),
            'difficulty' => AppHelper::sanitize($_GET['difficulty'] ?? ''),
            'user_id' => $user['id'],
            'limit' => $perPage,
            'offset' => $offset,
        ];

        // Obtener rutinas del usuario
        if ($user['role'] === 'client') {
            $routines = $this->routineModel->getClientRoutines($user['id'], $filters);
            $totalRoutines = $this->routineModel->countClientRoutines($user['id'], $filters);
        } elseif ($user['role'] === 'instructor') {
            $routines = $this->routineModel->getInstructorRoutines($user['id'], $filters);
            $totalRoutines = $this->routineModel->countInstructorRoutines($user['id'], $filters);
        } else {
            $routines = $this->routineModel->getAllRoutines($filters);
            $totalRoutines = $this->routineModel->countAllRoutines($filters);
        }

        // Obtener rutinas públicas/plantillas
        $publicRoutines = $this->routineModel->getPublicRoutines(6);

        // Calcular paginación
        $totalPages = ceil($totalRoutines / $perPage);
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalRoutines,
            'per_page' => $perPage,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
        ];

        $pageTitle = 'Mis Rutinas - STYLOFITNESS';
        $additionalCSS = ['routine-styles.css'];
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/routines/index.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function create()
    {
        // Verificar permisos
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['instructor', 'admin'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para crear rutinas');
            AppHelper::redirect('/routines');
            return;
        }

        // Obtener datos necesarios para el formulario
        $exerciseCategories = $this->exerciseModel->getCategories();
        $exercises = $this->exerciseModel->getExercises(['is_active' => true]);
        $clients = [];

        if ($user['role'] === 'instructor') {
            $userModel = new User();
            $clients = $userModel->getInstructorClients($user['id']);
        } elseif ($user['role'] === 'admin') {
            $userModel = new User();
            $clients = $userModel->getUsers(['role' => 'client', 'is_active' => true]);
        }

        $pageTitle = 'Crear Rutina - STYLOFITNESS';
        $additionalCSS = ['routine-styles.css', 'routine-builder.css'];
        $additionalJS = ['routine-builder.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/routines/create.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/routines');
            return;
        }

        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['instructor', 'admin'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para crear rutinas');
            AppHelper::redirect('/routines');
            return;
        }

        // Validar token CSRF
        if (!AppHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            AppHelper::setFlashMessage('error', 'Token de seguridad inválido');
            AppHelper::redirect('/routines/create');
            return;
        }

        $data = [
            'gym_id' => $user['gym_id'],
            'instructor_id' => $user['id'],
            'client_id' => isset($_POST['client_id']) ? (int)$_POST['client_id'] : null,
            'name' => AppHelper::sanitize($_POST['name'] ?? ''),
            'description' => AppHelper::sanitize($_POST['description'] ?? ''),
            'objective' => AppHelper::sanitize($_POST['objective'] ?? ''),
            'difficulty_level' => AppHelper::sanitize($_POST['difficulty_level'] ?? ''),
            'duration_weeks' => (int)($_POST['duration_weeks'] ?? 4),
            'sessions_per_week' => (int)($_POST['sessions_per_week'] ?? 3),
            'estimated_duration_minutes' => (int)($_POST['estimated_duration_minutes'] ?? 60),
            'is_template' => isset($_POST['is_template']),
            'is_active' => true,
        ];

        // Validar datos
        $errors = $this->validateRoutineData($data);

        if (!empty($errors)) {
            $_SESSION['routine_errors'] = $errors;
            $_SESSION['routine_data'] = $data;
            AppHelper::redirect('/routines/create');
            return;
        }

        // Crear rutina
        $routineId = $this->routineModel->create($data);

        if ($routineId) {
            // Procesar ejercicios de la rutina
            $this->processRoutineExercises($routineId, $_POST['exercises'] ?? []);

            AppHelper::setFlashMessage('success', 'Rutina creada exitosamente');
            AppHelper::redirect('/routines/view/' . $routineId);
        } else {
            AppHelper::setFlashMessage('error', 'Error al crear la rutina');
            AppHelper::redirect('/routines/create');
        }
    }

    public function show($id)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $routine = $this->routineModel->findById($id);

        if (!$routine) {
            AppHelper::setFlashMessage('error', 'Rutina no encontrada');
            AppHelper::redirect('/routines');
            return;
        }

        $user = AppHelper::getCurrentUser();

        // Verificar permisos
        if (!$this->canViewRoutine($routine, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para ver esta rutina');
            AppHelper::redirect('/routines');
            return;
        }

        // Obtener ejercicios de la rutina organizados por día
        $routineExercises = $this->routineModel->getRoutineExercises($id);
        $exercisesByDay = [];

        foreach ($routineExercises as $exercise) {
            $day = $exercise['day_number'];
            if (!isset($exercisesByDay[$day])) {
                $exercisesByDay[$day] = [];
            }
            $exercisesByDay[$day][] = $exercise;
        }

        // Obtener progreso del cliente si aplica
        $progress = null;
        if ($routine['client_id'] && $user['role'] !== 'client') {
            $progress = $this->routineModel->getClientProgress($routine['client_id'], $id);
        } elseif ($user['role'] === 'client' && $routine['client_id'] == $user['id']) {
            $progress = $this->routineModel->getClientProgress($user['id'], $id);
        }

        // Obtener productos recomendados
        $recommendedProducts = $this->getRecommendedProducts($routine);

        $pageTitle = $routine['name'] . ' - STYLOFITNESS';
        $additionalCSS = ['routine-styles.css', 'routine-view.css'];
        $additionalJS = ['routine-view.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/routines/show.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function edit($id)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $routine = $this->routineModel->findById($id);

        if (!$routine) {
            AppHelper::setFlashMessage('error', 'Rutina no encontrada');
            AppHelper::redirect('/routines');
            return;
        }

        $user = AppHelper::getCurrentUser();

        // Verificar permisos
        if (!$this->canEditRoutine($routine, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para editar esta rutina');
            AppHelper::redirect('/routines');
            return;
        }

        // Obtener datos para el formulario
        $exerciseCategories = $this->exerciseModel->getCategories();
        $exercises = $this->exerciseModel->getExercises(['is_active' => true]);
        $routineExercises = $this->routineModel->getRoutineExercises($id);

        $clients = [];
        if ($user['role'] === 'admin') {
            $userModel = new User();
            $clients = $userModel->getUsers(['role' => 'client', 'is_active' => true]);
        }

        $pageTitle = 'Editar: ' . $routine['name'] . ' - STYLOFITNESS';
        $additionalCSS = ['routine-styles.css', 'routine-builder.css'];
        $additionalJS = ['routine-builder.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/routines/edit.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/routines');
            return;
        }

        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $routine = $this->routineModel->findById($id);
        if (!$routine) {
            AppHelper::setFlashMessage('error', 'Rutina no encontrada');
            AppHelper::redirect('/routines');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!$this->canEditRoutine($routine, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para editar esta rutina');
            AppHelper::redirect('/routines');
            return;
        }

        $data = [
            'name' => AppHelper::sanitize($_POST['name'] ?? ''),
            'description' => AppHelper::sanitize($_POST['description'] ?? ''),
            'objective' => AppHelper::sanitize($_POST['objective'] ?? ''),
            'difficulty_level' => AppHelper::sanitize($_POST['difficulty_level'] ?? ''),
            'duration_weeks' => (int)($_POST['duration_weeks'] ?? 4),
            'sessions_per_week' => (int)($_POST['sessions_per_week'] ?? 3),
            'estimated_duration_minutes' => (int)($_POST['estimated_duration_minutes'] ?? 60),
            'is_template' => isset($_POST['is_template']),
        ];

        if ($user['role'] === 'admin' && isset($_POST['client_id'])) {
            $data['client_id'] = (int)$_POST['client_id'];
        }

        // Actualizar rutina
        if ($this->routineModel->update($id, $data)) {
            // Actualizar ejercicios
            $this->updateRoutineExercises($id, $_POST['exercises'] ?? []);

            AppHelper::setFlashMessage('success', 'Rutina actualizada exitosamente');
            AppHelper::redirect('/routines/view/' . $id);
        } else {
            AppHelper::setFlashMessage('error', 'Error al actualizar la rutina');
            AppHelper::redirect('/routines/edit/' . $id);
        }
    }

    public function delete($id)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $routine = $this->routineModel->findById($id);
        if (!$routine) {
            AppHelper::setFlashMessage('error', 'Rutina no encontrada');
            AppHelper::redirect('/routines');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!$this->canEditRoutine($routine, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para eliminar esta rutina');
            AppHelper::redirect('/routines');
            return;
        }

        if ($this->routineModel->delete($id)) {
            AppHelper::setFlashMessage('success', 'Rutina eliminada exitosamente');
        } else {
            AppHelper::setFlashMessage('error', 'Error al eliminar la rutina');
        }

        AppHelper::redirect('/routines');
    }

    public function logWorkout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }

        if (!AppHelper::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }

        $user = AppHelper::getCurrentUser();
        $routineId = isset($_POST['routine_id']) ? (int)$_POST['routine_id'] : 0;
        $exerciseId = isset($_POST['exercise_id']) ? (int)$_POST['exercise_id'] : 0;
        $sets = isset($_POST['sets']) ? (int)$_POST['sets'] : 0;
        $reps = AppHelper::sanitize($_POST['reps'] ?? '');
        $weight = AppHelper::sanitize($_POST['weight'] ?? '');
        $notes = AppHelper::sanitize($_POST['notes'] ?? '');

        if (!$routineId || !$exerciseId) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            exit();
        }

        // Verificar que la rutina pertenece al usuario
        $routine = $this->routineModel->findById($routineId);
        if (!$routine || $routine['client_id'] != $user['id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permisos']);
            exit();
        }

        // Registrar el workout
        $logData = [
            'user_id' => $user['id'],
            'routine_id' => $routineId,
            'exercise_id' => $exerciseId,
            'sets_completed' => $sets,
            'reps' => $reps,
            'weight_used' => $weight,
            'notes' => $notes,
            'completed_at' => date('Y-m-d H:i:s'),
        ];

        $logId = $this->routineModel->logWorkout($logData);

        if ($logId) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Ejercicio registrado exitosamente',
                'log_id' => $logId,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al registrar el ejercicio']);
        }
    }

    private function validateRoutineData($data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        }

        if (empty($data['objective'])) {
            $errors['objective'] = 'El objetivo es obligatorio';
        }

        if (empty($data['difficulty_level'])) {
            $errors['difficulty_level'] = 'El nivel de dificultad es obligatorio';
        }

        if ($data['duration_weeks'] < 1 || $data['duration_weeks'] > 52) {
            $errors['duration_weeks'] = 'La duración debe ser entre 1 y 52 semanas';
        }

        if ($data['sessions_per_week'] < 1 || $data['sessions_per_week'] > 7) {
            $errors['sessions_per_week'] = 'Las sesiones por semana deben ser entre 1 y 7';
        }

        return $errors;
    }

    private function processRoutineExercises($routineId, $exercises)
    {
        foreach ($exercises as $dayNumber => $dayExercises) {
            foreach ($dayExercises as $order => $exercise) {
                $exerciseData = [
                    'routine_id' => $routineId,
                    'exercise_id' => (int)$exercise['exercise_id'],
                    'day_number' => (int)$dayNumber,
                    'order_index' => (int)$order,
                    'sets' => (int)($exercise['sets'] ?? 3),
                    'reps' => AppHelper::sanitize($exercise['reps'] ?? '10'),
                    'weight' => AppHelper::sanitize($exercise['weight'] ?? ''),
                    'rest_seconds' => (int)($exercise['rest_seconds'] ?? 60),
                    'tempo' => AppHelper::sanitize($exercise['tempo'] ?? ''),
                    'notes' => AppHelper::sanitize($exercise['notes'] ?? ''),
                ];

                $this->routineModel->addExerciseToRoutine($exerciseData);
            }
        }
    }

    private function updateRoutineExercises($routineId, $exercises)
    {
        // Eliminar ejercicios existentes
        $this->routineModel->removeAllExercisesFromRoutine($routineId);

        // Agregar ejercicios actualizados
        $this->processRoutineExercises($routineId, $exercises);
    }

    private function canViewRoutine($routine, $user)
    {
        // Administradores pueden ver todas
        if ($user['role'] === 'admin') {
            return true;
        }

        // Instructores pueden ver sus rutinas y las de sus clientes
        if ($user['role'] === 'instructor') {
            return $routine['instructor_id'] == $user['id'];
        }

        // Clientes pueden ver sus rutinas asignadas y plantillas públicas
        if ($user['role'] === 'client') {
            return $routine['client_id'] == $user['id'] || $routine['is_template'];
        }

        return false;
    }

    private function canEditRoutine($routine, $user)
    {
        // Solo administradores e instructores pueden editar
        if ($user['role'] === 'client') {
            return false;
        }

        // Administradores pueden editar todas
        if ($user['role'] === 'admin') {
            return true;
        }

        // Instructores solo pueden editar sus rutinas
        return $routine['instructor_id'] == $user['id'];
    }

    public function duplicate($id)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $routine = $this->routineModel->findById($id);
        if (!$routine) {
            AppHelper::setFlashMessage('error', 'Rutina no encontrada');
            AppHelper::redirect('/routines');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!$this->canViewRoutine($routine, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para duplicar esta rutina');
            AppHelper::redirect('/routines');
            return;
        }

        // Datos para la nueva rutina
        $newData = [
            'instructor_id' => $user['id'],
            'client_id' => null,
            'name' => $routine['name'] . ' (Copia)',
            'is_template' => false,
        ];

        $newRoutineId = $this->routineModel->duplicate($id, $newData);

        if ($newRoutineId) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'routine_id' => $newRoutineId,
                    'message' => 'Rutina duplicada exitosamente',
                ]);
                exit();
            } else {
                AppHelper::setFlashMessage('success', 'Rutina duplicada exitosamente');
                AppHelper::redirect('/routines/edit/' . $newRoutineId);
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Error al duplicar la rutina',
                ]);
                exit();
            } else {
                AppHelper::setFlashMessage('error', 'Error al duplicar la rutina');
                AppHelper::redirect('/routines/view/' . $id);
            }
        }
    }

    public function getByObjective($objective)
    {
        $routines = $this->routineModel->getRoutinesByObjective($objective, 20);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'routines' => $routines,
        ]);
    }

    public function templates()
    {
        // Página para ver todas las plantillas públicas
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'objective' => AppHelper::sanitize($_GET['objective'] ?? ''),
            'difficulty' => AppHelper::sanitize($_GET['difficulty'] ?? ''),
            'is_template' => true,
            'limit' => $perPage,
            'offset' => $offset,
        ];

        $routines = $this->routineModel->getAllRoutines($filters);
        $totalRoutines = $this->routineModel->countAllRoutines($filters);

        $totalPages = ceil($totalRoutines / $perPage);
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalRoutines,
            'per_page' => $perPage,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
        ];

        $pageTitle = 'Plantillas de Rutinas - STYLOFITNESS';
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/routines/templates.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function assign()
    {
        // Asignar rutina a cliente
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/routines');
            return;
        }

        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['instructor', 'admin'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para asignar rutinas');
            AppHelper::redirect('/routines');
            return;
        }

        $routineId = (int)$_POST['routine_id'];
        $clientId = (int)$_POST['client_id'];

        $routine = $this->routineModel->findById($routineId);
        if (!$routine) {
            AppHelper::setFlashMessage('error', 'Rutina no encontrada');
            AppHelper::redirect('/routines');
            return;
        }

        // Verificar permisos
        if (!$this->canEditRoutine($routine, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para modificar esta rutina');
            AppHelper::redirect('/routines');
            return;
        }

        // Duplicar rutina para el cliente
        $newData = [
            'instructor_id' => $user['id'],
            'client_id' => $clientId,
            'name' => $routine['name'],
            'is_template' => false,
        ];

        $newRoutineId = $this->routineModel->duplicate($routineId, $newData);

        if ($newRoutineId) {
            AppHelper::setFlashMessage('success', 'Rutina asignada exitosamente al cliente');
            AppHelper::redirect('/routines/view/' . $newRoutineId);
        } else {
            AppHelper::setFlashMessage('error', 'Error al asignar la rutina');
            AppHelper::redirect('/routines/view/' . $routineId);
        }
    }

    public function getProgress($id)
    {
        if (!AppHelper::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }

        $user = AppHelper::getCurrentUser();
        $routine = $this->routineModel->findById($id);

        if (!$routine) {
            http_response_code(404);
            echo json_encode(['error' => 'Rutina no encontrada']);
            exit();
        }

        if (!$this->canViewRoutine($routine, $user)) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permisos']);
            exit();
        }

        // Obtener progreso
        $clientId = $routine['client_id'] ?? $user['id'];
        $progress = $this->routineModel->getClientProgress($clientId, $id);

        // Obtener logs recientes
        $recentLogs = $this->db->fetchAll(
            'SELECT wl.*, e.name as exercise_name 
             FROM workout_logs wl 
             JOIN exercises e ON wl.exercise_id = e.id 
             WHERE wl.user_id = ? AND wl.routine_id = ? 
             ORDER BY wl.completed_at DESC 
             LIMIT 10',
            [$clientId, $id]
        );

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'progress' => $progress,
            'recent_logs' => $recentLogs,
        ]);
    }

    public function exportPdf($id)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $routine = $this->routineModel->findById($id);
        if (!$routine) {
            AppHelper::setFlashMessage('error', 'Rutina no encontrada');
            AppHelper::redirect('/routines');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!$this->canViewRoutine($routine, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para exportar esta rutina');
            AppHelper::redirect('/routines');
            return;
        }

        // Obtener ejercicios
        $routineExercises = $this->routineModel->getRoutineExercises($id);
        $exercisesByDay = [];

        foreach ($routineExercises as $exercise) {
            $day = $exercise['day_number'];
            if (!isset($exercisesByDay[$day])) {
                $exercisesByDay[$day] = [];
            }
            $exercisesByDay[$day][] = $exercise;
        }

        // Generar PDF (aquí usarías una librería como TCPDF o DomPDF)
        $this->generateRoutinePdf($routine, $exercisesByDay);
    }

    private function generateRoutinePdf($routine, $exercisesByDay)
    {
        // Configurar headers para PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="rutina_' . $routine['id'] . '.pdf"');

        // Aquí implementarías la generación del PDF
        // Por ahora, generar un PDF simple con HTML
        $html = $this->generateRoutineHtml($routine, $exercisesByDay);

        // Si tienes DomPDF instalado:
        // $dompdf = new Dompdf();
        // $dompdf->loadHtml($html);
        // $dompdf->render();
        // echo $dompdf->output();

        // Temporal: mostrar HTML
        echo $html;
    }

    private function generateRoutineHtml($routine, $exercisesByDay)
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Rutina: <?= htmlspecialchars($routine['name']) ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .routine-info { margin-bottom: 20px; }
                .day-section { margin-bottom: 30px; page-break-inside: avoid; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1><?= htmlspecialchars($routine['name']) ?></h1>
                <h2>STYLOFITNESS</h2>
            </div>
            
            <div class="routine-info">
                <p><strong>Descripción:</strong> <?= htmlspecialchars($routine['description']) ?></p>
                <p><strong>Objetivo:</strong> <?= ucfirst(str_replace('_', ' ', $routine['objective'])) ?></p>
                <p><strong>Nivel:</strong> <?= ucfirst($routine['difficulty_level']) ?></p>
                <p><strong>Duración:</strong> <?= $routine['duration_weeks'] ?> semanas</p>
                <p><strong>Días por semana:</strong> <?= $routine['sessions_per_week'] ?></p>
            </div>
            
            <?php foreach ($exercisesByDay as $day => $exercises): ?>
            <div class="day-section">
                <h3>Día <?= $day ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Ejercicio</th>
                            <th>Series</th>
                            <th>Repeticiones</th>
                            <th>Peso</th>
                            <th>Descanso</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exercises as $exercise): ?>
                        <tr>
                            <td><?= htmlspecialchars($exercise['exercise_name']) ?></td>
                            <td><?= $exercise['sets'] ?></td>
                            <td><?= htmlspecialchars($exercise['reps']) ?></td>
                            <td><?= htmlspecialchars($exercise['weight']) ?></td>
                            <td><?= $exercise['rest_seconds'] ?>s</td>
                            <td><?= htmlspecialchars($exercise['notes']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
            
            <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #666;">
                <p>Generado el <?= date('d/m/Y H:i') ?> | STYLOFITNESS</p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    private function getRecommendedProducts($routine)
    {
        $productModel = new Product();

        // Mapear objetivos a categorías de productos
        $objectiveMapping = [
            'weight_loss' => ['quemadores', 'l-carnitina'],
            'muscle_gain' => ['proteinas', 'creatina', 'ganadores'],
            'strength' => ['creatina', 'pre-entrenos'],
            'endurance' => ['bcaa', 'bebidas-deportivas'],
        ];

        $categories = $objectiveMapping[$routine['objective']] ?? ['proteinas'];

        $products = [];
        foreach ($categories as $category) {
            $categoryProducts = $productModel->getProducts([
                'category_slug' => $category,
                'is_active' => true,
                'limit' => 2,
            ]);
            $products = array_merge($products, $categoryProducts);
        }

        return array_slice($products, 0, 4);
    }
}
?>