<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\User;
use StyleFitness\Models\Routine;
use StyleFitness\Models\Order;
use StyleFitness\Helpers\AppHelper;
use StyleFitness\Helpers\ValidationHelper;
use PDO;
use Exception;

/**
 * Controlador de Usuario - STYLOFITNESS
 * Maneja el perfil del usuario y sus datos personales
 */
class UserController
{
    private $db;
    private $userModel;
    private $routineModel;
    private $orderModel;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->userModel = new User();
        $this->routineModel = new Routine();
        $this->orderModel = new Order();
    }

    /**
     * Mostrar perfil del usuario
     */
    public function profile()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $currentUser = AppHelper::getCurrentUser();
        $userId = $currentUser['id'];

        // Obtener estadísticas del usuario
        $stats = [
            'total_routines' => 0,
            'active_routines' => 0,
            'total_workouts' => 0,
            'total_orders' => 0,
            'total_spent' => 0
        ];

        try {
            // Estadísticas de rutinas
            if ($currentUser['role'] === 'client') {
                $routines = $this->routineModel->getClientRoutines($userId);
                $stats['total_routines'] = count($routines);
                $stats['active_routines'] = count(array_filter($routines, function($r) {
                    return $r['is_active'];
                }));
            }

            // Estadísticas de órdenes si existe el modelo
            if (class_exists('StyleFitness\\Models\\Order')) {
                $orders = $this->orderModel->getUserOrders($userId);
                $stats['total_orders'] = count($orders);
                $stats['total_spent'] = array_sum(array_column($orders, 'total'));
            }
        } catch (Exception $e) {
            // Continuar sin estadísticas si hay error
        }

        $pageTitle = 'Mi Perfil - STYLOFITNESS';
        $additionalCSS = ['profile.css'];
        $additionalJS = ['profile.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/user/profile.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Actualizar perfil del usuario
     */
    public function updateProfile()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/profile');
            return;
        }

        $currentUser = AppHelper::getCurrentUser();
        $userId = $currentUser['id'];

        // Validar token CSRF
        if (!AppHelper::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            AppHelper::setFlashMessage('error', 'Token de seguridad inválido');
            AppHelper::redirect('/profile');
            return;
        }

        // Obtener y sanitizar datos
        $data = [
            'first_name' => AppHelper::sanitize($_POST['first_name'] ?? ''),
            'last_name' => AppHelper::sanitize($_POST['last_name'] ?? ''),
            'email' => AppHelper::sanitize($_POST['email'] ?? ''),
            'phone' => AppHelper::sanitize($_POST['phone'] ?? ''),
            'date_of_birth' => AppHelper::sanitize($_POST['date_of_birth'] ?? ''),
            'gender' => AppHelper::sanitize($_POST['gender'] ?? ''),
            'height' => !empty($_POST['height']) ? (float)$_POST['height'] : null,
            'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
            'fitness_goal' => AppHelper::sanitize($_POST['fitness_goal'] ?? ''),
            'experience_level' => AppHelper::sanitize($_POST['experience_level'] ?? ''),
            'medical_conditions' => AppHelper::sanitize($_POST['medical_conditions'] ?? ''),
            'emergency_contact_name' => AppHelper::sanitize($_POST['emergency_contact_name'] ?? ''),
            'emergency_contact_phone' => AppHelper::sanitize($_POST['emergency_contact_phone'] ?? '')
        ];

        // Validaciones
        $errors = [];

        if (empty($data['first_name'])) {
            $errors[] = 'El nombre es requerido';
        }

        if (empty($data['last_name'])) {
            $errors[] = 'El apellido es requerido';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email válido es requerido';
        }

        // Verificar si el email ya existe (excepto el usuario actual)
        if ($this->userModel->emailExists($data['email']) && $data['email'] !== $user['email']) {
            $errors[] = 'Este email ya está registrado';
        }

        if (!empty($errors)) {
            AppHelper::setFlashMessage('error', implode('<br>', $errors));
            AppHelper::redirect('/profile');
            return;
        }

        // Actualizar usuario
        try {
            $success = $this->userModel->update($userId, $data);
            
            if ($success) {
                // Actualizar sesión con nuevos datos
                $updatedUser = $this->userModel->findById($userId);
                $_SESSION['user_data'] = $updatedUser;
                
                AppHelper::setFlashMessage('success', 'Perfil actualizado exitosamente');
            } else {
                AppHelper::setFlashMessage('error', 'Error al actualizar el perfil');
            }
        } catch (Exception $e) {
            AppHelper::setFlashMessage('error', 'Error al actualizar el perfil: ' . $e->getMessage());
        }

        AppHelper::redirect('/profile');
    }

    /**
     * Actualizar contraseña del usuario
     */
    public function updatePassword()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/profile');
            return;
        }

        $currentUser = AppHelper::getCurrentUser();
        $userId = $currentUser['id'];

        // Validar token CSRF
        if (!AppHelper::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            AppHelper::setFlashMessage('error', 'Token de seguridad inválido');
            AppHelper::redirect('/profile');
            return;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validaciones
        $errors = [];

        if (empty($currentPassword)) {
            $errors[] = 'La contraseña actual es requerida';
        }

        if (empty($newPassword)) {
            $errors[] = 'La nueva contraseña es requerida';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'La nueva contraseña debe tener al menos 6 caracteres';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Las contraseñas no coinciden';
        }

        // Verificar contraseña actual
        if (!password_verify($currentPassword, $currentUser['password'])) {
            $errors[] = 'La contraseña actual es incorrecta';
        }

        if (!empty($errors)) {
            AppHelper::setFlashMessage('error', implode('<br>', $errors));
            AppHelper::redirect('/profile');
            return;
        }

        // Actualizar contraseña
        try {
            $success = $this->userModel->updatePassword($userId, $newPassword);
            
            if ($success) {
                AppHelper::setFlashMessage('success', 'Contraseña actualizada exitosamente');
            } else {
                AppHelper::setFlashMessage('error', 'Error al actualizar la contraseña');
            }
        } catch (Exception $e) {
            AppHelper::setFlashMessage('error', 'Error al actualizar la contraseña: ' . $e->getMessage());
        }

        AppHelper::redirect('/profile');
    }

    /**
     * Actualizar avatar del usuario
     */
    public function updateAvatar()
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/profile');
            return;
        }

        $currentUser = AppHelper::getCurrentUser();
        $userId = $currentUser['id'];

        // Validar token CSRF
        if (!AppHelper::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            AppHelper::setFlashMessage('error', 'Token de seguridad inválido');
            AppHelper::redirect('/profile');
            return;
        }

        // Verificar si se subió un archivo
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            AppHelper::setFlashMessage('error', 'Error al subir la imagen');
            AppHelper::redirect('/profile');
            return;
        }

        $file = $_FILES['avatar'];
        
        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            AppHelper::setFlashMessage('error', 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP');
            AppHelper::redirect('/profile');
            return;
        }

        // Validar tamaño (máximo 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            AppHelper::setFlashMessage('error', 'El archivo es demasiado grande. Máximo 5MB');
            AppHelper::redirect('/profile');
            return;
        }

        // Crear directorio si no existe
        $uploadDir = PUBLIC_PATH . '/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generar nombre único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Eliminar avatar anterior si existe
            if (!empty($currentUser['profile_image']) && file_exists(PUBLIC_PATH . $currentUser['profile_image'])) {
                unlink(PUBLIC_PATH . $currentUser['profile_image']);
            }

            // Actualizar base de datos
            $avatarPath = '/uploads/avatars/' . $filename;
            $success = $this->userModel->update($userId, ['profile_image' => $avatarPath]);
            
            if ($success) {
                // Actualizar sesión
                $_SESSION['user_data']['profile_image'] = $avatarPath;
                AppHelper::setFlashMessage('success', 'Avatar actualizado exitosamente');
            } else {
                AppHelper::setFlashMessage('error', 'Error al guardar el avatar en la base de datos');
            }
        } else {
            AppHelper::setFlashMessage('error', 'Error al guardar el archivo');
        }

        AppHelper::redirect('/profile');
    }

    /**
     * Mostrar las rutinas del usuario
     */
    public function myRoutines() {
        // Verificar autenticación
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
        }

        $currentUser = AppHelper::getCurrentUser();
        
        try {
            // Obtener rutinas del usuario desde la base de datos
            $db = Database::getInstance();
            
            $query = "
                SELECT 
                    r.*,
                    u.name as instructor_name,
                    COUNT(re.id) as exercise_count,
                    SUM(re.sets) as total_sets,
                    COALESCE(AVG(wr.completion_percentage), 0) as completion_percentage
                FROM routines r
                LEFT JOIN users u ON r.instructor_id = u.id
                LEFT JOIN routine_exercises re ON r.id = re.routine_id
                LEFT JOIN workout_records wr ON r.id = wr.routine_id AND wr.user_id = ?
                WHERE r.user_id = ? OR r.id IN (
                    SELECT routine_id FROM user_routines WHERE user_id = ?
                )
                GROUP BY r.id
                ORDER BY r.created_at DESC
            ";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$currentUser['id'], $currentUser['id'], $currentUser['id']]);
            $routines = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener rutinas del usuario: " . $e->getMessage());
            $routines = [];
        }
        
        $pageTitle = 'Mis Rutinas - STYLOFITNESS';
        $additionalCSS = ['routine-styles.css'];
        $additionalJS = [];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/user/my-routines.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    /**
     * Mostrar los pedidos del usuario
     */
    public function myOrders() {
        // Verificar autenticación
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
        }

        $currentUser = AppHelper::getCurrentUser();
        
        try {
            // Obtener pedidos del usuario desde la base de datos
            $db = Database::getInstance();
            
            // Obtener pedidos con sus items
            $query = "
                SELECT 
                    o.*,
                    GROUP_CONCAT(
                        CONCAT(
                            oi.product_name, '|',
                            oi.quantity, '|',
                            oi.price, '|',
                            COALESCE(p.image_url, ''), '|',
                            COALESCE(p.description, '')
                        ) SEPARATOR ';;'
                    ) as items_data
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC
            ";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$currentUser['id']]);
            $ordersData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar los datos de los items
            $orders = [];
            foreach ($ordersData as $order) {
                $items = [];
                if (!empty($order['items_data'])) {
                    $itemsArray = explode(';;', $order['items_data']);
                    foreach ($itemsArray as $itemData) {
                        $itemParts = explode('|', $itemData);
                        if (count($itemParts) >= 5) {
                            $items[] = [
                                'product_name' => $itemParts[0],
                                'quantity' => (int)$itemParts[1],
                                'price' => (float)$itemParts[2],
                                'product_image' => $itemParts[3],
                                'product_description' => $itemParts[4]
                            ];
                        }
                    }
                }
                
                $order['items'] = $items;
                unset($order['items_data']);
                $orders[] = $order;
            }
            
        } catch (Exception $e) {
            error_log("Error al obtener pedidos del usuario: " . $e->getMessage());
            $orders = [];
        }
        
        $pageTitle = 'Mis Pedidos - STYLOFITNESS';
        $additionalCSS = [];
        $additionalJS = [];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/user/my-orders.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
}