<?php

namespace StyleFitness\Models;

use StyleFitness\Config\Database;
use Exception;
use PDO;

/**
 * Modelo de Salas - STYLOFITNESS
 * Gestión de salas, posiciones específicas y control de aforo
 */
class Room
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Crear una nueva sala
     */
    public function create($data)
    {
        $sql = 'INSERT INTO rooms (
            gym_id, name, description, room_type, total_capacity,
            floor_plan_image, equipment_available, amenities, dimensions,
            location_notes, is_active, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';

        return $this->db->insert($sql, [
            $data['gym_id'],
            $data['name'],
            $data['description'] ?? '',
            $data['room_type'] ?? 'capacity_only',
            $data['total_capacity'],
            $data['floor_plan_image'] ?? null,
            json_encode($data['equipment_available'] ?? []),
            json_encode($data['amenities'] ?? []),
            $data['dimensions'] ?? null,
            $data['location_notes'] ?? null,
            $data['is_active'] ?? true,
        ]);
    }

    /**
     * Obtener sala por ID
     */
    public function findById($id)
    {
        $room = $this->db->fetch(
            'SELECT r.*, g.name as gym_name
             FROM rooms r
             LEFT JOIN gyms g ON r.gym_id = g.id
             WHERE r.id = ?',
            [$id]
        );

        if ($room) {
            // Decodificar JSON
            $room['equipment_available'] = json_decode($room['equipment_available'], true) ?? [];
            $room['amenities'] = json_decode($room['amenities'], true) ?? [];
            
            // Obtener posiciones si es una sala con posiciones específicas
            if ($room['room_type'] === 'positioned') {
                $room['positions'] = $this->getRoomPositions($id);
            }
        }

        return $room;
    }

    /**
     * Actualizar sala
     */
    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        $allowedFields = [
            'name', 'description', 'room_type', 'total_capacity',
            'floor_plan_image', 'equipment_available', 'amenities',
            'dimensions', 'location_notes', 'is_active',
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "{$key} = ?";
                if (in_array($key, ['equipment_available', 'amenities'])) {
                    $values[] = json_encode($value);
                } else {
                    $values[] = $value;
                }
            }
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = 'updated_at = NOW()';
        $values[] = $id;

        $sql = 'UPDATE rooms SET ' . implode(', ', $fields) . ' WHERE id = ?';

        return $this->db->query($sql, $values);
    }

    /**
     * Obtener salas con filtros
     */
    public function getRooms($filters = [])
    {
        $where = ['r.is_active = 1'];
        $params = [];

        if (!empty($filters['gym_id'])) {
            $where[] = 'r.gym_id = ?';
            $params[] = $filters['gym_id'];
        }

        if (!empty($filters['room_type'])) {
            $where[] = 'r.room_type = ?';
            $params[] = $filters['room_type'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(r.name LIKE ? OR r.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        $sql = 'SELECT r.*, g.name as gym_name,
                COUNT(DISTINCT gc.id) as classes_count
                FROM rooms r
                LEFT JOIN gyms g ON r.gym_id = g.id
                LEFT JOIN group_classes gc ON r.id = gc.room_id AND gc.is_active = 1
                WHERE ' . implode(' AND ', $where) . '
                GROUP BY r.id
                ORDER BY r.name ASC';

        if (!empty($filters['limit'])) {
            $sql .= ' LIMIT ' . (int)$filters['limit'];
            if (!empty($filters['offset'])) {
                $sql .= ' OFFSET ' . (int)$filters['offset'];
            }
        }

        $rooms = $this->db->fetchAll($sql, $params);

        // Decodificar JSON para cada sala
        foreach ($rooms as &$room) {
            $room['equipment_available'] = json_decode($room['equipment_available'], true) ?? [];
            $room['amenities'] = json_decode($room['amenities'], true) ?? [];
        }

        return $rooms;
    }

    /**
     * Contar salas
     */
    public function countRooms($filters = [])
    {
        $where = ['r.is_active = 1'];
        $params = [];

        if (!empty($filters['gym_id'])) {
            $where[] = 'r.gym_id = ?';
            $params[] = $filters['gym_id'];
        }

        if (!empty($filters['room_type'])) {
            $where[] = 'r.room_type = ?';
            $params[] = $filters['room_type'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(r.name LIKE ? OR r.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        $sql = 'SELECT COUNT(*) FROM rooms r WHERE ' . implode(' AND ', $where);

        return $this->db->count($sql, $params);
    }

    /**
     * Obtener posiciones de una sala
     */
    public function getRoomPositions($roomId, $includeAvailability = false)
    {
        $sql = 'SELECT * FROM room_positions WHERE room_id = ? AND is_available = 1 ORDER BY row_number, seat_number';
        $positions = $this->db->fetchAll($sql, [$roomId]);

        if ($includeAvailability) {
            // Agregar información de disponibilidad para cada posición
            foreach ($positions as &$position) {
                $position['is_occupied'] = false; // Se actualizará según las reservas
            }
        }

        return $positions;
    }

    /**
     * Crear posición en sala
     */
    public function createPosition($data)
    {
        $sql = 'INSERT INTO room_positions (
            room_id, position_number, row_number, seat_number,
            x_coordinate, y_coordinate, position_type, is_available,
            notes, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';

        return $this->db->insert($sql, [
            $data['room_id'],
            $data['position_number'],
            $data['row_number'] ?? null,
            $data['seat_number'] ?? null,
            $data['x_coordinate'] ?? null,
            $data['y_coordinate'] ?? null,
            $data['position_type'] ?? 'regular',
            $data['is_available'] ?? true,
            $data['notes'] ?? null,
        ]);
    }

    /**
     * Actualizar posición
     */
    public function updatePosition($id, $data)
    {
        $fields = [];
        $values = [];

        $allowedFields = [
            'position_number', 'row_number', 'seat_number',
            'x_coordinate', 'y_coordinate', 'position_type',
            'is_available', 'notes',
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

        $sql = 'UPDATE room_positions SET ' . implode(', ', $fields) . ' WHERE id = ?';

        return $this->db->query($sql, $values);
    }

    /**
     * Eliminar posición
     */
    public function deletePosition($id)
    {
        return $this->db->query('DELETE FROM room_positions WHERE id = ?', [$id]);
    }

    /**
     * Obtener disponibilidad de posiciones para una clase específica
     */
    public function getPositionAvailability($roomId, $scheduleId, $bookingDate)
    {
        $positions = $this->getRoomPositions($roomId);
        
        // Obtener posiciones ocupadas
        $occupiedPositions = $this->db->fetchAll(
            'SELECT rp.id as position_id, rp.position_number
             FROM room_positions rp
             INNER JOIN class_position_bookings cpb ON rp.id = cpb.position_id
             INNER JOIN class_bookings cb ON cpb.booking_id = cb.id
             WHERE rp.room_id = ? AND cb.schedule_id = ? AND cb.booking_date = ?
             AND cb.status IN ("booked", "confirmed") AND cpb.status IN ("reserved", "confirmed")',
            [$roomId, $scheduleId, $bookingDate]
        );

        $occupiedIds = array_column($occupiedPositions, 'position_id');

        // Marcar posiciones como ocupadas
        foreach ($positions as &$position) {
            $position['is_occupied'] = in_array($position['id'], $occupiedIds);
            $position['is_available_for_booking'] = !$position['is_occupied'] && $position['is_available'];
        }

        return $positions;
    }

    /**
     * Reservar posición específica
     */
    public function reservePosition($bookingId, $positionId)
    {
        // Verificar que la posición esté disponible
        $position = $this->db->fetch(
            'SELECT * FROM room_positions WHERE id = ? AND is_available = 1',
            [$positionId]
        );

        if (!$position) {
            return ['error' => 'Posición no disponible'];
        }

        // Verificar que no esté ya reservada
        $existingReservation = $this->db->fetch(
            'SELECT cpb.id FROM class_position_bookings cpb
             INNER JOIN class_bookings cb ON cpb.booking_id = cb.id
             WHERE cpb.position_id = ? AND cb.booking_date = (
                 SELECT booking_date FROM class_bookings WHERE id = ?
             ) AND cpb.status IN ("reserved", "confirmed")',
            [$positionId, $bookingId]
        );

        if ($existingReservation) {
            return ['error' => 'Posición ya reservada'];
        }

        // Crear reserva de posición
        $reservationId = $this->db->insert(
            'INSERT INTO class_position_bookings (
                booking_id, position_id, status, reserved_at
            ) VALUES (?, ?, "reserved", NOW())',
            [$bookingId, $positionId]
        );

        if ($reservationId) {
            return ['success' => true, 'reservation_id' => $reservationId];
        } else {
            return ['error' => 'Error al reservar posición'];
        }
    }

    /**
     * Cancelar reserva de posición
     */
    public function cancelPositionReservation($bookingId, $positionId = null)
    {
        $sql = 'UPDATE class_position_bookings 
                SET status = "cancelled", cancelled_at = NOW() 
                WHERE booking_id = ?';
        $params = [$bookingId];

        if ($positionId) {
            $sql .= ' AND position_id = ?';
            $params[] = $positionId;
        }

        return $this->db->query($sql, $params);
    }

    /**
     * Obtener estadísticas de una sala
     */
    public function getRoomStats($id)
    {
        $stats = [];

        // Capacidad total
        $room = $this->findById($id);
        $stats['total_capacity'] = $room['total_capacity'] ?? 0;
        $stats['room_type'] = $room['room_type'] ?? 'capacity_only';

        // Clases programadas
        $stats['scheduled_classes'] = $this->db->count(
            'SELECT COUNT(*) FROM group_classes WHERE room_id = ? AND is_active = 1',
            [$id]
        );

        // Reservas este mes
        $stats['bookings_this_month'] = $this->db->count(
            'SELECT COUNT(*) FROM class_bookings cb
             INNER JOIN class_schedules cs ON cb.schedule_id = cs.id
             INNER JOIN group_classes gc ON cs.class_id = gc.id
             WHERE gc.room_id = ? AND MONTH(cb.booking_date) = MONTH(CURDATE())
             AND YEAR(cb.booking_date) = YEAR(CURDATE())
             AND cb.status IN ("booked", "confirmed")',
            [$id]
        );

        // Tasa de ocupación promedio
        $avgOccupancy = $this->db->fetch(
            'SELECT AVG(booking_count) as avg_occupancy FROM (
                SELECT COUNT(*) as booking_count
                FROM class_bookings cb
                INNER JOIN class_schedules cs ON cb.schedule_id = cs.id
                INNER JOIN group_classes gc ON cs.class_id = gc.id
                WHERE gc.room_id = ? AND cb.booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                AND cb.status IN ("booked", "confirmed")
                GROUP BY cb.schedule_id, cb.booking_date
             ) as subquery',
            [$id]
        );

        $stats['avg_occupancy_rate'] = $stats['total_capacity'] > 0
            ? round(($avgOccupancy['avg_occupancy'] / $stats['total_capacity']) * 100, 1)
            : 0;

        return $stats;
    }

    /**
     * Eliminar sala
     */
    public function delete($id)
    {
        // Verificar si hay clases activas
        $activeClasses = $this->db->count(
            'SELECT COUNT(*) FROM group_classes WHERE room_id = ? AND is_active = 1',
            [$id]
        );

        if ($activeClasses > 0) {
            return false; // No eliminar salas con clases activas
        }

        // Eliminar posiciones
        $this->db->query('DELETE FROM room_positions WHERE room_id = ?', [$id]);

        // Eliminar sala
        return $this->db->query('DELETE FROM rooms WHERE id = ?', [$id]);
    }

    /**
     * Validar datos de sala
     */
    public function validateRoom($data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        }

        if (empty($data['gym_id'])) {
            $errors['gym_id'] = 'El gimnasio es obligatorio';
        }

        if (!isset($data['total_capacity']) || $data['total_capacity'] <= 0) {
            $errors['total_capacity'] = 'La capacidad debe ser mayor a 0';
        }

        if (!in_array($data['room_type'] ?? '', ['positioned', 'capacity_only'])) {
            $errors['room_type'] = 'Tipo de sala inválido';
        }

        return $errors;
    }

    /**
     * Obtener tipos de sala disponibles
     */
    public function getRoomTypes()
    {
        return [
            'positioned' => 'Con Posiciones Específicas',
            'capacity_only' => 'Solo Control de Aforo',
        ];
    }

    /**
     * Obtener tipos de posición disponibles
     */
    public function getPositionTypes()
    {
        return [
            'regular' => 'Regular',
            'premium' => 'Premium',
            'accessible' => 'Accesible',
            'restricted' => 'Restringida',
        ];
    }

    /**
     * Obtener salas por gimnasio
     */
    public function getRoomsByGym($gymId)
    {
        return $this->getRooms(['gym_id' => $gymId]);
    }

    /**
     * Verificar disponibilidad de sala para una fecha y horario
     */
    public function checkRoomAvailability($roomId, $date, $startTime, $endTime, $excludeClassId = null)
    {
        $sql = 'SELECT COUNT(*) FROM group_classes gc
                INNER JOIN class_schedules cs ON gc.id = cs.class_id
                WHERE gc.room_id = ? AND cs.is_active = 1 AND gc.is_active = 1
                AND (
                    (cs.start_time <= ? AND cs.end_time > ?) OR
                    (cs.start_time < ? AND cs.end_time >= ?) OR
                    (cs.start_time >= ? AND cs.end_time <= ?)
                )';
        
        $params = [$roomId, $startTime, $startTime, $endTime, $endTime, $startTime, $endTime];
        
        if ($excludeClassId) {
            $sql .= ' AND gc.id != ?';
            $params[] = $excludeClassId;
        }

        $conflicts = $this->db->count($sql, $params);
        
        return $conflicts === 0;
    }

    /**
     * Obtener posiciones disponibles para una clase específica
     */
    public function getAvailablePositions($roomId, $scheduleId = null, $bookingDate = null)
    {
        $sql = 'SELECT rp.* FROM room_positions rp 
                WHERE rp.room_id = ? AND rp.is_available = 1';
        $params = [$roomId];

        if ($scheduleId && $bookingDate) {
            $sql .= ' AND rp.id NOT IN (
                        SELECT cpb.position_id 
                        FROM class_position_bookings cpb 
                        INNER JOIN class_bookings cb ON cpb.booking_id = cb.id
                        WHERE cb.schedule_id = ? AND cb.booking_date = ? AND cpb.status IN ("reserved", "confirmed")
                    )';
            $params[] = $scheduleId;
            $params[] = $bookingDate;
        }

        $sql .= ' ORDER BY rp.row_number, rp.seat_number';
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Obtener layout completo de la sala
     */
    public function getRoomLayout($roomId)
    {
        $room = $this->findById($roomId);
        if (!$room) {
            return null;
        }

        $positions = $this->getRoomPositions($roomId);
        
        return [
            'room' => $room,
            'positions' => $positions,
            'layout_type' => $room['room_type'],
        ];
    }

    /**
     * Obtener posiciones ocupadas para una clase específica
     */
    public function getOccupiedPositions($scheduleId, $bookingDate)
    {
        $sql = 'SELECT cpb.position_id, rp.row_number, rp.seat_number, rp.x_coordinate, rp.y_coordinate
                FROM class_position_bookings cpb
                INNER JOIN room_positions rp ON cpb.position_id = rp.id
                INNER JOIN class_bookings cb ON cpb.booking_id = cb.id
                WHERE cb.schedule_id = ? AND cb.booking_date = ? AND cpb.status IN ("reserved", "confirmed")';
        
        return $this->db->fetchAll($sql, [$scheduleId, $bookingDate]);
    }

    /**
     * Verificar si una posición específica está disponible
     */
    public function isPositionAvailable($positionId, $scheduleId, $bookingDate)
    {
        $sql = 'SELECT COUNT(*) FROM class_position_bookings cpb
                INNER JOIN class_bookings cb ON cpb.booking_id = cb.id
                WHERE cpb.position_id = ? AND cb.schedule_id = ? AND cb.booking_date = ? 
                AND cpb.status IN ("reserved", "confirmed")';
        
        $count = $this->db->count($sql, [$positionId, $scheduleId, $bookingDate]);
        return $count == 0;
    }

    /**
     * Crear reserva temporal de posición
     */
    public function createTempReservation($positionId, $scheduleId, $bookingDate, $userId)
    {
        // Primero eliminar reservas temporales expiradas
        $this->cleanExpiredTempReservations();
        
        // Verificar si la posición sigue disponible
        if (!$this->isPositionAvailable($positionId, $scheduleId, $bookingDate)) {
            return false;
        }
        
        $sql = 'INSERT INTO temp_position_reservations (position_id, schedule_id, booking_date, user_id, expires_at, created_at)
                VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE), NOW())';
        
        return $this->db->insert($sql, [$positionId, $scheduleId, $bookingDate, $userId]);
    }

    /**
     * Limpiar reservas temporales expiradas
     */
    public function cleanExpiredTempReservations()
    {
        $sql = 'DELETE FROM temp_position_reservations WHERE expires_at < NOW()';
        return $this->db->query($sql, []);
    }

    /**
     * Confirmar reserva temporal
     */
    public function confirmTempReservation($tempReservationId, $bookingId)
    {
        $sql = 'SELECT * FROM temp_position_reservations WHERE id = ? AND expires_at > NOW()';
        $tempReservation = $this->db->fetch($sql, [$tempReservationId]);
        
        if (!$tempReservation) {
            return false;
        }
        
        // Crear la reserva definitiva
        $sql = 'INSERT INTO class_position_bookings (booking_id, position_id, status, reserved_at)
                VALUES (?, ?, "reserved", NOW())';
        
        $success = $this->db->insert($sql, [
            $bookingId,
            $tempReservation['position_id']
        ]);
        
        if ($success) {
            // Eliminar la reserva temporal
            $this->db->query('DELETE FROM temp_position_reservations WHERE id = ?', [$tempReservationId]);
            return true;
        }
        
        return false;
    }
}