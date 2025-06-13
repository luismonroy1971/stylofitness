<?php
/**
 * Controlador de Autenticación - STYLOFITNESS
 * Maneja login, registro y gestión de sesiones
 */

class AuthController {
    
    private $db;
    private $userModel;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->userModel = new User();
    }
    
    public function login() {
        if (AppHelper::isLoggedIn()) {
            AppHelper::redirect('/dashboard');
            return;
        }
        
        $pageTitle = 'Iniciar Sesión - STYLOFITNESS';
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/auth/login.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
    
    public function register() {
        if (AppHelper::isLoggedIn()) {
            AppHelper::redirect('/dashboard');
            return;
        }
        
        $pageTitle = 'Registrarse - STYLOFITNESS';
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/auth/register.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
    
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/login');
            return;
        }
        
        $email = AppHelper::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validación básica
        if (empty($email) || empty($password)) {
            AppHelper::setFlashMessage('error', 'Email y contraseña son obligatorios');
            AppHelper::redirect('/login');
            return;
        }
        
        // Verificar credenciales
        $user = $this->userModel->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_active']) {
                AppHelper::setFlashMessage('error', 'Tu cuenta está desactivada. Contacta al administrador.');
                AppHelper::redirect('/login');
                return;
            }
            
            // Iniciar sesión
            $this->createSession($user);
            
            // Recordar usuario si se seleccionó
            if ($remember) {
                $this->createRememberToken($user['id']);
            }
            
            // Registrar login
            $this->logUserActivity($user['id'], 'login');
            
            AppHelper::setFlashMessage('success', '¡Bienvenido de vuelta!');
            
            // Redirigir según rol
            $redirectUrl = $this->getRedirectUrl($user['role']);
            AppHelper::redirect($redirectUrl);
            
        } else {
            AppHelper::setFlashMessage('error', 'Credenciales incorrectas');
            AppHelper::redirect('/login');
        }
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/register');
            return;
        }
        
        // Validar token CSRF
        if (!AppHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            AppHelper::setFlashMessage('error', 'Token de seguridad inválido');
            AppHelper::redirect('/register');
            return;
        }
        
        $data = [
            'first_name' => AppHelper::sanitize($_POST['first_name'] ?? ''),
            'last_name' => AppHelper::sanitize($_POST['last_name'] ?? ''),
            'email' => AppHelper::sanitize($_POST['email'] ?? ''),
            'phone' => AppHelper::sanitize($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'gym_id' => 1, // Sede principal por defecto
            'role' => 'client'
        ];
        
        // Validar datos
        $errors = $this->validateRegistrationData($data);
        
        if (!empty($errors)) {
            $_SESSION['registration_errors'] = $errors;
            $_SESSION['registration_data'] = $data;
            AppHelper::redirect('/register');
            return;
        }
        
        // Verificar si el email ya existe
        if ($this->userModel->emailExists($data['email'])) {
            AppHelper::setFlashMessage('error', 'Este email ya está registrado');
            AppHelper::redirect('/register');
            return;
        }
        
        // Crear usuario
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['membership_expires'] = date('Y-m-d', strtotime('+1 month')); // Membresía de prueba
        
        $userId = $this->userModel->create($data);
        
        if ($userId) {
            // Enviar email de bienvenida
            $this->sendWelcomeEmail($data);
            
            // Iniciar sesión automáticamente
            $user = $this->userModel->findById($userId);
            $this->createSession($user);
            
            AppHelper::setFlashMessage('success', '¡Cuenta creada exitosamente! Bienvenido a STYLOFITNESS');
            AppHelper::redirect('/dashboard');
        } else {
            AppHelper::setFlashMessage('error', 'Error al crear la cuenta. Inténtalo de nuevo.');
            AppHelper::redirect('/register');
        }
    }
    
    public function logout() {
        // Eliminar remember token si existe
        if (isset($_COOKIE['remember_token'])) {
            $this->deleteRememberToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Registrar logout
        if (AppHelper::isLoggedIn()) {
            $user = AppHelper::getCurrentUser();
            $this->logUserActivity($user['id'], 'logout');
        }
        
        // Destruir sesión
        session_destroy();
        
        AppHelper::setFlashMessage('info', 'Sesión cerrada correctamente');
        AppHelper::redirect('/');
    }
    
    private function validateRegistrationData($data) {
        $errors = [];
        
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'El nombre es obligatorio';
        }
        
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'El apellido es obligatorio';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'El email es obligatorio';
        } elseif (!AppHelper::validateEmail($data['email'])) {
            $errors['email'] = 'El email no es válido';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'La contraseña es obligatoria';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Las contraseñas no coinciden';
        }
        
        if (!empty($data['phone']) && !preg_match('/^[+]?[\d\s\-\(\)]+$/', $data['phone'])) {
            $errors['phone'] = 'El teléfono no es válido';
        }
        
        return $errors;
    }
    
    private function createSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_data'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role'],
            'gym_id' => $user['gym_id'],
            'profile_image' => $user['profile_image'],
            'membership_type' => $user['membership_type'],
            'membership_expires' => $user['membership_expires']
        ];
        
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
    }
    
    private function createRememberToken($userId) {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        // Guardar token en base de datos
        $this->db->query(
            "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)",
            [$userId, $hashedToken, $expires]
        );
        
        // Establecer cookie
        setcookie('remember_token', $token, strtotime('+30 days'), '/', '', false, true);
    }
    
    private function deleteRememberToken($token) {
        $hashedToken = hash('sha256', $token);
        $this->db->query("DELETE FROM remember_tokens WHERE token = ?", [$hashedToken]);
    }
    
    private function getRedirectUrl($role) {
        switch ($role) {
            case 'admin':
                return '/admin';
            case 'instructor':
                return '/instructor/dashboard';
            case 'client':
            default:
                return '/dashboard';
        }
    }
    
    private function logUserActivity($userId, $action, $details = null) {
        $this->db->query(
            "INSERT INTO user_activity_logs (user_id, action, details, ip_address, user_agent, created_at) 
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $userId,
                $action,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]
        );
    }
    
    private function sendWelcomeEmail($userData) {
        // Implementar envío de email de bienvenida
        // Por ahora solo log
        AppHelper::log("Welcome email should be sent to: " . $userData['email']);
    }
    
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = AppHelper::sanitize($_POST['email'] ?? '');
            
            if (empty($email) || !AppHelper::validateEmail($email)) {
                AppHelper::setFlashMessage('error', 'Ingresa un email válido');
                AppHelper::redirect('/forgot-password');
                return;
            }
            
            $user = $this->userModel->findByEmail($email);
            
            if ($user) {
                // Generar token de recuperación
                $token = $this->generatePasswordResetToken($user['id']);
                
                // Enviar email (implementar)
                AppHelper::log("Password reset token for {$email}: {$token}");
                
                AppHelper::setFlashMessage('success', 'Te hemos enviado un enlace de recuperación a tu email');
            } else {
                // Por seguridad, mostrar el mismo mensaje aunque el email no exista
                AppHelper::setFlashMessage('success', 'Te hemos enviado un enlace de recuperación a tu email');
            }
            
            AppHelper::redirect('/login');
            return;
        }
        
        $pageTitle = 'Recuperar Contraseña - STYLOFITNESS';
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/auth/forgot-password.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
    
    private function generatePasswordResetToken($userId) {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Eliminar tokens anteriores
        $this->db->query("DELETE FROM password_reset_tokens WHERE user_id = ?", [$userId]);
        
        // Crear nuevo token
        $this->db->query(
            "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)",
            [$userId, $hashedToken, $expires]
        );
        
        return $token;
    }
}
?>