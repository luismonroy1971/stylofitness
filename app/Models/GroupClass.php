<?php

namespace StyleFitness\Models;

use StyleFitness\Config\Database;
use Exception;
use PDO;
use DateTime;

/**
 * Modelo de Clases Grupales - STYLOFITNESS
 * Gestión de clases grupales, horarios y reservas
 */

class GroupClass
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $sql = 'INSERT INTO group_classes (
            gym_id, instructor_id, name, description, class_type, duration_minutes,
            max_participants, room_id, room, equipment_needed, difficulty_level, price,
            image_url, requirements, benefits, is_active, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';

        return $this->db->insert($sql, [
            $data['gym_id'],
            $data['instructor_id'],
            $data['name'],
            $data['description'] ?? '',
            $data['class_type'] ?? 'cardio',
            $data['duration_minutes'] ?? 60,
            $data['max_participants'] ?? 20,
            $data['room_id'] ?? null,
            $data['room'] ?? null,
            $data['equipment_needed'] ?? null,
            $data['difficulty_level'] ?? 'intermediate',
            $data['price'] ?? 0.00,
            $data['image_url'] ?? null,
            $data['requirements'] ?? null,
            $data['benefits'] ?? null,
            $data['is_active'] ?? true,
        ]);
    }

    public function findById($id)
    {
        $class = $this->db->fetch(
            'SELECT gc.*, u.first_name, u.last_name, u.email as instructor_email,
             g.name as gym_name, r.name as room_name, r.room_type, r.total_capacity as room_capacity
             FROM group_classes gc
             LEFT JOIN users u ON gc.instructor_id = u.id
             LEFT JOIN gyms g ON gc.gym_id = g.id
             LEFT JOIN rooms r ON gc.room_id = r.id
             WHERE gc.id = ?',
            [$id]
        );

        if ($class) {
            $class['schedules'] = $this->getClassSchedules($id);
            $class['next_sessions'] = $this->getNextSessions($id, 5);
            
            // Agregar información de la sala si existe
            if ($class['room_id']) {
                $class['room_info'] = [
                    'id' => $class['room_id'],
                    'name' => $class['room_name'],
                    'type' => $class['room_type'],
                    'capacity' => $class['room_capacity']
                ];
            }
        }

        return $class;
    }

    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        $allowedFields = [
            'instructor_id', 'name', 'description', 'class_type', 'duration_minutes',
            'max_participants', 'room_id', 'room', 'equipment_needed', 'difficulty_level',
            'price', 'image_url', 'requirements', 'benefits', 'is_active',
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = 'updated_at = NOW()';
        $values[] = $id;

        $sql = 'UPDATE group_classes SET ' . implode(', ', $fields) . ' WHERE id = ?';

        return $this->db->query($sql, $values);
    }

    public function getClasses($filters = [])
    {
        $where = ['gc.is_active = 1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(gc.name LIKE ? OR gc.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        if (!empty($filters['class_type'])) {
            $where[] = 'gc.class_type = ?';
            $params[] = $filters['class_type'];
        }

        if (!empty($filters['instructor_id'])) {
            $where[] = 'gc.instructor_id = ?';
            $params[] = $filters['instructor_id'];
        }

        if (!empty($filters['gym_id'])) {
            $where[] = 'gc.gym_id = ?';
            $params[] = $filters['gym_id'];
        }

        if (!empty($filters['difficulty_level'])) {
            $where[] = 'gc.difficulty_level = ?';
            $params[] = $filters['difficulty_level'];
        }

        $sql = 'SELECT gc.*, u.first_name, u.last_name, g.name as gym_name,
                COUNT(DISTINCT cs.id) as schedule_count
                FROM group_classes gc
                LEFT JOIN users u ON gc.instructor_id = u.id
                LEFT JOIN gyms g ON gc.gym_id = g.id
                LEFT JOIN class_schedules cs ON gc.id = cs.class_id AND cs.is_active = 1
                WHERE ' . implode(' AND ', $where) . '
                GROUP BY gc.id
                ORDER BY gc.name ASC';

        if (!empty($filters['limit'])) {
            $sql .= ' LIMIT ' . (int)$filters['limit'];
            if (!empty($filters['offset'])) {
                $sql .= ' OFFSET ' . (int)$filters['offset'];
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function countClasses($filters = [])
    {
        $where = ['gc.is_active = 1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(gc.name LIKE ? OR gc.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        if (!empty($filters['class_type'])) {
            $where[] = 'gc.class_type = ?';
            $params[] = $filters['class_type'];
        }

        if (!empty($filters['instructor_id'])) {
            $where[] = 'gc.instructor_id = ?';
            $params[] = $filters['instructor_id'];
        }

        if (!empty($filters['gym_id'])) {
            $where[] = 'gc.gym_id = ?';
            $params[] = $filters['gym_id'];
        }

        $sql = 'SELECT COUNT(*) FROM group_classes gc WHERE ' . implode(' AND ', $where);

        return $this->db->count($sql, $params);
    }

    public function getUpcomingClasses($limit = 10)
    {
        return $this->db->fetchAll(
            "SELECT gc.name, gc.description, gc.duration_minutes, gc.max_participants,
             cs.day_of_week, cs.start_time, cs.end_time,
             u.first_name, u.last_name,
             COUNT(cb.id) as booked_spots,
             cs.id as schedule_id
             FROM group_classes gc
             JOIN class_schedules cs ON gc.id = cs.class_id
             JOIN users u ON gc.instructor_id = u.id
             LEFT JOIN class_bookings cb ON cs.id = cb.schedule_id 
                 AND cb.booking_date >= CURDATE() 
                 AND cb.status IN ('booked', 'confirmed')
             WHERE gc.is_active = 1 AND cs.is_active = 1
             GROUP BY gc.id, cs.id
             ORDER BY 
                 CASE cs.day_of_week 
                     WHEN 'monday' THEN 1
                     WHEN 'tuesday' THEN 2
                     WHEN 'wednesday' THEN 3
                     WHEN 'thursday' THEN 4
                     WHEN 'friday' THEN 5
                     WHEN 'saturday' THEN 6
                     WHEN 'sunday' THEN 7
                 END,
                 cs.start_time
             LIMIT ?",
            [$limit]
        );
    }

    public function getClassSchedules($classId)
    {
        return $this->db->fetchAll(
            "SELECT * FROM class_schedules 
             WHERE class_id = ? AND is_active = 1
             ORDER BY 
                 CASE day_of_week 
                     WHEN 'monday' THEN 1
                     WHEN 'tuesday' THEN 2
                     WHEN 'wednesday' THEN 3
                     WHEN 'thursday' THEN 4
                     WHEN 'friday' THEN 5
                     WHEN 'saturday' THEN 6
                     WHEN 'sunday' THEN 7
                 END,
                 start_time",
            [$classId]
        );
    }

    public function addSchedule($classId, $scheduleData)
    {
        $sql = 'INSERT INTO class_schedules (
            class_id, day_of_week, start_time, end_time, start_date, 
            end_date, is_recurring, exceptions, is_active, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';

        return $this->db->insert($sql, [
            $classId,
            $scheduleData['day_of_week'],
            $scheduleData['start_time'],
            $scheduleData['end_time'],
            $scheduleData['start_date'] ?? null,
            $scheduleData['end_date'] ?? null,
            $scheduleData['is_recurring'] ?? true,
            json_encode($scheduleData['exceptions'] ?? []),
            $scheduleData['is_active'] ?? true,
        ]);
    }

    public function updateSchedule($scheduleId, $data)
    {
        $fields = [];
        $values = [];

        $allowedFields = [
            'day_of_week', 'start_time', 'end_time', 'start_date',
            'end_date', 'is_recurring', 'exceptions', 'is_active',
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "{$key} = ?";
                if ($key === 'exceptions') {
                    $values[] = json_encode($value);
                } else {
                    $values[] = $value;
                }
            }
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $scheduleId;

        $sql = 'UPDATE class_schedules SET ' . implode(', ', $fields) . ' WHERE id = ?';

        return $this->db->query($sql, $values);
    }

    public function bookClass($userId, $scheduleId, $bookingDate)
    {
        // Verificar disponibilidad
        $schedule = $this->getScheduleById($scheduleId);
        if (!$schedule) {
            return ['error' => 'Horario no encontrado'];
        }

        $class = $this->findById($schedule['class_id']);
        if (!$class) {
            return ['error' => 'Clase no encontrada'];
        }

        // Verificar si ya está reservado
        $existingBooking = $this->db->fetch(
            "SELECT id FROM class_bookings 
             WHERE schedule_id = ? AND user_id = ? AND booking_date = ? 
             AND status IN ('booked', 'confirmed')",
            [$scheduleId, $userId, $bookingDate]
        );

        if ($existingBooking) {
            return ['error' => 'Ya tienes una reserva para esta clase'];
        }

        // Verificar capacidad
        $bookedCount = $this->db->count(
            "SELECT COUNT(*) FROM class_bookings 
             WHERE schedule_id = ? AND booking_date = ? 
             AND status IN ('booked', 'confirmed')",
            [$scheduleId, $bookingDate]
        );

        if ($bookedCount >= $class['max_participants']) {
            return ['error' => 'La clase está completa'];
        }

        // Crear reserva
        $bookingId = $this->db->insert(
            "INSERT INTO class_bookings (
                schedule_id, user_id, booking_date, status, 
                payment_status, amount_paid, booking_time, created_at
            ) VALUES (?, ?, ?, 'booked', 'free', 0, NOW(), NOW())",
            [$scheduleId, $userId, $bookingDate]
        );

        if ($bookingId) {
            return ['success' => true, 'booking_id' => $bookingId];
        } else {
            return ['error' => 'Error al crear la reserva'];
        }
    }

    public function cancelBooking($bookingId)
    {
        return $this->db->query(
            "UPDATE class_bookings 
             SET status = 'cancelled', updated_at = NOW() 
             WHERE id = ?",
            [$bookingId]
        );
    }

    /**
     * Obtener reservas de usuario con filtro por tipo
     */
    public function getUserBookings($userId, $type = 'all')
    {
        $where = 'cb.user_id = ?';
        $params = [$userId];

        switch ($type) {
            case 'upcoming':
                $where .= " AND cb.booking_date >= CURDATE() AND cb.status IN ('booked', 'confirmed')";
                break;
            case 'past':
                $where .= ' AND cb.booking_date < CURDATE()';
                break;
            case 'cancelled':
                $where .= " AND cb.status = 'cancelled'";
                break;
            default:
                // 'all' - no additional filter
                break;
        }

        return $this->db->fetchAll(
            "SELECT cb.*, gc.name as class_name, gc.description, gc.duration_minutes,
             cs.day_of_week, cs.start_time, cs.end_time,
             u.first_name as instructor_first_name, u.last_name as instructor_last_name
             FROM class_bookings cb
             JOIN class_schedules cs ON cb.schedule_id = cs.id
             JOIN group_classes gc ON cs.class_id = gc.id
             LEFT JOIN users u ON gc.instructor_id = u.id
             WHERE {$where}
             ORDER BY cb.booking_date DESC, cs.start_time DESC",
            $params
        );
    }

    public function getInstructorClasses($instructorId)
    {
        return $this->getClasses(['instructor_id' => $instructorId]);
    }

    public function getNextSessions($classId, $limit = 10)
    {
        $schedules = $this->getClassSchedules($classId);
        $sessions = [];

        foreach ($schedules as $schedule) {
            $sessions = array_merge($sessions, $this->generateSessions($schedule, $limit));
        }

        // Ordenar por fecha
        usort($sessions, function ($a, $b) {
            return strtotime($a['date'] . ' ' . $a['start_time']) - strtotime($b['date'] . ' ' . $b['start_time']);
        });

        return array_slice($sessions, 0, $limit);
    }

    public function getClassTypes()
    {
        return [
            'cardio' => 'Cardio',
            'strength' => 'Fuerza',
            'flexibility' => 'Flexibilidad',
            'dance' => 'Baile',
            'martial_arts' => 'Artes Marciales',
            'aqua' => 'Acuático',
            'yoga' => 'Yoga',
            'pilates' => 'Pilates',
            'crossfit' => 'CrossFit',
            'hiit' => 'HIIT',
            'spinning' => 'Spinning',
        ];
    }

    public function validateClass($data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        }

        if (empty($data['instructor_id'])) {
            $errors['instructor_id'] = 'El instructor es obligatorio';
        }

        if (!isset($data['duration_minutes']) || $data['duration_minutes'] <= 0) {
            $errors['duration_minutes'] = 'La duración debe ser mayor a 0';
        }

        if (!isset($data['max_participants']) || $data['max_participants'] <= 0) {
            $errors['max_participants'] = 'El número máximo de participantes debe ser mayor a 0';
        }

        return $errors;
    }

    public function delete($id)
    {
        // Verificar si hay reservas activas
        $activeBookings = $this->db->count(
            "SELECT COUNT(*) FROM class_bookings cb
             JOIN class_schedules cs ON cb.schedule_id = cs.id
             WHERE cs.class_id = ? AND cb.booking_date >= CURDATE() 
             AND cb.status IN ('booked', 'confirmed')",
            [$id]
        );

        if ($activeBookings > 0) {
            return false; // No eliminar clases con reservas activas
        }

        // Eliminar horarios
        $this->db->query('DELETE FROM class_schedules WHERE class_id = ?', [$id]);

        // Eliminar clase
        return $this->db->query('DELETE FROM group_classes WHERE id = ?', [$id]);
    }

    /**
     * Contar clases asignadas a un instructor
     */
    public function countClassesByInstructor($instructorId)
    {
        $sql = "SELECT COUNT(*) FROM group_classes WHERE instructor_id = ?";
        return $this->db->count($sql, [$instructorId]);
    }
    
    private function generateSessions($schedule, $limit)
    {
        $sessions = [];
        $currentDate = new DateTime();
        $endDate = new DateTime('+2 months'); // Generar sesiones para los próximos 2 meses

        $dayMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0,
        ];

        $targetDay = $dayMap[$schedule['day_of_week']];

        while ($currentDate <= $endDate && count($sessions) < $limit) {
            if ($currentDate->format('w') == $targetDay) {
                $sessions[] = [
                    'schedule_id' => $schedule['id'],
                    'date' => $currentDate->format('Y-m-d'),
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ];
            }
            $currentDate->modify('+1 day');
        }

        return $sessions;
    }

    // ==========================================
    // MÉTODOS ADICIONALES REQUERIDOS POR EL CONTROLADOR
    // ==========================================

    /**
     * Obtener clases con sus horarios (para index del controlador)
     */
    public function getClassesWithSchedules($filters = [])
    {
        $where = ['gc.is_active = 1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(gc.name LIKE ? OR gc.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        if (!empty($filters['class_type'])) {
            $where[] = 'gc.class_type = ?';
            $params[] = $filters['class_type'];
        }

        if (!empty($filters['difficulty'])) {
            $where[] = 'gc.difficulty_level = ?';
            $params[] = $filters['difficulty'];
        }

        if (!empty($filters['instructor'])) {
            $where[] = '(u.first_name LIKE ? OR u.last_name LIKE ?)';
            $instructorTerm = '%' . $filters['instructor'] . '%';
            $params = array_merge($params, [$instructorTerm, $instructorTerm]);
        }

        if (!empty($filters['day'])) {
            $where[] = 'cs.day_of_week = ?';
            $params[] = $filters['day'];
        }

        $sql = "SELECT gc.*, u.first_name, u.last_name, u.email as instructor_email,
                g.name as gym_name,
                GROUP_CONCAT(
                    CONCAT(cs.day_of_week, '|', cs.start_time, '|', cs.end_time, '|', cs.id) 
                    SEPARATOR ';'
                ) as schedules_data
                FROM group_classes gc
                LEFT JOIN users u ON gc.instructor_id = u.id
                LEFT JOIN gyms g ON gc.gym_id = g.id
                LEFT JOIN class_schedules cs ON gc.id = cs.class_id AND cs.is_active = 1
                WHERE " . implode(' AND ', $where) . '
                GROUP BY gc.id
                ORDER BY gc.name ASC';

        $classes = $this->db->fetchAll($sql, $params);

        // Procesar datos de horarios
        foreach ($classes as &$class) {
            $class['schedules'] = [];
            if ($class['schedules_data']) {
                $schedulesArray = explode(';', $class['schedules_data']);
                foreach ($schedulesArray as $scheduleData) {
                    $parts = explode('|', $scheduleData);
                    if (count($parts) >= 4) {
                        $class['schedules'][] = [
                            'day_of_week' => $parts[0],
                            'start_time' => $parts[1],
                            'end_time' => $parts[2],
                            'id' => $parts[3],
                        ];
                    }
                }
            }
            unset($class['schedules_data']);
        }

        return $classes;
    }

    /**
     * Obtener lista de instructores únicos
     */
    public function getInstructors()
    {
        return $this->db->fetchAll(
            "SELECT DISTINCT u.id, u.first_name, u.last_name, u.email
             FROM users u
             INNER JOIN group_classes gc ON u.id = gc.instructor_id
             WHERE u.role = 'instructor' AND u.is_active = 1 AND gc.is_active = 1
             ORDER BY u.first_name, u.last_name"
        );
    }

    /**
     * Obtener reservas de un horario en un rango de fechas
     */
    public function getScheduleBookings($scheduleId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT cb.*, u.first_name, u.last_name, u.email
             FROM class_bookings cb
             JOIN users u ON cb.user_id = u.id
             WHERE cb.schedule_id = ? AND cb.booking_date BETWEEN ? AND ?
             AND cb.status IN ('booked', 'confirmed')
             ORDER BY cb.booking_date ASC, cb.created_at ASC",
            [$scheduleId, $startDate, $endDate]
        );
    }

    /**
     * Obtener estadísticas de una clase
     */
    public function getClassStats($id)
    {
        $stats = [];

        // Total de reservas
        $stats['total_bookings'] = $this->db->count(
            'SELECT COUNT(*) FROM class_bookings cb
             JOIN class_schedules cs ON cb.schedule_id = cs.id
             WHERE cs.class_id = ?',
            [$id]
        );

        // Reservas este mes
        $stats['bookings_this_month'] = $this->db->count(
            'SELECT COUNT(*) FROM class_bookings cb
             JOIN class_schedules cs ON cb.schedule_id = cs.id
             WHERE cs.class_id = ? AND MONTH(cb.booking_date) = MONTH(CURDATE())
             AND YEAR(cb.booking_date) = YEAR(CURDATE())',
            [$id]
        );

        // Tasa de ocupación promedio
        $class = $this->findById($id);
        $avgOccupancy = $this->db->fetch(
            'SELECT AVG(booking_count) as avg_occupancy FROM (
                SELECT COUNT(*) as booking_count
                FROM class_bookings cb
                JOIN class_schedules cs ON cb.schedule_id = cs.id
                WHERE cs.class_id = ? AND cb.booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY cb.schedule_id, cb.booking_date
             ) as subquery',
            [$id]
        );

        $stats['avg_occupancy_rate'] = $class && $class['max_participants'] > 0
            ? round(($avgOccupancy['avg_occupancy'] / $class['max_participants']) * 100, 1)
            : 0;

        // Próximas sesiones disponibles
        $stats['upcoming_sessions'] = count($this->getNextSessions($id, 10));

        return $stats;
    }

    /**
     * Verificar si un usuario ya tiene reserva para una clase específica
     */
    public function hasUserBooking($scheduleId, $userId, $bookingDate)
    {
        $count = $this->db->count(
            "SELECT COUNT(*) FROM class_bookings 
             WHERE schedule_id = ? AND user_id = ? AND booking_date = ?
             AND status IN ('booked', 'confirmed')",
            [$scheduleId, $userId, $bookingDate]
        );

        return $count > 0;
    }

    /**
     * Obtener horario por ID
     */
    public function getScheduleById($scheduleId)
    {
        return $this->db->fetch(
            'SELECT cs.*, gc.name as class_name, gc.max_participants, gc.price,
             u.first_name as instructor_first_name, u.last_name as instructor_last_name
             FROM class_schedules cs
             JOIN group_classes gc ON cs.class_id = gc.id
             LEFT JOIN users u ON gc.instructor_id = u.id
             WHERE cs.id = ?',
            [$scheduleId]
        );
    }

    /**
     * Contar reservas para un horario específico en una fecha
     */
    public function countBookings($scheduleId, $bookingDate)
    {
        return $this->db->count(
            "SELECT COUNT(*) FROM class_bookings 
             WHERE schedule_id = ? AND booking_date = ?
             AND status IN ('booked', 'confirmed')",
            [$scheduleId, $bookingDate]
        );
    }

    /**
     * Crear una nueva reserva
     */
    public function createBooking($bookingData)
    {
        $sql = 'INSERT INTO class_bookings (
                schedule_id, user_id, booking_date, status, payment_status,
                amount_paid, booking_time, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())';

        return $this->db->insert($sql, [
            $bookingData['schedule_id'],
            $bookingData['user_id'],
            $bookingData['booking_date'],
            $bookingData['status'] ?? 'booked',
            $bookingData['payment_status'] ?? 'free',
            $bookingData['amount_paid'] ?? 0,
        ]);
    }

    /**
     * Obtener reserva por ID
     */
    public function getBookingById($bookingId)
    {
        return $this->db->fetch(
            'SELECT cb.*, cs.start_time, cs.end_time, cs.day_of_week,
             gc.name as class_name, gc.max_participants,
             u.first_name, u.last_name, u.email
             FROM class_bookings cb
             JOIN class_schedules cs ON cb.schedule_id = cs.id
             JOIN group_classes gc ON cs.class_id = gc.id
             JOIN users u ON cb.user_id = u.id
             WHERE cb.id = ?',
            [$bookingId]
        );
    }

    /**
     * Obtener horarios de una semana específica
     */
    public function getWeekSchedule($startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT cs.*, gc.name as class_name, gc.description, gc.duration_minutes,
             gc.max_participants, gc.difficulty_level, gc.class_type,
             u.first_name as instructor_first_name, u.last_name as instructor_last_name,
             COUNT(cb.id) as current_bookings
             FROM class_schedules cs
             JOIN group_classes gc ON cs.class_id = gc.id
             LEFT JOIN users u ON gc.instructor_id = u.id
             LEFT JOIN class_bookings cb ON cs.id = cb.schedule_id 
                 AND cb.booking_date BETWEEN ? AND ?
                 AND cb.status IN ('booked', 'confirmed')
             WHERE cs.is_active = 1 AND gc.is_active = 1
             GROUP BY cs.id
             ORDER BY 
                 CASE cs.day_of_week 
                     WHEN 'monday' THEN 1
                     WHEN 'tuesday' THEN 2
                     WHEN 'wednesday' THEN 3
                     WHEN 'thursday' THEN 4
                     WHEN 'friday' THEN 5
                     WHEN 'saturday' THEN 6
                     WHEN 'sunday' THEN 7
                 END,
                 cs.start_time",
            [$startDate, $endDate]
        );
    }

    /**
     * Verificar si un usuario está en lista de espera
     */
    public function isInWaitlist($scheduleId, $userId, $bookingDate)
    {
        $count = $this->db->count(
            "SELECT COUNT(*) FROM class_waitlist 
             WHERE schedule_id = ? AND user_id = ? AND booking_date = ?
             AND status = 'waiting'",
            [$scheduleId, $userId, $bookingDate]
        );

        return $count > 0;
    }

    /**
     * Obtener la siguiente posición en lista de espera
     */
    public function getNextWaitlistPosition($scheduleId, $bookingDate)
    {
        $maxPosition = $this->db->fetch(
            'SELECT MAX(position) as max_pos FROM class_waitlist 
             WHERE schedule_id = ? AND booking_date = ?',
            [$scheduleId, $bookingDate]
        );

        return ($maxPosition['max_pos'] ?? 0) + 1;
    }

    /**
     * Agregar usuario a lista de espera
     */
    public function addToWaitlist($waitlistData)
    {
        $sql = "INSERT INTO class_waitlist (
                schedule_id, user_id, booking_date, position, status, created_at
            ) VALUES (?, ?, ?, ?, 'waiting', NOW())";

        return $this->db->insert($sql, [
            $waitlistData['schedule_id'],
            $waitlistData['user_id'],
            $waitlistData['booking_date'],
            $waitlistData['position'],
        ]);
    }

    /**
     * Obtener clases populares basadas en reservas
     */
    public function getPopularClasses($limit = 4)
    {
        return $this->db->fetchAll(
            "SELECT gc.*, u.first_name, u.last_name, g.name as gym_name,
             COUNT(cb.id) as total_bookings
             FROM group_classes gc
             LEFT JOIN users u ON gc.instructor_id = u.id
             LEFT JOIN gyms g ON gc.gym_id = g.id
             LEFT JOIN class_schedules cs ON gc.id = cs.class_id
             LEFT JOIN class_bookings cb ON cs.id = cb.schedule_id
             WHERE gc.is_active = 1
             GROUP BY gc.id
             ORDER BY total_bookings DESC, gc.name ASC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Obtener estadísticas de clases
     */
    public function getStats()
    {
        $totalClasses = $this->db->fetch(
            'SELECT COUNT(*) as total FROM group_classes WHERE is_active = 1'
        );
        
        $totalBookings = $this->db->fetch(
            'SELECT COUNT(*) as total FROM class_bookings WHERE status IN ("booked", "confirmed")'
        );
        
        $avgParticipants = $this->db->fetch(
            'SELECT AVG(booking_count) as avg_participants
             FROM (
                 SELECT COUNT(cb.id) as booking_count
                 FROM class_schedules cs
                 LEFT JOIN class_bookings cb ON cs.id = cb.schedule_id
                 WHERE cs.is_active = 1
                 GROUP BY cs.id
             ) as subquery'
        );
        
        return [
            'total_classes' => $totalClasses['total'] ?? 0,
            'total_bookings' => $totalBookings['total'] ?? 0,
            'avg_participants' => round($avgParticipants['avg_participants'] ?? 0, 1)
        ];
    }

    // ==========================================
    // MÉTODOS PARA GESTIÓN DE SALAS Y POSICIONES
    // ==========================================

    /**
     * Reservar clase con posición específica
     */
    public function bookClassWithPosition($userId, $scheduleId, $bookingDate, $positionId = null)
    {
        // Verificar disponibilidad básica
        $bookingResult = $this->bookClass($userId, $scheduleId, $bookingDate);
        
        if (isset($bookingResult['error'])) {
            return $bookingResult;
        }

        $bookingId = $bookingResult['booking_id'];

        // Si se especifica una posición, reservarla
        if ($positionId) {
            $roomModel = new \StyleFitness\Models\Room();
            $positionResult = $roomModel->reservePosition($bookingId, $positionId);
            
            if (isset($positionResult['error'])) {
                // Cancelar la reserva de clase si no se pudo reservar la posición
                $this->cancelBooking($bookingId);
                return $positionResult;
            }

            return [
                'success' => true,
                'booking_id' => $bookingId,
                'position_reservation_id' => $positionResult['reservation_id']
            ];
        }

        return $bookingResult;
    }

    /**
     * Obtener disponibilidad de posiciones para una clase
     */
    public function getClassPositionAvailability($scheduleId, $bookingDate)
    {
        // Obtener información del horario y la clase
        $schedule = $this->getScheduleById($scheduleId);
        if (!$schedule) {
            return ['error' => 'Horario no encontrado'];
        }

        $class = $this->findById($schedule['class_id']);
        if (!$class || !$class['room_id']) {
            return ['error' => 'Clase o sala no encontrada'];
        }

        // Verificar si la sala tiene posiciones específicas
        $roomModel = new \StyleFitness\Models\Room();
        $room = $roomModel->findById($class['room_id']);
        
        if ($room['room_type'] !== 'positioned') {
            return ['error' => 'Esta sala no maneja posiciones específicas'];
        }

        // Obtener disponibilidad de posiciones
        $positions = $roomModel->getPositionAvailability($class['room_id'], $scheduleId, $bookingDate);
        
        return [
            'success' => true,
            'room_info' => $room,
            'positions' => $positions,
            'total_positions' => count($positions),
            'available_positions' => count(array_filter($positions, function($p) { return $p['is_available_for_booking']; }))
        ];
    }

    /**
     * Obtener reservas con posiciones para un horario específico
     */
    public function getScheduleBookingsWithPositions($scheduleId, $bookingDate)
    {
        return $this->db->fetchAll(
            "SELECT cb.*, u.first_name, u.last_name, u.email,
             cpb.id as position_booking_id, rp.position_number, rp.row_number, rp.seat_number
             FROM class_bookings cb
             JOIN users u ON cb.user_id = u.id
             LEFT JOIN class_position_bookings cpb ON cb.id = cpb.booking_id AND cpb.status IN ('reserved', 'confirmed')
             LEFT JOIN room_positions rp ON cpb.position_id = rp.id
             WHERE cb.schedule_id = ? AND cb.booking_date = ?
             AND cb.status IN ('booked', 'confirmed')
             ORDER BY rp.row_number, rp.seat_number, cb.created_at ASC",
            [$scheduleId, $bookingDate]
        );
    }

    /**
     * Cambiar posición de una reserva existente
     */
    public function changeBookingPosition($bookingId, $newPositionId)
    {
        $roomModel = new \StyleFitness\Models\Room();
        
        // Cancelar posición actual
        $roomModel->cancelPositionReservation($bookingId);
        
        // Reservar nueva posición
        return $roomModel->reservePosition($bookingId, $newPositionId);
    }

    /**
     * Verificar si una clase requiere selección de posición
     */
    public function requiresPositionSelection($classId)
    {
        $class = $this->findById($classId);
        return $class && $class['room_id'] && isset($class['room_info']) && $class['room_info']['type'] === 'positioned';
    }

    /**
     * Obtener clases con información de salas
     */
    public function getClassesWithRooms($filters = [])
    {
        $where = ['gc.is_active = 1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(gc.name LIKE ? OR gc.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        if (!empty($filters['class_type'])) {
            $where[] = 'gc.class_type = ?';
            $params[] = $filters['class_type'];
        }

        if (!empty($filters['room_id'])) {
            $where[] = 'gc.room_id = ?';
            $params[] = $filters['room_id'];
        }

        if (!empty($filters['gym_id'])) {
            $where[] = 'gc.gym_id = ?';
            $params[] = $filters['gym_id'];
        }

        $sql = "SELECT gc.*, u.first_name, u.last_name, u.email as instructor_email,
                g.name as gym_name, r.name as room_name, r.room_type, r.total_capacity as room_capacity,
                COUNT(DISTINCT cs.id) as schedule_count
                FROM group_classes gc
                LEFT JOIN users u ON gc.instructor_id = u.id
                LEFT JOIN gyms g ON gc.gym_id = g.id
                LEFT JOIN rooms r ON gc.room_id = r.id
                LEFT JOIN class_schedules cs ON gc.id = cs.class_id AND cs.is_active = 1
                WHERE " . implode(' AND ', $where) . '
                GROUP BY gc.id
                ORDER BY gc.name ASC';

        if (!empty($filters['limit'])) {
            $sql .= ' LIMIT ' . (int)$filters['limit'];
            if (!empty($filters['offset'])) {
                $sql .= ' OFFSET ' . (int)$filters['offset'];
            }
        }

        $classes = $this->db->fetchAll($sql, $params);

        // Agregar información de sala formateada
        foreach ($classes as &$class) {
            if ($class['room_id']) {
                $class['room_info'] = [
                    'id' => $class['room_id'],
                    'name' => $class['room_name'],
                    'type' => $class['room_type'],
                    'capacity' => $class['room_capacity']
                ];
            }
        }

        return $classes;
    }

    /**
     * Agregar usuario a lista de espera
     */
    public function addToWaitlistEnhanced($scheduleId, $userId, $bookingDate)
    {
        // Verificar si ya está en lista de espera
        if ($this->isInWaitlist($scheduleId, $userId, $bookingDate)) {
            return ['error' => 'Ya estás en la lista de espera para esta clase'];
        }

        // Obtener siguiente posición
        $position = $this->getNextWaitlistPosition($scheduleId, $bookingDate);

        // Agregar a lista de espera
        $waitlistId = $this->addToWaitlist([
            'schedule_id' => $scheduleId,
            'user_id' => $userId,
            'booking_date' => $bookingDate,
            'position' => $position
        ]);

        if ($waitlistId) {
            return [
                'success' => true,
                'waitlist_id' => $waitlistId,
                'position' => $position
            ];
        } else {
            return ['error' => 'Error al agregar a lista de espera'];
        }
    }
}
