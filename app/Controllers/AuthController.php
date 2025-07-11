<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\User;
use StyleFitness\Helpers\AppHelper;
use StyleFitness\Helpers\ValidationHelper;
use Exception;

/**
 * Controlador de Autenticación - STYLOFITNESS
 * Maneja login, registro y gestión de sesiones
 */

class AuthController
{
    private $db;
    private $userModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->userModel = new User();
    }

    public function login()
    {
        if (AppHelper::isLoggedIn()) {
            AppHelper::redirect('/dashboard');
            return;
        }

        $pageTitle = 'Iniciar Sesión - STYLOFITNESS';
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/auth/login.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function register()
    {
        // Verificar que el usuario esté logueado y sea staff o admin
        if (!AppHelper::isLoggedIn()) {
            AppHelper::setFlashMessage('error', 'Debes iniciar sesión para acceder a esta página');
            AppHelper::redirect('/login');
            return;
        }

        $currentUser = AppHelper::getCurrentUser();
        if (!in_array($currentUser['role'], ['admin', 'instructor', 'staff'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para registrar nuevos usuarios');
            AppHelper::redirect('/dashboard');
            return;
        }

        $pageTitle = 'Registrar Usuario - STYLOFITNESS';
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/auth/register.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/login');
            return;
        }

        $identifier = AppHelper::sanitize($_POST['email'] ?? ''); // Puede ser email, username o DNI
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validación básica
        if (empty($identifier) || empty($password)) {
            AppHelper::setFlashMessage('Usuario/Email y contraseña son obligatorios', 'error');
            AppHelper::redirect('/login');
            return;
        }

        // Verificar credenciales (buscar por email o username)
        $user = $this->userModel->findByEmailOrUsername($identifier);

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_active']) {
                AppHelper::setFlashMessage('Tu cuenta está desactivada. Contacta al administrador.', 'error');
                AppHelper::redirect('/login');
                return;
            }

            try {
                // Debug: Log inicio del proceso de autenticación
                error_log("AuthController: Starting authentication process for user: {$user['email']}");
                
                // Iniciar sesión
                $this->createSession($user);
                error_log("AuthController: Session created successfully");

                // Recordar usuario si se seleccionó
                if ($remember) {
                    $this->createRememberToken($user['id']);
                    error_log("AuthController: Remember token created");
                }

                // Registrar login
                $this->logUserActivity($user['id'], 'login');
                error_log("AuthController: User activity logged");

                // Establecer mensaje de éxito
                AppHelper::setFlashMessage('¡Bienvenido de vuelta!', 'success');
                error_log("AuthController: Flash message set");

                // Obtener URL de redirección
                $redirectUrl = $this->getRedirectUrl($user['role']);
                error_log("AuthController: Redirect URL determined: {$redirectUrl} for role: {$user['role']}");
                
                // Verificar que la sesión se creó correctamente
                if (!AppHelper::isLoggedIn()) {
                    error_log("AuthController: ERROR - Session not properly created!");
                    AppHelper::setFlashMessage('Error en el sistema de autenticación', 'error');
                    AppHelper::redirect('/login');
                    return;
                }
                
                // Debug: Verificar estado antes de redirección
                error_log("AuthController: User logged in status: " . (AppHelper::isLoggedIn() ? 'true' : 'false'));
                error_log("AuthController: Session user_id: " . ($_SESSION['user_id'] ?? 'not set'));
                
                // Asegurar que no hay output antes de la redirección
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                // Verificar si ya se enviaron headers
                if (headers_sent($file, $line)) {
                    error_log("AuthController: Headers already sent in {$file} on line {$line}. Cannot redirect normally.");
                    // Usar redirección JavaScript como fallback
                    echo "<script>window.location.href = '{$redirectUrl}';</script>";
                    echo "<noscript><meta http-equiv='refresh' content='0;url={$redirectUrl}'></noscript>";
                    exit();
                }
                
                error_log("AuthController: About to redirect to: {$redirectUrl}");
                AppHelper::redirect($redirectUrl);
                
            } catch (Exception $e) {
                error_log("AuthController: Exception during authentication: " . $e->getMessage());
                error_log("AuthController: Exception trace: " . $e->getTraceAsString());
                AppHelper::setFlashMessage('Error interno durante la autenticación', 'error');
                AppHelper::redirect('/login');
            }

        } else {
            AppHelper::setFlashMessage('Credenciales incorrectas', 'error');
            AppHelper::redirect('/login');
        }
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/admin/register');
            return;
        }

        // Verificar que el usuario esté logueado y sea staff o admin
        if (!AppHelper::isLoggedIn()) {
            AppHelper::setFlashMessage('error', 'Debes iniciar sesión para realizar esta acción');
            AppHelper::redirect('/login');
            return;
        }

        $currentUser = AppHelper::getCurrentUser();
        if (!in_array($currentUser['role'], ['admin', 'instructor', 'staff'])) {
            AppHelper::setFlashMessage('error', 'No tienes permisos para registrar nuevos usuarios');
            AppHelper::redirect('/dashboard');
            return;
        }

        // Validar token CSRF
        if (!AppHelper::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            AppHelper::setFlashMessage('error', 'Token de seguridad inválido');
            AppHelper::redirect('/admin/register');
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
            'role' => 'client',
        ];

        // Validar datos
        $errors = $this->validateRegistrationData($data);

        if (!empty($errors)) {
            $_SESSION['registration_errors'] = $errors;
            $_SESSION['registration_data'] = $data;
            AppHelper::redirect('/admin/register');
            return;
        }

        // Verificar si el email ya existe
        if ($this->userModel->emailExists($data['email'])) {
            AppHelper::setFlashMessage('error', 'Este email ya está registrado');
            AppHelper::redirect('/admin/register');
            return;
        }

        // Crear usuario
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['membership_expires'] = date('Y-m-d', strtotime('+1 month')); // Membresía de prueba

        $userId = $this->userModel->create($data);

        if ($userId) {
            // Enviar email de bienvenida
            $this->sendWelcomeEmail($data);

            // No iniciar sesión automáticamente cuando es creado por staff/admin
            AppHelper::setFlashMessage('success', '¡Usuario creado exitosamente!');
            AppHelper::redirect('/admin/register');
        } else {
            AppHelper::setFlashMessage('error', 'Error al crear la cuenta. Inténtalo de nuevo.');
            AppHelper::redirect('/admin/register');
        }
    }

    public function logout()
    {
        // Registrar logout antes de destruir la sesión
        if (AppHelper::isLoggedIn()) {
            $user = AppHelper::getCurrentUser();
            $this->logUserActivity($user['id'], 'logout');
        }

        // Eliminar remember token si existe
        if (isset($_COOKIE['remember_token'])) {
            $this->deleteRememberToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }

        // Usar AppHelper::logout() que maneja correctamente la sesión
        AppHelper::logout();
        
        AppHelper::setFlashMessage('info', 'Sesión cerrada correctamente');
        AppHelper::redirect('/');
    }

    private function validateRegistrationData($data)
    {
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

    private function createSession($user)
    {
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
            'membership_expires' => $user['membership_expires'],
        ];

        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
    }

    private function createRememberToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

        // Guardar token en base de datos
        $this->db->query(
            'INSERT INTO security_tokens (user_id, token, type, expires_at) VALUES (?, ?, ?, ?)',
            [$userId, $hashedToken, 'remember_me', $expires]
        );

        // Establecer cookie
        setcookie('remember_token', $token, strtotime('+30 days'), '/', '', false, true);
    }

    private function deleteRememberToken($token)
    {
        $hashedToken = hash('sha256', $token);
        $this->db->query('DELETE FROM security_tokens WHERE token = ? AND type = ?', [$hashedToken, 'remember_me']);
    }

    private function getRedirectUrl($role)
    {
        switch ($role) {
            case 'admin':
                return '/admin/dashboard';
            case 'instructor':
            case 'trainer':
                return '/trainer/progress/dashboard';
            case 'staff':
                return '/dashboard';
            case 'client':
            default:
                return '/dashboard';
        }
    }

    private function logUserActivity($userId, $action, $details = null)
    {
        $this->db->query(
            'INSERT INTO user_activity_logs (user_id, action, details, ip_address, user_agent, created_at) 
             VALUES (?, ?, ?, ?, ?, NOW())',
            [
                $userId,
                $action,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
            ]
        );
    }

    private function sendWelcomeEmail($userData)
    {
        // Implementar envío de email de bienvenida
        // Por ahora solo log
        error_log('Welcome email should be sent to: ' . $userData['email']);
    }

    public function forgotPassword()
    {
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
                error_log("Password reset token for {$email}: {$token}");

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

    private function generatePasswordResetToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Eliminar tokens anteriores
        $this->db->query('DELETE FROM security_tokens WHERE user_id = ? AND type = ?', [$userId, 'password_reset']);

        // Crear nuevo token
        $this->db->query(
            'INSERT INTO security_tokens (user_id, token, type, expires_at) VALUES (?, ?, ?, ?)',
            [$userId, $hashedToken, 'password_reset', $expires]
        );

        return $token;
    }
}
