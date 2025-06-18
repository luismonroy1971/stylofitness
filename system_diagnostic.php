<?php
/**
 * STYLOFITNESS - Diagnóstico Completo del Sistema
 * Script maestro para verificar todo el funcionamiento
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 120); // 2 minutos máximo

// Inicializar
session_start();
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

class SystemDiagnostic {
    private $results = [];
    private $totalTests = 0;
    private $passedTests = 0;
    private $startTime;
    
    public function __construct() {
        $this->startTime = microtime(true);
        $this->displayHeader();
    }
    
    public function runCompleteDiagnostic() {
        echo "<div class='diagnostic-container'>";
        
        $this->checkEnvironment();
        $this->checkFileSystem();
        $this->checkDatabase();
        $this->checkModels();
        $this->checkControllers();
        $this->checkRoutingSystem();
        $this->checkApiEndpoints();
        $this->checkPermissions();
        $this->checkPerformance();
        
        $this->displaySummary();
        echo "</div>";
        $this->displayFooter();
    }
    
    private function displayHeader() {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>StyloFitness - Diagnóstico Completo del Sistema</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh; 
                    padding: 20px;
                    line-height: 1.6;
                }
                .diagnostic-container { 
                    max-width: 1400px; 
                    margin: 0 auto; 
                    background: white; 
                    border-radius: 20px; 
                    overflow: hidden;
                    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
                }
                .main-header { 
                    background: linear-gradient(135deg, #FF6B00, #E55A00); 
                    color: white; 
                    padding: 40px; 
                    text-align: center;
                    position: relative;
                    overflow: hidden;
                }
                .main-header h1 { 
                    font-size: 3rem; 
                    margin-bottom: 15px; 
                    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                    position: relative;
                    z-index: 1;
                }
                .main-header p { 
                    font-size: 1.2rem; 
                    opacity: 0.95;
                    position: relative;
                    z-index: 1;
                }
                .section { 
                    margin-bottom: 30px; 
                    border-radius: 15px; 
                    overflow: hidden;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
                    background: white;
                }
                .section-header { 
                    background: linear-gradient(135deg, #6c757d, #495057); 
                    color: white; 
                    padding: 20px 25px; 
                    font-size: 1.3rem; 
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }
                .section-content { 
                    padding: 25px; 
                }
                .test-grid { 
                    display: grid; 
                    gap: 15px; 
                }
                .test-item { 
                    display: flex; 
                    justify-content: space-between; 
                    align-items: center; 
                    padding: 15px 20px; 
                    background: #f8f9fa; 
                    border-radius: 10px; 
                    transition: all 0.3s ease;
                    border-left: 4px solid transparent;
                }
                .test-item:hover { 
                    transform: translateX(5px); 
                    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                }
                .test-item.success { border-left-color: #28a745; }
                .test-item.error { border-left-color: #dc3545; }
                .test-item.warning { border-left-color: #ffc107; }
                .test-description { 
                    font-weight: 500; 
                    color: #495057;
                }
                .test-result { 
                    font-weight: bold; 
                    padding: 6px 12px; 
                    border-radius: 20px; 
                    font-size: 14px;
                }
                .result-success { background: #d4edda; color: #155724; }
                .result-error { background: #f8d7da; color: #721c24; }
                .result-warning { background: #fff3cd; color: #856404; }
                .result-info { background: #d1ecf1; color: #0c5460; }
                .test-details { 
                    font-size: 12px; 
                    color: #6c757d; 
                    margin-top: 8px; 
                    padding: 8px 12px; 
                    background: white; 
                    border-radius: 6px;
                    border-left: 3px solid #dee2e6;
                }
                .summary-section { 
                    background: linear-gradient(135deg, #e9ecef, #f8f9fa); 
                    padding: 30px; 
                    margin-top: 30px;
                }
                .summary-grid { 
                    display: grid; 
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
                    gap: 20px; 
                    margin-top: 20px; 
                }
                .summary-card { 
                    background: white; 
                    padding: 25px; 
                    border-radius: 15px; 
                    text-align: center; 
                    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                    transition: transform 0.3s ease;
                }
                .summary-card:hover { 
                    transform: translateY(-5px); 
                }
                .summary-number { 
                    font-size: 2.5rem; 
                    font-weight: bold; 
                    margin-bottom: 10px; 
                }
                .summary-label { 
                    color: #6c757d; 
                    text-transform: uppercase; 
                    font-size: 12px; 
                    letter-spacing: 1px; 
                    font-weight: 600;
                }
                .progress-bar { 
                    width: 100%; 
                    height: 8px; 
                    background: #e9ecef; 
                    border-radius: 4px; 
                    overflow: hidden; 
                    margin: 15px 0;
                }
                .progress-fill { 
                    height: 100%; 
                    background: linear-gradient(90deg, #28a745, #20c997); 
                    border-radius: 4px; 
                    transition: width 0.5s ease;
                }
                .actions { 
                    text-align: center; 
                    margin-top: 25px; 
                }
                .btn { 
                    display: inline-block; 
                    padding: 12px 25px; 
                    margin: 0 10px; 
                    background: #FF6B00; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 8px; 
                    font-weight: 600; 
                    transition: all 0.3s ease;
                    border: none;
                    cursor: pointer;
                }
                .btn:hover { 
                    background: #E55A00; 
                    transform: translateY(-2px); 
                    box-shadow: 0 5px 15px rgba(255, 107, 0, 0.3);
                }
                .btn-secondary { background: #6c757d; }
                .btn-secondary:hover { background: #5a6268; }
                .container { padding: 30px; }
            </style>
        </head>
        <body>
            <div class="main-header">
                <h1>🔧 StyloFitness</h1>
                <p>Diagnóstico Completo del Sistema - Verificación Integral de Funcionamiento</p>
            </div>
            <div class="container">
        <?php
    }
    
    private function checkEnvironment() {
        echo "<div class='section'>";
        echo "<div class='section-header'>🌐 Entorno del Sistema</div>";
        echo "<div class='section-content'>";
        echo "<div class='test-grid'>";
        
        // PHP Version
        $phpVersion = PHP_VERSION;
        $this->addTest(
            "Versión de PHP",
            version_compare($phpVersion, '8.0.0', '>='),
            $phpVersion . (version_compare($phpVersion, '8.0.0', '>=') ? ' (Compatible)' : ' (Actualizar recomendado)'),
            "Se recomienda PHP 8.0 o superior"
        );
        
        // Extensions
        $requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'curl', 'gd', 'mbstring'];
        foreach ($requiredExtensions as $ext) {
            $this->addTest(
                "Extensión PHP: {$ext}",
                extension_loaded($ext),
                extension_loaded($ext) ? 'Instalada' : 'No instalada',
                "Extensión requerida para el funcionamiento"
            );
        }
        
        // Memory Limit
        $memoryLimit = ini_get('memory_limit');
        $memoryBytes = $this->parseMemoryLimit($memoryLimit);
        $this->addTest(
            "Límite de memoria PHP",
            $memoryBytes >= 128 * 1024 * 1024,
            $memoryLimit,
            "Se recomienda al menos 128MB"
        );
        
        // Upload Max Size
        $uploadMax = ini_get('upload_max_filesize');
        $this->addTest(
            "Tamaño máximo de subida",
            true,
            $uploadMax,
            "Configuración actual de archivos"
        );
        
        echo "</div></div></div>";
    }
    
    private function checkFileSystem() {
        echo "<div class='section'>";
        echo "<div class='section-header'>📁 Sistema de Archivos</div>";
        echo "<div class='section-content'>";
        echo "<div class='test-grid'>";
        
        // Estructura de directorios
        $requiredDirs = [
            'app' => 'Directorio principal de la aplicación',
            'app/Controllers' => 'Controladores MVC',
            'app/Models' => 'Modelos de datos',
            'app/Views' => 'Vistas del sistema',
            'app/Config' => 'Archivos de configuración',
            'app/Helpers' => 'Clases auxiliares',
            'public' => 'Archivos públicos',
            'public/css' => 'Hojas de estilo',
            'public/js' => 'Scripts JavaScript',
            'public/images' => 'Imágenes del sistema',
            'public/uploads' => 'Archivos subidos'
        ];
        
        foreach ($requiredDirs as $dir => $description) {
            $exists = is_dir(ROOT_PATH . '/' . $dir);
            $this->addTest(
                $description,
                $exists,
                $exists ? 'Existe' : 'No encontrado',
                "Directorio: /{$dir}"
            );
        }
        
        // Archivos críticos
        $criticalFiles = [
            'index.php' => 'Archivo principal del sistema',
            '.htaccess' => 'Configuración de Apache',
            'app/Config/Database.php' => 'Configuración de base de datos',
            'app/Controllers/ApiController.php' => 'Controlador de API',
            'app/Models/User.php' => 'Modelo de usuarios'
        ];
        
        foreach ($criticalFiles as $file => $description) {
            $exists = file_exists(ROOT_PATH . '/' . $file);
            $this->addTest(
                $description,
                $exists,
                $exists ? 'Encontrado' : 'No encontrado',
                "Archivo: {$file}"
            );
        }
        
        echo "</div></div></div>";
    }
    
    private function checkDatabase() {
        echo "<div class='section'>";
        echo "<div class='section-header'>🗄️ Base de Datos</div>";
        echo "<div class='section-content'>";
        echo "<div class='test-grid'>";
        
        try {
            // Cargar configuración de base de datos
            require_once APP_PATH . '/Config/Database.php';
            $db = Database::getInstance();
            
            $this->addTest(
                "Conexión a MySQL",
                true,
                "Conectado exitosamente",
                "Base de datos accesible"
            );
            
            // Verificar versión de MySQL
            $version = $db->fetch("SELECT VERSION() as version");
            if ($version) {
                $this->addTest(
                    "Versión de MySQL",
                    true,
                    $version['version'],
                    "Versión del servidor MySQL"
                );
            }
            
            // Verificar base de datos actual
            $dbName = $db->fetch("SELECT DATABASE() as db_name");
            if ($dbName) {
                $this->addTest(
                    "Base de datos actual",
                    !empty($dbName['db_name']),
                    $dbName['db_name'] ?: 'Sin seleccionar',
                    "Base de datos en uso"
                );
            }
            
            // Verificar tablas principales
            $requiredTables = [
                'users' => 'Usuarios del sistema',
                'gyms' => 'Sedes del gimnasio',
                'exercises' => 'Ejercicios disponibles',
                'routines' => 'Rutinas de entrenamiento',
                'products' => 'Productos de la tienda',
                'group_classes' => 'Clases grupales'
            ];
            
            $tables = $db->fetchAll("SHOW TABLES");
            $existingTables = array_column($tables, array_values($tables[0])[0]);
            
            foreach ($requiredTables as $table => $description) {
                $exists = in_array($table, $existingTables);
                $count = 0;
                
                if ($exists) {
                    try {
                        $count = $db->count("SELECT COUNT(*) FROM `{$table}`");
                    } catch (Exception $e) {
                        $count = 'Error';
                    }
                }
                
                $this->addTest(
                    $description,
                    $exists,
                    $exists ? "Existe ({$count} registros)" : 'No encontrada',
                    "Tabla: {$table}"
                );
            }
            
        } catch (Exception $e) {
            $this->addTest(
                "Conexión a base de datos",
                false,
                "Error: " . $e->getMessage(),
                "Verificar configuración en .env"
            );
        }
        
        echo "</div></div></div>";
    }
    
    private function checkModels() {
        echo "<div class='section'>";
        echo "<div class='section-header'>📊 Modelos de Datos</div>";
        echo "<div class='section-content'>";
        echo "<div class='test-grid'>";
        
        // Autoloader simple
        spl_autoload_register(function ($class) {
            $paths = [APP_PATH . '/Models/', APP_PATH . '/Controllers/', APP_PATH . '/Helpers/'];
            foreach ($paths as $path) {
                $file = $path . $class . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        });
        
        $models = [
            'User' => 'Gestión de usuarios',
            'Exercise' => 'Gestión de ejercicios',
            'Routine' => 'Gestión de rutinas',
            'Product' => 'Gestión de productos',
            'Order' => 'Gestión de pedidos',
            'GroupClass' => 'Gestión de clases grupales'
        ];
        
        foreach ($models as $model => $description) {
            $classExists = class_exists($model);
            $canInstantiate = false;
            $methods = [];
            
            if ($classExists) {
                try {
                    $instance = new $model();
                    $canInstantiate = true;
                    $methods = get_class_methods($instance);
                } catch (Exception $e) {
                    $canInstantiate = false;
                }
            }
            
            $this->addTest(
                $description,
                $classExists && $canInstantiate,
                $classExists ? ($canInstantiate ? 'Funcional' : 'Error al instanciar') : 'No encontrado',
                $classExists ? "Métodos disponibles: " . count($methods) : "Archivo: app/Models/{$model}.php"
            );
        }
        
        echo "</div></div></div>";
    }
    
    private function checkControllers() {
        echo "<div class='section'>";
        echo "<div class='section-header'>🎮 Controladores</div>";
        echo "<div class='section-content'>";
        echo "<div class='test-grid'>";
        
        $controllers = [
            'ApiController' => 'API REST del sistema',
            'AuthController' => 'Autenticación de usuarios',
            'HomeController' => 'Página principal',
            'RoutineController' => 'Gestión de rutinas',
            'StoreController' => 'Tienda online',
            'AdminController' => 'Panel de administración',
            'CartController' => 'Carrito de compras',
            'GroupClassController' => 'Clases grupales'
        ];
        
        foreach ($controllers as $controller => $description) {
            $classExists = class_exists($controller);
            $canInstantiate = false;
            $methods = [];
            
            if ($classExists) {
                try {
                    $instance = new $controller();
                    $canInstantiate = true;
                    $methods = get_class_methods($instance);
                } catch (Exception $e) {
                    $canInstantiate = false;
                }
            }
            
            $this->addTest(
                $description,
                $classExists && $canInstantiate,
                $classExists ? ($canInstantiate ? 'Operativo' : 'Error al cargar') : 'No encontrado',
                $classExists ? "Métodos públicos: " . count(array_filter($methods, function($m) { return !str_starts_with($m, '_'); })) : "Archivo: app/Controllers/{$controller}.php"
            );
        }
        
        echo "</div></div></div>";
    }
    
    private function checkRoutingSystem() {
        echo "<div class='section'>";
        echo "<div class='section-header'>🛣️ Sistema de Rutas</div>";
        echo "<div class='section-content'>";
        echo "<div class='test-grid'>";
        
        // Verificar archivo principal
        $indexExists = file_exists(ROOT_PATH . '/index.php');
        $this->addTest(
            "Archivo principal del sistema",
            $indexExists,
            $indexExists ? 'Encontrado' : 'No encontrado',
            "Punto de entrada: index.php"
        );
        
        // Verificar .htaccess
        $htaccessExists = file_exists(ROOT_PATH . '/.htaccess');
        $this->addTest(
            "Configuración de Apache",
            $htaccessExists,
            $htaccessExists ? 'Configurado' : 'No encontrado',
            "Archivo: .htaccess"
        );
        
        // Verificar mod_rewrite (si es posible)
        if (function_exists('apache_get_modules')) {
            $modRewrite = in_array('mod_rewrite', apache_get_modules());
            $this->addTest(
                "Módulo mod_rewrite",
                $modRewrite,
                $modRewrite ? 'Habilitado' : 'No disponible',
                "Requerido para URLs amigables"
            );
        }
        
        echo "</div></div></div>";
    }
    
    private function checkApiEndpoints() {
        echo "<div class='section'>";
        echo "<div class='section-header'>🌐 Endpoints API</div>";
        echo "<div class='section-content'>";
        echo "<div class='test-grid'>";
        
        if (class_exists('ApiController')) {
            $apiController = new ApiController();
            $apiMethods = [
                'routines' => 'Gestión de rutinas',
                'exercises' => 'Gestión de ejercicios',
                'products' => 'Gestión de productos',
                'users' => 'Gestión de usuarios',
                'dashboardStats' => 'Estadísticas del dashboard'
            ];
            
            foreach ($apiMethods as $method => $description) {
                $methodExists = method_exists($apiController, $method);
                $this->addTest(
                    $description,
                    $methodExists,
                    $methodExists ? 'Disponible' : 'No implementado',
                    "Endpoint: /api/" . str_replace('_', '/', strtolower(preg_replace('/([A-Z])/', '-$1', $method)))
                );
            }
        } else {
            $this->addTest(
                "Controlador API",
                false,
                "ApiController no encontrado",
                "Verificar app/Controllers/ApiController.php"
            );
        }
        
        echo "</div></div></div>";
    }
    
    private function checkPermissions() {
        echo "<div class='section'>";
        echo "<div class='section-header'>🔒 Permisos del Sistema</div>";
        echo "<div class='section-content'>";
        echo "<div class='test-grid'>";
        
        $writableDirs = [
            'public/uploads' => 'Directorio de uploads',
            'logs' => 'Directorio de logs',
            'storage' => 'Directorio de almacenamiento'
        ];
        
        foreach ($writableDirs as $dir => $description) {
            $path = ROOT_PATH . '/' . $dir;
            $exists = is_dir($path);
            $writable = $exists && is_writable($path);
            
            $this->addTest(
                $description,
                $writable,
                $writable ? 'Escribible' : ($exists ? 'Solo lectura' : 'No existe'),
                "Permisos en: {$dir}"
            );
        }
        
        echo "</div></div></div>";
    }
    
    private function checkPerformance() {
        echo "<div class='section'>";
        echo "<div class='section-header'>⚡ Rendimiento</div>";
        echo "<div class='section-content'>";
        echo "<div class='test-grid'>";
        
        // Tiempo de ejecución hasta ahora
        $currentTime = microtime(true) - $this->startTime;
        $this->addTest(
            "Tiempo de diagnóstico",
            $currentTime < 30,
            round($currentTime, 2) . " segundos",
            "Tiempo total de verificación"
        );
        
        // Uso de memoria
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $this->addTest(
            "Uso de memoria",
            $memoryUsage < 50 * 1024 * 1024,
            $this->formatBytes($memoryUsage) . " (pico: " . $this->formatBytes($memoryPeak) . ")",
            "Consumo de memoria del script"
        );
        
        echo "</div></div></div>";
    }
    
    private function addTest($description, $success, $result, $details = '') {
        $this->totalTests++;
        if ($success) {
            $this->passedTests++;
        }
        
        $statusClass = $success ? 'success' : 'error';
        $resultClass = $success ? 'result-success' : 'result-error';
        $icon = $success ? '✅' : '❌';
        
        echo "<div class='test-item {$statusClass}'>";
        echo "<div>";
        echo "<div class='test-description'>{$description}</div>";
        if ($details) {
            echo "<div class='test-details'>{$details}</div>";
        }
        echo "</div>";
        echo "<div class='test-result {$resultClass}'>{$icon} {$result}</div>";
        echo "</div>";
        
        $this->results[] = [
            'description' => $description,
            'success' => $success,
            'result' => $result,
            'details' => $details
        ];
    }
    
    private function displaySummary() {
        $successRate = $this->totalTests > 0 ? round(($this->passedTests / $this->totalTests) * 100, 1) : 0;
        $executionTime = round(microtime(true) - $this->startTime, 2);
        
        echo "<div class='summary-section'>";
        echo "<h2>📋 Resumen del Diagnóstico</h2>";
        
        echo "<div class='summary-grid'>";
        
        echo "<div class='summary-card'>";
        echo "<div class='summary-number' style='color: #FF6B00;'>{$this->totalTests}</div>";
        echo "<div class='summary-label'>Pruebas Totales</div>";
        echo "</div>";
        
        echo "<div class='summary-card'>";
        echo "<div class='summary-number' style='color: #28a745;'>{$this->passedTests}</div>";
        echo "<div class='summary-label'>Exitosas</div>";
        echo "</div>";
        
        echo "<div class='summary-card'>";
        echo "<div class='summary-number' style='color: #dc3545;'>" . ($this->totalTests - $this->passedTests) . "</div>";
        echo "<div class='summary-label'>Fallidas</div>";
        echo "</div>";
        
        echo "<div class='summary-card'>";
        $rateColor = $successRate >= 90 ? '#28a745' : ($successRate >= 70 ? '#ffc107' : '#dc3545');
        echo "<div class='summary-number' style='color: {$rateColor};'>{$successRate}%</div>";
        echo "<div class='summary-label'>Tasa de Éxito</div>";
        echo "</div>";
        
        echo "<div class='summary-card'>";
        echo "<div class='summary-number' style='color: #6c757d;'>{$executionTime}s</div>";
        echo "<div class='summary-label'>Tiempo Total</div>";
        echo "</div>";
        
        echo "</div>";
        
        echo "<div class='progress-bar'>";
        echo "<div class='progress-fill' style='width: {$successRate}%;'></div>";
        echo "</div>";
        
        echo "<div class='actions'>";
        echo "<a href='?' class='btn'>🔄 Ejecutar Nuevamente</a>";
        echo "<a href='test_endpoints.php' class='btn btn-secondary'>📊 Pruebas Detalladas</a>";
        echo "<a href='check_routes.php' class='btn btn-secondary'>🌐 Verificar Rutas</a>";
        echo "</div>";
        
        echo "</div>";
    }
    
    private function displayFooter() {
        echo "</div>";
        echo "</body>";
        echo "</html>";
    }
    
    private function parseMemoryLimit($limit) {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit)-1]);
        $value = (int) $limit;
        
        switch($last) {
            case 'g': $value *= 1024;
            case 'm': $value *= 1024;
            case 'k': $value *= 1024;
        }
        
        return $value;
    }
    
    private function formatBytes($bytes) {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}

// Ejecutar el diagnóstico
$diagnostic = new SystemDiagnostic();
$diagnostic->runCompleteDiagnostic();
?>