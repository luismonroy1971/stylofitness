<?php
/**
 * Controlador de Carrito - STYLOFITNESS
 * Gestión del carrito de compras
 */

class CartController {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function index() {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }
        
        $user = AppHelper::getCurrentUser();
        $cartItems = $this->getCartItems($user['id']);
        $cartTotals = $this->calculateTotals($cartItems);
        $recommendedProducts = $this->getRecommendedProducts($user['id'], 4);
        
        $pageTitle = 'Carrito de Compras - STYLOFITNESS';
        $additionalCSS = ['cart.css'];
        $additionalJS = ['cart.js'];
        
        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/store/cart.php';
        include APP_PATH . '/Views/layout/footer.php';
    }
    
    public function add() {
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
        $variationId = !empty($_POST['variation_id']) ? (int)$_POST['variation_id'] : null;
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if ($productId <= 0 || $quantity <= 0) {
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        
        // Verificar que el producto existe y está activo
        $productModel = new Product();
        $product = $productModel->findById($productId);
        
        if (!$product || !$product['is_active']) {
            echo json_encode(['error' => 'Producto no encontrado']);
            return;
        }
        
        // Verificar stock
        if ($product['stock_quantity'] < $quantity) {
            echo json_encode(['error' => 'Stock insuficiente']);
            return;
        }
        
        // Verificar si ya existe en el carrito
        $existingItem = $this->db->fetch(
            "SELECT * FROM cart_items WHERE user_id = ? AND product_id = ? AND variation_id " . 
            ($variationId ? "= ?" : "IS NULL"),
            $variationId ? [$user['id'], $productId, $variationId] : [$user['id'], $productId]
        );
        
        if ($existingItem) {
            // Actualizar cantidad
            $newQuantity = $existingItem['quantity'] + $quantity;
            
            if ($newQuantity > $product['stock_quantity']) {
                echo json_encode(['error' => 'Stock insuficiente']);
                return;
            }
            
            $updated = $this->db->query(
                "UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?",
                [$newQuantity, $existingItem['id']]
            );
            
            if ($updated) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cantidad actualizada en el carrito',
                    'cart_count' => $this->getCartCount($user['id'])
                ]);
            } else {
                echo json_encode(['error' => 'Error al actualizar el carrito']);
            }
        } else {
            // Añadir nuevo item
            $price = $product['sale_price'] ?? $product['price'];
            
            $itemId = $this->db->insert(
                "INSERT INTO cart_items (user_id, product_id, variation_id, quantity, price, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
                [$user['id'], $productId, $variationId, $quantity, $price]
            );
            
            if ($itemId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto añadido al carrito',
                    'cart_count' => $this->getCartCount($user['id'])
                ]);
            } else {
                echo json_encode(['error' => 'Error al añadir al carrito']);
            }
        }
    }
    
    public function update() {
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
        $itemId = (int)($_POST['item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if ($itemId <= 0 || $quantity < 0) {
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        
        // Verificar que el item pertenece al usuario
        $item = $this->db->fetch(
            "SELECT ci.*, p.stock_quantity FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.id = ? AND ci.user_id = ?",
            [$itemId, $user['id']]
        );
        
        if (!$item) {
            echo json_encode(['error' => 'Item no encontrado']);
            return;
        }
        
        if ($quantity == 0) {
            // Eliminar item
            $deleted = $this->db->query("DELETE FROM cart_items WHERE id = ?", [$itemId]);
            
            if ($deleted) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto eliminado del carrito',
                    'cart_count' => $this->getCartCount($user['id']),
                    'cart_totals' => $this->getCartTotals($user['id'])
                ]);
            } else {
                echo json_encode(['error' => 'Error al eliminar del carrito']);
            }
        } else {
            // Verificar stock
            if ($quantity > $item['stock_quantity']) {
                echo json_encode(['error' => 'Stock insuficiente']);
                return;
            }
            
            // Actualizar cantidad
            $updated = $this->db->query(
                "UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?",
                [$quantity, $itemId]
            );
            
            if ($updated) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Carrito actualizado',
                    'cart_count' => $this->getCartCount($user['id']),
                    'cart_totals' => $this->getCartTotals($user['id'])
                ]);
            } else {
                echo json_encode(['error' => 'Error al actualizar el carrito']);
            }
        }
    }
    
    public function remove() {
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
        $itemId = (int)($_POST['item_id'] ?? 0);
        
        if ($itemId <= 0) {
            echo json_encode(['error' => 'ID de item inválido']);
            return;
        }
        
        $deleted = $this->db->query(
            "DELETE FROM cart_items WHERE id = ? AND user_id = ?",
            [$itemId, $user['id']]
        );
        
        if ($deleted) {
            echo json_encode([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'cart_count' => $this->getCartCount($user['id']),
                'cart_totals' => $this->getCartTotals($user['id'])
            ]);
        } else {
            echo json_encode(['error' => 'Error al eliminar del carrito']);
        }
    }
    
    public function clear() {
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
        
        $deleted = $this->db->query(
            "DELETE FROM cart_items WHERE user_id = ?",
            [$user['id']]
        );
        
        if ($deleted) {
            echo json_encode([
                'success' => true,
                'message' => 'Carrito vaciado',
                'cart_count' => 0
            ]);
        } else {
            echo json_encode(['error' => 'Error al vaciar el carrito']);
        }
    }
    
    public function getCount() {
        header('Content-Type: application/json');
        
        if (!AppHelper::isLoggedIn()) {
            echo json_encode(['count' => 0]);
            return;
        }
        
        $user = AppHelper::getCurrentUser();
        $count = $this->getCartCount($user['id']);
        
        echo json_encode(['count' => $count]);
    }
    
    private function getCartItems($userId) {
        return $this->db->fetchAll(
            "SELECT ci.*, p.name, p.slug, p.images, p.stock_quantity, p.sale_price,
             pv.name as variation_name, pv.sku as variation_sku
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             LEFT JOIN product_variations pv ON ci.variation_id = pv.id
             WHERE ci.user_id = ? AND p.is_active = 1
             ORDER BY ci.created_at DESC",
            [$userId]
        );
    }
    
    private function getCartCount($userId) {
        return $this->db->count(
            "SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE user_id = ?",
            [$userId]
        );
    }
    
    private function getCartTotals($userId) {
        $items = $this->getCartItems($userId);
        return $this->calculateTotals($items);
    }
    
    private function calculateTotals($items) {
        $subtotal = 0;
        $totalItems = 0;
        
        foreach ($items as $item) {
            $price = $item['sale_price'] ?? $item['price'];
            $subtotal += $price * $item['quantity'];
            $totalItems += $item['quantity'];
        }
        
        $shipping = $subtotal >= 150 ? 0 : 15; // Envío gratis por compras mayores a S/150
        $tax = $subtotal * 0.18; // IGV 18%
        $total = $subtotal + $shipping + $tax;
        
        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'item_count' => $totalItems,
            'free_shipping_remaining' => $subtotal < 150 ? (150 - $subtotal) : 0
        ];
    }
    
    private function getRecommendedProducts($userId, $limit = 4) {
        $productModel = new Product();
        return $productModel->getRecommendationsForUser($userId, $limit);
    }
    
    public function applyCoupon() {
        header('Content-Type: application/json');
        
        if (!AppHelper::isLoggedIn()) {
            echo json_encode(['error' => 'Debes iniciar sesión']);
            return;
        }
        
        $couponCode = AppHelper::sanitize($_POST['coupon_code'] ?? '');
        
        if (empty($couponCode)) {
            echo json_encode(['error' => 'Código de cupón requerido']);
            return;
        }
        
        // Verificar cupón
        $coupon = $this->db->fetch(
            "SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND valid_from <= NOW() AND valid_until >= NOW()",
            [$couponCode]
        );
        
        if (!$coupon) {
            echo json_encode(['error' => 'Cupón inválido o expirado']);
            return;
        }
        
        $user = AppHelper::getCurrentUser();
        $cartTotals = $this->getCartTotals($user['id']);
        
        // Verificar monto mínimo
        if ($cartTotals['subtotal'] < $coupon['minimum_amount']) {
            echo json_encode(['error' => "Monto mínimo requerido: S/{$coupon['minimum_amount']}"]);
            return;
        }
        
        // Calcular descuento
        if ($coupon['type'] === 'percentage') {
            $discount = ($cartTotals['subtotal'] * $coupon['value']) / 100;
        } else {
            $discount = $coupon['value'];
        }
        
        // Aplicar límite máximo si existe
        if ($coupon['maximum_discount'] && $discount > $coupon['maximum_discount']) {
            $discount = $coupon['maximum_discount'];
        }
        
        // Guardar cupón en sesión
        $_SESSION['applied_coupon'] = [
            'id' => $coupon['id'],
            'code' => $coupon['code'],
            'discount' => $discount,
            'type' => $coupon['type'],
            'value' => $coupon['value']
        ];
        
        // Recalcular totales
        $newTotals = $cartTotals;
        $newTotals['discount'] = $discount;
        $newTotals['total'] = $cartTotals['total'] - $discount;
        
        echo json_encode([
            'success' => true,
            'message' => 'Cupón aplicado correctamente',
            'discount' => $discount,
            'totals' => $newTotals
        ]);
    }
    
    public function removeCoupon() {
        header('Content-Type: application/json');
        
        unset($_SESSION['applied_coupon']);
        
        if (AppHelper::isLoggedIn()) {
            $user = AppHelper::getCurrentUser();
            $totals = $this->getCartTotals($user['id']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Cupón removido',
                'totals' => $totals
            ]);
        } else {
            echo json_encode(['success' => true]);
        }
    }
}
