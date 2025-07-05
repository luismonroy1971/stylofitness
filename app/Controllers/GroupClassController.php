<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\GroupClass;
use StyleFitness\Models\User;
use StyleFitness\Models\Room;
use StyleFitness\Helpers\AppHelper;
use Exception;

/**
 * Controlador de Clases Grupales - STYLOFITNESS
 * Maneja clases grupales, horarios y reservas
 */

class GroupClassController
{
    private $db;
    private $classModel;
    private $userModel;
    private $roomModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->classModel = new GroupClass();
        $this->userModel = new User();
        $this->roomModel = new Room();
    }

    public function index()
    {
        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'class_type' => AppHelper::sanitize($_GET['type'] ?? ''),
            'difficulty' => AppHelper::sanitize($_GET['difficulty'] ?? ''),
            'day' => AppHelper::sanitize($_GET['day'] ?? ''),
            'instructor' => AppHelper::sanitize($_GET['instructor'] ?? ''),
        ];

        // Obtener clases con horarios
        $classes = $this->classModel->getClassesWithSchedules($filters);

        // Obtener tipos de clases únicos
        $classTypes = $this->classModel->getClassTypes();

        // Obtener instructores únicos
        $instructors = $this->classModel->getInstructors();

        $pageTitle = 'Clases Grupales - STYLOFITNESS';
        $additionalCSS = ['classes.css'];
        $additionalJS = ['classes.js'];

        // Asegurarse de que las variables estén disponibles en la vista
        $viewData = [
            'classes' => $classes,
            'filters' => $filters,
            'classTypes' => $classTypes,
            'instructors' => $instructors,
        ];

        // Extraer variables para que estén disponibles en la vista
        extract($viewData);

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/classes/index.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function show($id)
    {
        $class = $this->classModel->findById($id);

        if (!$class || !$class['is_active']) {
            AppHelper::setFlashMessage('error', 'Clase no encontrada');
            AppHelper::redirect('/classes');
            return;
        }

        // Obtener horarios de la clase
        $schedules = $this->classModel->getClassSchedules($id);

        // Obtener reservas para cada horario (próximos 7 días)
        $bookings = [];
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+7 days'));

        foreach ($schedules as $schedule) {
            $bookings[$schedule['id']] = $this->classModel->getScheduleBookings(
                $schedule['id'],
                $startDate,
                $endDate
            );
        }

        // Obtener estadísticas de la clase
        $stats = $this->classModel->getClassStats($id);

        $pageTitle = $class['name'] . ' - STYLOFITNESS';
        $additionalCSS = ['class-detail.css'];
        $additionalJS = ['class-booking.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/classes/show.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function book()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }

        if (!AppHelper::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Debes iniciar sesión para reservar']);
            exit();
        }

        $scheduleId = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : 0;
        $bookingDate = AppHelper::sanitize($_POST['booking_date'] ?? '');
        $positionId = isset($_POST['position_id']) ? (int)$_POST['position_id'] : null;

        if (!$scheduleId || !$bookingDate) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            exit();
        }

        // Validar fecha
        if (!$this->isValidBookingDate($bookingDate)) {
            http_response_code(400);
            echo json_encode(['error' => 'Fecha de reserva inválida']);
            exit();
        }

        $user = AppHelper::getCurrentUser();

        // Verificar si ya tiene una reserva
        if ($this->classModel->hasUserBooking($scheduleId, $user['id'], $bookingDate)) {
            http_response_code(409);
            echo json_encode(['error' => 'Ya tienes una reserva para esta clase']);
            exit();
        }

        // Verificar disponibilidad
        $schedule = $this->classModel->getScheduleById($scheduleId);
        if (!$schedule) {
            http_response_code(404);
            echo json_encode(['error' => 'Horario no encontrado']);
            exit();
        }

        $class = $this->classModel->findById($schedule['class_id']);
        
        // Verificar si la clase requiere selección de posición
        if ($this->classModel->requiresPositionSelection($schedule['class_id'])) {
            if (!$positionId) {
                http_response_code(400);
                echo json_encode(['error' => 'Debe seleccionar una posición específica']);
                exit();
            }
            
            // Verificar disponibilidad de la posición
            $positionAvailable = $this->roomModel->isPositionAvailable($positionId, $scheduleId, $bookingDate);
            if (!$positionAvailable) {
                http_response_code(409);
                echo json_encode(['error' => 'La posición seleccionada no está disponible']);
                exit();
            }
        } else {
            // Para salas de aforo, verificar capacidad total
            $currentBookings = $this->classModel->countBookings($scheduleId, $bookingDate);
            if ($currentBookings >= $class['max_participants']) {
                http_response_code(400);
                echo json_encode(['error' => 'Clase completa']);
                exit();
            }
        }

        // Verificar membresía activa
        if (!$this->hasActiveMembership($user)) {
            http_response_code(403);
            echo json_encode(['error' => 'Necesitas una membresía activa para reservar clases']);
            exit();
        }

        // Crear reserva con o sin posición
        if ($positionId) {
            $bookingId = $this->classModel->bookClassWithPosition($scheduleId, $user['id'], $bookingDate, $positionId);
        } else {
            $bookingData = [
                'schedule_id' => $scheduleId,
                'user_id' => $user['id'],
                'booking_date' => $bookingDate,
                'status' => 'booked',
                'payment_status' => $class['price'] > 0 ? 'pending' : 'paid',
                'amount_paid' => $class['price'],
            ];
            $bookingId = $this->classModel->createBooking($bookingData);
        }

        if ($bookingId) {
            // Enviar notificación
            $this->sendBookingNotification($user, $class, $schedule, $bookingDate);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Reserva realizada exitosamente',
                'booking_id' => $bookingId,
                'position_id' => $positionId,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al procesar la reserva']);
        }
    }

    public function cancelBooking()
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

        $bookingId = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;

        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de reserva requerido']);
            exit();
        }

        $user = AppHelper::getCurrentUser();
        $booking = $this->classModel->getBookingById($bookingId);

        if (!$booking || $booking['user_id'] != $user['id']) {
            http_response_code(404);
            echo json_encode(['error' => 'Reserva no encontrada']);
            exit();
        }

        // Verificar si se puede cancelar (ej: hasta 2 horas antes)
        $bookingDateTime = strtotime($booking['booking_date'] . ' ' . $booking['start_time']);
        $minCancelTime = $bookingDateTime - (2 * 3600); // 2 horas antes

        if (time() > $minCancelTime) {
            http_response_code(400);
            echo json_encode(['error' => 'No se puede cancelar con menos de 2 horas de anticipación']);
            exit();
        }

        // Cancelar reserva
        if ($this->classModel->cancelBooking($bookingId)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Reserva cancelada exitosamente',
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al cancelar la reserva']);
        }
    }

    public function myBookings()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();

        // Obtener reservas del usuario
        $upcomingBookings = $this->classModel->getUserBookings($user['id'], 'upcoming');
        $pastBookings = $this->classModel->getUserBookings($user['id'], 'past');

        $pageTitle = 'Mis Reservas - STYLOFITNESS';
        $additionalCSS = ['my-bookings.css'];
        $additionalJS = ['my-bookings.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/classes/my-bookings.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function schedule()
    {
        $week = isset($_GET['week']) ? (int)$_GET['week'] : 0;
        $startDate = date('Y-m-d', strtotime("Monday this week + {$week} weeks"));
        $endDate = date('Y-m-d', strtotime("Sunday this week + {$week} weeks"));

        // Obtener horarios de la semana
        $schedules = $this->classModel->getWeekSchedule($startDate, $endDate);

        // Organizar por días
        $weekSchedule = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            $weekSchedule[$day] = array_filter($schedules, function ($schedule) use ($day) {
                return $schedule['day_of_week'] === $day;
            });

            // Ordenar por hora
            usort($weekSchedule[$day], function ($a, $b) {
                return strtotime($a['start_time']) - strtotime($b['start_time']);
            });
        }

        $pageTitle = 'Horario de Clases - STYLOFITNESS';
        $additionalCSS = ['schedule.css'];
        $additionalJS = ['schedule.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/classes/schedule.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function getAvailability()
    {
        $scheduleId = isset($_GET['schedule_id']) ? (int)$_GET['schedule_id'] : 0;
        $date = AppHelper::sanitize($_GET['date'] ?? '');

        if (!$scheduleId || !$date) {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetros requeridos']);
            exit();
        }

        $schedule = $this->classModel->getScheduleById($scheduleId);
        if (!$schedule) {
            http_response_code(404);
            echo json_encode(['error' => 'Horario no encontrado']);
            exit();
        }

        $class = $this->classModel->findById($schedule['class_id']);
        $currentBookings = $this->classModel->countBookings($scheduleId, $date);
        $availableSpots = $class['max_participants'] - $currentBookings;

        header('Content-Type: application/json');
        echo json_encode([
            'available_spots' => max(0, $availableSpots),
            'max_participants' => $class['max_participants'],
            'current_bookings' => $currentBookings,
            'is_available' => $availableSpots > 0,
        ]);
    }

    private function isValidBookingDate($date)
    {
        $bookingDate = strtotime($date);
        $today = strtotime(date('Y-m-d'));
        $maxAdvanceBooking = strtotime('+30 days');

        return $bookingDate >= $today && $bookingDate <= $maxAdvanceBooking;
    }

    private function hasActiveMembership($user)
    {
        return strtotime($user['membership_expires']) > time();
    }

    private function sendBookingNotification($user, $class, $schedule, $bookingDate)
    {
        // Crear notificación en base de datos
        $notificationData = [
            'user_id' => $user['id'],
            'type' => 'class_booking',
            'title' => 'Reserva Confirmada',
            'message' => "Tu reserva para {$class['name']} el {$bookingDate} ha sido confirmada.",
            'data' => json_encode([
                'class_id' => $class['id'],
                'schedule_id' => $schedule['id'],
                'booking_date' => $bookingDate,
            ]),
        ];

        $sql = 'INSERT INTO notifications (user_id, type, title, message, data, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())';

        $this->db->query($sql, [
            $notificationData['user_id'],
            $notificationData['type'],
            $notificationData['title'],
            $notificationData['message'],
            $notificationData['data'],
        ]);

        // Aquí se podría enviar email/SMS
        // $this->sendBookingEmail($user, $class, $schedule, $bookingDate);
    }

    public function waitlist()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }

        if (!AppHelper::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Debes iniciar sesión']);
            exit();
        }

        $scheduleId = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : 0;
        $bookingDate = AppHelper::sanitize($_POST['booking_date'] ?? '');

        $user = AppHelper::getCurrentUser();

        // Verificar si ya está en lista de espera
        if ($this->classModel->isInWaitlist($scheduleId, $user['id'], $bookingDate)) {
            http_response_code(409);
            echo json_encode(['error' => 'Ya estás en la lista de espera']);
            exit();
        }

        // Agregar a lista de espera
        $waitlistData = [
            'schedule_id' => $scheduleId,
            'user_id' => $user['id'],
            'booking_date' => $bookingDate,
            'position' => $this->classModel->getNextWaitlistPosition($scheduleId, $bookingDate),
        ];

        if ($this->classModel->addToWaitlist($waitlistData)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Agregado a la lista de espera',
                'position' => $waitlistData['position'],
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al procesar solicitud']);
        }
    }

    public function getRoomLayout()
    {
        $scheduleId = isset($_GET['schedule_id']) ? (int)$_GET['schedule_id'] : 0;
        $bookingDate = AppHelper::sanitize($_GET['booking_date'] ?? '');

        if (!$scheduleId || !$bookingDate) {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetros requeridos']);
            exit();
        }

        $schedule = $this->classModel->getScheduleById($scheduleId);
        if (!$schedule) {
            http_response_code(404);
            echo json_encode(['error' => 'Horario no encontrado']);
            exit();
        }

        $roomLayout = $this->roomModel->getRoomLayout($schedule['room_id']);
        $occupiedPositions = $this->roomModel->getOccupiedPositions($scheduleId, $bookingDate);

        header('Content-Type: application/json');
        echo json_encode([
            'layout' => $roomLayout,
            'occupied_positions' => $occupiedPositions,
            'room_info' => [
                'id' => $schedule['room_id'],
                'name' => $schedule['room_name'],
                'capacity' => $schedule['room_capacity'],
            ],
        ]);
    }

    public function selectPosition()
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

        $scheduleId = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : 0;
        $positionId = isset($_POST['position_id']) ? (int)$_POST['position_id'] : 0;
        $bookingDate = AppHelper::sanitize($_POST['booking_date'] ?? '');

        if (!$scheduleId || !$positionId || !$bookingDate) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            exit();
        }

        $user = AppHelper::getCurrentUser();

        // Verificar si la posición está disponible
        if (!$this->roomModel->isPositionAvailable($positionId, $scheduleId, $bookingDate)) {
            http_response_code(409);
            echo json_encode(['error' => 'Posición no disponible']);
            exit();
        }

        // Reservar temporalmente la posición (5 minutos)
        $tempReservation = $this->roomModel->createTempReservation($positionId, $scheduleId, $bookingDate, $user['id']);

        if ($tempReservation) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Posición reservada temporalmente',
                'temp_reservation_id' => $tempReservation,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al reservar posición']);
        }
    }
}
