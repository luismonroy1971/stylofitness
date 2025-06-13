<!-- Vista de Carrito de Compras - STYLOFITNESS -->
<div class="cart-page">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-shopping-cart"></i> Mi Carrito</h1>
            <nav class="breadcrumb">
                <a href="<?php echo AppHelper::baseUrl(); ?>">Inicio</a>
                <span>Carrito</span>
            </nav>
        </div>

        <?php if (!empty($cartItems)): ?>
            <div class="cart-content">
                <div class="cart-items-section">
                    <div class="cart-header">
                        <h2>Productos en tu carrito (<?php echo count($cartItems); ?>)</h2>
                        <button class="btn-clear-cart btn-outline-danger">
                            <i class="fas fa-trash"></i> Vaciar carrito
                        </button>
                    </div>

                    <div class="cart-items-container">
                        <?php foreach ($cartItems as $item): ?>
                            <?php 
                            $images = is_string($item['images']) ? json_decode($item['images'], true) : $item['images'];
                            $mainImage = !empty($images) ? $images[0] : '/images/default-product.jpg';
                            $currentPrice = $item['sale_price'] ?? $item['price'];
                            $originalPrice = $item['price'];
                            $hasDiscount = $item['sale_price'] && $item['sale_price'] < $item['price'];
                            ?>
                            <div class="cart-item" data-item-id="<?php echo $item['id']; ?>">
                                <div class="item-image">
                                    <img src="<?php echo AppHelper::uploadUrl($mainImage); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>

                                <div class="item-details">
                                    <h3 class="item-name">
                                        <a href="<?php echo AppHelper::baseUrl('store/product/' . $item['slug']); ?>">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                    </h3>
                                    
                                    <?php if ($item['variation_name']): ?>
                                        <p class="item-variation">
                                            Variación: <?php echo htmlspecialchars($item['variation_name']); ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="item-price">
                                        <?php if ($hasDiscount): ?>
                                            <span class="original-price">S/ <?php echo number_format($originalPrice, 2); ?></span>
                                            <span class="current-price">S/ <?php echo number_format($currentPrice, 2); ?></span>
                                            <span class="discount-badge">
                                                -<?php echo round((($originalPrice - $currentPrice) / $originalPrice) * 100); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="current-price">S/ <?php echo number_format($currentPrice, 2); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="stock-info">
                                        <?php if ($item['stock_quantity'] > 10): ?>
                                            <span class="stock-status in-stock">✓ En stock</span>
                                        <?php elseif ($item['stock_quantity'] > 0): ?>
                                            <span class="stock-status low-stock">⚠ Últimas <?php echo $item['stock_quantity']; ?> unidades</span>
                                        <?php else: ?>
                                            <span class="stock-status out-of-stock">✗ Sin stock</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="item-quantity">
                                    <label>Cantidad:</label>
                                    <div class="quantity-controls">
                                        <button class="quantity-btn minus" data-action="decrease">-</button>
                                        <input type="number" 
                                               class="cart-quantity-input" 
                                               data-item-id="<?php echo $item['id']; ?>"
                                               data-original-value="<?php echo $item['quantity']; ?>"
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="0" 
                                               max="<?php echo $item['stock_quantity']; ?>">
                                        <button class="quantity-btn plus" data-action="increase">+</button>
                                    </div>
                                </div>

                                <div class="item-total">
                                    <div class="total-price">
                                        S/ <?php echo number_format($currentPrice * $item['quantity'], 2); ?>
                                    </div>
                                    <div class="unit-price">
                                        S/ <?php echo number_format($currentPrice, 2); ?> c/u
                                    </div>
                                </div>

                                <div class="item-actions">
                                    <button class="btn-remove-cart btn-icon" 
                                            data-item-id="<?php echo $item['id']; ?>"
                                            title="Eliminar del carrito">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn-wishlist btn-icon" 
                                            data-product-id="<?php echo $item['product_id']; ?>"
                                            title="Mover a lista de deseos">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="cart-summary-section">
                    <div class="cart-summary sticky-summary">
                        <h3>Resumen del pedido</h3>

                        <!-- Cupón de descuento -->
                        <div class="coupon-section">
                            <form id="coupon-form">
                                <div class="input-group">
                                    <input type="text" 
                                           id="coupon-code" 
                                           placeholder="Código de cupón"
                                           class="form-control">
                                    <button type="submit" 
                                            id="apply-coupon-btn" 
                                            class="btn btn-outline-primary">
                                        Aplicar
                                    </button>
                                </div>
                            </form>
                            
                            <div class="applied-coupon" style="display: none;">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>

                        <!-- Totales -->
                        <div class="order-totals">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span class="cart-subtotal">S/ <?php echo number_format($cartTotals['subtotal'], 2); ?></span>
                            </div>

                            <div class="total-row">
                                <span>Envío:</span>
                                <span class="cart-shipping">
                                    <?php echo $cartTotals['shipping'] > 0 ? 'S/ ' . number_format($cartTotals['shipping'], 2) : 'Gratis'; ?>
                                </span>
                            </div>

                            <div class="total-row">
                                <span>IGV (18%):</span>
                                <span class="cart-tax">S/ <?php echo number_format($cartTotals['tax'], 2); ?></span>
                            </div>

                            <div class="total-row discount-row" style="display: none;">
                                <span>Descuento:</span>
                                <span class="cart-discount">-S/ 0.00</span>
                            </div>

                            <div class="total-row total-final">
                                <span>Total:</span>
                                <span class="cart-total">S/ <?php echo number_format($cartTotals['total'], 2); ?></span>
                            </div>
                        </div>

                        <!-- Mensaje de envío gratis -->
                        <?php if ($cartTotals['free_shipping_remaining'] > 0): ?>
                            <div class="free-shipping-message">
                                <i class="fas fa-truck"></i>
                                Añade S/ <?php echo number_format($cartTotals['free_shipping_remaining'], 2); ?> más para obtener <strong>envío gratis</strong>
                            </div>
                        <?php else: ?>
                            <div class="free-shipping-achieved">
                                <i class="fas fa-check-circle"></i>
                                ¡Felicidades! Tienes <strong>envío gratis</strong>
                            </div>
                        <?php endif; ?>

                        <!-- Botones de acción -->
                        <div class="cart-actions">
                            <a href="<?php echo AppHelper::baseUrl('checkout'); ?>" 
                               class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-credit-card"></i>
                                Proceder al checkout
                            </a>
                            
                            <a href="<?php echo AppHelper::baseUrl('store'); ?>" 
                               class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-arrow-left"></i>
                                Continuar comprando
                            </a>
                        </div>

                        <!-- Garantías y servicios -->
                        <div class="cart-guarantees">
                            <div class="guarantee-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Compra 100% segura</span>
                            </div>
                            <div class="guarantee-item">
                                <i class="fas fa-undo"></i>
                                <span>Devolución gratuita</span>
                            </div>
                            <div class="guarantee-item">
                                <i class="fas fa-truck"></i>
                                <span>Envío rápido</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos recomendados -->
            <?php if (!empty($recommendedProducts)): ?>
                <div class="recommended-section">
                    <h3>Productos recomendados para ti</h3>
                    <div class="products-grid">
                        <?php foreach ($recommendedProducts as $product): ?>
                            <?php 
                            $images = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
                            $mainImage = !empty($images) ? $images[0] : '/images/default-product.jpg';
                            ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="<?php echo AppHelper::uploadUrl($mainImage); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         loading="lazy">
                                    
                                    <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                        <?php $discount = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
                                        <div class="product-badge">-<?php echo $discount; ?>%</div>
                                    <?php endif; ?>
                                </div>

                                <div class="product-info">
                                    <h4 class="product-title">
                                        <a href="<?php echo AppHelper::baseUrl('store/product/' . $product['slug']); ?>">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </a>
                                    </h4>

                                    <div class="product-price">
                                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                            <span class="original-price">S/ <?php echo number_format($product['price'], 2); ?></span>
                                            <span class="current-price">S/ <?php echo number_format($product['sale_price'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="current-price">S/ <?php echo number_format($product['price'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="product-actions">
                                        <button class="btn-add-cart btn btn-primary btn-sm" 
                                                data-product-id="<?php echo $product['id']; ?>">
                                            <i class="fas fa-plus"></i>
                                            Añadir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Carrito vacío -->
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2>Tu carrito está vacío</h2>
                <p>Parece que aún no has añadido productos a tu carrito.</p>
                <p>Descubre nuestros increíbles productos y comienza a armar tu pedido.</p>
                
                <div class="empty-cart-actions">
                    <a href="<?php echo AppHelper::baseUrl('store'); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-store"></i>
                        Explorar productos
                    </a>
                    
                    <?php if (AppHelper::isLoggedIn()): ?>
                        <a href="<?php echo AppHelper::baseUrl('store/wishlist'); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-heart"></i>
                            Ver mi lista de deseos
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Categorías populares -->
                <div class="popular-categories">
                    <h3>Categorías populares</h3>
                    <div class="categories-grid">
                        <a href="<?php echo AppHelper::baseUrl('store/category/proteinas'); ?>" class="category-item">
                            <i class="fas fa-dumbbell"></i>
                            <span>Proteínas</span>
                        </a>
                        <a href="<?php echo AppHelper::baseUrl('store/category/pre-entrenos'); ?>" class="category-item">
                            <i class="fas fa-bolt"></i>
                            <span>Pre-entrenos</span>
                        </a>
                        <a href="<?php echo AppHelper::baseUrl('store/category/vitaminas'); ?>" class="category-item">
                            <i class="fas fa-pills"></i>
                            <span>Vitaminas</span>
                        </a>
                        <a href="<?php echo AppHelper::baseUrl('store/category/accesorios'); ?>" class="category-item">
                            <i class="fas fa-tshirt"></i>
                            <span>Accesorios</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de confirmación -->
<div id="confirm-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar acción</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p id="confirm-message">¿Estás seguro?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="confirm-cancel">Cancelar</button>
            <button class="btn btn-danger" id="confirm-accept">Confirmar</button>
        </div>
    </div>
</div>
