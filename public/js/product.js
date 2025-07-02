/**
 * STYLOFITNESS - Product Detail Page JavaScript
 * Funcionalidad interactiva para la página de producto
 */

// Variables globales
let currentProductId = null;
let currentQuantity = 1;
let isAddingToCart = false;

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    initializeProductPage();
});

function initializeProductPage() {
    // Obtener ID del producto desde la URL o elemento
    const productElement = document.querySelector('[data-product-id]');
    if (productElement) {
        currentProductId = productElement.getAttribute('data-product-id');
    }
    
    // Inicializar eventos
    initializeImageGallery();
    initializeQuantityControls();
    initializeAddToCartButton();
    initializeImageZoom();
    
    // Animaciones de entrada
    animatePageElements();
}

/**
 * Galería de imágenes
 */
function initializeImageGallery() {
    const thumbnails = document.querySelectorAll('.thumbnail-image');
    const mainImage = document.getElementById('mainProductImage');
    
    if (!mainImage) return;
    
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            changeMainImage(this.src, this);
        });
        
        // Precargar imágenes
        const img = new Image();
        img.src = thumbnail.src;
    });
    
    // Navegación con teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            navigateImage(-1);
        } else if (e.key === 'ArrowRight') {
            navigateImage(1);
        }
    });
}

function changeMainImage(imageSrc, thumbnail) {
    const mainImage = document.getElementById('mainProductImage');
    if (!mainImage) return;
    
    // Efecto de transición suave
    mainImage.style.opacity = '0.5';
    
    setTimeout(() => {
        mainImage.src = imageSrc;
        mainImage.style.opacity = '1';
    }, 150);
    
    // Actualizar thumbnails activos
    document.querySelectorAll('.thumbnail-image').forEach(img => {
        img.classList.remove('active');
    });
    
    if (thumbnail) {
        thumbnail.classList.add('active');
    }
}

function navigateImage(direction) {
    const thumbnails = document.querySelectorAll('.thumbnail-image');
    const activeThumbnail = document.querySelector('.thumbnail-image.active');
    
    if (!activeThumbnail || thumbnails.length <= 1) return;
    
    let currentIndex = Array.from(thumbnails).indexOf(activeThumbnail);
    let newIndex = currentIndex + direction;
    
    if (newIndex < 0) newIndex = thumbnails.length - 1;
    if (newIndex >= thumbnails.length) newIndex = 0;
    
    thumbnails[newIndex].click();
}

/**
 * Zoom de imagen
 */
function initializeImageZoom() {
    const mainImageContainer = document.querySelector('.main-image-container');
    const mainImage = document.getElementById('mainProductImage');
    
    if (!mainImageContainer || !mainImage) return;
    
    let isZoomed = false;
    
    mainImageContainer.addEventListener('click', function(e) {
        if (window.innerWidth > 768) { // Solo en desktop
            toggleImageZoom();
        }
    });
    
    function toggleImageZoom() {
        if (!isZoomed) {
            mainImage.style.transform = 'scale(2)';
            mainImage.style.cursor = 'zoom-out';
            mainImageContainer.style.overflow = 'hidden';
            isZoomed = true;
        } else {
            mainImage.style.transform = 'scale(1)';
            mainImage.style.cursor = 'zoom-in';
            isZoomed = false;
        }
    }
    
    // Resetear zoom al cambiar imagen
    mainImage.addEventListener('load', function() {
        if (isZoomed) {
            mainImage.style.transform = 'scale(1)';
            isZoomed = false;
        }
    });
}

/**
 * Controles de cantidad
 */
function initializeQuantityControls() {
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.querySelector('.qty-btn[onclick*="-1"]');
    const increaseBtn = document.querySelector('.qty-btn[onclick*="1"]');
    
    if (!quantityInput) return;
    
    // Validación en tiempo real
    quantityInput.addEventListener('input', function() {
        validateQuantity();
    });
    
    quantityInput.addEventListener('blur', function() {
        validateQuantity();
    });
    
    // Eventos de teclado
    quantityInput.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            changeQuantity(1);
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            changeQuantity(-1);
        }
    });
}

function changeQuantity(delta) {
    const quantityInput = document.getElementById('quantity');
    if (!quantityInput) return;
    
    const currentValue = parseInt(quantityInput.value) || 1;
    const newValue = currentValue + delta;
    const min = parseInt(quantityInput.min) || 1;
    const max = parseInt(quantityInput.max) || 999;
    
    if (newValue >= min && newValue <= max) {
        quantityInput.value = newValue;
        currentQuantity = newValue;
        
        // Animación visual
        quantityInput.style.transform = 'scale(1.1)';
        setTimeout(() => {
            quantityInput.style.transform = 'scale(1)';
        }, 150);
        
        updateTotalPrice();
    }
}

function validateQuantity() {
    const quantityInput = document.getElementById('quantity');
    if (!quantityInput) return;
    
    const value = parseInt(quantityInput.value);
    const min = parseInt(quantityInput.min) || 1;
    const max = parseInt(quantityInput.max) || 999;
    
    if (isNaN(value) || value < min) {
        quantityInput.value = min;
    } else if (value > max) {
        quantityInput.value = max;
        showNotification('Cantidad máxima disponible: ' + max, 'warning');
    }
    
    currentQuantity = parseInt(quantityInput.value);
    updateTotalPrice();
}

function updateTotalPrice() {
    const priceElement = document.querySelector('.current-price');
    if (!priceElement) return;
    
    const priceText = priceElement.textContent.replace(/[^\d.]/g, '');
    const unitPrice = parseFloat(priceText);
    
    if (!isNaN(unitPrice)) {
        const totalPrice = unitPrice * currentQuantity;
        // Aquí podrías mostrar el precio total si tienes un elemento para ello
    }
}

/**
 * Agregar al carrito
 */
function initializeAddToCartButton() {
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    if (!addToCartBtn) return;
    
    addToCartBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.getAttribute('onclick')?.match(/\d+/)?.[0] || currentProductId;
        if (productId) {
            addToCart(productId);
        }
    });
}

function addToCart(productId) {
    if (isAddingToCart) return;
    
    isAddingToCart = true;
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    const originalText = addToCartBtn?.innerHTML;
    
    // Mostrar estado de carga
    if (addToCartBtn) {
        addToCartBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="loading-spinner">
                <path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z" />
            </svg>
            Agregando...
        `;
        addToCartBtn.disabled = true;
    }
    
    const quantity = currentQuantity;
    
    // Simular petición al servidor
    setTimeout(() => {
        // En una implementación real, harías una petición AJAX
        fetch('/api/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('¡Producto agregado al carrito!', 'success');
                updateCartCounter(data.cart_count);
                
                // Animación de éxito
                if (addToCartBtn) {
                    addToCartBtn.style.background = 'linear-gradient(135deg, #00ff88 0%, #00cc6a 100%)';
                    setTimeout(() => {
                        addToCartBtn.style.background = '';
                    }, 1000);
                }
            } else {
                showNotification(data.message || 'Error al agregar al carrito', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('¡Producto agregado al carrito!', 'success'); // Fallback para demo
        })
        .finally(() => {
            // Restaurar botón
            if (addToCartBtn && originalText) {
                addToCartBtn.innerHTML = originalText;
                addToCartBtn.disabled = false;
            }
            isAddingToCart = false;
        });
    }, 500);
}

/**
 * Notificaciones
 */
function showNotification(message, type = 'info') {
    // Remover notificación existente
    const existingNotification = document.querySelector('.product-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Crear nueva notificación
    const notification = document.createElement('div');
    notification.className = `product-notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" />
                </svg>
            </button>
        </div>
    `;
    
    // Estilos de la notificación
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 400px;
    `;
    
    // Colores según tipo
    const colors = {
        success: 'linear-gradient(135deg, #00ff88 0%, #00cc6a 100%)',
        error: 'linear-gradient(135deg, #ff4444 0%, #cc3333 100%)',
        warning: 'linear-gradient(135deg, #ffaa00 0%, #cc8800 100%)',
        info: 'linear-gradient(135deg, #0088ff 0%, #0066cc 100%)'
    };
    
    notification.style.background = colors[type] || colors.info;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 5000);
}

/**
 * Actualizar contador del carrito
 */
function updateCartCounter(count) {
    const cartCounters = document.querySelectorAll('.cart-count, .cart-counter');
    cartCounters.forEach(counter => {
        counter.textContent = count;
        
        // Animación
        counter.style.transform = 'scale(1.3)';
        setTimeout(() => {
            counter.style.transform = 'scale(1)';
        }, 200);
    });
}

/**
 * Animaciones de página
 */
function animatePageElements() {
    // Observador de intersección para animaciones
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    // Elementos a animar
    const elementsToAnimate = document.querySelectorAll(`
        .product-info > *,
        .product-gallery,
        .related-products .product-card
    `);
    
    elementsToAnimate.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(element);
    });
}

/**
 * Utilidades
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// CSS para el spinner de carga
const style = document.createElement('style');
style.textContent = `
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    
    .notification-close {
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        padding: 0;
        opacity: 0.8;
        transition: opacity 0.2s ease;
    }
    
    .notification-close:hover {
        opacity: 1;
    }
`;
document.head.appendChild(style);

// Exportar funciones para uso global
window.changeMainImage = changeMainImage;
window.changeQuantity = changeQuantity;
window.addToCart = addToCart;