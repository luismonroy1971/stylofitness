<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\Order;
use StyleFitness\Models\User;
use StyleFitness\Models\Product;
use StyleFitness\Helpers\AppHelper;
use Exception;

/**
 * Controlador de Checkout - STYLOFITNESS
 * Gestión del proceso de compra y pago
 */

class CheckoutController
{
    private $db;
    private $orderModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::setFlashMessage('error', 'Debes iniciar sesión para continuar');
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();

        // Obtener items del carrito
        $cartItems = $this->getCartItems($user['id']);

        if (empty($cartItems)) {
            AppHelper::setFlashMessage('error', 'Tu carrito está vacío');
            AppHelper::redirect('/store');
            return;
        }

        // Verificar stock de todos los productos
        $stockErrors = $this->validateStock($cartItems);
        if (!empty($stockErrors)) {
            $_SESSION['stock_errors'] = $stockErrors;
            AppHelper::setFlashMessage('error', 'Algunos productos no tienen stock suficiente');
            AppHelper::redirect('/cart');
            return;
        }

        // Calcular totales
        $totals = $this->calculateTotals($cartItems);

        // Obtener cupón aplicado si existe
        $appliedCoupon = $_SESSION['applied_coupon'] ?? null;
        if ($appliedCoupon) {
            $totals['discount'] = $appliedCoupon['discount'];
            $totals['total'] = $totals['total'] - $appliedCoupon['discount'];
        }

        // Direcciones guardadas del usuario
        $savedAddresses = $this->getUserAddresses($user['id']);

        // Métodos de pago disponibles
        $paymentMethods = $this->getAvailablePaymentMethods();

        // Métodos de envío
        $shippingMethods = $this->getShippingMethods($totals['subtotal']);

        $pageTitle = 'Checkout - STYLOFITNESS';
        $additionalCSS = ['checkout.css'];
        $additionalJS = ['checkout.js'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/store/checkout.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function process(): void
    {
        if (!AppHelper::isLoggedIn()) {
            $this->jsonResponse(['error' => 'Debes iniciar sesión'], 401);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Método no permitido'], 405);
            return;
        }

        $user = AppHelper::getCurrentUser();
        $cartItems = $this->getCartItems($user['id']);

        if (empty($cartItems)) {
            $this->jsonResponse(['error' => 'Tu carrito está vacío'], 400);
            return;
        }

        // Validar datos del formulario
        $checkoutData = $this->validateCheckoutData($_POST);
        if (isset($checkoutData['errors'])) {
            $this->jsonResponse(['error' => 'Datos inválidos', 'validation_errors' => $checkoutData['errors']], 400);
            return;
        }

        // Verificar stock una vez más
        $stockErrors = $this->validateStock($cartItems);
        if (!empty($stockErrors)) {
            $this->jsonResponse(['error' => 'Stock insuficiente', 'stock_errors' => $stockErrors], 400);
            return;
        }

        // Calcular totales finales
        $totals = $this->calculateTotals($cartItems);

        // Aplicar cupón si existe
        $appliedCoupon = $_SESSION['applied_coupon'] ?? null;
        if ($appliedCoupon) {
            $totals['discount'] = $appliedCoupon['discount'];
            $totals['total'] = $totals['total'] - $appliedCoupon['discount'];
        }

        try {
            // Iniciar transacción
            $this->db->getConnection()->beginTransaction();

            // Crear orden
            $orderData = [
                'user_id' => $user['id'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $checkoutData['payment_method'],
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax'],
                'shipping_amount' => $totals['shipping'],
                'discount_amount' => $totals['discount'] ?? 0,
                'total_amount' => $totals['total'],
                'currency' => 'PEN',
                'billing_address' => $checkoutData['billing_address'],
                'shipping_address' => $checkoutData['shipping_address'],
                'shipping_method' => $checkoutData['shipping_method'],
                'notes' => $checkoutData['notes'] ?? null,
            ];

            $orderId = $this->orderModel->create($orderData);

            if (!$orderId) {
                throw new Exception('Error al crear la orden');
            }

            // Añadir items a la orden
            foreach ($cartItems as $item) {
                $itemData = [
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'] ?? 'N/A',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                    'product_data' => [
                        'images' => $item['images'],
                        'slug' => $item['slug'],
                    ],
                ];

                $this->orderModel->addItem($orderId, $itemData);
            }

            // Registrar uso del cupón si aplica
            if ($appliedCoupon) {
                $this->recordCouponUsage($appliedCoupon['id'], $user['id'], $orderId, $appliedCoupon['discount']);
            }

            // Procesar pago según el método seleccionado
            $paymentResult = $this->processPayment($orderId, $checkoutData, $totals);

            if ($paymentResult['success']) {
                // Limpiar carrito
                $this->clearCart($user['id']);

                // Limpiar cupón aplicado
                unset($_SESSION['applied_coupon']);

                // Confirmar transacción
                $this->db->getConnection()->commit();

                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Orden creada exitosamente',
                    'order_id' => $orderId,
                    'payment_data' => $paymentResult['data'] ?? null,
                ]);
            } else {
                throw new Exception($paymentResult['error'] ?? 'Error en el procesamiento del pago');
            }

        } catch (Exception $e) {
            // Revertir transacción
            $this->db->getConnection()->rollBack();

            error_log('Checkout error: ' . $e->getMessage());

            $this->jsonResponse([
                'error' => 'Error al procesar la orden: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function success(string $orderNumber): void
    {
        if (!AppHelper::isLoggedIn()) {
            AppHelper::redirect('/login');
            return;
        }

        $user = AppHelper::getCurrentUser();
        $order = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order || $order['user_id'] != $user['id']) {
            AppHelper::setFlashMessage('error', 'Orden no encontrada');
            AppHelper::redirect('/store');
            return;
        }

        $pageTitle = 'Compra Exitosa - STYLOFITNESS';
        $additionalCSS = ['checkout.css'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/store/checkout-success.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    public function cancel(): void
    {
        $pageTitle = 'Compra Cancelada - STYLOFITNESS';
        $additionalCSS = ['checkout.css'];

        include APP_PATH . '/Views/layout/header.php';
        include APP_PATH . '/Views/store/checkout-cancel.php';
        include APP_PATH . '/Views/layout/footer.php';
    }

    private function getCartItems(int $userId): array
    {
        return $this->db->fetchAll(
            'SELECT ci.*, p.name, p.slug, p.images, p.stock_quantity, p.sale_price, p.sku
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.user_id = ? AND p.is_active = 1
             ORDER BY ci.created_at DESC',
            [$userId]
        );
    }

    private function validateStock(array $cartItems): array
    {
        $errors = [];

        foreach ($cartItems as $item) {
            if ($item['quantity'] > $item['stock_quantity']) {
                $errors[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'requested' => $item['quantity'],
                    'available' => $item['stock_quantity'],
                ];
            }
        }

        return $errors;
    }

    private function calculateTotals(array $items): array
    {
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
        ];
    }

    private function validateCheckoutData(array $data): array
    {
        $errors = [];

        // Validar dirección de facturación
        $billingRequired = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'postal_code'];
        foreach ($billingRequired as $field) {
            if (empty($data["billing_{$field}"])) {
                $errors["billing_{$field}"] = "El campo {$field} es obligatorio";
            }
        }

        // Validar email
        if (!empty($data['billing_email']) && !filter_var($data['billing_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['billing_email'] = 'Email inválido';
        }

        // Validar método de pago
        $allowedPaymentMethods = ['credit_card', 'paypal', 'bank_transfer', 'cash_on_delivery'];
        if (empty($data['payment_method']) || !in_array($data['payment_method'], $allowedPaymentMethods)) {
            $errors['payment_method'] = 'Método de pago inválido';
        }

        // Validar datos de tarjeta si es pago con tarjeta
        if ($data['payment_method'] === 'credit_card') {
            if (empty($data['card_number'])) {
                $errors['card_number'] = 'Número de tarjeta requerido';
            } elseif (!preg_match('/^\d{13,19}$/', str_replace(' ', '', $data['card_number']))) {
                $errors['card_number'] = 'Número de tarjeta inválido';
            }

            if (empty($data['card_expiry'])) {
                $errors['card_expiry'] = 'Fecha de vencimiento requerida';
            } elseif (!preg_match('/^\d{2}\/\d{2}$/', $data['card_expiry'])) {
                $errors['card_expiry'] = 'Formato de fecha inválido (MM/YY)';
            }

            if (empty($data['card_cvv'])) {
                $errors['card_cvv'] = 'CVV requerido';
            } elseif (!preg_match('/^\d{3,4}$/', $data['card_cvv'])) {
                $errors['card_cvv'] = 'CVV inválido';
            }

            if (empty($data['card_name'])) {
                $errors['card_name'] = 'Nombre en la tarjeta requerido';
            }
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // Preparar datos limpios
        return [
            'payment_method' => $data['payment_method'],
            'shipping_method' => $data['shipping_method'] ?? 'standard',
            'billing_address' => [
                'first_name' => $data['billing_first_name'],
                'last_name' => $data['billing_last_name'],
                'email' => $data['billing_email'],
                'phone' => $data['billing_phone'],
                'address' => $data['billing_address'],
                'address_2' => $data['billing_address_2'] ?? '',
                'city' => $data['billing_city'],
                'state' => $data['billing_state'] ?? '',
                'postal_code' => $data['billing_postal_code'],
                'country' => $data['billing_country'] ?? 'PE',
            ],
            'shipping_address' => [
                'first_name' => $data['shipping_first_name'] ?? $data['billing_first_name'],
                'last_name' => $data['shipping_last_name'] ?? $data['billing_last_name'],
                'phone' => $data['shipping_phone'] ?? $data['billing_phone'],
                'address' => $data['shipping_address'] ?? $data['billing_address'],
                'address_2' => $data['shipping_address_2'] ?? $data['billing_address_2'] ?? '',
                'city' => $data['shipping_city'] ?? $data['billing_city'],
                'state' => $data['shipping_state'] ?? $data['billing_state'] ?? '',
                'postal_code' => $data['shipping_postal_code'] ?? $data['billing_postal_code'],
                'country' => $data['shipping_country'] ?? $data['billing_country'] ?? 'PE',
            ],
            'payment_data' => [
                'card_number' => $data['card_number'] ?? null,
                'card_expiry' => $data['card_expiry'] ?? null,
                'card_cvv' => $data['card_cvv'] ?? null,
                'card_name' => $data['card_name'] ?? null,
            ],
            'notes' => $data['notes'] ?? null,
        ];
    }

    private function processPayment(int $orderId, array $checkoutData, array $totals): array
    {
        switch ($checkoutData['payment_method']) {
            case 'credit_card':
                return $this->processCreditCardPayment($orderId, $checkoutData, $totals);

            case 'paypal':
                return $this->processPayPalPayment($orderId, $checkoutData, $totals);

            case 'bank_transfer':
                return $this->processBankTransferPayment($orderId, $checkoutData, $totals);

            case 'cash_on_delivery':
                return $this->processCashOnDeliveryPayment($orderId, $checkoutData, $totals);

            default:
                return ['success' => false, 'error' => 'Método de pago no soportado'];
        }
    }

    private function processCreditCardPayment(int $orderId, array $checkoutData, array $totals): array
    {
        // Integración con Stripe u otro procesador de pagos
        // Por ahora, simulamos el pago exitoso

        try {
            // Aquí integrarías con Stripe API
            // $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
            // $paymentIntent = $stripe->paymentIntents->create([...]);

            // Simulación para desarrollo
            $paymentReference = 'sim_' . uniqid();

            // Actualizar orden como pagada
            $this->orderModel->update($orderId, [
                'payment_status' => 'paid',
                'payment_reference' => $paymentReference,
                'status' => 'processing',
            ]);

            return [
                'success' => true,
                'data' => [
                    'payment_reference' => $paymentReference,
                    'redirect_url' => '/checkout/success/' . $this->getOrderNumber($orderId),
                ],
            ];

        } catch (Exception $e) {
            error_log('Error en pago con tarjeta: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error al procesar el pago con tarjeta: ' . $e->getMessage()];
        }
    }

    private function processPayPalPayment(int $orderId, array $checkoutData, array $totals): array
    {
        // Integración con PayPal
        try {
            // Aquí integrarías con PayPal API

            return [
                'success' => true,
                'data' => [
                    'redirect_url' => 'https://www.paypal.com/checkoutnow?order_id=' . $orderId,
                ],
            ];

        } catch (Exception $e) {
            error_log('Error en pago con PayPal: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error al procesar el pago con PayPal: ' . $e->getMessage()];
        }
    }

    private function processBankTransferPayment(int $orderId, array $checkoutData, array $totals): array
    {
        // Actualizar orden como pendiente de pago
        $this->orderModel->update($orderId, [
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        return [
            'success' => true,
            'data' => [
                'redirect_url' => '/checkout/success/' . $this->getOrderNumber($orderId),
            ],
        ];
    }

    private function processCashOnDeliveryPayment(int $orderId, array $checkoutData, array $totals): array
    {
        // Actualizar orden como pendiente de pago
        $this->orderModel->update($orderId, [
            'payment_status' => 'pending',
            'status' => 'confirmed',
        ]);

        return [
            'success' => true,
            'data' => [
                'redirect_url' => '/checkout/success/' . $this->getOrderNumber($orderId),
            ],
        ];
    }

    private function getUserAddresses(int $userId): array
    {
        // Obtener direcciones guardadas del usuario
        return $this->db->fetchAll(
            'SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC',
            [$userId]
        );
    }

    private function getAvailablePaymentMethods(): array
    {
        return [
            'credit_card' => [
                'name' => 'Tarjeta de Crédito/Débito',
                'description' => 'Visa, MasterCard, American Express',
                'icon' => 'fas fa-credit-card',
                'enabled' => true,
            ],
            'paypal' => [
                'name' => 'PayPal',
                'description' => 'Paga con tu cuenta PayPal',
                'icon' => 'fab fa-paypal',
                'enabled' => true,
            ],
            'bank_transfer' => [
                'name' => 'Transferencia Bancaria',
                'description' => 'Depósito o transferencia bancaria',
                'icon' => 'fas fa-university',
                'enabled' => true,
            ],
            'cash_on_delivery' => [
                'name' => 'Pago Contra Entrega',
                'description' => 'Paga cuando recibas tu pedido',
                'icon' => 'fas fa-money-bill-wave',
                'enabled' => true,
            ],
        ];
    }

    private function getShippingMethods(float $subtotal): array
    {
        $methods = [
            'standard' => [
                'name' => 'Envío Estándar',
                'description' => '3-5 días hábiles',
                'price' => $subtotal >= 150 ? 0 : 15,
                'estimated_days' => '3-5',
            ],
            'express' => [
                'name' => 'Envío Express',
                'description' => '1-2 días hábiles',
                'price' => 25,
                'estimated_days' => '1-2',
            ],
        ];

        return $methods;
    }

    private function recordCouponUsage(int $couponId, int $userId, int $orderId, float $discountAmount): void
    {
        $this->db->insert(
            'INSERT INTO coupon_usage (coupon_id, user_id, order_id, discount_amount, used_at) VALUES (?, ?, ?, ?, NOW())',
            [$couponId, $userId, $orderId, $discountAmount]
        );

        // Actualizar contador de uso del cupón
        $this->db->query(
            'UPDATE coupons SET used_count = used_count + 1 WHERE id = ?',
            [$couponId]
        );
    }

    private function clearCart(int $userId): bool
    {
        return $this->db->query('DELETE FROM cart_items WHERE user_id = ?', [$userId]);
    }

    private function getOrderNumber(int $orderId): ?string
    {
        $order = $this->orderModel->findById($orderId);
        return $order ? $order['order_number'] : null;
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
