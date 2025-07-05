<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\WorkoutLog;
use StyleFitness\Models\Routine;
use StyleFitness\Models\User;
use StyleFitness\Models\Exercise;
use StyleFitness\Helpers\AppHelper;
use Exception;

/**
 * Controlador de Seguimiento de Progreso para Entrenadores - STYLOFITNESS
 * Maneja el seguimiento detallado del progreso de clientes
 */

class TrainerProgressController
{
    private $db;
    private $workoutLogModel;
    private $routineModel;
    private $userModel;
    private $exerciseModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->workoutLogModel = new WorkoutLog();
        $this->routineModel = new Routine();
        $this->userModel = new User();
        $this->exerciseModel = new Exercise();
    }

    /**
     * Dashboard principal de seguimiento
     */
    public function index()
    {
        // Verificar permisos
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['instructor', 'admin'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            AppHelper::redirect('/dashboard');
            return;
        }

        $trainerId = $user['role'] === 'instructor' ? $user['id'] : null;
        
        // Obtener clientes activos
        $activeClients = $this->workoutLogModel->getTrainerActiveClients($trainerId ?? 0, 30);
        
        // Obtener alertas de progreso
        $alerts = $this->workoutLogModel->getProgressAlerts($trainerId ?? 0);
        
        // Estadísticas generales
        $stats = $this->getTrainerOverviewStats($trainerId);
        
        $pageTitle = 'Seguimiento de Progreso - STYLOFITNESS';
        $additionalCSS = ['trainer-progress.css'];
        $additionalJS = ['trainer-progress.js', 'chart.min.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/trainer/progress/index.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Ver progreso detallado de un cliente específico
     */
    public function clientDetail($clientId)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['instructor', 'admin'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            AppHelper::redirect('/dashboard');
            return;
        }

        // Verificar que el cliente pertenece al entrenador (si no es admin)
        if ($user['role'] === 'instructor') {
            $clientRoutines = $this->routineModel->getInstructorRoutines($user['id'], ['client_id' => $clientId]);
            if (empty($clientRoutines)) {
                AppHelper::setFlashMessage('error', 'No tienes permisos para ver este cliente');
                AppHelper::redirect('/trainer/progress');
                return;
            }
        }

        // Obtener información del cliente
        $client = $this->userModel->findById($clientId);
        if (!$client || $client['role'] !== 'client') {
            AppHelper::setFlashMessage('error', 'Cliente no encontrado');
            AppHelper::redirect('/trainer/progress');
            return;
        }

        // Obtener datos de progreso
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
        $progressStats = $this->workoutLogModel->getClientProgressStats($clientId, $days);
        $workoutHistory = $this->workoutLogModel->getClientWorkoutHistory($clientId, ['limit' => 50]);
        $dailyActivity = $this->workoutLogModel->getDailyActivitySummary($clientId, 14);
        $progressComparison = $this->workoutLogModel->getProgressComparison($clientId, $days, $days);
        
        // Obtener rutinas del cliente
        $clientRoutines = $this->routineModel->getClientRoutines($clientId);
        
        // Obtener progreso por ejercicio (top 10 más frecuentes)
        $exerciseProgress = $this->getClientExerciseProgress($clientId, $days);

        $pageTitle = "Progreso de {$client['first_name']} {$client['last_name']} - STYLOFITNESS";
        $additionalCSS = ['trainer-progress.css', 'client-detail.css'];
        $additionalJS = ['trainer-progress.js', 'client-detail.js', 'chart.min.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/trainer/progress/client.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Vista de progreso para el cliente (acceso propio)
     */
    public function clientProgress()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        $clientId = $_GET['id'] ?? null;

        // Si es un cliente, solo puede ver su propio progreso
        if ($user['role'] === 'client') {
            if ($clientId && $clientId != $user['id']) {
                AppHelper::setFlashMessage('error', 'No tienes permisos para ver este progreso');
                AppHelper::redirect('/profile');
                return;
            }
            $clientId = $user['id'];
        }
        // Si es instructor o admin, verificar permisos
        elseif (in_array($user['role'], ['instructor', 'admin'])) {
            if (!$clientId) {
                AppHelper::setFlashMessage('error', 'Cliente no especificado');
                AppHelper::redirect('/trainer/progress');
                return;
            }

            $trainerId = $user['role'] === 'instructor' ? $user['id'] : null;
            if ($trainerId) {
                $clientRoutines = $this->routineModel->getInstructorRoutines($trainerId, ['client_id' => $clientId]);
                if (empty($clientRoutines)) {
                    AppHelper::setFlashMessage('error', 'No tienes permisos para ver este cliente');
                    AppHelper::redirect('/trainer/progress');
                    return;
                }
            }
        } else {
            AppHelper::setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            AppHelper::redirect('/dashboard');
            return;
        }

        $client = $this->userModel->findById($clientId);
        if (!$client || $client['role'] !== 'client') {
            AppHelper::setFlashMessage('error', 'Cliente no encontrado');
            AppHelper::redirect($user['role'] === 'client' ? '/profile' : '/trainer/progress');
            return;
        }

        $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;

        // Obtener datos del progreso
        $clientStats = $this->workoutLogModel->getClientProgressStats($clientId, $days);
        $recentWorkouts = $this->workoutLogModel->getClientRecentWorkouts($clientId, 10);
        $exerciseStats = $this->workoutLogModel->getClientExerciseStats($clientId, $days);
        $routines = $this->routineModel->getClientActiveRoutines($clientId);

        $pageTitle = "Progreso de {$client['first_name']} {$client['last_name']} - STYLOFITNESS";
        $additionalCSS = ['trainer-progress.css', 'client-detail.css'];
        $additionalJS = ['trainer-progress.js', 'client-detail.js', 'chart.min.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/trainer/progress/client.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Comparar progreso entre clientes
     */
    public function compareClients()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['instructor', 'admin'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            AppHelper::redirect('/dashboard');
            return;
        }

        $trainerId = $user['role'] === 'instructor' ? $user['id'] : null;
        $clientIds = $_GET['clients'] ?? [];
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;

        $comparisons = [];
        $clients = [];

        if (!empty($clientIds) && is_array($clientIds)) {
            foreach ($clientIds as $clientId) {
                // Verificar permisos
                if ($trainerId) {
                    $clientRoutines = $this->routineModel->getInstructorRoutines($trainerId, ['client_id' => $clientId]);
                    if (empty($clientRoutines)) continue;
                }

                $client = $this->userModel->findById($clientId);
                if ($client && $client['role'] === 'client') {
                    $clients[] = $client;
                    $comparisons[$clientId] = $this->workoutLogModel->getClientProgressStats($clientId, $days);
                }
            }
        }

        // Obtener lista de clientes disponibles
        $availableClients = $this->workoutLogModel->getTrainerActiveClients($trainerId ?? 0, 90);

        $pageTitle = 'Comparar Progreso de Clientes - STYLOFITNESS';
        $additionalCSS = ['trainer-progress.css', 'client-comparison.css'];
        $additionalJS = ['trainer-progress.js', 'client-comparison.js', 'chart.min.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/trainer/progress/compare-clients.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Generar reporte de progreso
     */
    public function generateReport()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['instructor', 'admin'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            AppHelper::redirect('/dashboard');
            return;
        }

        $trainerId = $user['role'] === 'instructor' ? $user['id'] : null;
        $clientId = $_GET['client_id'] ?? null;
        $reportType = $_GET['type'] ?? 'monthly';
        $format = $_GET['format'] ?? 'html';

        if (!$clientId) {
            AppHelper::setFlashMessage('error', 'Debe seleccionar un cliente');
            AppHelper::redirect('/trainer/progress');
            return;
        }

        // Verificar permisos
        if ($trainerId) {
            $clientRoutines = $this->routineModel->getInstructorRoutines($trainerId, ['client_id' => $clientId]);
            if (empty($clientRoutines)) {
                AppHelper::setFlashMessage('error', 'No tienes permisos para ver este cliente');
                AppHelper::redirect('/trainer/progress');
                return;
            }
        }

        $client = $this->userModel->findById($clientId);
        if (!$client || $client['role'] !== 'client') {
            AppHelper::setFlashMessage('error', 'Cliente no encontrado');
            AppHelper::redirect('/trainer/progress');
            return;
        }

        // Generar datos del reporte según el tipo
        $reportData = $this->generateReportData($clientId, $reportType);

        if ($format === 'pdf') {
            $this->generatePDFReport($client, $reportData, $reportType);
        } else {
            $pageTitle = "Reporte de Progreso - {$client['first_name']} {$client['last_name']}";
            $additionalCSS = ['trainer-progress.css', 'report.css'];
            $additionalJS = ['chart.min.js'];

            include APP_PATH . '/Views/layout/header.php';
            include APP_PATH . '/Views/trainer/progress/report.php';
            include APP_PATH . '/Views/layout/footer.php';
        }
    }

    /**
     * API: Obtener datos de progreso en formato JSON
     */
    public function getProgressData()
    {
        if (!AppHelper::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['instructor', 'admin'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permisos']);
            return;
        }

        $clientId = $_GET['client_id'] ?? null;
        $exerciseId = $_GET['exercise_id'] ?? null;
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;

        if (!$clientId) {
            http_response_code(400);
            echo json_encode(['error' => 'client_id requerido']);
            return;
        }

        try {
            if ($exerciseId) {
                // Progreso de ejercicio específico
                $data = $this->workoutLogModel->getExerciseProgress($clientId, $exerciseId, $days);
            } else {
                // Estadísticas generales
                $data = $this->workoutLogModel->getClientProgressStats($clientId, $days);
            }

            header('Content-Type: application/json');
            echo json_encode($data);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Obtener estadísticas generales del entrenador
     */
    private function getTrainerOverviewStats($trainerId)
    {
        $stats = [];

        if ($trainerId) {
            // Estadísticas específicas del entrenador
            $stats = $this->routineModel->getInstructorStats($trainerId);
            
            // Agregar estadísticas de workout logs
            $activeClients = $this->workoutLogModel->getTrainerActiveClients($trainerId, 30);
            $stats['active_clients_count'] = count($activeClients);
            
            // Promedio de entrenamientos por cliente
            $totalWorkouts = array_sum(array_column($activeClients, 'workout_days'));
            $stats['avg_workouts_per_client'] = $stats['active_clients_count'] > 0 
                ? round($totalWorkouts / $stats['active_clients_count'], 1) : 0;
        } else {
            // Estadísticas globales para admin
            $stats = $this->routineModel->getGlobalStats();
        }

        return $stats;
    }

    /**
     * Obtener progreso por ejercicio de un cliente
     */
    private function getClientExerciseProgress($clientId, $days)
    {
        $exerciseProgress = [];
        
        // Obtener ejercicios más frecuentes
        $frequentExercises = $this->workoutLogModel->getClientProgressStats($clientId, $days)['most_frequent_exercises'] ?? [];
        
        foreach ($frequentExercises as $exercise) {
            $exerciseId = $this->exerciseModel->getExerciseIdByName($exercise['name']);
            if ($exerciseId) {
                $progress = $this->workoutLogModel->getExerciseProgress($clientId, $exerciseId, $days);
                $exerciseProgress[] = [
                    'exercise' => $exercise,
                    'progress' => $progress
                ];
            }
        }
        
        return $exerciseProgress;
    }

    /**
     * Generar datos para reporte
     */
    private function generateReportData($clientId, $reportType)
    {
        $days = match($reportType) {
            'weekly' => 7,
            'monthly' => 30,
            'quarterly' => 90,
            'yearly' => 365,
            default => 30
        };

        return [
            'stats' => $this->workoutLogModel->getClientProgressStats($clientId, $days),
            'history' => $this->workoutLogModel->getClientWorkoutHistory($clientId, ['limit' => 100]),
            'daily_activity' => $this->workoutLogModel->getDailyActivitySummary($clientId, $days),
            'comparison' => $this->workoutLogModel->getProgressComparison($clientId, $days, $days),
            'routines' => $this->routineModel->getClientRoutines($clientId),
            'period' => $reportType,
            'days' => $days
        ];
    }

    /**
     * Generar reporte en PDF
     */
    private function generatePDFReport($client, $reportData, $reportType)
    {
        // Aquí se implementaría la generación de PDF
        // Por ahora, redirigir a la versión HTML
        AppHelper::setFlashMessage('info', 'Generación de PDF en desarrollo. Mostrando versión HTML.');
        AppHelper::redirect('/trainer/progress/report?client_id=' . $client['id'] . '&type=' . $reportType);
    }

}