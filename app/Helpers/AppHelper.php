<?php

namespace StyleFitness\Helpers;

use StyleFitness\Config\Database;
use Exception;
use DateTime;
use SimpleXMLElement;

/**
 * Helper Principal de la Aplicación - STYLOFITNESS
 * Funciones de utilidad general para toda la aplicación
 */

class AppHelper
{
    // ==========================================
    // AUTENTICACIÓN Y SESIONES
    // ==========================================

    /**
     * Verificar si el usuario está logueado
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Obtener el usuario actual de la sesión
     */
    public static function getCurrentUser()
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        // Si ya está en sesión, devolverlo
        if (isset($_SESSION['user_data'])) {
            return $_SESSION['user_data'];
        }

        // Si no, obtenerlo de la base de datos
        try {
            $db = Database::getInstance();
            $user = $db->fetch(
                'SELECT u.*, g.name as gym_name, g.theme_colors 
                 FROM users u 
                 LEFT JOIN gyms g ON u.gym_id = g.id 
                 WHERE u.id = ? AND u.is_active = 1',
                [$_SESSION['user_id']]
            );

            if ($user) {
                $_SESSION['user_data'] = $user;
                return $user;
            } else {
                // Usuario no encontrado, limpiar sesión
                self::logout();
                return null;
            }
        } catch (Exception $e) {
            error_log('Error getting current user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Iniciar sesión de usuario
     */
    public static function login($user)
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_data'] = $user;
        $_SESSION['login_time'] = time();

        // Actualizar último login
        try {
            $db = Database::getInstance();
            $db->query(
                'UPDATE users SET last_login_at = NOW(), login_count = login_count + 1 WHERE id = ?',
                [$user['id']]
            );
        } catch (Exception $e) {
            error_log('Error updating login info: ' . $e->getMessage());
        }
    }

    /**
     * Cerrar sesión
     */
    public static function logout()
    {
        session_unset();
        session_destroy();
        session_start(); // Reiniciar para flash messages
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public static function hasRole($role)
    {
        $user = self::getCurrentUser();
        if (!$user) {
            return false;
        }

        if (is_array($role)) {
            return in_array($user['role'], $role);
        }

        return $user['role'] === $role;
    }

    /**
     * Verificar si el usuario es administrador
     */
    public static function isAdmin()
    {
        return self::hasRole('admin');
    }

    /**
     * Verificar si el usuario es instructor
     */
    public static function isInstructor()
    {
        return self::hasRole('instructor');
    }

    /**
     * Verificar si el usuario es cliente
     */
    public static function isClient()
    {
        return self::hasRole('client');
    }

    // ==========================================
    // REDIRECCIONES Y NAVEGACIÓN
    // ==========================================

    /**
     * Redireccionar a una URL
     */
    public static function redirect($url, $permanent = false)
    {
        $statusCode = $permanent ? 301 : 302;

        // Agregar dominio base si es necesario
        if (!preg_match('/^https?:\/\//', $url)) {
            $baseUrl = self::getBaseUrl();
            $cleanUrl = ltrim($url, '/');
            $url = $baseUrl . $cleanUrl;
        }

        header("Location: $url", true, $statusCode);
        exit();
    }

    /**
     * Obtener URL base de la aplicación
     */
    public static function getBaseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('\\', '/', dirname($scriptName));

        // Normalizar el path base
        $basePath = $basePath === '/' ? '' : rtrim($basePath, '/');

        return $protocol . $host . $basePath . '/';
    }

    /**
     * Alias de getBaseUrl para compatibilidad
     */
    public static function baseUrl($path = '')
    {
        return self::getBaseUrl() . ltrim($path, '/');
    }

    /**
     * Generar URL de la aplicación
     */
    public static function url($path = '')
    {
        return self::getBaseUrl() . ltrim($path, '/');
    }

    /**
     * Generar URL para archivos de assets (CSS, JS, imágenes)
     */
    public static function asset($path)
    {
        return self::getBaseUrl() . ltrim($path, '/');
    }

    /**
     * Generar URL para archivos subidos
     */
    public static function uploadUrl($path = '')
    {
        // Si el path ya incluye 'uploads/', no duplicar
        if (strpos($path, '/uploads/') === 0 || strpos($path, 'uploads/') === 0) {
            return self::getBaseUrl() . ltrim($path, '/');
        }
        return self::getBaseUrl() . 'uploads/' . ltrim($path, '/');
    }

    /**
     * Redireccionar con autenticación requerida
     */
    public static function requireAuth($redirectUrl = null)
    {
        if (!self::isLoggedIn()) {
            $redirect = $redirectUrl ?: $_SERVER['REQUEST_URI'];
            self::redirect('/login?redirect=' . urlencode($redirect));
        }
    }

    /**
     * Redireccionar con rol requerido
     */
    public static function requireRole($role, $redirectUrl = '/')
    {
        if (!self::hasRole($role)) {
            self::setFlashMessage('No tienes permisos para acceder a esta página', 'error');
            self::redirect($redirectUrl);
        }
    }

    // ==========================================
    // MENSAJES FLASH
    // ==========================================

    /**
     * Establecer mensaje flash
     */
    public static function setFlashMessage($message, $type = 'info')
    {
        $_SESSION['flash_messages'][$type] = $message;
    }

    /**
     * Obtener mensaje flash por tipo
     */
    public static function getFlashMessage($type = null)
    {
        if ($type === null) {
            $messages = $_SESSION['flash_messages'] ?? [];
            unset($_SESSION['flash_messages']);
            return $messages;
        }
        
        if (isset($_SESSION['flash_messages'][$type])) {
            $message = $_SESSION['flash_messages'][$type];
            unset($_SESSION['flash_messages'][$type]);
            return $message;
        }
        
        return null;
    }

    /**
     * Verificar si hay mensaje flash de un tipo específico
     */
    public static function hasFlashMessage($type)
    {
        return isset($_SESSION['flash_messages'][$type]);
    }

    /**
     * Verificar si hay mensajes flash
     */
    public static function hasFlashMessages()
    {
        return !empty($_SESSION['flash_messages']);
    }

    // ==========================================
    // RESPUESTAS JSON
    // ==========================================

    /**
     * Enviar respuesta JSON
     */
    public static function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Respuesta JSON de éxito
     */
    public static function jsonSuccess($message = 'Operación exitosa', $data = null)
    {
        $response = ['success' => true, 'message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        self::jsonResponse($response);
    }

    /**
     * Respuesta JSON de error
     */
    public static function jsonError($message = 'Error en la operación', $statusCode = 400)
    {
        self::jsonResponse(['success' => false, 'message' => $message], $statusCode);
    }

    // ==========================================
    // VALIDACIONES
    // ==========================================

    /**
     * Validar email
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar contraseña
     */
    public static function validatePassword($password)
    {
        return strlen($password) >= 8;
    }

    /**
     * Validar teléfono
     */
    public static function validatePhone($phone)
    {
        return preg_match('/^[\+]?[0-9\s\-\(\)]{8,20}$/', $phone);
    }

    /**
     * Sanitizar datos de entrada
     */
    public static function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Limpiar entrada de datos
     */
    public static function cleanInput($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar CSRF token
     */
    public static function validateCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generar CSRF token
     */
    public static function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verificar CSRF token
     */
    public static function verifyCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Validar datos de usuario
     */
    public static function validateUser($data)
    {
        $errors = [];

        // Validar email
        if (empty($data['email'])) {
            $errors['email'] = 'El email es requerido';
        } elseif (!self::validateEmail($data['email'])) {
            $errors['email'] = 'El email no es válido';
        }

        // Validar contraseña (solo si está presente)
        if (isset($data['password']) && !empty($data['password'])) {
            if (!self::validatePassword($data['password'])) {
                $errors['password'] = 'La contraseña debe tener al menos 8 caracteres';
            }
        }

        // Validar nombre
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'El nombre es requerido';
        }

        // Validar apellido
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'El apellido es requerido';
        }

        // Validar teléfono (si está presente)
        if (!empty($data['phone']) && !self::validatePhone($data['phone'])) {
            $errors['phone'] = 'El teléfono no es válido';
        }

        // Validar rol
        $validRoles = ['admin', 'instructor', 'client'];
        if (empty($data['role']) || !in_array($data['role'], $validRoles)) {
            $errors['role'] = 'El rol seleccionado no es válido';
        }

        return $errors;
    }

    // ==========================================
    // FORMATEO Y UTILIDADES
    // ==========================================

    /**
     * Formatear precio
     */
    public static function formatPrice($price, $currency = 'S/')
    {
        return $currency . ' ' . number_format((float)$price, 2);
    }

    /**
     * Formatear fecha
     */
    public static function formatDate($date, $format = 'd/m/Y')
    {
        if (!$date) {
            return '';
        }

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        return $date->format($format);
    }

    /**
     * Formatear fecha con hora
     */
    public static function formatDateTime($datetime, $format = 'd/m/Y H:i')
    {
        return self::formatDate($datetime, $format);
    }

    /**
     * Formatear fecha relativa (hace X tiempo)
     */
    public static function timeAgo($datetime)
    {
        if (is_string($datetime)) {
            $datetime = new DateTime($datetime);
        }

        $now = new DateTime();
        $diff = $now->diff($datetime);

        if ($diff->days > 30) {
            return self::formatDate($datetime);
        } elseif ($diff->days > 0) {
            return $diff->days . ' día' . ($diff->days > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Hace un momento';
        }
    }

    /**
     * Truncar texto
     */
    public static function truncate($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Generar slug
     */
    public static function generateSlug($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }

    /**
     * Crear slug URL-friendly (alias de generateSlug)
     */
    public static function createSlug($text)
    {
        return self::generateSlug($text);
    }

    /**
     * Formatear bytes
     */
    public static function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    // ==========================================
    // ARCHIVOS Y UPLOADS
    // ==========================================

    /**
     * Obtener extensión de archivo
     */
    public static function getFileExtension($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Verificar si es imagen
     */
    public static function isImage($filename)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array(self::getFileExtension($filename), $imageExtensions);
    }

    /**
     * Verificar si es video
     */
    public static function isVideo($filename)
    {
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
        return in_array(self::getFileExtension($filename), $videoExtensions);
    }

    /**
     * Generar nombre único para archivo
     */
    public static function generateUniqueFilename($originalName)
    {
        $extension = self::getFileExtension($originalName);
        return uniqid() . '_' . time() . '.' . $extension;
    }

    // ==========================================
    // PAGINACIÓN
    // ==========================================

    /**
     * Generar enlaces de paginación
     */
    public static function generatePagination($currentPage, $totalPages, $baseUrl, $params = [])
    {
        if ($totalPages <= 1) {
            return '';
        }

        $html = '<nav aria-label="Paginación"><ul class="pagination">';

        // Botón anterior
        if ($currentPage > 1) {
            $prevUrl = self::buildUrl($baseUrl, array_merge($params, ['page' => $currentPage - 1]));
            $html .= '<li class="page-item"><a class="page-link" href="' . $prevUrl . '">&laquo; Anterior</a></li>';
        }

        // Números de página
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $currentPage + 2);

        if ($start > 1) {
            $firstUrl = self::buildUrl($baseUrl, array_merge($params, ['page' => 1]));
            $html .= '<li class="page-item"><a class="page-link" href="' . $firstUrl . '">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $currentPage) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $pageUrl = self::buildUrl($baseUrl, array_merge($params, ['page' => $i]));
                $html .= '<li class="page-item"><a class="page-link" href="' . $pageUrl . '">' . $i . '</a></li>';
            }
        }

        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $lastUrl = self::buildUrl($baseUrl, array_merge($params, ['page' => $totalPages]));
            $html .= '<li class="page-item"><a class="page-link" href="' . $lastUrl . '">' . $totalPages . '</a></li>';
        }

        // Botón siguiente
        if ($currentPage < $totalPages) {
            $nextUrl = self::buildUrl($baseUrl, array_merge($params, ['page' => $currentPage + 1]));
            $html .= '<li class="page-item"><a class="page-link" href="' . $nextUrl . '">Siguiente &raquo;</a></li>';
        }

        $html .= '</ul></nav>';

        return $html;
    }

    /**
     * Construir URL con parámetros
     */
    public static function buildUrl($baseUrl, $params = [])
    {
        if (empty($params)) {
            return $baseUrl;
        }

        $queryString = http_build_query($params);
        $separator = strpos($baseUrl, '?') !== false ? '&' : '?';

        return $baseUrl . $separator . $queryString;
    }

    // ==========================================
    // CONFIGURACIÓN
    // ==========================================

    /**
     * Obtener configuración del sistema
     */
    public static function getConfig($key, $default = null)
    {
        static $config = null;

        if ($config === null) {
            try {
                $db = Database::getInstance();
                $settings = $db->fetchAll('SELECT `key`, `value`, `type` FROM system_settings');

                $config = [];
                foreach ($settings as $setting) {
                    $value = $setting['value'];

                    // Convertir según el tipo
                    switch ($setting['type']) {
                        case 'boolean':
                            $value = (bool)$value;
                            break;
                        case 'integer':
                            $value = (int)$value;
                            break;
                        case 'json':
                            $value = json_decode($value, true);
                            break;
                    }

                    $config[$setting['key']] = $value;
                }
            } catch (Exception $e) {
                error_log('Error loading config: ' . $e->getMessage());
                $config = [];
            }
        }

        return $config[$key] ?? $default;
    }

    // ==========================================
    // LOGGING
    // ==========================================

    /**
     * Log de actividad del usuario
     */
    public static function logActivity($action, $resourceType = null, $resourceId = null, $details = [])
    {
        $user = self::getCurrentUser();
        if (!$user) {
            return;
        }

        try {
            $db = Database::getInstance();
            $db->insert(
                'INSERT INTO user_activity_logs (user_id, action, resource_type, resource_id, details, ip_address, user_agent) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [
                    $user['id'],
                    $action,
                    $resourceType,
                    $resourceId,
                    json_encode($details),
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null,
                ]
            );
        } catch (Exception $e) {
            error_log('Error logging activity: ' . $e->getMessage());
        }
    }

    // ==========================================
    // UTILIDADES ESPECÍFICAS DEL GIMNASIO
    // ==========================================

    /**
     * Obtener colores del tema del gimnasio
     */
    public static function getGymThemeColors()
    {
        $user = self::getCurrentUser();
        if (!$user || !isset($user['theme_colors'])) {
            return [
                'primary' => '#FF6B00',
                'secondary' => '#E55A00',
                'accent' => '#FFB366',
                'dark' => '#2C2C2C',
                'light' => '#F8F9FA',
            ];
        }

        return json_decode($user['theme_colors'], true);
    }

    /**
     * Formatear duración de ejercicio
     */
    public static function formatExerciseDuration($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return $hours . 'h' . ($remainingMinutes > 0 ? ' ' . $remainingMinutes . 'min' : '');
    }

    /**
     * Formatear dificultad
     */
    public static function formatDifficulty($level)
    {
        $levels = [
            'beginner' => 'Principiante',
            'intermediate' => 'Intermedio',
            'advanced' => 'Avanzado',
        ];

        return $levels[$level] ?? ucfirst($level);
    }

    /**
     * Formatear objetivo de rutina
     */
    public static function formatObjective($objective)
    {
        $objectives = [
            'weight_loss' => 'Pérdida de Peso',
            'muscle_gain' => 'Ganancia Muscular',
            'strength' => 'Fuerza',
            'endurance' => 'Resistencia',
            'flexibility' => 'Flexibilidad',
            'rehabilitation' => 'Rehabilitación',
        ];

        return $objectives[$objective] ?? ucfirst($objective);
    }

    /**
     * Comprimir imagen
     */
    public static function compressImage($source, $destination, $quality = 80)
    {
        if (!file_exists($source)) {
            return false;
        }

        $imageInfo = getimagesize($source);
        if (!$imageInfo) {
            return false;
        }

        $mimeType = $imageInfo['mime'];

        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        if (!$image) {
            return false;
        }

        // Redimensionar si es muy grande
        $width = imagesx($image);
        $height = imagesy($image);

        $maxWidth = 800;
        $maxHeight = 600;

        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int)($width * $ratio);
            $newHeight = (int)($height * $ratio);

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Mantener transparencia para PNG
            if ($mimeType === 'image/png') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                imagefill($resizedImage, 0, 0, $transparent);
            }

            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resizedImage;
        }

        // Guardar imagen comprimida
        $result = false;
        switch ($mimeType) {
            case 'image/jpeg':
                $result = imagejpeg($image, $destination, $quality);
                break;
            case 'image/png':
                $result = imagepng($image, $destination, (int)(9 * (100 - $quality) / 100));
                break;
            case 'image/gif':
                $result = imagegif($image, $destination);
                break;
        }

        imagedestroy($image);
        return $result;
    }

    /**
     * Convertir array a XML
     */
    public static function arrayToXml($data, $rootElement = 'root', $xml = null)
    {
        if ($xml === null) {
            $xml = new SimpleXMLElement('<' . $rootElement . '/>');
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item' . $key;
                }
                $subnode = $xml->addChild($key);
                self::arrayToXml($value, $rootElement, $subnode);
            } else {
                if (is_numeric($key)) {
                    $key = 'item' . $key;
                }
                $xml->addChild($key, htmlspecialchars($value));
            }
        }

        return $xml->asXML();
    }

    /**
     * Cargar una vista con datos
     */
    public static function loadView($viewPath, $data = [])
    {
        // Extraer variables para la vista
        extract($data);
        
        // Construir la ruta completa de la vista
        $viewFile = APP_PATH . '/Views/' . $viewPath . '.php';
        
        // Verificar que el archivo existe
        if (!file_exists($viewFile)) {
            throw new Exception("Vista no encontrada: {$viewPath}");
        }
        
        // Incluir el header
        include APP_PATH . '/Views/layout/header.php';
        
        // Incluir la vista
        include $viewFile;
        
        // Incluir el footer
        include APP_PATH . '/Views/layout/footer.php';
    }
}

// Función global para obtener configuración (shortcut)
function getAppConfig($key, $default = null)
{
    return AppHelper::getConfig($key, $default);
}
