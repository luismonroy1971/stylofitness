<?php

/**
 * Funciones auxiliares globales - STYLOFITNESS
 * Funciones de utilidad disponibles en toda la aplicación
 */

// Prevenir inclusión múltiple
if (!defined('STYLOFITNESS_FUNCTIONS_LOADED')) {
    define('STYLOFITNESS_FUNCTIONS_LOADED', true);
} else {
    return;
}

// ==========================================
// FUNCIONES DE CONFIGURACIÓN
// ==========================================

if (!function_exists('config')) {
    /**
     * Obtener valor de configuración
     */
    function config(string $key, mixed $default = null): mixed
    {
        static $config = null;

        if ($config === null) {
            $config = [];

            // Cargar desde .env si existe
            if (file_exists(ROOT_PATH . '/.env')) {
                $env = parse_ini_file(ROOT_PATH . '/.env');
                $config = array_merge($config, $env);
            }

            // Valores por defecto
            $config = array_merge([
                'app_name' => 'STYLOFITNESS',
                'app_version' => '1.0.0',
                'app_env' => 'production',
                'app_debug' => false,
                'db_host' => 'localhost',
                'db_database' => 'stylofitness_gym',
                'db_username' => 'root',
                'db_password' => '',
                'cache_enabled' => true,
                'cache_time' => 3600,
                'session_lifetime' => 120,
                'upload_max_size' => '50M',
                'timezone' => 'America/Lima',
            ], $config);
        }

        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}

if (!function_exists('isDebug')) {
    /**
     * Verificar si estamos en modo debug
     */
    function isDebug(): bool
    {
        return config('app_debug', false) === true || config('app_debug', false) === 'true';
    }
}

if (!function_exists('isProduction')) {
    /**
     * Verificar si estamos en producción
     */
    function isProduction(): bool
    {
        return config('app_env', 'production') === 'production';
    }
}

if (!function_exists('getAppConfig')) {
    /**
     * Obtener configuración de la aplicación
     * Función de compatibilidad para el sistema
     */
    function getAppConfig(string $key, mixed $default = null): mixed
    {
        // Mapeo de claves específicas
        $keyMap = [
            'debug_enabled' => 'app_debug',
            'app_name' => 'app_name',
            'app_version' => 'app_version',
            'environment' => 'app_env'
        ];
        
        // Usar mapeo si existe, sino usar la clave directamente
        $configKey = $keyMap[$key] ?? $key;
        
        // Para debug_enabled, también verificar constantes
        if ($key === 'debug_enabled') {
            if (defined('APP_ENV') && APP_ENV === 'development') {
                return true;
            }
            return config($configKey, $default) === true || config($configKey, $default) === 'true';
        }
        
        return config($configKey, $default);
    }
}

// ==========================================
// FUNCIONES DE UTILIDAD
// ==========================================

if (!function_exists('dd')) {
    /**
     * Dumps de variables para debug
     */
    function dd(...$vars)
    {
        if (!isDebug()) {
            return;
        }

        echo '<pre style="background: #1e1e1e; color: #fff; padding: 20px; border-radius: 8px; margin: 10px; font-family: Monaco, monospace; font-size: 14px; line-height: 1.5; overflow: auto; max-height: 500px;">';

        foreach ($vars as $var) {
            var_dump($var);
            echo "\n" . str_repeat('-', 50) . "\n";
        }

        echo '</pre>';

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        if (isset($trace[0])) {
            echo '<div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin: 10px; border-radius: 5px; font-family: Monaco, monospace; font-size: 12px; color: #495057;">';
            echo '<strong>Debug called from:</strong> ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'];
            echo '</div>';
        }

        die();
    }
}

if (!function_exists('debug_log')) {
    /**
     * Log de debug simple
     */
    function debug_log($message, $context = [])
    {
        if (!isDebug()) {
            return;
        }

        $logFile = ROOT_PATH . '/logs/debug.log';
        $timestamp = date('Y-m-d H:i:s');

        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        $logMessage = "[$timestamp] $message";

        if (!empty($context)) {
            $logMessage .= ' | Context: ' . json_encode($context);
        }

        $logMessage .= "\n";

        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('generateUuid')) {
    /**
     * Generar UUID v4
     */
    function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('generateSecureToken')) {
    /**
     * Generar token seguro
     */
    function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}

if (!function_exists('hashPassword')) {
    /**
     * Hash de password seguro
     */
    function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}

if (!function_exists('verifyPassword')) {
    /**
     * Verificar password
     */
    function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}

// ==========================================
// FUNCIONES DE FORMATO
// ==========================================

if (!function_exists('formatPrice')) {
    /**
     * Formatear precio
     */
    function formatPrice(float $price, string $currency = 'PEN'): string
    {
        $symbols = [
            'PEN' => 'S/',
            'USD' => '$',
            'EUR' => '€',
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';
        return $symbol . ' ' . number_format($price, 2);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Formatear fecha
     */
    function formatDate(string $date, string $format = 'd/m/Y'): string
    {
        if (is_string($date)) {
            $date = new DateTime($date);
        }

        return $date->format($format);
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Formatear fecha relativa
     */
    function timeAgo(string $datetime): string
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) {
            return 'hace un momento';
        }
        if ($time < 3600) {
            return 'hace ' . floor($time / 60) . ' minutos';
        }
        if ($time < 86400) {
            return 'hace ' . floor($time / 3600) . ' horas';
        }
        if ($time < 2592000) {
            return 'hace ' . floor($time / 86400) . ' días';
        }
        if ($time < 31104000) {
            return 'hace ' . floor($time / 2592000) . ' meses';
        }

        return 'hace más de un año';
    }
}

if (!function_exists('formatFileSize')) {
    /**
     * Formatear tamaño de archivo
     */
    function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

if (!function_exists('truncate')) {
    /**
     * Truncar texto
     */
    function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length - strlen($suffix)) . $suffix;
    }
}

if (!function_exists('pluralize')) {
    /**
     * Pluralizar texto
     */
    function pluralize(int $count, string $singular, ?string $plural = null): string
    {
        if ($plural === null) {
            $plural = $singular . 's';
        }

        return $count == 1 ? $singular : $plural;
    }
}

// ==========================================
// FUNCIONES DE VALIDACIÓN
// ==========================================

if (!function_exists('isValidEmail')) {
    /**
     * Validar email
     */
    function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('isValidUrl')) {
    /**
     * Validar URL
     */
    function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}

if (!function_exists('isValidPhone')) {
    /**
     * Validar teléfono
     */
    function isValidPhone(string $phone): bool
    {
        // Patrón básico para teléfonos peruanos
        return preg_match('/^(\+51|51)?[9][0-9]{8}$/', str_replace([' ', '-', '(', ')'], '', $phone));
    }
}

if (!function_exists('isValidDNI')) {
    /**
     * Validar DNI peruano
     */
    function isValidDNI(string $dni): bool
    {
        return preg_match('/^[0-9]{8}$/', $dni);
    }
}

if (!function_exists('isValidRUC')) {
    /**
     * Validar RUC peruano
     */
    function isValidRUC(string $ruc): bool
    {
        return preg_match('/^[0-9]{11}$/', $ruc);
    }
}

// ==========================================
// FUNCIONES DE ARCHIVO
// ==========================================

if (!function_exists('getFileExtension')) {
    /**
     * Obtener extensión de archivo
     */
    function getFileExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}

if (!function_exists('isImage')) {
    /**
     * Verificar si el archivo es una imagen
     */
    function isImage(string $filename): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array(getFileExtension($filename), $imageExtensions);
    }
}

if (!function_exists('isVideo')) {
    /**
     * Verificar si el archivo es un video
     */
    function isVideo(string $filename): bool
    {
        $videoExtensions = ['mp4', 'webm', 'ogg', 'avi', 'mov', 'wmv'];
        return in_array(getFileExtension($filename), $videoExtensions);
    }
}

if (!function_exists('sanitizeFilename')) {
    /**
     * Limpiar nombre de archivo
     */
    function sanitizeFilename(string $filename): string
    {
        // Remover caracteres especiales
        $clean = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);

        // Evitar nombres de archivos vacíos
        if (empty($clean)) {
            $clean = 'file_' . time();
        }

        return $clean;
    }
}

if (!function_exists('generateUniqueFilename')) {
    /**
     * Generar nombre único de archivo
     */
    function generateUniqueFilename(string $originalName, string $directory = ''): string
    {
        $extension = getFileExtension($originalName);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $basename = sanitizeFilename($basename);

        $filename = $basename . '.' . $extension;
        $counter = 1;

        while (file_exists($directory . '/' . $filename)) {
            $filename = $basename . '_' . $counter . '.' . $extension;
            $counter++;
        }

        return $filename;
    }
}

// ==========================================
// FUNCIONES DE ARRAY
// ==========================================

if (!function_exists('array_get')) {
    /**
     * Obtener valor de array con valor por defecto
     */
    function array_get(array $array, string $key, mixed $default = null): mixed
    {
        if (!is_array($array)) {
            return $default;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        // Soporte para notación de punto
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $value = $array;

            foreach ($keys as $k) {
                if (!is_array($value) || !array_key_exists($k, $value)) {
                    return $default;
                }
                $value = $value[$k];
            }

            return $value;
        }

        return $default;
    }
}

if (!function_exists('isAssociativeArray')) {
    /**
     * Verificar si array es asociativo
     */
    function isAssociativeArray(array $array): bool
    {
        if (!is_array($array)) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}

if (!function_exists('array_only')) {
    /**
     * Filtrar array por claves
     */
    function array_only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}

if (!function_exists('array_group_by')) {
    /**
     * Agrupar array por clave
     */
    function array_group_by(array $array, string $key): array
    {
        $grouped = [];

        foreach ($array as $item) {
            $groupKey = is_callable($key) ? $key($item) : $item[$key];
            $grouped[$groupKey][] = $item;
        }

        return $grouped;
    }
}

// ==========================================
// FUNCIONES DE STRING (Compatibilidad PHP < 8.0)
// ==========================================

if (!function_exists('str_slug')) {
    /**
     * Generar slug
     */
    function str_slug(string $text, string $separator = '-'): string
    {
        // Convertir a minúsculas
        $text = strtolower($text);

        // Reemplazar caracteres especiales
        $text = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $text);

        // Remover caracteres no alfanuméricos
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);

        // Reemplazar espacios con separador
        $text = preg_replace('/\s+/', $separator, trim($text));

        return $text;
    }
}

/**
 * Verificar si string contiene otra string (PHP 8.0+)
 */
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle !== '' && strpos($haystack, $needle) !== false;
    }
}

/**
 * Verificar si string empieza con otra string (PHP 8.0+)
 */
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle !== '' && strpos($haystack, $needle) === 0;
    }
}

/**
 * Verificar si string termina con otra string (PHP 8.0+)
 */
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        return $needle !== '' && substr($haystack, -strlen($needle)) === $needle;
    }
}

if (!function_exists('str_camel')) {
    /**
     * Convertir a camelCase
     */
    function str_camel(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string))));
    }
}

if (!function_exists('str_pascal')) {
    /**
     * Convertir a PascalCase
     */
    function str_pascal(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }
}

if (!function_exists('str_snake')) {
    /**
     * Convertir a snake_case
     */
    function str_snake(string $string): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }
}

// ==========================================
// FUNCIONES DE REQUEST
// ==========================================

if (!function_exists('request_method')) {
    /**
     * Obtener método HTTP actual
     */
    function request_method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}

if (!function_exists('is_ajax')) {
    /**
     * Verificar si es request AJAX
     */
    function is_ajax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

if (!function_exists('get_client_ip')) {
    /**
     * Obtener IP del cliente
     */
    function get_client_ip(): string
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return trim($ip);
            }
        }

        return '0.0.0.0';
    }
}

if (!function_exists('get_user_agent')) {
    /**
     * Obtener User Agent
     */
    function get_user_agent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}

if (!function_exists('is_mobile')) {
    /**
     * Verificar si es dispositivo móvil
     */
    function is_mobile(): bool
    {
        $userAgent = get_user_agent();
        return preg_match('/Mobile|Android|iPhone|iPad|BlackBerry|Windows Phone/', $userAgent);
    }
}

// ==========================================
// FUNCIONES DE RESPUESTA HTTP
// ==========================================

if (!function_exists('json_response')) {
    /**
     * Respuesta JSON
     */
    function json_response(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('json_error')) {
    /**
     * Respuesta de error JSON
     */
    function json_error(string $message, int $statusCode = 400, mixed $details = null): void
    {
        $response = ['error' => $message];

        if ($details !== null) {
            $response['details'] = $details;
        }

        json_response($response, $statusCode);
    }
}

if (!function_exists('json_success')) {
    /**
     * Respuesta de éxito JSON
     */
    function json_success(mixed $data = null, string $message = 'Success'): void
    {
        $response = ['success' => true, 'message' => $message];

        if ($data !== null) {
            $response['data'] = $data;
        }

        json_response($response, 200);
    }
}

// ==========================================
// FUNCIONES DE TIEMPO
// ==========================================

if (!function_exists('now')) {
    /**
     * Obtener timestamp actual
     */
    function now(): int
    {
        return time();
    }
}

if (!function_exists('today')) {
    /**
     * Obtener fecha actual
     */
    function today(): string
    {
        return date('Y-m-d');
    }
}

if (!function_exists('current_datetime')) {
    /**
     * Obtener fecha y hora actual
     */
    function current_datetime(): string
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('add_days')) {
    /**
     * Agregar días a una fecha
     */
    function add_days(string $date, int $days): string
    {
        return date('Y-m-d', strtotime($date . " +{$days} days"));
    }
}

if (!function_exists('days_between')) {
    /**
     * Diferencia en días entre fechas
     */
    function days_between(string $date1, string $date2): int
    {
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $interval = $datetime1->diff($datetime2);
        return $interval->days;
    }
}

// ==========================================
// FUNCIONES DE RENDERIZADO
// ==========================================

if (!function_exists('view')) {
    /**
     * Renderizar vista
     */
    function view(string $viewPath, array $data = []): string
    {
        extract($data);

        $viewFile = ROOT_PATH . '/app/Views/' . str_replace('.', '/', $viewPath) . '.php';

        if (file_exists($viewFile)) {
            ob_start();
            include $viewFile;
            return ob_get_clean();
        }

        throw new Exception("Vista no encontrada: $viewPath");
    }
}

if (!function_exists('partial')) {
    /**
     * Incluir parcial
     */
    function partial(string $partialPath, array $data = []): void
    {
        echo view($partialPath, $data);
    }
}

// ==========================================
// FUNCIONES DE SEGURIDAD ADICIONALES
// ==========================================

if (!function_exists('e')) {
    /**
     * Escape HTML
     */
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('clean')) {
    /**
     * Limpiar input
     */
    function clean(mixed $input): mixed
    {
        if (is_array($input)) {
            return array_map('clean', $input);
        }

        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generar token anti-CSRF
     */
    function csrf_token(): string
    {
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_token'];
    }
}

if (!function_exists('csrf_verify')) {
    /**
     * Verificar token anti-CSRF
     */
    function csrf_verify(string $token): bool
    {
        return isset($_SESSION['_token']) && hash_equals($_SESSION['_token'], $token);
    }
}

// ==========================================
// FUNCIONES DE LOG
// ==========================================

if (!function_exists('write_log')) {
    /**
     * Log simple
     */
    function write_log(string $message, string $level = 'info'): void
    {
        $logFile = ROOT_PATH . '/logs/' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');

        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        $logMessage = "[$timestamp] [$level] $message\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

// Alias para diferentes niveles de log
if (!function_exists('log_info')) {
    function log_info(string $message): void
    {
        write_log($message, 'info');
    }
}
if (!function_exists('log_warning')) {
    function log_warning(string $message): void
    {
        write_log($message, 'warning');
    }
}
if (!function_exists('log_error')) {
    function log_error(string $message): void
    {
        write_log($message, 'error');
    }
}
if (!function_exists('log_debug')) {
    function log_debug(string $message): void
    {
        write_log($message, 'debug');
    }
}

// ==========================================
// CLASE CACHE SIMPLE
// ==========================================

if (!class_exists('SimpleCache')) {
    /**
     * Cache simple basado en archivos
     */
    class SimpleCache
    {
        private static $cacheDir = null;

        private static function getCacheDir()
        {
            if (self::$cacheDir === null) {
                self::$cacheDir = ROOT_PATH . '/storage/cache';
                if (!is_dir(self::$cacheDir)) {
                    mkdir(self::$cacheDir, 0755, true);
                }
            }
            return self::$cacheDir;
        }

        public static function get(string $key, mixed $default = null): mixed
        {
            $file = self::getCacheDir() . '/' . md5($key) . '.cache';

            if (!file_exists($file)) {
                return $default;
            }

            $data = unserialize(file_get_contents($file));

            if ($data['expires'] > 0 && $data['expires'] < time()) {
                unlink($file);
                return $default;
            }

            return $data['value'];
        }

        public static function put(string $key, mixed $value, int $ttl = 3600): void
        {
            $file = self::getCacheDir() . '/' . md5($key) . '.cache';

            $data = [
                'value' => $value,
                'expires' => $ttl > 0 ? time() + $ttl : 0,
            ];

            file_put_contents($file, serialize($data));
        }

        public static function forget(string $key): void
        {
            $file = self::getCacheDir() . '/' . md5($key) . '.cache';

            if (file_exists($file)) {
                unlink($file);
            }
        }

        public static function flush(): void
        {
            $files = glob(self::getCacheDir() . '/*.cache');

            foreach ($files as $file) {
                unlink($file);
            }
        }
    }
}

// ==========================================
// FUNCIONES DE CACHE GLOBALES
// ==========================================

if (!function_exists('cache_get')) {
    function cache_get(string $key, mixed $default = null): mixed
    {
        return SimpleCache::get($key, $default);
    }
}

if (!function_exists('cache_put')) {
    function cache_put(string $key, mixed $value, int $ttl = 3600): void
    {
        SimpleCache::put($key, $value, $ttl);
    }
}

if (!function_exists('cache_forget')) {
    function cache_forget(string $key): void
    {
        SimpleCache::forget($key);
    }
}

if (!function_exists('cache_flush')) {
    function cache_flush(): void
    {
        SimpleCache::flush();
    }
}
