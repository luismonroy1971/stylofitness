<?php use StyleFitness\Helpers\AppHelper; ?>
<!-- Hero Section Premium de Tienda -->
<section class="store-hero-premium">
    <div class="hero-background">
        <div class="hero-particles"></div>
        <div class="hero-gradient"></div>
    </div>
    
    <div class="container">
        <div class="hero-content-premium" data-aos="fade-up">
            <div class="hero-badge">
                <i class="fas fa-crown"></i>
                <span>Premium Store</span>
            </div>
            
            <h1 class="hero-title-premium">
                <span class="title-main">STYLOFITNESS</span>
                <span class="title-sub">STORE</span>
            </h1>
            
            <p class="hero-subtitle-premium">
                Suplementos deportivos de élite y accesorios premium para atletas profesionales
            </p>
            
            <!-- Buscador Premium -->
            <div class="search-section-premium">
                <form action="<?php echo AppHelper::baseUrl('store/search'); ?>" method="GET" class="search-form-premium">
                    <div class="search-container">
                        <div class="search-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" 
                               name="q" 
                               placeholder="Buscar productos premium..." 
                               value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
                               class="search-input-premium">
                        <button type="submit" class="search-btn-premium">
                            <span>Buscar</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    
                    <!-- Filtros rápidos -->
                    <div class="quick-filters-premium">
                        <button type="button" class="filter-tag <?php echo isset($_GET['featured']) ? 'active' : ''; ?>" data-filter="featured">
                            <i class="fas fa-star"></i> Destacados
                        </button>
                        <button type="button" class="filter-tag <?php echo isset($_GET['on_sale']) ? 'active' : ''; ?>" data-filter="on_sale">
                            <i class="fas fa-fire"></i> En Oferta
                        </button>
                        <button type="button" class="filter-tag <?php echo isset($_GET['new']) ? 'active' : ''; ?>" data-filter="new">
                            <i class="fas fa-sparkles"></i> Nuevos
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Trust Badges Premium -->
            <div class="trust-badges-premium">
                <div class="trust-badge-premium" data-aos="fade-up" data-aos-delay="100">
                    <div class="badge-icon">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <div class="badge-content">
                        <span class="badge-title">100% Originales</span>
                        <span class="badge-desc">Productos certificados</span>
                    </div>
                </div>
                
                <div class="trust-badge-premium" data-aos="fade-up" data-aos-delay="200">
                    <div class="badge-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="badge-content">
                        <span class="badge-title">Envío Express</span>
                        <span class="badge-desc">Gratis desde S/150</span>
                    </div>
                </div>
                
                <div class="trust-badge-premium" data-aos="fade-up" data-aos-delay="300">
                    <div class="badge-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="badge-content">
                        <span class="badge-title">Calidad Premium</span>
                        <span class="badge-desc">Garantía total</span>
                    </div>
                </div>
                
                <div class="trust-badge-premium" data-aos="fade-up" data-aos-delay="400">
                    <div class="badge-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="badge-content">
                        <span class="badge-title">Soporte 24/7</span>
                        <span class="badge-desc">Atención experta</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categorías Premium -->
<section class="categories-premium-section">
    <div class="container">
        <div class="section-header-premium" data-aos="fade-up">
            <h2 class="section-title-premium">
                <span class="title-icon"><i class="fas fa-th-large"></i></span>
                <span class="title-text">Categorías Premium</span>
                <span class="title-accent"></span>
            </h2>
            <p class="section-subtitle-premium">Explora nuestra selección de productos de élite</p>
        </div>
        
        <div class="categories-grid-premium">
            <?php foreach ($categories as $index => $category): ?>
                <?php 
                $categorySlug = $category['slug'];
                $hasImage = false;
                $imagePath = '';
                
                // Mapeo de slugs a nombres de archivos de imagen
                $imageMapping = [
                    'proteinas' => 'proteins.jpg',
                    'pre-entrenos' => 'oxido.jpg',
                    'vitaminas-minerales' => 'vitaminas.jpg',
                    'creatina' => 'creatinas-glutaminas.jpg',
                    'quemadores-grasa' => 'quemadores.jpg',
                    'aminoacidos' => 'bcca-amino.jpg',
                    'accesorios-entrenamiento' => 'protectores.jpg',
                    'ropa-deportiva' => 'gainers.jpg'
                ];
                
                // Buscar imagen usando el mapeo
                if (isset($imageMapping[$categorySlug])) {
                    $uploadImagePath = 'uploads/images/categories/' . $imageMapping[$categorySlug];
                    $uploadFullPath = __DIR__ . '/../../../public/' . $uploadImagePath;
                    if (file_exists($uploadFullPath)) {
                        $hasImage = true;
                        $imagePath = AppHelper::baseUrl($uploadImagePath);
                    }
                }
                
                // Fallback a image_url de la base de datos
                if (!$hasImage && !empty($category['image_url'])) {
                    $hasImage = true;
                    $imagePath = AppHelper::uploadUrl($category['image_url']);
                }
                ?>
                
                <div class="category-card-premium <?php echo ($categoryId == $category['id']) ? 'active' : ''; ?>" 
                     data-aos="fade-up" 
                     data-aos-delay="<?php echo $index * 100; ?>">
                    <a href="<?php echo AppHelper::baseUrl('store/category/' . $category['slug']); ?>" class="category-link">
                        <div class="category-image-container">
                            <?php if ($hasImage): ?>
                                <img src="<?php echo $imagePath; ?>" 
                                     alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                     class="category-image-premium">
                            <?php else: ?>
                                <div class="category-placeholder">
                                    <i class="fas fa-box"></i>
                                </div>
                            <?php endif; ?>
                            <div class="category-overlay-premium"></div>
                            <div class="category-hover-effect"></div>
                        </div>
                        
                        <div class="category-content-premium">
                            <h3 class="category-name-premium"><?php echo htmlspecialchars($category['name']); ?></h3>
                            <div class="category-stats">
                                <span class="product-count-premium">
                                    <i class="fas fa-cube"></i>
                                    <?php echo $category['product_count']; ?> productos
                                </span>
                            </div>
                            <div class="category-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Filtros Avanzados -->
<section class="filters-premium-section">
    <div class="container">
        <div class="filters-container-premium">
            <form method="GET" class="filters-form-premium" id="filters-form">
                <input type="hidden" name="category" value="<?php echo $categoryId ?? ''; ?>">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                
                <div class="filters-row">
                    <!-- Ordenamiento -->
                    <div class="filter-group-premium">
                        <label class="filter-label-premium">
                            <i class="fas fa-sort"></i>
                            Ordenar por
                        </label>
                        <select name="sort" class="filter-select-premium" onchange="this.form.submit()">
                            <option value="name" <?php echo ($_GET['sort'] ?? '') === 'name' ? 'selected' : ''; ?>>Nombre A-Z</option>
                            <option value="price" <?php echo ($_GET['sort'] ?? '') === 'price' ? 'selected' : ''; ?>>Precio menor</option>
                            <option value="created_at" <?php echo ($_GET['sort'] ?? '') === 'created_at' ? 'selected' : ''; ?>>Más recientes</option>
                            <option value="popularity" <?php echo ($_GET['sort'] ?? '') === 'popularity' ? 'selected' : ''; ?>>Más populares</option>
                        </select>
                    </div>
                    
                    <!-- Rango de precios -->
                    <div class="filter-group-premium">
                        <label class="filter-label-premium">
                            <i class="fas fa-dollar-sign"></i>
                            Rango de precio
                        </label>
                        <div class="price-range-premium">
                            <input type="number" name="price_min" placeholder="Mín" 
                                   value="<?php echo $_GET['price_min'] ?? ''; ?>" class="price-input-premium">
                            <span class="price-separator">-</span>
                            <input type="number" name="price_max" placeholder="Máx" 
                                   value="<?php echo $_GET['price_max'] ?? ''; ?>" class="price-input-premium">
                            <button type="submit" class="price-filter-btn-premium">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Vista toggle -->
                    <div class="filter-group-premium">
                        <label class="filter-label-premium">
                            <i class="fas fa-eye"></i>
                            Vista
                        </label>
                        <div class="view-toggle-premium">
                            <button type="button" class="view-btn-premium grid-view active" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="view-btn-premium list-view" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros rápidos -->
                <div class="quick-filters-row">
                    <label class="quick-filter-premium">
                        <input type="checkbox" name="featured" value="1" 
                               <?php echo isset($_GET['featured']) ? 'checked' : ''; ?> 
                               onchange="this.form.submit()">
                        <span class="checkmark-premium"></span>
                        <span class="filter-text">Destacados</span>
                    </label>
                    
                    <label class="quick-filter-premium">
                        <input type="checkbox" name="on_sale" value="1" 
                               <?php echo isset($_GET['on_sale']) ? 'checked' : ''; ?> 
                               onchange="this.form.submit()">
                        <span class="checkmark-premium"></span>
                        <span class="filter-text">En oferta</span>
                    </label>
                    
                    <label class="quick-filter-premium">
                        <input type="checkbox" name="in_stock" value="1" 
                               <?php echo isset($_GET['in_stock']) ? 'checked' : ''; ?> 
                               onchange="this.form.submit()">
                        <span class="checkmark-premium"></span>
                        <span class="filter-text">En stock</span>
                    </label>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Productos Destacados -->
<?php if (empty($_GET['search']) && empty($categoryId) && !empty($featuredProducts)): ?>
<section class="featured-products-premium">
    <div class="container">
        <div class="section-header-premium" data-aos="fade-up">
            <h2 class="section-title-premium">
                <span class="title-icon"><i class="fas fa-star"></i></span>
                <span class="title-text">Productos Destacados</span>
                <span class="title-accent"></span>
            </h2>
            <p class="section-subtitle-premium">Los suplementos más populares entre atletas profesionales</p>
        </div>
        
        <div class="featured-carousel-premium">
            <div class="carousel-container-premium">
                <?php foreach ($featuredProducts as $index => $product): ?>
                    <div class="featured-slide" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <?php include 'product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="carousel-controls-premium">
                <button class="carousel-btn-premium carousel-prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn-premium carousel-next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Lista de Productos Premium -->
<section class="products-section-premium">
    <div class="container">
        <!-- Resultados Header -->
        <div class="results-header-premium">
            <div class="results-info-premium">
                <span class="results-count-premium">
                    <strong><?php echo count($products); ?></strong> de <strong><?php echo $pagination['total_items']; ?></strong> productos
                </span>
                <?php if (!empty($_GET['search'])): ?>
                    <span class="search-query-premium">
                        para "<strong><?php echo htmlspecialchars($_GET['search']); ?></strong>"
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Grid de productos -->
        <?php if (!empty($products)): ?>
            <div class="products-grid-premium" id="products-grid">
                <?php foreach ($products as $index => $product): ?>
                    <div class="product-item-premium" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                        <?php include 'product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Paginación Premium -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="pagination-wrapper-premium">
                    <nav class="pagination-premium" aria-label="Navegación de productos">
                        <?php if ($pagination['has_previous']): ?>
                            <a href="<?php echo $this->buildPaginationUrl($pagination['previous_page']); ?>" 
                               class="pagination-btn-premium prev-btn">
                                <i class="fas fa-chevron-left"></i>
                                <span>Anterior</span>
                            </a>
                        <?php endif; ?>
                        
                        <div class="pagination-numbers-premium">
                            <?php
                            $start = max(1, $pagination['current_page'] - 2);
                            $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            
                            if ($start > 1): ?>
                                <a href="<?php echo $this->buildPaginationUrl(1); ?>" class="pagination-number-premium">1</a>
                                <?php if ($start > 2): ?>
                                    <span class="pagination-ellipsis-premium">...</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <a href="<?php echo $this->buildPaginationUrl($i); ?>" 
                                   class="pagination-number-premium <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($end < $pagination['total_pages']): ?>
                                <?php if ($end < $pagination['total_pages'] - 1): ?>
                                    <span class="pagination-ellipsis-premium">...</span>
                                <?php endif; ?>
                                <a href="<?php echo $this->buildPaginationUrl($pagination['total_pages']); ?>" 
                                   class="pagination-number-premium"><?php echo $pagination['total_pages']; ?></a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($pagination['has_next']): ?>
                            <a href="<?php echo $this->buildPaginationUrl($pagination['next_page']); ?>" 
                               class="pagination-btn-premium next-btn">
                                <span>Siguiente</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                    
                    <div class="pagination-info-premium">
                        Página <?php echo $pagination['current_page']; ?> de <?php echo $pagination['total_pages']; ?>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- Estado vacío Premium -->
            <div class="empty-state-premium">
                <div class="empty-animation">
                    <div class="empty-icon-premium">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="empty-particles"></div>
                </div>
                
                <h3 class="empty-title-premium">No se encontraron productos</h3>
                <p class="empty-description-premium">
                    <?php if (!empty($_GET['search'])): ?>
                        No hay productos que coincidan con tu búsqueda "<?php echo htmlspecialchars($_GET['search']); ?>".
                    <?php else: ?>
                        No hay productos disponibles en esta categoría.
                    <?php endif; ?>
                </p>
                
                <div class="empty-actions-premium">
                    <a href="<?php echo AppHelper::baseUrl('store'); ?>" class="btn-primary-premium">
                        <i class="fas fa-store"></i>
                        <span>Ver todos los productos</span>
                    </a>
                    <?php if (!empty($_GET['search'])): ?>
                        <button class="btn-secondary-premium" onclick="document.querySelector('input[name=q]').value=''; document.querySelector('.search-form-premium').submit();">
                            <i class="fas fa-times"></i>
                            <span>Limpiar búsqueda</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Newsletter Premium -->
<section class="newsletter-premium-section">
    <div class="newsletter-background">
        <div class="newsletter-particles"></div>
    </div>
    
    <div class="container">
        <div class="newsletter-content-premium">
            <div class="newsletter-text-premium" data-aos="fade-right">
                <h2 class="newsletter-title-premium">
                    <span class="title-highlight">¡Únete al club</span>
                    <span class="title-main">STYLOFITNESS!</span>
                </h2>
                <p class="newsletter-description-premium">
                    Recibe ofertas exclusivas, nuevos productos y consejos de expertos directo en tu email.
                </p>
                
                <ul class="newsletter-benefits-premium">
                    <li><i class="fas fa-check-circle"></i> Descuentos exclusivos hasta 30%</li>
                    <li><i class="fas fa-check-circle"></i> Acceso anticipado a lanzamientos</li>
                    <li><i class="fas fa-check-circle"></i> Consejos personalizados de nutrición</li>
                    <li><i class="fas fa-check-circle"></i> Contenido premium sin spam</li>
                </ul>
            </div>
            
            <div class="newsletter-form-premium" data-aos="fade-left">
                <form class="newsletter-form-container">
                    <div class="form-group-premium">
                        <input type="email" placeholder="Tu email" class="newsletter-input-premium" required>
                        <button type="submit" class="newsletter-btn-premium">
                            <span>Suscribirse</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <p class="newsletter-privacy">
                        <i class="fas fa-shield-alt"></i>
                        Respetamos tu privacidad. Sin spam garantizado.
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
// JavaScript para efectos premium
document.addEventListener('DOMContentLoaded', function() {
    // Efectos de partículas
    createParticles();
    
    // Filtros interactivos
    initPremiumFilters();
    
    // Carousel de productos destacados
    initFeaturedCarousel();
    
    // Efectos de hover en categorías
    initCategoryEffects();
});

function createParticles() {
    const containers = document.querySelectorAll('.hero-particles, .newsletter-particles');
    containers.forEach(container => {
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 2 + 's';
            particle.style.animationDuration = (Math.random() * 3 + 2) + 's';
            container.appendChild(particle);
        }
    });
}

function initPremiumFilters() {
    const filterTags = document.querySelectorAll('.filter-tag');
    filterTags.forEach(tag => {
        tag.addEventListener('click', function() {
            this.classList.toggle('active');
            // Aquí puedes agregar lógica para aplicar filtros
        });
    });
    
    const viewBtns = document.querySelectorAll('.view-btn-premium');
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            const grid = document.getElementById('products-grid');
            if (grid) {
                grid.className = view === 'list' ? 'products-list-premium' : 'products-grid-premium';
            }
        });
    });
}

function initFeaturedCarousel() {
    const carousel = document.querySelector('.carousel-container-premium');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    
    if (!carousel) return;
    
    let currentSlide = 0;
    const slides = carousel.querySelectorAll('.featured-slide');
    const totalSlides = slides.length;
    
    function updateCarousel() {
        const translateX = -currentSlide * (100 / 3); // 3 slides visibles
        carousel.style.transform = `translateX(${translateX}%)`;
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentSlide = (currentSlide + 1) % (totalSlides - 2);
            updateCarousel();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentSlide = currentSlide > 0 ? currentSlide - 1 : totalSlides - 3;
            updateCarousel();
        });
    }
}

function initCategoryEffects() {
    const categoryCards = document.querySelectorAll('.category-card-premium');
    
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}
</script>