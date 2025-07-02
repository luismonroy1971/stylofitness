<?php

namespace StyleFitness\Controllers;

use StyleFitness\Config\Database;
use StyleFitness\Models\Order;
use StyleFitness\Models\User;
use StyleFitness\Helpers\AppHelper;
use Exception;

/**
 * Controlador de Webhooks - STYLOFITNESS
 * Maneja webhooks de pagos y servicios externos
 */

class WebhookController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function stripePayment()
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $endpoint_secret = getWebhookConfig('stripe_webhook_secret', '');

        try {
            // Verificar la firma del webhook
            $event = $this->verifyStripeWebhook($payload, $sig_header, $endpoint_secret);

            switch ($event['type']) {
                case 'payment_intent.succeeded':
                    $this->handleStripePaymentSuccess($event['data']['object']);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handleStripePaymentFailed($event['data']['object']);
                    break;

                case 'customer.subscription.created':
                    $this->handleStripeSubscriptionCreated($event['data']['object']);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleStripeSubscriptionCanceled($event['data']['object']);
                    break;

                default:
                    error_log('Unhandled Stripe event type: ' . $event['type']);
            }

            http_response_code(200);
            echo json_encode(['status' => 'success']);

        } catch (Exception $e) {
            error_log('Stripe webhook error: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function paypalPayment()
    {
        $payload = @file_get_contents('php://input');
        $headers = getallheaders();

        try {
            // Verificar el webhook de PayPal
            if (!$this->verifyPayPalWebhook($payload, $headers)) {
                throw new Exception('Invalid PayPal webhook signature');
            }

            $data = json_decode($payload, true);

            switch ($data['event_type']) {
                case 'PAYMENT.CAPTURE.COMPLETED':
                    $this->handlePayPalPaymentSuccess($data);
                    break;

                case 'PAYMENT.CAPTURE.DENIED':
                    $this->handlePayPalPaymentFailed($data);
                    break;

                default:
                    error_log('Unhandled PayPal event type: ' . $data['event_type']);
            }

            http_response_code(200);
            echo json_encode(['status' => 'success']);

        } catch (Exception $e) {
            error_log('PayPal webhook error: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function mailgunWebhook()
    {
        $payload = $_POST;

        if (!$this->verifyMailgunWebhook($payload)) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $event = $payload['event-data'];

        switch ($event['event']) {
            case 'delivered':
                $this->handleEmailDelivered($event);
                break;

            case 'opened':
                $this->handleEmailOpened($event);
                break;

            case 'clicked':
                $this->handleEmailClicked($event);
                break;

            case 'bounced':
            case 'failed':
                $this->handleEmailFailed($event);
                break;

            default:
                error_log('Unhandled Mailgun event: ' . $event['event']);
        }

        http_response_code(200);
        echo json_encode(['status' => 'success']);
    }

    // Métodos privados para manejar eventos de Stripe

    private function verifyStripeWebhook($payload, $sig_header, $endpoint_secret)
    {
        if (empty($endpoint_secret)) {
            throw new Exception('Stripe webhook secret not configured');
        }

        $elements = explode(',', $sig_header);
        $signature = null;
        $timestamp = null;

        foreach ($elements as $element) {
            if (strpos($element, 't=') === 0) {
                $timestamp = substr($element, 2);
            } elseif (strpos($element, 'v1=') === 0) {
                $signature = substr($element, 3);
            }
        }

        if (!$timestamp || !$signature) {
            throw new Exception('Invalid signature header');
        }

        // Verificar el timestamp (no más de 5 minutos)
        if (abs(time() - $timestamp) > 300) {
            throw new Exception('Request timestamp too old');
        }

        // Verificar la firma
        $expected_signature = hash_hmac('sha256', $timestamp . '.' . $payload, $endpoint_secret);

        if (!hash_equals($expected_signature, $signature)) {
            throw new Exception('Invalid signature');
        }

        return json_decode($payload, true);
    }

    private function handleStripePaymentSuccess($payment_intent)
    {
        $order_id = $payment_intent['metadata']['order_id'] ?? null;

        if ($order_id) {
            $orderModel = new Order();
            $orderModel->update($order_id, [
                'payment_status' => 'paid',
                'payment_reference' => $payment_intent['id'],
                'status' => 'processing',
            ]);

            // Enviar email de confirmación
            $this->sendOrderConfirmationEmail($order_id);

            // Actualizar stock de productos
            $this->updateProductStock($order_id);

            error_log("Payment succeeded for order: {$order_id}");
        }
    }

    private function handleStripePaymentFailed($payment_intent)
    {
        $order_id = $payment_intent['metadata']['order_id'] ?? null;

        if ($order_id) {
            $orderModel = new Order();
            $orderModel->update($order_id, [
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);

            error_log("Payment failed for order: {$order_id}");
        }
    }

    private function handleStripeSubscriptionCreated($subscription)
    {
        $user_id = $subscription['metadata']['user_id'] ?? null;

        if ($user_id) {
            $userModel = new User();
            $userModel->update($user_id, [
                'membership_type' => 'premium',
                'membership_expires' => date('Y-m-d', $subscription['current_period_end']),
            ]);

            error_log("Subscription created for user: {$user_id}");
        }
    }

    private function handleStripeSubscriptionCanceled($subscription)
    {
        $user_id = $subscription['metadata']['user_id'] ?? null;

        if ($user_id) {
            $userModel = new User();
            $userModel->update($user_id, [
                'membership_type' => 'basic',
            ]);

            error_log("Subscription canceled for user: {$user_id}");
        }
    }

    // Métodos privados para manejar eventos de PayPal

    private function verifyPayPalWebhook($payload, $headers)
    {
        // Implementar verificación de webhook de PayPal
        // Por ahora, retornar true para desarrollo
        return true;
    }

    private function handlePayPalPaymentSuccess($data)
    {
        $order_id = $data['resource']['custom_id'] ?? null;

        if ($order_id) {
            $orderModel = new Order();
            $orderModel->update($order_id, [
                'payment_status' => 'paid',
                'payment_reference' => $data['resource']['id'],
                'status' => 'processing',
            ]);

            $this->sendOrderConfirmationEmail($order_id);
            $this->updateProductStock($order_id);

            error_log("PayPal payment succeeded for order: {$order_id}");
        }
    }

    private function handlePayPalPaymentFailed($data)
    {
        $order_id = $data['resource']['custom_id'] ?? null;

        if ($order_id) {
            $orderModel = new Order();
            $orderModel->update($order_id, [
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);

            error_log("PayPal payment failed for order: {$order_id}");
        }
    }

    // Métodos privados para manejar eventos de Mailgun

    private function verifyMailgunWebhook($payload)
    {
        $webhook_key = getWebhookConfig('mailgun_webhook_key', '');

        if (empty($webhook_key)) {
            return false;
        }

        $token = $payload['signature']['token'] ?? '';
        $timestamp = $payload['signature']['timestamp'] ?? '';
        $signature = $payload['signature']['signature'] ?? '';

        $expected_signature = hash_hmac('sha256', $timestamp . $token, $webhook_key);

        return hash_equals($expected_signature, $signature);
    }

    private function handleEmailDelivered($event)
    {
        $message_id = $event['message']['headers']['message-id'] ?? '';
        $recipient = $event['recipient'] ?? '';

        // Registrar entrega del email
        $this->db->query(
            "INSERT INTO email_logs (message_id, recipient, event_type, created_at) VALUES (?, ?, 'delivered', NOW())",
            [$message_id, $recipient]
        );
    }

    private function handleEmailOpened($event)
    {
        $message_id = $event['message']['headers']['message-id'] ?? '';
        $recipient = $event['recipient'] ?? '';

        $this->db->query(
            "INSERT INTO email_logs (message_id, recipient, event_type, created_at) VALUES (?, ?, 'opened', NOW())",
            [$message_id, $recipient]
        );
    }

    private function handleEmailClicked($event)
    {
        $message_id = $event['message']['headers']['message-id'] ?? '';
        $recipient = $event['recipient'] ?? '';
        $url = $event['url'] ?? '';

        $this->db->query(
            "INSERT INTO email_logs (message_id, recipient, event_type, event_data, created_at) VALUES (?, ?, 'clicked', ?, NOW())",
            [$message_id, $recipient, json_encode(['url' => $url])]
        );
    }

    private function handleEmailFailed($event)
    {
        $message_id = $event['message']['headers']['message-id'] ?? '';
        $recipient = $event['recipient'] ?? '';
        $reason = $event['reason'] ?? '';

        $this->db->query(
            "INSERT INTO email_logs (message_id, recipient, event_type, event_data, created_at) VALUES (?, ?, 'failed', ?, NOW())",
            [$message_id, $recipient, json_encode(['reason' => $reason])]
        );
    }

    // Métodos auxiliares

    private function sendOrderConfirmationEmail($order_id)
    {
        $orderModel = new Order();
        $order = $orderModel->findById($order_id);

        if ($order) {
            $userModel = new User();
            $user = $userModel->findById($order['user_id']);

            if ($user) {
                // Implementar envío de email de confirmación
                error_log('Should send order confirmation email to: ' . $user['email']);
            }
        }
    }

    private function updateProductStock($order_id)
    {
        $orderModel = new Order();
        $orderItems = $orderModel->getOrderItems($order_id);

        foreach ($orderItems as $item) {
            $this->db->query(
                'UPDATE products SET stock_quantity = stock_quantity - ?, sales_count = sales_count + ? WHERE id = ?',
                [$item['quantity'], $item['quantity'], $item['product_id']]
            );
        }
    }
}

// Función auxiliar para obtener configuración
function getWebhookConfig($key, $default = null)
{
    static $config = null;

    if ($config === null) {
        $config = [
            'stripe_webhook_secret' => $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '',
            'mailgun_webhook_key' => $_ENV['MAILGUN_WEBHOOK_KEY'] ?? '',
        ];
    }

    return $config[$key] ?? $default;
}
