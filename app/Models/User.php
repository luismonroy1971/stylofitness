<?php
/**
 * Modelo User - STYLOFITNESS
 * Maneja todas las operaciones relacionadas con usuarios
 */

class User {
    
    private $db;
    private $table = 'users';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Crear un nuevo usuario
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (gym_id, username, email, password, first_name, last_name, phone, role, membership_type, membership_expires, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        // Generar username único si no se proporciona
        if (empty($data['username'])) {
            $data['username'] = $this->generateUniqueUsername($data['first_name'], $data['last_name']);
        }
        
        $params = [
            $data['gym_id'] ?? 1,
            $data['username'],
            $data['email'],
            $data['password'],
            $data['first_name'],
            $data['last_name'],
            $data['phone'] ?? null,
            $data['role'] ?? 'client',
            $data['membership_type'] ?? 'basic',
            $data['membership_expires'] ?? date('Y-m-d', strtotime('+1 month'))
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    /**
     * Buscar usuario por ID
     */
    public function findById($id) {
        $sql = "SELECT u.*, g.name as gym_name 
                FROM {$this->table} u 
                LEFT JOIN gyms g ON u.gym_id = g.id 
                WHERE u.id = ?";
        
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Buscar usuario por email
     */
    public function findByEmail($email) {
        $sql = "SELECT u.*, g.name as gym_name 
                FROM {$this->table} u 
                LEFT JOIN gyms g ON u.gym_id = g.id 
                WHERE u.email = ?";
        
        return $this->db->fetch($sql, [$email]);
    }
    
    /**
     * Buscar usuario por username
     */
    public function findByUsername($username) {
        $sql = "SELECT u.*, g.name as gym_name 
                FROM {$this->table} u 
                LEFT JOIN gyms g ON u.gym_id = g.id 
                WHERE u.username = ?";
        
        return $this->db->fetch($sql, [$username]);
    }
    
    /**
     * Verificar si existe el email
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        return $this->db->count($sql, $params) > 0;
    }
    
    /**
     * Verificar si existe el username
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        return $this->db->count($sql, $params) > 0;
    }
    
    /**
     * Actualizar usuario
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = [
            'first_name', 'last_name', 'email', 'username', 'phone', 
            'profile_image', 'membership_type', 'membership_expires', 
            'is_active', 'role', 'gym_id'
        ];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = NOW()";
        $params[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Actualizar contraseña
     */
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE {$this->table} SET password = ?, updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->db->query($sql, [$hashedPassword, $id]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Obtener usuarios con filtros
     */
    public function getUsers($filters = []) {
        $sql = "SELECT u.*, g.name as gym_name 
                FROM {$this->table} u 
                LEFT JOIN gyms g ON u.gym_id = g.id 
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por rol
        if (!empty($filters['role'])) {
            $sql .= " AND u.role = ?";
            $params[] = $filters['role'];
        }
        
        // Filtro por gym
        if (!empty($filters['gym_id'])) {
            $sql .= " AND u.gym_id = ?";
            $params[] = $filters['gym_id'];
        }
        
        // Filtro por estado activo
        if (isset($filters['is_active'])) {
            $sql .= " AND u.is_active = ?";
            $params[] = $filters['is_active'];
        }
        
        // Filtro de búsqueda
        if (!empty($filters['search'])) {
            $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR u.username LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        // Filtro por membresía expirada
        if (isset($filters['membership_expired'])) {
            if ($filters['membership_expired']) {
                $sql .= " AND u.membership_expires < CURDATE()";
            } else {
                $sql .= " AND u.membership_expires >= CURDATE()";
            }
        }
        
        // Ordenamiento
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDir = strtoupper($filters['order_dir'] ?? 'DESC');
        $sql .= " ORDER BY u.{$orderBy} {$orderDir}";
        
        // Límite y offset
        if (isset($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
            
            if (isset($filters['offset'])) {
                $sql .= " OFFSET ?";
                $params[] = (int)$filters['offset'];
            }
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Contar usuarios con filtros
     */
    public function countUsers($filters = []) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE 1=1";
        $params = [];
        
        // Aplicar los mismos filtros que en getUsers
        if (!empty($filters['role'])) {
            $sql .= " AND role = ?";
            $params[] = $filters['role'];
        }
        
        if (!empty($filters['gym_id'])) {
            $sql .= " AND gym_id = ?";
            $params[] = $filters['gym_id'];
        }
        
        if (isset($filters['is_active'])) {
            $sql .= " AND is_active = ?";
            $params[] = $filters['is_active'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR username LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (isset($filters['membership_expired'])) {
            if ($filters['membership_expired']) {
                $sql .= " AND membership_expires < CURDATE()";
            } else {
                $sql .= " AND membership_expires >= CURDATE()";
            }
        }
        
        return $this->db->count($sql, $params);
    }
    
    /**
     * Obtener clientes de un instructor
     */
    public function getInstructorClients($instructorId) {
        $sql = "SELECT DISTINCT u.*, g.name as gym_name 
                FROM {$this->table} u 
                LEFT JOIN gyms g ON u.gym_id = g.id 
                INNER JOIN routines r ON u.id = r.client_id 
                WHERE r.instructor_id = ? AND u.is_active = 1 
                ORDER BY u.first_name, u.last_name";
        
        return $this->db->fetchAll($sql, [$instructorId]);
    }
    
    /**
     * Obtener estadísticas del usuario
     */
    public function getUserStats($userId) {
        $stats = [];
        
        // Rutinas activas
        $stats['active_routines'] = $this->db->count(
            "SELECT COUNT(*) FROM routines WHERE client_id = ? AND is_active = 1",
            [$userId]
        );
        
        // Entrenamientos completados
        $stats['completed_workouts'] = $this->db->count(
            "SELECT COUNT(*) FROM workout_logs WHERE user_id = ?",
            [$userId]
        );
        
        // Clases reservadas este mes
        $stats['classes_this_month'] = $this->db->count(
            "SELECT COUNT(*) FROM class_bookings cb 
             WHERE cb.user_id = ? AND MONTH(cb.booking_date) = MONTH(CURDATE()) 
             AND YEAR(cb.booking_date) = YEAR(CURDATE())",
            [$userId]
        );
        
        // Órdenes realizadas
        $stats['total_orders'] = $this->db->count(
            "SELECT COUNT(*) FROM orders WHERE user_id = ?",
            [$userId]
        );
        
        // Dinero gastado
        $totalSpent = $this->db->fetch(
            "SELECT SUM(total_amount) as total FROM orders WHERE user_id = ? AND status = 'completed'",
            [$userId]
        );
        $stats['total_spent'] = $totalSpent['total'] ?? 0;
        
        return $stats;
    }
    
    /**
     * Activar/desactivar usuario
     */
    public function toggleStatus($id) {
        $sql = "UPDATE {$this->table} SET is_active = NOT is_active, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Eliminar usuario (soft delete)
     */
    public function delete($id) {
        // En lugar de eliminar, desactivamos
        return $this->update($id, ['is_active' => false]);
    }
    
    /**
     * Generar username único
     */
    private function generateUniqueUsername($firstName, $lastName) {
        $baseUsername = strtolower(substr($firstName, 0, 1) . $lastName);
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername);
        
        $username = $baseUsername;
        $counter = 1;
        
        while ($this->usernameExists($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
    
    /**
     * Extender membresía
     */
    public function extendMembership($userId, $days) {
        $user = $this->findById($userId);
        if (!$user) return false;
        
        $currentExpiry = strtotime($user['membership_expires']);
        $newExpiry = date('Y-m-d', $currentExpiry + ($days * 86400));
        
        return $this->update($userId, ['membership_expires' => $newExpiry]);
    }
    
    /**
     * Verificar si la membresía está activa
     */
    public function hasMembershipActive($userId) {
        $user = $this->findById($userId);
        if (!$user) return false;
        
        return strtotime($user['membership_expires']) > time();
    }
    
    /**
     * Subir imagen de perfil
     */
    public function uploadProfileImage($userId, $file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido');
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB máximo
            throw new Exception('El archivo es demasiado grande');
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
        $uploadPath = UPLOAD_PATH . '/profiles/' . $filename;
        
        // Crear directorio si no existe
        $profileDir = UPLOAD_PATH . '/profiles';
        if (!file_exists($profileDir)) {
            mkdir($profileDir, 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Comprimir imagen
            AppHelper::compressImage($uploadPath, $uploadPath, 80);
            
            // Actualizar base de datos
            $this->update($userId, ['profile_image' => '/uploads/profiles/' . $filename]);
            
            return '/uploads/profiles/' . $filename;
        }
        
        throw new Exception('Error al subir la imagen');
    }
    
    /**
     * Obtener actividad reciente del usuario
     */
    public function getRecentActivity($userId, $limit = 10) {
        $sql = "SELECT * FROM user_activity_logs 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$userId, $limit]);
    }
    
    /**
     * Registrar actividad del usuario
     */
    public function logActivity($userId, $action, $details = null) {
        $sql = "INSERT INTO user_activity_logs (user_id, action, details, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $userId,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        
        return $this->db->insert($sql, $params);
    }
}
?>