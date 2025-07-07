<?php
/**
 * STYLOFITNESS - Aplicación Web Profesional para Gimnasios
 * Página principal con enrutamiento MVC mejorado
 */

// Configuración de errores según el entorno
if (defined('APP_ENV') && APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

// Configuración de zona horaria
date_default_timezone_set('America/Lima');

// Iniciar sesión (si no está iniciada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir constantes de la aplicación
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Incluir configuración
require_once APP_PATH . '/Config/Database.php';
require_once APP_PATH . '/Config/App.php';
require_once APP_PATH . '/Config/RoutineConfig.php';

// Incluir helpers
require_once APP_PATH . '/Helpers/AppHelper.php';
require_once APP_PATH . '/Helpers/RoutineHelper.php';

// Usar las clases necesarias
use StyleFitness\Config\Database;

// Autoloader simple para clases
spl_autoload_register(function ($class) {
    // Extraer solo el nombre de la clase del namespace completo
    $className = basename(str_replace('\\', '/', $class));
    
    $paths = [
        APP_PATH . '/Controllers/',
        APP_PATH . '/Models/',
        APP_PATH . '/Helpers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Validación automática de remember token
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    try {
        $db = Database::getInstance()->getConnection();
        $token = $_COOKIE['remember_token'];
        
        // Buscar el token en la tabla security_tokens
        $stmt = $db->prepare("SELECT u.* FROM users u 
                             INNER JOIN security_tokens st ON u.id = st.user_id 
                             WHERE st.token = ? AND st.type = 'remember_me' AND st.expires_at > NOW()");
        $stmt->execute([hash('sha256', $token)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Regenerar sesión por seguridad
            session_regenerate_id(true);
            
            // Establecer datos de sesión usando la estructura correcta
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_data'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'role' => $user['role'],
                'gym_id' => $user['gym_id'],
                'profile_image' => $user['profile_image'],
                'membership_type' => $user['membership_type'],
                'membership_expires' => $user['membership_expires'],
            ];
            
            // Actualizar último acceso
            $updateStmt = $db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            error_log("Auto-login successful for user: " . $user['email']);
        } else {
            // Token inválido o expirado, eliminar cookie
            setcookie('remember_token', '', time() - 3600, '/');
            error_log("Invalid or expired remember token, cookie cleared");
        }
    } catch (Exception $e) {
        error_log("Remember token validation error: " . $e->getMessage());
        // En caso de error, eliminar cookie por seguridad
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

// Sistema de enrutamiento mejorado
class Router {
    private $routes = [];
    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function put($path, $callback) {
        $this->routes['PUT'][$path] = $callback;
    }
    
    public function delete($path, $callback) {
        $this->routes['DELETE'][$path] = $callback;
    }
    
    public function resolve() {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Remover el directorio base si existe
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('\\', '/', dirname($scriptName));
        
        // Si estamos en un subdirectorio, removerlo de la URI
        if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        // Limpiar la URI
        $requestUri = '/' . ltrim($requestUri, '/');
        $requestUri = $requestUri === '/' ? '/' : rtrim($requestUri, '/');
        
        // Debug logging
        error_log("Routing Debug - Method: {$requestMethod}, Original URI: {$_SERVER['REQUEST_URI']}, Processed URI: {$requestUri}, Base: {$basePath}, Script: {$scriptName}");
        
        // Buscar ruta exacta primero
        $callback = $this->routes[$requestMethod][$requestUri] ?? null;
        
        if ($callback) {
            error_log("Found exact route for {$requestMethod} {$requestUri}");
            return $this->executeCallback($callback);
        }
        
        // Buscar rutas con parámetros
        foreach ($this->routes[$requestMethod] ?? [] as $route => $callback) {
            $pattern = $this->convertRouteToRegex($route);
            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remover el match completo
                error_log("Found parametric route {$route} for {$requestMethod} {$requestUri}");
                return $this->executeCallback($callback, $matches);
            }
        }
        
        // Log para debugging 404
        error_log("404 - No route found for {$requestMethod} {$requestUri}");
        error_log("Available routes for {$requestMethod}: " . implode(', ', array_keys($this->routes[$requestMethod] ?? [])));
        
        // Ruta 404
        http_response_code(404);
        if (file_exists(APP_PATH . '/Views/errors/404.php')) {
            include APP_PATH . '/Views/errors/404.php';
        } else {
            $this->render404();
        }
    }
    
    private function convertRouteToRegex($route) {
        $pattern = str_replace('/', '\/', $route);
        $pattern = preg_replace('/\{([^}]+)\}/', '([^\/]+)', $pattern);
        return '/^' . $pattern . '$/';
    }
    
    private function executeCallback($callback, $params = []) {
        if (is_string($callback)) {
            $parts = explode('@', $callback);
            $controllerName = $parts[0];
            $method = $parts[1];
            
            // Agregar namespace completo si no lo tiene
            $fullControllerName = $controllerName;
            if (strpos($controllerName, '\\') === false) {
                $fullControllerName = 'StyleFitness\\Controllers\\' . $controllerName;
            }
            
            // Verificar que el controlador existe
            if (class_exists($fullControllerName)) {
                $controller = new $fullControllerName();
                if (method_exists($controller, $method)) {
                    return call_user_func_array([$controller, $method], $params);
                } else {
                    error_log("Method {$method} not found in controller {$fullControllerName}");
                    throw new Exception("Method {$method} not found in controller {$fullControllerName}");
                }
            } else {
                error_log("Controller {$fullControllerName} not found");
                throw new Exception("Controller {$fullControllerName} not found");
            }
        }
        
        return call_user_func_array($callback, $params);
    }
    
    private function render404() {
        echo "<!DOCTYPE html>";
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>404 - Página No Encontrada | StyloFitness</title>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #FF6B00, #E55A00); margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }";
        echo ".container { background: white; padding: 40px; border-radius: 15px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.2); max-width: 500px; }";
        echo ".error-code { font-size: 6rem; font-weight: bold; color: #FF6B00; margin: 0; }";
        echo "h1 { color: #333; margin: 20px 0; }";
        echo "p { color: #666; margin-bottom: 30px; }";
        echo ".btn { background: #FF6B00; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 5px; }";
        echo ".btn:hover { background: #E55A00; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<div class='container'>";
        echo "<div class='error-code'>404</div>";
        echo "<h1>Página No Encontrada</h1>";
        echo "<p>Lo sentimos, la página que buscas no existe o ha sido movida. Pero no te preocupes, ¡tenemos muchas otras opciones increíbles para ti!</p>";
        echo "<a href='/' class='btn'>🏠 Ir al Inicio</a>";
        echo "<a href='/store' class='btn'>🛒 Ir a Tienda</a>";
        echo "<a href='/classes' class='btn'>💪 Clases Grupales</a>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
    }
}

// Inicializar router
$router = new Router();

// ==========================================
// RUTAS PRINCIPALES
// ==========================================

$router->get('/', 'HomeController@index');
$router->get('/dashboard', 'HomeController@dashboard');

// ==========================================
// RUTAS DE AUTENTICACIÓN 
// ==========================================

$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@authenticate');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotPassword');
$router->post('/forgot-password', 'AuthController@sendResetLink');
$router->get('/reset-password/{token}', 'AuthController@resetPassword');
$router->post('/reset-password', 'AuthController@updatePassword');

// ==========================================
// RUTAS DE REGISTRO (SOLO STAFF/ADMIN)
// ==========================================

$router->get('/admin/register', 'AuthController@register');
$router->post('/admin/register', 'AuthController@store');
$router->get('/staff/register', 'AuthController@register');
$router->post('/staff/register', 'AuthController@store');

// ==========================================
// RUTAS DE RUTINAS
// ==========================================

// Listado y visualización
$router->get('/routines', 'RoutineController@index');
$router->get('/routines/templates', 'RoutineController@templates');
$router->get('/routines/view/{id}', 'RoutineController@show');

// Creación y edición
$router->get('/routines/create', 'RoutineController@create');
$router->post('/routines/store', 'RoutineController@store');
$router->get('/routines/edit/{id}', 'RoutineController@edit');
$router->post('/routines/update/{id}', 'RoutineController@update');

// Operaciones adicionales
$router->post('/routines/duplicate/{id}', 'RoutineController@duplicate');
$router->get('/routines/duplicate/{id}', 'RoutineController@duplicate');
$router->post('/routines/delete/{id}', 'RoutineController@delete');
$router->get('/routines/delete/{id}', 'RoutineController@delete');
$router->post('/routines/assign', 'RoutineController@assign');

// Logs y progreso
$router->post('/routines/log-workout', 'RoutineController@logWorkout');
$router->get('/routines/progress/{id}', 'RoutineController@getProgress');
$router->get('/routines/export/{id}', 'RoutineController@exportPdf');

// Rutinas por objetivo
$router->get('/routines/objective/{objective}', 'RoutineController@getByObjective');

// ==========================================
// RUTAS DE EJERCICIOS
// ==========================================

$router->get('/exercises', 'ExerciseController@index');
$router->get('/exercises/create', 'ExerciseController@create');
$router->post('/exercises/store', 'ExerciseController@store');
$router->get('/exercises/view/{id}', 'ExerciseController@show');
$router->get('/exercises/edit/{id}', 'ExerciseController@edit');
$router->post('/exercises/update/{id}', 'ExerciseController@update');
$router->post('/exercises/delete/{id}', 'ExerciseController@delete');
$router->get('/exercises/category/{id}', 'ExerciseController@byCategory');
$router->get('/exercises/search', 'ExerciseController@search');

// ==========================================
// RUTAS DE GESTIÓN DE EJERCICIOS (ADMIN)
// ==========================================

$router->get('/admin/exercise-management', 'ExerciseManagementController@index');
$router->get('/admin/exercise-management/create', 'ExerciseManagementController@create');
$router->post('/admin/exercise-management/store', 'ExerciseManagementController@store');
$router->get('/admin/exercise-management/show/{id}', 'ExerciseManagementController@show');
$router->get('/admin/exercise-management/edit/{id}', 'ExerciseManagementController@edit');
$router->post('/admin/exercise-management/update/{id}', 'ExerciseManagementController@update');
$router->post('/admin/exercise-management/delete/{id}', 'ExerciseManagementController@delete');

// ==========================================
// RUTAS DE PLANTILLAS DE RUTINAS (TRAINER)
// ==========================================

$router->get('/trainer/templates', 'RoutineTemplateController@index');
$router->get('/trainer/templates/create', 'RoutineTemplateController@create');
$router->post('/trainer/templates/store', 'RoutineTemplateController@store');
$router->get('/trainer/templates/show/{id}', 'RoutineTemplateController@show');
$router->get('/trainer/templates/edit/{id}', 'RoutineTemplateController@edit');
$router->post('/trainer/templates/update/{id}', 'RoutineTemplateController@update');
$router->post('/trainer/templates/assign', 'RoutineTemplateController@assign');
$router->get('/trainer/templates/duplicate/{id}', 'RoutineTemplateController@duplicate');
$router->post('/trainer/templates/duplicate/{id}', 'RoutineTemplateController@duplicate');
$router->get('/trainer/templates/export/{id}', 'RoutineTemplateController@export');

// ==========================================
// RUTAS DE SEGUIMIENTO DE PROGRESO (TRAINER)
// ==========================================

// Dashboard de progreso
$router->get('/trainer/progress', 'TrainerProgressController@index');
$router->get('/trainer/progress/dashboard', 'TrainerProgressController@dashboard');

// Progreso de clientes individuales
$router->get('/trainer/progress/client/{id}', 'TrainerProgressController@clientProgress');
$router->get('/trainer/progress/client/{id}/details', 'TrainerProgressController@clientDetails');
$router->post('/trainer/progress/client/{id}/notes', 'TrainerProgressController@saveClientNotes');

// Comparación de clientes
$router->get('/trainer/progress/compare', 'TrainerProgressController@compare');
$router->post('/trainer/progress/compare/data', 'TrainerProgressController@getComparisonData');

// Reportes de progreso
$router->get('/trainer/progress/reports', 'TrainerProgressController@reports');
$router->post('/trainer/progress/reports/generate', 'TrainerProgressController@generateReport');
$router->post('/trainer/progress/reports/preview', 'TrainerProgressController@previewReport');
$router->post('/trainer/progress/reports/schedule', 'TrainerProgressController@scheduleReport');
$router->get('/trainer/progress/reports/{id}/duplicate', 'TrainerProgressController@duplicateReport');
$router->post('/trainer/progress/reports/{id}/share', 'TrainerProgressController@shareReport');
$router->delete('/trainer/progress/reports/{id}', 'TrainerProgressController@deleteReport');

// Plantillas de reportes
$router->get('/trainer/progress/templates', 'TrainerProgressController@reportTemplates');
$router->post('/trainer/progress/templates', 'TrainerProgressController@saveReportTemplate');
$router->get('/trainer/progress/templates/{id}', 'TrainerProgressController@loadReportTemplate');
$router->delete('/trainer/progress/templates/{id}', 'TrainerProgressController@deleteReportTemplate');

// ==========================================
// RUTAS DE TIENDA
// ==========================================

$router->get('/store', 'StoreController@index');
$router->get('/store/category/{category}', 'StoreController@category');
$router->get('/store/product/{slug}', 'StoreController@product');
$router->get('/store/search', 'StoreController@search');

// Carrito de compras
$router->get('/cart', 'CartController@index');
$router->get('/cart/count', 'CartController@getCount');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/clear', 'CartController@clear');

// Checkout y pagos
$router->get('/checkout', 'CheckoutController@index');
$router->post('/checkout/process', 'CheckoutController@process');
$router->get('/checkout/success/{order}', 'CheckoutController@success');
$router->get('/checkout/cancel', 'CheckoutController@cancel');

// Lista de deseos
$router->get('/wishlist', 'WishlistController@index');
$router->post('/wishlist/add', 'WishlistController@add');
$router->post('/wishlist/remove', 'WishlistController@remove');

// ==========================================
// RUTAS DE CLASES GRUPALES
// ==========================================

$router->get('/classes', 'GroupClassController@index');
$router->get('/classes/view/{id}', 'GroupClassController@show');
$router->post('/classes/book', 'GroupClassController@book');
$router->post('/classes/cancel', 'GroupClassController@cancel');
$router->get('/classes/schedule', 'GroupClassController@schedule');
$router->get('/my-classes', 'GroupClassController@myClasses');

// ==========================================
// RUTAS DE PERFIL DE USUARIO
// ==========================================

$router->get('/profile', 'UserController@profile');
$router->post('/profile/update', 'UserController@updateProfile');
$router->post('/profile/update-password', 'UserController@updatePassword');
$router->post('/profile/update-avatar', 'UserController@updateAvatar');
$router->get('/my-progress', 'TrainerProgressController@clientProgress');
$router->get('/progress', 'TrainerProgressController@clientProgress');
$router->get('/my-routines', 'UserController@myRoutines');
$router->get('/my-orders', 'UserController@myOrders');

// ==========================================
// RUTAS DE ADMINISTRACIÓN
// ==========================================

$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/users/create', 'AdminController@createUser');
$router->post('/admin/users/store', 'AdminController@storeUser');
$router->get('/admin/users/edit/{id}', 'AdminController@editUser');
$router->post('/admin/users/update/{id}', 'AdminController@updateUser');
$router->post('/admin/users/delete/{id}', 'AdminController@deleteUser');

$router->get('/admin/products', 'AdminController@products');
$router->get('/admin/products/create', 'AdminController@createProduct');
$router->post('/admin/products/store', 'AdminController@storeProduct');
$router->get('/admin/products/edit/{id}', 'AdminController@editProduct');
$router->post('/admin/products/update/{id}', 'AdminController@updateProduct');
$router->post('/admin/products/delete/{id}', 'AdminController@deleteProduct');

$router->get('/admin/routines', 'AdminController@routines');
$router->get('/admin/exercises', 'AdminController@exercises');
$router->get('/admin/orders', 'AdminController@orders');
$router->get('/admin/classes', 'AdminController@classes');
$router->get('/admin/reports', 'AdminController@reports');
$router->get('/admin/settings', 'AdminController@settings');
$router->post('/admin/settings/update', 'AdminController@updateSettings');

// ==========================================
// RUTAS DE ADMINISTRACIÓN - LANDING PAGE
// ==========================================

// Special Offers
$router->get('/admin/landing/special-offers', 'AdminLandingController@specialOffers');
$router->get('/admin/landing/special-offers/get', 'AdminLandingController@getSpecialOffers');
$router->get('/admin/landing/special-offers/get-single', 'AdminLandingController@getSingleSpecialOffer');
$router->post('/admin/landing/special-offers/create', 'AdminLandingController@createSpecialOffer');
$router->post('/admin/landing/special-offers/update', 'AdminLandingController@updateSpecialOffer');
$router->post('/admin/landing/special-offers/delete', 'AdminLandingController@deleteSpecialOffer');
$router->post('/admin/landing/special-offers/toggle-status', 'AdminLandingController@toggleSpecialOfferStatus');

// Why Choose Us
$router->get('/admin/landing/why-choose-us', 'AdminLandingController@whyChooseUs');
$router->get('/admin/landing/why-choose-us/get', 'AdminLandingController@getWhyChooseUsFeatures');
$router->get('/admin/landing/why-choose-us/get-single', 'AdminLandingController@getSingleWhyChooseUsFeature');
$router->post('/admin/landing/why-choose-us/create', 'AdminLandingController@createWhyChooseUsFeature');
$router->post('/admin/landing/why-choose-us/update', 'AdminLandingController@updateWhyChooseUsFeature');
$router->post('/admin/landing/why-choose-us/delete', 'AdminLandingController@deleteWhyChooseUsFeature');
$router->post('/admin/landing/why-choose-us/toggle-status', 'AdminLandingController@toggleWhyChooseUsFeatureStatus');

// Testimonials
$router->get('/admin/landing/testimonials', 'AdminLandingController@testimonials');
$router->get('/admin/landing/testimonials/get', 'AdminLandingController@getTestimonials');
$router->get('/admin/landing/testimonials/get-single', 'AdminLandingController@getSingleTestimonial');
$router->post('/admin/landing/testimonials/create', 'AdminLandingController@createTestimonial');
$router->post('/admin/landing/testimonials/update', 'AdminLandingController@updateTestimonial');
$router->post('/admin/landing/testimonials/delete', 'AdminLandingController@deleteTestimonial');
$router->post('/admin/landing/testimonials/toggle-status', 'AdminLandingController@toggleTestimonialStatus');

// Landing Page Config
$router->get('/admin/landing/config', 'AdminLandingController@config');
$router->get('/admin/landing/config/get', 'AdminLandingController@getConfigs');
$router->get('/admin/landing/config/get-section', 'AdminLandingController@getSectionConfigs');
$router->get('/admin/landing/config/get-single', 'AdminLandingController@getSingleConfig');
$router->post('/admin/landing/config/create', 'AdminLandingController@createConfig');
$router->post('/admin/landing/config/update', 'AdminLandingController@updateConfig');
$router->post('/admin/landing/config/delete', 'AdminLandingController@deleteConfig');
$router->post('/admin/landing/config/toggle-status', 'AdminLandingController@toggleConfigStatus');

// ==========================================
// API ROUTES
// ==========================================

// API de rutinas
$router->get('/api/routines', 'ApiController@routines');
$router->get('/api/routines/{id}', 'ApiController@routine');
$router->post('/api/routines', 'ApiController@createRoutine');
$router->put('/api/routines/{id}', 'ApiController@updateRoutine');
$router->delete('/api/routines/{id}', 'ApiController@deleteRoutine');

// API de ejercicios
$router->get('/api/exercises', 'ApiController@exercises');
$router->get('/api/exercises/categories', 'ApiController@exerciseCategories');
$router->get('/api/exercises/search', 'ApiController@searchExercises');
$router->get('/api/exercises/by-zone/{zone}', 'RoutineTemplateController@getExercisesByZone');
$router->get('/api/exercises/by-category/{categoryId}', 'ExerciseManagementController@getExercisesByCategory');

// API de instructor
$router->get('/api/instructor/clients', 'RoutineTemplateController@getInstructorClients');

// API de plantillas
$router->get('/api/templates/usage-stats/{id}', 'RoutineTemplateController@getUsageStats');

// API de seguimiento de progreso
$router->get('/api/trainer/progress/dashboard', 'TrainerProgressController@getDashboardData');
$router->get('/api/trainer/progress/client/{id}', 'TrainerProgressController@getClientProgressData');
$router->get('/api/trainer/progress/client/{id}/workouts', 'TrainerProgressController@getClientWorkouts');
$router->get('/api/trainer/progress/client/{id}/stats', 'TrainerProgressController@getClientStats');
$router->get('/api/trainer/progress/client/{id}/charts', 'TrainerProgressController@getClientCharts');
$router->get('/api/trainer/progress/alerts', 'TrainerProgressController@getProgressAlerts');
$router->get('/api/trainer/progress/compare/clients', 'TrainerProgressController@getClientsForComparison');
$router->post('/api/trainer/progress/export', 'TrainerProgressController@exportProgressData');
$router->get('/api/trainer/reports/recent', 'TrainerProgressController@getRecentReports');
$router->post('/api/trainer/workout-logs', 'TrainerProgressController@logWorkout');
$router->get('/api/trainer/workout-logs/{clientId}', 'TrainerProgressController@getWorkoutLogs');

// API de productos
$router->get('/api/products', 'ApiController@products');
$router->get('/api/products/featured', 'ApiController@featuredProducts');
$router->get('/api/products/recommendations', 'ApiController@productRecommendations');

// API de autenticación
$router->post('/api/v1/auth/login', 'ApiController@login');
$router->post('/api/auth/login', 'ApiController@login');
$router->post('/api/v1/auth/logout', 'ApiController@logout');
$router->post('/api/auth/logout', 'ApiController@logout');

// API de usuarios
$router->get('/api/users', 'ApiController@users');
$router->get('/api/clients', 'ApiController@clients');
$router->get('/api/instructors', 'ApiController@instructors');

// API de clases
$router->get('/api/classes', 'ApiController@classes');
$router->get('/api/classes/upcoming', 'ApiController@upcomingClasses');
$router->get('/api/classes/{id}', 'ApiController@getClass');
$router->get('/api/classes/{id}/schedule', 'ApiController@getClassSchedule');
$router->get('/api/classes/schedule/availability', 'ApiController@getScheduleAvailability');

// API de estadísticas
$router->get('/api/stats', 'ApiController@dashboardStats');
$router->get('/api/stats/dashboard', 'ApiController@dashboardStats');
$router->get('/api/stats/routines', 'ApiController@routineStats');
$router->get('/api/stats/sales', 'ApiController@salesStats');

// API de uploads
$router->post('/api/upload/image', 'ApiController@uploadImage');
$router->post('/api/upload/video', 'ApiController@uploadVideo');
$router->post('/api/upload/document', 'ApiController@uploadDocument');

// ==========================================
// RUTAS DE PÁGINAS ESTÁTICAS
// ==========================================

$router->get('/about', 'PageController@about');
$router->get('/contact', 'PageController@contact');
$router->post('/contact', 'PageController@sendContact');
$router->get('/privacy', 'PageController@privacy');
$router->get('/terms', 'PageController@terms');
$router->get('/faq', 'PageController@faq');

// ==========================================
// WEBHOOKS Y INTEGRACIONES
// ==========================================

$router->post('/webhook/payment/stripe', 'WebhookController@stripePayment');
$router->post('/webhook/payment/paypal', 'WebhookController@paypalPayment');
$router->post('/webhook/email/mailgun', 'WebhookController@mailgunWebhook');

// ==========================================
// RUTAS DE DESARROLLO (solo en modo debug)
// ==========================================

if (function_exists('getAppConfig') && getAppConfig('debug_enabled', false)) {
    $router->get('/dev/phpinfo', function() {
        phpinfo();
    });
    
    $router->get('/dev/test-email', 'DevController@testEmail');
    $router->get('/dev/test-db', 'DevController@testDatabase');
    $router->get('/dev/clear-cache', 'DevController@clearCache');
}

// Manejo de errores
set_exception_handler(function($exception) {
    error_log("Uncaught exception: " . $exception->getMessage());
    
    if (function_exists('getAppConfig') && getAppConfig('debug_enabled', false)) {
        echo '<pre>' . $exception->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        if (file_exists(APP_PATH . '/Views/errors/500.php')) {
            include APP_PATH . '/Views/errors/500.php';
        } else {
            echo '<h1>500 - Error interno del servidor</h1>';
        }
    }
});

// Resolver ruta
try {
    $router->resolve();
} catch (Exception $e) {
    error_log("Router exception: " . $e->getMessage());
    
    if (function_exists('getAppConfig') && getAppConfig('debug_enabled', false)) {
        echo '<pre>Error: ' . $e->getMessage() . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>Error interno del servidor</h1>';
    }
}
?>