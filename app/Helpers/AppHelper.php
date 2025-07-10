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
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Obtener el usuario actual de la sesión
     */
    public static function getCurrentUser(): ?array
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
    public static function login(array $user): void
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
    public static function logout(): void
    {
        // Eliminar cookies de remember_token si existen
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        session_unset();
        session_destroy();
        session_start(); // Reiniciar para flash messages
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public static function hasRole(string|array $role): bool
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
    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }

    /**
     * Verificar si el usuario es instructor
     */
    public static function isInstructor(): bool
    {
        return self::hasRole('instructor');
    }

    /**
     * Verificar si el usuario es cliente
     */
    public static function isClient(): bool
    {
        return self::hasRole('client');
    }

    // ==========================================
    // REDIRECCIONES Y NAVEGACIÓN
    // ==========================================

    /**
     * Redireccionar a una URL
     */
    public static function redirect(string $url, bool $permanent = false): void
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
    public static function getBaseUrl(): string
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
    public static function baseUrl(string $path = ''): string
    {
        return self::getBaseUrl() . ltrim($path, '/');
    }

    /**
     * Generar URL de la aplicación
     */
    public static function url(string $path = ''): string
    {
        return self::getBaseUrl() . ltrim($path, '/');
    }

    /**
     * Generar URL para archivos de assets (CSS, JS, imágenes)
     */
    public static function asset(string $path): string
    {
        $cleanPath = ltrim($path, '/');
        
        // Detectar si estamos en desarrollo local o en hosting
        $isLocalDev = (
            $_SERVER['HTTP_HOST'] === 'localhost' || 
            strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0 ||
            $_SERVER['HTTP_HOST'] === '127.0.0.1' ||
            strpos($_SERVER['HTTP_HOST'], '127.0.0.1:') === 0
        );
        
        // En desarrollo local, el router.php maneja la redirección
        // En hosting, necesitamos agregar 'public/' explícitamente
        if ($isLocalDev) {
            return self::getBaseUrl() . $cleanPath;
        } else {
            return self::getBaseUrl() . 'public/' . $cleanPath;
        }
    }

    /**
     * Generar URL para archivos subidos
     */
    public static function uploadUrl(string $path = ''): string
    {
        $cleanPath = ltrim($path, '/');
        
        // Detectar si estamos en desarrollo local o en hosting
        $isLocalDev = (
            $_SERVER['HTTP_HOST'] === 'localhost' || 
            strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0 ||
            $_SERVER['HTTP_HOST'] === '127.0.0.1' ||
            strpos($_SERVER['HTTP_HOST'], '127.0.0.1:') === 0
        );
        
        // Si el path ya incluye 'uploads/', no duplicar
        if (strpos($cleanPath, 'uploads/') === 0) {
            if ($isLocalDev) {
                return self::getBaseUrl() . $cleanPath;
            } else {
                return self::getBaseUrl() . 'public/' . $cleanPath;
            }
        }
        
        // Agregar 'uploads/' al path
        if ($isLocalDev) {
            return self::getBaseUrl() . 'uploads/' . $cleanPath;
        } else {
            return self::getBaseUrl() . 'public/uploads/' . $cleanPath;
        }
    }

    /**
     * Redireccionar con autenticación requerida
     */
    public static function requireAuth(?string $redirectUrl = null): void
    {
        if (!self::isLoggedIn()) {
            $redirect = $redirectUrl ?: $_SERVER['REQUEST_URI'];
            self::redirect('/login?redirect=' . urlencode($redirect));
        }
    }

    /**
     * Redireccionar con rol requerido
     */
    public static function requireRole(string|array $role, string $redirectUrl = '/'): void
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
    public static function setFlashMessage(string $message, string $type = 'info'): void
    {
        $_SESSION['flash_messages'][$type] = $message;
    }

    /**
     * Obtener mensaje flash por tipo
     */
    public static function getFlashMessage(?string $type = null): mixed
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
    public static function hasFlashMessage(string $type): bool
    {
        return isset($_SESSION['flash_messages'][$type]);
    }

    /**
     * Verificar si hay mensajes flash
     */
    public static function hasFlashMessages(): bool
    {
        return !empty($_SESSION['flash_messages']);
    }

    // ==========================================
    // RESPUESTAS JSON
    // ==========================================

    /**
     * Enviar respuesta JSON
     */
    public static function jsonResponse(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Respuesta JSON de éxito
     */
    public static function jsonSuccess(string $message = 'Operación exitosa', mixed $data = null): void
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
    public static function jsonError(string $message = 'Error en la operación', int $statusCode = 400): void
    {
        self::jsonResponse(['success' => false, 'message' => $message], $statusCode);
    }

    // ==========================================
    // VALIDACIONES
    // ==========================================

    /**
     * Validar email
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar contraseña
     */
    public static function validatePassword(string $password): bool
    {
        return strlen($password) >= 8;
    }

    /**
     * Validar teléfono
     */
    public static function validatePhone(string $phone): int|false
    {
        return preg_match('/^[\+]?[0-9\s\-\(\)]{8,20}$/', $phone);
    }

    /**
     * Sanitizar datos de entrada
     */
    public static function sanitize(mixed $data): mixed
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Limpiar entrada de datos
     */
    public static function cleanInput(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar CSRF token
     */
    public static function validateCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generar CSRF token
     */
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verificar CSRF token
     */
    public static function verifyCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Validar datos de usuario
     */
    public static function validateUser(array $data): array
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
    public static function formatPrice(float|int $price, string $currency = 'S/'): string
    {
        return $currency . ' ' . number_format((float)$price, 2);
    }

    /**
     * Formatear fecha
     */
    public static function formatDate(string|DateTime|null $date, string $format = 'd/m/Y'): string
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
    public static function formatDateTime(string|DateTime|null $datetime, string $format = 'd/m/Y H:i'): string
    {
        return self::formatDate($datetime, $format);
    }

    /**
     * Formatear fecha relativa (hace X tiempo)
     */
    public static function timeAgo(string|DateTime $datetime): string
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
    public static function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Generar slug
     */
    public static function generateSlug(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }

    /**
     * Crear slug URL-friendly (alias de generateSlug)
     */
    public static function createSlug(string $text): string
    {
        return self::generateSlug($text);
    }

    /**
     * Formatear bytes
     */
    public static function formatBytes(int|float $size, int $precision = 2): string
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
    public static function getFileExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Verificar si es imagen
     */
    public static function isImage(string $filename): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array(self::getFileExtension($filename), $imageExtensions);
    }

    /**
     * Verificar si es video
     */
    public static function isVideo(string $filename): bool
    {
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
        return in_array(self::getFileExtension($filename), $videoExtensions);
    }

    /**
     * Generar nombre único para archivo
     */
    public static function generateUniqueFilename(string $originalName): string
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
    public static function generatePagination(int $currentPage, int $totalPages, string $baseUrl, array $params = []): string
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
    public static function buildUrl(string $baseUrl, array $params = []): string
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
    public static function getConfig(string $key, mixed $default = null): mixed
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
    public static function logActivity(string $action, ?string $resourceType = null, ?int $resourceId = null, array $details = []): void
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
    public static function getGymThemeColors(): array
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
    public static function formatExerciseDuration(int $minutes): string
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
    public static function formatDifficulty(string $level): string
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
    public static function formatObjective(string $objective): string
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
    public static function compressImage(string $source, string $destination, int $quality = 80): bool
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
    public static function arrayToXml(array $data, string $rootElement = 'root', ?SimpleXMLElement $xml = null): string|false
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
    public static function loadView(string $viewPath, array $data = []): void
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

    /**
     * Limpiar y mostrar descripción de producto
     * Decodifica entidades HTML y limpia caracteres especiales
     */
    public static function cleanDescription(string $description, int $maxLength = 0): string
    {
        if (empty($description)) {
            return '';
        }

        // Decodificar entidades HTML como &amp; &lt; &gt;
        $cleaned = html_entity_decode($description, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Limpiar caracteres de control y espacios extra
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $cleaned);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned);
        
        // Truncar si se especifica longitud máxima
        if ($maxLength > 0 && mb_strlen($cleaned) > $maxLength) {
            $cleaned = mb_substr($cleaned, 0, $maxLength) . '...';
        }
        
        return $cleaned;
    }

    /**
     * Mostrar descripción segura para HTML
     * Limpia la descripción y la escapa para mostrar en HTML
     */
    public static function safeDescription(string $description, int $maxLength = 0): string
    {
        $cleaned = self::cleanDescription($description, $maxLength);
        return htmlspecialchars($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Truncar texto a un número específico de palabras
     * @param string $text El texto a truncar
     * @param int $wordLimit Número máximo de palabras
     * @param string $suffix Sufijo a agregar cuando se trunca (por defecto "...")
     * @return string Texto truncado
     */
    public static function truncateWords(string $text, int $wordLimit = 10, string $suffix = '...'): string
    {
        if (empty($text)) {
            return '';
        }

        // Limpiar el texto primero
        $cleaned = self::cleanDescription($text);
        
        // Dividir en palabras
        $words = preg_split('/\s+/', $cleaned, -1, PREG_SPLIT_NO_EMPTY);
        
        // Si tiene menos palabras que el límite, devolver el texto completo
        if (count($words) <= $wordLimit) {
            return $cleaned;
        }
        
        // Tomar solo las primeras palabras y agregar sufijo
        $truncated = implode(' ', array_slice($words, 0, $wordLimit));
        return $truncated . $suffix;
    }

    /**
     * Truncar descripción de producto de forma segura para HTML
     * @param string $description La descripción a truncar
     * @param int $wordLimit Número máximo de palabras
     * @param string $suffix Sufijo a agregar cuando se trunca
     * @return string Descripción truncada y escapada para HTML
     */
    public static function safeTruncatedDescription(string $description, int $wordLimit = 10, string $suffix = '...'): string
    {
        $truncated = self::truncateWords($description, $wordLimit, $suffix);
        return htmlspecialchars($truncated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

// Función global para obtener configuración (shortcut)
function getAppConfig(string $key, mixed $default = null): mixed
{
    return AppHelper::getConfig($key, $default);
}
