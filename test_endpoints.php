<?php
/**
 * STYLOFITNESS - Script de Verificación de Endpoints
 * Herramienta para verificar el funcionamiento del backend
 */

// Configuración de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Iniciar sesión
session_start();

// Definir constantes
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');

// Cargar dependencias
require_once APP_PATH . '/Config/Database.php';
require_once APP_PATH . '/Helpers/AppHelper.php';

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/Controllers/',
        APP_PATH . '/Models/',
        APP_PATH . '/Helpers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

class EndpointTester {
    private $results = [];
    private $db = null;
    
    public function __construct() {
        echo "<style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #FF6B00, #E55A00); color: white; padding: 30px; margin: -20px -20px 20px -20px; border-radius: 10px 10px 0 0; text-align: center; }
            .test-section { margin-bottom: 30px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
            .test-header { background: #f8f9fa; padding: 15px; font-weight: bold; border-bottom: 1px solid #ddd; }
            .test-item { padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
            .test-item:last-child { border-bottom: none; }
            .status-success { color: #28a745; font-weight: bold; }
            .status-error { color: #dc3545; font-weight: bold; }
            .status-warning { color: #ffc107; font-weight: bold; }
            .code-block { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px; margin-top: 10px; border-left: 4px solid #FF6B00; overflow-x: auto; }
            .summary { background: #e9ecef; padding: 20px; border-radius: 8px; margin-top: 20px; }
            .btn { padding: 8px 16px; background: #FF6B00; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
            .btn:hover { background: #E55A00; }
        </style>";
        
        echo "<div class='container'>";
        echo "<div class='header'>";
        echo "<h1>🏋️ STYLOFITNESS - Verificador de Endpoints</h1>";
        echo "<p>Diagnóstico completo del sistema backend</p>";
        echo "</div>";
    }
    
    public function runAllTests() {
        $this->testDatabaseConnection();
        $this->testEnvironmentVariables();
        $this->testFileStructure();
        $this->testDatabaseTables();
        $this->testModels();
        $this->testControllers();
        $this->testApiEndpoints();
        $this->showSummary();
        echo "</div>";
    }
    
    private function testDatabaseConnection() {
        echo "<div class='test-section'>";
        echo "<div class='test-header'>🗄️ Conexión a Base de Datos</div>";
        
        try {
            $this->db = Database::getInstance();
            $connection = $this->db->getConnection();
            
            if ($connection) {
                $this->addResult('db_connection', true, 'Conexión exitosa a la base de datos');
                echo "<div class='test-item'>";
                echo "<span>Conexión a MySQL</span>";
                echo "<span class='status-success'>✅ CONECTADO</span>";
                echo "</div>";
                
                // Test query
                $result = $this->db->fetch("SELECT VERSION() as version");
                if ($result) {
                    echo "<div class='test-item'>";
                    echo "<span>Versión de MySQL</span>";
                    echo "<span class='status-success'>{$result['version']}</span>";
                    echo "</div>";
                }
                
                // Test database name
                $dbResult = $this->db->fetch("SELECT DATABASE() as db_name");
                if ($dbResult) {
                    echo "<div class='test-item'>";
                    echo "<span>Base de datos actual</span>";
                    echo "<span class='status-success'>{$dbResult['db_name']}</span>";
                    echo "</div>";
                }
                
            } else {
                $this->addResult('db_connection', false, 'No se pudo establecer conexión');
                echo "<div class='test-item'>";
                echo "<span>Conexión a MySQL</span>";
                echo "<span class='status-error'>❌ ERROR</span>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            $this->addResult('db_connection', false, $e->getMessage());
            echo "<div class='test-item'>";
            echo "<span>Conexión a MySQL</span>";
            echo "<span class='status-error'>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</span>";
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    private function testEnvironmentVariables() {
        echo "<div class='test-section'>";
        echo "<div class='test-header'>⚙️ Variables de Entorno</div>";
        
        $envFile = ROOT_PATH . '/.env';
        if (file_exists($envFile)) {
            $this->addResult('env_file', true, 'Archivo .env encontrado');
            echo "<div class='test-item'>";
            echo "<span>Archivo .env</span>";
            echo "<span class='status-success'>✅ ENCONTRADO</span>";
            echo "</div>";
            
            // Parse .env file
            $envContent = file_get_contents($envFile);
            $envVars = [];
            foreach (explode("\n", $envContent) as $line) {
                if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
                    list($key, $value) = explode('=', $line, 2);
                    $envVars[trim($key)] = trim($value);
                }
            }
            
            $requiredVars = ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'APP_NAME'];
            foreach ($requiredVars as $var) {
                echo "<div class='test-item'>";
                echo "<span>{$var}</span>";
                if (isset($envVars[$var]) && !empty($envVars[$var])) {
                    echo "<span class='status-success'>✅ CONFIGURADO</span>";
                } else {
                    echo "<span class='status-warning'>⚠️ NO CONFIGURADO</span>";
                }
                echo "</div>";
            }
            
        } else {
            $this->addResult('env_file', false, 'Archivo .env no encontrado');
            echo "<div class='test-item'>";
            echo "<span>Archivo .env</span>";
            echo "<span class='status-error'>❌ NO ENCONTRADO</span>";
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    private function testFileStructure() {
        echo "<div class='test-section'>";
        echo "<div class='test-header'>📁 Estructura de Archivos</div>";
        
        $requiredDirs = [
            'app' => 'Directorio principal de la aplicación',
            'app/Controllers' => 'Controladores',
            'app/Models' => 'Modelos',
            'app/Views' => 'Vistas',
            'app/Config' => 'Configuración',
            'public' => 'Archivos públicos',
            'database' => 'Scripts de base de datos'
        ];
        
        foreach ($requiredDirs as $dir => $description) {
            echo "<div class='test-item'>";
            echo "<span>{$description}</span>";
            if (is_dir(ROOT_PATH . '/' . $dir)) {
                echo "<span class='status-success'>✅ EXISTE</span>";
                $this->addResult("dir_{$dir}", true, "Directorio {$dir} existe");
            } else {
                echo "<span class='status-error'>❌ NO EXISTE</span>";
                $this->addResult("dir_{$dir}", false, "Directorio {$dir} no existe");
            }
            echo "</div>";
        }
        
        $requiredFiles = [
            'index.php' => 'Archivo principal',
            'app/Controllers/ApiController.php' => 'Controlador API',
            'app/Config/Database.php' => 'Configuración de base de datos',
            '.htaccess' => 'Configuración Apache'
        ];
        
        foreach ($requiredFiles as $file => $description) {
            echo "<div class='test-item'>";
            echo "<span>{$description}</span>";
            if (file_exists(ROOT_PATH . '/' . $file)) {
                echo "<span class='status-success'>✅ EXISTE</span>";
                $this->addResult("file_{$file}", true, "Archivo {$file} existe");
            } else {
                echo "<span class='status-error'>❌ NO EXISTE</span>";
                $this->addResult("file_{$file}", false, "Archivo {$file} no existe");
            }
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    private function testDatabaseTables() {
        echo "<div class='test-section'>";
        echo "<div class='test-header'>🗃️ Tablas de Base de Datos</div>";
        
        if (!$this->db) {
            echo "<div class='test-item'>";
            echo "<span>Verificación de tablas</span>";
            echo "<span class='status-error'>❌ NO HAY CONEXIÓN A BD</span>";
            echo "</div>";
            echo "</div>";
            return;
        }
        
        $requiredTables = [
            'users' => 'Usuarios del sistema',
            'gyms' => 'Sedes del gimnasio',
            'exercises' => 'Ejercicios',
            'exercise_categories' => 'Categorías de ejercicios',
            'routines' => 'Rutinas de entrenamiento',
            'routine_exercises' => 'Ejercicios en rutinas',
            'products' => 'Productos de la tienda',
            'product_categories' => 'Categorías de productos',
            'group_classes' => 'Clases grupales'
        ];
        
        try {
            $tables = $this->db->fetchAll("SHOW TABLES");
            $existingTables = array_column($tables, array_values($tables[0])[0]);
            
            foreach ($requiredTables as $table => $description) {
                echo "<div class='test-item'>";
                echo "<span>{$description} ({$table})</span>";
                if (in_array($table, $existingTables)) {
                    echo "<span class='status-success'>✅ EXISTE</span>";
                    $this->addResult("table_{$table}", true, "Tabla {$table} existe");
                    
                    // Count records
                    try {
                        $count = $this->db->count("SELECT COUNT(*) FROM {$table}");
                        echo "<div style='font-size: 12px; color: #666; margin-top: 5px;'>{$count} registros</div>";
                    } catch (Exception $e) {
                        echo "<div style='font-size: 12px; color: #dc3545; margin-top: 5px;'>Error al contar: {$e->getMessage()}</div>";
                    }
                    
                } else {
                    echo "<span class='status-error'>❌ NO EXISTE</span>";
                    $this->addResult("table_{$table}", false, "Tabla {$table} no existe");
                }
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='test-item'>";
            echo "<span>Error verificando tablas</span>";
            echo "<span class='status-error'>❌ " . htmlspecialchars($e->getMessage()) . "</span>";
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    private function testModels() {
        echo "<div class='test-section'>";
        echo "<div class='test-header'>📊 Modelos</div>";
        
        $models = [
            'User' => 'Modelo de usuarios',
            'Exercise' => 'Modelo de ejercicios',
            'Routine' => 'Modelo de rutinas',
            'Product' => 'Modelo de productos',
            'GroupClass' => 'Modelo de clases grupales'
        ];
        
        foreach ($models as $model => $description) {
            echo "<div class='test-item'>";
            echo "<span>{$description}</span>";
            
            try {
                if (class_exists($model)) {
                    $instance = new $model();
                    echo "<span class='status-success'>✅ CARGADO</span>";
                    $this->addResult("model_{$model}", true, "Modelo {$model} cargado correctamente");
                } else {
                    echo "<span class='status-error'>❌ NO ENCONTRADO</span>";
                    $this->addResult("model_{$model}", false, "Modelo {$model} no encontrado");
                }
            } catch (Exception $e) {
                echo "<span class='status-error'>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</span>";
                $this->addResult("model_{$model}", false, "Error en modelo {$model}: " . $e->getMessage());
            }
            
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    private function testControllers() {
        echo "<div class='test-section'>";
        echo "<div class='test-header'>🎮 Controladores</div>";
        
        $controllers = [
            'ApiController' => 'Controlador de API',
            'AuthController' => 'Controlador de autenticación',
            'HomeController' => 'Controlador principal',
            'RoutineController' => 'Controlador de rutinas',
            'StoreController' => 'Controlador de tienda',
            'AdminController' => 'Controlador de administración'
        ];
        
        foreach ($controllers as $controller => $description) {
            echo "<div class='test-item'>";
            echo "<span>{$description}</span>";
            
            try {
                if (class_exists($controller)) {
                    echo "<span class='status-success'>✅ CARGADO</span>";
                    $this->addResult("controller_{$controller}", true, "Controlador {$controller} cargado correctamente");
                    
                    // Test instantiation
                    try {
                        $instance = new $controller();
                        echo "<div style='font-size: 12px; color: #28a745; margin-top: 5px;'>Instanciación exitosa</div>";
                    } catch (Exception $e) {
                        echo "<div style='font-size: 12px; color: #dc3545; margin-top: 5px;'>Error en instanciación: {$e->getMessage()}</div>";
                    }
                    
                } else {
                    echo "<span class='status-error'>❌ NO ENCONTRADO</span>";
                    $this->addResult("controller_{$controller}", false, "Controlador {$controller} no encontrado");
                }
            } catch (Exception $e) {
                echo "<span class='status-error'>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</span>";
                $this->addResult("controller_{$controller}", false, "Error en controlador {$controller}: " . $e->getMessage());
            }
            
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    private function testApiEndpoints() {
        echo "<div class='test-section'>";
        echo "<div class='test-header'>🌐 Endpoints API (Simulados)</div>";
        
        if (!class_exists('ApiController')) {
            echo "<div class='test-item'>";
            echo "<span>API Controller no disponible</span>";
            echo "<span class='status-error'>❌ NO DISPONIBLE</span>";
            echo "</div>";
            echo "</div>";
            return;
        }
        
        // Simulate testing API methods
        $apiMethods = [
            'routines' => 'GET /api/routines',
            'exercises' => 'GET /api/exercises', 
            'products' => 'GET /api/products',
            'users' => 'GET /api/users',
            'dashboardStats' => 'GET /api/stats/dashboard'
        ];
        
        foreach ($apiMethods as $method => $endpoint) {
            echo "<div class='test-item'>";
            echo "<span>{$endpoint}</span>";
            
            try {
                $apiController = new ApiController();
                if (method_exists($apiController, $method)) {
                    echo "<span class='status-success'>✅ MÉTODO EXISTE</span>";
                    $this->addResult("api_{$method}", true, "Método {$method} existe en ApiController");
                } else {
                    echo "<span class='status-error'>❌ MÉTODO NO EXISTE</span>";
                    $this->addResult("api_{$method}", false, "Método {$method} no existe en ApiController");
                }
            } catch (Exception $e) {
                echo "<span class='status-error'>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</span>";
                $this->addResult("api_{$method}", false, "Error verificando método {$method}: " . $e->getMessage());
            }
            
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    private function addResult($test, $success, $message) {
        $this->results[] = [
            'test' => $test,
            'success' => $success,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    private function showSummary() {
        $total = count($this->results);
        $successful = count(array_filter($this->results, function($r) { return $r['success']; }));
        $failed = $total - $successful;
        $successRate = $total > 0 ? round(($successful / $total) * 100, 1) : 0;
        
        echo "<div class='summary'>";
        echo "<h3>📋 Resumen del Diagnóstico</h3>";
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 15px;'>";
        
        echo "<div style='text-align: center; padding: 15px; background: white; border-radius: 8px;'>";
        echo "<div style='font-size: 24px; font-weight: bold; color: #FF6B00;'>{$total}</div>";
        echo "<div style='font-size: 14px; color: #666;'>PRUEBAS TOTALES</div>";
        echo "</div>";
        
        echo "<div style='text-align: center; padding: 15px; background: white; border-radius: 8px;'>";
        echo "<div style='font-size: 24px; font-weight: bold; color: #28a745;'>{$successful}</div>";
        echo "<div style='font-size: 14px; color: #666;'>EXITOSAS</div>";
        echo "</div>";
        
        echo "<div style='text-align: center; padding: 15px; background: white; border-radius: 8px;'>";
        echo "<div style='font-size: 24px; font-weight: bold; color: #dc3545;'>{$failed}</div>";
        echo "<div style='font-size: 14px; color: #666;'>FALLIDAS</div>";
        echo "</div>";
        
        echo "<div style='text-align: center; padding: 15px; background: white; border-radius: 8px;'>";
        echo "<div style='font-size: 24px; font-weight: bold; color: " . ($successRate >= 80 ? '#28a745' : ($successRate >= 60 ? '#ffc107' : '#dc3545')) . ";'>{$successRate}%</div>";
        echo "<div style='font-size: 14px; color: #666;'>TASA DE ÉXITO</div>";
        echo "</div>";
        
        echo "</div>";
        
        if ($failed > 0) {
            echo "<h4 style='margin-top: 20px; color: #dc3545;'>❌ Pruebas Fallidas:</h4>";
            echo "<div class='code-block'>";
            foreach ($this->results as $result) {
                if (!$result['success']) {
                    echo "• {$result['test']}: {$result['message']}<br>";
                }
            }
            echo "</div>";
        }
        
        echo "<div style='margin-top: 20px; text-align: center;'>";
        echo "<a href='?' class='btn'>🔄 Ejecutar Nuevamente</a>";
        echo "<a href='test_endpoints.json.php' class='btn'>📄 Exportar JSON</a>";
        echo "<a href='endpoint_tester.html' class='btn'>🌐 Usar Herramienta Web</a>";
        echo "</div>";
        
        echo "</div>";
        
        // Save results to session for JSON export
        $_SESSION['test_results'] = $this->results;
    }
}

// Ejecutar las pruebas
$tester = new EndpointTester();
$tester->runAllTests();
?>