<?php
/**
 * STYLOFITNESS - Diagnóstico del Sistema Optimizado
 * Versión ligera que no agota la memoria
 */

// Configuración de memoria y tiempo
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 60);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inicializar
session_start();
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');

class LightweightDiagnostic {
    private $results = [];
    private $startTime;
    
    public function __construct() {
        $this->startTime = microtime(true);
        $this->displayHeader();
    }
    
    public function runDiagnostic() {
        echo "<div class='container'>";
        
        $this->checkBasicEnvironment();
        $this->checkDatabase();
        $this->checkFiles();
        $this->displaySummary();
        
        echo "</div>";
        $this->displayFooter();
    }
    
    private function checkBasicEnvironment() {
        echo "<div class='section'>";
        echo "<div class='section-header'>🌐 Entorno Básico</div>";
        echo "<div class='section-content'>";
        
        // PHP Version
        $phpVersion = PHP_VERSION;
        $this->addResult(
            "Versión de PHP",
            version_compare($phpVersion, '7.4.0', '>='),
            $phpVersion,
            "PHP 7.4+ requerido"
        );
        
        // Extensions básicas
        $extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
        foreach ($extensions as $ext) {
            $this->addResult(
                "Extensión: {$ext}",
                extension_loaded($ext),
                extension_loaded($ext) ? 'Instalada' : 'Faltante',
                "Extensión PHP requerida"
            );
        }
        
        // Memory limit
        $memLimit = ini_get('memory_limit');
        $this->addResult(
            "Límite de memoria",
            true,
            $memLimit,
            "Configuración actual de PHP"
        );
        
        echo "</div></div>";
    }
    
    private function checkDatabase() {
        echo "<div class='section'>";
        echo "<div class='section-header'>🗄️ Base de Datos</div>";
        echo "<div class='section-content'>";
        
        try {
            // Cargar configuración manualmente
            $envFile = ROOT_PATH . '/.env';
            $dbConfig = $this->loadDbConfig($envFile);
            
            echo "<div class='config-display'>";
            echo "<p><strong>Host:</strong> {$dbConfig['host']}:{$dbConfig['port']}</p>";
            echo "<p><strong>Base de datos:</strong> {$dbConfig['database']}</p>";
            echo "<p><strong>Usuario:</strong> {$dbConfig['username']}</p>";
            echo "</div>";
            
            // Intentar conexión directa
            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            $this->addResult(
                "Conexión MySQL",
                true,
                "Conectado exitosamente",
                "Base de datos accesible"
            );
            
            // Información básica
            $version = $pdo->query("SELECT VERSION() as v")->fetch();
            $this->addResult(
                "Versión MySQL",
                true,
                $version['v'],
                "Servidor de base de datos"
            );
            
            // Contar tablas
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            $this->addResult(
                "Tablas encontradas",
                count($tables) > 0,
                count($tables) . " tablas",
                "Estructura de base de datos"
            );
            
            // Verificar tablas principales
            $mainTables = ['users', 'gyms', 'exercises', 'routines', 'products'];
            $existingMain = array_intersect($mainTables, $tables);
            $this->addResult(
                "Tablas principales",
                count($existingMain) >= 3,
                count($existingMain) . "/" . count($mainTables) . " encontradas",
                "Tablas críticas del sistema"
            );
            
        } catch (Exception $e) {
            $this->addResult(
                "Error de conexión",
                false,
                $e->getMessage(),
                "Verificar configuración de BD"
            );
        }
        
        echo "</div></div>";
    }
    
    private function checkFiles() {
        echo "<div class='section'>";
        echo "<div class='section-header'>📁 Archivos del Sistema</div>";
        echo "<div class='section-content'>";
        
        $criticalFiles = [
            'index.php' => 'Archivo principal',
            '.htaccess' => 'Configuración Apache',
            '.env' => 'Variables de entorno',
            'app/Config/Database.php' => 'Configuración BD',
            'app/Controllers/ApiController.php' => 'API Controller',
            'app/Models/User.php' => 'Modelo Usuario'
        ];
        
        foreach ($criticalFiles as $file => $desc) {
            $exists = file_exists(ROOT_PATH . '/' . $file);
            $this->addResult(
                $desc,
                $exists,
                $exists ? 'Encontrado' : 'Faltante',
                "Archivo: {$file}"
            );
        }
        
        // Verificar permisos de escritura
        $writableDirs = ['public/uploads', 'logs'];
        foreach ($writableDirs as $dir) {
            $path = ROOT_PATH . '/' . $dir;
            $writable = is_dir($path) && is_writable($path);
            $this->addResult(
                "Permisos: {$dir}",
                $writable,
                $writable ? 'Escribible' : 'Sin permisos',
                "Directorio debe ser escribible"
            );
        }
        
        echo "</div></div>";
    }
    
    private function loadDbConfig($envFile) {
        $config = [
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'stylofitness_gym',
            'username' => 'root',
            'password' => ''
        ];
        
        if (file_exists($envFile)) {
            $content = file_get_contents($envFile);
            if (preg_match('/DB_HOST=(.*)/', $content, $m)) $config['host'] = trim($m[1]);
            if (preg_match('/DB_PORT=(.*)/', $content, $m)) $config['port'] = trim($m[1]);
            if (preg_match('/DB_DATABASE=(.*)/', $content, $m)) $config['database'] = trim($m[1]);
            if (preg_match('/DB_USERNAME=(.*)/', $content, $m)) $config['username'] = trim($m[1]);
            if (preg_match('/DB_PASSWORD=(.*)/', $content, $m)) $config['password'] = trim($m[1]);
        }
        
        return $config;
    }
    
    private function addResult($test, $success, $result, $details = '') {
        $this->results[] = [
            'test' => $test,
            'success' => $success,
            'result' => $result,
            'details' => $details
        ];
        
        $statusClass = $success ? 'success' : 'error';
        $icon = $success ? '✅' : '❌';
        
        echo "<div class='test-item {$statusClass}'>";
        echo "<div class='test-info'>";
        echo "<div class='test-name'>{$test}</div>";
        if ($details) {
            echo "<div class='test-details'>{$details}</div>";
        }
        echo "</div>";
        echo "<div class='test-result'>{$icon} {$result}</div>";
        echo "</div>";
    }
    
    private function displaySummary() {
        $total = count($this->results);
        $success = count(array_filter($this->results, function($r) { return $r['success']; }));
        $failed = $total - $success;
        $successRate = $total > 0 ? round(($success / $total) * 100, 1) : 0;
        $executionTime = round(microtime(true) - $this->startTime, 2);
        
        echo "<div class='summary'>";
        echo "<h2>📊 Resumen del Diagnóstico</h2>";
        
        echo "<div class='stats-grid'>";
        echo "<div class='stat-card'><div class='stat-number' style='color: #FF6B00;'>{$total}</div><div class='stat-label'>Total</div></div>";
        echo "<div class='stat-card'><div class='stat-number' style='color: #28a745;'>{$success}</div><div class='stat-label'>Exitosos</div></div>";
        echo "<div class='stat-card'><div class='stat-number' style='color: #dc3545;'>{$failed}</div><div class='stat-label'>Fallidos</div></div>";
        echo "<div class='stat-card'><div class='stat-number' style='color: " . ($successRate >= 80 ? '#28a745' : '#ffc107') . ";'>{$successRate}%</div><div class='stat-label'>Éxito</div></div>";
        echo "<div class='stat-card'><div class='stat-number' style='color: #6c757d;'>{$executionTime}s</div><div class='stat-label'>Tiempo</div></div>";
        echo "</div>";
        
        if ($failed > 0) {
            echo "<div class='errors'>";
            echo "<h3>❌ Problemas Encontrados:</h3>";
            foreach ($this->results as $result) {
                if (!$result['success']) {
                    echo "<div class='error-item'>• {$result['test']}: {$result['result']}</div>";
                }
            }
            echo "</div>";
        }
        
        echo "<div class='actions'>";
        echo "<a href='?' class='btn'>🔄 Verificar Nuevamente</a>";
        echo "<a href='quick_diagnosis.php' class='btn'>⚡ Diagnóstico Rápido</a>";
        echo "<a href='db_verification.php' class='btn'>🗄️ Verificar BD</a>";
        echo "</div>";
        
        echo "</div>";
    }
    
    private function displayHeader() {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>StyloFitness - Diagnóstico Optimizado</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
                .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #FF6B00, #E55A00); color: white; padding: 30px; text-align: center; }
                .header h1 { font-size: 2rem; margin-bottom: 10px; }
                .header p { opacity: 0.9; }
                .section { margin: 20px; }
                .section-header { background: #6c757d; color: white; padding: 15px; font-weight: bold; border-radius: 8px 8px 0 0; }
                .section-content { background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; }
                .config-display { background: white; padding: 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #17a2b8; }
                .config-display p { margin: 5px 0; }
                .test-item { display: flex; justify-content: space-between; align-items: center; padding: 12px; margin-bottom: 8px; border-radius: 6px; }
                .test-item.success { background: #d4edda; border-left: 4px solid #28a745; }
                .test-item.error { background: #f8d7da; border-left: 4px solid #dc3545; }
                .test-name { font-weight: bold; }
                .test-details { font-size: 12px; color: #666; margin-top: 2px; }
                .test-result { font-weight: bold; white-space: nowrap; }
                .summary { background: #e9ecef; padding: 25px; margin: 20px; border-radius: 10px; }
                .summary h2 { margin-bottom: 20px; color: #495057; }
                .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px; }
                .stat-card { background: white; padding: 15px; border-radius: 8px; text-align: center; }
                .stat-number { font-size: 1.8rem; font-weight: bold; margin-bottom: 5px; }
                .stat-label { color: #6c757d; font-size: 12px; text-transform: uppercase; }
                .errors { background: #f8d7da; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545; }
                .errors h3 { margin-bottom: 10px; color: #721c24; }
                .error-item { margin: 5px 0; color: #721c24; }
                .actions { text-align: center; }
                .btn { display: inline-block; padding: 10px 20px; margin: 5px; background: #FF6B00; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
                .btn:hover { background: #E55A00; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔧 StyloFitness</h1>
                    <p>Diagnóstico del Sistema - Versión Optimizada</p>
                </div>
        <?php
    }
    
    private function displayFooter() {
        echo "</div></body></html>";
    }
}

// Ejecutar diagnóstico
$diagnostic = new LightweightDiagnostic();
$diagnostic->runDiagnostic();
?>