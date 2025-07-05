<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\Routine;
use StyleFitness\Models\Exercise;
use StyleFitness\Models\User;
use StyleFitness\Helpers\AppHelper;
use Exception;
use PDO;

/**
 * Controlador de Plantillas de Rutinas - STYLOFITNESS
 * Maneja la creación y gestión de plantillas de rutinas diferenciadas por género
 */
class RoutineTemplateController
{
    private $db;
    private $routineModel;
    private $exerciseModel;
    private $userModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->routineModel = new Routine();
        $this->exerciseModel = new Exercise();
        $this->userModel = new User();
    }

    /**
     * Página principal de gestión de plantillas
     */
    public function index()
    {
        if (!$this->checkInstructorPermissions()) {
            return;
        }

        $user = AppHelper::getCurrentUser();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'objective' => AppHelper::sanitize($_GET['objective'] ?? ''),
            'difficulty' => AppHelper::sanitize($_GET['difficulty'] ?? ''),
            'gender' => AppHelper::sanitize($_GET['gender'] ?? ''),
            'instructor_id' => $user['role'] === 'instructor' ? $user['id'] : null,
            'is_template' => true,
            'limit' => $perPage,
            'offset' => $offset
        ];

        $templates = $this->routineModel->getTemplates($filters);
        $totalTemplates = $this->routineModel->countTemplates($filters);

        // Calcular paginación
        $totalPages = ceil($totalTemplates / $perPage);
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalTemplates,
            'per_page' => $perPage,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
        ];

        $pageTitle = 'Plantillas de Rutinas - STYLOFITNESS';
        $additionalCSS = ['routine-templates.css'];
        $additionalJS = ['routine-templates.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/trainer/templates/index.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Formulario para crear nueva plantilla
     */
    public function create()
    {
        if (!$this->checkInstructorPermissions()) {
            return;
        }

        // Obtener datos necesarios para el formulario
        $exerciseCategories = $this->exerciseModel->getCategories();
        $exercises = $this->exerciseModel->getExercises(['is_active' => true]);
        $muscleGroups = $this->exerciseModel->getMuscleGroups();
        
        // Organizar ejercicios por zona corporal
        $exercisesByZone = $this->organizeExercisesByBodyZone($exercises);
        
        // Obtener datos de sesión si hay errores
        $errors = $_SESSION['template_errors'] ?? [];
        $oldData = $_SESSION['template_data'] ?? [];
        unset($_SESSION['template_errors'], $_SESSION['template_data']);

        $pageTitle = 'Crear Plantilla de Rutina - STYLOFITNESS';
        $additionalCSS = ['template-builder.css', 'body-zone-selector.css'];
        $additionalJS = ['template-builder.js', 'body-zone-selector.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/trainer/templates/create.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Procesar creación de plantilla
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/trainer/templates');
            return;
        }

        if (!$this->checkInstructorPermissions()) {
            return;
        }

        // Validar token CSRF
        if (!AppHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            AppHelper::setFlashMessage('error', 'Token de seguridad inválido');
            AppHelper::redirect('/trainer/templates/create');
            return;
        }

        $user = AppHelper::getCurrentUser();
        
        $data = [
            'gym_id' => $user['gym_id'],
            'instructor_id' => $user['id'],
            'name' => AppHelper::sanitize($_POST['name'] ?? ''),
            'description' => AppHelper::sanitize($_POST['description'] ?? ''),
            'objective' => AppHelper::sanitize($_POST['objective'] ?? ''),
            'difficulty_level' => AppHelper::sanitize($_POST['difficulty_level'] ?? ''),
            'target_gender' => AppHelper::sanitize($_POST['target_gender'] ?? ''),
            'duration_weeks' => (int)($_POST['duration_weeks'] ?? 4),
            'sessions_per_week' => (int)($_POST['sessions_per_week'] ?? 3),
            'estimated_duration_minutes' => (int)($_POST['estimated_duration_minutes'] ?? 60),
            'is_template' => true,
            'is_public' => isset($_POST['is_public']),
            'is_active' => true,
            'body_zones' => isset($_POST['body_zones']) ? $_POST['body_zones'] : [],
            'tags' => isset($_POST['tags']) ? explode(',', $_POST['tags']) : []
        ];

        // Validar datos
        $errors = $this->validateTemplateData($data);

        if (!empty($errors)) {
            $_SESSION['template_errors'] = $errors;
            $_SESSION['template_data'] = $data;
            AppHelper::redirect('/trainer/templates/create');
            return;
        }

        // Crear plantilla
        $templateId = $this->routineModel->createTemplate($data);

        if ($templateId) {
            // Procesar ejercicios organizados por zonas corporales
            $this->processTemplateBodyZones($templateId, $_POST['body_zones'] ?? []);

            AppHelper::setFlashMessage('success', 'Plantilla creada exitosamente');
            AppHelper::redirect('/trainer/templates/view/' . $templateId);
        } else {
            AppHelper::setFlashMessage('error', 'Error al crear la plantilla');
            AppHelper::redirect('/trainer/templates/create');
        }
    }

    /**
     * Ver detalles de una plantilla
     */
    public function show($id)
    {
        if (!$this->checkInstructorPermissions()) {
            return;
        }

        $template = $this->routineModel->findById($id);
        
        if (!$template || !$template['is_template']) {
            AppHelper::setFlashMessage('error', 'Plantilla no encontrada');
            AppHelper::redirect('/trainer/templates');
            return;
        }

        $user = AppHelper::getCurrentUser();
        
        // Verificar permisos
        if (!$this->canViewTemplate($template, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para ver esta plantilla');
            AppHelper::redirect('/trainer/templates');
            return;
        }

        // Obtener ejercicios organizados por zonas corporales
        $templateExercises = $this->routineModel->getRoutineExercises($id);
        
        // Obtener estadísticas de uso
        $usageStats = $this->getTemplateUsageStats($id);
        
        // Obtener clientes del instructor para asignación rápida
        $clients = [];
        if ($user['role'] === 'instructor') {
            $clients = $this->userModel->getInstructorClients($user['id']);
        }

        $pageTitle = $template['name'] . ' - Plantilla de Rutina';
        $additionalCSS = ['template-view.css'];
        $additionalJS = ['template-view.js', 'template-assignment.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/trainer/templates/show.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Formulario para editar plantilla
     */
    public function edit($id)
    {
        if (!$this->checkInstructorPermissions()) {
            return;
        }

        $template = $this->routineModel->findById($id);
        
        if (!$template || !$template['is_template']) {
            AppHelper::setFlashMessage('error', 'Plantilla no encontrada');
            AppHelper::redirect('/trainer/templates');
            return;
        }

        $user = AppHelper::getCurrentUser();
        
        // Verificar permisos
        if (!$this->canEditTemplate($template, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para editar esta plantilla');
            AppHelper::redirect('/trainer/templates');
            return;
        }

        // Obtener datos para el formulario
        $exerciseCategories = $this->exerciseModel->getCategories();
        $exercises = $this->exerciseModel->getExercises(['is_active' => true]);
        $templateExercises = $this->routineModel->getRoutineExercises($id);
        $exercisesByZone = $this->organizeExercisesByBodyZone($exercises);
        
        // Obtener datos de sesión si hay errores
        $errors = $_SESSION['template_errors'] ?? [];
        $oldData = $_SESSION['template_data'] ?? $template;
        unset($_SESSION['template_errors'], $_SESSION['template_data']);

        $pageTitle = 'Editar: ' . $template['name'] . ' - STYLOFITNESS';
        $additionalCSS = ['template-builder.css', 'body-zone-selector.css'];
        $additionalJS = ['template-builder.js', 'body-zone-selector.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/trainer/templates/edit.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Procesar actualización de plantilla
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/trainer/templates');
            return;
        }

        if (!$this->checkInstructorPermissions()) {
            return;
        }

        $template = $this->routineModel->findById($id);
        if (!$template || !$template['is_template']) {
            AppHelper::setFlashMessage('error', 'Plantilla no encontrada');
            AppHelper::redirect('/trainer/templates');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!$this->canEditTemplate($template, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para editar esta plantilla');
            AppHelper::redirect('/trainer/templates');
            return;
        }

        $data = [
            'name' => AppHelper::sanitize($_POST['name'] ?? ''),
            'description' => AppHelper::sanitize($_POST['description'] ?? ''),
            'objective' => AppHelper::sanitize($_POST['objective'] ?? ''),
            'difficulty_level' => AppHelper::sanitize($_POST['difficulty_level'] ?? ''),
            'target_gender' => AppHelper::sanitize($_POST['target_gender'] ?? ''),
            'duration_weeks' => (int)($_POST['duration_weeks'] ?? 4),
            'sessions_per_week' => (int)($_POST['sessions_per_week'] ?? 3),
            'estimated_duration_minutes' => (int)($_POST['estimated_duration_minutes'] ?? 60),
            'is_public' => isset($_POST['is_public']),
            'tags' => isset($_POST['tags']) ? explode(',', $_POST['tags']) : []
        ];

        // Validar datos
        $errors = $this->validateTemplateData($data);

        if (!empty($errors)) {
            $_SESSION['template_errors'] = $errors;
            $_SESSION['template_data'] = array_merge($template, $data);
            AppHelper::redirect('/trainer/templates/edit/' . $id);
            return;
        }

        // Actualizar plantilla
        if ($this->routineModel->update($id, $data)) {
            // Actualizar ejercicios por zonas corporales
            $this->updateTemplateBodyZones($id, $_POST['body_zones'] ?? []);

            AppHelper::setFlashMessage('success', 'Plantilla actualizada exitosamente');
            AppHelper::redirect('/trainer/templates/view/' . $id);
        } else {
            AppHelper::setFlashMessage('error', 'Error al actualizar la plantilla');
            AppHelper::redirect('/trainer/templates/edit/' . $id);
        }
    }

    /**
     * Asignar plantilla a un cliente
     */
    public function assignToClient()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (!$this->checkInstructorPermissions()) {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $templateId = isset($_POST['template_id']) ? (int)$_POST['template_id'] : 0;
        $clientId = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
        $customizations = $_POST['customizations'] ?? [];

        if (!$templateId || !$clientId) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        $template = $this->routineModel->findById($templateId);
        if (!$template || !$template['is_template']) {
            http_response_code(404);
            echo json_encode(['error' => 'Plantilla no encontrada']);
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!$this->canUseTemplate($template, $user)) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para usar esta plantilla']);
            return;
        }

        // Crear rutina personalizada basada en la plantilla
        $routineId = $this->createRoutineFromTemplate($templateId, $clientId, $customizations);

        if ($routineId) {
            echo json_encode([
                'success' => true,
                'routine_id' => $routineId,
                'message' => 'Rutina asignada exitosamente'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear la rutina']);
        }
    }

    /**
     * Duplicar plantilla
     */
    public function duplicate($id)
    {
        if (!$this->checkInstructorPermissions()) {
            return;
        }

        $template = $this->routineModel->findById($id);
        if (!$template || !$template['is_template']) {
            AppHelper::setFlashMessage('error', 'Plantilla no encontrada');
            AppHelper::redirect('/trainer/templates');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!$this->canUseTemplate($template, $user)) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para duplicar esta plantilla');
            AppHelper::redirect('/trainer/templates');
            return;
        }

        // Duplicar plantilla
        $newTemplateId = $this->duplicateTemplate($id);

        if ($newTemplateId) {
            AppHelper::setFlashMessage('success', 'Plantilla duplicada exitosamente');
            AppHelper::redirect('/trainer/templates/edit/' . $newTemplateId);
        } else {
            AppHelper::setFlashMessage('error', 'Error al duplicar la plantilla');
            AppHelper::redirect('/trainer/templates');
        }
    }

    /**
     * API para obtener ejercicios por zona corporal (AJAX)
     */
    public function getExercisesByBodyZone()
    {
        if (!AppHelper::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $bodyZone = AppHelper::sanitize($_GET['body_zone'] ?? '');
        $difficulty = AppHelper::sanitize($_GET['difficulty'] ?? '');
        $gender = AppHelper::sanitize($_GET['gender'] ?? '');
        
        $exercises = $this->exerciseModel->getExercisesByBodyZone($bodyZone, $difficulty, $gender);
        
        header('Content-Type: application/json');
        echo json_encode($exercises);
    }

    /**
     * Verificar permisos básicos
     */
    private function checkPermissions()
    {
        if (!AppHelper::isLoggedIn()) {
            return false;
        }

        $user = AppHelper::getCurrentUser();
        return in_array($user['role'], ['instructor', 'admin']);
    }

    /**
     * Obtener plantillas
     */
    public function getTemplates()
    {
        if (!$this->checkPermissions()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        try {
            $user = AppHelper::getCurrentUser();
            $filters = [
                'instructor_id' => $user['role'] === 'instructor' ? $user['id'] : null,
                'is_template' => true
            ];
            
            $templates = $this->routineModel->getTemplates($filters);
            
            echo json_encode([
                'success' => true,
                'templates' => $templates
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting templates: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Contar plantillas
     */
    public function countTemplates()
    {
        if (!$this->checkPermissions()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        try {
            $user = AppHelper::getCurrentUser();
            $filters = [
                'instructor_id' => $user['role'] === 'instructor' ? $user['id'] : null,
                'is_template' => true
            ];
            
            $count = $this->routineModel->countTemplates($filters);
            
            echo json_encode([
                'success' => true,
                'count' => (int)$count
            ]);
            
        } catch (Exception $e) {
            error_log("Error counting templates: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Crear plantilla
     */
    public function createTemplate()
    {
        if (!$this->checkPermissions()) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para realizar esta acción');
            AppHelper::redirect('/trainer/templates');
            return;
        }

        // Redirigir al formulario de creación
        AppHelper::redirect('/trainer/templates/create');
    }

    /**
     * Obtener ejercicios de plantilla por zona corporal (API)
     */
    public function getTemplateExercisesByZone()
    {
        if (!$this->checkPermissions()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        $templateId = $_GET['template_id'] ?? '';
        $zone = $_GET['zone'] ?? '';
        
        if (empty($templateId)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de plantilla requerido']);
            return;
        }

        try {
            $exercises = $this->routineModel->getTemplateExercisesByZone($templateId, $zone);
            
            echo json_encode([
                'success' => true,
                'exercises' => $exercises
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting template exercises by zone: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Añadir ejercicio a plantilla
     */
    public function addExerciseToTemplate()
    {
        if (!$this->checkPermissions()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        $templateId = $_POST['template_id'] ?? '';
        $exerciseId = $_POST['exercise_id'] ?? '';
        $bodyZone = $_POST['body_zone'] ?? '';
        $exerciseData = $_POST['exercise_data'] ?? [];
        
        if (empty($templateId) || empty($exerciseId)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de plantilla y ejercicio requeridos']);
            return;
        }

        try {
            $exerciseData['routine_id'] = $templateId;
            $exerciseData['exercise_id'] = $exerciseId;
            $exerciseData['body_zone'] = $bodyZone;
            
            $result = $this->routineModel->addExerciseToTemplate($exerciseData);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Ejercicio añadido correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al añadir ejercicio']);
            }
            
        } catch (Exception $e) {
            error_log("Error adding exercise to template: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Remover todos los ejercicios de una plantilla
     */
    public function removeAllTemplateExercises()
    {
        if (!$this->checkPermissions()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        $templateId = $_POST['template_id'] ?? '';
        
        if (empty($templateId)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de plantilla requerido']);
            return;
        }

        try {
            $result = $this->routineModel->removeAllTemplateExercises($templateId);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Ejercicios removidos correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al remover ejercicios']);
            }
            
        } catch (Exception $e) {
            error_log("Error removing template exercises: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Obtener clientes del instructor
     */
    public function getInstructorClients()
    {
        if (!$this->checkPermissions()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        try {
            $user = AppHelper::getCurrentUser();
            
            if ($user['role'] !== 'instructor') {
                http_response_code(403);
                echo json_encode(['error' => 'Solo instructores pueden acceder a esta función']);
                return;
            }
            
            $clients = $this->userModel->getInstructorClients($user['id']);
            
            echo json_encode([
                'success' => true,
                'clients' => $clients
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting instructor clients: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Verificar permisos de instructor
     */
    private function checkInstructorPermissions()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return false;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['instructor', 'admin'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            AppHelper::redirect('/dashboard');
            return false;
        }

        return true;
    }

    /**
     * Verificar si puede ver la plantilla
     */
    private function canViewTemplate($template, $user)
    {
        if ($user['role'] === 'admin') {
            return true;
        }
        
        if ($user['role'] === 'instructor') {
            return $template['instructor_id'] == $user['id'] || $template['is_public'];
        }
        
        return false;
    }

    /**
     * Verificar si puede editar la plantilla
     */
    private function canEditTemplate($template, $user)
    {
        if ($user['role'] === 'admin') {
            return true;
        }
        
        if ($user['role'] === 'instructor') {
            return $template['instructor_id'] == $user['id'];
        }
        
        return false;
    }

    /**
     * Verificar si puede usar la plantilla
     */
    private function canUseTemplate($template, $user)
    {
        if ($user['role'] === 'admin') {
            return true;
        }
        
        if ($user['role'] === 'instructor') {
            return $template['instructor_id'] == $user['id'] || $template['is_public'];
        }
        
        return false;
    }

    /**
     * Validar datos de la plantilla
     */
    private function validateTemplateData($data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'El nombre de la plantilla es requerido';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'El nombre debe tener al menos 3 caracteres';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'La descripción es requerida';
        }

        if (empty($data['objective'])) {
            $errors['objective'] = 'El objetivo es requerido';
        }

        if (empty($data['difficulty_level'])) {
            $errors['difficulty_level'] = 'El nivel de dificultad es requerido';
        }

        if (empty($data['target_gender'])) {
            $errors['target_gender'] = 'El género objetivo es requerido';
        } elseif (!in_array($data['target_gender'], ['male', 'female', 'unisex'])) {
            $errors['target_gender'] = 'Género objetivo inválido';
        }

        if (empty($data['body_zones']) || !is_array($data['body_zones'])) {
            $errors['body_zones'] = 'Debe configurar al menos una zona corporal';
        }

        return $errors;
    }

    /**
     * Organizar ejercicios por zona corporal
     */
    private function organizeExercisesByBodyZone($exercises)
    {
        $bodyZones = [
            'chest' => 'Pecho',
            'back' => 'Espalda', 
            'shoulders' => 'Hombros',
            'arms' => 'Brazos',
            'legs' => 'Piernas',
            'glutes' => 'Glúteos',
            'core' => 'Core/Abdomen',
            'cardio' => 'Cardio'
        ];

        $exercisesByZone = [];
        
        foreach ($bodyZones as $zone => $zoneName) {
            $exercisesByZone[$zone] = [
                'name' => $zoneName,
                'exercises' => []
            ];
        }

        foreach ($exercises as $exercise) {
            $muscleGroups = json_decode($exercise['muscle_groups'], true) ?? [];
            
            foreach ($muscleGroups as $muscleGroup) {
                $zone = $this->mapMuscleGroupToBodyZone($muscleGroup);
                if ($zone && isset($exercisesByZone[$zone])) {
                    $exercisesByZone[$zone]['exercises'][] = $exercise;
                }
            }
        }

        return $exercisesByZone;
    }

    /**
     * Mapear grupo muscular a zona corporal
     */
    private function mapMuscleGroupToBodyZone($muscleGroup)
    {
        $mapping = [
            'pectorales' => 'chest',
            'pecho' => 'chest',
            'espalda' => 'back',
            'dorsales' => 'back',
            'trapecio' => 'back',
            'hombros' => 'shoulders',
            'deltoides' => 'shoulders',
            'brazos' => 'arms',
            'biceps' => 'arms',
            'triceps' => 'arms',
            'antebrazos' => 'arms',
            'piernas' => 'legs',
            'cuadriceps' => 'legs',
            'isquiotibiales' => 'legs',
            'pantorrillas' => 'legs',
            'gluteos' => 'glutes',
            'core' => 'core',
            'abdominales' => 'core',
            'cardio' => 'cardio'
        ];

        return $mapping[strtolower($muscleGroup)] ?? null;
    }

    /**
     * Procesar zonas corporales de la plantilla
     */
    private function processTemplateBodyZones($templateId, $bodyZones)
    {
        foreach ($bodyZones as $zone => $zoneData) {
            if (!empty($zoneData['exercises'])) {
                foreach ($zoneData['exercises'] as $exerciseData) {
                    $templateExerciseData = [
                        'routine_id' => $templateId,
                        'exercise_id' => $exerciseData['exercise_id'],
                        'body_zone' => $zone,
                        'day_number' => $exerciseData['day_number'] ?? 1,
                        'order_index' => $exerciseData['order_index'] ?? 1,
                        'sets' => $exerciseData['sets'] ?? 3,
                        'reps' => $exerciseData['reps'] ?? '10',
                        'weight' => $exerciseData['weight'] ?? '',
                        'rest_seconds' => $exerciseData['rest_seconds'] ?? 60,
                        'tempo' => $exerciseData['tempo'] ?? '',
                        'notes' => $exerciseData['notes'] ?? ''
                    ];
                    
                    $this->routineModel->addExerciseToTemplate($templateExerciseData);
                }
            }
        }
    }

    /**
     * Actualizar zonas corporales de la plantilla
     */
    private function updateTemplateBodyZones($templateId, $bodyZones)
    {
        // Eliminar ejercicios existentes
        $this->routineModel->removeAllTemplateExercises($templateId);
        
        // Agregar nuevos ejercicios
        $this->processTemplateBodyZones($templateId, $bodyZones);
    }

    /**
     * Crear rutina desde plantilla
     */
    private function createRoutineFromTemplate($templateId, $clientId, $customizations = [])
    {
        $template = $this->routineModel->findById($templateId);
        if (!$template) {
            return false;
        }

        $user = AppHelper::getCurrentUser();
        
        // Crear nueva rutina basada en la plantilla
        $routineData = [
            'gym_id' => $user['gym_id'],
            'instructor_id' => $user['id'],
            'client_id' => $clientId,
            'name' => $template['name'] . ' - Personalizada',
            'description' => $template['description'],
            'objective' => $template['objective'],
            'difficulty_level' => $template['difficulty_level'],
            'duration_weeks' => $template['duration_weeks'],
            'sessions_per_week' => $template['sessions_per_week'],
            'estimated_duration_minutes' => $template['estimated_duration_minutes'],
            'is_template' => false,
            'is_active' => true,
            'template_id' => $templateId
        ];

        // Aplicar personalizaciones
        if (!empty($customizations)) {
            $routineData = array_merge($routineData, $customizations);
        }

        $routineId = $this->routineModel->create($routineData);
        
        if ($routineId) {
            // Copiar ejercicios de la plantilla
            $this->copyTemplateExercisesToRoutine($templateId, $routineId, $customizations);
        }

        return $routineId;
    }

    /**
     * Copiar ejercicios de plantilla a rutina
     */
    private function copyTemplateExercisesToRoutine($templateId, $routineId, $customizations = [])
    {
        $templateExercises = $this->routineModel->getRoutineExercises($templateId);
        
        foreach ($templateExercises as $exercise) {
            $exerciseData = [
                'routine_id' => $routineId,
                'exercise_id' => $exercise['exercise_id'],
                'day_number' => $exercise['day_number'],
                'order_index' => $exercise['order_index'],
                'sets' => $exercise['sets'],
                'reps' => $exercise['reps'],
                'weight' => $exercise['weight'],
                'rest_seconds' => $exercise['rest_seconds'],
                'notes' => $exercise['notes'],
                'body_zone' => $exercise['body_zone']
            ];
            
            // Aplicar personalizaciones específicas del ejercicio
            $exerciseId = $exercise['exercise_id'];
            if (isset($customizations['exercises'][$exerciseId])) {
                $exerciseData = array_merge($exerciseData, $customizations['exercises'][$exerciseId]);
            }
            
            $this->routineModel->addExerciseToRoutine($exerciseData);
        }
    }

    /**
     * Duplicar plantilla
     */
    private function duplicateTemplate($templateId)
    {
        $template = $this->routineModel->findById($templateId);
        if (!$template) {
            return false;
        }

        $user = AppHelper::getCurrentUser();
        
        // Crear nueva plantilla
        $newTemplateData = $template;
        unset($newTemplateData['id'], $newTemplateData['created_at'], $newTemplateData['updated_at']);
        $newTemplateData['name'] = $template['name'] . ' - Copia';
        $newTemplateData['instructor_id'] = $user['id'];
        $newTemplateData['is_public'] = false;
        
        $newTemplateId = $this->routineModel->create($newTemplateData);
        
        if ($newTemplateId) {
            // Copiar ejercicios
            $this->copyTemplateExercisesToRoutine($templateId, $newTemplateId);
        }

        return $newTemplateId;
    }

    /**
     * Obtener estadísticas de uso de la plantilla
     */
    private function getTemplateUsageStats($templateId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_uses,
                COUNT(DISTINCT client_id) as unique_clients,
                AVG(CASE WHEN r.is_active = 1 THEN 1 ELSE 0 END) as active_rate
            FROM routines r 
            WHERE r.template_id = ?
        ");
        $stmt->execute([$templateId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}