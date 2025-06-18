<?php
/**
 * STYLOFITNESS - Verificador de Rutas en Tiempo Real
 * Script para probar endpoints específicos
 */

// Configuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inicializar el sistema
session_start();
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');

// Cargar dependencias básicas
if (file_exists(APP_PATH . '/Config/Database.php')) {
    require_once APP_PATH . '/Config/Database.php';
}

if (file_exists(APP_PATH . '/Helpers/AppHelper.php')) {
    require_once APP_PATH . '/Helpers/AppHelper.php';
}

class RouteChecker {
    private $baseUrl;
    private $results = [];
    
    public function __construct() {
        // Detectar URL base automáticamente
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['REQUEST_URI']);
        $this->baseUrl = $protocol . '://' . $host . $path;
        
        // Limpiar la URL
        $this->baseUrl = rtrim($this->baseUrl, '/');
        
        // Si hay un parámetro de URL, usarlo
        if (isset($_GET['base_url']) && !empty($_GET['base_url'])) {
            $this->baseUrl = rtrim($_GET['base_url'], '/');
        }
    }
    
    public function checkRoutes() {
        $routes = [
            // Rutas públicas
            ['GET', '/', 'Página principal'],
            ['GET', '/login', 'Página de login'],
            ['GET', '/register', 'Página de registro'],
            ['GET', '/store', 'Tienda online'],
            ['GET', '/classes', 'Clases grupales'],
            
            // API Routes (sin autenticación)
            ['GET', '/api/products', 'API - Productos'],
            ['GET', '/api/products/featured', 'API - Productos destacados'],
            ['GET', '/api/exercises/categories', 'API - Categorías de ejercicios'],
            
            // Rutas que requieren autenticación (deberían devolver 401 o redirigir)
            ['GET', '/dashboard', 'Dashboard (requiere auth)'],
            ['GET', '/routines', 'Rutinas (requiere auth)'],
            ['GET', '/api/routines', 'API - Rutinas (requiere auth)'],
            ['GET', '/api/users', 'API - Usuarios (requiere auth)'],
            ['GET', '/admin', 'Panel admin (requiere auth)'],
        ];
        
        echo $this->renderHeader();
        
        foreach ($routes as $route) {
            $this->testRoute($route[0], $route[1], $route[2]);
        }
        
        echo $this->renderSummary();
        echo $this->renderFooter();
    }
    
    private function testRoute($method, $path, $description) {
        $url = $this->baseUrl . $path;
        $startTime = microtime(true);
        
        // Configurar contexto para la petición
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => [
                    'User-Agent: StyloFitness Route Checker',
                    'Accept: text/html,application/json,*/*',
                    'Connection: close'
                ],
                'timeout' => 10,
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);
        
        // Obtener headers de respuesta
        $httpCode = 0;
        $contentType = 'unknown';
        
        if (isset($http_response_header)) {
            $statusLine = $http_response_header[0] ?? '';
            if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $statusLine, $matches)) {
                $httpCode = (int)$matches[1];
            }
            
            foreach ($http_response_header as $header) {
                if (stripos($header, 'content-type:') === 0) {
                    $contentType = trim(substr($header, 13));
                    break;
                }
            }
        }
        
        // Determinar el estado
        $status = 'error';
        $statusText = 'Error';
        $statusClass = 'status-error';
        
        if ($response !== false) {
            if ($httpCode >= 200 && $httpCode < 300) {
                $status = 'success';
                $statusText = "✅ {$httpCode}";
                $statusClass = 'status-success';
            } elseif ($httpCode >= 300 && $httpCode < 400) {
                $status = 'redirect';
                $statusText = "↗️ {$httpCode}";
                $statusClass = 'status-warning';
            } elseif ($httpCode >= 400 && $httpCode < 500) {
                $status = 'client_error';
                $statusText = "⚠️ {$httpCode}";
                $statusClass = 'status-warning';
            } elseif ($httpCode >= 500) {
                $status = 'server_error';
                $statusText = "❌ {$httpCode}";
                $statusClass = 'status-error';
            }
        } else {
            $statusText = "💥 No response";
        }
        
        // Analizar el contenido de la respuesta
        $responseInfo = '';
        if ($response) {
            $responseLength = strlen($response);
            $responseInfo = "Tamaño: " . $this->formatBytes($responseLength);
            
            // Detectar tipo de contenido
            if (strpos($contentType, 'application/json') !== false) {
                $responseInfo .= " | JSON";
                $jsonData = json_decode($response, true);
                if ($jsonData && isset($jsonData['error'])) {
                    $responseInfo .= " | Error: " . $jsonData['error'];
                }
            } elseif (strpos($contentType, 'text/html') !== false) {
                $responseInfo .= " | HTML";
                if (strpos($response, '<title>') !== false) {
                    preg_match('/<title>(.*?)<\/title>/i', $response, $matches);
                    if (isset($matches[1])) {
                        $responseInfo .= " | Título: " . trim($matches[1]);
                    }
                }
            }
        }
        
        // Guardar resultado
        $this->results[] = [
            'method' => $method,
            'path' => $path,
            'description' => $description,
            'url' => $url,
            'status' => $status,
            'httpCode' => $httpCode,
            'responseTime' => $responseTime,
            'contentType' => $contentType,
            'responseInfo' => $responseInfo,
            'statusClass' => $statusClass,
            'statusText' => $statusText
        ];
        
        // Renderizar fila
        echo $this->renderRouteRow($this->results[count($this->results) - 1]);
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
    
    private function renderHeader() {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>StyloFitness - Verificador de Rutas</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
                .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #FF6B00, #E55A00); color: white; padding: 30px; text-align: center; }
                .header h1 { font-size: 2.2rem; margin-bottom: 10px; }
                .header p { opacity: 0.9; }
                .controls { padding: 20px; background: #f8f9fa; display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
                .form-group { display: flex; align-items: center; gap: 10px; }
                .form-group input { padding: 8px 12px; border: 2px solid #ddd; border-radius: 6px; min-width: 300px; }
                .btn { padding: 10px 20px; background: #FF6B00; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; }
                .btn:hover { background: #E55A00; }
                .table-container { overflow-x: auto; }
                .route-table { width: 100%; border-collapse: collapse; }
                .route-table th { background: #6c757d; color: white; padding: 12px; text-align: left; font-weight: 600; }
                .route-table td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: top; }
                .route-table tr:hover { background: #f8f9fa; }
                .method-badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
                .method-get { background: #d1ecf1; color: #0c5460; }
                .method-post { background: #d4edda; color: #155724; }
                .route-url { font-family: monospace; font-size: 13px; word-break: break-all; }
                .status-success { color: #28a745; font-weight: bold; }
                .status-error { color: #dc3545; font-weight: bold; }
                .status-warning { color: #ffc107; font-weight: bold; }
                .response-time { font-size: 12px; color: #666; }
                .response-info { font-size: 11px; color: #666; margin-top: 5px; }
                .summary { padding: 20px; background: #e9ecef; }
                .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 15px; }
                .stat-card { background: white; padding: 15px; border-radius: 8px; text-align: center; }
                .stat-number { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
                .stat-label { font-size: 12px; color: #666; text-transform: uppercase; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌐 StyloFitness - Verificador de Rutas</h1>
                    <p>Prueba en tiempo real todas las rutas y endpoints del sistema</p>
                </div>
                
                <div class='controls'>
                    <form method='get' style='display: flex; gap: 15px; align-items: center; flex-wrap: wrap;'>
                        <div class='form-group'>
                            <label for='base_url'>URL Base:</label>
                            <input type='url' id='base_url' name='base_url' value='{$this->baseUrl}' placeholder='http://localhost/stylofitness'>
                        </div>
                        <button type='submit' class='btn'>🔄 Verificar Rutas</button>
                    </form>
                    <a href='test_endpoints.php' class='btn'>📊 Pruebas Completas</a>
                </div>
                
                <div class='table-container'>
                    <table class='route-table'>
                        <thead>
                            <tr>
                                <th>Método</th>
                                <th>Ruta</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Tiempo</th>
                                <th>Información</th>
                            </tr>
                        </thead>
                        <tbody>
        ";
    }
    
    private function renderRouteRow($result) {
        $methodClass = 'method-' . strtolower($result['method']);
        
        return "
            <tr>
                <td><span class='method-badge {$methodClass}'>{$result['method']}</span></td>
                <td class='route-url'>{$result['path']}</td>
                <td>{$result['description']}</td>
                <td class='{$result['statusClass']}'>{$result['statusText']}</td>
                <td class='response-time'>{$result['responseTime']}ms</td>
                <td>
                    <div>{$result['contentType']}</div>
                    <div class='response-info'>{$result['responseInfo']}</div>
                </td>
            </tr>
        ";
    }
    
    private function renderSummary() {
        $total = count($this->results);
        $success = count(array_filter($this->results, function($r) { return $r['status'] === 'success'; }));
        $errors = count(array_filter($this->results, function($r) { return $r['status'] === 'error' || $r['status'] === 'server_error'; }));
        $warnings = count(array_filter($this->results, function($r) { return $r['status'] === 'redirect' || $r['status'] === 'client_error'; }));
        $avgTime = $total > 0 ? round(array_sum(array_column($this->results, 'responseTime')) / $total, 2) : 0;
        
        return "
                        </tbody>
                    </table>
                </div>
                
                <div class='summary'>
                    <h3>📈 Resumen de Verificación</h3>
                    <div class='stats-grid'>
                        <div class='stat-card'>
                            <div class='stat-number' style='color: #FF6B00;'>{$total}</div>
                            <div class='stat-label'>Total Rutas</div>
                        </div>
                        <div class='stat-card'>
                            <div class='stat-number' style='color: #28a745;'>{$success}</div>
                            <div class='stat-label'>Exitosas</div>
                        </div>
                        <div class='stat-card'>
                            <div class='stat-number' style='color: #ffc107;'>{$warnings}</div>
                            <div class='stat-label'>Advertencias</div>
                        </div>
                        <div class='stat-card'>
                            <div class='stat-number' style='color: #dc3545;'>{$errors}</div>
                            <div class='stat-label'>Errores</div>
                        </div>
                        <div class='stat-card'>
                            <div class='stat-number' style='color: #6c757d;'>{$avgTime}ms</div>
                            <div class='stat-label'>Tiempo Promedio</div>
                        </div>
                    </div>
                </div>
        ";
    }
    
    private function renderFooter() {
        return "
            </div>
            <script>
                // Auto-refresh cada 30 segundos si se especifica
                if (new URLSearchParams(window.location.search).get('auto_refresh') === '1') {
                    setTimeout(() => {
                        window.location.reload();
                    }, 30000);
                }
            </script>
        </body>
        </html>
        ";
    }
}

// Ejecutar verificación
$checker = new RouteChecker();
$checker->checkRoutes();
?>