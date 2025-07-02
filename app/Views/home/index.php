<!-- Sección de Ofertas Especiales - PRIMERA SECCIÓN -->
<?php use StyleFitness\Helpers\AppHelper; ?>

<!-- Hero Section Configurado -->
<?php 
$heroData = $this->landingController->getHeroData();
$heroConfig = $heroData['config'];
?>
<section class="hero-main-section" id="hero-main">
    <div class="hero-main-container">
        <div class="hero-background">
            <div class="hero-overlay"></div>
            <?php if (!empty($heroConfig['config_data']['background_image'])): ?>
                <img src="<?php echo AppHelper::asset($heroConfig['config_data']['background_image']); ?>" alt="Hero Background" class="hero-bg-image">
            <?php endif; ?>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <?php if (!empty($heroConfig['config_data']['show_stats']) && !empty($heroData['stats'])): ?>
                    <div class="hero-stats animate__animated animate__fadeInUp animate__delay-4s">
                        <?php foreach ($heroData['stats'] as $stat): ?>
                        <div class="hero-stat-item">
                            <span class="stat-number"><?php echo $stat['value']; ?></span>
                            <span class="stat-label"><?php echo $stat['label']; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Carrusel Impresionante de Productos Destacados -->
<?php if (!empty($featuredProducts)): ?>
<section class="hero-carousel-section" id="hero-carousel">
    <div class="hero-container">
        <div class="hero-header">
            <div class="container">
                <div class="hero-title-section">
                    <h1 class="hero-title animate__animated animate__fadeInDown">
                        <i class="fas fa-crown crown-icon"></i>
                        <span class="gradient-text-premium">PRODUCTOS DESTACADOS</span>
                        <i class="fas fa-crown crown-icon"></i>
                    </h1>
                </div>
            </div>
        </div>
        
        <div class="hero-carousel" id="hero-products-carousel">
            <div class="carousel-wrapper">
                <div class="carousel-track" id="hero-track">
                    <?php foreach ($featuredProducts as $index => $product): ?>
                        <div class="mega-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>">
                            <div class="slide-background">
                                <div class="bg-overlay bg-overlay-<?php echo ($index % 4) + 1; ?>"></div>
                                <div class="bg-particles"></div>
                            </div>
                            
                            <div class="container">
                                <div class="slide-content">
                                    <!-- Columna 1: Información básica del producto (más angosta) -->
                                    <div class="product-info-mega">
                                        <div class="product-category-mega animate__animated animate__fadeInLeft animate__delay-1s">
                                            <?php echo htmlspecialchars($product['category_name'] ?? 'Categoría'); ?>
                                        </div>

                                        <h3 class="product-title-mega animate__animated animate__fadeInLeft animate__delay-1-5s">
                                            <?php echo htmlspecialchars($product['name'] ?? 'Producto'); ?>
                                        </h3>

                                        <p class="product-description-mega animate__animated animate__fadeInLeft animate__delay-2s">
                                            <?php 
                                            $description = '';
                                            if (!empty($product['short_description'])) {
                                                $description = $product['short_description'];
                                            } elseif (!empty($product['description'])) {
                                                $description = $product['description'];
                                            }
                                            echo htmlspecialchars($description); 
                                            ?>
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
                                    </div>
                                    
                                    <!-- Columna 2: Imagen del producto -->
                                    <div class="product-visual-mega animate__animated animate__fadeInUp animate__delay-1s">
                                        <div class="mega-image-container">
                                            <?php 
                                            $productImages = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
                                            if (!empty($productImages)) {
                                                // Si la ruta ya incluye /uploads/, usar directamente con baseUrl
                                                if (strpos($productImages[0], '/uploads/') === 0) {
                                                    $mainImage = AppHelper::baseUrl($productImages[0]);
                                                } else {
                                                    // Si no incluye /uploads/, usar uploadUrl
                                                    $mainImage = AppHelper::uploadUrl($productImages[0]);
                                                }
                                            } else {
                                                $mainImage = AppHelper::asset('images/placeholder.jpg');
                                            }
                                            ?>
                                            <img src="<?php echo $mainImage; ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name'] ?? 'Producto'); ?>"
                                                 class="mega-product-image"
                                                 loading="lazy">
                                            
                                            <div class="image-glow"></div>
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
                                        </div>
                                    </div>
                                    
                                    <!-- Columna 3: Detalles adicionales (timer, botones, stock) -->
                                    <div class="product-details-mega animate__animated animate__fadeInRight animate__delay-2s">
                                        <div class="countdown-timer" 
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
                                        
                                        <div class="mega-actions">
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
                                        
                                        <div class="stock-indicator">
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
                <?php foreach ($featuredProducts as $index => $product): ?>
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

<!-- Nueva Sección Hero Configurada -->
<?php 
$heroData = $this->landingController->getHeroData();
$heroConfig = $heroData['config'];
$gymStats = $heroData['stats'] ?? [];
?>
<section class="hero-config-section" id="hero-configurado">
    <div class="container">
        <div class="hero-config-content">
            <div class="hero-config-badge">
                <i class="fas fa-rocket"></i>
                <span>CONFIGURACIÓN DINÁMICA</span>
            </div>
            <h1 class="hero-config-title">
                <?php echo htmlspecialchars($heroConfig['title'] ?? 'Bienvenido a STYLOFITNESS'); ?>
            </h1>
            <h2 class="hero-config-subtitle">
                <?php echo htmlspecialchars($heroConfig['subtitle'] ?? 'Tu transformación comienza aquí'); ?>
            </h2>
            <p class="hero-config-description">
                <?php echo htmlspecialchars($heroConfig['description'] ?? 'Descubre una nueva forma de entrenar con tecnología de vanguardia y entrenadores expertos.'); ?>
            </p>
            <?php if (!empty($heroConfig['cta_link']) && !empty($heroConfig['cta_text'])): ?>
            <a href="<?php echo htmlspecialchars($heroConfig['cta_link']); ?>" class="hero-config-cta">
                <i class="fas fa-play"></i>
                <?php echo htmlspecialchars($heroConfig['cta_text']); ?>
            </a>
            <?php endif; ?>
            
            <?php if (!empty($gymStats)): ?>
            <div class="hero-config-stats">
                <?php foreach ($gymStats as $stat): ?>
                <div class="hero-config-stat">
                    <span class="hero-config-stat-number"><?php echo htmlspecialchars($stat['value']); ?></span>
                    <span class="hero-config-stat-label"><?php echo htmlspecialchars($stat['label']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Nueva Sección de Características Configurada -->
<?php 
$servicesData = $this->landingController->getServicesData();
$featuresConfig = $servicesData['config'];
$whyChooseUsItems = $servicesData['why_choose_us'];
?>
<section class="section features-config-section" id="features-configurado" style="background-color: <?php echo $featuresConfig['config_data']['background_color'] ?? '#2d1b69'; ?>">
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
            <div class="features-config-badge">
                <i class="fas fa-star"></i>
                <span>CARACTERÍSTICAS ÚNICAS</span>
            </div>
            <h2 class="features-mega-title-new">
                <span class="title-line-1-new"><?php echo htmlspecialchars($featuresConfig['title'] ?? '¿Por qué elegir'); ?></span>
                <span class="title-line-2-new features-config-title">STYLOFITNESS?</span>
            </h2>
            <p class="features-mega-subtitle-new">
                <?php echo htmlspecialchars($featuresConfig['subtitle'] ?? 'Descubre lo que nos hace únicos'); ?>
            </p>
            <div class="features-stats-mini">
                <div class="mini-stat">
                    <span class="mini-stat-number">10K+</span>
                    <span class="mini-stat-label">Clientes Felices</span>
                </div>
                <div class="mini-stat">
                    <span class="mini-stat-number">2</span>
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
                                <span class="stat-number">2</span>
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
        
    </div>
</section>

<!-- Sección Por Qué Elegirnos -->
<?php if (!empty($whyChooseUs)): ?>
<section class="why-choose-us-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">¿Por Qué Elegirnos?</h2>
            <p class="section-subtitle">Descubre las razones que nos hacen únicos</p>
        </div>
        <div class="features-grid">
            <?php $featureIndex = 0; foreach ($whyChooseUs as $feature): ?>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="<?php echo $featureIndex * 100; ?>">
                    <div class="feature-icon">
                        <i class="<?php echo htmlspecialchars($feature['icon'] ?? 'fas fa-star'); ?>"></i>
                    </div>
                    <h3 class="feature-title"><?php echo htmlspecialchars($feature['title']); ?></h3>
                    <p class="feature-description"><?php echo htmlspecialchars($feature['description']); ?></p>
                </div>
            <?php $featureIndex++; endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

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
                            $mainImage = !empty($productImages) ? 
                                (strpos($productImages[0], '/uploads/') === 0 ? AppHelper::getBaseUrl() . ltrim($productImages[0], '/') : AppHelper::uploadUrl($productImages[0])) : 
                                AppHelper::asset('images/placeholder.jpg');
                            ?>
                            <img src="<?php echo $mainImage; ?>" 
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
                        <div class="product-category-enhanced"><?php echo htmlspecialchars($product['category_name'] ?? 'Categoría'); ?></div>
                        <h3 class="product-title-enhanced">
                            <a href="<?php echo AppHelper::getBaseUrl('store/product/' . $product['slug']); ?>">
                                <?php echo htmlspecialchars($product['name'] ?? 'Producto'); ?>
                            </a>
                        </h3>
                       <p class="product-description-enhanced"><?php echo htmlspecialchars($product['short_description'] ?? 'Descripción del producto'); ?></p>
                        
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

<!-- Clases Grupales Ultra Modernas -->
<?php if (!empty($upcomingClasses)): ?>
<section class="section ultra-modern-classes-section" id="clases-grupales">
    <div class="classes-background-ultra">
        <div class="gradient-overlay-classes"></div>
        <div class="floating-elements-classes">
            <div class="float-element float-dumbbell">
                <i class="fas fa-dumbbell"></i>
            </div>
            <div class="float-element float-heart">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div class="float-element float-star">
                <i class="fas fa-star"></i>
            </div>
            <div class="float-element float-fire">
                <i class="fas fa-fire"></i>
            </div>
        </div>
        <div class="animated-waves">
            <div class="wave wave-1"></div>
            <div class="wave wave-2"></div>
            <div class="wave wave-3"></div>
        </div>
    </div>
    
    <div class="container">
        <!-- Header Ultra Moderno -->
        <div class="ultra-header" data-aos="zoom-in">
            <div class="header-badge-ultra">
                <i class="fas fa-users"></i>
                <span>EXPERIENCIA PREMIUM</span>
                <div class="badge-glow"></div>
            </div>
            
            <h2 class="ultra-title">
                <span class="title-line-1">CLASES</span>
                <span class="title-line-2 gradient-text-ultra">GRUPALES</span>
                <div class="title-underline"></div>
            </h2>
            
            <p class="ultra-subtitle">
                Entrena con otros y mantente motivado en nuestras sedes
            </p>
            
            <div class="quick-stats">
                <div class="stat-item">
                    <span class="stat-number" data-count="50">0</span>
                    <span class="stat-label">Clases por Semana</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="2">0</span>
                    <span class="stat-label">Sedes Disponibles</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="1200">0</span>
                    <span class="stat-label">Miembros Activos</span>
                </div>
            </div>
        </div>
        
        <!-- Grid de Clases Ultra Moderno -->
        <div class="ultra-classes-grid">
            <?php foreach ($upcomingClasses as $index => $class): ?>
                <div class="ultra-class-card" 
                     data-aos="fade-up" 
                     data-aos-delay="<?php echo $index * 100; ?>"
                     data-class-id="<?php echo $class['id']; ?>">
                    
                    <!-- Header de la Tarjeta -->
                    <div class="card-header-ultra">
                        <div class="time-badge-ultra">
                            <div class="time-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="time-info">
                                <span class="day-name">
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
                                    echo $dayNames[$class['day_of_week']];
                                    ?>
                                </span>
                                <span class="time-slot"><?php echo date('H:i', strtotime($class['start_time'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="duration-badge">
                            <span><?php echo $class['duration_minutes']; ?> min</span>
                        </div>
                    </div>
                    
                    <!-- Contenido Principal -->
                    <div class="card-content-ultra">
                        <div class="class-icon-ultra">
                            <?php
                            // Iconos dinámicos según el tipo de clase
                            $classIcons = [
                                'HIIT' => 'fas fa-fire',
                                'CrossFit' => 'fas fa-dumbbell',
                                'Spinning' => 'fas fa-bicycle',
                                'Yoga' => 'fas fa-spa',
                                'Pilates' => 'fas fa-leaf',
                                'Cardio' => 'fas fa-heartbeat',
                                'Funcional' => 'fas fa-running'
                            ];
                            
                            $icon = 'fas fa-users'; // Icono por defecto
                            foreach ($classIcons as $type => $iconClass) {
                                if (stripos($class['name'], $type) !== false) {
                                    $icon = $iconClass;
                                    break;
                                }
                            }
                            ?>
                            <i class="<?php echo $icon; ?>"></i>
                        </div>
                        
                        <h3 class="class-title-ultra"><?php echo htmlspecialchars($class['name']); ?></h3>
                        
                        <div class="instructor-info-ultra">
                            <div class="instructor-avatar">
                                <img src="<?php echo AppHelper::asset('images/trainers/' . ($class['trainer_photo'] ?? 'default-trainer.jpg')); ?>" 
                                     alt="<?php echo htmlspecialchars($class['first_name'] . ' ' . $class['last_name']); ?>"
                                     onerror="this.src='<?php echo AppHelper::asset('images/trainers/default-trainer.jpg'); ?>'">
                                <div class="avatar-ring"></div>
                            </div>
                            <div class="instructor-details">
                                <span class="instructor-name"><?php echo htmlspecialchars($class['first_name'] . ' ' . $class['last_name']); ?></span>
                                <span class="instructor-title">Instructor Certificado</span>
                            </div>
                        </div>
                        
                        <p class="class-description-ultra">
                            <?php echo htmlspecialchars($class['description'] ?? 'Entrenamiento grupal dinámico y motivador'); ?>
                        </p>
                        
                        <!-- Información de Capacidad -->
                        <div class="capacity-section-ultra">
                            <div class="capacity-header">
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo $class['booked_spots']; ?>/<?php echo $class['max_participants']; ?> lugares</span>
                                </div>
                                <div class="availability-status">
                                    <?php 
                                    $available = $class['max_participants'] - $class['booked_spots'];
                                    $availabilityClass = $available > 5 ? 'high' : ($available > 0 ? 'medium' : 'full');
                                    ?>
                                    <span class="status-indicator status-<?php echo $availabilityClass; ?>">
                                        <?php if ($available > 0): ?>
                                            <i class="fas fa-check-circle"></i>
                                            Disponible
                                        <?php else: ?>
                                            <i class="fas fa-times-circle"></i>
                                            Completo
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Barra de Progreso Animada -->
                            <div class="progress-container-ultra">
                                <?php $fillPercentage = ($class['booked_spots'] / $class['max_participants']) * 100; ?>
                                <div class="progress-bar-ultra">
                                    <div class="progress-fill-ultra progress-<?php echo $availabilityClass; ?>" 
                                         style="width: 0%"
                                         data-width="<?php echo $fillPercentage; ?>%">
                                        <div class="progress-glow"></div>
                                    </div>
                                </div>
                                <div class="progress-text"><?php echo round($fillPercentage); ?>% ocupado</div>
                            </div>
                        </div>
                        
                        <!-- Características de la Clase -->
                        <div class="class-features-ultra">
                            <div class="feature-tag gym-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($class['gym_name'] ?? 'Sede Principal'); ?></span>
                            </div>
                            <div class="feature-tag">
                                <i class="fas fa-fire"></i>
                                <span>Alta Intensidad</span>
                            </div>
                            <div class="feature-tag">
                                <i class="fas fa-trophy"></i>
                                <span>Resultados Garantizados</span>
                            </div>
                            <div class="feature-tag">
                                <i class="fas fa-heart"></i>
                                <span>Ambiente Motivador</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer de la Tarjeta con Botón de Acción -->
                    <div class="card-footer-ultra">
                        <?php if (AppHelper::isLoggedIn()): ?>
                            <button class="btn-reserve-ultra <?php echo $available <= 0 ? 'disabled' : ''; ?>" 
                                    data-class-id="<?php echo $class['id']; ?>"
                                    <?php echo $available <= 0 ? 'disabled' : ''; ?>>
                                <div class="btn-content">
                                    <i class="fas fa-calendar-plus"></i>
                                    <span class="btn-text">
                                        <?php echo $available > 0 ? 'Reservar Clase' : 'Clase Completa'; ?>
                                    </span>
                                </div>
                                <div class="btn-glow"></div>
                                <div class="btn-ripple"></div>
                            </button>
                        <?php else: ?>
                            <a href="<?php echo AppHelper::getBaseUrl('login'); ?>" class="btn-reserve-ultra">
                                <div class="btn-content">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span class="btn-text">Inicia Sesión para Reservar</span>
                                </div>
                                <div class="btn-glow"></div>
                                <div class="btn-ripple"></div>
                            </a>
                        <?php endif; ?>
                        
                        <!-- Botón Secundario - Más Info -->
                        <button class="btn-info-ultra" data-class-id="<?php echo $class['id']; ?>">
                            <i class="fas fa-info-circle"></i>
                            <span>Más Info</span>
                        </button>
                    </div>
                    
                    <!-- Efectos Visuales -->
                    <div class="card-effects">
                        <div class="hover-glow"></div>
                        <div class="corner-decoration corner-tl"></div>
                        <div class="corner-decoration corner-tr"></div>
                        <div class="corner-decoration corner-bl"></div>
                        <div class="corner-decoration corner-br"></div>
                    </div>
                    
                    <!-- Live Indicator (si la clase está en vivo) -->
                    <?php if (date('H:i') >= date('H:i', strtotime($class['start_time'])) && 
                              date('H:i') <= date('H:i', strtotime($class['start_time'] . ' + ' . $class['duration_minutes'] . ' minutes'))): ?>
                        <div class="live-indicator">
                            <div class="live-dot"></div>
                            <span>EN VIVO</span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Sección de Beneficios de Clases Grupales -->
        <div class="benefits-section-ultra" data-aos="fade-up" data-aos-delay="400">
            <h3 class="benefits-title">¿Por qué entrenar en grupo?</h3>
            <div class="benefits-grid-ultra">
                <div class="benefit-item-ultra">
                    <div class="benefit-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Motivación Grupal</h4>
                    <p>La energía del grupo te impulsa a superar tus límites</p>
                </div>
                <div class="benefit-item-ultra">
                    <div class="benefit-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>Horarios Estructurados</h4>
                    <p>Mantén la consistencia con horarios fijos</p>
                </div>
                <div class="benefit-item-ultra">
                    <div class="benefit-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h4>Instructores Expertos</h4>
                    <p>Guía profesional en cada sesión</p>
                </div>
                <div class="benefit-item-ultra">
                    <div class="benefit-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4>Ambiente Social</h4>
                    <p>Conoce personas con tus mismos objetivos</p>
                </div>
            </div>
        </div>
        
        <!-- CTA Section -->
        <div class="ultra-cta-section" data-aos="zoom-in" data-aos-delay="500">
            <div class="cta-content-ultra">
                <h3>¿Listo para unirte a la experiencia grupal?</h3>
                <p>Descubre todas nuestras clases y encuentra la que mejor se adapte a ti</p>
                <div class="cta-buttons-ultra">
                    <a href="<?php echo AppHelper::getBaseUrl('classes'); ?>" class="btn-primary-ultra">
                        <i class="fas fa-calendar"></i>
                        <span>Ver Todas las Clases</span>
                        <div class="btn-shine"></div>
                    </a>
                    <a href="<?php echo AppHelper::getBaseUrl('contact'); ?>" class="btn-secondary-ultra">
                        <i class="fas fa-phone"></i>
                        <span>Solicitar Información</span>
                    </a>
                </div>
                
                <!-- Mini estadísticas -->
                <div class="mini-stats-ultra">
                    <div class="mini-stat">
                        <span class="number">98%</span>
                        <span class="label">Satisfacción</span>
                    </div>
                    <div class="mini-stat">
                        <span class="number">500+</span>
                        <span class="label">Clases al mes</span>
                    </div>
                    <div class="mini-stat">
                        <span class="number">24/7</span>
                        <span class="label">Soporte</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Información de Clase -->
    <div class="class-info-modal" id="classInfoModal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalClassName">Información de la Clase</h3>
                <button class="modal-close" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Contenido dinámico de la clase -->
            </div>
        </div>
    </div>
</section>

<!-- Estilos CSS para la sección ultra moderna -->
<style>
/* =============================================
   CLASES GRUPALES ULTRA MODERNAS
   ============================================= */

.ultra-modern-classes-section {
    background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 30%, #415a77 70%, #778da9 100%);
    position: relative;
    overflow: hidden;
    padding: 120px 0;
    min-height: 100vh;
    border-top: 5px solid #415a77;
    border-bottom: 5px solid #415a77;
}

.classes-background-ultra {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.gradient-overlay-classes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        linear-gradient(45deg, rgba(65, 90, 119, 0.3) 0%, transparent 50%, rgba(119, 141, 169, 0.2) 100%),
        radial-gradient(circle at 30% 40%, rgba(27, 38, 59, 0.4) 0%, transparent 60%),
        radial-gradient(circle at 70% 60%, rgba(65, 90, 119, 0.3) 0%, transparent 60%);
    animation: classesOverlayShift 12s ease-in-out infinite alternate;
}

@keyframes classesOverlayShift {
    0% { transform: translateX(-25px) translateY(-20px); }
    100% { transform: translateX(25px) translateY(20px); }
}

.floating-elements-classes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2;
}

.float-element {
    position: absolute;
    font-size: 2rem;
    color: rgba(255, 107, 0, 0.2);
    animation: floatAround 15s infinite ease-in-out;
}

.float-dumbbell {
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.float-heart {
    top: 30%;
    right: 15%;
    animation-delay: 3s;
}

.float-star {
    bottom: 30%;
    left: 20%;
    animation-delay: 6s;
}

.float-fire {
    bottom: 20%;
    right: 10%;
    animation-delay: 9s;
}

.animated-waves {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 200px;
}

.wave {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 200%;
    height: 100px;
    background: linear-gradient(90deg, transparent, rgba(255, 107, 0, 0.1), transparent);
    animation: waveMove 20s infinite linear;
}

.wave-1 {
    animation-delay: 0s;
    opacity: 0.3;
}

.wave-2 {
    animation-delay: -7s;
    opacity: 0.2;
}

.wave-3 {
    animation-delay: -14s;
    opacity: 0.1;
}

/* Header Ultra Moderno */
.ultra-header {
    text-align: center;
    margin-bottom: 4rem;
    position: relative;
    z-index: 5;
}

.header-badge-ultra {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 107, 0, 0.15);
    border: 2px solid rgba(255, 107, 0, 0.3);
    padding: 1rem 2rem;
    border-radius: 50px;
    color: #FFB366;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.badge-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    animation: slideGlow 3s infinite;
}

.ultra-title {
    margin-bottom: 1.5rem;
    position: relative;
}

.title-line-1 {
    display: block;
    font-size: 2rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 3px;
}

.title-line-2 {
    display: block;
    font-size: 4rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 4px;
    margin-bottom: 1rem;
}

.gradient-text-ultra {
    background: linear-gradient(135deg, #FF6B00 0%, #FFB366 25%, #FF6B00 50%, #FFD700 75%, #FF6B00 100%);
    background-size: 400% 400%;
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientFlow 4s ease-in-out infinite;
}

.title-underline {
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #FF6B00, #FFB366);
    margin: 0 auto;
    border-radius: 2px;
    position: relative;
    overflow: hidden;
}

.title-underline::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
    animation: underlineShine 2s infinite;
}

.ultra-subtitle {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

.quick-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 900;
    color: #FF6B00;
    margin-bottom: 0.5rem;
    font-family: 'Courier New', monospace;
}

.stat-label {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Grid de Clases Ultra Moderno */
.ultra-classes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
    position: relative;
    z-index: 5;
}

.ultra-class-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 107, 0, 0.2);
    border-radius: 25px;
    padding: 0;
    overflow: hidden;
    position: relative;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    backdrop-filter: blur(15px);
    cursor: pointer;
}

.ultra-class-card:hover {
    transform: translateY(-10px) scale(1.02);
    border-color: rgba(255, 107, 0, 0.5);
    box-shadow: 0 20px 60px rgba(255, 107, 0, 0.3);
}

.card-header-ultra {
    background: linear-gradient(135deg, rgba(255, 107, 0, 0.9), rgba(255, 179, 102, 0.9));
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.card-header-ultra::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: headerGlow 4s ease-in-out infinite;
}

.time-badge-ultra {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
    position: relative;
    z-index: 2;
}

.time-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    backdrop-filter: blur(10px);
}

.time-info {
    display: flex;
    flex-direction: column;
}

.day-name {
    font-size: 1.1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.time-slot {
    font-size: 1.5rem;
    font-weight: 900;
    font-family: 'Courier New', monospace;
}

.duration-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    color: white;
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 2;
}

.card-content-ultra {
    padding: 2rem;
    position: relative;
}

.class-icon-ultra {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #FF6B00, #FFB366);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin: 0 auto 1.5rem auto;
    box-shadow: 0 10px 30px rgba(255, 107, 0, 0.4);
    position: relative;
    overflow: hidden;
}

.class-icon-ultra::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    animation: iconSpin 3s infinite;
}

.class-title-ultra {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    text-align: center;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.instructor-info-ultra {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    border: 1px solid rgba(255, 107, 0, 0.1);
}

.instructor-avatar {
    position: relative;
    width: 50px;
    height: 50px;
}

.instructor-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #FF6B00;
}

.avatar-ring {
    position: absolute;
    top: -3px;
    left: -3px;
    width: calc(100% + 6px);
    height: calc(100% + 6px);
    border: 2px solid rgba(255, 107, 0, 0.5);
    border-radius: 50%;
    animation: ringPulse 2s infinite;
}

.instructor-details {
    flex: 1;
}

.instructor-name {
    display: block;
    color: white;
    font-weight: 600;
    font-size: 1rem;
}

.instructor-title {
    display: block;
    color: #FF6B00;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.class-description-ultra {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin-bottom: 1.5rem;
    text-align: center;
}

.capacity-section-ultra {
    margin-bottom: 1.5rem;
}

.capacity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.capacity-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
}

.capacity-info i {
    color: #FF6B00;
}

.availability-status {
    font-size: 0.9rem;
    font-weight: 600;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-high {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.status-medium {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.status-full {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.3);
}

.progress-container-ultra {
    position: relative;
}

.progress-bar-ultra {
    width: 100%;
    height: 8px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill-ultra {
    height: 100%;
    border-radius: 10px;
    position: relative;
    transition: width 2s ease-in-out;
}

.progress-high {
    background: linear-gradient(90deg, #28a745, #20c997);
}

.progress-medium {
    background: linear-gradient(90deg, #ffc107, #fd7e14);
}

.progress-full {
    background: linear-gradient(90deg, #dc3545, #e83e8c);
}

.progress-glow {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: progressGlow 2s infinite;
}

.progress-text {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    text-align: center;
}

.class-features-ultra {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.feature-tag {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 107, 0, 0.1);
    border: 1px solid rgba(255, 107, 0, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(5px);
    transition: all 0.3s ease;
}

.feature-tag:hover {
    background: rgba(255, 107, 0, 0.2);
    border-color: rgba(255, 107, 0, 0.4);
    transform: translateY(-2px);
}

.feature-tag i {
    color: #FF6B00;
    font-size: 0.9rem;
}

.gym-location {
    background: rgba(255, 107, 0, 0.25);
    border: 1px solid rgba(255, 107, 0, 0.5);
    font-weight: 600;
}

.gym-location i {
    color: #FF6B00;
}

.card-footer-ultra {
    padding: 1.5rem 2rem 2rem;
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-reserve-ultra {
    flex: 1;
    background: linear-gradient(135deg, #FF6B00, #FFB366);
    border: none;
    padding: 1rem 1.5rem;
    border-radius: 15px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-reserve-ultra:hover:not(.disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255, 107, 0, 0.4);
}

.btn-reserve-ultra.disabled {
    background: #6c757d;
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-content {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    z-index: 2;
}

.btn-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
}

.btn-reserve-ultra:hover .btn-glow {
    left: 100%;
}

.btn-ripple {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.btn-reserve-ultra:active .btn-ripple {
    width: 300px;
    height: 300px;
}

.btn-info-ultra {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1rem;
    border-radius: 15px;
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-info-ultra:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 107, 0, 0.3);
    color: #FF6B00;
    transform: translateY(-2px);
}

.card-effects {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.hover-glow {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at center, rgba(255, 107, 0, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
    border-radius: 25px;
}

.ultra-class-card:hover .hover-glow {
    opacity: 1;
}

.corner-decoration {
    position: absolute;
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 107, 0, 0.3);
    transition: all 0.3s ease;
}

.corner-tl {
    top: 15px;
    left: 15px;
    border-right: none;
    border-bottom: none;
}

.corner-tr {
    top: 15px;
    right: 15px;
    border-left: none;
    border-bottom: none;
}

.corner-bl {
    bottom: 15px;
    left: 15px;
    border-right: none;
    border-top: none;
}

.corner-br {
    bottom: 15px;
    right: 15px;
    border-left: none;
    border-top: none;
}

.ultra-class-card:hover .corner-decoration {
    border-color: rgba(255, 107, 0, 0.8);
    transform: scale(1.2);
}

.live-indicator {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    backdrop-filter: blur(10px);
    z-index: 10;
}

.live-dot {
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
    animation: livePulse 1s infinite;
}

/* Sección de Beneficios */
.benefits-section-ultra {
    margin-bottom: 3rem;
    text-align: center;
    position: relative;
    z-index: 5;
}

.benefits-title {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 2rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.benefits-grid-ultra {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.benefit-item-ultra {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 107, 0, 0.2);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.benefit-item-ultra:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 107, 0, 0.4);
    box-shadow: 0 15px 40px rgba(255, 107, 0, 0.2);
}

.benefit-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #FF6B00, #FFB366);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
    margin: 0 auto 1rem auto;
    box-shadow: 0 10px 30px rgba(255, 107, 0, 0.3);
}

.benefit-item-ultra h4 {
    color: white;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.benefit-item-ultra p {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.5;
}

/* CTA Section */
.ultra-cta-section {
    text-align: center;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 107, 0, 0.2);
    border-radius: 25px;
    padding: 3rem 2rem;
    backdrop-filter: blur(15px);
    position: relative;
    z-index: 5;
    overflow: hidden;
}

.ultra-cta-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 107, 0, 0.1) 0%, transparent 70%);
    animation: ctaGlow 6s ease-in-out infinite;
}

.cta-content-ultra {
    position: relative;
    z-index: 2;
}

.cta-content-ultra h3 {
    font-size: 2.2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.cta-content-ultra p {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

.cta-buttons-ultra {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.btn-primary-ultra {
    background: linear-gradient(135deg, #FF6B00, #FFB366);
    color: white;
    padding: 1.2rem 2.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 10px 30px rgba(255, 107, 0, 0.3);
}

.btn-primary-ultra:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 15px 40px rgba(255, 107, 0, 0.4);
    color: white;
    text-decoration: none;
}

.btn-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
}

.btn-primary-ultra:hover .btn-shine {
    left: 100%;
}

.btn-secondary-ultra {
    background: transparent;
    color: #FF6B00;
    border: 2px solid #FF6B00;
    padding: 1.2rem 2.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-secondary-ultra:hover {
    background: #FF6B00;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 107, 0, 0.3);
    text-decoration: none;
}

.mini-stats-ultra {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
}

.mini-stat {
    text-align: center;
}

.mini-stat .number {
    display: block;
    font-size: 2rem;
    font-weight: 900;
    color: #FF6B00;
    margin-bottom: 0.25rem;
    font-family: 'Courier New', monospace;
}

.mini-stat .label {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Modal */
.class-info-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    display: none;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    backdrop-filter: blur(15px);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.modal-header h3 {
    color: #333;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #666;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: rgba(0, 0, 0, 0.1);
    color: #333;
}

.modal-body {
    padding: 2rem;
}

/* Animaciones */
@keyframes floatAround {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.2;
    }
    25% {
        transform: translateY(-20px) rotate(90deg);
        opacity: 0.4;
    }
    50% {
        transform: translateY(-10px) rotate(180deg);
        opacity: 0.3;
    }
    75% {
        transform: translateY(-30px) rotate(270deg);
        opacity: 0.4;
    }
}

@keyframes waveMove {
    0% {
        transform: translateX(-50%);
    }
    100% {
        transform: translateX(0%);
    }
}

@keyframes slideGlow {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

@keyframes gradientFlow {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes underlineShine {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

@keyframes headerGlow {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.1;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 0.2;
    }
}

@keyframes iconSpin {
    0% {
        transform: translate(-50%, -50%) rotate(0deg);
    }
    100% {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

@keyframes ringPulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.7;
    }
}

@keyframes progressGlow {
    0%, 100% {
        transform: translateX(-100%);
    }
    50% {
        transform: translateX(100%);
    }
}

@keyframes livePulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(0.8);
    }
}

@keyframes ctaGlow {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.1;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
        opacity: 0.2;
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .ultra-classes-grid {
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
    }
    
    .quick-stats {
        gap: 2rem;
    }
    
    .mini-stats-ultra {
        gap: 2rem;
    }
}

@media (max-width: 768px) {
    .ultra-modern-classes-section {
        padding: 4rem 0;
    }
    
    .ultra-classes-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .title-line-1 {
        font-size: 1.5rem;
    }
    
    .title-line-2 {
        font-size: 2.5rem;
    }
    
    .quick-stats {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .benefits-grid-ultra {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .cta-buttons-ultra {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    
    .btn-primary-ultra,
    .btn-secondary-ultra {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
    
    .mini-stats-ultra {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .card-footer-ultra {
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem 1rem 1.5rem;
    }
    
    .btn-reserve-ultra {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .btn-info-ultra {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.75rem 1rem;
        background-color: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        color: white;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        margin-top: 0.5rem;
    }
    
    .btn-info-ultra:hover {
        background-color: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
    }
    
    /* Estilos para botones ya definidos en .btn-info-ultra y .btn-reserve-ultra */
}

@media (max-width: 480px) {
    .ultra-header {
        margin-bottom: 3rem;
    }
    
    .header-badge-ultra {
        padding: 0.75rem 1.5rem;
        font-size: 0.8rem;
    }
    
    .title-line-2 {
        font-size: 2rem;
    }
    
    .ultra-subtitle {
        font-size: 1rem;
    }
    
    .card-content-ultra {
        padding: 1.5rem;
    }
    
    .class-icon-ultra {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .class-title-ultra {
        font-size: 1.3rem;
    }
    
    .instructor-info-ultra {
        padding: 0.75rem;
    }
    
    .instructor-avatar {
        width: 40px;
        height: 40px;
    }
    
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
    
    .modal-header {
        padding: 1rem 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
}
</style>

<!-- JavaScript para funcionalidad interactiva -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animación de contadores
    const counters = document.querySelectorAll('[data-count]');
    
    const animateCounter = (counter) => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, 16);
    };
    
    // Intersection Observer para animaciones
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (entry.target.hasAttribute('data-count')) {
                    animateCounter(entry.target);
                }
                
                // Animar barras de progreso
                const progressBars = entry.target.querySelectorAll('.progress-fill-ultra');
                progressBars.forEach(bar => {
                    const width = bar.getAttribute('data-width');
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 500);
                });
            }
        });
    });
    
    // Observar elementos
    counters.forEach(counter => observer.observe(counter));
    document.querySelectorAll('.ultra-class-card').forEach(card => observer.observe(card));
    
    // Efectos de hover para las tarjetas
    document.querySelectorAll('.ultra-class-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Modal de información
    const modal = document.getElementById('classInfoModal');
    const modalBody = document.getElementById('modalBody');
    const modalClassName = document.getElementById('modalClassName');
    const closeModal = document.getElementById('closeModal');
    
    // Botones de más información
    document.querySelectorAll('.btn-info-ultra').forEach(btn => {
        btn.addEventListener('click', function() {
            const classId = this.getAttribute('data-class-id');
            const classCard = this.closest('.ultra-class-card');
            const className = classCard.querySelector('.class-title-ultra').textContent;
            const description = classCard.querySelector('.class-description-ultra').textContent;
            const instructor = classCard.querySelector('.instructor-name').textContent;
            const time = classCard.querySelector('.time-slot').textContent;
            const day = classCard.querySelector('.day-name').textContent;
            
            modalClassName.textContent = className;
            modalBody.innerHTML = `
                <div class="modal-class-info">
                    <div class="modal-time-info">
                        <h4><i class="fas fa-calendar"></i> Horario</h4>
                        <p>${day} a las ${time}</p>
                    </div>
                    <div class="modal-instructor-info">
                        <h4><i class="fas fa-user"></i> Instructor</h4>
                        <p>${instructor}</p>
                    </div>
                    <div class="modal-description">
                        <h4><i class="fas fa-info-circle"></i> Descripción</h4>
                        <p>${description}</p>
                    </div>
                    <div class="modal-benefits">
                        <h4><i class="fas fa-star"></i> Beneficios</h4>
                        <ul>
                            <li>Mejora tu resistencia cardiovascular</li>
                            <li>Fortalece todo tu cuerpo</li>
                            <li>Quema calorías de forma eficiente</li>
                            <li>Entrena en un ambiente motivador</li>
                        </ul>
                    </div>
                </div>
            `;
            
            modal.style.display = 'block';
            setTimeout(() => {
                modal.style.opacity = '1';
            }, 10);
        });
    });
    
    // Cerrar modal
    closeModal.addEventListener('click', function() {
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    });
    
    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('modal-overlay')) {
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    });
    
    // Botones de reserva
    document.querySelectorAll('.btn-reserve-ultra').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;
            
            // Efecto de ripple
            const ripple = this.querySelector('.btn-ripple');
            ripple.style.width = '300px';
            ripple.style.height = '300px';
            
            setTimeout(() => {
                ripple.style.width = '0';
                ripple.style.height = '0';
            }, 600);
            
            // Obtener el ID de la clase desde el atributo data-class-id
            const classId = this.getAttribute('data-class-id');
            if (!classId) return;
            
            const originalText = this.querySelector('.btn-text').textContent;
            const originalIcon = this.querySelector('i').className;
            
            this.querySelector('i').className = 'fas fa-spinner fa-spin';
            this.querySelector('.btn-text').textContent = 'Reservando...';
            this.disabled = true;
            
            // Realizar la reserva mediante AJAX
            const formData = new FormData();
            formData.append('schedule_id', classId);
            formData.append('booking_date', new Date().toISOString().split('T')[0]); // Fecha actual
            
            fetch('/classes/book', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reserva exitosa
                    this.querySelector('i').className = 'fas fa-check';
                    this.querySelector('.btn-text').textContent = '¡Reservado!';
                    this.style.background = '#28a745';
                    
                    // Actualizar contador de lugares
                    const capacityInfo = this.closest('.class-card-ultra').querySelector('.capacity-info span');
                    if (capacityInfo) {
                        const parts = capacityInfo.textContent.split('/');
                        if (parts.length === 2) {
                            const booked = parseInt(parts[0]) + 1;
                            const max = parseInt(parts[1]);
                            capacityInfo.textContent = `${booked}/${max} lugares`;
                            
                            // Actualizar barra de progreso
                            const progressFill = this.closest('.class-card-ultra').querySelector('.progress-fill-ultra');
                            if (progressFill) {
                                const fillPercentage = (booked / max) * 100;
                                progressFill.style.width = `${fillPercentage}%`;
                            }
                        }
                    }
                    
                    // Si la clase está llena, deshabilitar el botón
                    const parts = capacityInfo ? capacityInfo.textContent.split('/') : [];
                    if (parts.length === 2 && parseInt(parts[0]) >= parseInt(parts[1])) {
                        this.classList.add('disabled');
                        this.setAttribute('disabled', 'disabled');
                    }
                } else {
                    // Error en la reserva
                    this.querySelector('i').className = 'fas fa-times';
                    this.querySelector('.btn-text').textContent = 'Error: ' + (data.error || 'Intenta de nuevo');
                    this.style.background = '#dc3545';
                }
                
                // Restaurar el botón después de un tiempo
                setTimeout(() => {
                    if (!data.success) {
                        this.querySelector('i').className = originalIcon;
                        this.querySelector('.btn-text').textContent = originalText;
                        this.style.background = '';
                        this.disabled = false;
                    }
                }, 2000);
            })
            .catch(error => {
                console.error('Error al reservar:', error);
                this.querySelector('i').className = 'fas fa-times';
                this.querySelector('.btn-text').textContent = 'Error: Intenta de nuevo';
                this.style.background = '#dc3545';
                
                setTimeout(() => {
                    this.querySelector('i').className = originalIcon;
                    this.querySelector('.btn-text').textContent = originalText;
                    this.style.background = '';
                    this.disabled = false;
                }, 2000);
            });
        });
        });
    });
    
    // Efectos de partículas flotantes
    const createFloatingParticle = () => {
        const particle = document.createElement('div');
        particle.className = 'floating-particle';
        particle.innerHTML = '<i class="fas fa-circle"></i>';
        particle.style.cssText = `
            position: absolute;
            left: ${Math.random() * 100}%;
            top: 100%;
            color: rgba(255, 107, 0, 0.3);
            font-size: ${Math.random() * 10 + 5}px;
            animation: floatUp ${Math.random() * 3 + 2}s linear forwards;
            pointer-events: none;
            z-index: 1;
        `;
        
        document.querySelector('.classes-background-to ultra').appendChild(particle);
        
        setTimeout(() => {
            particle.remove();
        }, 5000);
    };
    
    // Crear partículas periódicamente
    setInterval(createFloatingParticle, 2000);
    
    // Estilo para la animación de partículas flotantes
    if (!document.querySelector('#floatUpKeyframes')) {
        const style = document.createElement('style');
        style.id = 'floatUpKeyframes';
        style.textContent = `
            @keyframes floatUp {
                0% {
                    transform: translateY(0) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

</script>
<?php endif; ?>

<?php if (!empty($testimonials)): ?>
<!-- SECCIÓN DE TESTIMONIOS ULTRA MODERNA -->
<section class="section testimonials-ultra-section" id="testimonios">
    <div class="testimonials-background-ultra">
        <!-- Fondo animado con grid -->
        <div class="background-grid"></div>
        
        <!-- Elementos flotantes -->
        <div class="floating-testimonial-elements">
            <div class="testimonial-float-element">
                <i class="fas fa-quote-left"></i>
            </div>
            <div class="testimonial-float-element">
                <i class="fas fa-star"></i>
            </div>
            <div class="testimonial-float-element">
                <i class="fas fa-heart"></i>
            </div>
            <div class="testimonial-float-element">
                <i class="fas fa-trophy"></i>
            </div>
        </div>
        
        <!-- Partículas interactivas -->
        <div class="testimonials-particles" id="testimonials-particles"></div>
    </div>
    
    <div class="container">
        <!-- Header ultra moderno -->
        <div class="testimonials-header-ultra">
            <div class="testimonials-badge-ultra">
                <i class="fas fa-quote-left"></i>
                <span>TESTIMONIOS REALES</span>
                <div class="badge-shine"></div>
            </div>
            
            <h2 class="testimonials-title-ultra">
                <span class="title-line-testimonials title-line-1-testimonials">LO QUE DICEN</span>
                <span class="title-line-testimonials title-line-2-testimonials">NUESTROS CLIENTES</span>
            </h2>
            
            <p class="testimonials-subtitle-ultra">
                Historias reales de transformación y éxito que inspiran
            </p>
            
            <!-- Estadísticas mini -->
            <div class="testimonials-stats-ultra">
                <div class="testimonials-stat-item">
                    <span class="stat-number-ultra" data-count="98">0</span>
                    <span class="stat-label-ultra">% Satisfacción</span>
                </div>
                <div class="testimonials-stat-item">
                    <span class="stat-number-ultra" data-count="1200">0</span>
                    <span class="stat-label-ultra">Clientes Felices</span>
                </div>
                <div class="testimonials-stat-item">
                    <span class="stat-number-ultra" data-count="5">0</span>
                    <span class="stat-label-ultra">Estrellas Promedio</span>
                </div>
            </div>
        </div>
        
        <!-- Grid de testimonios modernos -->
        <div class="testimonials-grid-ultra">
            <?php foreach ($testimonials as $index => $testimonial): ?>
                <div class="testimonial-card-ultra" data-aos="fade-up" data-aos-delay="<?php echo $index * 150; ?>">
                    <div class="testimonial-card-inner">
                        <!-- Header de la tarjeta -->
                        <div class="testimonial-header-ultra">
                            <div class="testimonial-avatar-section-ultra">
                                <div class="avatar-container-ultra">
                                    <div class="avatar-glow-ring"></div>
                                    <img src="<?php echo AppHelper::asset('images/testimonials/' . ($testimonial['image'] ?? 'default.jpg')); ?>" 
                                         alt="<?php echo htmlspecialchars($testimonial['name']); ?>" 
                                         class="testimonial-avatar-ultra"
                                         onerror="this.src='<?php echo AppHelper::asset('images/testimonials/default.jpg'); ?>'">
                                    <div class="avatar-status-dot"></div>
                                </div>
                                
                                <div class="testimonial-verified-ultra">
                                    <i class="fas fa-check-circle"></i>
                                    <span>CLIENTE VERIFICADO</span>
                                </div>
                            </div>
                            
                            <!-- Rating animado -->
                            <div class="testimonial-rating-ultra">
                                <div class="stars-container">
                                    <?php 
                                    $rating = $testimonial['rating'] ?? 5;
                                    for ($i = 1; $i <= 5; $i++): 
                                    ?>
                                        <i class="fas fa-star star-ultra <?php echo $i <= $rating ? 'star-filled' : ''; ?>" 
                                           style="animation-delay: <?php echo $i * 0.1; ?>s"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-number-ultra"><?php echo number_format($rating, 1); ?></span>
                            </div>
                        </div>
                        
                        <!-- Contenido del testimonio -->
                        <div class="testimonial-content-ultra">
                            <div class="quote-mark-open">"</div>
                            <blockquote class="testimonial-text-ultra">
                                <?php echo htmlspecialchars($testimonial['text'] ?? 'Las rutinas personalizadas de STYLOFITNESS me ayudaron a alcanzar mis objetivos en tiempo récord. ¡Increíble transformación!'); ?>
                            </blockquote>
                            <div class="quote-mark-close">"</div>
                        </div>
                        
                        <!-- Footer con información del cliente -->
                        <div class="testimonial-footer-ultra">
                            <div class="client-info-ultra">
                                <div class="client-name-ultra"><?php echo htmlspecialchars($testimonial['name'] ?? 'Cliente Satisfecho'); ?></div>
                                <div class="client-role-ultra"><?php echo htmlspecialchars($testimonial['role'] ?? 'Miembro Premium'); ?></div>
                                <div class="client-location-ultra">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Lima, Perú</span>
                                </div>
                            </div>
                            
                            <div class="testimonial-date-ultra">
                                <i class="fas fa-calendar-alt"></i>
                                <span><?php echo date('M Y', strtotime('-' . rand(1, 12) . ' months')); ?></span>
                            </div>
                        </div>
                        
                        <!-- Métricas de transformación -->
                        <div class="transformation-metrics-ultra">
                            <div class="metric-item-ultra">
                                <div class="metric-icon-ultra">
                                    <i class="fas fa-weight"></i>
                                </div>
                                <div class="metric-data-ultra">
                                    <span class="metric-number"><?php echo rand(5, 25); ?>kg</span>
                                    <span class="metric-label">Perdidos</span>
                                </div>
                            </div>
                            
                            <div class="metric-item-ultra">
                                <div class="metric-icon-ultra">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="metric-data-ultra">
                                    <span class="metric-number"><?php echo rand(3, 18); ?></span>
                                    <span class="metric-label">Meses</span>
                                </div>
                            </div>
                            
                            <div class="metric-item-ultra">
                                <div class="metric-icon-ultra">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="metric-data-ultra">
                                    <span class="metric-number"><?php echo rand(15, 45); ?>%</span>
                                    <span class="metric-label">Mejora</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Efectos visuales -->
                        <div class="card-glow-effect-ultra"></div>
                        <div class="card-border-animation"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- CTA de testimonios -->
        <div class="testimonials-cta-ultra">
            <div class="cta-content-testimonials">
                <div class="cta-icon-ultra">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="cta-title-ultra">¿Quieres ser el próximo en compartir tu historia de éxito?</h3>
                <p class="cta-subtitle-ultra">Únete a miles de personas que ya transformaron su vida con StyloFitness</p>
                
                <div class="cta-buttons-testimonials">
                    <a href="<?php echo AppHelper::getBaseUrl('register'); ?>" class="btn-cta-primary-ultra">
                        <span class="btn-text">Comenzar Mi Transformación</span>
                        <i class="fas fa-arrow-right"></i>
                        <div class="btn-glow-ultra"></div>
                    </a>
                    
                    <a href="<?php echo AppHelper::getBaseUrl('testimonials'); ?>" class="btn-cta-secondary-ultra">
                        <span class="btn-text">Ver Más Testimonios</span>
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
                
                <!-- Garantías -->
                <div class="testimonials-guarantees">
                    <div class="guarantee-item-ultra">
                        <i class="fas fa-shield-check"></i>
                        <span>Garantía de Resultados</span>
                    </div>
                    <div class="guarantee-item-ultra">
                        <i class="fas fa-headset"></i>
                        <span>Soporte 24/7</span>
                    </div>
                    <div class="guarantee-item-ultra">
                        <i class="fas fa-medal"></i>
                        <span>Instructores Certificados</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CSS ULTRA MODERNO PARA TESTIMONIOS -->
<style>
/* ================================================
   SECCIÓN DE TESTIMONIOS ULTRA MODERNA
   ================================================ */

.testimonials-ultra-section {
    background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 25%, #16213e 50%, #1a1a2e 75%, #0a0a1a 100%);
    padding: 8rem 0;
    position: relative;
    overflow: hidden;
    min-height: 100vh;
}

/* Fondo animado */
.testimonials-background-ultra {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
}

.background-grid {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        linear-gradient(rgba(255, 107, 0, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 107, 0, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
    animation: gridMove 20s linear infinite;
}

.floating-testimonial-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.testimonial-float-element {
    position: absolute;
    font-size: 3rem;
    color: rgba(255, 107, 0, 0.1);
    animation: testimonialFloat 15s infinite ease-in-out;
    z-index: 2;
}

.testimonial-float-element:nth-child(1) {
    top: 10%;
    left: 5%;
    animation-delay: 0s;
}

.testimonial-float-element:nth-child(2) {
    top: 20%;
    right: 10%;
    animation-delay: 3s;
}

.testimonial-float-element:nth-child(3) {
    bottom: 30%;
    left: 15%;
    animation-delay: 6s;
}

.testimonial-float-element:nth-child(4) {
    bottom: 15%;
    right: 5%;
    animation-delay: 9s;
}

/* Header ultra moderno */
.testimonials-header-ultra {
    text-align: center;
    margin-bottom: 4rem;
    position: relative;
    z-index: 10;
}

.testimonials-badge-ultra {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255, 107, 0, 0.1);
    border: 2px solid rgba(255, 107, 0, 0.3);
    padding: 1rem 2.5rem;
    border-radius: 50px;
    color: #FFB366;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 2rem;
    backdrop-filter: blur(15px);
    position: relative;
    overflow: hidden;
    animation: badgePulse 3s infinite;
}

.testimonials-badge-ultra::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    animation: badgeShine 3s infinite;
}

.testimonials-title-ultra {
    font-size: 3.5rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 3px;
    margin-bottom: 1.5rem;
    line-height: 1.1;
}

.title-line-testimonials {
    display: block;
    margin-bottom: 0.5rem;
}

.title-line-1-testimonials {
    color: rgba(255, 255, 255, 0.8);
    font-size: 2rem;
    font-weight: 600;
}

.title-line-2-testimonials {
    background: linear-gradient(135deg, #FF6B00 0%, #FFB366 25%, #FFD700 50%, #FF6B00 75%, #FFB366 100%);
    background-size: 400% 400%;
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: ultraGradientFlow 4s ease-in-out infinite;
    font-size: 4rem;
}

.testimonials-subtitle-ultra {
    font-size: 1.3rem;
    color: rgba(255, 255, 255, 0.7);
    max-width: 600px;
    margin: 0 auto 2rem auto;
    line-height: 1.6;
}

.testimonials-stats-ultra {
    display: flex;
    justify-content: center;
    gap: 4rem;
    margin-top: 2rem;
}

.testimonials-stat-item {
    text-align: center;
}

.stat-number-ultra {
    display: block;
    font-size: 3rem;
    font-weight: 900;
    color: #FF6B00;
    font-family: 'Courier New', monospace;
    margin-bottom: 0.5rem;
    text-shadow: 0 0 20px rgba(255, 107, 0, 0.5);
}

.stat-label-ultra {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Grid de testimonios */
.testimonials-grid-ultra {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 2.5rem;
    margin-bottom: 4rem;
    position: relative;
    z-index: 10;
}

/* Tarjetas de testimonios ultra modernas */
.testimonial-card-ultra {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 107, 0, 0.2);
    border-radius: 30px;
    padding: 0;
    overflow: hidden;
    position: relative;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    backdrop-filter: blur(20px);
    cursor: pointer;
    transform-style: preserve-3d;
}

.testimonial-card-ultra:hover {
    transform: translateY(-15px) rotateX(2deg) rotateY(2deg);
    border-color: rgba(255, 107, 0, 0.6);
    box-shadow: 
        0 25px 80px rgba(255, 107, 0, 0.3),
        0 0 50px rgba(255, 107, 0, 0.2);
}

.testimonial-card-inner {
    position: relative;
    z-index: 2;
    height: 100%;
}

/* Header de tarjeta */
.testimonial-header-ultra {
    background: linear-gradient(135deg, rgba(255, 107, 0, 0.9), rgba(255, 179, 102, 0.8));
    padding: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.testimonial-header-ultra::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: headerPulse 4s ease-in-out infinite;
}

.testimonial-avatar-section-ultra {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    position: relative;
    z-index: 2;
}

.avatar-container-ultra {
    position: relative;
    width: 70px;
    height: 70px;
}

.avatar-glow-ring {
    position: absolute;
    top: -5px;
    left: -5px;
    width: calc(100% + 10px);
    height: calc(100% + 10px);
    border: 3px solid rgba(255, 255, 255, 0.5);
    border-radius: 50%;
    animation: avatarRing 3s infinite;
}

.testimonial-avatar-ultra {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.avatar-status-dot {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 20px;
    height: 20px;
    background: #28a745;
    border: 3px solid white;
    border-radius: 50%;
    animation: statusPulse 2s infinite;
}

.testimonial-verified-ultra {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    color: white;
    font-size: 0.8rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.testimonial-verified-ultra i {
    color: #28a745;
    font-size: 1rem;
}

/* Rating animado */
.testimonial-rating-ultra {
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    z-index: 2;
}

.stars-container {
    display: flex;
    gap: 0.25rem;
}

.star-ultra {
    font-size: 1.5rem;
    color: rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    animation: starFloat 2s ease-in-out infinite;
}

.star-ultra.star-filled {
    color: #FFD700;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
}

.rating-number-ultra {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 15px;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    backdrop-filter: blur(10px);
}

/* Contenido del testimonio */
.testimonial-content-ultra {
    padding: 2.5rem;
    position: relative;
    text-align: center;
}

.quote-mark-open,
.quote-mark-close {
    font-size: 4rem;
    color: rgba(255, 107, 0, 0.3);
    font-weight: 900;
    line-height: 1;
    position: absolute;
}

.quote-mark-open {
    top: 1rem;
    left: 1.5rem;
}

.quote-mark-close {
    bottom: 1rem;
    right: 1.5rem;
}

.testimonial-text-ultra {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.7;
    font-style: italic;
    margin: 2rem 0;
    position: relative;
    z-index: 2;
}

/* Footer de testimonio */
.testimonial-footer-ultra {
    padding: 1.5rem 2.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255, 255, 255, 0.05);
    border-top: 1px solid rgba(255, 107, 0, 0.1);
}

.client-info-ultra {
    text-align: left;
}

.client-name-ultra {
    font-size: 1.2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.5rem;
}

.client-role-ultra {
    font-size: 0.9rem;
    color: #FF6B00;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.25rem;
}

.client-location-ultra {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.testimonial-date-ultra {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.6);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Métricas de transformación */
.transformation-metrics-ultra {
    padding: 1.5rem 2.5rem 2.5rem;
    display: flex;
    justify-content: space-around;
    gap: 1rem;
}

.metric-item-ultra {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: rgba(255, 107, 0, 0.1);
    padding: 1rem;
    border-radius: 15px;
    border: 1px solid rgba(255, 107, 0, 0.2);
    transition: all 0.3s ease;
}

.metric-item-ultra:hover {
    background: rgba(255, 107, 0, 0.2);
    transform: translateY(-2px);
}

.metric-icon-ultra {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #FF6B00, #FFB366);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.metric-data-ultra {
    text-align: center;
}

.metric-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 900;
    color: #FF6B00;
    margin-bottom: 0.25rem;
}

.metric-label {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Efectos visuales */
.card-glow-effect-ultra {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at center, rgba(255, 107, 0, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
    border-radius: 30px;
    pointer-events: none;
}

.testimonial-card-ultra:hover .card-glow-effect-ultra {
    opacity: 1;
}

.card-border-animation {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 2px solid transparent;
    border-radius: 30px;
    background: linear-gradient(45deg, transparent, rgba(255, 107, 0, 0.5), transparent);
    background-size: 400% 400%;
    animation: borderFlow 3s ease-in-out infinite;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.testimonial-card-ultra:hover .card-border-animation {
    opacity: 1;
}

/* CTA de testimonios */
.testimonials-cta-ultra {
    text-align: center;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 107, 0, 0.2);
    border-radius: 30px;
    padding: 4rem 3rem;
    backdrop-filter: blur(20px);
    position: relative;
    z-index: 10;
    overflow: hidden;
}

.testimonials-cta-ultra::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 107, 0, 0.1) 0%, transparent 70%);
    animation: ctaPulse 6s ease-in-out infinite;
}

.cta-content-testimonials {
    position: relative;
    z-index: 2;
}

.cta-icon-ultra {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #FF6B00, #FFB366);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    margin: 0 auto 2rem auto;
    box-shadow: 0 15px 40px rgba(255, 107, 0, 0.4);
    animation: iconBounce 3s ease-in-out infinite;
}

.cta-title-ultra {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.cta-subtitle-ultra {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 3rem;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

.cta-buttons-testimonials {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.btn-cta-primary-ultra {
    background: linear-gradient(135deg, #FF6B00, #FFB366);
    color: white;
    padding: 1.5rem 3rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 15px 40px rgba(255, 107, 0, 0.3);
}

.btn-cta-primary-ultra:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 20px 60px rgba(255, 107, 0, 0.4);
    color: white;
    text-decoration: none;
}

.btn-glow-ultra {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
}

.btn-cta-primary-ultra:hover .btn-glow-ultra {
    left: 100%;
}

.btn-cta-secondary-ultra {
    background: transparent;
    color: #FF6B00;
    border: 2px solid #FF6B00;
    padding: 1.5rem 3rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-cta-secondary-ultra:hover {
    background: #FF6B00;
    color: white;
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(255, 107, 0, 0.3);
    text-decoration: none;
}

/* Garantías */
.testimonials-guarantees {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.guarantee-item-ultra {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.guarantee-item-ultra i {
    color: #FF6B00;
    font-size: 1.2rem;
}

/* Animaciones */
@keyframes gridMove {
    0% {
        transform: translate(0, 0);
    }
    100% {
        transform: translate(50px, 50px);
    }
}

@keyframes testimonialFloat {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.1;
    }
    25% {
        transform: translateY(-30px) rotate(90deg);
        opacity: 0.3;
    }
    50% {
        transform: translateY(-15px) rotate(180deg);
        opacity: 0.2;
    }
    75% {
        transform: translateY(-45px) rotate(270deg);
        opacity: 0.3;
    }
}

@keyframes badgePulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 20px rgba(255, 107, 0, 0.3);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 40px rgba(255, 107, 0, 0.5);
    }
}

@keyframes badgeShine {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

@keyframes ultraGradientFlow {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes headerPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.1;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 0.2;
    }
}

@keyframes avatarRing {
    0%, 100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
    50% {
        transform: scale(1.1) rotate(180deg);
        opacity: 0.7;
    }
}

@keyframes statusPulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.8;
    }
}

@keyframes starFloat {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-5px);
    }
}

@keyframes borderFlow {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes ctaPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.1;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
        opacity: 0.2;
    }
}

@keyframes iconBounce {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .testimonials-grid-ultra {
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 2rem;
    }
    
    .testimonials-stats-ultra {
        gap: 2.5rem;
    }
    
    .transformation-metrics-ultra {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    
    .metric-item-ultra {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .testimonials-ultra-section {
        padding: 5rem 0;
    }
    
    .testimonials-grid-ultra {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .testimonials-title-ultra {
        font-size: 2.5rem;
    }
    
    .title-line-1-testimonials {
        font-size: 1.5rem;
    }
    
    .title-line-2-testimonials {
        font-size: 2.5rem;
    }
    
    .testimonials-stats-ultra {
        flex-direction: column;
        gap: 2rem;
    }
    
    .testimonials-guarantees {
        flex-direction: column;
        gap: 1.5rem;
        align-items: center;
    }
    
    .cta-buttons-testimonials {
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
    }
    
    .btn-cta-primary-ultra,
    .btn-cta-secondary-ultra {
        width: 100%;
        max-width: 350px;
        justify-content: center;
    }
    
    .testimonial-header-ultra {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
    }
    
    .testimonial-footer-ultra {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .testimonials-badge-ultra {
        padding: 0.75rem 1.5rem;
        font-size: 0.8rem;
    }
    
    .testimonials-title-ultra {
        font-size: 2rem;
    }
    
    .title-line-2-testimonials {
        font-size: 2rem;
    }
    
    .testimonials-subtitle-ultra {
        font-size: 1.1rem;
    }
    
    .testimonial-content-ultra {
        padding: 2rem 1.5rem;
    }
    
    .testimonial-text-ultra {
        font-size: 1.1rem;
    }
    
    .quote-mark-open,
    .quote-mark-close {
        font-size: 3rem;
    }
    
    .quote-mark-open {
        top: 0.5rem;
        left: 1rem;
    }
    
    .quote-mark-close {
        bottom: 0.5rem;
        right: 1rem;
    }
    
    .testimonials-cta-ultra {
        padding: 3rem 2rem;
    }
    
    .cta-title-ultra {
        font-size: 2rem;
    }
    
    .cta-subtitle-ultra {
        font-size: 1.1rem;
    }
    
    .btn-cta-primary-ultra,
    .btn-cta-secondary-ultra {
        padding: 1.25rem 2rem;
        font-size: 1rem;
    }
    
    .avatar-container-ultra {
        width: 60px;
        height: 60px;
    }
    
    .stat-number-ultra {
        font-size: 2.5rem;
    }
}

/* Partículas interactivas */
.testimonials-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 3;
}

.particle-testimonial {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(255, 107, 0, 0.6);
    border-radius: 50%;
    animation: particleMove 10s linear infinite;
}

@keyframes particleMove {
    0% {
        transform: translateY(100vh) translateX(0) scale(0);
        opacity: 0;
    }
    10% {
        opacity: 1;
        transform: scale(1);
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-100vh) translateX(50px) scale(0);
        opacity: 0;
    }
}

/* Efectos de hover adicionales */
.testimonial-card-ultra:hover .testimonial-avatar-ultra {
    transform: scale(1.1);
    box-shadow: 0 15px 40px rgba(255, 107, 0, 0.4);
}

.testimonial-card-ultra:hover .star-ultra.star-filled {
    transform: scale(1.1);
    color: #FFD700;
    text-shadow: 0 0 15px rgba(255, 215, 0, 0.8);
}

.testimonial-card-ultra:hover .metric-icon-ultra {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 5px 20px rgba(255, 107, 0, 0.4);
}

/* Scroll reveal effects */
.testimonial-card-ultra {
    opacity: 0;
    transform: translateY(50px);
    transition: all 0.6s ease;
}

.testimonial-card-ultra.aos-animate {
    opacity: 1;
    transform: translateY(0);
}

/* =============================================
   SECCIÓN POR QUÉ ELEGIRNOS
   ============================================= */

.why-choose-us-section {
    padding: 120px 0;
    background: linear-gradient(135deg, #2c1810 0%, #8b4513 30%, #d2691e 70%, #ff8c00 100%);
    position: relative;
    overflow: hidden;
    border-top: 5px solid #ff6b00;
    border-bottom: 5px solid #ff6b00;
}

.why-choose-us-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 30%, rgba(255, 140, 0, 0.3) 0%, transparent 40%),
        radial-gradient(circle at 80% 70%, rgba(255, 165, 0, 0.2) 0%, transparent 50%),
        linear-gradient(45deg, transparent 30%, rgba(255, 107, 0, 0.1) 50%, transparent 70%);
    pointer-events: none;
    animation: backgroundShift 8s ease-in-out infinite alternate;
}

@keyframes backgroundShift {
    0% { transform: translateX(-20px) translateY(-10px); }
    100% { transform: translateX(20px) translateY(10px); }
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.feature-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 107, 0, 0.2);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.4s ease;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 107, 0, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    border-color: rgba(255, 107, 0, 0.5);
    box-shadow: 0 20px 40px rgba(255, 107, 0, 0.2);
}

.feature-card:hover::before {
    opacity: 1;
}

.feature-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #ff6b00, #ffb366);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: white;
    position: relative;
    z-index: 2;
}

.feature-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    position: relative;
    z-index: 2;
}

.feature-description {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    position: relative;
    z-index: 2;
}

/* =============================================
   SECCIÓN PRODUCTOS DESTACADOS
   ============================================= */

.featured-products-section-enhanced {
    padding: 120px 0;
    background: linear-gradient(135deg, #1a0033 0%, #330066 30%, #4d0099 70%, #6600cc 100%);
    position: relative;
    overflow: hidden;
    border-top: 5px solid #6600cc;
    border-bottom: 5px solid #6600cc;
}

.featured-products-section-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 25% 25%, rgba(102, 0, 204, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 75% 75%, rgba(153, 51, 255, 0.2) 0%, transparent 50%),
        linear-gradient(45deg, transparent 40%, rgba(102, 0, 204, 0.1) 60%, transparent 80%);
    pointer-events: none;
    animation: featuredBackgroundShift 10s ease-in-out infinite alternate;
}

@keyframes featuredBackgroundShift {
    0% { transform: translateX(-30px) translateY(-15px) rotate(0deg); }
    100% { transform: translateX(30px) translateY(15px) rotate(2deg); }
}

.featured-products-section-enhanced .section-header-enhanced {
    text-align: center;
    margin-bottom: 4rem;
    position: relative;
    z-index: 2;
}

.featured-products-section-enhanced .section-title {
    color: #ffffff;
    font-size: 3rem;
    font-weight: 800;
    text-shadow: 0 0 20px rgba(102, 0, 204, 0.5);
    margin-bottom: 1rem;
}

.featured-products-section-enhanced .section-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.2rem;
    max-width: 600px;
    margin: 0 auto;
}

/* =============================================
   SECCIÓN OFERTAS ESPECIALES
   ============================================= */

.special-offers-section {
    padding: 100px 0;
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a3e 50%, #0f0f23 100%);
    position: relative;
}

.offers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.offer-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 107, 0, 0.2);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.4s ease;
    backdrop-filter: blur(10px);
    position: relative;
}

.offer-card:hover {
    transform: translateY(-10px) scale(1.02);
    border-color: rgba(255, 107, 0, 0.5);
    box-shadow: 0 25px 50px rgba(255, 107, 0, 0.3);
}

.offer-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.offer-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.offer-card:hover .offer-image img {
    transform: scale(1.1);
}

.discount-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(135deg, #ff6b00, #ffb366);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);
}

.offer-content {
    padding: 2rem;
}

.offer-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
}

.offer-description {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.offer-pricing {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.original-price {
    color: rgba(255, 255, 255, 0.5);
    text-decoration: line-through;
    font-size: 1.1rem;
}

.discounted-price {
    color: #ff6b00;
    font-size: 1.5rem;
    font-weight: 700;
}

.offer-validity {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.btn-offer {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #ff6b00, #ffb366);
    color: white;
    padding: 1rem 2rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-offer:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(255, 107, 0, 0.4);
    text-decoration: none;
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .features-grid,
    .offers-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .feature-card,
    .offer-card {
        padding: 1.5rem;
    }
    
    .why-choose-us-section,
    .special-offers-section {
        padding: 60px 0;
    }
}

</style>

<!-- JavaScript para interactividad -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador animado para estadísticas
    const animateCounters = () => {
        const counters = document.querySelectorAll('[data-count]');
        
        const animateCounter = (counter) => {
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current);
            }, 16);
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && entry.target.hasAttribute('data-count')) {
                    animateCounter(entry.target);
                }
            });
        });
        
        counters.forEach(counter => observer.observe(counter));
    };
    
    // Crear partículas interactivas
    const createParticles = () => {
        const particlesContainer = document.getElementById('testimonials-particles');
        if (!particlesContainer) return;
        
        const createParticle = () => {
            const particle = document.createElement('div');
            particle.className = 'particle-testimonial';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 2 + 's';
            particle.style.animationDuration = (Math.random() * 5 + 5) + 's';
            
            particlesContainer.appendChild(particle);
            
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.parentNode.removeChild(particle);
                }
            }, 10000);
        };
        
        // Crear partículas periódicamente
        setInterval(createParticle, 1000);
    };
    
    // Efectos de hover avanzados para las tarjetas
    const addCardEffects = () => {
        const cards = document.querySelectorAll('.testimonial-card-ultra');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                // Efecto de rotación suave
                this.style.transform = 'translateY(-15px) rotateX(5deg) rotateY(5deg)';
                
                // Animar las estrellas
                const stars = this.querySelectorAll('.star-ultra.star-filled');
                stars.forEach((star, index) => {
                    setTimeout(() => {
                        star.style.transform = 'scale(1.2) rotate(10deg)';
                        star.style.textShadow = '0 0 20px rgba(255, 215, 0, 1)';
                    }, index * 100);
                });
                
                // Efecto de pulso en el avatar
                const avatar = this.querySelector('.testimonial-avatar-ultra');
                if (avatar) {
                    avatar.style.transform = 'scale(1.1)';
                    avatar.style.boxShadow = '0 20px 50px rgba(255, 107, 0, 0.5)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) rotateX(0) rotateY(0)';
                
                // Restaurar estrellas
                const stars = this.querySelectorAll('.star-ultra.star-filled');
                stars.forEach(star => {
                    star.style.transform = 'scale(1) rotate(0deg)';
                    star.style.textShadow = '0 0 10px rgba(255, 215, 0, 0.5)';
                });
                
                // Restaurar avatar
                const avatar = this.querySelector('.testimonial-avatar-ultra');
                if (avatar) {
                    avatar.style.transform = 'scale(1)';
                    avatar.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.3)';
                }
            });
            
            // Efecto de clic (like)
            card.addEventListener('click', function() {
                // Crear corazón flotante
                const heart = document.createElement('div');
                heart.innerHTML = '<i class="fas fa-heart"></i>';
                heart.style.cssText = `
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    color: #ff4757;
                    font-size: 2rem;
                    pointer-events: none;
                    animation: heartFloat 1s ease-out forwards;
                    z-index: 1000;
                `;
                
                this.style.position = 'relative';
                this.appendChild(heart);
                
                setTimeout(() => {
                    if (heart.parentNode) {
                        heart.parentNode.removeChild(heart);
                    }
                }, 1000);
                
                // Efecto de "like" en la tarjeta
                this.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            });
        });
    };
    
    // Animación de aparición de elementos
    const initScrollAnimations = () => {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('aos-animate');
                }
            });
        }, observerOptions);
        
        const animatedElements = document.querySelectorAll('.testimonial-card-ultra');
        animatedElements.forEach(el => observer.observe(el));
    };
    
    // Efecto parallax suave para elementos flotantes
    const addParallaxEffect = () => {
        const floatingElements = document.querySelectorAll('.testimonial-float-element');
        
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            floatingElements.forEach((element, index) => {
                const speed = (index + 1) * 0.1;
                element.style.transform = `translateY(${rate * speed}px) rotate(${scrolled * speed * 0.1}deg)`;
            });
        });
    };
    
    // Inicializar todo
    animateCounters();
    createParticles();
    addCardEffects();
    initScrollAnimations();
    addParallaxEffect();
    
    // Agregar estilos para animaciones dinámicas
    if (!document.querySelector('#dynamic-animations')) {
        const style = document.createElement('style');
        style.id = 'dynamic-animations';
        style.textContent = `
            @keyframes heartFloat {
                0% {
                    transform: translate(-50%, -50%) scale(0);
                    opacity: 1;
                }
                50% {
                    transform: translate(-50%, -80px) scale(1.2);
                    opacity: 1;
                }
                100% {
                    transform: translate(-50%, -120px) scale(0);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    console.log('✨ Testimonios ultra modernos inicializados correctamente');
});
</script>
<?php endif; ?>

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