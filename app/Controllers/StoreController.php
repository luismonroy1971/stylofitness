<?php
/**
 * Controlador de Tienda - STYLOFITNESS
 * Gestión del catálogo de productos y tienda online
 */

namespace StyleFitness\Controllers;

use StyleFitness\Helpers\AppHelper;
use StyleFitness\Config\Database;
use StyleFitness\Models\Product;
use StyleFitness\Models\ProductCategory;

class StoreController {
    
    private $db;
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->productModel = new Product();
        $this->categoryModel = new ProductCategory();
    }
    
    public function index() {
        // Headers anti-caché agresivos para prevenir cacheo en navegadores
        if (!headers_sent()) {
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0, private');
            header('Pragma: no-cache');
            header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Vary: *');
            header('X-Accel-Expires: 0');
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;
        
        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'category_id' => (int)($_GET['category'] ?? 0),
            'brand' => AppHelper::sanitize($_GET['brand'] ?? ''),
            'price_min' => (float)($_GET['price_min'] ?? 0),
            'price_max' => (float)($_GET['price_max'] ?? 0),
            'sort' => AppHelper::sanitize($_GET['sort'] ?? 'name_asc'),
            'is_active' => true,
            'limit' => $perPage,
            'offset' => $offset
        ];
        
        $products = $this->productModel->getProducts($filters);
        $totalProducts = $this->productModel->countProducts($filters);
        
        // Obtener datos para filtros
        $categories = $this->categoryModel->getMainCategories();
        $brands = $this->productModel->getBrands();
        $priceRange = $this->productModel->getPriceRange();
        
        // Productos destacados
        $featuredProducts = $this->productModel->getFeaturedProducts(8);
        
        // Paginación
        $totalPages = ceil($totalProducts / $perPage);
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalProducts,
            'per_page' => $perPage,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
            'previous_page' => $page - 1,
            'next_page' => $page + 1
        ];
        
        // Función auxiliar para construir URLs de paginación
        $buildPaginationUrl = function($pageNum) {
            return $this->buildPaginationUrl($pageNum);
        };
        
        // Si es una petición AJAX, devolver solo el contenido de productos
        if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            // Establecer variables necesarias para la vista
            $categoryId = $filters['category_id'];
            include APP_PATH . '/Views/store/index.php';
            return;
        }
        
        $pageTitle = 'Tienda - STYLOFITNESS';
        $pageDescription = 'Los mejores suplementos y accesorios deportivos con envío gratis';
        $additionalCSS = ['store.css'];
        $additionalJS = ['store.js'];
        
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/store/index.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
    
    public function category($categorySlug) {
        // Headers anti-caché agresivos para prevenir cacheo en navegadores
        if (!headers_sent()) {
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0, private');
            header('Pragma: no-cache');
            header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Vary: *');
            header('X-Accel-Expires: 0');
        }
        
        $category = $this->categoryModel->findBySlug($categorySlug);
        
        if (!$category) {
            AppHelper::setFlashMessage('error', 'Categoría no encontrada');
            AppHelper::redirect('/store');
            return;
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;
        
        $filters = [
            'search' => AppHelper::sanitize($_GET['search'] ?? ''),
            'category_id' => $category['id'],
            'brand' => AppHelper::sanitize($_GET['brand'] ?? ''),
            'price_min' => (float)($_GET['price_min'] ?? 0),
            'price_max' => (float)($_GET['price_max'] ?? 0),
            'sort' => AppHelper::sanitize($_GET['sort'] ?? 'name_asc'),
            'is_active' => true,
            'limit' => $perPage,
            'offset' => $offset
        ];
        
        $products = $this->productModel->getProducts($filters);
        $totalProducts = $this->productModel->countProducts($filters);
        
        // Subcategorías
        $subcategories = $this->categoryModel->getChildren($category['id']);
        
        // Datos para filtros
        $brands = $this->productModel->getBrands();
        $priceRange = $this->productModel->getPriceRange();
        
        // Paginación
        $totalPages = ceil($totalProducts / $perPage);
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalProducts,
            'per_page' => $perPage,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
            'previous_page' => $page - 1,
            'next_page' => $page + 1
        ];
        
        // Función auxiliar para construir URLs de paginación
        $buildPaginationUrl = function($pageNum) {
            return $this->buildPaginationUrl($pageNum);
        };
        
        $pageTitle = $category['name'] . ' - Tienda STYLOFITNESS';
        $pageDescription = $category['meta_description'] ?: $category['description'];
        $additionalCSS = ['store.css'];
        $additionalJS = ['store.js'];
        
        // Pasar la categoría actual a la vista
        $categoryId = $category['id'];
        
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/store/index.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
    
    public function product($productSlug) {
        // Headers anti-caché agresivos para prevenir cacheo en navegadores
        if (!headers_sent()) {
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0, private');
            header('Pragma: no-cache');
            header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Vary: *');
            header('X-Accel-Expires: 0');
        }
        
        $product = $this->productModel->findBySlug($productSlug);
        
        if (!$product) {
            AppHelper::setFlashMessage('error', 'Producto no encontrado');
            AppHelper::redirect('/store');
            return;
        }
        
        // Productos relacionados
        $relatedProducts = $this->productModel->getRelatedProducts($product['id'], $product['category_id'], 4);
        
        // Reseñas del producto
        $reviews = $this->productModel->getProductReviews($product['id'], 10);
        
        // Verificar si el usuario puede dejar reseña
        $canReview = false;
        if (AppHelper::isLoggedIn()) {
            $user = AppHelper::getCurrentUser();
            
            // Verificar si ha comprado el producto
            $hasPurchased = $this->db->count(
                "SELECT COUNT(*) FROM order_items oi
                 JOIN orders o ON oi.order_id = o.id
                 WHERE o.user_id = ? AND oi.product_id = ? AND o.payment_status = 'paid'",
                [$user['id'], $product['id']]
            ) > 0;
            
            // Verificar si ya ha dejado reseña
            $hasReviewed = $this->db->count(
                "SELECT COUNT(*) FROM product_reviews WHERE user_id = ? AND product_id = ?",
                [$user['id'], $product['id']]
            ) > 0;
            
            $canReview = $hasPurchased && !$hasReviewed;
        }
        
        // Breadcrumb
        $breadcrumb = [];
        if ($product['category_id']) {
            $breadcrumb = $this->categoryModel->getBreadcrumb($product['category_id']);
        }
        $breadcrumb[] = ['name' => $product['name'], 'slug' => $product['slug']];
        
        $pageTitle = $product['name'] . ' - STYLOFITNESS';
        $pageDescription = $product['meta_description'] ?: $product['short_description'];
        $additionalCSS = ['product.css'];
        $additionalJS = ['product.js'];
        
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/store/product.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
    
    public function search() {
        $query = AppHelper::sanitize($_GET['q'] ?? '');
        
        if (empty($query)) {
            AppHelper::redirect('/store');
            return;
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;
        
        $filters = [
            'search' => $query,
            'category_id' => (int)($_GET['category'] ?? 0),
            'brand' => AppHelper::sanitize($_GET['brand'] ?? ''),
            'price_min' => (float)($_GET['price_min'] ?? 0),
            'price_max' => (float)($_GET['price_max'] ?? 0),
            'sort' => AppHelper::sanitize($_GET['sort'] ?? 'relevance'),
            'is_active' => true,
            'limit' => $perPage,
            'offset' => $offset
        ];
        
        $products = $this->productModel->searchProducts($query, $perPage * 5); // Más resultados para búsqueda
        $totalProducts = count($products);
        
        // Aplicar filtros adicionales si es necesario
        if (!empty($filters['category_id']) || !empty($filters['brand']) || $filters['price_min'] > 0 || $filters['price_max'] > 0) {
            $products = $this->productModel->getProducts($filters);
            $totalProducts = $this->productModel->countProducts($filters);
        }
        
        // Paginación
        $totalPages = ceil($totalProducts / $perPage);
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalProducts,
            'per_page' => $perPage,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages
        ];
        
        // Datos para filtros
        $categories = $this->categoryModel->getMainCategories();
        $brands = $this->productModel->getBrands();
        $priceRange = $this->productModel->getPriceRange();
        
        $pageTitle = "Búsqueda: {$query} - STYLOFITNESS";
        $pageDescription = "Resultados de búsqueda para '{$query}'";
        $additionalCSS = ['store.css'];
        $additionalJS = ['store.js'];
        
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/store/search.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
    
    public function quickView() {
        header('Content-Type: application/json');
        
        $productId = (int)($_GET['id'] ?? 0);
        
        if ($productId <= 0) {
            echo json_encode(['error' => 'ID de producto inválido']);
            return;
        }
        
        $product = $this->productModel->findById($productId);
        
        if (!$product || !$product['is_active']) {
            echo json_encode(['error' => 'Producto no encontrado']);
            return;
        }
        
        // Formatear datos para la vista rápida
        $productData = [
            'id' => $product['id'],
            'name' => $product['name'],
            'short_description' => $product['short_description'],
            'price' => $product['price'],
            'sale_price' => $product['sale_price'],
            'images' => $product['images'],
            'stock_quantity' => $product['stock_quantity'],
            'avg_rating' => $product['avg_rating'],
            'reviews_count' => $product['reviews_count'],
            'specifications' => $product['specifications'],
            'brand' => $product['brand'],
            'category_name' => $product['category_name']
        ];
        
        echo json_encode([
            'success' => true,
            'product' => $productData
        ]);
    }
    
    public function addReview() {
        header('Content-Type: application/json');
        
        if (!AppHelper::isLoggedIn()) {
            echo json_encode(['error' => 'Debes iniciar sesión']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        $user = AppHelper::getCurrentUser();
        $productId = (int)($_POST['product_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $title = AppHelper::sanitize($_POST['title'] ?? '');
        $comment = AppHelper::sanitize($_POST['comment'] ?? '');
        $pros = AppHelper::sanitize($_POST['pros'] ?? '');
        $cons = AppHelper::sanitize($_POST['cons'] ?? '');
        
        // Validaciones
        if ($productId <= 0) {
            echo json_encode(['error' => 'Producto inválido']);
            return;
        }
        
        if ($rating < 1 || $rating > 5) {
            echo json_encode(['error' => 'Calificación debe ser entre 1 y 5']);
            return;
        }
        
        if (empty($comment)) {
            echo json_encode(['error' => 'El comentario es obligatorio']);
            return;
        }
        
        // Verificar que ha comprado el producto
        $hasPurchased = $this->db->count(
            "SELECT COUNT(*) FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             WHERE o.user_id = ? AND oi.product_id = ? AND o.payment_status = 'paid'",
            [$user['id'], $productId]
        ) > 0;
        
        if (!$hasPurchased) {
            echo json_encode(['error' => 'Solo puedes reseñar productos que hayas comprado']);
            return;
        }
        
        // Verificar que no ha dejado reseña antes
        $hasReviewed = $this->db->count(
            "SELECT COUNT(*) FROM product_reviews WHERE user_id = ? AND product_id = ?",
            [$user['id'], $productId]
        ) > 0;
        
        if ($hasReviewed) {
            echo json_encode(['error' => 'Ya has dejado una reseña para este producto']);
            return;
        }
        
        // Crear reseña
        $reviewData = [
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
            'pros' => $pros,
            'cons' => $cons,
            'is_verified_purchase' => true
        ];
        
        $reviewId = $this->productModel->addReview($productId, $user['id'], $reviewData);
        
        if ($reviewId) {
            echo json_encode([
                'success' => true,
                'message' => 'Reseña enviada correctamente. Será publicada tras su revisión.'
            ]);
        } else {
            echo json_encode(['error' => 'Error al enviar la reseña']);
        }
    }
    
    public function getFilters() {
        header('Content-Type: application/json');
        
        $categoryId = (int)($_GET['category_id'] ?? 0);
        
        $filters = [
            'categories' => $this->categoryModel->getMainCategories(),
            'brands' => $this->productModel->getBrands(),
            'price_range' => $this->productModel->getPriceRange()
        ];
        
        if ($categoryId > 0) {
            $filters['subcategories'] = $this->categoryModel->getChildren($categoryId);
        }
        
        echo json_encode([
            'success' => true,
            'filters' => $filters
        ]);
    }
    
    public function compare() {
        $productIds = $_GET['products'] ?? [];
        
        if (!is_array($productIds) || count($productIds) < 2 || count($productIds) > 4) {
            AppHelper::setFlashMessage('error', 'Selecciona entre 2 y 4 productos para comparar');
            AppHelper::redirect('/store');
            return;
        }
        
        $products = [];
        foreach ($productIds as $id) {
            $product = $this->productModel->findById((int)$id);
            if ($product && $product['is_active']) {
                $products[] = $product;
            }
        }
        
        if (count($products) < 2) {
            AppHelper::setFlashMessage('error', 'No se pudieron cargar los productos para comparar');
            AppHelper::redirect('/store');
            return;
        }
        
        $pageTitle = 'Comparar Productos - STYLOFITNESS';
        $additionalCSS = ['compare.css'];
        $additionalJS = ['compare.js'];
        
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/store/compare.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
    
    public function wishlist() {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }
        
        $user = AppHelper::getCurrentUser();
        
        $wishlistItems = $this->db->fetchAll(
            "SELECT w.*, p.name, p.slug, p.price, p.sale_price, p.images, p.stock_quantity
             FROM wishlists w
             JOIN products p ON w.product_id = p.id
             WHERE w.user_id = ? AND p.is_active = 1
             ORDER BY w.created_at DESC",
            [$user['id']]
        );
        
        $pageTitle = 'Lista de Deseos - STYLOFITNESS';
        $additionalCSS = ['wishlist.css'];
        $additionalJS = ['wishlist.js'];
        
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/store/wishlist.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
    
    public function addToWishlist() {
        header('Content-Type: application/json');
        
        if (!AppHelper::isLoggedIn()) {
            echo json_encode(['error' => 'Debes iniciar sesión']);
            return;
        }
        
        $user = AppHelper::getCurrentUser();
        $productId = (int)($_POST['product_id'] ?? 0);
        
        if ($productId <= 0) {
            echo json_encode(['error' => 'ID de producto inválido']);
            return;
        }
        
        // Verificar si ya está en la lista
        $exists = $this->db->count(
            "SELECT COUNT(*) FROM wishlists WHERE user_id = ? AND product_id = ?",
            [$user['id'], $productId]
        ) > 0;
        
        if ($exists) {
            echo json_encode(['error' => 'El producto ya está en tu lista de deseos']);
            return;
        }
        
        // Añadir a la lista
        $added = $this->db->insert(
            "INSERT INTO wishlists (user_id, product_id, created_at) VALUES (?, ?, NOW())",
            [$user['id'], $productId]
        );
        
        if ($added) {
            echo json_encode([
                'success' => true,
                'message' => 'Producto añadido a la lista de deseos'
            ]);
        } else {
            echo json_encode(['error' => 'Error al añadir a la lista de deseos']);
        }
    }
    
    public function removeFromWishlist() {
        header('Content-Type: application/json');
        
        if (!AppHelper::isLoggedIn()) {
            echo json_encode(['error' => 'Debes iniciar sesión']);
            return;
        }
        
        $user = AppHelper::getCurrentUser();
        $productId = (int)($_POST['product_id'] ?? 0);
        
        if ($productId <= 0) {
            echo json_encode(['error' => 'ID de producto inválido']);
            return;
        }
        
        $removed = $this->db->query(
            "DELETE FROM wishlists WHERE user_id = ? AND product_id = ?",
            [$user['id'], $productId]
        );
        
        if ($removed) {
            echo json_encode([
                'success' => true,
                'message' => 'Producto removido de la lista de deseos'
            ]);
        } else {
            echo json_encode(['error' => 'Error al remover de la lista de deseos']);
        }
    }
    
    /**
     * Construye la URL para la paginación manteniendo los filtros actuales
     * @param int $page Número de página
     * @return string URL con parámetros de paginación y filtros
     */
    private function buildPaginationUrl($page) {
        $params = $_GET;
        $params['page'] = $page;
        
        $url = AppHelper::baseUrl('store');
        
        if (!empty($params['category'])) {
            $url = AppHelper::baseUrl('store/category/' . $this->categoryModel->getSlugById($params['category']));
            unset($params['category']);
        }
        
        $queryString = http_build_query($params);
        
        return $url . ($queryString ? '?' . $queryString : '');
    }
}
