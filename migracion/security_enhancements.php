<?php
/**
 * Mejoras de Seguridad y Validación
 * Sistema de Migración StyloFitness
 * 
 * Este archivo contiene clases y funciones para mejorar
 * la seguridad y validación del sistema de migración.
 */

/**
 * Logger centralizado para el sistema de migración
 */
class MigrationLogger {
    private $log_file;
    private $log_level;
    
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_CRITICAL = 'CRITICAL';
    
    public function __construct($log_file = null, $log_level = self::LEVEL_INFO) {
        $this->log_file = $log_file ?? __DIR__ . '/logs/migration_' . date('Y-m-d') . '.log';
        $this->log_level = $log_level;
        
        // Crear directorio de logs si no existe
        $log_dir = dirname($this->log_file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
    }
    
    public function debug($message, $context = []) {
        $this->log(self::LEVEL_DEBUG, $message, $context);
    }
    
    public function info($message, $context = []) {
        $this->log(self::LEVEL_INFO, $message, $context);
    }
    
    public function warning($message, $context = []) {
        $this->log(self::LEVEL_WARNING, $message, $context);
    }
    
    public function error($message, $context = []) {
        $this->log(self::LEVEL_ERROR, $message, $context);
    }
    
    public function critical($message, $context = []) {
        $this->log(self::LEVEL_CRITICAL, $message, $context);
    }
    
    private function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $formatted = "[$timestamp] [$level] $message";
        
        if (!empty($context)) {
            $formatted .= ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        
        $formatted .= PHP_EOL;
        
        // Escribir al archivo de log
        file_put_contents($this->log_file, $formatted, FILE_APPEND | LOCK_EX);
        
        // También mostrar en consola para errores críticos
        if (in_array($level, [self::LEVEL_ERROR, self::LEVEL_CRITICAL])) {
            echo "[LOG] $formatted";
        }
    }
    
    public function getLogFile() {
        return $this->log_file;
    }
}

/**
 * Validador de datos para el sistema de migración
 */
class DataValidator {
    private $logger;
    
    public function __construct(MigrationLogger $logger = null) {
        $this->logger = $logger ?? new MigrationLogger();
    }
    
    /**
     * Validar configuración de base de datos
     */
    public function validateDatabaseConfig($config) {
        $required_fields = ['host', 'database', 'username', 'password'];
        
        foreach ($required_fields as $field) {
            if (!isset($config[$field]) || empty($config[$field])) {
                throw new InvalidArgumentException("Campo de configuración requerido: $field");
            }
        }
        
        // Validar formato del host
        if (!filter_var($config['host'], FILTER_VALIDATE_IP) && !filter_var($config['host'], FILTER_VALIDATE_DOMAIN)) {
            if ($config['host'] !== 'localhost') {
                $this->logger->warning("Host de base de datos puede ser inválido", ['host' => $config['host']]);
            }
        }
        
        return true;
    }
    
    /**
     * Validar datos de producto
     */
    public function validateProduct($product_data) {
        $rules = [
            'name' => ['required' => true, 'type' => 'string', 'max_length' => 255],
            'price' => ['required' => true, 'type' => 'numeric', 'min' => 0],
            'description' => ['required' => false, 'type' => 'string'],
            'category_id' => ['required' => true, 'type' => 'integer', 'min' => 1]
        ];
        
        return $this->validateData($product_data, $rules);
    }
    
    /**
     * Validar datos de categoría
     */
    public function validateCategory($category_data) {
        $rules = [
            'name' => ['required' => true, 'type' => 'string', 'max_length' => 100],
            'description' => ['required' => false, 'type' => 'string'],
            'slug' => ['required' => true, 'type' => 'string', 'pattern' => '/^[a-z0-9-]+$/']
        ];
        
        return $this->validateData($category_data, $rules);
    }
    
    /**
     * Validar datos según reglas específicas
     */
    private function validateData($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            // Verificar campos requeridos
            if ($rule['required'] && (is_null($value) || $value === '')) {
                $errors[] = "Campo requerido: $field";
                continue;
            }
            
            // Si el campo no es requerido y está vacío, continuar
            if (!$rule['required'] && (is_null($value) || $value === '')) {
                continue;
            }
            
            // Validar tipo
            if (isset($rule['type'])) {
                if (!$this->validateType($value, $rule['type'])) {
                    $errors[] = "Tipo inválido para $field. Esperado: {$rule['type']}";
                }
            }
            
            // Validar longitud máxima
            if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                $errors[] = "$field excede la longitud máxima de {$rule['max_length']} caracteres";
            }
            
            // Validar valor mínimo
            if (isset($rule['min']) && is_numeric($value) && $value < $rule['min']) {
                $errors[] = "$field debe ser mayor o igual a {$rule['min']}";
            }
            
            // Validar patrón
            if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
                $errors[] = "$field no cumple con el formato requerido";
            }
        }
        
        if (!empty($errors)) {
            $this->logger->error("Errores de validación", ['errors' => $errors, 'data' => $data]);
            throw new InvalidArgumentException("Errores de validación: " . implode(', ', $errors));
        }
        
        return true;
    }
    
    /**
     * Validar tipo de dato
     */
    private function validateType($value, $type) {
        switch ($type) {
            case 'string':
                return is_string($value);
            case 'integer':
                return is_int($value) || (is_string($value) && ctype_digit($value));
            case 'numeric':
                return is_numeric($value);
            case 'boolean':
                return is_bool($value);
            case 'array':
                return is_array($value);
            default:
                return true;
        }
    }
}

/**
 * Sanitizador de datos
 */
class DataSanitizer {
    /**
     * Sanitizar string general
     */
    public static function sanitizeString($input) {
        if (!is_string($input)) {
            return $input;
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitizar nombre de archivo
     */
    public static function sanitizeFilename($filename) {
        // Remover caracteres peligrosos
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Evitar nombres de archivo peligrosos
        $dangerous_names = ['con', 'prn', 'aux', 'nul', 'com1', 'com2', 'com3', 'com4', 'com5', 'com6', 'com7', 'com8', 'com9', 'lpt1', 'lpt2', 'lpt3', 'lpt4', 'lpt5', 'lpt6', 'lpt7', 'lpt8', 'lpt9'];
        
        $name_without_ext = pathinfo($filename, PATHINFO_FILENAME);
        if (in_array(strtolower($name_without_ext), $dangerous_names)) {
            $filename = 'safe_' . $filename;
        }
        
        return $filename;
    }
    
    /**
     * Sanitizar slug
     */
    public static function sanitizeSlug($input) {
        // Convertir a minúsculas
        $slug = strtolower($input);
        
        // Remover acentos
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        
        // Reemplazar espacios y caracteres especiales con guiones
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Remover guiones al inicio y final
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Sanitizar HTML (para descripciones)
     */
    public static function sanitizeHTML($input) {
        // Permitir solo tags seguros
        $allowed_tags = '<p><br><strong><em><ul><ol><li><a><h1><h2><h3><h4><h5><h6>';
        
        return strip_tags($input, $allowed_tags);
    }
    
    /**
     * Sanitizar precio
     */
    public static function sanitizePrice($price) {
        // Remover caracteres no numéricos excepto punto y coma
        $price = preg_replace('/[^0-9.,]/', '', $price);
        
        // Convertir coma a punto para decimales
        $price = str_replace(',', '.', $price);
        
        // Convertir a float
        return floatval($price);
    }
}

/**
 * Excepciones personalizadas para el sistema de migración
 */
class MigrationException extends Exception {}
class DatabaseConnectionException extends MigrationException {}
class ImageProcessingException extends MigrationException {}
class ConfigurationException extends MigrationException {}
class ValidationException extends MigrationException {}

/**
 * Monitor de rendimiento
 */
class PerformanceMonitor {
    private $start_time;
    private $memory_start;
    private $query_count = 0;
    private $checkpoints = [];
    
    public function start() {
        $this->start_time = microtime(true);
        $this->memory_start = memory_get_usage();
        $this->query_count = 0;
        $this->checkpoints = [];
    }
    
    public function checkpoint($name) {
        $this->checkpoints[$name] = [
            'time' => microtime(true) - $this->start_time,
            'memory' => memory_get_usage() - $this->memory_start
        ];
    }
    
    public function incrementQueryCount() {
        $this->query_count++;
    }
    
    public function getMetrics() {
        return [
            'execution_time' => microtime(true) - $this->start_time,
            'memory_used' => memory_get_usage() - $this->memory_start,
            'peak_memory' => memory_get_peak_usage(),
            'queries_executed' => $this->query_count,
            'checkpoints' => $this->checkpoints
        ];
    }
    
    public function formatMetrics() {
        $metrics = $this->getMetrics();
        
        $output = "\n📊 MÉTRICAS DE RENDIMIENTO\n";
        $output .= str_repeat("-", 40) . "\n";
        $output .= sprintf("⏱️  Tiempo de ejecución: %.2f segundos\n", $metrics['execution_time']);
        $output .= sprintf("💾 Memoria utilizada: %s\n", $this->formatBytes($metrics['memory_used']));
        $output .= sprintf("📈 Pico de memoria: %s\n", $this->formatBytes($metrics['peak_memory']));
        $output .= sprintf("🔍 Consultas ejecutadas: %d\n", $metrics['queries_executed']);
        
        if (!empty($metrics['checkpoints'])) {
            $output .= "\n📍 CHECKPOINTS:\n";
            foreach ($metrics['checkpoints'] as $name => $data) {
                $output .= sprintf("  %s: %.2fs, %s\n", $name, $data['time'], $this->formatBytes($data['memory']));
            }
        }
        
        return $output;
    }
    
    private function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}

/**
 * Utilidades de seguridad
 */
class SecurityUtils {
    /**
     * Verificar permisos de directorio
     */
    public static function checkDirectoryPermissions($directory) {
        if (!is_dir($directory)) {
            throw new ConfigurationException("Directorio no existe: $directory");
        }
        
        if (!is_readable($directory)) {
            throw new ConfigurationException("Directorio no es legible: $directory");
        }
        
        if (!is_writable($directory)) {
            throw new ConfigurationException("Directorio no es escribible: $directory");
        }
        
        return true;
    }
    
    /**
     * Validar extensión de archivo de imagen
     */
    public static function validateImageExtension($filename) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return in_array($extension, $allowed_extensions);
    }
    
    /**
     * Verificar tamaño de archivo
     */
    public static function validateFileSize($file_path, $max_size_mb = 10) {
        if (!file_exists($file_path)) {
            return false;
        }
        
        $file_size = filesize($file_path);
        $max_size_bytes = $max_size_mb * 1024 * 1024;
        
        return $file_size <= $max_size_bytes;
    }
    
    /**
     * Generar hash de archivo para verificar integridad
     */
    public static function generateFileHash($file_path) {
        if (!file_exists($file_path)) {
            return false;
        }
        
        return hash_file('sha256', $file_path);
    }
}

// Ejemplo de uso
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    echo "🔒 MEJORAS DE SEGURIDAD - SISTEMA DE MIGRACIÓN\n";
    echo str_repeat("=", 50) . "\n\n";
    
    // Crear logger
    $logger = new MigrationLogger();
    $logger->info("Sistema de seguridad inicializado");
    
    // Crear validador
    $validator = new DataValidator($logger);
    
    // Ejemplo de validación de producto
    try {
        $product_data = [
            'name' => 'Proteína Whey',
            'price' => 29.99,
            'description' => 'Proteína de alta calidad',
            'category_id' => 1
        ];
        
        $validator->validateProduct($product_data);
        echo "✅ Validación de producto exitosa\n";
        
    } catch (Exception $e) {
        echo "❌ Error de validación: " . $e->getMessage() . "\n";
    }
    
    // Ejemplo de sanitización
    $unsafe_string = "<script>alert('hack')</script>Producto & Categoría";
    $safe_string = DataSanitizer::sanitizeString($unsafe_string);
    echo "🧹 Sanitización: '$unsafe_string' -> '$safe_string'\n";
    
    // Ejemplo de monitor de rendimiento
    $monitor = new PerformanceMonitor();
    $monitor->start();
    
    // Simular trabajo
    usleep(100000); // 0.1 segundos
    $monitor->checkpoint('Trabajo simulado');
    
    echo $monitor->formatMetrics();
    
    echo "\n✅ Mejoras de seguridad listas para implementar\n";
}
?>