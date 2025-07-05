<?php use StyleFitness\Helpers\AppHelper; ?>
<!-- Hero Section de Tienda -->
<section class="store-hero">
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <h1 class="hero-title">
                <i class="fas fa-store"></i>
                Tienda STYLOFITNESS
            </h1>
            <p class="hero-subtitle">
                Los mejores suplementos deportivos y accesorios para potenciar tu entrenamiento
            </p>
            
            <!-- Buscador de productos -->
            <div class="search-section">
                <form action="<?php echo AppHelper::baseUrl('store/search'); ?>" method="GET" class="product-search-form">
                    <div class="search-input-wrapper">
                        <input type="text" 
                               name="q" 
                               placeholder="Buscar productos..." 
                               value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
                               class="search-input">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Badges de confianza -->
            <div class="trust-badges">
                <div class="trust-badge">
                    <i class="fas fa-shield-check"></i>
                    <span>Productos Originales</span>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Envío Gratis +S/150</span>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-medal"></i>
                    <span>Mejor Calidad</span>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-headset"></i>
                    <span>Soporte 24/7</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filtros y Categorías -->
<section class="store-filters">
    <div class="container">
        <div class="filters-wrapper">
            <!-- Categorías principales -->
            <div class="categories-filter">
                <h3 class="filter-title">
                    <i class="fas fa-list"></i>
                    Categorías
                </h3>
                <div class="category-grid">
                    <?php foreach ($categories as $category): ?>
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
                            $uploadImagePath = 'public/uploads/images/categories/' . $imageMapping[$categorySlug];
                            $uploadFullPath = __DIR__ . '/../../../public/' . $uploadImagePath;
                            if (file_exists($uploadFullPath)) {
                                $hasImage = true;
                                $imagePath = AppHelper::baseUrl($uploadImagePath);
                            }
                        }
                        
                        // Si no se encuentra con el mapeo, buscar por slug directo
                        if (!$hasImage) {
                            $uploadImageExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                            foreach ($uploadImageExtensions as $ext) {
                                $uploadImagePath = 'public/uploads/images/categories/' . $categorySlug . '.' . $ext;
                                $uploadFullPath = __DIR__ . '/../../../public/' . $uploadImagePath;
                                if (file_exists($uploadFullPath)) {
                                    $hasImage = true;
                                    $imagePath = AppHelper::baseUrl($uploadImagePath);
                                    break;
                                }
                            }
                        }
                        
                        // Si no se encuentra, buscar SVG en images/categories
                        if (!$hasImage) {
                            $svgPath = 'public/images/categories/' . $categorySlug . '.svg';
                            $svgFullPath = __DIR__ . '/../../../public/' . $svgPath;
                            if (file_exists($svgFullPath)) {
                                $hasImage = true;
                                $imagePath = AppHelper::asset($svgPath);
                            }
                        }
                        
                        // Fallback a image_url de la base de datos
                        if (!$hasImage && !empty($category['image_url'])) {
                            $hasImage = true;
                            $imagePath = AppHelper::uploadUrl($category['image_url']);
                        }
                        ?>
                        <a href="<?php echo AppHelper::baseUrl('store/category/' . $category['slug']); ?>" 
                           class="category-card <?php echo ($categoryId == $category['id']) ? 'active' : ''; ?>">
                            <?php if ($hasImage): ?>
                                <img src="<?php echo $imagePath; ?>" 
                                     alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                     class="category-image">
                            <?php endif; ?>
                            <div class="category-overlay"></div>
                            
                            <div class="category-info">
                                <h4 class="category-name"><?php echo htmlspecialchars($category['name']); ?></h4>
                                <span class="product-count"><?php echo $category['product_count']; ?> productos</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Filtros adicionales -->
            <div class="additional-filters">
                <form method="GET" class="filters-form" id="filters-form">
                    <input type="hidden" name="category" value="<?php echo $categoryId ?? ''; ?>">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    
                    <!-- Ordenamiento -->
                    <div class="filter-group">
                        <label for="sort" class="filter-label">Ordenar por:</label>
                        <select name="sort" id="sort" class="filter-select" onchange="this.form.submit()">
                            <option value="name" <?php echo ($_GET['sort'] ?? '') === 'name' ? 'selected' : ''; ?>>Nombre A-Z</option>
                            <option value="price" <?php echo ($_GET['sort'] ?? '') === 'price' ? 'selected' : ''; ?>>Precio menor</option>
                            <option value="created_at" <?php echo ($_GET['sort'] ?? '') === 'created_at' ? 'selected' : ''; ?>>Más recientes</option>
                            <option value="popularity" <?php echo ($_GET['sort'] ?? '') === 'popularity' ? 'selected' : ''; ?>>Más populares</option>
                        </select>
                    </div>
                    
                    <!-- Rango de precios -->
                    <div class="filter-group">
                        <label class="filter-label">Rango de precio:</label>
                        <div class="price-range">
                            <input type="number" name="price_min" placeholder="Mín" 
                                   value="<?php echo $_GET['price_min'] ?? ''; ?>" class="price-input">
                            <span class="price-separator">-</span>
                            <input type="number" name="price_max" placeholder="Máx" 
                                   value="<?php echo $_GET['price_max'] ?? ''; ?>" class="price-input">
                            <button type="submit" class="price-filter-btn">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filtros rápidos -->
                    <div class="filter-group">
                        <div class="quick-filters">
                            <label class="quick-filter">
                                <input type="checkbox" name="featured" value="1" 
                                       <?php echo isset($_GET['featured']) ? 'checked' : ''; ?> 
                                       onchange="this.form.submit()">
                                <span class="checkmark"></span>
                                Destacados
                            </label>
                            <label class="quick-filter">
                                <input type="checkbox" name="on_sale" value="1" 
                                       <?php echo isset($_GET['on_sale']) ? 'checked' : ''; ?> 
                                       onchange="this.form.submit()">
                                <span class="checkmark"></span>
                                En oferta
                            </label>
                            <label class="quick-filter">
                                <input type="checkbox" name="in_stock" value="1" 
                                       <?php echo isset($_GET['in_stock']) ? 'checked' : ''; ?> 
                                       onchange="this.form.submit()">
                                <span class="checkmark"></span>
                                En stock
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Productos Destacados (si no hay filtros aplicados) -->
<?php if (empty($_GET['search']) && empty($categoryId) && !empty($featuredProducts)): ?>
<section class="section featured-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">
                <i class="fas fa-star"></i>
                Productos Destacados
            </h2>
            <p class="section-subtitle">Los suplementos más populares entre nuestros clientes</p>
        </div>
        
        <div class="products-carousel">
            <div class="carousel-container">
                <?php foreach ($featuredProducts as $index => $product): ?>
                    <div class="product-slide">
                        <?php include 'product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="carousel-nav">
                <button class="carousel-btn carousel-prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn carousel-next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Lista de Productos -->
<section class="products-section">
    <div class="container">
        <!-- Resultados y paginación superior -->
        <div class="results-header">
            <div class="results-info">
                <span class="results-count">
                    Mostrando <?php echo count($products); ?> de <?php echo $pagination['total_items']; ?> productos
                </span>
                <?php if (!empty($_GET['search'])): ?>
                    <span class="search-query">
                        para "<strong><?php echo htmlspecialchars($_GET['search']); ?></strong>"
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="view-toggle">
                <button class="view-btn grid-view active" data-view="grid">
                    <i class="fas fa-th"></i>
                </button>
                <button class="view-btn list-view" data-view="list">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
        
        <!-- Grid de productos -->
        <?php if (!empty($products)): ?>
            <div class="products-grid" id="products-grid">
                <?php foreach ($products as $index => $product): ?>
                    <div class="product-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <?php include 'product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Paginación -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="pagination-wrapper">
                    <nav class="pagination" aria-label="Navegación de productos">
                        <?php if ($pagination['has_previous']): ?>
                            <a href="<?php echo $this->buildPaginationUrl($pagination['previous_page']); ?>" 
                               class="pagination-btn prev-btn">
                                <i class="fas fa-chevron-left"></i>
                                Anterior
                            </a>
                        <?php endif; ?>
                        
                        <div class="pagination-numbers">
                            <?php
                            $start = max(1, $pagination['current_page'] - 2);
                            $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            
                            if ($start > 1): ?>
                                <a href="<?php echo $this->buildPaginationUrl(1); ?>" class="pagination-number">1</a>
                                <?php if ($start > 2): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <a href="<?php echo $this->buildPaginationUrl($i); ?>" 
                                   class="pagination-number <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($end < $pagination['total_pages']): ?>
                                <?php if ($end < $pagination['total_pages'] - 1): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                                <a href="<?php echo $this->buildPaginationUrl($pagination['total_pages']); ?>" 
                                   class="pagination-number"><?php echo $pagination['total_pages']; ?></a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($pagination['has_next']): ?>
                            <a href="<?php echo $this->buildPaginationUrl($pagination['next_page']); ?>" 
                               class="pagination-btn next-btn">
                                Siguiente
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- Estado vacío -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="empty-title">No se encontraron productos</h3>
                <p class="empty-description">
                    <?php if (!empty($_GET['search'])): ?>
                        No hay productos que coincidan con tu búsqueda "<?php echo htmlspecialchars($_GET['search']); ?>".
                    <?php else: ?>
                        No hay productos disponibles en esta categoría.
                    <?php endif; ?>
                </p>
                <div class="empty-actions">
                    <a href="<?php echo AppHelper::baseUrl('store'); ?>" class="btn-primary">
                        <i class="fas fa-store"></i>
                        Ver todos los productos
                    </a>
                    <?php if (!empty($_GET['search'])): ?>
                        <button class="btn-secondary" onclick="document.querySelector('input[name=q]').value=''; document.querySelector('.product-search-form').submit();">
                            <i class="fas fa-times"></i>
                            Limpiar búsqueda
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Productos en Oferta -->
<?php if (!empty($saleProducts)): ?>
<section class="section sale-section bg-light">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">
                <i class="fas fa-fire"></i>
                Ofertas Especiales
            </h2>
            <p class="section-subtitle">Aprovecha estos descuentos por tiempo limitado</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($saleProducts as $index => $product): ?>
                <div class="product-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 150; ?>">
                    <?php include 'product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="section-cta" data-aos="fade-up">
            <a href="<?php echo AppHelper::baseUrl('store?on_sale=1'); ?>" class="btn-primary btn-lg">
                <i class="fas fa-percentage"></i>
                Ver Todas las Ofertas
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter y Beneficios -->
<section class="section newsletter-section bg-primary">
    <div class="container">
        <div class="newsletter-content">
            <div class="newsletter-text" data-aos="fade-right">
                <h2 class="newsletter-title">¡No te pierdas nuestras ofertas!</h2>
                <p class="newsletter-description">
                    Suscríbete y recibe descuentos exclusivos, nuevos productos y consejos de fitness directo en tu email.
                </p>
                <ul class="newsletter-benefits">
                    <li><i class="fas fa-check"></i> Descuentos exclusivos hasta 20%</li>
                    <li><i class="fas fa-check"></i> Acceso anticipado a nuevos productos</li>
                    <li><i class="fas fa-check"></i> Consejos personalizados de nutrición</li>
                    <li><i class="fas fa-check"></i> Sin spam, solo contenido valioso</li>
                </ul>
            </div>
            
            <div class="newsletter-form-wrapper" data-aos="fade-left">
                <form class="newsletter-form" id="store-newsletter-form">
                    <h3>Únete a nuestra comunidad</h3>
                    <div class="form-group">
                        <input type="email" 
                               name="email" 
                               placeholder="Tu email" 
                               class="newsletter-input" 
                               required>
                        <button type="submit" class="newsletter-submit">
                            <i class="fas fa-paper-plane"></i>
                            Suscribirse
                        </button>
                    </div>
                    <p class="newsletter-privacy">
                        Al suscribirte aceptas nuestra 
                        <a href="<?php echo AppHelper::baseUrl('privacy'); ?>">Política de Privacidad</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
// Función para construir URL de paginación (helper de PHP convertido a JS)
<?php
function buildPaginationUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
?>

document.addEventListener('DOMContentLoaded', function() {
    // Toggle de vista grid/list
    const viewButtons = document.querySelectorAll('.view-btn');
    const productsGrid = document.getElementById('products-grid');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Actualizar botones activos
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Cambiar clase del grid
            if (view === 'list') {
                productsGrid.classList.add('list-view');
            } else {
                productsGrid.classList.remove('list-view');
            }
            
            // Guardar preferencia
            localStorage.setItem('store_view_preference', view);
        });
    });
    
    // Cargar preferencia guardada
    const savedView = localStorage.getItem('store_view_preference');
    if (savedView === 'list') {
        document.querySelector('.list-view').click();
    }
    
    // Newsletter form
    const newsletterForm = document.getElementById('store-newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = this.querySelector('input[name="email"]').value;
            
            // Aquí iría la lógica de suscripción
            STYLOFITNESS.showNotification('¡Suscrito!', 'Te has suscrito exitosamente', 'success');
            this.reset();
        });
    }
    
    // Lazy loading para imágenes de productos
    const productImages = document.querySelectorAll('.product-image img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                observer.unobserve(img);
            }
        });
    });
    
    productImages.forEach(img => imageObserver.observe(img));
});
</script>

<style>
/* Estilos específicos para la tienda */
.store-hero {
    background: linear-gradient(135deg, var(--gradient-dark), var(--gradient-primary));
    padding: 8rem 0 4rem;
    text-align: center;
    color: white;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    background: var(--gradient-primary);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.search-section {
    max-width: 600px;
    margin: 2rem auto;
}

.search-input-wrapper {
    position: relative;
    display: flex;
}

.search-input {
    flex: 1;
    padding: 1rem 1.5rem;
    border: none;
    border-radius: 50px 0 0 50px;
    font-size: 1.1rem;
    background: rgba(255, 255, 255, 0.95);
}

.search-btn {
    padding: 1rem 2rem;
    background: var(--accent-color);
    border: none;
    border-radius: 0 50px 50px 0;
    color: white;
    font-size: 1.1rem;
    cursor: pointer;
    transition: var(--transition-fast);
}

.search-btn:hover {
    background: var(--secondary-color);
    transform: translateX(-2px);
}

.trust-badges {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 3rem;
    flex-wrap: wrap;
}

.trust-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.store-filters {
    background: white;
    padding: 2rem 0;
    border-bottom: 1px solid #eee;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.category-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border: 2px solid #f0f0f0;
    border-radius: 10px;
    text-decoration: none;
    color: var(--text-dark);
    transition: var(--transition-fast);
}

.category-card:hover,
.category-card.active {
    border-color: var(--primary-color);
    background: rgba(255, 107, 0, 0.05);
    transform: translateY(-2px);
}

.category-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gradient-primary);
    border-radius: 8px;
    color: white;
    font-size: 1.5rem;
}

.additional-filters {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.filters-form {
    display: flex;
    gap: 2rem;
    align-items: center;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-select,
.price-input {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 0.9rem;
}

.price-range {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.price-input {
    width: 80px;
}

.quick-filters {
    display: flex;
    gap: 1rem;
}

.quick-filter {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.9rem;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.view-toggle {
    display: flex;
    gap: 0.5rem;
}

.view-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    background: white;
    cursor: pointer;
    border-radius: 5px;
    transition: var(--transition-fast);
}

.view-btn.active,
.view-btn:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.products-grid.list-view .product-card {
    display: flex;
    align-items: center;
    text-align: left;
}

.products-grid.list-view .product-image {
    width: 150px;
    height: 150px;
    flex-shrink: 0;
    margin-right: 1.5rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    color: var(--text-light);
    margin-bottom: 1rem;
}

.empty-title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--text-dark);
}

.empty-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .trust-badges {
        gap: 1rem;
    }
    
    .trust-badge {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .category-grid {
        grid-template-columns: 1fr;
    }
    
    .filters-form {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .results-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .products-grid.list-view .product-card {
        flex-direction: column;
        text-align: center;
    }
    
    .products-grid.list-view .product-image {
        width: 100%;
        height: 200px;
        margin-right: 0;
        margin-bottom: 1rem;
    }
}
</style>