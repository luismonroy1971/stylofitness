<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\Room;
use StyleFitness\Models\Gym;
use StyleFitness\Helpers\AppHelper;
use Exception;

/**
 * Controlador de Salas - STYLOFITNESS
 * Gestión de salas, posiciones específicas y control de aforo
 */
class RoomController
{
    private $db;
    private $roomModel;
    private $gymModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->roomModel = new Room();
        $this->gymModel = new Gym();
    }

    /**
     * Mostrar lista de salas
     */
    public function index()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['admin', 'instructor'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            AppHelper::redirect('/');
            return;
        }

        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'gym_id' => AppHelper::sanitize($_GET['gym_id'] ?? ''),
            'room_type' => AppHelper::sanitize($_GET['room_type'] ?? ''),
            'limit' => $limit,
            'offset' => $offset
        ];

        // Si es instructor, solo mostrar salas de sus gimnasios
        if ($user['role'] === 'instructor') {
            $filters['gym_id'] = $user['gym_id'] ?? 0;
        }

        $rooms = $this->roomModel->getRooms($filters);
        $totalRooms = $this->roomModel->countRooms($filters);
        $totalPages = ceil($totalRooms / $limit);

        // Obtener gimnasios para filtro
        $gyms = $this->gymModel->getActive();
        $roomTypes = $this->roomModel->getRoomTypes();

        $pageTitle = 'Gestión de Salas - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin.js'];

        $viewData = [
            'rooms' => $rooms,
            'gyms' => $gyms,
            'roomTypes' => $roomTypes,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalRooms
            ]
        ];

        extract($viewData);
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/rooms/index.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Mostrar formulario de creación de sala
     */
    public function create()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if ($user['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'No tienes permisos para crear salas');
            AppHelper::redirect('/rooms');
            return;
        }

        $gyms = $this->gymModel->getActive();
        $roomTypes = $this->roomModel->getRoomTypes();
        $positionTypes = $this->roomModel->getPositionTypes();

        $pageTitle = 'Crear Sala - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin.js'];

        $viewData = [
            'gyms' => $gyms,
            'roomTypes' => $roomTypes,
            'positionTypes' => $positionTypes
        ];

        extract($viewData);
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/rooms/create.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Procesar creación de sala
     */
    public function store()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if ($user['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'No tienes permisos para crear salas');
            AppHelper::redirect('/rooms');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/rooms/create');
            return;
        }

        $data = [
            'gym_id' => (int)($_POST['gym_id'] ?? 0),
            'name' => AppHelper::sanitize($_POST['name'] ?? ''),
            'description' => AppHelper::sanitize($_POST['description'] ?? ''),
            'room_type' => AppHelper::sanitize($_POST['room_type'] ?? ''),
            'total_capacity' => (int)($_POST['total_capacity'] ?? 0),
            'floor_plan_image' => AppHelper::sanitize($_POST['floor_plan_image'] ?? ''),
            'equipment_available' => $_POST['equipment_available'] ?? [],
            'amenities' => $_POST['amenities'] ?? [],
            'dimensions' => AppHelper::sanitize($_POST['dimensions'] ?? ''),
            'location_notes' => AppHelper::sanitize($_POST['location_notes'] ?? ''),
            'is_active' => isset($_POST['is_active'])
        ];

        // Validar datos
        $errors = $this->roomModel->validateRoom($data);

        if (!empty($errors)) {
            AppHelper::setFlashMessage('error', 'Por favor corrige los errores en el formulario');
            $_SESSION['form_data'] = $data;
            $_SESSION['form_errors'] = $errors;
            AppHelper::redirect('/rooms/create');
            return;
        }

        $roomId = $this->roomModel->create($data);

        if ($roomId) {
            AppHelper::setFlashMessage('success', 'Sala creada exitosamente');
            AppHelper::redirect('/rooms/' . $roomId);
        } else {
            AppHelper::setFlashMessage('error', 'Error al crear la sala');
            AppHelper::redirect('/rooms/create');
        }
    }

    /**
     * Mostrar detalles de una sala
     */
    public function show($id)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['admin', 'instructor'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            AppHelper::redirect('/');
            return;
        }

        $room = $this->roomModel->findById($id);
        if (!$room) {
            AppHelper::setFlashMessage('error', 'Sala no encontrada');
            AppHelper::redirect('/rooms');
            return;
        }

        // Verificar permisos para instructores
        if ($user['role'] === 'instructor' && $room['gym_id'] != $user['gym_id']) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para ver esta sala');
            AppHelper::redirect('/rooms');
            return;
        }

        $stats = $this->roomModel->getRoomStats($id);

        $pageTitle = $room['name'] . ' - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin.js'];

        $viewData = [
            'room' => $room,
            'stats' => $stats
        ];

        extract($viewData);
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/rooms/show.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if ($user['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'No tienes permisos para editar salas');
            AppHelper::redirect('/rooms');
            return;
        }

        $room = $this->roomModel->findById($id);
        if (!$room) {
            AppHelper::setFlashMessage('error', 'Sala no encontrada');
            AppHelper::redirect('/rooms');
            return;
        }

        $gyms = $this->gymModel->getActive();
        $roomTypes = $this->roomModel->getRoomTypes();
        $positionTypes = $this->roomModel->getPositionTypes();

        $pageTitle = 'Editar ' . $room['name'] . ' - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin.js'];

        $viewData = [
            'room' => $room,
            'gyms' => $gyms,
            'roomTypes' => $roomTypes,
            'positionTypes' => $positionTypes
        ];

        extract($viewData);
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/rooms/edit.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Procesar actualización de sala
     */
    public function update($id)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if ($user['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'No tienes permisos para actualizar salas');
            AppHelper::redirect('/rooms');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/rooms/' . $id . '/edit');
            return;
        }

        $room = $this->roomModel->findById($id);
        if (!$room) {
            AppHelper::setFlashMessage('error', 'Sala no encontrada');
            AppHelper::redirect('/rooms');
            return;
        }

        $data = [
            'name' => AppHelper::sanitize($_POST['name'] ?? ''),
            'description' => AppHelper::sanitize($_POST['description'] ?? ''),
            'room_type' => AppHelper::sanitize($_POST['room_type'] ?? ''),
            'total_capacity' => (int)($_POST['total_capacity'] ?? 0),
            'floor_plan_image' => AppHelper::sanitize($_POST['floor_plan_image'] ?? ''),
            'equipment_available' => $_POST['equipment_available'] ?? [],
            'amenities' => $_POST['amenities'] ?? [],
            'dimensions' => AppHelper::sanitize($_POST['dimensions'] ?? ''),
            'location_notes' => AppHelper::sanitize($_POST['location_notes'] ?? ''),
            'is_active' => isset($_POST['is_active'])
        ];

        // Validar datos
        $errors = $this->roomModel->validateRoom(array_merge($data, ['gym_id' => $room['gym_id']]));

        if (!empty($errors)) {
            AppHelper::setFlashMessage('error', 'Por favor corrige los errores en el formulario');
            $_SESSION['form_data'] = $data;
            $_SESSION['form_errors'] = $errors;
            AppHelper::redirect('/rooms/' . $id . '/edit');
            return;
        }

        $success = $this->roomModel->update($id, $data);

        if ($success) {
            AppHelper::setFlashMessage('success', 'Sala actualizada exitosamente');
            AppHelper::redirect('/rooms/' . $id);
        } else {
            AppHelper::setFlashMessage('error', 'Error al actualizar la sala');
            AppHelper::redirect('/rooms/' . $id . '/edit');
        }
    }

    /**
     * Eliminar sala
     */
    public function delete($id)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if ($user['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'No tienes permisos para eliminar salas');
            AppHelper::redirect('/rooms');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/rooms');
            return;
        }

        $room = $this->roomModel->findById($id);
        if (!$room) {
            AppHelper::setFlashMessage('error', 'Sala no encontrada');
            AppHelper::redirect('/rooms');
            return;
        }

        $success = $this->roomModel->delete($id);

        if ($success) {
            AppHelper::setFlashMessage('success', 'Sala eliminada exitosamente');
        } else {
            AppHelper::setFlashMessage('error', 'No se puede eliminar la sala. Puede tener clases activas asociadas.');
        }

        AppHelper::redirect('/rooms');
    }

    /**
     * Gestionar posiciones de una sala
     */
    public function positions($id)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if ($user['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'No tienes permisos para gestionar posiciones');
            AppHelper::redirect('/rooms');
            return;
        }

        $room = $this->roomModel->findById($id);
        if (!$room) {
            AppHelper::setFlashMessage('error', 'Sala no encontrada');
            AppHelper::redirect('/rooms');
            return;
        }

        if ($room['room_type'] !== 'positioned') {
            AppHelper::setFlashMessage('error', 'Esta sala no maneja posiciones específicas');
            AppHelper::redirect('/rooms/' . $id);
            return;
        }

        $positions = $this->roomModel->getRoomPositions($id);
        $positionTypes = $this->roomModel->getPositionTypes();

        $pageTitle = 'Posiciones - ' . $room['name'] . ' - STYLOFITNESS';
        $additionalCSS = ['admin.css', 'rooms.css'];
        $additionalJS = ['admin.js', 'rooms.js'];

        $viewData = [
            'room' => $room,
            'positions' => $positions,
            'positionTypes' => $positionTypes
        ];

        extract($viewData);
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/rooms/positions.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Crear nueva posición
     */
    public function createPosition($roomId)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if ($user['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'No tienes permisos para crear posiciones');
            AppHelper::redirect('/rooms');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/rooms/' . $roomId . '/positions');
            return;
        }

        $room = $this->roomModel->findById($roomId);
        if (!$room || $room['room_type'] !== 'positioned') {
            AppHelper::setFlashMessage('error', 'Sala no válida para posiciones');
            AppHelper::redirect('/rooms');
            return;
        }

        $data = [
            'room_id' => $roomId,
            'position_number' => trim($_POST['position_number']),
            'row_number' => trim($_POST['row_number'] ?? ''),
            'seat_number' => trim($_POST['seat_number'] ?? ''),
            'x_coordinate' => (float)($_POST['x_coordinate'] ?? 0),
            'y_coordinate' => (float)($_POST['y_coordinate'] ?? 0),
            'position_type' => $_POST['position_type'] ?? 'regular',
            'is_available' => isset($_POST['is_available']),
            'notes' => trim($_POST['notes'] ?? '')
        ];

        $positionId = $this->roomModel->createPosition($data);

        if ($positionId) {
            AppHelper::setFlashMessage('success', 'Posición creada exitosamente');
        } else {
            AppHelper::setFlashMessage('error', 'Error al crear la posición');
        }

        AppHelper::redirect('/rooms/' . $roomId . '/positions');
    }

    /**
     * Actualizar posición
     */
    public function updatePosition($roomId, $positionId)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if ($user['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'No tienes permisos para actualizar posiciones');
            AppHelper::redirect('/rooms');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/rooms/' . $roomId . '/positions');
            return;
        }

        $data = [
            'position_number' => trim($_POST['position_number']),
            'row_number' => trim($_POST['row_number'] ?? ''),
            'seat_number' => trim($_POST['seat_number'] ?? ''),
            'x_coordinate' => (float)($_POST['x_coordinate'] ?? 0),
            'y_coordinate' => (float)($_POST['y_coordinate'] ?? 0),
            'position_type' => $_POST['position_type'] ?? 'regular',
            'is_available' => isset($_POST['is_available']),
            'notes' => trim($_POST['notes'] ?? '')
        ];

        $success = $this->roomModel->updatePosition($positionId, $data);

        if ($success) {
            AppHelper::setFlashMessage('success', 'Posición actualizada exitosamente');
        } else {
            AppHelper::setFlashMessage('error', 'Error al actualizar la posición');
        }

        AppHelper::redirect('/rooms/' . $roomId . '/positions');
    }

    /**
     * Eliminar posición
     */
    public function deletePosition($roomId, $positionId)
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/auth/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        if ($user['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'No tienes permisos para eliminar posiciones');
            AppHelper::redirect('/rooms');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/rooms/' . $roomId . '/positions');
            return;
        }

        $success = $this->roomModel->deletePosition($positionId);

        if ($success) {
            AppHelper::setFlashMessage('success', 'Posición eliminada exitosamente');
        } else {
            AppHelper::setFlashMessage('error', 'Error al eliminar la posición');
        }

        AppHelper::redirect('/rooms/' . $roomId . '/positions');
    }

    /**
     * API: Obtener salas por gimnasio
     */
    public function apiGetRoomsByGym($gymId)
    {
        if (!AppHelper::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['admin', 'instructor'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Sin permisos']);
            return;
        }

        $rooms = $this->roomModel->getRoomsByGym($gymId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'rooms' => $rooms
        ]);
    }

    /**
     * API: Obtener disponibilidad de posiciones
     */
    public function apiGetPositionAvailability($roomId)
    {
        if (!AppHelper::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['admin', 'instructor', 'client'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Sin permisos']);
            return;
        }

        $scheduleId = $_GET['schedule_id'] ?? null;
        $bookingDate = $_GET['booking_date'] ?? null;

        if (!$scheduleId || !$bookingDate) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Parámetros requeridos: schedule_id, booking_date'
            ]);
            return;
        }

        $positions = $this->roomModel->getPositionAvailability($roomId, $scheduleId, $bookingDate);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'positions' => $positions
        ]);
    }

    /**
     * API: Verificar disponibilidad de sala
     */
    public function apiCheckAvailability($roomId)
    {
        if (!AppHelper::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        $user = AppHelper::getCurrentUser();
        if (!in_array($user['role'], ['admin', 'instructor'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Sin permisos']);
            return;
        }

        $date = $_GET['date'] ?? null;
        $startTime = $_GET['start_time'] ?? null;
        $endTime = $_GET['end_time'] ?? null;
        $excludeClassId = $_GET['exclude_class_id'] ?? null;

        if (!$date || !$startTime || !$endTime) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Parámetros requeridos: date, start_time, end_time'
            ]);
            return;
        }

        $isAvailable = $this->roomModel->checkRoomAvailability($roomId, $date, $startTime, $endTime, $excludeClassId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'available' => $isAvailable
        ]);
    }
}