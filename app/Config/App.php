<?php
/**
 * Configuración General de la Aplicación - STYLOFITNESS
 */

// Configuración de la aplicación
define('APP_NAME', 'STYLOFITNESS');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production

// URLs y rutas
define('BASE_URL', 'http://localhost/stylofitness');
define('ASSETS_URL', BASE_URL . '/public');
define('UPLOAD_URL', BASE_URL . '/public/uploads');

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS
session_start();

// Configuración de archivos
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm', 'video/ogg']);

// Configuración de email (para notificaciones)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'noreply@stylofitness.com');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@stylofitness.com');
define('FROM_NAME', 'STYLOFITNESS');

// Colores del tema basados en el logo
define('THEME_COLORS', [
    'primary' => '#FF6B00',      // Naranja principal del logo
    'secondary' => '#E55A00',    // Naranja más oscuro
    'accent' => '#FFB366',       // Naranja claro/dorado
    'dark' => '#2C2C2C',         // Fondo oscuro
    'light' => '#F8F9FA',        // Fondo claro
    'white' => '#FFFFFF',        // Blanco puro
    'text_light' => '#CCCCCC',   // Texto secundario
    'success' => '#28A745',      // Verde éxito
    'warning' => '#FFC107',      // Amarillo advertencia
    'danger' => '#DC3545',       // Rojo peligro
    'info' => '#17A2B8'          // Azul información
]);

// Configuración de paginación
define('ITEMS_PER_PAGE', 12);
define('PRODUCTS_PER_PAGE', 16);
define('ROUTINES_PER_PAGE', 8);

// Configuración de cache
define('CACHE_ENABLED', true);
define('CACHE_TIME', 3600); // 1 hora

// Funciones auxiliares
class AppHelper {
    
    // Función para obtener URL base
    public static function baseUrl($path = '') {
        return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
    }
    
    // Función para obtener URL de assets
    public static function asset($path) {
        return ASSETS_URL . '/' . ltrim($path, '/');
    }
    
    // Función para obtener URL de uploads
    public static function uploadUrl($path) {
        return UPLOAD_URL . '/' . ltrim($path, '/');
    }
    
    // Función para sanitizar entrada
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    // Función para validar email
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    // Función para generar slug
    public static function generateSlug($text) {
        $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
        $text = preg_replace('/\s+/', '-', trim($text));
        return strtolower($text);
    }
    
    // Función para formatear precio
    public static function formatPrice($price) {
        return 'S/ ' . number_format($price, 2);
    }
    
    // Función para generar token CSRF
    public static function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    // Función para verificar token CSRF
    public static function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // Función para redireccionar
    public static function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    // Función para establecer mensaje flash
    public static function setFlashMessage($type, $message) {
        $_SESSION['flash'][$type] = $message;
    }
    
    // Función para obtener mensaje flash
    public static function getFlashMessage($type) {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }
    
    // Función para verificar si usuario está logueado
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Función para obtener usuario actual
    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return $_SESSION['user_data'] ?? null;
        }
        return null;
    }
    
    // Función para verificar rol de usuario
    public static function hasRole($role) {
        $user = self::getCurrentUser();
        return $user && $user['role'] === $role;
    }
    
    // Función para comprimir imagen
    public static function compressImage($source, $destination, $quality = 80) {
        $info = getimagesize($source);
        
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        } else {
            return false;
        }
        
        return imagejpeg($image, $destination, $quality);
    }
    
    // Función para generar thumbnail
    public static function generateThumbnail($source, $destination, $width = 300, $height = 300) {
        list($orig_width, $orig_height) = getimagesize($source);
        
        $ratio = min($width / $orig_width, $height / $orig_height);
        $new_width = $orig_width * $ratio;
        $new_height = $orig_height * $ratio;
        
        $new_image = imagecreatetruecolor($new_width, $new_height);
        
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
        } else {
            return false;
        }
        
        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
        
        return imagejpeg($new_image, $destination, 90);
    }
    
    // Función para logs
    public static function log($message, $level = 'info') {
        $logFile = ROOT_PATH . '/logs/' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    // Función para verificar permisos de archivo
    public static function checkFilePermissions($file) {
        return is_readable($file) && is_writable($file);
    }
    
    // Función para obtener información del dispositivo
    public static function isMobile() {
        return preg_match('/Mobile|Android|iPhone|iPad/', $_SERVER['HTTP_USER_AGENT'] ?? '');
    }
}

// Inicializar configuración
if (!file_exists(ROOT_PATH . '/logs')) {
    mkdir(ROOT_PATH . '/logs', 0755, true);
}

// Configurar manejo de errores
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/error.log');
}

// Configurar timezone
date_default_timezone_set('America/Lima');
?>