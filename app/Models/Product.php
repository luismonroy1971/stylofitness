<?php
/**
 * Modelo de Productos - STYLOFITNESS
 * Gestión completa de productos y catálogo
 */

class Product {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO products (
            category_id, name, slug, description, short_description, sku, 
            price, sale_price, cost_price, stock_quantity, min_stock_level,
            weight, dimensions, images, gallery, specifications, nutritional_info,
            usage_instructions, ingredients, warnings, brand, is_featured, 
            is_active, meta_title, meta_description, meta_keywords, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        return $this->db->insert($sql, [
            $data['category_id'] ?? null,
            $data['name'],
            $data['slug'] ?? $this->generateSlug($data['name']),
            $data['description'] ?? '',
            $data['short_description'] ?? '',
            $data['sku'],
            $data['price'],
            $data['sale_price'] ?? null,
            $data['cost_price'] ?? null,
            $data['stock_quantity'] ?? 0,
            $data['min_stock_level'] ?? 5,
            $data['weight'] ?? null,
            $data['dimensions'] ?? null,
            json_encode($data['images'] ?? []),
            json_encode($data['gallery'] ?? []),
            json_encode($data['specifications'] ?? []),
            json_encode($data['nutritional_info'] ?? []),
            $data['usage_instructions'] ?? null,
            $data['ingredients'] ?? null,
            $data['warnings'] ?? null,
            $data['brand'] ?? null,
            $data['is_featured'] ?? false,
            $data['is_active'] ?? true,
            $data['meta_title'] ?? null,
            $data['meta_description'] ?? null,
            $data['meta_keywords'] ?? null
        ]);
    }
    
    public function findById($id) {
        $product = $this->db->fetch(
            "SELECT p.*, pc.name as category_name, pc.slug as category_slug
             FROM products p
             LEFT JOIN product_categories pc ON p.category_id = pc.id
             WHERE p.id = ?",
            [$id]
        );
        
        if ($product) {
            $product['images'] = !empty($product['images']) ? json_decode($product['images'], true) : [];
            $product['gallery'] = !empty($product['gallery']) ? json_decode($product['gallery'], true) : [];
            $product['specifications'] = !empty($product['specifications']) ? json_decode($product['specifications'], true) : [];
            $product['nutritional_info'] = !empty($product['nutritional_info']) ? json_decode($product['nutritional_info'], true) : [];
        }
        
        return $product;
    }
    
    public function findBySlug($slug) {
        $product = $this->db->fetch(
            "SELECT p.*, pc.name as category_name, pc.slug as category_slug
             FROM products p
             LEFT JOIN product_categories pc ON p.category_id = pc.id
             WHERE p.slug = ? AND p.is_active = 1",
            [$slug]
        );
        
        if ($product) {
            $product['images'] = !empty($product['images']) ? json_decode($product['images'], true) : [];
            $product['gallery'] = !empty($product['gallery']) ? json_decode($product['gallery'], true) : [];
            $product['specifications'] = !empty($product['specifications']) ? json_decode($product['specifications'], true) : [];
            $product['nutritional_info'] = !empty($product['nutritional_info']) ? json_decode($product['nutritional_info'], true) : [];
            
            // Incrementar contador de visualizaciones
            $this->incrementViews($product['id']);
        }
        
        return $product;
    }
    
    public function findBySku($sku) {
        return $this->db->fetch(
            "SELECT * FROM products WHERE sku = ?",
            [$sku]
        );
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = [
            'category_id', 'name', 'slug', 'description', 'short_description', 
            'sku', 'price', 'sale_price', 'cost_price', 'stock_quantity', 
            'min_stock_level', 'weight', 'dimensions', 'images', 'gallery',
            'specifications', 'nutritional_info', 'usage_instructions', 
            'ingredients', 'warnings', 'brand', 'is_featured', 'is_active',
            'meta_title', 'meta_description', 'meta_keywords'
        ];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "{$key} = ?";
                
                // Encodificar arrays como JSON
                if (in_array($key, ['images', 'gallery', 'specifications', 'nutritional_info'])) {
                    $values[] = json_encode($value);
                } else {
                    $values[] = $value;
                }
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = NOW()";
        $values[] = $id;
        
        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->db->query($sql, $values);
    }
    
    public function getProducts($filters = []) {
        $where = ["p.is_active = 1"];
        $params = [];
        
        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ? OR p.brand LIKE ? OR p.sku LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, array_fill(0, 5, $searchTerm));
        }
        
        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['category_slug'])) {
            $where[] = "pc.slug = ?";
            $params[] = $filters['category_slug'];
        }
        
        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $where[] = "p.is_featured = ?";
            $params[] = (bool)$filters['is_featured'];
        }
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $where[] = "p.is_active = ?";
            $params[] = (bool)$filters['is_active'];
        }
        
        if (!empty($filters['price_min'])) {
            $where[] = "COALESCE(p.sale_price, p.price) >= ?";
            $params[] = $filters['price_min'];
        }
        
        if (!empty($filters['price_max'])) {
            $where[] = "COALESCE(p.sale_price, p.price) <= ?";
            $params[] = $filters['price_max'];
        }
        
        if (!empty($filters['brand'])) {
            $where[] = "p.brand = ?";
            $params[] = $filters['brand'];
        }
        
        if (!empty($filters['in_stock'])) {
            $where[] = "p.stock_quantity > 0";
        }
        
        $orderBy = "p.created_at DESC";
        
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'name_asc':
                    $orderBy = "p.name ASC";
                    break;
                case 'name_desc':
                    $orderBy = "p.name DESC";
                    break;
                case 'price_asc':
                    $orderBy = "COALESCE(p.sale_price, p.price) ASC";
                    break;
                case 'price_desc':
                    $orderBy = "COALESCE(p.sale_price, p.price) DESC";
                    break;
                case 'rating':
                    $orderBy = "p.avg_rating DESC";
                    break;
                case 'popular':
                    $orderBy = "p.sales_count DESC";
                    break;
            }
        }
        
        $sql = "SELECT p.*, pc.name as category_name, pc.slug as category_slug
                FROM products p
                LEFT JOIN product_categories pc ON p.category_id = pc.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY {$orderBy}";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
            if (!empty($filters['offset'])) {
                $sql .= " OFFSET " . (int)$filters['offset'];
            }
        }
        
        $products = $this->db->fetchAll($sql, $params);
        
        // Decodificar JSON fields
        foreach ($products as &$product) {
            $product['images'] = !empty($product['images']) ? json_decode($product['images'], true) : [];
            $product['gallery'] = !empty($product['gallery']) ? json_decode($product['gallery'], true) : [];
            $product['specifications'] = !empty($product['specifications']) ? json_decode($product['specifications'], true) : [];
            $product['nutritional_info'] = !empty($product['nutritional_info']) ? json_decode($product['nutritional_info'], true) : [];
        }
        
        return $products;
    }
    
    public function countProducts($filters = []) {
        $where = ["p.is_active = 1"];
        $params = [];
        
        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ? OR p.brand LIKE ? OR p.sku LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, array_fill(0, 5, $searchTerm));
        }
        
        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['category_slug'])) {
            $where[] = "pc.slug = ?";
            $params[] = $filters['category_slug'];
        }
        
        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $where[] = "p.is_featured = ?";
            $params[] = (bool)$filters['is_featured'];
        }
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $where[] = "p.is_active = ?";
            $params[] = (bool)$filters['is_active'];
        }
        
        if (!empty($filters['price_min'])) {
            $where[] = "COALESCE(p.sale_price, p.price) >= ?";
            $params[] = $filters['price_min'];
        }
        
        if (!empty($filters['price_max'])) {
            $where[] = "COALESCE(p.sale_price, p.price) <= ?";
            $params[] = $filters['price_max'];
        }
        
        if (!empty($filters['brand'])) {
            $where[] = "p.brand = ?";
            $params[] = $filters['brand'];
        }
        
        if (!empty($filters['in_stock'])) {
            $where[] = "p.stock_quantity > 0";
        }
        
        $sql = "SELECT COUNT(*) FROM products p
                LEFT JOIN product_categories pc ON p.category_id = pc.id
                WHERE " . implode(' AND ', $where);
        
        return $this->db->count($sql, $params);
    }
    
    public function getFeaturedProducts($limit = 8) {
        return $this->getProducts([
            'is_featured' => true,
            'is_active' => true,
            'limit' => $limit,
            'sort' => 'popular'
        ]);
    }
    
    public function getPromotionalProducts($limit = 10) {
        $sql = "SELECT p.*, pc.name as category_name, pc.slug as category_slug,
                ROUND(((p.price - p.sale_price) / p.price) * 100) as discount_percentage
                FROM products p 
                LEFT JOIN product_categories pc ON p.category_id = pc.id 
                WHERE p.sale_price IS NOT NULL 
                AND p.sale_price < p.price 
                AND p.is_active = 1 
                ORDER BY discount_percentage DESC 
                LIMIT ?";
        
        $products = $this->db->fetchAll($sql, [$limit]);
        
        foreach ($products as &$product) {
            $product['images'] = !empty($product['images']) ? json_decode($product['images'], true) : [];
        }
        
        return $products;
    }
    
    public function getRelatedProducts($productId, $categoryId, $limit = 4) {
        return $this->getProducts([
            'category_id' => $categoryId,
            'is_active' => true,
            'limit' => $limit
        ]);
    }
    
    public function getRecommendationsForUser($userId, $limit = 5) {
        // Obtener objetivos de rutinas del usuario
        $userObjectives = $this->db->fetchAll(
            "SELECT DISTINCT objective FROM routines WHERE client_id = ? AND is_active = 1",
            [$userId]
        );
        
        $objectiveCategories = [];
        foreach ($userObjectives as $obj) {
            switch ($obj['objective']) {
                case 'weight_loss':
                    $objectiveCategories[] = 'quemadores-grasa';
                    break;
                case 'muscle_gain':
                    $objectiveCategories[] = 'proteinas';
                    $objectiveCategories[] = 'creatina';
                    break;
                case 'strength':
                    $objectiveCategories[] = 'creatina';
                    $objectiveCategories[] = 'pre-entrenos';
                    break;
                case 'endurance':
                    $objectiveCategories[] = 'aminoacidos';
                    break;
            }
        }
        
        if (empty($objectiveCategories)) {
            // Productos más populares por defecto
            return $this->getProducts([
                'is_active' => true,
                'limit' => $limit,
                'sort' => 'popular'
            ]);
        }
        
        $categoryConditions = str_repeat('pc.slug = ? OR ', count($objectiveCategories));
        $categoryConditions = rtrim($categoryConditions, ' OR ');
        
        $sql = "SELECT p.*, pc.name as category_name, pc.slug as category_slug
                FROM products p
                JOIN product_categories pc ON p.category_id = pc.id
                WHERE ({$categoryConditions}) AND p.is_active = 1
                ORDER BY p.sales_count DESC, p.avg_rating DESC
                LIMIT ?";
        
        $params = array_merge($objectiveCategories, [$limit]);
        $products = $this->db->fetchAll($sql, $params);
        
        foreach ($products as &$product) {
            $product['images'] = !empty($product['images']) ? json_decode($product['images'], true) : [];
        }
        
        return $products;
    }
    
    public function searchProducts($query, $limit = 20) {
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT p.*, pc.name as category_name,
                MATCH(p.name, p.description, p.short_description) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
                FROM products p
                LEFT JOIN product_categories pc ON p.category_id = pc.id
                WHERE p.is_active = 1 AND (
                    MATCH(p.name, p.description, p.short_description) AGAINST(? IN NATURAL LANGUAGE MODE)
                    OR p.name LIKE ?
                    OR p.brand LIKE ?
                    OR p.sku LIKE ?
                )
                ORDER BY relevance DESC, p.sales_count DESC
                LIMIT ?";
        
        $products = $this->db->fetchAll($sql, [$query, $query, $searchTerm, $searchTerm, $searchTerm, $limit]);
        
        foreach ($products as &$product) {
            $product['images'] = json_decode($product['images'], true) ?: [];
        }
        
        return $products;
    }
    
    public function getBrands() {
        return $this->db->fetchAll(
            "SELECT brand, COUNT(*) as product_count
             FROM products 
             WHERE brand IS NOT NULL AND brand != '' AND is_active = 1
             GROUP BY brand
             ORDER BY brand"
        );
    }
    
    public function getPriceRange() {
        return $this->db->fetch(
            "SELECT 
                MIN(COALESCE(sale_price, price)) as min_price,
                MAX(COALESCE(sale_price, price)) as max_price
             FROM products 
             WHERE is_active = 1"
        );
    }
    
    public function updateStock($productId, $quantity, $operation = 'decrease') {
        $operator = $operation === 'increase' ? '+' : '-';
        
        return $this->db->query(
            "UPDATE products SET stock_quantity = stock_quantity {$operator} ? WHERE id = ?",
            [$quantity, $productId]
        );
    }
    
    public function getLowStockProducts($threshold = null) {
        $sql = "SELECT p.*, pc.name as category_name
                FROM products p
                LEFT JOIN product_categories pc ON p.category_id = pc.id
                WHERE p.is_active = 1 AND p.stock_quantity <= " . 
                ($threshold ? "?" : "p.min_stock_level") . "
                ORDER BY p.stock_quantity ASC";
        
        $params = $threshold ? [$threshold] : [];
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function incrementViews($productId) {
        return $this->db->query(
            "UPDATE products SET views_count = views_count + 1 WHERE id = ?",
            [$productId]
        );
    }
    
    public function addReview($productId, $userId, $reviewData) {
        $sql = "INSERT INTO product_reviews (
            product_id, user_id, rating, title, comment, pros, cons, 
            images, is_verified_purchase, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $reviewId = $this->db->insert($sql, [
            $productId,
            $userId,
            $reviewData['rating'],
            $reviewData['title'] ?? null,
            $reviewData['comment'] ?? null,
            $reviewData['pros'] ?? null,
            $reviewData['cons'] ?? null,
            json_encode($reviewData['images'] ?? []),
            $reviewData['is_verified_purchase'] ?? false
        ]);
        
        if ($reviewId) {
            $this->updateProductRating($productId);
        }
        
        return $reviewId;
    }
    
    public function getProductReviews($productId, $limit = 10, $offset = 0) {
        return $this->db->fetchAll(
            "SELECT pr.*, u.first_name, u.last_name
             FROM product_reviews pr
             JOIN users u ON pr.user_id = u.id
             WHERE pr.product_id = ? AND pr.is_approved = 1
             ORDER BY pr.created_at DESC
             LIMIT ? OFFSET ?",
            [$productId, $limit, $offset]
        );
    }
    
    private function updateProductRating($productId) {
        $stats = $this->db->fetch(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count
             FROM product_reviews 
             WHERE product_id = ? AND is_approved = 1",
            [$productId]
        );
        
        $this->update($productId, [
            'avg_rating' => round($stats['avg_rating'], 2),
            'reviews_count' => $stats['review_count']
        ]);
    }
    
    public function validateProduct($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        }
        
        if (empty($data['sku'])) {
            $errors['sku'] = 'El SKU es obligatorio';
        } elseif ($this->skuExists($data['sku'], $data['id'] ?? null)) {
            $errors['sku'] = 'Este SKU ya está en uso';
        }
        
        if (!isset($data['price']) || $data['price'] <= 0) {
            $errors['price'] = 'El precio debe ser mayor a 0';
        }
        
        if (isset($data['sale_price']) && $data['sale_price'] >= $data['price']) {
            $errors['sale_price'] = 'El precio de oferta debe ser menor al precio regular';
        }
        
        if (!isset($data['stock_quantity']) || $data['stock_quantity'] < 0) {
            $errors['stock_quantity'] = 'El stock no puede ser negativo';
        }
        
        return $errors;
    }
    
    public function skuExists($sku, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM products WHERE sku = ?";
        $params = [$sku];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        return $this->db->count($sql, $params) > 0;
    }
    
    public function delete($id) {
        // Solo marcar como inactivo en lugar de eliminar
        return $this->update($id, ['is_active' => false]);
    }
    
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Verificar unicidad
        $baseSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug) {
        return $this->db->count(
            "SELECT COUNT(*) FROM products WHERE slug = ?",
            [$slug]
        ) > 0;
    }
}
