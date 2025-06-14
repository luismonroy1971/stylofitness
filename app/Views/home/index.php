<!-- Hero Section -->
<section class="hero" id="hero">
    <div class="hero-background">
        <video autoplay muted loop class="hero-video">
            <source src="<?php echo AppHelper::asset('videos/hero-bg.mp4'); ?>" type="video/mp4">
        </video>
        <div class="hero-overlay"></div>
    </div>
    
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <h1 class="hero-title animate__animated animate__fadeInUp">
                Transforma Tu Cuerpo<br>
                <span class="gradient-text">Con STYLOFITNESS</span>
            </h1>
            <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                Rutinas personalizadas, suplementos de calidad premium y el mejor seguimiento profesional. 
                Tu transformación comienza aquí.
            </p>
            
            <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-2s">
                <a href="<?php echo AppHelper::getBaseUrl('register'); ?>" class="btn-primary btn-lg">
                    <i class="fas fa-rocket"></i>
                    Comenzar Ahora
                </a>
                
                <a href="<?php echo AppHelper::getBaseUrl('store'); ?>" class="btn-secondary btn-lg">
                    <i class="fas fa-store"></i>
                    Explorar Tienda
                </a>
            </div>
            
            <!-- Estadísticas Hero -->
            <div class="hero-stats animate__animated animate__fadeInUp animate__delay-3s">
                <div class="hero-stat">
                    <div class="stat-number"><?php echo number_format($stats['active_clients'] ?? 0); ?>+</div>
                    <div class="stat-label">Clientes Activos</div>
                </div>
                <div class="hero-stat">
                    <div class="stat-number"><?php echo number_format($stats['total_routines'] ?? 0); ?>+</div>
                    <div class="stat-label">Rutinas Creadas</div>
                </div>
                <div class="hero-stat">
                    <div class="stat-number"><?php echo number_format($stats['total_products'] ?? 0); ?>+</div>
                    <div class="stat-label">Productos Premium</div>
                </div>
                <div class="hero-stat">
                    <div class="stat-number"><?php echo number_format($stats['weekly_classes'] ?? 0); ?>+</div>
                    <div class="stat-label">Clases Semanales</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="scroll-indicator animate__animated animate__bounce animate__infinite">
        <i class="fas fa-chevron-down"></i>
    </div>
</section>

<!-- Carrusel de Productos Promocionales -->
<?php if (!empty($promotionalProducts)): ?>
<section class="section promotional-carousel-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">
                <i class="fas fa-fire"></i>
                Ofertas Especiales
            </h2>
            <p class="section-subtitle">Descuentos exclusivos en nuestros productos más populares</p>
        </div>
        
        <div class="product-carousel" data-aos="fade-up" data-aos-delay="200">
            <div class="carousel-container" id="promotional-carousel">
                <?php foreach ($promotionalProducts as $index => $product): ?>
                    <div class="carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                        <div class="carousel-content">
                            <div class="product-badge">
                                <?php if ($product['discount_percentage']): ?>
                                    -<?php echo $product['discount_percentage']; ?>% OFF
                                <?php endif; ?>
                            </div>
                            
                            <div class="carousel-text">
                                <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                                <h3 class="carousel-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="carousel-description"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></p>
                                
                                <div class="carousel-price">
                                    <?php if ($product['sale_price']): ?>
                                        <span class="original-price">S/ <?php echo number_format($product['price'], 2); ?></span>
                                        <span class="sale-price">S/ <?php echo number_format($product['sale_price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="current-price">S/ <?php echo number_format($product['price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="carousel-actions">
                                    <button class="btn-primary btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-cart-plus"></i>
                                        Agregar al Carrito
                                    </button>
                                    <a href="<?php echo AppHelper::getBaseUrl('store/product/' . $product['slug']); ?>" class="btn-secondary">
                                        Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="carousel-image">
                            <?php 
                            $productImages = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
                            $mainImage = !empty($productImages) ? $productImages[0] : '/public/images/default-product.jpg';
                            ?>
                            <img src="<?php echo AppHelper::uploadUrl($mainImage); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 loading="lazy">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Controles del carrusel -->
            <div class="carousel-controls">
                <?php foreach ($promotionalProducts as $index => $product): ?>
                    <button class="carousel-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                            data-slide="<?php echo $index; ?>"></button>
                <?php endforeach; ?>
            </div>
            
            <button class="carousel-nav carousel-prev">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-nav carousel-next">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Sección de Características -->
<section class="section features-section bg-dark">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title text-white">¿Por Qué Elegir STYLOFITNESS?</h2>
            <p class="section-subtitle text-light">La experiencia fitness más completa del mercado</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card glass-effect" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <h3 class="feature-title">Rutinas Personalizadas</h3>
                <p class="feature-description">
                    Entrenamientos diseñados específicamente para tus objetivos, nivel y disponibilidad de tiempo.
                </p>
                <ul class="feature-list">
                    <li>Videos HD explicativos</li>
                    <li>Seguimiento de progreso</li>
                    <li>Ajustes automáticos</li>
                    <li>Soporte 24/7</li>
                </ul>
            </div>
            
            <div class="feature-card glass-effect" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h3 class="feature-title">Tienda Premium</h3>
                <p class="feature-description">
                    Los mejores suplementos y accesorios con integración directa a tus rutinas de entrenamiento.
                </p>
                <ul class="feature-list">
                    <li>Productos originales</li>
                    <li>Recomendaciones IA</li>
                    <li>Envío gratis +S/150</li>
                    <li>Garantía total</li>
                </ul>
            </div>
            
            <div class="feature-card glass-effect" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="feature-title">Clases Grupales</h3>
                <p class="feature-description">
                    Entrenamientos en grupo con instructores certificados en nuestras múltiples sedes.
                </p>
                <ul class="feature-list">
                    <li>Horarios flexibles</li>
                    <li>Instructores expertos</li>
                    <li>Reserva online</li>
                    <li>Ambiente motivador</li>
                </ul>
            </div>
            
            <div class="feature-card glass-effect" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Seguimiento Avanzado</h3>
                <p class="feature-description">
                    Monitoreo completo de tu progreso con métricas detalladas y análisis profesional.
                </p>
                <ul class="feature-list">
                    <li>Dashboard interactivo</li>
                    <li>Reportes detallados</li>
                    <li>Metas personalizadas</li>
                    <li>Análisis de tendencias</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Productos Destacados -->
<?php if (!empty($featuredProducts)): ?>
<section class="section featured-products-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">
                <i class="fas fa-star"></i>
                Productos Destacados
            </h2>
            <p class="section-subtitle">Los suplementos más populares entre nuestros clientes</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($featuredProducts as $index => $product): ?>
                <div class="product-card hover-lift" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="product-image">
                        <?php 
                        $productImages = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
                        $mainImage = !empty($productImages) ? $productImages[0] : '/public/images/default-product.jpg';
                        ?>
                        <img src="<?php echo AppHelper::uploadUrl($mainImage); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             loading="lazy">
                        
                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                            <?php $discount = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
                            <div class="product-badge">-<?php echo $discount; ?>%</div>
                        <?php elseif ($product['is_featured']): ?>
                            <div class="product-badge featured">⭐ Destacado</div>
                        <?php endif; ?>
                        
                        <div class="product-overlay">
                            <div class="product-actions">
                                <button class="btn-quick-view" data-product-id="<?php echo $product['id']; ?>" title="Vista Rápida">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-wishlist" data-product-id="<?php echo $product['id']; ?>" title="Agregar a Favoritos">
                                    <i class="fas fa-heart"></i>
                                </button>
                                <button class="btn-compare" data-product-id="<?php echo $product['id']; ?>" title="Comparar">
                                    <i class="fas fa-balance-scale"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></div>
                        <h3 class="product-title">
                            <a href="<?php echo AppHelper::getBaseUrl('store/product/' . $product['slug']); ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <p class="product-description"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></p>
                        
                        <div class="product-rating">
                            <?php
                            $rating = $product['avg_rating'] ?? 4.5;
                            for ($i = 1; $i <= 5; $i++):
                                if ($i <= $rating):
                                    echo '<i class="fas fa-star"></i>';
                                elseif ($i - 0.5 <= $rating):
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                else:
                                    echo '<i class="far fa-star"></i>';
                                endif;
                            endfor;
                            ?>
                            <span class="rating-count">(<?php echo $product['reviews_count'] ?? 0; ?>)</span>
                        </div>
                        
                        <div class="product-price">
                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                <span class="original-price">S/ <?php echo number_format($product['price'], 2); ?></span>
                                <span class="current-price">S/ <?php echo number_format($product['sale_price'], 2); ?></span>
                            <?php else: ?>
                                <span class="current-price">S/ <?php echo number_format($product['price'], 2); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-stock">
                            <?php if ($product['stock_quantity'] > 10): ?>
                                <span class="stock-status in-stock">✓ En Stock</span>
                            <?php elseif ($product['stock_quantity'] > 0): ?>
                                <span class="stock-status low-stock">⚠ Últimas unidades</span>
                            <?php else: ?>
                                <span class="stock-status out-of-stock">✗ Agotado</span>
                            <?php endif; ?>
                        </div>
                        
                        <button class="btn-add-cart <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>" 
                                data-product-id="<?php echo $product['id']; ?>"
                                <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-shopping-cart"></i>
                            <?php echo $product['stock_quantity'] > 0 ? 'Agregar al Carrito' : 'Agotado'; ?>
                        </button>
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
<section class="section classes-section bg-light">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">
                <i class="fas fa-users"></i>
                Clases Grupales
            </h2>
            <p class="section-subtitle">Entrena con otros y mantente motivado en nuestras sedes</p>
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
<section class="section testimonials-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">
                <i class="fas fa-quote-left"></i>
                Lo Que Dicen Nuestros Clientes
            </h2>
            <p class="section-subtitle">Historias reales de transformación y éxito</p>
        </div>
        
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $index => $testimonial): ?>
                <div class="testimonial-card glass-effect" data-aos="fade-up" data-aos-delay="<?php echo $index * 200; ?>">
                    <div class="testimonial-content">
                        <div class="testimonial-rating">
                            <?php for ($i = 1; $i <= $testimonial['rating']; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        
                        <blockquote class="testimonial-text">
                            "<?php echo htmlspecialchars($testimonial['text']); ?>"
                        </blockquote>
                        
                        <div class="testimonial-author">
                            <img src="<?php echo AppHelper::asset('images/testimonials/' . ($testimonial['image'] ?? 'default.jpg')); ?>" 
                                 alt="<?php echo htmlspecialchars($testimonial['name']); ?>" 
                                 class="author-avatar">
                            <div class="author-info">
                                <div class="author-name"><?php echo htmlspecialchars($testimonial['name']); ?></div>
                                <div class="author-role"><?php echo htmlspecialchars($testimonial['role']); ?></div>
                            </div>
                        </div>
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