<?php
/**
 * STYLOFITNESS - Script de Verificación de Base de Datos Corregido
 * Verificación específica para conexión con la BD real
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar variables de entorno desde .env
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
    return true;
}

// Cargar configuración
$envLoaded = loadEnv(__DIR__ . '/.env');

class DatabaseVerifier {
    private $db = null;
    private $results = [];
    
    public function __construct() {
        $this->displayHeader();
    }
    
    public function verify() {
        $this->testConnection();
        $this->verifyTables();
        $this->verifyData();
        $this->testQueries();
        $this->displayResults();
    }
    
    private function testConnection() {
        echo "<div class='section'>";
        echo "<div class='section-header'>🔌 Prueba de Conexión</div>";
        echo "<div class='section-content'>";
        
        // Mostrar configuración actual
        $dbHost = $_ENV['DB_HOST'] ?? 'localhost';
        $dbPort = $_ENV['DB_PORT'] ?? '3306';
        $dbName = $_ENV['DB_DATABASE'] ?? 'stylofitness_gym';
        $dbUser = $_ENV['DB_USERNAME'] ?? 'root';
        $dbPass = $_ENV['DB_PASSWORD'] ?? '';
        
        echo "<div class='config-info'>";
        echo "<p><strong>Host:</strong> {$dbHost}:{$dbPort}</p>";
        echo "<p><strong>Base de datos:</strong> {$dbName}</p>";
        echo "<p><strong>Usuario:</strong> {$dbUser}</p>";
        echo "<p><strong>Archivo .env:</strong> " . ($GLOBALS['envLoaded'] ? '✅ Cargado' : '❌ No encontrado') . "</p>";
        echo "</div>";
        
        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->db = new PDO($dsn, $dbUser, $dbPass, $options);
            
            $this->addResult('✅ Conexión Exitosa', 'Conectado a la base de datos MySQL', 'success');
            
            // Verificar información de la base de datos
            $version = $this->db->query("SELECT VERSION() as version")->fetch();
            $this->addResult('Versión MySQL', $version['version'], 'info');
            
            $currentDb = $this->db->query("SELECT DATABASE() as db_name")->fetch();
            $this->addResult('Base de datos actual', $currentDb['db_name'], 'info');
            
        } catch (PDOException $e) {
            $this->addResult('❌ Error de Conexión', $e->getMessage(), 'error');
            echo "</div></div>";
            return false;
        }
        
        echo "</div></div>";
        return true;
    }
    
    private function verifyTables() {
        if (!$this->db) return;
        
        echo "<div class='section'>";
        echo "<div class='section-header'>📋 Verificación de Tablas</div>";
        echo "<div class='section-content'>";
        
        try {
            // Obtener todas las tablas
            $stmt = $this->db->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $this->addResult('Total de tablas encontradas', count($tables), 'info');
            
            // Tablas esperadas
            $expectedTables = [
                'users' => 'Usuarios del sistema',
                'gyms' => 'Sedes del gimnasio',
                'exercises' => 'Ejercicios disponibles',
                'exercise_categories' => 'Categorías de ejercicios',
                'routines' => 'Rutinas de entrenamiento',
                'routine_exercises' => 'Ejercicios en rutinas',
                'products' => 'Productos de la tienda',
                'product_categories' => 'Categorías de productos',
                'orders' => 'Órdenes de compra',
                'order_items' => 'Items de órdenes',
                'group_classes' => 'Clases grupales',
                'class_schedules' => 'Horarios de clases',
                'class_bookings' => 'Reservas de clases',
                'security_tokens' => 'Tokens de seguridad',
                'user_activity_logs' => 'Logs de actividad',
                'system_settings' => 'Configuraciones del sistema',
                'notifications' => 'Notificaciones',
                'media_files' => 'Archivos multimedia'
            ];
            
            echo "<div class='table-grid'>";
            foreach ($expectedTables as $table => $description) {
                $exists = in_array($table, $tables);
                $count = 0;
                
                if ($exists) {
                    try {
                        $countStmt = $this->db->query("SELECT COUNT(*) FROM `{$table}`");
                        $count = $countStmt->fetchColumn();
                    } catch (Exception $e) {
                        $count = 'Error';
                    }
                }
                
                echo "<div class='table-item " . ($exists ? 'success' : 'error') . "'>";
                echo "<div class='table-name'>" . ($exists ? '✅' : '❌') . " {$table}</div>";
                echo "<div class='table-desc'>{$description}</div>";
                if ($exists) {
                    echo "<div class='table-count'>{$count} registros</div>";
                }
                echo "</div>";
                
                $this->addResult(
                    "Tabla: {$table}",
                    $exists ? "Existe ({$count} registros)" : 'No encontrada',
                    $exists ? 'success' : 'error'
                );
            }
            echo "</div>";
            
        } catch (Exception $e) {
            $this->addResult('Error verificando tablas', $e->getMessage(), 'error');
        }
        
        echo "</div></div>";
    }
    
    private function verifyData() {
        if (!$this->db) return;
        
        echo "<div class='section'>";
        echo "<div class='section-header'>📊 Verificación de Datos</div>";
        echo "<div class='section-content'>";
        
        $dataChecks = [
            'users' => 'SELECT COUNT(*) FROM users WHERE is_active = 1',
            'gyms' => 'SELECT COUNT(*) FROM gyms WHERE is_active = 1',
            'exercises' => 'SELECT COUNT(*) FROM exercises WHERE is_active = 1',
            'exercise_categories' => 'SELECT COUNT(*) FROM exercise_categories WHERE is_active = 1',
            'routines' => 'SELECT COUNT(*) FROM routines WHERE is_active = 1',
            'products' => 'SELECT COUNT(*) FROM products WHERE is_active = 1',
            'product_categories' => 'SELECT COUNT(*) FROM product_categories WHERE is_active = 1'
        ];
        
        echo "<div class='data-grid'>";
        foreach ($dataChecks as $table => $query) {
            try {
                $count = $this->db->query($query)->fetchColumn();
                echo "<div class='data-item " . ($count > 0 ? 'success' : 'warning') . "'>";
                echo "<div class='data-table'>{$table}</div>";
                echo "<div class='data-count'>{$count} activos</div>";
                echo "</div>";
                
                $this->addResult(
                    "Datos en {$table}",
                    "{$count} registros activos",
                    $count > 0 ? 'success' : 'warning'
                );
            } catch (Exception $e) {
                echo "<div class='data-item error'>";
                echo "<div class='data-table'>{$table}</div>";
                echo "<div class='data-count'>Error</div>";
                echo "</div>";
                
                $this->addResult("Error en {$table}", $e->getMessage(), 'error');
            }
        }
        echo "</div>";
        
        echo "</div></div>";
    }
    
    private function testQueries() {
        if (!$this->db) return;
        
        echo "<div class='section'>";
        echo "<div class='section-header'>🔍 Pruebas de Consultas</div>";
        echo "<div class='section-content'>";
        
        $testQueries = [
            'Usuarios por rol' => "SELECT role, COUNT(*) as count FROM users GROUP BY role",
            'Ejercicios por categoría' => "SELECT ec.name, COUNT(e.id) as count FROM exercise_categories ec LEFT JOIN exercises e ON ec.id = e.category_id GROUP BY ec.id",
            'Productos por categoría' => "SELECT pc.name, COUNT(p.id) as count FROM product_categories pc LEFT JOIN products p ON pc.id = p.category_id GROUP BY pc.id",
            'Rutinas por dificultad' => "SELECT difficulty_level, COUNT(*) as count FROM routines GROUP BY difficulty_level"
        ];
        
        foreach ($testQueries as $testName => $query) {
            try {
                $results = $this->db->query($query)->fetchAll();
                
                echo "<div class='query-test success'>";
                echo "<div class='query-name'>✅ {$testName}</div>";
                echo "<div class='query-results'>";
                
                foreach ($results as $row) {
                    $values = array_values($row);
                    echo "<span class='result-item'>{$values[0]}: {$values[1]}</span>";
                }
                
                echo "</div>";
                echo "</div>";
                
                $this->addResult("Query: {$testName}", count($results) . " resultados", 'success');
                
            } catch (Exception $e) {
                echo "<div class='query-test error'>";
                echo "<div class='query-name'>❌ {$testName}</div>";
                echo "<div class='query-error'>{$e->getMessage()}</div>";
                echo "</div>";
                
                $this->addResult("Query Error: {$testName}", $e->getMessage(), 'error');
            }
        }
        
        echo "</div></div>";
    }
    
    private function addResult($test, $result, $type) {
        $this->results[] = [
            'test' => $test,
            'result' => $result,
            'type' => $type
        ];
    }
    
    private function displayResults() {
        $total = count($this->results);
        $success = count(array_filter($this->results, function($r) { return $r['type'] === 'success'; }));
        $errors = count(array_filter($this->results, function($r) { return $r['type'] === 'error'; }));
        $successRate = $total > 0 ? round(($success / $total) * 100, 1) : 0;
        
        echo "<div class='summary'>";
        echo "<h2>📈 Resumen Final</h2>";
        echo "<div class='summary-stats'>";
        echo "<div class='stat-card'>";
        echo "<div class='stat-number' style='color: #FF6B00;'>{$total}</div>";
        echo "<div class='stat-label'>Total Pruebas</div>";
        echo "</div>";
        echo "<div class='stat-card'>";
        echo "<div class='stat-number' style='color: #28a745;'>{$success}</div>";
        echo "<div class='stat-label'>Exitosas</div>";
        echo "</div>";
        echo "<div class='stat-card'>";
        echo "<div class='stat-number' style='color: #dc3545;'>{$errors}</div>";
        echo "<div class='stat-label'>Errores</div>";
        echo "</div>";
        echo "<div class='stat-card'>";
        $rateColor = $successRate >= 80 ? '#28a745' : ($successRate >= 60 ? '#ffc107' : '#dc3545');
        echo "<div class='stat-number' style='color: {$rateColor};'>{$successRate}%</div>";
        echo "<div class='stat-label'>Tasa de Éxito</div>";
        echo "</div>";
        echo "</div>";
        
        if ($errors > 0) {
            echo "<div class='error-details'>";
            echo "<h3>❌ Errores Encontrados:</h3>";
            foreach ($this->results as $result) {
                if ($result['type'] === 'error') {
                    echo "<div class='error-item'>• {$result['test']}: {$result['result']}</div>";
                }
            }
            echo "</div>";
        }
        
        echo "<div class='actions'>";
        echo "<a href='?' class='btn'>🔄 Verificar Nuevamente</a>";
        echo "<a href='system_diagnostic.php' class='btn'>📊 Diagnóstico Completo</a>";
        echo "<a href='check_routes.php' class='btn'>🌐 Verificar Rutas</a>";
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
            <title>StyloFitness - Verificación de Base de Datos</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh; 
                    padding: 20px;
                }
                .container { 
                    max-width: 1200px; 
                    margin: 0 auto; 
                    background: white; 
                    border-radius: 20px; 
                    overflow: hidden;
                    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
                }
                .header { 
                    background: linear-gradient(135deg, #FF6B00, #E55A00); 
                    color: white; 
                    padding: 40px; 
                    text-align: center;
                }
                .header h1 { font-size: 2.5rem; margin-bottom: 10px; }
                .header p { font-size: 1.1rem; opacity: 0.9; }
                .section { margin: 30px; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
                .section-header { 
                    background: linear-gradient(135deg, #6c757d, #495057); 
                    color: white; 
                    padding: 20px; 
                    font-size: 1.3rem; 
                    font-weight: 600;
                }
                .section-content { padding: 25px; background: white; }
                .config-info { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
                .config-info p { margin: 5px 0; }
                .table-grid, .data-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin-top: 15px; }
                .table-item, .data-item { 
                    padding: 15px; 
                    border-radius: 8px; 
                    border-left: 4px solid transparent;
                }
                .table-item.success, .data-item.success { background: #d4edda; border-left-color: #28a745; }
                .table-item.error, .data-item.error { background: #f8d7da; border-left-color: #dc3545; }
                .table-item.warning, .data-item.warning { background: #fff3cd; border-left-color: #ffc107; }
                .table-name, .data-table { font-weight: bold; margin-bottom: 5px; }
                .table-desc, .data-count { font-size: 14px; color: #666; }
                .query-test { 
                    background: #f8f9fa; 
                    padding: 15px; 
                    border-radius: 8px; 
                    margin-bottom: 15px;
                    border-left: 4px solid transparent;
                }
                .query-test.success { border-left-color: #28a745; }
                .query-test.error { border-left-color: #dc3545; }
                .query-name { font-weight: bold; margin-bottom: 8px; }
                .query-results { display: flex; flex-wrap: wrap; gap: 10px; }
                .result-item { 
                    background: white; 
                    padding: 4px 8px; 
                    border-radius: 4px; 
                    font-size: 13px;
                    border: 1px solid #dee2e6;
                }
                .query-error { color: #dc3545; font-size: 14px; }
                .summary { 
                    background: linear-gradient(135deg, #e9ecef, #f8f9fa); 
                    padding: 30px; 
                    margin: 30px;
                    border-radius: 15px;
                }
                .summary h2 { margin-bottom: 20px; color: #495057; }
                .summary-stats { 
                    display: grid; 
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
                    gap: 20px; 
                    margin-bottom: 25px;
                }
                .stat-card { 
                    background: white; 
                    padding: 20px; 
                    border-radius: 10px; 
                    text-align: center;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                }
                .stat-number { font-size: 2rem; font-weight: bold; margin-bottom: 8px; }
                .stat-label { color: #6c757d; font-size: 14px; text-transform: uppercase; }
                .error-details { 
                    background: #f8d7da; 
                    padding: 20px; 
                    border-radius: 8px; 
                    margin-bottom: 25px;
                    border-left: 4px solid #dc3545;
                }
                .error-details h3 { margin-bottom: 15px; color: #721c24; }
                .error-item { margin: 8px 0; color: #721c24; }
                .actions { text-align: center; }
                .btn { 
                    display: inline-block; 
                    padding: 12px 24px; 
                    margin: 0 10px; 
                    background: #FF6B00; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 8px; 
                    font-weight: 600;
                    transition: all 0.3s ease;
                }
                .btn:hover { 
                    background: #E55A00; 
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(255, 107, 0, 0.3);
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🗄️ StyloFitness</h1>
                    <p>Verificación de Base de Datos - Diagnóstico de Conectividad</p>
                </div>
        <?php
    }
}

// Ejecutar verificación
$verifier = new DatabaseVerifier();
$verifier->verify();

echo "</div>";
echo "</body>";
echo "</html>";
?>