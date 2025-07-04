<?php
// Verificar que tenemos los datos del producto
if (!isset($product) || empty($product)) {
    header('HTTP/1.0 404 Not Found');
    include APP_PATH . '/Views/errors/404.php';
    exit;
}

// Obtener imágenes del producto
$productImages = [];
if (!empty($product['images'])) {
    $images = json_decode($product['images'], true);
    if (is_array($images)) {
        $productImages = $images;
    }
}

// Si no hay imágenes, usar placeholder
if (empty($productImages)) {
    $productImages = ['/images/placeholder.jpg'];
}

// Calcular descuento si existe
$hasDiscount = !empty($product['sale_price']) && $product['sale_price'] < $product['price'];
$discountPercentage = 0;
if ($hasDiscount) {
    $discountPercentage = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
}

$finalPrice = $hasDiscount ? $product['sale_price'] : $product['price'];
?>

<div class="product-detail-container" data-product-id="<?= $product['id'] ?>">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="<?= AppHelper::baseUrl('/') ?>">Inicio</a></li>
                <li><a href="<?= AppHelper::baseUrl('/store') ?>">Tienda</a></li>
                <?php if (!empty($product['category_name'])): ?>
                <li><a href="<?= AppHelper::baseUrl('/store?category=' . urlencode($product['category_slug'])) ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
                <?php endif; ?>
                <li class="active"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </div>
    </nav>

    <div class="container">
        <div class="product-detail-grid">
            <!-- Galería de imágenes -->
            <div class="product-gallery">
                <div class="main-image-container">
                    <img id="mainProductImage" src="<?= AppHelper::baseUrl($productImages[0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="main-product-image">
                    <?php if ($hasDiscount): ?>
                    <div class="discount-badge">-<?= $discountPercentage ?>%</div>
                    <?php endif; ?>
                    <?php if ($product['is_featured']): ?>
                    <div class="featured-badge">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Destacado
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (count($productImages) > 1): ?>
                <div class="image-thumbnails">
                    <?php foreach ($productImages as $index => $image): ?>
                    <img src="<?= AppHelper::baseUrl($image) ?>" alt="<?= htmlspecialchars($product['name']) ?> - Imagen <?= $index + 1 ?>" 
                         class="thumbnail-image <?= $index === 0 ? 'active' : '' ?>" 
                         onclick="changeMainImage('<?= AppHelper::baseUrl($image) ?>', this)">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Información del producto -->
            <div class="product-info">
                <div class="product-header">
                    <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                    <?php if (!empty($product['brand'])): ?>
                    <div class="product-brand">por <strong><?= htmlspecialchars($product['brand']) ?></strong></div>
                    <?php endif; ?>
                    <div class="product-sku">SKU: <?= htmlspecialchars($product['sku']) ?></div>
                </div>

                <div class="product-pricing">
                    <?php if ($hasDiscount): ?>
                    <div class="price-container">
                        <span class="current-price">S/ <?= number_format($product['sale_price'], 2) ?></span>
                        <span class="original-price">S/ <?= number_format($product['price'], 2) ?></span>
                        <span class="savings">Ahorras S/ <?= number_format($product['price'] - $product['sale_price'], 2) ?></span>
                    </div>
                    <?php else: ?>
                    <div class="price-container">
                        <span class="current-price">S/ <?= number_format($product['price'], 2) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="product-description">
                    <h3>Descripción</h3>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>

                <div class="product-details">
                    <div class="detail-item">
                        <span class="detail-label">Stock disponible:</span>
                        <span class="detail-value stock-<?= $product['stock_quantity'] > 10 ? 'high' : ($product['stock_quantity'] > 0 ? 'low' : 'out') ?>">
                            <?= $product['stock_quantity'] > 0 ? $product['stock_quantity'] . ' unidades' : 'Agotado' ?>
                        </span>
                    </div>
                    <?php if (!empty($product['weight'])): ?>
                    <div class="detail-item">
                        <span class="detail-label">Peso:</span>
                        <span class="detail-value"><?= htmlspecialchars($product['weight']) ?> kg</span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($product['category_name'])): ?>
                    <div class="detail-item">
                        <span class="detail-label">Categoría:</span>
                        <span class="detail-value">
                            <a href="<?= AppHelper::baseUrl('/store?category=' . urlencode($product['category_slug'])) ?>">
                                <?= htmlspecialchars($product['category_name']) ?>
                            </a>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="product-actions">
                    <?php if ($product['stock_quantity'] > 0): ?>
                    <div class="quantity-selector">
                        <label for="quantity">Cantidad:</label>
                        <div class="quantity-controls">
                            <button type="button" class="qty-btn" onclick="changeQuantity(-1)">-</button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                            <button type="button" class="qty-btn" onclick="changeQuantity(1)">+</button>
                        </div>
                    </div>
                    
                    <button class="add-to-cart-btn" data-product-id="<?= $product['id'] ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z"/>
                            <path d="M9 8V17H11V8H9ZM13 8V17H15V8H13Z"/>
                        </svg>
                        Agregar al Carrito
                    </button>
                    <?php else: ?>
                    <button class="out-of-stock-btn" disabled>
                        Producto Agotado
                    </button>
                    <?php endif; ?>
                </div>

                <div class="product-guarantees">
                    <div class="guarantee-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1ZM10 17L6 13L7.41 11.59L10 14.17L16.59 7.58L18 9L10 17Z"/>
                        </svg>
                        <span>Garantía de calidad</span>
                    </div>
                    <div class="guarantee-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L13.5 2.5L16.17 5.17L10.5 10.84L11.92 12.25L15.92 8.25L18.42 10.75L21 9ZM1 15.5V17.5L9.5 9L8.09 7.59L1 15.5Z"/>
                        </svg>
                        <span>Envío rápido y seguro</span>
                    </div>
                    <div class="guarantee-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12S6.48 22 12 22 22 17.52 22 12 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z"/>
                        </svg>
                        <span>Soporte especializado</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos relacionados -->
        <?php if (!empty($relatedProducts)): ?>
        <section class="related-products">
            <h2>Productos Relacionados</h2>
            <div class="products-grid">
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                <div class="product-card">
                    <a href="<?= AppHelper::baseUrl('/store/product/' . $relatedProduct['slug']) ?>" class="product-link">
                        <div class="product-image-container">
                            <?php
                            $relatedImages = [];
                            if (!empty($relatedProduct['images'])) {
                                $images = json_decode($relatedProduct['images'], true);
                                if (is_array($images)) {
                                    $relatedImages = $images;
                                }
                            }
                            $relatedImageSrc = !empty($relatedImages) ? $relatedImages[0] : '/images/placeholder.jpg';
                            ?>
                            <img src="<?= AppHelper::baseUrl($relatedImageSrc) ?>" alt="<?= htmlspecialchars($relatedProduct['name']) ?>" class="product-image">
                            <?php if (!empty($relatedProduct['sale_price']) && $relatedProduct['sale_price'] < $relatedProduct['price']): ?>
                            <div class="discount-badge">-<?= round((($relatedProduct['price'] - $relatedProduct['sale_price']) / $relatedProduct['price']) * 100) ?>%</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($relatedProduct['name']) ?></h3>
                            <div class="product-price">
                                <?php if (!empty($relatedProduct['sale_price']) && $relatedProduct['sale_price'] < $relatedProduct['price']): ?>
                                <span class="current-price">S/ <?= number_format($relatedProduct['sale_price'], 2) ?></span>
                                <span class="original-price">S/ <?= number_format($relatedProduct['price'], 2) ?></span>
                                <?php else: ?>
                                <span class="current-price">S/ <?= number_format($relatedProduct['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>

<script>
function changeMainImage(imageSrc, thumbnail) {
    document.getElementById('mainProductImage').src = imageSrc;
    
    // Remover clase active de todas las miniaturas
    document.querySelectorAll('.thumbnail-image').forEach(img => {
        img.classList.remove('active');
    });
    
    // Agregar clase active a la miniatura clickeada
    thumbnail.classList.add('active');
}

function changeQuantity(delta) {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const newValue = currentValue + delta;
    const min = parseInt(quantityInput.min);
    const max = parseInt(quantityInput.max);
    
    if (newValue >= min && newValue <= max) {
        quantityInput.value = newValue;
    }
}

function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    
    // Aquí implementarías la lógica para agregar al carrito
    // Por ahora, solo mostraremos un mensaje
    alert(`Producto agregado al carrito (Cantidad: ${quantity})`);
    
    // En una implementación real, harías una petición AJAX al servidor
    // fetch('/api/cart/add', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/json',
    //     },
    //     body: JSON.stringify({
    //         product_id: productId,
    //         quantity: quantity
    //     })
    // })
    // .then(response => response.json())
    // .then(data => {
    //     // Manejar respuesta
    // });
}
</script>