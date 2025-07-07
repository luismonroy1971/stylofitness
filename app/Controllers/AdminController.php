<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\User;
use StyleFitness\Models\Product;
use StyleFitness\Models\ProductCategory;
use StyleFitness\Models\Order;
use StyleFitness\Models\GroupClass;
use StyleFitness\Models\Routine;
use StyleFitness\Models\Exercise;
use StyleFitness\Helpers\AppHelper;
use Exception;

/**
 * Controlador de Administración - STYLOFITNESS
 * Panel completo para gestión administrativa
 */

class AdminController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();

        // Verificar que el usuario sea administrador
        if (!AppHelper::isLoggedIn() || AppHelper::getCurrentUser()['role'] !== 'admin') {
            AppHelper::setFlashMessage('error', 'Acceso no autorizado');
            AppHelper::redirect('/login');
            return;
        }
    }

    public function dashboard()
    {
        // Obtener estadísticas generales
        $stats = $this->getDashboardStats();

        // Obtener datos para gráficos
        $chartData = $this->getChartData();

        // Actividad reciente
        $recentActivity = $this->getRecentActivity();

        // Alertas del sistema
        $alerts = $this->getSystemAlerts();

        $pageTitle = 'Panel de Administración - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-dashboard.js', 'chart.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/dashboard.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function users()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'role' => AppHelper::sanitize($_GET['role'] ?? ''),
            'status' => AppHelper::sanitize($_GET['status'] ?? ''),
            'limit' => $perPage,
            'offset' => $offset,
        ];

        $userModel = new User();
        $users = $userModel->getUsers($filters);
        $totalUsers = $userModel->countUsers($filters);

        $pagination = $this->calculatePagination($page, $totalUsers, $perPage);

        $pageTitle = 'Gestión de Usuarios - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-users.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/users.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function createUser()
    {
        $pageTitle = 'Crear Usuario - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-user-form.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/user-form.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function storeUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/admin/users');
            return;
        }

        $data = [
            'username' => AppHelper::sanitize($_POST['username'] ?? ''),
            'email' => AppHelper::sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'first_name' => AppHelper::sanitize($_POST['first_name'] ?? ''),
            'last_name' => AppHelper::sanitize($_POST['last_name'] ?? ''),
            'phone' => AppHelper::sanitize($_POST['phone'] ?? ''),
            'role' => AppHelper::sanitize($_POST['role'] ?? 'client'),
            'gym_id' => (int)($_POST['gym_id'] ?? 1),
            'membership_type' => AppHelper::sanitize($_POST['membership_type'] ?? 'basic'),
            'membership_expires' => $_POST['membership_expires'] ?? null,
            'is_active' => isset($_POST['is_active']),
        ];

        $userModel = new User();
        $errors = AppHelper::validateUser($data);

        if (!empty($errors)) {
            $_SESSION['admin_errors'] = $errors;
            $_SESSION['admin_form_data'] = $data;
            AppHelper::redirect('/admin/users/create');
            return;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $userId = $userModel->create($data);

        if ($userId) {
            AppHelper::setFlashMessage('success', 'Usuario creado exitosamente');
            AppHelper::redirect('/admin/users');
        } else {
            AppHelper::setFlashMessage('error', 'Error al crear el usuario');
            AppHelper::redirect('/admin/users/create');
        }
    }

    public function editUser($id)
    {
        $userModel = new User();
        $user = $userModel->findById($id);

        if (!$user) {
            AppHelper::setFlashMessage('error', 'Usuario no encontrado');
            AppHelper::redirect('/admin/users');
            return;
        }

        $pageTitle = 'Editar Usuario - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-user-form.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/user-form.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function updateUser($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/admin/users');
            return;
        }

        $userModel = new User();
        $existingUser = $userModel->findById($id);

        if (!$existingUser) {
            AppHelper::setFlashMessage('error', 'Usuario no encontrado');
            AppHelper::redirect('/admin/users');
            return;
        }

        $data = [
            'username' => AppHelper::sanitize($_POST['username'] ?? ''),
            'email' => AppHelper::sanitize($_POST['email'] ?? ''),
            'first_name' => AppHelper::sanitize($_POST['first_name'] ?? ''),
            'last_name' => AppHelper::sanitize($_POST['last_name'] ?? ''),
            'phone' => AppHelper::sanitize($_POST['phone'] ?? ''),
            'role' => AppHelper::sanitize($_POST['role'] ?? 'client'),
            'gym_id' => (int)($_POST['gym_id'] ?? 1),
            'membership_type' => AppHelper::sanitize($_POST['membership_type'] ?? 'basic'),
            'membership_expires' => $_POST['membership_expires'] ?? null,
            'is_active' => isset($_POST['is_active']),
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        if ($userModel->update($id, $data)) {
            AppHelper::setFlashMessage('success', 'Usuario actualizado exitosamente');
            AppHelper::redirect('/admin/users');
        } else {
            AppHelper::setFlashMessage('error', 'Error al actualizar el usuario');
            AppHelper::redirect('/admin/users/edit/' . $id);
        }
    }

    public function deleteUser($id)
    {
        $userModel = new User();
        $user = $userModel->findById($id);

        if (!$user) {
            AppHelper::setFlashMessage('error', 'Usuario no encontrado');
            AppHelper::redirect('/admin/users');
            return;
        }

        // No permitir eliminar el último administrador
        if ($user['role'] === 'admin') {
            $adminCount = $this->db->count("SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1");
            if ($adminCount <= 1) {
                AppHelper::setFlashMessage('error', 'No se puede eliminar el último administrador');
                AppHelper::redirect('/admin/users');
                return;
            }
        }

        if ($userModel->delete($id)) {
            AppHelper::setFlashMessage('success', 'Usuario eliminado exitosamente');
        } else {
            AppHelper::setFlashMessage('error', 'Error al eliminar el usuario');
        }

        AppHelper::redirect('/admin/users');
    }

    public function products()
    {
        // Si es una petición AJAX, devolver solo los datos
        if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
            $this->getProductsAjax();
            return;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'category_id' => !empty($_GET['category']) ? (int)$_GET['category'] : 0,
            'is_active' => $_GET['status'] ?? '',
            'is_featured' => $_GET['featured'] ?? '',
            'limit' => $perPage,
            'offset' => $offset,
        ];

        $productModel = new Product();
        $products = $productModel->getProducts($filters);
        $totalProducts = $productModel->countProducts($filters);

        $categoryModel = new ProductCategory();
        $categories = $categoryModel->getCategories();

        $pagination = $this->calculatePagination($page, $totalProducts, $perPage);

        $pageTitle = 'Gestión de Productos - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-products.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/products.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function getProductsAjax()
    {
        header('Content-Type: application/json');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'category_id' => !empty($_GET['category']) ? (int)$_GET['category'] : 0,
            'is_active' => $_GET['status'] ?? '',
            'is_featured' => $_GET['featured'] ?? '',
            'limit' => $perPage,
            'offset' => $offset,
        ];

        $productModel = new Product();
        $products = $productModel->getProducts($filters);
        $totalProducts = $productModel->countProducts($filters);

        $pagination = $this->calculatePagination($page, $totalProducts, $perPage);

        echo json_encode([
            'success' => true,
            'products' => $products,
            'pagination' => $pagination,
            'total' => $totalProducts
        ]);
    }

    public function createProduct()
    {
        $categoryModel = new ProductCategory();
        $categories = $categoryModel->getCategories();

        $pageTitle = 'Crear Producto - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-product-form.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/product-form.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function storeProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/admin/products');
            return;
        }

        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name' => AppHelper::sanitize($_POST['name'] ?? ''),
            'slug' => AppHelper::createSlug($_POST['name'] ?? ''),
            'description' => AppHelper::sanitize($_POST['description'] ?? ''),
            'short_description' => AppHelper::sanitize($_POST['short_description'] ?? ''),
            'sku' => AppHelper::sanitize($_POST['sku'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
            'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
            'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
            'brand' => AppHelper::sanitize($_POST['brand'] ?? ''),
            'is_featured' => isset($_POST['is_featured']),
            'is_active' => isset($_POST['is_active']),
            'meta_title' => AppHelper::sanitize($_POST['meta_title'] ?? ''),
            'meta_description' => AppHelper::sanitize($_POST['meta_description'] ?? ''),
        ];

        $productModel = new Product();
        $errors = $productModel->validateProduct($data);

        if (!empty($errors)) {
            $_SESSION['admin_errors'] = $errors;
            $_SESSION['admin_form_data'] = $data;
            AppHelper::redirect('/admin/products/create');
            return;
        }

        $productId = $productModel->create($data);

        if ($productId) {
            // Procesar imágenes si se subieron
            $this->processProductImages($productId, $_FILES['images'] ?? []);

            AppHelper::setFlashMessage('success', 'Producto creado exitosamente');
            AppHelper::redirect('/admin/products');
        } else {
            AppHelper::setFlashMessage('error', 'Error al crear el producto');
            AppHelper::redirect('/admin/products/create');
        }
    }

    public function editProduct($id)
    {
        $productModel = new Product();
        $product = $productModel->findById($id);

        if (!$product) {
            AppHelper::setFlashMessage('error', 'Producto no encontrado');
            AppHelper::redirect('/admin/products');
            return;
        }

        $categoryModel = new ProductCategory();
        $categories = $categoryModel->getCategories();

        $pageTitle = 'Editar Producto - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-product-form.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/product-form.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function updateProduct($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                return;
            }
            AppHelper::redirect('/admin/products');
            return;
        }

        $productModel = new Product();
        $existingProduct = $productModel->findById($id);

        if (!$existingProduct) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                return;
            }
            AppHelper::setFlashMessage('error', 'Producto no encontrado');
            AppHelper::redirect('/admin/products');
            return;
        }

        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name' => AppHelper::sanitize($_POST['name'] ?? ''),
            'slug' => AppHelper::createSlug($_POST['name'] ?? ''),
            'description' => AppHelper::sanitize($_POST['description'] ?? ''),
            'short_description' => AppHelper::sanitize($_POST['short_description'] ?? ''),
            'sku' => AppHelper::sanitize($_POST['sku'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
            'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
            'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
            'brand' => AppHelper::sanitize($_POST['brand'] ?? ''),
            'is_featured' => isset($_POST['is_featured']),
            'is_active' => isset($_POST['is_active']),
            'meta_title' => AppHelper::sanitize($_POST['meta_title'] ?? ''),
            'meta_description' => AppHelper::sanitize($_POST['meta_description'] ?? ''),
        ];

        if ($productModel->update($id, $data)) {
            // Procesar nuevas imágenes si se subieron
            if (!empty($_FILES['images']['name'][0])) {
                $this->processProductImages($id, $_FILES['images']);
            }

            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Producto actualizado exitosamente']);
                return;
            }
            AppHelper::setFlashMessage('success', 'Producto actualizado exitosamente');
            AppHelper::redirect('/admin/products');
        } else {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el producto']);
                return;
            }
            AppHelper::setFlashMessage('error', 'Error al actualizar el producto');
            AppHelper::redirect('/admin/products/edit/' . $id);
        }
    }

    public function deleteProduct($id)
    {
        $productModel = new Product();
        $product = $productModel->findById($id);

        if (!$product) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                return;
            }
            AppHelper::setFlashMessage('error', 'Producto no encontrado');
            AppHelper::redirect('/admin/products');
            return;
        }

        // Verificar si el producto tiene pedidos asociados
        $orderCount = $this->db->count(
            'SELECT COUNT(*) FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             WHERE oi.product_id = ? AND o.status != "cancelled"',
            [$id]
        );

        if ($orderCount > 0) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar el producto porque tiene pedidos asociados']);
                return;
            }
            AppHelper::setFlashMessage('error', 'No se puede eliminar el producto porque tiene pedidos asociados');
            AppHelper::redirect('/admin/products');
            return;
        }

        // Eliminar imágenes del producto
        if (!empty($product['images'])) {
            // Verificar si images ya es un array o es una cadena JSON
            if (is_string($product['images'])) {
                $images = json_decode($product['images'], true);
            } else {
                $images = $product['images'];
            }
            
            if (is_array($images)) {
                foreach ($images as $image) {
                    $imagePath = PUBLIC_PATH . $image;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }
        }

        if ($productModel->delete($id)) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Producto eliminado exitosamente']);
                return;
            }
            AppHelper::setFlashMessage('success', 'Producto eliminado exitosamente');
        } else {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
                return;
            }
            AppHelper::setFlashMessage('error', 'Error al eliminar el producto');
        }

        AppHelper::redirect('/admin/products');
    }

    public function orders()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'status' => AppHelper::sanitize($_GET['status'] ?? ''),
            'payment_status' => AppHelper::sanitize($_GET['payment_status'] ?? ''),
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'limit' => $perPage,
            'offset' => $offset,
        ];

        $orderModel = new Order();
        $orders = $orderModel->getOrders($filters);
        $totalOrders = $orderModel->countOrders($filters);

        $pagination = $this->calculatePagination($page, $totalOrders, $perPage);

        $pageTitle = 'Gestión de Pedidos - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-orders.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/orders.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function routines()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'objective' => AppHelper::sanitize($_GET['objective'] ?? ''),
            'difficulty' => AppHelper::sanitize($_GET['difficulty'] ?? ''),
            'is_template' => $_GET['is_template'] ?? '',
            'limit' => $perPage,
            'offset' => $offset,
        ];

        $routineModel = new Routine();
        $routines = $routineModel->getAllRoutines($filters);
        $totalRoutines = $routineModel->countAllRoutines($filters);

        $pagination = $this->calculatePagination($page, $totalRoutines, $perPage);

        $pageTitle = 'Gestión de Rutinas - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-routines.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/routines.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function exercises()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'category_id' => (int)($_GET['category_id'] ?? 0),
            'difficulty' => AppHelper::sanitize($_GET['difficulty'] ?? ''),
            'limit' => $perPage,
            'offset' => $offset,
        ];

        $exerciseModel = new Exercise();
        $exercises = $exerciseModel->getExercises($filters);
        $totalExercises = $exerciseModel->countExercises($filters);

        $categories = $exerciseModel->getCategories();

        $pagination = $this->calculatePagination($page, $totalExercises, $perPage);

        $pageTitle = 'Gestión de Ejercicios - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-exercises.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/exercises.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function classes()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'class_type' => AppHelper::sanitize($_GET['class_type'] ?? ''),
            'instructor_id' => (int)($_GET['instructor_id'] ?? 0),
            'limit' => $perPage,
            'offset' => $offset,
        ];

        $classModel = new GroupClass();
        $classes = $classModel->getClasses($filters);
        $totalClasses = $classModel->countClasses($filters);

        $userModel = new User();
        $instructors = $userModel->getUsers(['role' => 'instructor', 'is_active' => true]);

        $pagination = $this->calculatePagination($page, $totalClasses, $perPage);

        $pageTitle = 'Gestión de Clases - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-classes.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/classes.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function reports()
    {
        $reportType = AppHelper::sanitize($_GET['type'] ?? 'overview');
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');

        $reportData = [];

        switch ($reportType) {
            case 'sales':
                $reportData = $this->getSalesReport($dateFrom, $dateTo);
                break;
            case 'users':
                $reportData = $this->getUsersReport($dateFrom, $dateTo);
                break;
            case 'routines':
                $reportData = $this->getRoutinesReport($dateFrom, $dateTo);
                break;
            case 'overview':
            default:
                $reportData = $this->getOverviewReport($dateFrom, $dateTo);
                break;
        }

        $pageTitle = 'Reportes y Análisis - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-reports.js', 'chart.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/reports.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function settings()
    {
        $settingsModel = new SystemSettings();
        $settings = $settingsModel->getAllSettings();

        $pageTitle = 'Configuración del Sistema - STYLOFITNESS';
        $additionalCSS = ['admin.css'];
        $additionalJS = ['admin-settings.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/admin/settings.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function updateSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            AppHelper::redirect('/admin/settings');
            return;
        }

        $settingsModel = new SystemSettings();
        $updates = 0;

        foreach ($_POST['settings'] as $key => $value) {
            if ($settingsModel->updateSetting($key, $value)) {
                $updates++;
            }
        }

        if ($updates > 0) {
            AppHelper::setFlashMessage('success', "Se actualizaron {$updates} configuraciones");
        } else {
            AppHelper::setFlashMessage('error', 'No se pudo actualizar la configuración');
        }

        AppHelper::redirect('/admin/settings');
    }

    // Métodos auxiliares

    private function getDashboardStats()
    {
        return [
            'total_users' => $this->db->count('SELECT COUNT(*) FROM users WHERE is_active = 1'),
            'total_clients' => $this->db->count("SELECT COUNT(*) FROM users WHERE role = 'client' AND is_active = 1"),
            'total_instructors' => $this->db->count("SELECT COUNT(*) FROM users WHERE role = 'instructor' AND is_active = 1"),
            'total_products' => $this->db->count('SELECT COUNT(*) FROM products WHERE is_active = 1'),
            'total_routines' => $this->db->count('SELECT COUNT(*) FROM routines WHERE is_active = 1'),
            'total_orders' => $this->db->count('SELECT COUNT(*) FROM orders'),
            'pending_orders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'pending'"),
            'monthly_revenue' => $this->db->fetch(
                "SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders 
                 WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
                 AND YEAR(created_at) = YEAR(CURRENT_DATE()) 
                 AND payment_status = 'paid'"
            )['revenue'] ?? 0,
            'low_stock_products' => $this->db->count(
                'SELECT COUNT(*) FROM products 
                 WHERE stock_quantity <= min_stock_level AND is_active = 1'
            ),
            'active_memberships' => $this->db->count(
                "SELECT COUNT(*) FROM users 
                 WHERE role = 'client' AND membership_expires > CURDATE()"
            ),
        ];
    }

    private function getChartData()
    {
        // Datos para gráficos del dashboard
        $last30Days = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $last30Days[] = $date;
        }

        // Ventas por día
        $salesData = [];
        foreach ($last30Days as $date) {
            $sales = $this->db->fetch(
                "SELECT COALESCE(SUM(total_amount), 0) as total 
                 FROM orders 
                 WHERE DATE(created_at) = ? AND payment_status = 'paid'",
                [$date]
            );
            $salesData[] = (float)$sales['total'];
        }

        // Nuevos usuarios por día
        $usersData = [];
        foreach ($last30Days as $date) {
            $users = $this->db->count(
                'SELECT COUNT(*) FROM users WHERE DATE(created_at) = ?',
                [$date]
            );
            $usersData[] = $users;
        }

        return [
            'labels' => $last30Days,
            'sales' => $salesData,
            'users' => $usersData,
        ];
    }

    private function getRecentActivity()
    {
        return $this->db->fetchAll(
            'SELECT ual.*, u.first_name, u.last_name, u.email
             FROM user_activity_logs ual
             LEFT JOIN users u ON ual.user_id = u.id
             ORDER BY ual.created_at DESC
             LIMIT 10'
        );
    }

    private function getSystemAlerts()
    {
        $alerts = [];

        // Productos con stock bajo
        $lowStockCount = $this->db->count(
            'SELECT COUNT(*) FROM products 
             WHERE stock_quantity <= min_stock_level AND is_active = 1'
        );

        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Hay {$lowStockCount} productos con stock bajo",
                'action' => '/admin/products?low_stock=1',
            ];
        }

        // Pedidos pendientes
        $pendingOrders = $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'pending'");

        if ($pendingOrders > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "Hay {$pendingOrders} pedidos pendientes de procesar",
                'action' => '/admin/orders?status=pending',
            ];
        }

        // Membresías por vencer
        $expiringMemberships = $this->db->count(
            "SELECT COUNT(*) FROM users 
             WHERE role = 'client' 
             AND membership_expires BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
        );

        if ($expiringMemberships > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$expiringMemberships} membresías vencen en los próximos 7 días",
                'action' => '/admin/users?expiring=1',
            ];
        }

        return $alerts;
    }

    private function calculatePagination($currentPage, $totalItems, $perPage)
    {
        $totalPages = ceil($totalItems / $perPage);

        return [
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'per_page' => $perPage,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_page' => $currentPage > 1 ? $currentPage - 1 : null,
            'next_page' => $currentPage < $totalPages ? $currentPage + 1 : null,
        ];
    }

    private function processProductImages($productId, $images)
    {
        if (empty($images['name'][0])) {
            return;
        }

        $uploadedImages = [];
        $uploadDir = UPLOAD_PATH . '/images/products/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        for ($i = 0; $i < count($images['name']); $i++) {
            if ($images['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $fileName = uniqid() . '_' . basename($images['name'][$i]);
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($images['tmp_name'][$i], $filePath)) {
                $uploadedImages[] = '/uploads/images/products/' . $fileName;
            }
        }

        if (!empty($uploadedImages)) {
            $productModel = new Product();
            $productModel->update($productId, [
                'images' => json_encode($uploadedImages),
            ]);
        }
    }

    private function getSalesReport($dateFrom, $dateTo)
    {
        // Implementar reporte de ventas
        return [];
    }

    private function getUsersReport($dateFrom, $dateTo)
    {
        // Implementar reporte de usuarios
        return [];
    }

    private function getRoutinesReport($dateFrom, $dateTo)
    {
        // Implementar reporte de rutinas
        return [];
    }

    private function getOverviewReport($dateFrom, $dateTo)
    {
        // Implementar reporte general
        return [];
    }

    /**
     * Detecta si la petición es AJAX
     */
    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}

// Crear modelo de configuraciones del sistema
class SystemSettings
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllSettings()
    {
        return $this->db->fetchAll(
            'SELECT * FROM system_settings ORDER BY `group`, `key`'
        );
    }

    public function getSetting($key, $default = null)
    {
        $setting = $this->db->fetch(
            'SELECT value FROM system_settings WHERE `key` = ?',
            [$key]
        );

        return $setting ? $setting['value'] : $default;
    }

    public function updateSetting($key, $value)
    {
        $exists = $this->db->count(
            'SELECT COUNT(*) FROM system_settings WHERE `key` = ?',
            [$key]
        );

        if ($exists) {
            return $this->db->query(
                'UPDATE system_settings SET value = ?, updated_at = NOW() WHERE `key` = ?',
                [$value, $key]
            );
        } else {
            return $this->db->query(
                'INSERT INTO system_settings (`key`, value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())',
                [$key, $value]
            );
        }
    }
}
