<!-- Vista de Checkout - STYLOFITNESS -->
<div class="checkout-page">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-credit-card"></i> Checkout</h1>
            <div class="checkout-steps">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span class="step-label">Información</span>
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    <span class="step-label">Pago</span>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <span class="step-label">Confirmación</span>
                </div>
            </div>
        </div>

        <form id="checkout-form" class="checkout-form">
            <div class="checkout-content">
                <div class="checkout-main">
                    <!-- Información de contacto -->
                    <div class="checkout-section">
                        <h2><i class="fas fa-user"></i> Información de contacto</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="billing_first_name">Nombre *</label>
                                <input type="text" 
                                       id="billing_first_name" 
                                       name="billing_first_name" 
                                       value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>"
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="billing_last_name">Apellido *</label>
                                <input type="text" 
                                       id="billing_last_name" 
                                       name="billing_last_name" 
                                       value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="billing_email">Email *</label>
                                <input type="email" 
                                       id="billing_email" 
                                       name="billing_email" 
                                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="billing_phone">Teléfono *</label>
                                <input type="tel" 
                                       id="billing_phone" 
                                       name="billing_phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección de facturación -->
                    <div class="checkout-section">
                        <h2><i class="fas fa-map-marker-alt"></i> Dirección de facturación</h2>
                        
                        <div class="form-group">
                            <label for="billing_address">Dirección *</label>
                            <input type="text" 
                                   id="billing_address" 
                                   name="billing_address" 
                                   placeholder="Ej: Av. Larco 123"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="billing_address_2">Referencia (opcional)</label>
                            <input type="text" 
                                   id="billing_address_2" 
                                   name="billing_address_2" 
                                   placeholder="Ej: Dpto 302, cerca al banco">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="billing_city">Ciudad *</label>
                                <input type="text" 
                                       id="billing_city" 
                                       name="billing_city" 
                                       value="Lima"
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="billing_postal_code">Código postal *</label>
                                <input type="text" 
                                       id="billing_postal_code" 
                                       name="billing_postal_code" 
                                       placeholder="15001"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección de envío -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <h2><i class="fas fa-shipping-fast"></i> Dirección de envío</h2>
                            <label class="checkbox-container">
                                <input type="checkbox" id="same_as_billing" checked>
                                <span class="checkmark"></span>
                                Igual que la dirección de facturación
                            </label>
                        </div>

                        <div id="shipping-address-fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="shipping_first_name">Nombre</label>
                                    <input type="text" id="shipping_first_name" name="shipping_first_name">
                                </div>
                                <div class="form-group">
                                    <label for="shipping_last_name">Apellido</label>
                                    <input type="text" id="shipping_last_name" name="shipping_last_name">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="shipping_phone">Teléfono</label>
                                <input type="tel" id="shipping_phone" name="shipping_phone">
                            </div>

                            <div class="form-group">
                                <label for="shipping_address">Dirección</label>
                                <input type="text" id="shipping_address" name="shipping_address">
                            </div>

                            <div class="form-group">
                                <label for="shipping_address_2">Referencia</label>
                                <input type="text" id="shipping_address_2" name="shipping_address_2">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="shipping_city">Ciudad</label>
                                    <input type="text" id="shipping_city" name="shipping_city">
                                </div>
                                <div class="form-group">
                                    <label for="shipping_postal_code">Código postal</label>
                                    <input type="text" id="shipping_postal_code" name="shipping_postal_code">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Método de envío -->
                    <div class="checkout-section">
                        <h2><i class="fas fa-truck"></i> Método de envío</h2>
                        
                        <div class="shipping-methods">
                            <?php foreach ($shippingMethods as $key => $method): ?>
                                <label class="shipping-method">
                                    <input type="radio" 
                                           name="shipping_method" 
                                           value="<?php echo $key; ?>"
                                           data-price="<?php echo $method['price']; ?>"
                                           <?php echo $key === 'standard' ? 'checked' : ''; ?>>
                                    <div class="method-info">
                                        <div class="method-name"><?php echo $method['name']; ?></div>
                                        <div class="method-description"><?php echo $method['description']; ?></div>
                                    </div>
                                    <div class="method-price">
                                        <?php echo $method['price'] > 0 ? 'S/ ' . number_format($method['price'], 2) : 'Gratis'; ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Método de pago -->
                    <div class="checkout-section">
                        <h2><i class="fas fa-credit-card"></i> Método de pago</h2>
                        
                        <div class="payment-methods">
                            <?php foreach ($paymentMethods as $key => $method): ?>
                                <?php if ($method['enabled']): ?>
                                    <label class="payment-method">
                                        <input type="radio" 
                                               name="payment_method" 
                                               value="<?php echo $key; ?>"
                                               <?php echo $key === 'credit_card' ? 'checked' : ''; ?>>
                                        <div class="method-content">
                                            <div class="method-header">
                                                <i class="<?php echo $method['icon']; ?>"></i>
                                                <span class="method-name"><?php echo $method['name']; ?></span>
                                            </div>
                                            <div class="method-description"><?php echo $method['description']; ?></div>
                                        </div>
                                    </label>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Formulario de tarjeta de crédito -->
                        <div id="credit-card-form" class="payment-form">
                            <div class="card-form">
                                <div class="form-group">
                                    <label for="card_number">Número de tarjeta *</label>
                                    <input type="text" 
                                           id="card_number" 
                                           name="card_number" 
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="19">
                                    <div class="card-icons">
                                        <i class="fab fa-cc-visa"></i>
                                        <i class="fab fa-cc-mastercard"></i>
                                        <i class="fab fa-cc-amex"></i>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="card_expiry">Vencimiento *</label>
                                        <input type="text" 
                                               id="card_expiry" 
                                               name="card_expiry" 
                                               placeholder="MM/YY"
                                               maxlength="5">
                                    </div>
                                    <div class="form-group">
                                        <label for="card_cvv">CVV *</label>
                                        <input type="text" 
                                               id="card_cvv" 
                                               name="card_cvv" 
                                               placeholder="123"
                                               maxlength="4">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="card_name">Nombre en la tarjeta *</label>
                                    <input type="text" 
                                           id="card_name" 
                                           name="card_name" 
                                           placeholder="Nombre completo">
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional para otros métodos -->
                        <div id="bank-transfer-info" class="payment-form" style="display: none;">
                            <div class="info-box">
                                <h4>Instrucciones para transferencia bancaria</h4>
                                <p>Después de confirmar tu pedido, recibirás las instrucciones para realizar la transferencia bancaria a tu email.</p>
                                <ul>
                                    <li>Tu pedido se procesará después de confirmar el pago</li>
                                    <li>Tienes 3 días para realizar la transferencia</li>
                                    <li>Envía el comprobante a pagos@stylofitness.com</li>
                                </ul>
                            </div>
                        </div>

                        <div id="cash-on-delivery-info" class="payment-form" style="display: none;">
                            <div class="info-box">
                                <h4>Pago contra entrega</h4>
                                <p>Paga cuando recibas tu pedido en la puerta de tu casa.</p>
                                <ul>
                                    <li>Solo efectivo</li>
                                    <li>Disponible en Lima Metropolitana</li>
                                    <li>Cargo adicional de S/ 5.00 por el servicio</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Notas adicionales -->
                    <div class="checkout-section">
                        <h2><i class="fas fa-sticky-note"></i> Notas del pedido (opcional)</h2>
                        <div class="form-group">
                            <textarea name="notes" 
                                      id="order_notes" 
                                      rows="3" 
                                      placeholder="Instrucciones especiales para la entrega, comentarios, etc."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Resumen del pedido -->
                <div class="checkout-sidebar">
                    <div class="order-summary">
                        <h3>Resumen del pedido</h3>

                        <div class="order-items">
                            <?php foreach ($cartItems as $item): ?>
                                <?php 
                                $images = is_string($item['images']) ? json_decode($item['images'], true) : $item['images'];
                                $mainImage = !empty($images) ? $images[0] : '/images/default-product.jpg';
                                $currentPrice = $item['sale_price'] ?? $item['price'];
                                ?>
                                <div class="order-item">
                                    <div class="item-image">
                                        <img src="<?php echo AppHelper::uploadUrl($mainImage); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        <span class="item-quantity"><?php echo $item['quantity']; ?></span>
                                    </div>
                                    <div class="item-details">
                                        <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <?php if ($item['variation_name']): ?>
                                            <div class="item-variation"><?php echo htmlspecialchars($item['variation_name']); ?></div>
                                        <?php endif; ?>
                                        <div class="item-price">S/ <?php echo number_format($currentPrice * $item['quantity'], 2); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Cupón aplicado -->
                        <?php if (isset($_SESSION['applied_coupon'])): ?>
                            <div class="applied-coupon-summary">
                                <div class="coupon-info">
                                    <span class="coupon-code"><?php echo $_SESSION['applied_coupon']['code']; ?></span>
                                    <span class="coupon-discount">-S/ <?php echo number_format($_SESSION['applied_coupon']['discount'], 2); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="order-totals">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span class="checkout-subtotal">S/ <?php echo number_format($totals['subtotal'], 2); ?></span>
                            </div>

                            <div class="total-row">
                                <span>Envío:</span>
                                <span class="checkout-shipping">
                                    <?php echo $totals['shipping'] > 0 ? 'S/ ' . number_format($totals['shipping'], 2) : 'Gratis'; ?>
                                </span>
                            </div>

                            <div class="total-row">
                                <span>IGV (18%):</span>
                                <span class="checkout-tax">S/ <?php echo number_format($totals['tax'], 2); ?></span>
                            </div>

                            <?php if (isset($totals['discount']) && $totals['discount'] > 0): ?>
                                <div class="total-row discount-row">
                                    <span>Descuento:</span>
                                    <span class="checkout-discount">-S/ <?php echo number_format($totals['discount'], 2); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="total-row total-final">
                                <span>Total:</span>
                                <span class="checkout-total">S/ <?php echo number_format($totals['total'], 2); ?></span>
                            </div>
                        </div>

                        <div class="checkout-actions">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" id="place-order-btn">
                                <i class="fas fa-lock"></i>
                                Realizar pedido
                            </button>
                            
                            <a href="<?php echo AppHelper::baseUrl('cart'); ?>" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-arrow-left"></i>
                                Volver al carrito
                            </a>
                        </div>

                        <div class="security-badges">
                            <div class="security-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Compra 100% segura</span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-lock"></i>
                                <span>Datos protegidos SSL</span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-undo"></i>
                                <span>Devolución gratuita</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de procesamiento -->
<div id="processing-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-body text-center">
            <div class="processing-spinner">
                <i class="fas fa-spinner fa-spin fa-3x"></i>
            </div>
            <h3>Procesando tu pedido...</h3>
            <p>Por favor espera mientras procesamos tu pago y confirmamos tu pedido.</p>
            <p><strong>No cierres esta ventana</strong></p>
        </div>
    </div>
</div>
