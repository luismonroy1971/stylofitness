<?php
/**
 * Modelo de Pedidos - STYLOFITNESS
 * Gestión completa de pedidos y transacciones
 */

class Order {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO orders (
            user_id, order_number, status, payment_status, payment_method, 
            subtotal, tax_amount, shipping_amount, discount_amount, total_amount, 
            currency, billing_address, shipping_address, shipping_method, notes, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        return $this->db->insert($sql, [
            $data['user_id'],
            $this->generateOrderNumber(),
            $data['status'] ?? 'pending',
            $data['payment_status'] ?? 'pending',
            $data['payment_method'] ?? null,
            $data['subtotal'],
            $data['tax_amount'] ?? 0,
            $data['shipping_amount'] ?? 0,
            $data['discount_amount'] ?? 0,
            $data['total_amount'],
            $data['currency'] ?? 'PEN',
            json_encode($data['billing_address'] ?? []),
            json_encode($data['shipping_address'] ?? []),
            $data['shipping_method'] ?? null,
            $data['notes'] ?? null
        ]);
    }
    
    public function findById($id) {
        $order = $this->db->fetch(
            "SELECT o.*, u.first_name, u.last_name, u.email 
             FROM orders o
             JOIN users u ON o.user_id = u.id 
             WHERE o.id = ?",
            [$id]
        );
        
        if ($order) {
            $order['billing_address'] = json_decode($order['billing_address'], true);
            $order['shipping_address'] = json_decode($order['shipping_address'], true);
            $order['items'] = $this->getOrderItems($id);
        }
        
        return $order;
    }
    
    public function findByOrderNumber($orderNumber) {
        $order = $this->db->fetch(
            "SELECT * FROM orders WHERE order_number = ?",
            [$orderNumber]
        );
        
        if ($order) {
            $order['billing_address'] = json_decode($order['billing_address'], true);
            $order['shipping_address'] = json_decode($order['shipping_address'], true);
            $order['items'] = $this->getOrderItems($order['id']);
        }
        
        return $order;
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['status', 'payment_status', 'payment_method', 'payment_reference', 'tracking_number', 'internal_notes'])) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = NOW()";
        $values[] = $id;
        
        $sql = "UPDATE orders SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->db->query($sql, $values);
    }
    
    public function getOrders($filters = []) {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['search'])) {
            $where[] = "(o.order_number LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($filters['status'])) {
            $where[] = "o.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['payment_status'])) {
            $where[] = "o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(o.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(o.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['user_id'])) {
            $where[] = "o.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email,
                COUNT(oi.id) as item_count
                FROM orders o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE " . implode(' AND ', $where) . "
                GROUP BY o.id
                ORDER BY o.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
            if (!empty($filters['offset'])) {
                $sql .= " OFFSET " . (int)$filters['offset'];
            }
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function countOrders($filters = []) {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['search'])) {
            $where[] = "(o.order_number LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($filters['status'])) {
            $where[] = "o.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['payment_status'])) {
            $where[] = "o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(o.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(o.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['user_id'])) {
            $where[] = "o.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        $sql = "SELECT COUNT(DISTINCT o.id) FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE " . implode(' AND ', $where);
        
        return $this->db->count($sql, $params);
    }
    
    public function getOrderItems($orderId) {
        return $this->db->fetchAll(
            "SELECT oi.*, p.name as product_name, p.images, p.slug
             FROM order_items oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?
             ORDER BY oi.id",
            [$orderId]
        );
    }
    
    public function addItem($orderId, $itemData) {
        $sql = "INSERT INTO order_items (
            order_id, product_id, variation_id, product_name, product_sku,
            quantity, unit_price, total_price, product_data, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        return $this->db->insert($sql, [
            $orderId,
            $itemData['product_id'],
            $itemData['variation_id'] ?? null,
            $itemData['product_name'],
            $itemData['product_sku'],
            $itemData['quantity'],
            $itemData['unit_price'],
            $itemData['total_price'],
            json_encode($itemData['product_data'] ?? [])
        ]);
    }
    
    public function getUserOrders($userId, $limit = 10, $offset = 0) {
        return $this->db->fetchAll(
            "SELECT o.*, COUNT(oi.id) as item_count
             FROM orders o
             LEFT JOIN order_items oi ON o.id = oi.order_id
             WHERE o.user_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC
             LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );
    }
    
    public function getRecentOrders($limit = 10) {
        return $this->db->fetchAll(
            "SELECT o.*, u.first_name, u.last_name, u.email
             FROM orders o
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }
    
    public function getSalesStats($dateFrom, $dateTo) {
        $stats = [];
        
        // Ventas totales
        $totalSales = $this->db->fetch(
            "SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(total_amount), 0) as total_revenue,
                AVG(total_amount) as average_order_value
             FROM orders 
             WHERE DATE(created_at) BETWEEN ? AND ? 
             AND payment_status = 'paid'",
            [$dateFrom, $dateTo]
        );
        
        $stats['total_orders'] = (int)$totalSales['total_orders'];
        $stats['total_revenue'] = (float)$totalSales['total_revenue'];
        $stats['average_order_value'] = (float)$totalSales['average_order_value'];
        
        // Ventas por día
        $dailySales = $this->db->fetchAll(
            "SELECT 
                DATE(created_at) as date,
                COUNT(*) as orders,
                SUM(total_amount) as revenue
             FROM orders 
             WHERE DATE(created_at) BETWEEN ? AND ? 
             AND payment_status = 'paid'
             GROUP BY DATE(created_at)
             ORDER BY date",
            [$dateFrom, $dateTo]
        );
        
        $stats['daily_sales'] = $dailySales;
        
        // Estados de pedidos
        $statusStats = $this->db->fetchAll(
            "SELECT status, COUNT(*) as count
             FROM orders 
             WHERE DATE(created_at) BETWEEN ? AND ?
             GROUP BY status",
            [$dateFrom, $dateTo]
        );
        
        $stats['status_distribution'] = $statusStats;
        
        // Productos más vendidos
        $topProducts = $this->db->fetchAll(
            "SELECT 
                oi.product_name,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.total_price) as total_revenue
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             WHERE DATE(o.created_at) BETWEEN ? AND ?
             AND o.payment_status = 'paid'
             GROUP BY oi.product_id, oi.product_name
             ORDER BY total_quantity DESC
             LIMIT 10",
            [$dateFrom, $dateTo]
        );
        
        $stats['top_products'] = $topProducts;
        
        return $stats;
    }
    
    public function updateStatus($orderId, $status, $notes = null) {
        $data = ['status' => $status];
        if ($notes) {
            $data['internal_notes'] = $notes;
        }
        
        $updated = $this->update($orderId, $data);
        
        if ($updated) {
            // Registrar cambio de estado
            $this->logStatusChange($orderId, $status, $notes);
            
            // Enviar notificación al cliente si es necesario
            if (in_array($status, ['processing', 'shipped', 'delivered'])) {
                $this->sendStatusUpdateEmail($orderId, $status);
            }
        }
        
        return $updated;
    }
    
    public function processPayment($orderId, $paymentData) {
        $order = $this->findById($orderId);
        if (!$order) {
            return false;
        }
        
        // Actualizar estado de pago
        $updateData = [
            'payment_status' => 'paid',
            'payment_method' => $paymentData['method'],
            'payment_reference' => $paymentData['reference'],
            'status' => 'processing'
        ];
        
        $updated = $this->update($orderId, $updateData);
        
        if ($updated) {
            // Actualizar stock de productos
            $this->updateProductStock($orderId);
            
            // Enviar email de confirmación
            $this->sendOrderConfirmationEmail($orderId);
            
            return true;
        }
        
        return false;
    }
    
    private function generateOrderNumber() {
        $prefix = 'SF-' . date('Ymd') . '-';
        $lastOrder = $this->db->fetch(
            "SELECT order_number FROM orders 
             WHERE order_number LIKE ? 
             ORDER BY created_at DESC LIMIT 1",
            [$prefix . '%']
        );
        
        if ($lastOrder) {
            $lastNumber = (int)substr($lastOrder['order_number'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
    
    private function logStatusChange($orderId, $status, $notes) {
        $this->db->query(
            "INSERT INTO order_status_history (order_id, status, notes, created_at) VALUES (?, ?, ?, NOW())",
            [$orderId, $status, $notes]
        );
    }
    
    private function sendStatusUpdateEmail($orderId, $status) {
        // Implementar envío de email de actualización de estado
        error_log("Should send status update email for order {$orderId} with status {$status}");
    }
    
    private function sendOrderConfirmationEmail($orderId) {
        // Implementar envío de email de confirmación
        error_log("Should send order confirmation email for order {$orderId}");
    }
    
    private function updateProductStock($orderId) {
        $items = $this->getOrderItems($orderId);
        
        foreach ($items as $item) {
            $this->db->query(
                "UPDATE products 
                 SET stock_quantity = stock_quantity - ?, 
                     sales_count = sales_count + ? 
                 WHERE id = ?",
                [$item['quantity'], $item['quantity'], $item['product_id']]
            );
        }
    }
    
    public function validateOrder($data) {
        $errors = [];
        
        if (empty($data['user_id'])) {
            $errors['user_id'] = 'ID de usuario es obligatorio';
        }
        
        if (empty($data['items']) || !is_array($data['items'])) {
            $errors['items'] = 'Debe incluir al menos un producto';
        }
        
        if (empty($data['billing_address'])) {
            $errors['billing_address'] = 'Dirección de facturación es obligatoria';
        }
        
        if (!isset($data['total_amount']) || $data['total_amount'] <= 0) {
            $errors['total_amount'] = 'Total debe ser mayor a 0';
        }
        
        return $errors;
    }
    
    public function delete($id) {
        // Solo permitir eliminar pedidos en estado draft o cancelled
        $order = $this->findById($id);
        if (!$order || !in_array($order['status'], ['draft', 'cancelled'])) {
            return false;
        }
        
        // Eliminar items del pedido
        $this->db->query("DELETE FROM order_items WHERE order_id = ?", [$id]);
        
        // Eliminar el pedido
        return $this->db->query("DELETE FROM orders WHERE id = ?", [$id]);
    }
}
