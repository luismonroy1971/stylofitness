<!-- Sección de Ofertas Especiales - PRIMERA SECCIÓN -->
<?php if (!empty($promotionalProducts)): ?>
<section class="section special-offers-section" id="special-offers">
    <div class="offers-container">
        <div class="offers-header">
            <div class="container">
                <div class="compact-title-section">
                    <h2 class="offers-title-compact animate__animated animate__fadeInDown">
                        <i class="fas fa-fire fire-icon"></i>
                        <span class="gradient-text-enhanced">OFERTAS ESPECIALES</span>
                        <i class="fas fa-fire fire-icon"></i>
                    </h2>
                    <p class="offers-subtitle-compact animate__animated animate__fadeInUp animate__delay-1s">
                        Descuentos exclusivos por tiempo limitado - ¡No te los pierdas!
                    </p>
                </div>
            </div>
        </div>
        
        <div class="mega-carousel" id="mega-offers-carousel">
            <div class="carousel-wrapper">
                <div class="carousel-track" id="offers-track">
                    <?php foreach ($promotionalProducts as $index => $product): ?>
                        <div class="mega-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>">
                            <div class="slide-background">
                                <div class="bg-overlay bg-overlay-<?php echo ($index % 4) + 1; ?>"></div>
                                <div class="bg-particles"></div>
                            </div>
                            
                            <div class="container">
                                <div class="slide-content">
                                    <div class="product-info-mega">
                                        <div class="mega-badge animate__animated animate__bounceIn animate__delay-1s">
                                            <?php if ($product['discount_percentage']): ?>
                                                <span class="discount-percent"><?php echo $product['discount_percentage']; ?>%</span>
                                                <span class="discount-text">OFF</span>
                                            <?php else: ?>
                                                <span class="special-text">ESPECIAL</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="product-category-mega animate__animated animate__fadeInLeft animate__delay-1s">
                                            <?php echo htmlspecialchars($product['category_name']); ?>
                                        </div>
                                        
                                        <h3 class="product-title-mega animate__animated animate__fadeInLeft animate__delay-1-5s">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h3>
                                        
                                        <p class="product-description-mega animate__animated animate__fadeInLeft animate__delay-2s">
                                            <?php echo htmlspecialchars($product['short_description'] ?? $product['description'] ?? ''); ?>
                                        </p>
                                        
                                        <div class="price-mega animate__animated animate__fadeInLeft animate__delay-2-5s">
                                            <?php if ($product['sale_price']): ?>
                                                <span class="original-price-mega">S/ <?php echo number_format($product['price'], 2); ?></span>
                                                <span class="sale-price-mega">S/ <?php echo number_format($product['sale_price'], 2); ?></span>
                                                <span class="savings-mega">
                                                    ¡Ahorras S/ <?php echo number_format($product['price'] - $product['sale_price'], 2); ?>!
                                                </span>
                                            <?php else: ?>
                                                <span class="current-price-mega">S/ <?php echo number_format($product['price'], 2); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="countdown-timer animate__animated animate__fadeInLeft animate__delay-3s" 
                                             data-end-date="<?php echo date('Y-m-d H:i:s', strtotime('+7 days')); ?>">
                                            <div class="timer-label">Oferta termina en:</div>
                                            <div class="timer-digits">
                                                <div class="timer-unit">
                                                    <span class="timer-value" id="days-<?php echo $index; ?>">07</span>
                                                    <span class="timer-label-small">DÍAS</span>
                                                </div>
                                                <div class="timer-separator">:</div>
                                                <div class="timer-unit">
                                                    <span class="timer-value" id="hours-<?php echo $index; ?>">23</span>
                                                    <span class="timer-label-small">HORAS</span>
                                                </div>
                                                <div class="timer-separator">:</div>
                                                <div class="timer-unit">
                                                    <span class="timer-value" id="minutes-<?php echo $index; ?>">59</span>
                                                    <span class="timer-label-small">MIN</span>
                                                </div>
                                                <div class="timer-separator">:</div>
                                                <div class="timer-unit">
                                                    <span class="timer-value" id="seconds-<?php echo $index; ?>">59</span>
                                                    <span class="timer-label-small">SEG</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mega-actions animate__animated animate__fadeInUp animate__delay-3-5s">
                                            <button class="btn-mega-primary btn-add-cart-mega" data-product-id="<?php echo $product['id']; ?>">
                                                <i class="fas fa-cart-plus"></i>
                                                <span>AGREGAR AL CARRITO</span>
                                                <div class="btn-shine"></div>
                                            </button>
                                            
                                            <a href="<?php echo AppHelper::getBaseUrl('store/product/' . $product['slug']); ?>" 
                                               class="btn-mega-secondary">
                                                <i class="fas fa-eye"></i>
                                                Ver Detalles
                                            </a>
                                        </div>
                                        
                                        <div class="stock-indicator animate__animated animate__fadeInUp animate__delay-4s">
                                            <?php 
                                            $stockPercentage = min(100, ($product['stock_quantity'] / 50) * 100);
                                            $stockClass = $stockPercentage > 50 ? 'high' : ($stockPercentage > 20 ? 'medium' : 'low');
                                            ?>
                                            <div class="stock-bar">
                                                <div class="stock-fill stock-<?php echo $stockClass; ?>" 
                                                     style="width: <?php echo $stockPercentage; ?>%"></div>
                                            </div>
                                            <span class="stock-text">
                                                <?php if ($product['stock_quantity'] > 10): ?>
                                                    ✨ ¡En Stock! - Más de <?php echo $product['stock_quantity']; ?> disponibles
                                                <?php elseif ($product['stock_quantity'] > 0): ?>
                                                    ⚠ ¡Últimas <?php echo $product['stock_quantity']; ?> unidades!
                                                <?php else: ?>
                                                    ❌ Agotado
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="product-visual-mega animate__animated animate__fadeInRight animate__delay-1s">
                                        <div class="mega-image-container">
                                            <?php 
                                            $productImages = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
                                            $mainImage = !empty($productImages) ? $productImages[0] : '/public/images/default-product.jpg';
                                            ?>
                                            <img src="<?php echo AppHelper::uploadUrl($mainImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="mega-product-image"
                                                 loading="lazy">
                                            
                                            <div class="image-glow"></div>
                                            <div class="floating-elements">
                                                <div class="float-element float-1">+</div>
                                                <div class="float-element float-2">★</div>
                                                <div class="float-element float-3">◆</div>
                                                <div class="float-element float-4">✨</div>
                                            </div>
                                        </div>
                                        
                                        <div class="product-features">
                                            <div class="feature-item">
                                                <i class="fas fa-shipping-fast"></i>
                                                <span>Envío Gratis</span>
                                            </div>
                                            <div class="feature-item">
                                                <i class="fas fa-shield-alt"></i>
                                                <span>Garantía Total</span>
                                            </div>
                                            <div class="feature-item">
                                                <i class="fas fa-medal"></i>
                                                <span>Calidad Premium</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Controles avanzados del carrusel -->
            <div class="mega-carousel-controls">
                <button class="mega-nav mega-prev" id="mega-prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="mega-nav mega-next" id="mega-next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <div class="mega-indicators">
                <?php foreach ($promotionalProducts as $index => $product): ?>
                    <button class="mega-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                            data-slide="<?php echo $index; ?>">
                        <span class="dot-progress"></span>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Progress bar -->
            <div class="carousel-progress">
                <div class="progress-bar-mega" id="carousel-progress"></div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Nueva Sección de Características Impactante -->
<section class="section modern-features-section">
    <div class="features-background">
        <div class="gradient-overlay-enhanced"></div>
        <div class="animated-particles">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
            <div class="particle particle-4"></div>
            <div class="particle particle-5"></div>
            <div class="particle particle-6"></div>
        </div>
        <div class="geometric-shapes">
            <div class="geo-shape geo-triangle"></div>
            <div class="geo-shape geo-circle"></div>
            <div class="geo-shape geo-square"></div>
        </div>
    </div>
    
    <div class="container">
        <div class="features-hero-enhanced" data-aos="zoom-in">
            <div class="features-badge">
                <i class="fas fa-crown"></i>
                <span>PREMIUM EXPERIENCE</span>
            </div>
            <h2 class="features-mega-title-new">
                <span class="title-line-1-new">¿Por Qué Elegir</span>
                <span class="title-line-2-new gradient-text-premium">STYLOFITNESS?</span>
            </h2>
            <p class="features-mega-subtitle-new">
                La revolución fitness que transformará tu vida por completo
            </p>
            <div class="features-stats-mini">
                <div class="mini-stat">
                    <span class="mini-stat-number">10K+</span>
                    <span class="mini-stat-label">Clientes Felices</span>
                </div>
                <div class="mini-stat">
                    <span class="mini-stat-number">15</span>
                    <span class="mini-stat-label">Sedes</span>
                </div>
                <div class="mini-stat">
                    <span class="mini-stat-number">98%</span>
                    <span class="mini-stat-label">Satisfacción</span>
                </div>
            </div>
        </div>
        
        <div class="modern-features-grid-enhanced">
            <!-- Rutinas Personalizadas -->
            <div class="modern-feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card-inner">
                    <div class="feature-glow"></div>
                    <div class="feature-icon-modern">
                        <div class="icon-bg">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div class="icon-pulse"></div>
                    </div>
                    
                    <div class="feature-content-modern">
                        <div class="feature-badge-mini">
                            <i class="fas fa-magic"></i>
                            <span>IA POWERED</span>
                        </div>
                        <h3 class="feature-title-modern">Rutinas Personalizadas</h3>
                        <p class="feature-description-modern">
                            Entrenamientos inteligentes diseñados con IA avanzada que se adaptan a tu progreso en tiempo real
                        </p>
                        
                        <div class="feature-highlights">
                            <div class="highlight-item">
                                <i class="fas fa-video"></i>
                                <span>Videos HD Explicativos</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-chart-line"></i>
                                <span>Seguimiento en Tiempo Real</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-robot"></i>
                                <span>Ajustes Automáticos IA</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-headset"></i>
                                <span>Soporte 24/7</span>
                            </div>
                        </div>
                        
                        <div class="feature-stats">
                            <div class="stat">
                                <span class="stat-number">1000+</span>
                                <span class="stat-label">Ejercicios</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">95%</span>
                                <span class="stat-label">Efectividad</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tienda Premium -->
            <div class="modern-feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card-inner">
                    <div class="feature-glow"></div>
                    <div class="feature-icon-modern">
                        <div class="icon-bg">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="icon-pulse"></div>
                    </div>
                    
                    <div class="feature-content-modern">
                        <h3 class="feature-title-modern">Tienda Premium</h3>
                        <p class="feature-description-modern">
                            Suplementos y accesorios de máxima calidad con recomendaciones inteligentes
                        </p>
                        
                        <div class="feature-highlights">
                            <div class="highlight-item">
                                <i class="fas fa-certificate"></i>
                                <span>Productos Certificados</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-brain"></i>
                                <span>Recomendaciones IA</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-shipping-fast"></i>
                                <span>Envío Express Gratis</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Garantía Total</span>
                            </div>
                        </div>
                        
                        <div class="feature-stats">
                            <div class="stat">
                                <span class="stat-number">500+</span>
                                <span class="stat-label">Productos</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">99%</span>
                                <span class="stat-label">Satisfacción</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Clases Grupales -->
            <div class="modern-feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card-inner">
                    <div class="feature-glow"></div>
                    <div class="feature-icon-modern">
                        <div class="icon-bg">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="icon-pulse"></div>
                    </div>
                    
                    <div class="feature-content-modern">
                        <h3 class="feature-title-modern">Clases Grupales</h3>
                        <p class="feature-description-modern">
                            Entrenamientos grupales energizantes con instructores certificados
                        </p>
                        
                        <div class="feature-highlights">
                            <div class="highlight-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Horarios Flexibles</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-user-graduate"></i>
                                <span>Instructores Expertos</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Reserva Online</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-fire"></i>
                                <span>Ambiente Motivador</span>
                            </div>
                        </div>
                        
                        <div class="feature-stats">
                            <div class="stat">
                                <span class="stat-number">50+</span>
                                <span class="stat-label">Clases/Sem</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">15</span>
                                <span class="stat-label">Sedes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Seguimiento Avanzado -->
            <div class="modern-feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card-inner">
                    <div class="feature-glow"></div>
                    <div class="feature-icon-modern">
                        <div class="icon-bg">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="icon-pulse"></div>
                    </div>
                    
                    <div class="feature-content-modern">
                        <h3 class="feature-title-modern">Seguimiento Avanzado</h3>
                        <p class="feature-description-modern">
                            Monitoreo completo con métricas avanzadas y análisis predictivo
                        </p>
                        
                        <div class="feature-highlights">
                            <div class="highlight-item">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard Interactivo</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-file-chart-line"></i>
                                <span>Reportes Detallados</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-bullseye"></i>
                                <span>Metas Inteligentes</span>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-analytics"></i>
                                <span>Análisis Predictivo</span>
                            </div>
                        </div>
                        
                        <div class="feature-stats">
                            <div class="stat">
                                <span class="stat-number">25+</span>
                                <span class="stat-label">Métricas</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">100%</span>
                                <span class="stat-label">Precisión</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sección de Beneficios Adicionales -->
        <div class="additional-benefits" data-aos="fade-up" data-aos-delay="500">
            <div class="benefits-title">
                <h3>Y mucho más...</h3>
            </div>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <i class="fas fa-mobile-alt"></i>
                    <span>App Móvil Nativa</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-cloud"></i>
                    <span>Sincronización Cloud</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-medal"></i>
                    <span>Sistema de Logros</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-users-cog"></i>
                    <span>Comunidad Activa</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-heart-pulse"></i>
                    <span>Monitor de Salud</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Cursos y Talleres</span>
                </div>
            </div>
        </div>
        
        <!-- CTA de la sección -->
        <div class="features-cta" data-aos="zoom-in" data-aos-delay="600">
            <div class="cta-content">
                <h3>¿Listo para experimentar la diferencia?</h3>
                <p>Únete a la revolución fitness más avanzada del país</p>
                <div class="cta-buttons">
                    <a href="<?php echo AppHelper::getBaseUrl('register'); ?>" class="btn-cta-primary">
                        <i class="fas fa-rocket"></i>
                        Comenzar Gratis
                    </a>
                    <a href="<?php echo AppHelper::getBaseUrl('contact'); ?>" class="btn-cta-secondary">
                        <i class="fas fa-phone"></i>
                        Solicitar Demo
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Productos Destacados -->
<?php if (!empty($featuredProducts)): ?>
<section class="section featured-products-section-enhanced">
    <div class="products-background">
        <div class="products-overlay"></div>
        <div class="floating-icons">
            <i class="fas fa-dumbbell floating-icon icon-1"></i>
            <i class="fas fa-fire floating-icon icon-2"></i>
            <i class="fas fa-star floating-icon icon-3"></i>
            <i class="fas fa-bolt floating-icon icon-4"></i>
        </div>
    </div>
    
    <div class="container">
        <div class="section-header-enhanced" data-aos="fade-up">
            <div class="section-badge-products">
                <i class="fas fa-trophy"></i>
                <span>LO MEJOR DEL MERCADO</span>
            </div>
            <h2 class="section-title-enhanced">
                <span class="title-accent">Productos</span>
                <span class="title-main gradient-text-premium">DESTACADOS</span>
            </h2>
            <p class="section-subtitle-enhanced">Los suplementos más innovadores y efectivos del mercado fitness</p>
            <div class="section-decorative-line"></div>
        </div>
        
        <div class="products-grid-enhanced">
            <?php foreach ($featuredProducts as $index => $product): ?>
                <div class="product-card-enhanced hover-lift-enhanced" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="product-card-inner">
                        <div class="product-image-enhanced">
                            <?php 
                            $productImages = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
                            $mainImage = !empty($productImages) ? $productImages[0] : '/public/images/default-product.jpg';
                            ?>
                            <img src="<?php echo AppHelper::uploadUrl($mainImage); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 loading="lazy">
                            
                            <div class="product-overlay-enhanced">
                                <div class="product-actions-enhanced">
                                    <button class="btn-action-modern btn-quick-view" data-product-id="<?php echo $product['id']; ?>" title="Vista Rápida">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action-modern btn-wishlist" data-product-id="<?php echo $product['id']; ?>" title="Agregar a Favoritos">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="btn-action-modern btn-compare" data-product-id="<?php echo $product['id']; ?>" title="Comparar">
                                        <i class="fas fa-balance-scale"></i>
                                    </button>
                                </div>
                                <div class="quick-add-overlay">
                                    <button class="btn-quick-add" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-cart-plus"></i>
                                        <span>Añadir al Carrito</span>
                                    </button>
                                </div>
                            </div>
                            
                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                <?php $discount = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
                                <div class="product-badge-enhanced discount-badge">-<?php echo $discount; ?>%</div>
                            <?php elseif ($product['is_featured']): ?>
                                <div class="product-badge-enhanced featured-badge">
                                    <i class="fas fa-crown"></i>
                                    <span>PREMIUM</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-glow"></div>
                        </div>
                    
                    <div class="product-info-enhanced">
                        <div class="product-category-enhanced"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></div>
                        <h3 class="product-title-enhanced">
                            <a href="<?php echo AppHelper::getBaseUrl('store/product/' . $product['slug']); ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <p class="product-description-enhanced"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></p>
                        
                        <div class="product-rating-enhanced">
                            <?php
                            $rating = $product['avg_rating'] ?? 4.5;
                            for ($i = 1; $i <= 5; $i++):
                                if ($i <= $rating):
                                    echo '<i class="fas fa-star star-filled"></i>';
                                elseif ($i - 0.5 <= $rating):
                                    echo '<i class="fas fa-star-half-alt star-half"></i>';
                                else:
                                    echo '<i class="far fa-star star-empty"></i>';
                                endif;
                            endfor;
                            ?>
                            <span class="rating-count-enhanced">(<?php echo $product['reviews_count'] ?? 0; ?>)</span>
                        </div>
                        
                        <div class="product-price-enhanced">
                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                <span class="original-price-enhanced">S/ <?php echo number_format($product['price'], 2); ?></span>
                                <span class="current-price-enhanced">S/ <?php echo number_format($product['sale_price'], 2); ?></span>
                                <div class="savings-badge">
                                    <i class="fas fa-piggy-bank"></i>
                                    <span>Ahorras S/ <?php echo number_format($product['price'] - $product['sale_price'], 2); ?></span>
                                </div>
                            <?php else: ?>
                                <span class="current-price-enhanced">S/ <?php echo number_format($product['price'], 2); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-stock-enhanced">
                            <?php if ($product['stock_quantity'] > 10): ?>
                                <div class="stock-indicator high-stock">
                                    <i class="fas fa-check-circle"></i>
                                    <span>En Stock</span>
                                </div>
                            <?php elseif ($product['stock_quantity'] > 0): ?>
                                <div class="stock-indicator low-stock">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>¡Últimas <?php echo $product['stock_quantity']; ?> unidades!</span>
                                </div>
                            <?php else: ?>
                                <div class="stock-indicator out-of-stock">
                                    <i class="fas fa-times-circle"></i>
                                    <span>Agotado</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-actions-bottom">
                            <button class="btn-add-cart-enhanced <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>" 
                                    data-product-id="<?php echo $product['id']; ?>"
                                    <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                <i class="fas fa-shopping-cart"></i>
                                <span><?php echo $product['stock_quantity'] > 0 ? 'Agregar al Carrito' : 'Agotado'; ?></span>
                                <div class="btn-shine-effect"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="section-cta" data-aos="fade-up">
            <a href="<?php echo AppHelper::getBaseUrl('store'); ?>" class="btn-primary btn-lg">
                <i class="fas fa-store"></i>
                Ver Todos los Productos
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Clases Grupales -->
<?php if (!empty($upcomingClasses)): ?>
<section class="section classes-section-enhanced">
    <div class="classes-background">
        <div class="classes-overlay"></div>
        <div class="animated-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
    
    <div class="container">
        <div class="section-header-enhanced" data-aos="fade-up">
            <div class="section-badge-classes">
                <i class="fas fa-users"></i>
                <span>EXPERIENCIA GRUPAL</span>
            </div>
            <h2 class="section-title-enhanced">
                <span class="title-accent">Clases</span>
                <span class="title-main gradient-text-premium">GRUPALES</span>
            </h2>
            <p class="section-subtitle-enhanced">Entrena con otros y mantente motivado en nuestras sedes</p>
            <div class="section-decorative-line"></div>
        </div>
        
        <div class="classes-grid">
            <?php foreach ($upcomingClasses as $index => $class): ?>
                <div class="class-card hover-lift" data-aos="fade-up" data-aos-delay="<?php echo $index * 150; ?>">
                    <div class="class-header">
                        <div class="class-time">
                            <i class="fas fa-clock"></i>
                            <?php
                            $dayNames = [
                                'monday' => 'Lunes',
                                'tuesday' => 'Martes', 
                                'wednesday' => 'Miércoles',
                                'thursday' => 'Jueves',
                                'friday' => 'Viernes',
                                'saturday' => 'Sábado',
                                'sunday' => 'Domingo'
                            ];
                            echo $dayNames[$class['day_of_week']] . ' ' . date('H:i', strtotime($class['start_time']));
                            ?>
                        </div>
                        <div class="class-duration"><?php echo $class['duration_minutes']; ?> min</div>
                    </div>
                    
                    <div class="class-body">
                        <h3 class="class-name"><?php echo htmlspecialchars($class['name']); ?></h3>
                        <p class="class-instructor">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($class['first_name'] . ' ' . $class['last_name']); ?>
                        </p>
                        <p class="class-description"><?php echo htmlspecialchars($class['description'] ?? ''); ?></p>
                        
                        <div class="class-details">
                            <div class="class-capacity">
                                <i class="fas fa-users"></i>
                                <?php echo $class['booked_spots']; ?>/<?php echo $class['max_participants']; ?> lugares
                            </div>
                            <div class="class-availability">
                                <?php 
                                $available = $class['max_participants'] - $class['booked_spots'];
                                if ($available > 0):
                                ?>
                                    <span class="available">✓ Disponible</span>
                                <?php else: ?>
                                    <span class="full">Completo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="class-progress">
                            <?php $fillPercentage = ($class['booked_spots'] / $class['max_participants']) * 100; ?>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $fillPercentage; ?>%"></div>
                            </div>
                        </div>
                        
                        <?php if (AppHelper::isLoggedIn()): ?>
                            <button class="btn-book-class <?php echo $available <= 0 ? 'disabled' : ''; ?>" 
                                    data-class-id="<?php echo $class['id']; ?>"
                                    <?php echo $available <= 0 ? 'disabled' : ''; ?>>
                                <i class="fas fa-calendar-plus"></i>
                                <?php echo $available > 0 ? 'Reservar Clase' : 'Clase Completa'; ?>
                            </button>
                        <?php else: ?>
                            <a href="<?php echo AppHelper::getBaseUrl('login'); ?>" class="btn-book-class">
                                <i class="fas fa-sign-in-alt"></i>
                                Inicia Sesión para Reservar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="section-cta" data-aos="fade-up">
            <a href="<?php echo AppHelper::getBaseUrl('classes'); ?>" class="btn-primary btn-lg">
                <i class="fas fa-calendar"></i>
                Ver Todas las Clases
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonios -->
<?php if (!empty($testimonials)): ?>
<section class="section testimonials-section-enhanced">
    <div class="testimonials-background">
        <div class="testimonials-overlay"></div>
        <div class="floating-quotes">
            <div class="quote-icon quote-1">❝</div>
            <div class="quote-icon quote-2">❞</div>
            <div class="quote-icon quote-3">❝</div>
            <div class="quote-icon quote-4">❞</div>
        </div>
    </div>
    
    <div class="container">
        <div class="section-header-enhanced" data-aos="fade-up">
            <div class="section-badge-testimonials">
                <i class="fas fa-quote-left"></i>
                <span>TESTIMONIOS REALES</span>
            </div>
            <h2 class="section-title-enhanced">
                <span class="title-accent">Lo Que Dicen</span>
                <span class="title-main gradient-text-premium">NUESTROS CLIENTES</span>
            </h2>
            <p class="section-subtitle-enhanced">Historias reales de transformación y éxito</p>
            <div class="section-decorative-line"></div>
        </div>
        
        <div class="testimonials-grid-enhanced">
            <?php foreach ($testimonials as $index => $testimonial): ?>
                <div class="testimonial-card-modern glass-effect-enhanced" data-aos="fade-up" data-aos-delay="<?php echo $index * 200; ?>">
                    <div class="testimonial-card-inner">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar-section">
                                <div class="avatar-container">
                                    <img src="<?php echo AppHelper::asset('images/testimonials/' . ($testimonial['image'] ?? 'default.jpg')); ?>" 
                                         alt="<?php echo htmlspecialchars($testimonial['name']); ?>" 
                                         class="author-avatar-enhanced">
                                    <div class="avatar-ring"></div>
                                    <div class="avatar-glow"></div>
                                </div>
                                <div class="testimonial-verified">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Cliente Verificado</span>
                                </div>
                            </div>
                            
                            <div class="testimonial-rating-enhanced">
                                <?php for ($i = 1; $i <= $testimonial['rating']; $i++): ?>
                                    <i class="fas fa-star star-animated" style="animation-delay: <?php echo $i * 0.1; ?>s"></i>
                                <?php endfor; ?>
                                <span class="rating-number"><?php echo $testimonial['rating']; ?>.0</span>
                            </div>
                        </div>
                        
                        <div class="quote-container">
                            <div class="quote-mark quote-open">“</div>
                            <blockquote class="testimonial-text-enhanced">
                                <?php echo htmlspecialchars($testimonial['text']); ?>
                            </blockquote>
                            <div class="quote-mark quote-close">”</div>
                        </div>
                        
                        <div class="testimonial-footer">
                            <div class="author-info-enhanced">
                                <div class="author-name-enhanced"><?php echo htmlspecialchars($testimonial['name']); ?></div>
                                <div class="author-role-enhanced"><?php echo htmlspecialchars($testimonial['role']); ?></div>
                                <div class="author-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Lima, Perú</span>
                                </div>
                            </div>
                            
                            <div class="testimonial-date">
                                <i class="fas fa-calendar-alt"></i>
                                <span><?php echo date('M Y', strtotime('-' . rand(1, 12) . ' months')); ?></span>
                            </div>
                        </div>
                        
                        <div class="testimonial-metrics">
                            <div class="metric">
                                <i class="fas fa-trophy"></i>
                                <span><?php echo rand(5, 25); ?>kg perdidos</span>
                            </div>
                            <div class="metric">
                                <i class="fas fa-fire"></i>
                                <span><?php echo rand(3, 18); ?> meses</span>
                            </div>
                        </div>
                        
                        <div class="card-glow-effect"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Final -->
<section class="section cta-section bg-primary">
    <div class="container">
        <div class="cta-content" data-aos="zoom-in">
            <h2 class="cta-title">¿Listo Para Transformar Tu Vida?</h2>
            <p class="cta-subtitle">
                Únete a miles de personas que ya han logrado sus objetivos con STYLOFITNESS.
                Tu transformación comienza con un solo clic.
            </p>
            
            <div class="cta-buttons">
                <?php if (!AppHelper::isLoggedIn()): ?>
                    <a href="<?php echo AppHelper::getBaseUrl('register'); ?>" class="btn-secondary btn-lg">
                        <i class="fas fa-user-plus"></i>
                        Crear Cuenta Gratis
                    </a>
                    <a href="<?php echo AppHelper::getBaseUrl('contact'); ?>" class="btn-outline btn-lg">
                        <i class="fas fa-phone"></i>
                        Hablar con un Asesor
                    </a>
                <?php else: ?>
                    <a href="<?php echo AppHelper::getBaseUrl('routines/create'); ?>" class="btn-secondary btn-lg">
                        <i class="fas fa-plus"></i>
                        Crear Mi Primera Rutina
                    </a>
                    <a href="<?php echo AppHelper::getBaseUrl('store'); ?>" class="btn-outline btn-lg">
                        <i class="fas fa-shopping-cart"></i>
                        Explorar Productos
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="cta-guarantees">
                <div class="guarantee-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Garantía 30 días</span>
                </div>
                <div class="guarantee-item">
                    <i class="fas fa-headset"></i>
                    <span>Soporte 24/7</span>
                </div>
                <div class="guarantee-item">
                    <i class="fas fa-medal"></i>
                    <span>Resultados garantizados</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Esquema estructurado para SEO -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "STYLOFITNESS - Gimnasio Profesional",
    "description": "<?php echo $pageDescription; ?>",
    "url": "<?php echo AppHelper::getBaseUrl(); ?>",
    "mainEntity": {
        "@type": "HealthClub",
        "name": "STYLOFITNESS",
        "description": "Gimnasio profesional con rutinas personalizadas y tienda de suplementos",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Av. Principal 123",
            "addressLocality": "San Isidro",
            "addressRegion": "Lima",
            "addressCountry": "PE"
        },
        "telephone": "+51-999-888-777",
        "priceRange": "$$",
        "amenityFeature": [
            "Rutinas Personalizadas",
            "Tienda de Suplementos", 
            "Clases Grupales",
            "Entrenadores Certificados"
        ]
    }
}
</script>