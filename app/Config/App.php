<?php

/**
 * Configuración General de la Aplicación - STYLOFITNESS
 */

// Define ROOT_PATH if not already defined (for standalone usage)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)));
}

// Configuración de la aplicación
define('APP_NAME', 'STYLOFITNESS');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production

// URLs y rutas
define('BASE_URL', 'http://localhost:8000');
define('ASSETS_URL', BASE_URL);
define('UPLOAD_URL', BASE_URL . '/uploads');

// Configuración de sesiones
// Solo configurar si la sesión no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS
    session_start();
}

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
if (!defined('THEME_COLORS')) {
    define('THEME_COLORS', [
        'primary' => '#FF6B00',      // Naranja vibrante del logo
        'secondary' => '#E55A00',    // Naranja más oscuro
        'accent' => '#FF8533',       // Naranja intermedio
        'metallic' => '#8A8A8A',     // Gris metálico del logo
        'dark' => '#2A2A2A',         // Fondo oscuro refinado
        'light' => '#F5F5F5',        // Fondo claro suave
        'white' => '#FFFFFF',        // Blanco puro
        'text_light' => '#B8B8B8',   // Texto secundario
        'text_dark' => '#2A2A2A',    // Texto principal
        'success' => '#28A745',      // Verde éxito
        'warning' => '#FFC107',      // Amarillo advertencia
        'danger' => '#DC3545',       // Rojo peligro
        'info' => '#17A2B8',          // Azul información
    ]);
}

// Configuración de paginación
if (!defined('ITEMS_PER_PAGE')) {
    define('ITEMS_PER_PAGE', 12);
}
if (!defined('PRODUCTS_PER_PAGE')) {
    define('PRODUCTS_PER_PAGE', 16);
}
if (!defined('ROUTINES_PER_PAGE')) {
    define('ROUTINES_PER_PAGE', 8);
}

// Configuración de cache
define('CACHE_ENABLED', true);
define('CACHE_TIME', 3600); // 1 hora

// Funciones auxiliares
class AppConfig
{
    // Función para obtener URL base
    public static function baseUrl(string $path = ''): string
    {
        return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
    }

    // Función para obtener URL de assets
    public static function asset(string $path): string
    {
        return ASSETS_URL . '/' . ltrim($path, '/');
    }

    // Función para obtener URL de uploads
    public static function uploadUrl(string $path): string
    {
        return UPLOAD_URL . '/' . ltrim($path, '/');
    }

    // Función para sanitizar entrada
    public static function sanitize(mixed $input): mixed
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
    }

    // Función para validar email
    public static function validateEmail(string $email): string|false
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // Función para generar slug
    public static function generateSlug(?string $text): string
    {
        if ($text === null) {
            return '';
        }
        $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
        $text = preg_replace('/\s+/', '-', trim($text));
        return strtolower($text);
    }

    // Función para formatear precio
    public static function formatPrice(float $price): string
    {
        return 'S/ ' . number_format($price, 2);
    }

    // Función para generar token CSRF
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Función para verificar token CSRF
    public static function verifyCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Función para redireccionar
    public static function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    // Función para establecer mensaje flash
    public static function setFlashMessage(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    // Función para obtener mensaje flash
    public static function getFlashMessage(string $type): ?string
    {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }

    // Función para verificar si usuario está logueado
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    // Función para obtener usuario actual
    public static function getCurrentUser(): ?array
    {
        if (self::isLoggedIn()) {
            return $_SESSION['user_data'] ?? null;
        }
        return null;
    }

    // Función para verificar rol de usuario
    public static function hasRole(string $role): bool
    {
        $user = self::getCurrentUser();
        return $user && $user['role'] === $role;
    }

    // Función para comprimir imagen
    public static function compressImage(string $source, string $destination, int $quality = 80): bool
    {
        $info = getimagesize($source);
        if ($info === false) {
            return false;
        }

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        } else {
            return false;
        }

        if ($image === false) {
            return false;
        }

        return imagejpeg($image, $destination, $quality) !== false;
    }

    // Función para generar thumbnail
    public static function generateThumbnail(string $source, string $destination, int $width = 300, int $height = 300): bool
    {
        $imageSize = getimagesize($source);
        if ($imageSize === false) {
            return false;
        }
        list($orig_width, $orig_height) = $imageSize;

        $ratio = min($width / $orig_width, $height / $orig_height);
        $new_width = (int)($orig_width * $ratio);
        $new_height = (int)($orig_height * $ratio);

        $new_image = imagecreatetruecolor($new_width, $new_height);
        if ($new_image === false) {
            return false;
        }

        $info = getimagesize($source);
        if ($info === false) {
            return false;
        }
        
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
        } else {
            return false;
        }

        if ($image === false) {
            return false;
        }

        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);

        return imagejpeg($new_image, $destination, 90) !== false;
    }

    // Función para logs
    public static function log(string $message, string $level = 'info'): void
    {
        $logFile = ROOT_PATH . '/logs/' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;

        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }

    // Función para verificar permisos de archivo
    public static function checkFilePermissions(string $file): bool
    {
        return is_readable($file) && is_writable($file);
    }

    // Función para obtener información del dispositivo
    public static function isMobile(): bool
    {
        return (bool)preg_match('/Mobile|Android|iPhone|iPad/', $_SERVER['HTTP_USER_AGENT'] ?? '');
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
} else {
    // Configuración para desarrollo
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
}

// Configurar timezone
date_default_timezone_set('America/Lima');
