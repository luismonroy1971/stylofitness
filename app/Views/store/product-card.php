<?php
/**
 * Tarjeta de producto - STYLOFITNESS
 * Plantilla para mostrar un producto en la tienda
 */

use StyleFitness\Helpers\AppHelper;

// Asegurarse de que las imágenes sean un array
$images = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
$mainImage = !empty($images) ? $images[0] : '/images/default-product.jpg';

// Calcular descuento si hay precio de oferta
$hasDiscount = !empty($product['sale_price']) && $product['sale_price'] < $product['price'];
$discountPercentage = $hasDiscount ? round(100 - ($product['sale_price'] * 100 / $product['price'])) : 0;
?>

<div class="product-card" data-product-id="<?php echo $product['id']; ?>">
    <div class="product-image">
        <a href="<?php echo AppHelper::baseUrl('store/product/' . $product['slug']); ?>">
            <img src="<?php echo AppHelper::uploadUrl($mainImage); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 loading="lazy">
        </a>
        
        <?php if ($hasDiscount): ?>
            <div class="product-badge">
                -<?php echo $discountPercentage; ?>%
            </div>
        <?php endif; ?>
        
        <?php if (!empty($product['is_featured'])): ?>
            <div class="product-badge featured-badge">
                <img src="<?php echo AppHelper::asset('images/featured-badge.svg'); ?>" alt="Destacado" class="featured-icon">
            </div>
        <?php endif; ?>
        
        <div class="product-actions">
            <button class="btn-action btn-quick-view" data-product-id="<?php echo $product['id']; ?>" title="Vista Rápida">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn-action btn-add-to-cart" data-product-id="<?php echo $product['id']; ?>" title="Añadir al Carrito">
                <i class="fas fa-shopping-cart"></i>
            </button>
            <button class="btn-action btn-add-to-wishlist" data-product-id="<?php echo $product['id']; ?>" title="Añadir a Favoritos">
                <i class="fas fa-heart"></i>
            </button>
        </div>
    </div>
    
    <div class="product-info">
        <div class="product-category">
            <a href="<?php echo AppHelper::baseUrl('store/category/' . $product['category_slug']); ?>">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </a>
        </div>
        
        <h3 class="product-title">
            <a href="<?php echo AppHelper::baseUrl('store/product/' . $product['slug']); ?>">
                <?php echo htmlspecialchars($product['name']); ?>
            </a>
        </h3>
        
        <div class="product-rating">
            <?php 
            $rating = isset($product['avg_rating']) ? round($product['avg_rating'] * 2) / 2 : 0; // Redondear a 0.5 más cercano
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $rating) {
                    echo '<i class="fas fa-star"></i>';
                } elseif ($i - 0.5 == $rating) {
                    echo '<i class="fas fa-star-half-alt"></i>';
                } else {
                    echo '<i class="far fa-star"></i>';
                }
            }
            ?>
            <span class="rating-count">(<?php echo isset($product['review_count']) ? $product['review_count'] : 0; ?>)</span>
        </div>
        
        <div class="product-price">
            <?php if ($hasDiscount): ?>
                <span class="original-price">S/ <?php echo number_format($product['price'], 2); ?></span>
                <span class="current-price">S/ <?php echo number_format($product['sale_price'], 2); ?></span>
            <?php else: ?>
                <span class="current-price">S/ <?php echo number_format($product['price'], 2); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="product-description">
            <?php 
            $description = isset($product['short_description']) && !empty($product['short_description']) 
                ? $product['short_description'] 
                : (isset($product['description']) ? $product['description'] : 'Sin descripción disponible');
            echo htmlspecialchars(substr($description, 0, 100) . '...');
            ?>
        </div>
        
        <div class="product-footer">
            <div class="stock-status <?php echo (isset($product['stock_quantity']) && $product['stock_quantity'] > 0) ? 'in-stock' : 'out-of-stock'; ?>">
                <?php if (isset($product['stock_quantity']) && $product['stock_quantity'] > 0): ?>
                    <i class="fas fa-check-circle"></i> En stock
                <?php else: ?>
                    <i class="fas fa-times-circle"></i> Agotado
                <?php endif; ?>
            </div>
            
            <div class="product-brand">
                <?php echo htmlspecialchars(isset($product['brand']) ? $product['brand'] : 'Sin marca'); ?>
            </div>
        </div>
    </div>
</div>