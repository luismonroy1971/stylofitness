/**
 * STYLOFITNESS - JavaScript Principal
 * Manejo de interacciones y efectos visuales profesionales
 */

// =============================================
// CONFIGURACIÓN GLOBAL
// =============================================
const STYLOFITNESS = {
    config: {
        apiUrl: '/api',
        animationDuration: 300,
        carouselInterval: 12000,
        scrollOffset: 100
    },
    
    // Cache para elementos DOM
    elements: {},
    
    // Estado de la aplicación
    state: {
        isLoading: false,
        currentUser: null,
        cart: [],
        carouselIndex: 0
    }
};

// =============================================
// MEGA CARRUSEL DE OFERTAS ESPECIALES
// =============================================
STYLOFITNESS.initMegaCarousel = function() {
    const megaCarousel = document.getElementById('hero-products-carousel');
    if (!megaCarousel) return;
    
    const track = document.getElementById('hero-track');
    const slides = document.querySelectorAll('.mega-slide');
    const dots = document.querySelectorAll('.mega-dot');
    const prevBtn = document.getElementById('mega-prev');
    const nextBtn = document.getElementById('mega-next');
    const progressBar = document.getElementById('carousel-progress');
    
    if (!track || slides.length === 0) return;
    
    let currentSlide = 0;
    const totalSlides = slides.length;
    let autoplayInterval;
    let progressInterval;
    const slideInterval = 12000; // 12 segundos por slide
    
    // Función para mover a un slide específico
    const moveToSlide = (index) => {
        currentSlide = index;
        // Para 5 productos: cada movimiento es 20% (100% / 5)
        const translateX = -index * 20;
        track.style.transform = `translateX(${translateX}%)`;
        
        // Actualizar clases activas
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
        
        // Reiniciar progress bar
        if (progressBar) {
            progressBar.style.width = '0%';
        }
        
        // Actualizar contadores de tiempo
        if (this.updateCountdownTimers) {
            this.updateCountdownTimers();
        }
    };
    
    // Función para ir al siguiente slide
    const nextSlide = () => {
        // Para 5 productos: navegar de 0 a 4
        const nextIndex = currentSlide >= totalSlides - 1 ? 0 : currentSlide + 1;
        moveToSlide(nextIndex);
    };
    
    // Función para ir al slide anterior
    const prevSlide = () => {
        const prevIndex = currentSlide <= 0 ? totalSlides - 1 : currentSlide - 1;
        moveToSlide(prevIndex);
    };
    
    // Event listeners para controles
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetAutoplay();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetAutoplay();
        });
    }
    
    // Event listeners para dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            moveToSlide(index);
            resetAutoplay();
        });
    });
    
    // Autoplay con progress bar
    const startAutoplay = () => {
        let progress = 0;
        const progressStep = 100 / (slideInterval / 50); // Actualizar cada 50ms
        
        autoplayInterval = setInterval(nextSlide, slideInterval);
        
        progressInterval = setInterval(() => {
            progress += progressStep;
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
            
            if (progress >= 100) {
                progress = 0;
            }
        }, 50);
    };
    
    const stopAutoplay = () => {
        clearInterval(autoplayInterval);
        clearInterval(progressInterval);
    };
    
    const resetAutoplay = () => {
        stopAutoplay();
        startAutoplay();
    };
    
    // Pausar autoplay al hover
    megaCarousel.addEventListener('mouseenter', stopAutoplay);
    megaCarousel.addEventListener('mouseleave', startAutoplay);
    
    // Soporte para touch/swipe
    let startX = 0;
    let endX = 0;
    let isDragging = false;
    
    megaCarousel.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isDragging = true;
    }, { passive: true });
    
    megaCarousel.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        endX = e.touches[0].clientX;
    }, { passive: true });
    
    megaCarousel.addEventListener('touchend', (e) => {
        if (!isDragging) return;
        isDragging = false;
        
        const diff = startX - endX;
        const threshold = 50;
        
        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
            resetAutoplay();
        }
    }, { passive: true });
    
    // Soporte para navegación con teclado
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
            resetAutoplay();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
            resetAutoplay();
        }
    });
    
    // Inicializar carrusel
    moveToSlide(0);
    startAutoplay();
    
    // Inicializar contadores de tiempo
    this.initCountdownTimers();
};

// =============================================
// CONTADORES REGRESIVOS
// =============================================
STYLOFITNESS.initCountdownTimers = function() {
    const timers = document.querySelectorAll('.countdown-timer');
    
    timers.forEach((timer, index) => {
        const endDate = new Date(timer.dataset.endDate);
        
        const updateTimer = () => {
            const now = new Date().getTime();
            const distance = endDate.getTime() - now;
            
            if (distance < 0) {
                // Tiempo expirado
                timer.innerHTML = '<div class="timer-expired">¡Oferta Expirada!</div>';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Actualizar elementos específicos del timer
            const daysEl = document.getElementById(`days-${index}`);
            const hoursEl = document.getElementById(`hours-${index}`);
            const minutesEl = document.getElementById(`minutes-${index}`);
            const secondsEl = document.getElementById(`seconds-${index}`);
            
            if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
            if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
            if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
        };
        
        // Actualizar inmediatamente
        updateTimer();
        
        // Actualizar cada segundo
        setInterval(updateTimer, 1000);
    });
};

STYLOFITNESS.updateCountdownTimers = function() {
    // Re-inicializar los timers cuando cambie de slide
    this.initCountdownTimers();
};

// =============================================
// FUNCIONES MEJORADAS PARA EL CARRITO
// =============================================
STYLOFITNESS.initMegaCartButtons = function() {
    // Event listeners para botones de agregar al carrito del mega carrusel
    document.querySelectorAll('.btn-add-cart-mega').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const productId = btn.dataset.productId;
            const quantity = 1;
            
            // Efecto visual especial para el mega carrusel
            btn.classList.add('adding');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>AGREGANDO...</span>';
            
            // Agregar al carrito
            this.addToCart(productId, quantity, () => {
                // Callback de éxito
                btn.classList.remove('adding');
                btn.classList.add('added');
                btn.innerHTML = '<i class="fas fa-check"></i><span>¡AGREGADO!</span>';
                
                setTimeout(() => {
                    btn.classList.remove('added');
                    btn.innerHTML = '<i class="fas fa-cart-plus"></i><span>AGREGAR AL CARRITO</span>';
                }, 3000);
            });
        });
    });
};

// Modificar la función addToCart para soportar callbacks
STYLOFITNESS.addToCart = function(productId, quantity = 1, callback = null) {
    // Obtener datos del producto
    fetch(`${this.config.apiUrl}/products/${productId}`)
        .then(response => response.json())
        .then(product => {
            const existingItem = this.state.cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                this.state.cart.push({
                    id: productId,
                    name: product.name,
                    price: product.price,
                    image: product.image,
                    quantity: quantity
                });
            }
            
            this.saveCart();
            this.updateCartUI();
            this.showNotification('¡Agregado!', `${product.name} añadido al carrito`, 'success');
            
            // Ejecutar callback si existe
            if (callback && typeof callback === 'function') {
                callback(product);
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            this.showNotification('Error', 'No se pudo agregar el producto', 'error');
        });
};

// Actualizar la inicialización para incluir los nuevos botones
STYLOFITNESS.initCart = function() {
    // Cargar carrito desde localStorage
    this.loadCart();
    
    // Event listeners para botones normales de agregar al carrito
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const productId = btn.dataset.productId;
            const quantity = parseInt(btn.dataset.quantity) || 1;
            this.addToCart(productId, quantity);
        });
    });
    
    // Event listeners para botones mega del carrusel
    this.initMegaCartButtons();
    
    // Event listeners para botones de cantidad
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const action = btn.dataset.action;
            const productId = btn.dataset.productId;
            
            if (action === 'increase') {
                this.updateCartQuantity(productId, 1);
            } else if (action === 'decrease') {
                this.updateCartQuantity(productId, -1);
            }
        });
    });
};

// =============================================
// INICIALIZACIÓN
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    STYLOFITNESS.init();
});

STYLOFITNESS.init = function() {
    console.log('🔥 STYLOFITNESS App iniciada');
    
    // Cachear elementos importantes
    this.cacheElements();
    
    // Inicializar componentes
    this.initLoadingScreen();
    this.initNavigation();
    this.initMegaCarousel(); // Nuevo carrusel de ofertas
    this.initHeroProductsCarousel(); // Carrusel de productos destacados
    this.initCarousel();
    this.initScrollEffects();
    this.initAnimations();
    this.initForms();
    this.initCart();
    this.initVideoPlayer();
    this.initLazyLoading();
    this.initFlashMessages();
    
    // Configurar event listeners
    this.setupEventListeners();
    
    // Cargar datos iniciales
    this.loadInitialData();
};

// =============================================
// CACHE DE ELEMENTOS DOM
// =============================================
STYLOFITNESS.cacheElements = function() {
    this.elements = {
        header: document.querySelector('.header'),
        navToggle: document.querySelector('.nav-toggle'),
        navMenu: document.querySelector('.nav-menu'),
        
        // Carrusel normal
        carousel: document.querySelector('.product-carousel'),
        carouselContainer: document.querySelector('.carousel-container'),
        carouselDots: document.querySelectorAll('.carousel-dot'),
        carouselPrev: document.querySelector('.carousel-prev'),
        carouselNext: document.querySelector('.carousel-next'),
        
        // Mega carrusel de ofertas
        megaCarousel: document.getElementById('mega-offers-carousel'),
        megaTrack: document.getElementById('offers-track'),
        megaDots: document.querySelectorAll('.mega-dot'),
        megaPrev: document.getElementById('mega-prev'),
        megaNext: document.getElementById('mega-next'),
        
        productCards: document.querySelectorAll('.product-card'),
        classCards: document.querySelectorAll('.class-card'),
        cartIcon: document.querySelector('.cart-icon'),
        cartCount: document.querySelector('.cart-count'),
        loadingSpinner: document.querySelector('.loading-spinner'),
        forms: document.querySelectorAll('form'),
        videos: document.querySelectorAll('video'),
        lazyImages: document.querySelectorAll('img[data-src]')
    };
};

// =============================================
// NAVEGACIÓN
// =============================================
STYLOFITNESS.initNavigation = function() {
    const header = this.elements.header;
    
    // Efecto de scroll en header
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Navegación móvil
    const navToggle = this.elements.navToggle;
    const navMenu = this.elements.navMenu;
    const navOverlay = document.getElementById('nav-overlay');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
            if (navOverlay) {
                navOverlay.classList.toggle('active');
            }
            document.body.classList.toggle('nav-open');
        });
    }
    
    // Cerrar menú móvil al hacer clic en overlay
    if (navOverlay) {
        navOverlay.addEventListener('click', () => {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
            navOverlay.classList.remove('active');
            document.body.classList.remove('nav-open');
        });
    }
    
    // Cerrar menú móvil al hacer clic en enlaces
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
                if (navOverlay) {
                    navOverlay.classList.remove('active');
                }
                document.body.classList.remove('nav-open');
            }
        });
    });
    
    // Dropdown del usuario
    const userTrigger = document.getElementById('user-menu-trigger');
    const userDropdown = document.getElementById('user-dropdown-menu');
    
    if (userTrigger && userDropdown) {
        userTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });
        
        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!userTrigger.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }
    
    // Smooth scroll para enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Búsqueda en tiempo real
    const searchInput = document.querySelector('.search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    
    if (searchInput && searchSuggestions) {
        let searchTimeout;
        
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            
            // Limpiar timeout anterior
            clearTimeout(searchTimeout);
            
            if (query.length >= 2) {
                // Buscar después de 300ms de inactividad
                searchTimeout = setTimeout(() => {
                    this.performSearch(query, searchSuggestions);
                }, 300);
            } else {
                searchSuggestions.style.display = 'none';
            }
        });
        
        // Cerrar sugerencias al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });
    }
};

// =============================================
// CARRUSEL DE PRODUCTOS
// =============================================
STYLOFITNESS.initCarousel = function() {
    const carousel = this.elements.carousel;
    if (!carousel) return;
    
    const container = this.elements.carouselContainer;
    const dots = this.elements.carouselDots;
    const prevBtn = this.elements.carouselPrev;
    const nextBtn = this.elements.carouselNext;
    
    let currentIndex = 0;
    const totalSlides = dots.length;
    let autoplayInterval;
    
    // Función para mover el carrusel
    const moveToSlide = (index) => {
        currentIndex = index;
        const translateX = -index * 100;
        container.style.transform = `translateX(${translateX}%)`;
        
        // Actualizar dots
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
        
        // Actualizar estado global
        this.state.carouselIndex = index;
    };
    
    // Función para siguiente slide
    const nextSlide = () => {
        const nextIndex = (currentIndex + 1) % totalSlides;
        moveToSlide(nextIndex);
    };
    
    // Función para slide anterior
    const prevSlide = () => {
        const prevIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        moveToSlide(prevIndex);
    };
    
    // Event listeners
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => moveToSlide(index));
    });
    
    // Autoplay
    const startAutoplay = () => {
        autoplayInterval = setInterval(nextSlide, this.config.carouselInterval);
    };
    
    const stopAutoplay = () => {
        clearInterval(autoplayInterval);
    };
    
    // Pausar autoplay al hover
    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);
    
    // Iniciar autoplay
    startAutoplay();
    
    // Soporte para touch/swipe
    let startX = 0;
    let endX = 0;
    
    carousel.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
    });
    
    carousel.addEventListener('touchend', (e) => {
        endX = e.changedTouches[0].clientX;
        const diff = startX - endX;
        
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        }
    });
};

// =============================================
// EFECTOS DE SCROLL
// =============================================
STYLOFITNESS.initScrollEffects = function() {
    // Intersection Observer para animaciones
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                
                // Animación de contadores
                if (entry.target.classList.contains('stat-number')) {
                    this.animateCounter(entry.target);
                }
            }
        });
    }, observerOptions);
    
    // Observar elementos
    document.querySelectorAll('.product-card, .class-card, .testimonial-card, .stat-card').forEach(el => {
        observer.observe(el);
    });
    
    // Parallax effect para hero
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.hero');
        
        if (hero) {
            const speed = 0.5;
            hero.style.transform = `translateY(${scrolled * speed}px)`;
        }
    });
};

// =============================================
// ANIMACIONES
// =============================================
STYLOFITNESS.initAnimations = function() {
    // Animación de aparición progresiva
    const staggerAnimation = (elements, delay = 100) => {
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('slide-up');
            }, index * delay);
        });
    };
    
    // Aplicar a grids
    const productGrid = document.querySelector('.products-grid');
    if (productGrid) {
        const products = productGrid.querySelectorAll('.product-card');
        staggerAnimation(products);
    }
    
    const classesGrid = document.querySelector('.classes-grid');
    if (classesGrid) {
        const classes = classesGrid.querySelectorAll('.class-card');
        staggerAnimation(classes);
    }
};

// =============================================
// CONTADOR ANIMADO
// =============================================
STYLOFITNESS.animateCounter = function(element) {
    const target = parseInt(element.textContent);
    const duration = 2000;
    const start = performance.now();
    
    const updateCounter = (currentTime) => {
        const elapsed = currentTime - start;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(target * easeOut);
        
        element.textContent = current.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target.toLocaleString();
        }
    };
    
    requestAnimationFrame(updateCounter);
};

// =============================================
// FORMULARIOS
// =============================================
STYLOFITNESS.initForms = function() {
    this.elements.forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmit(form);
        });
        
        // Validación en tiempo real
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
            
            input.addEventListener('input', () => {
                this.clearFieldError(input);
            });
        });
    });
};

STYLOFITNESS.handleFormSubmit = function(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Mostrar loading
    this.showLoading(submitBtn);
    
    // Obtener action y method
    const action = form.action || window.location.href;
    const method = form.method || 'POST';
    
    // Enviar datos
    fetch(action, {
        method: method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        this.hideLoading(submitBtn);
        
        if (data.success) {
            this.showNotification('Éxito', data.message, 'success');
            
            // Redirigir si es necesario
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        } else {
            this.showNotification('Error', data.message, 'error');
            
            // Mostrar errores de campos
            if (data.errors) {
                this.showFieldErrors(form, data.errors);
            }
        }
    })
    .catch(error => {
        this.hideLoading(submitBtn);
        this.showNotification('Error', 'Ocurrió un error. Inténtalo de nuevo.', 'error');
        console.error('Form error:', error);
    });
};

// =============================================
// CARRITO DE COMPRAS
// =============================================
STYLOFITNESS.initCart = function() {
    // Cargar carrito desde localStorage
    this.loadCart();
    
    // Event listeners para botones normales de agregar al carrito
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const productId = btn.dataset.productId;
            const quantity = parseInt(btn.dataset.quantity) || 1;
            this.addToCart(productId, quantity);
        });
    });
    
    // Event listeners para botones mega del carrusel
    this.initMegaCartButtons();
    
    // Event listeners para botones de cantidad
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const action = btn.dataset.action;
            const productId = btn.dataset.productId;
            
            if (action === 'increase') {
                this.updateCartQuantity(productId, 1);
            } else if (action === 'decrease') {
                this.updateCartQuantity(productId, -1);
            }
        });
    });
};

STYLOFITNESS.addToCart = function(productId, quantity = 1, callback = null) {
    // Obtener datos del producto
    fetch(`${this.config.apiUrl}/products/${productId}`)
        .then(response => response.json())
        .then(product => {
            const existingItem = this.state.cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                this.state.cart.push({
                    id: productId,
                    name: product.name,
                    price: product.price,
                    image: product.image,
                    quantity: quantity
                });
            }
            
            this.saveCart();
            this.updateCartUI();
            this.showNotification('¡Agregado!', `${product.name} añadido al carrito`, 'success');
            
            // Ejecutar callback si existe
            if (callback && typeof callback === 'function') {
                callback(product);
            }
            
            // Efecto visual en el botón normal (si no es mega)
            if (!callback) {
                const btn = document.querySelector(`[data-product-id="${productId}"]`);
                if (btn) {
                    btn.classList.add('added');
                    setTimeout(() => btn.classList.remove('added'), 2000);
                }
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            this.showNotification('Error', 'No se pudo agregar el producto', 'error');
        });
};

STYLOFITNESS.updateCartQuantity = function(productId, change) {
    const item = this.state.cart.find(item => item.id === productId);
    
    if (item) {
        item.quantity += change;
        
        if (item.quantity <= 0) {
            this.removeFromCart(productId);
        } else {
            this.saveCart();
            this.updateCartUI();
        }
    }
};

STYLOFITNESS.removeFromCart = function(productId) {
    this.state.cart = this.state.cart.filter(item => item.id !== productId);
    this.saveCart();
    this.updateCartUI();
};

STYLOFITNESS.saveCart = function() {
    localStorage.setItem('stylofitness_cart', JSON.stringify(this.state.cart));
};

STYLOFITNESS.loadCart = function() {
    const saved = localStorage.getItem('stylofitness_cart');
    if (saved) {
        this.state.cart = JSON.parse(saved);
        this.updateCartUI();
    }
};

STYLOFITNESS.updateCartUI = function() {
    const cartCount = this.elements.cartCount;
    const totalItems = this.state.cart.reduce((sum, item) => sum + item.quantity, 0);
    
    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'block' : 'none';
    }
    
    // Actualizar modal del carrito si está abierto
    const cartModal = document.querySelector('.cart-modal');
    if (cartModal && cartModal.classList.contains('active')) {
        this.renderCartModal();
    }
};

// =============================================
// REPRODUCTOR DE VIDEO
// =============================================
STYLOFITNESS.initVideoPlayer = function() {
    this.elements.videos.forEach(video => {
        // Controles personalizados
        const wrapper = document.createElement('div');
        wrapper.className = 'video-wrapper';
        video.parentNode.insertBefore(wrapper, video);
        wrapper.appendChild(video);
        
        // Botón de play/pause
        const playBtn = document.createElement('button');
        playBtn.className = 'video-play-btn';
        playBtn.innerHTML = '▶';
        wrapper.appendChild(playBtn);
        
        // Event listeners
        playBtn.addEventListener('click', () => {
            if (video.paused) {
                video.play();
                playBtn.style.display = 'none';
            } else {
                video.pause();
                playBtn.style.display = 'block';
            }
        });
        
        video.addEventListener('click', () => {
            playBtn.click();
        });
        
        video.addEventListener('ended', () => {
            playBtn.style.display = 'block';
            playBtn.innerHTML = '🔄';
        });
        
        // Lazy loading para videos
        if ('IntersectionObserver' in window) {
            const videoObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const video = entry.target;
                        video.src = video.dataset.src;
                        video.load();
                        videoObserver.unobserve(video);
                    }
                });
            });
            
            if (video.dataset.src) {
                videoObserver.observe(video);
            }
        }
    });
};

// =============================================
// LAZY LOADING DE IMÁGENES
// =============================================
STYLOFITNESS.initLazyLoading = function() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        this.elements.lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback para navegadores sin IntersectionObserver
        this.elements.lazyImages.forEach(img => {
            img.src = img.dataset.src;
        });
    }
};

// =============================================
// NOTIFICACIONES
// =============================================
STYLOFITNESS.showNotification = function(title, message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Animación de entrada
    requestAnimationFrame(() => {
        notification.classList.add('show');
    });
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        this.hideNotification(notification);
    }, 5000);
    
    // Botón de cerrar
    notification.querySelector('.notification-close').addEventListener('click', () => {
        this.hideNotification(notification);
    });
};

STYLOFITNESS.hideNotification = function(notification) {
    notification.classList.remove('show');
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
};

// =============================================
// UTILIDADES
// =============================================
STYLOFITNESS.showLoading = function(element) {
    const originalText = element.textContent;
    element.setAttribute('data-original-text', originalText);
    element.innerHTML = '<span class="loading"></span> Cargando...';
    element.disabled = true;
    this.state.isLoading = true;
};

STYLOFITNESS.hideLoading = function(element) {
    const originalText = element.getAttribute('data-original-text');
    element.textContent = originalText;
    element.disabled = false;
    this.state.isLoading = false;
};

STYLOFITNESS.validateField = function(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.required;
    
    // Limpiar errores previos
    this.clearFieldError(field);
    
    // Validar campo requerido
    if (required && !value) {
        this.showFieldError(field, 'Este campo es obligatorio');
        return false;
    }
    
    // Validar email
    if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            this.showFieldError(field, 'Ingresa un email válido');
            return false;
        }
    }
    
    // Validar teléfono
    if (field.name === 'phone' && value) {
        const phoneRegex = /^[+]?[\d\s-()]+$/;
        if (!phoneRegex.test(value)) {
            this.showFieldError(field, 'Ingresa un teléfono válido');
            return false;
        }
    }
    
    // Validar contraseña
    if (type === 'password' && value) {
        if (value.length < 6) {
            this.showFieldError(field, 'La contraseña debe tener al menos 6 caracteres');
            return false;
        }
    }
    
    return true;
};

STYLOFITNESS.showFieldError = function(field, message) {
    field.classList.add('error');
    
    let errorDiv = field.parentNode.querySelector('.field-error');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        field.parentNode.appendChild(errorDiv);
    }
    
    errorDiv.textContent = message;
};

STYLOFITNESS.clearFieldError = function(field) {
    field.classList.remove('error');
    
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
};

STYLOFITNESS.showFieldErrors = function(form, errors) {
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            this.showFieldError(field, errors[fieldName]);
        }
    });
};

// =============================================
// EVENT LISTENERS GLOBALES
// =============================================
STYLOFITNESS.setupEventListeners = function() {
    // Cerrar modales al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-overlay')) {
            this.closeModal(e.target.querySelector('.modal'));
        }
    });
    
    // Cerrar modales con ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal.active');
            if (activeModal) {
                this.closeModal(activeModal);
            }
        }
    });
    
    // Manejar errores de imágenes
    document.addEventListener('error', (e) => {
        if (e.target.tagName === 'IMG') {
            e.target.src = '/images/placeholder.jpg';
        }
    }, true);
};

// =============================================
// CARGA DE DATOS INICIAL
// =============================================
STYLOFITNESS.loadInitialData = function() {
    // Cargar productos destacados
    this.loadFeaturedProducts();
    
    // Cargar clases próximas
    this.loadUpcomingClasses();
    
    // Cargar estadísticas
    this.loadStats();
};

STYLOFITNESS.loadFeaturedProducts = function() {
    fetch(`${this.config.apiUrl}/products/featured`)
        .then(response => response.json())
        .then(products => {
            this.renderProducts(products);
        })
        .catch(error => {
            console.error('Error loading featured products:', error);
        });
};

STYLOFITNESS.loadUpcomingClasses = function() {
    fetch(`${this.config.apiUrl}/classes/upcoming`)
        .then(response => response.json())
        .then(classes => {
            this.renderClasses(classes);
        })
        .catch(error => {
            console.error('Error loading upcoming classes:', error);
        });
};

STYLOFITNESS.loadStats = function() {
    fetch(`${this.config.apiUrl}/stats`)
        .then(response => response.json())
        .then(stats => {
            this.renderStats(stats);
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
};

// =============================================
// RENDERIZADO DE COMPONENTES
// =============================================
STYLOFITNESS.renderProducts = function(products) {
    const container = document.querySelector('.products-grid');
    if (!container) return;
    
    container.innerHTML = products.map(product => `
        <div class="product-card hover-lift" data-product-id="${product.id}">
            <div class="product-image">
                <img src="${product.image}" alt="${product.name}" loading="lazy">
                ${product.discount ? `<div class="product-badge">-${product.discount}%</div>` : ''}
            </div>
            <div class="product-info">
                <div class="product-category">${product.category}</div>
                <h3 class="product-title">${product.name}</h3>
                <p class="product-description">${product.description}</p>
                <div class="product-price">
                    <span class="current-price">S/ ${product.price}</span>
                    ${product.original_price ? `<span class="original-price">S/ ${product.original_price}</span>` : ''}
                </div>
                <div class="product-actions">
                    <button class="btn-add-cart" data-product-id="${product.id}">
                        Agregar al Carrito
                    </button>
                    <button class="btn-quick-view" data-product-id="${product.id}">
                        Vista Rápida
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    // Reinicializar event listeners
    this.initCart();
};

// =============================================
// BÚSQUEDA
// =============================================
STYLOFITNESS.performSearch = function(query, suggestionsContainer) {
    // Mostrar loading en sugerencias
    suggestionsContainer.innerHTML = '<div class="search-loading">Buscando...</div>';
    suggestionsContainer.style.display = 'block';
    
    // Realizar búsqueda
    fetch(`${this.config.apiUrl}/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            this.renderSearchSuggestions(data.suggestions, suggestionsContainer);
        })
        .catch(error => {
            console.error('Search error:', error);
            suggestionsContainer.innerHTML = '<div class="search-error">Error en la búsqueda</div>';
        });
};

STYLOFITNESS.renderSearchSuggestions = function(suggestions, container) {
    if (!suggestions || suggestions.length === 0) {
        container.innerHTML = '<div class="search-no-results">No se encontraron resultados</div>';
        return;
    }
    
    const html = suggestions.map(item => `
        <div class="search-suggestion-item" data-type="${item.type}" data-id="${item.id}">
            <div class="suggestion-icon">
                <i class="fas fa-${item.type === 'product' ? 'shopping-bag' : 'dumbbell'}"></i>
            </div>
            <div class="suggestion-content">
                <div class="suggestion-title">${item.name}</div>
                <div class="suggestion-category">${item.category}</div>
            </div>
            ${item.price ? `<div class="suggestion-price">S/ ${item.price}</div>` : ''}
        </div>
    `).join('');
    
    container.innerHTML = html;
    
    // Agregar event listeners a las sugerencias
    container.querySelectorAll('.search-suggestion-item').forEach(item => {
        item.addEventListener('click', () => {
            const type = item.dataset.type;
            const id = item.dataset.id;
            
            if (type === 'product') {
                window.location.href = `/store/product/${id}`;
            } else if (type === 'routine') {
                window.location.href = `/routines/${id}`;
            }
            
            container.style.display = 'none';
        });
    });
};

// =============================================
// NOTIFICACIONES FLASH
// =============================================
STYLOFITNESS.initFlashMessages = function() {
    const flashMessages = document.querySelectorAll('.flash-message');
    
    flashMessages.forEach(message => {
        const closeBtn = message.querySelector('.flash-close');
        
        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            this.hideFlashMessage(message);
        }, 5000);
        
        // Cerrar al hacer clic
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                this.hideFlashMessage(message);
            });
        }
    });
};

STYLOFITNESS.hideFlashMessage = function(message) {
    message.style.transform = 'translateY(-100%)';
    message.style.opacity = '0';
    
    setTimeout(() => {
        if (message.parentNode) {
            message.parentNode.removeChild(message);
        }
    }, 300);
};

// =============================================
// LOADING SCREEN
// =============================================
STYLOFITNESS.initLoadingScreen = function() {
    const loadingScreen = document.getElementById('loading-screen');
    
    if (loadingScreen) {
        // Ocultar loading screen cuando la página esté lista
        window.addEventListener('load', () => {
            setTimeout(() => {
                loadingScreen.style.opacity = '0';
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, 500);
            }, 1000);
        });
    }
};

// =============================================
// API HELPERS
// =============================================
STYLOFITNESS.api = {
    get: async function(endpoint) {
        const response = await fetch(`${STYLOFITNESS.config.apiUrl}${endpoint}`);
        return response.json();
    },
    
    post: async function(endpoint, data) {
        const response = await fetch(`${STYLOFITNESS.config.apiUrl}${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        return response.json();
    }
};

// =============================================
// CARRUSEL DE PRODUCTOS DESTACADOS
// =============================================
STYLOFITNESS.initHeroProductsCarousel = function() {
    const carousel = document.getElementById('hero-products-carousel');
    if (!carousel) return;
    
    const track = carousel.querySelector('.mega-carousel-inner');
    const slides = carousel.querySelectorAll('.mega-slide');
    const indicators = carousel.querySelectorAll('.mega-indicator');
    
    if (!track || slides.length === 0) return;
    
    let currentSlide = 0;
    const totalSlides = slides.length;
    const slideWidth = 100 / 3; // 3 productos visibles
    
    // Función para mover el carrusel
    const moveToSlide = (index) => {
        currentSlide = index;
        const translateX = -(currentSlide * slideWidth);
        track.style.transform = `translateX(${translateX}%)`;
        
        // Actualizar indicadores
        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === currentSlide);
        });
    };
    
    // Auto-play cada 12 segundos
    let autoplayInterval = setInterval(() => {
        const nextIndex = (currentSlide + 1) % totalSlides;
        moveToSlide(nextIndex);
    }, 12000);
    
    // Event listeners para indicadores
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            moveToSlide(index);
            clearInterval(autoplayInterval);
            autoplayInterval = setInterval(() => {
                const nextIndex = (currentSlide + 1) % totalSlides;
                moveToSlide(nextIndex);
            }, 12000);
        });
    });
    
    // Pausar autoplay al hover
    carousel.addEventListener('mouseenter', () => {
        clearInterval(autoplayInterval);
    });
    
    carousel.addEventListener('mouseleave', () => {
        autoplayInterval = setInterval(() => {
            const nextIndex = (currentSlide + 1) % totalSlides;
            moveToSlide(nextIndex);
        }, 12000);
    });
    
    // Inicializar
    moveToSlide(0);
};

// =============================================
// EXPORTAR PARA USO GLOBAL
// =============================================
window.STYLOFITNESS = STYLOFITNESS;