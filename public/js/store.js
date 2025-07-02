/**
 * STYLOFITNESS - Sistema de Carrito de Compras
 * JavaScript para gestión del carrito y funcionalidades de tienda
 */

class ShoppingCart {
    constructor() {
        this.cart = [];
        this.total = 0;
        this.itemCount = 0;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updateCartCounter();
        this.initializeCart();
    }
    
    bindEvents() {
        // Filtros AJAX
        this.initAjaxFilters();
        
        // Botones de añadir al carrito
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-add-cart') || e.target.closest('.btn-add-cart')) {
                e.preventDefault();
                const btn = e.target.matches('.btn-add-cart') ? e.target : e.target.closest('.btn-add-cart');
                this.addToCart(btn);
            }
        });
        
        // Botones de actualizar cantidad en carrito
        document.addEventListener('change', (e) => {
            if (e.target.matches('.cart-quantity-input')) {
                this.updateCartItemQuantity(e.target);
            }
        });
        
        // Botones de eliminar del carrito
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-remove-cart') || e.target.closest('.btn-remove-cart')) {
                e.preventDefault();
                const btn = e.target.matches('.btn-remove-cart') ? e.target : e.target.closest('.btn-remove-cart');
                this.removeFromCart(btn);
            }
        });
        
        // Botón de vaciar carrito
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-clear-cart')) {
                e.preventDefault();
                this.clearCart();
            }
        });
        
        // Aplicar cupón
        const couponForm = document.getElementById('coupon-form');
        if (couponForm) {
            couponForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.applyCoupon();
            });
        }
        
        // Remover cupón
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-remove-coupon')) {
                e.preventDefault();
                this.removeCoupon();
            }
        });
    }
    
    async addToCart(btn) {
        const productId = btn.dataset.productId;
        const variationId = btn.dataset.variationId || null;
        const quantity = this.getQuantityFromForm(btn) || 1;
        
        if (!productId) {
            this.showNotification('Error: ID de producto no encontrado', 'error');
            return;
        }
        
        // Mostrar loading
        this.setButtonLoading(btn, true);
        
        try {
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    product_id: productId,
                    variation_id: variationId,
                    quantity: quantity
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updateCartCounter(data.cart_count);
                this.animateAddToCart(btn);
                
                // Actualizar el carrito flotante si existe
                this.updateMiniCart();
            } else {
                this.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showNotification('Error al añadir al carrito', 'error');
        } finally {
            this.setButtonLoading(btn, false);
        }
    }
    
    async updateCartItemQuantity(input) {
        const itemId = input.dataset.itemId;
        const quantity = parseInt(input.value);
        
        if (!itemId || quantity < 0) {
            return;
        }
        
        try {
            const response = await fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    item_id: itemId,
                    quantity: quantity
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateCartCounter(data.cart_count);
                this.updateCartTotals(data.cart_totals);
                
                if (quantity === 0) {
                    this.removeCartItemFromDOM(itemId);
                    this.showNotification(data.message, 'info');
                }
            } else {
                this.showNotification(data.error, 'error');
                // Revertir el valor del input
                input.value = input.dataset.originalValue || 1;
            }
        } catch (error) {
            console.error('Error updating cart:', error);
            this.showNotification('Error al actualizar el carrito', 'error');
        }
    }
    
    async removeFromCart(btn) {
        const itemId = btn.dataset.itemId;
        
        if (!itemId) {
            return;
        }
        
        if (!confirm('¿Estás seguro de que quieres eliminar este producto del carrito?')) {
            return;
        }
        
        try {
            const response = await fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    item_id: itemId
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.removeCartItemFromDOM(itemId);
                this.updateCartCounter(data.cart_count);
                this.updateCartTotals(data.cart_totals);
                this.showNotification(data.message, 'success');
            } else {
                this.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error removing from cart:', error);
            this.showNotification('Error al eliminar del carrito', 'error');
        }
    }
    
    async clearCart() {
        if (!confirm('¿Estás seguro de que quieres vaciar todo el carrito?')) {
            return;
        }
        
        try {
            const response = await fetch('/cart/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remover todos los items del DOM
                const cartItems = document.querySelectorAll('.cart-item');
                cartItems.forEach(item => item.remove());
                
                this.updateCartCounter(0);
                this.showEmptyCartMessage();
                this.showNotification(data.message, 'success');
            } else {
                this.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
            this.showNotification('Error al vaciar el carrito', 'error');
        }
    }
    
    async applyCoupon() {
        const couponInput = document.getElementById('coupon-code');
        const couponButton = document.getElementById('apply-coupon-btn');
        
        if (!couponInput || !couponInput.value) {
            this.showNotification('Ingresa un código de cupón', 'error');
            return;
        }
        
        const couponCode = couponInput.value.trim();
        
        this.setButtonLoading(couponButton, true);
        
        try {
            const response = await fetch('/cart/apply-coupon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    coupon_code: couponCode
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updateCartTotals(data.totals);
                this.showAppliedCoupon(couponCode, data.discount);
                couponInput.value = '';
            } else {
                this.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error applying coupon:', error);
            this.showNotification('Error al aplicar el cupón', 'error');
        } finally {
            this.setButtonLoading(couponButton, false);
        }
    }
    
    async removeCoupon() {
        try {
            const response = await fetch('/cart/remove-coupon', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'info');
                this.updateCartTotals(data.totals);
                this.hideAppliedCoupon();
            } else {
                this.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error removing coupon:', error);
            this.showNotification('Error al remover el cupón', 'error');
        }
    }
    
    async updateCartCounter(count = null) {
        if (count === null) {
            try {
                const response = await fetch('/cart/count');
                const data = await response.json();
                count = data.count || 0;
            } catch (error) {
                console.error('Error getting cart count:', error);
                return;
            }
        }
        
        const counters = document.querySelectorAll('.cart-counter');
        counters.forEach(counter => {
            counter.textContent = count;
            counter.style.display = count > 0 ? 'inline' : 'none';
        });
        
        this.itemCount = count;
    }
    
    updateCartTotals(totals) {
        if (!totals) return;
        
        // Actualizar subtotal
        const subtotalElements = document.querySelectorAll('.cart-subtotal');
        subtotalElements.forEach(el => {
            el.textContent = 'S/ ' + totals.subtotal.toFixed(2);
        });
        
        // Actualizar envío
        const shippingElements = document.querySelectorAll('.cart-shipping');
        shippingElements.forEach(el => {
            el.textContent = totals.shipping > 0 ? 'S/ ' + totals.shipping.toFixed(2) : 'Gratis';
        });
        
        // Actualizar impuestos
        const taxElements = document.querySelectorAll('.cart-tax');
        taxElements.forEach(el => {
            el.textContent = 'S/ ' + totals.tax.toFixed(2);
        });
        
        // Actualizar descuento
        const discountElements = document.querySelectorAll('.cart-discount');
        discountElements.forEach(el => {
            if (totals.discount && totals.discount > 0) {
                el.textContent = '-S/ ' + totals.discount.toFixed(2);
                el.parentElement.style.display = 'block';
            } else {
                el.parentElement.style.display = 'none';
            }
        });
        
        // Actualizar total
        const totalElements = document.querySelectorAll('.cart-total');
        totalElements.forEach(el => {
            el.textContent = 'S/ ' + totals.total.toFixed(2);
        });
        
        // Actualizar mensaje de envío gratis
        if (totals.free_shipping_remaining > 0) {
            this.showFreeShippingMessage(totals.free_shipping_remaining);
        } else {
            this.hideFreeShippingMessage();
        }
    }
    
    showFreeShippingMessage(remaining) {
        const messageElements = document.querySelectorAll('.free-shipping-message');
        messageElements.forEach(el => {
            el.innerHTML = `<i class="fas fa-truck"></i> Añade S/ ${remaining.toFixed(2)} más para obtener <strong>envío gratis</strong>`;
            el.style.display = 'block';
        });
    }
    
    hideFreeShippingMessage() {
        const messageElements = document.querySelectorAll('.free-shipping-message');
        messageElements.forEach(el => {
            el.style.display = 'none';
        });
    }
    
    showAppliedCoupon(code, discount) {
        const couponElements = document.querySelectorAll('.applied-coupon');
        couponElements.forEach(el => {
            el.innerHTML = `
                <span class="coupon-code">${code}</span>
                <span class="coupon-discount">-S/ ${discount.toFixed(2)}</span>
                <button class="btn-remove-coupon" title="Remover cupón">
                    <i class="fas fa-times"></i>
                </button>
            `;
            el.style.display = 'flex';
        });
    }
    
    hideAppliedCoupon() {
        const couponElements = document.querySelectorAll('.applied-coupon');
        couponElements.forEach(el => {
            el.style.display = 'none';
        });
    }
    
    removeCartItemFromDOM(itemId) {
        const item = document.querySelector(`[data-item-id="${itemId}"]`);
        if (item) {
            item.remove();
        }
        
        // Verificar si el carrito está vacío
        const remainingItems = document.querySelectorAll('.cart-item');
        if (remainingItems.length === 0) {
            this.showEmptyCartMessage();
        }
    }
    
    showEmptyCartMessage() {
        const cartContainer = document.querySelector('.cart-items-container');
        if (cartContainer) {
            cartContainer.innerHTML = `
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Tu carrito está vacío</h3>
                    <p>Descubre nuestros productos y añádelos a tu carrito</p>
                    <a href="/store" class="btn btn-primary">
                        <i class="fas fa-store"></i>
                        Ir a la tienda
                    </a>
                </div>
            `;
        }
    }
    
    getQuantityFromForm(btn) {
        const form = btn.closest('form');
        if (form) {
            const quantityInput = form.querySelector('.quantity-input');
            return quantityInput ? parseInt(quantityInput.value) : 1;
        }
        
        const quantityInput = btn.parentElement.querySelector('.quantity-input');
        return quantityInput ? parseInt(quantityInput.value) : 1;
    }
    
    setButtonLoading(btn, loading) {
        if (!btn) return;
        
        if (loading) {
            btn.disabled = true;
            btn.dataset.originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
        } else {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.originalText || btn.innerHTML;
        }
    }
    
    animateAddToCart(btn) {
        const productImg = btn.closest('.product-card')?.querySelector('img');
        const cartIcon = document.querySelector('.cart-icon');
        
        if (!productImg || !cartIcon) return;
        
        // Crear imagen temporal para la animación
        const tempImg = productImg.cloneNode();
        tempImg.style.position = 'fixed';
        tempImg.style.width = '50px';
        tempImg.style.height = '50px';
        tempImg.style.zIndex = '9999';
        tempImg.style.pointerEvents = 'none';
        tempImg.style.borderRadius = '50%';
        tempImg.style.transition = 'all 0.8s ease';
        
        const rect = productImg.getBoundingClientRect();
        tempImg.style.left = rect.left + 'px';
        tempImg.style.top = rect.top + 'px';
        
        document.body.appendChild(tempImg);
        
        // Animar hacia el carrito
        setTimeout(() => {
            const cartRect = cartIcon.getBoundingClientRect();
            tempImg.style.left = cartRect.left + 'px';
            tempImg.style.top = cartRect.top + 'px';
            tempImg.style.transform = 'scale(0)';
            tempImg.style.opacity = '0';
        }, 50);
        
        // Remover imagen temporal
        setTimeout(() => {
            document.body.removeChild(tempImg);
        }, 850);
    }
    
    async updateMiniCart() {
        const miniCart = document.querySelector('.mini-cart');
        if (!miniCart) return;
        
        try {
            const response = await fetch('/cart/mini-cart');
            const html = await response.text();
            miniCart.innerHTML = html;
        } catch (error) {
            console.error('Error updating mini cart:', error);
        }
    }
    
    initializeCart() {
        // Inicializar cantidad inputs con valores originales
        const quantityInputs = document.querySelectorAll('.cart-quantity-input');
        quantityInputs.forEach(input => {
            input.dataset.originalValue = input.value;
        });
        
        // Inicializar tooltips
        this.initializeTooltips();
    }
    
    initializeTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', this.showTooltip);
            element.addEventListener('mouseleave', this.hideTooltip);
        });
    }
    
    showTooltip(e) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = e.target.dataset.tooltip;
        
        document.body.appendChild(tooltip);
        
        const rect = e.target.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
        
        e.target.tooltipElement = tooltip;
    }
    
    hideTooltip(e) {
        if (e.target.tooltipElement) {
            document.body.removeChild(e.target.tooltipElement);
            e.target.tooltipElement = null;
        }
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    getNotificationIcon(type) {
        const icons = {
            success: 'check',
            error: 'times',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
}

// Clase para gestión de productos
class ProductManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initializeProductGallery();
        this.initializeProductFilters();
    }
    
    bindEvents() {
        // Vista rápida de productos
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-quick-view') || e.target.closest('.btn-quick-view')) {
                e.preventDefault();
                const btn = e.target.matches('.btn-quick-view') ? e.target : e.target.closest('.btn-quick-view');
                this.showQuickView(btn.dataset.productId);
            }
        });
        
        // Lista de deseos
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-wishlist') || e.target.closest('.btn-wishlist')) {
                e.preventDefault();
                const btn = e.target.matches('.btn-wishlist') ? e.target : e.target.closest('.btn-wishlist');
                this.toggleWishlist(btn);
            }
        });
        
        // Comparar productos
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-compare') || e.target.closest('.btn-compare')) {
                e.preventDefault();
                const btn = e.target.matches('.btn-compare') ? e.target : e.target.closest('.btn-compare');
                this.toggleCompare(btn);
            }
        });
        
        // Filtros de productos
        document.addEventListener('change', (e) => {
            if (e.target.matches('.product-filter')) {
                this.applyFilters();
            }
        });
    }
    
    async showQuickView(productId) {
        if (!productId) return;
        
        try {
            const response = await fetch(`/store/quick-view?id=${productId}`);
            const data = await response.json();
            
            if (data.success) {
                this.displayQuickViewModal(data.product);
            } else {
                shoppingCart.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error loading quick view:', error);
            shoppingCart.showNotification('Error al cargar el producto', 'error');
        }
    }
    
    displayQuickViewModal(product) {
        const modal = document.getElementById('quick-view-modal') || this.createQuickViewModal();
        
        const modalContent = modal.querySelector('.modal-content');
        modalContent.innerHTML = `
            <div class="modal-header">
                <h3>${product.name}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="product-quick-view">
                    <div class="product-image">
                        <img src="${product.images[0] || '/images/default-product.jpg'}" alt="${product.name}">
                    </div>
                    <div class="product-info">
                        <div class="product-price">
                            ${product.sale_price ? 
                                `<span class="original-price">S/ ${product.price}</span>
                                 <span class="sale-price">S/ ${product.sale_price}</span>` :
                                `<span class="current-price">S/ ${product.price}</span>`
                            }
                        </div>
                        <div class="product-rating">
                            ${this.generateStars(product.avg_rating)}
                            <span class="rating-count">(${product.reviews_count} reseñas)</span>
                        </div>
                        <div class="product-description">
                            <p>${product.short_description}</p>
                        </div>
                        <div class="product-stock">
                            ${product.stock_quantity > 0 ? 
                                `<span class="in-stock">✓ En stock (${product.stock_quantity} disponibles)</span>` :
                                `<span class="out-of-stock">✗ Agotado</span>`
                            }
                        </div>
                        <div class="product-actions">
                            <div class="quantity-selector">
                                <button class="quantity-btn minus">-</button>
                                <input type="number" class="quantity-input" value="1" min="1" max="${product.stock_quantity}">
                                <button class="quantity-btn plus">+</button>
                            </div>
                            <button class="btn-add-cart btn-primary" data-product-id="${product.id}" ${product.stock_quantity <= 0 ? 'disabled' : ''}>
                                <i class="fas fa-shopping-cart"></i>
                                ${product.stock_quantity > 0 ? 'Añadir al carrito' : 'Agotado'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        modal.style.display = 'block';
        
        // Configurar eventos del modal
        this.setupQuickViewEvents(modal);
    }
    
    createQuickViewModal() {
        const modal = document.createElement('div');
        modal.id = 'quick-view-modal';
        modal.className = 'modal';
        modal.innerHTML = '<div class="modal-content"></div>';
        
        document.body.appendChild(modal);
        
        // Cerrar modal al hacer clic fuera
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        return modal;
    }
    
    setupQuickViewEvents(modal) {
        // Cerrar modal
        const closeBtn = modal.querySelector('.modal-close');
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
        
        // Selectores de cantidad
        const minusBtn = modal.querySelector('.quantity-btn.minus');
        const plusBtn = modal.querySelector('.quantity-btn.plus');
        const quantityInput = modal.querySelector('.quantity-input');
        
        minusBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        plusBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value);
            const maxValue = parseInt(quantityInput.max);
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        });
    }
    
    generateStars(rating) {
        let stars = '';
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 !== 0;
        
        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="fas fa-star"></i>';
        }
        
        if (hasHalfStar) {
            stars += '<i class="fas fa-star-half-alt"></i>';
        }
        
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
        for (let i = 0; i < emptyStars; i++) {
            stars += '<i class="far fa-star"></i>';
        }
        
        return stars;
    }
    
    async toggleWishlist(btn) {
        const productId = btn.dataset.productId;
        const isInWishlist = btn.classList.contains('in-wishlist');
        
        try {
            const response = await fetch(isInWishlist ? '/store/remove-from-wishlist' : '/store/add-to-wishlist', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    product_id: productId
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                btn.classList.toggle('in-wishlist');
                btn.innerHTML = isInWishlist ? '<i class="far fa-heart"></i>' : '<i class="fas fa-heart"></i>';
                shoppingCart.showNotification(data.message, 'success');
            } else {
                shoppingCart.showNotification(data.error, 'error');
            }
        } catch (error) {
            console.error('Error toggling wishlist:', error);
            shoppingCart.showNotification('Error al actualizar la lista de deseos', 'error');
        }
    }
    
    toggleCompare(btn) {
        const productId = btn.dataset.productId;
        const isInCompare = btn.classList.contains('in-compare');
        
        let compareList = JSON.parse(localStorage.getItem('compareList') || '[]');
        
        if (isInCompare) {
            // Remover de comparación
            compareList = compareList.filter(id => id !== productId);
            btn.classList.remove('in-compare');
            btn.innerHTML = '<i class="fas fa-balance-scale"></i>';
        } else {
            // Añadir a comparación (máximo 4 productos)
            if (compareList.length >= 4) {
                shoppingCart.showNotification('Máximo 4 productos para comparar', 'warning');
                return;
            }
            
            compareList.push(productId);
            btn.classList.add('in-compare');
            btn.innerHTML = '<i class="fas fa-balance-scale"></i>';
        }
        
        localStorage.setItem('compareList', JSON.stringify(compareList));
        this.updateCompareCounter(compareList.length);
        
        shoppingCart.showNotification(
            isInCompare ? 'Producto removido de comparación' : 'Producto añadido a comparación',
            'info'
        );
    }
    
    updateCompareCounter(count) {
        const counters = document.querySelectorAll('.compare-counter');
        counters.forEach(counter => {
            counter.textContent = count;
            counter.style.display = count > 0 ? 'inline' : 'none';
        });
    }
    
    initializeProductGallery() {
        const galleryThumbnails = document.querySelectorAll('.gallery-thumbnail');
        const mainImage = document.querySelector('.main-product-image');
        
        if (!mainImage) return;
        
        galleryThumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Remover clase active de todos los thumbnails
                galleryThumbnails.forEach(thumb => thumb.classList.remove('active'));
                
                // Añadir clase active al thumbnail clickeado
                thumbnail.classList.add('active');
                
                // Cambiar imagen principal
                mainImage.src = thumbnail.href;
                mainImage.alt = thumbnail.querySelector('img').alt;
            });
        });
    }
    
    initializeProductFilters() {
        const filterForm = document.getElementById('product-filters');
        if (!filterForm) return;
        
        const filters = filterForm.querySelectorAll('.product-filter');
        filters.forEach(filter => {
            filter.addEventListener('change', () => {
                this.applyFilters();
            });
        });
    }
    
    applyFilters() {
        const filterForm = document.getElementById('product-filters');
        if (!filterForm) return;
        
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        // Mantener la URL actual y añadir filtros
        const currentUrl = new URL(window.location);
        
        // Limpiar filtros existentes
        currentUrl.searchParams.delete('category');
        currentUrl.searchParams.delete('brand');
        currentUrl.searchParams.delete('price_min');
        currentUrl.searchParams.delete('price_max');
        currentUrl.searchParams.delete('sort');
        
        // Añadir nuevos filtros
        for (const [key, value] of params) {
            if (value) {
                currentUrl.searchParams.set(key, value);
            }
        }
        
        // Navegar a la nueva URL
        window.location.href = currentUrl.toString();
    }
    
    initAjaxFilters() {
        // Interceptar envío de formularios de filtros
        const filtersForm = document.getElementById('filters-form');
        if (filtersForm) {
            filtersForm.addEventListener('change', (e) => {
                if (e.target.matches('select, input[type="checkbox"]')) {
                    this.loadProductsAjax(filtersForm);
                }
            });
        }
        
        // Interceptar filtros de precio
        const priceFilterBtn = document.querySelector('.price-filter-btn');
        if (priceFilterBtn) {
            priceFilterBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.loadProductsAjax(filtersForm);
            });
        }
    }
    
    async loadProductsAjax(form) {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        // Agregar parámetro para indicar que es una petición AJAX
        params.append('ajax', '1');
        
        try {
            // Mostrar loading
            const productsGrid = document.getElementById('products-grid');
            if (productsGrid) {
                productsGrid.style.opacity = '0.5';
                productsGrid.style.pointerEvents = 'none';
            }
            
            const response = await fetch(window.location.pathname + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const html = await response.text();
                
                // Crear un elemento temporal para parsear el HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                // Extraer solo la sección de productos
                const newProductsGrid = tempDiv.querySelector('#products-grid');
                const newPagination = tempDiv.querySelector('.pagination-wrapper');
                const newResultsInfo = tempDiv.querySelector('.results-info');
                
                if (newProductsGrid && productsGrid) {
                    productsGrid.innerHTML = newProductsGrid.innerHTML;
                    productsGrid.style.opacity = '1';
                    productsGrid.style.pointerEvents = 'auto';
                }
                
                // Actualizar paginación si existe
                const currentPagination = document.querySelector('.pagination-wrapper');
                if (newPagination && currentPagination) {
                    currentPagination.innerHTML = newPagination.innerHTML;
                } else if (!newPagination && currentPagination) {
                    currentPagination.remove();
                }
                
                // Actualizar información de resultados
                const currentResultsInfo = document.querySelector('.results-info');
                if (newResultsInfo && currentResultsInfo) {
                    currentResultsInfo.innerHTML = newResultsInfo.innerHTML;
                }
                
                // Actualizar URL sin recargar la página
                const newUrl = window.location.pathname + '?' + params.toString().replace('ajax=1&', '').replace('&ajax=1', '').replace('ajax=1', '');
                window.history.pushState({}, '', newUrl);
                
            } else {
                console.error('Error al cargar productos:', response.statusText);
                if (productsGrid) {
                    productsGrid.style.opacity = '1';
                    productsGrid.style.pointerEvents = 'auto';
                }
            }
        } catch (error) {
            console.error('Error en la petición AJAX:', error);
            if (productsGrid) {
                productsGrid.style.opacity = '1';
                productsGrid.style.pointerEvents = 'auto';
            }
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.shoppingCart = new ShoppingCart();
    window.productManager = new ProductManager();
    
    // Inicializar StoreManager si estamos en la página de tienda
    if (document.querySelector('.store-page')) {
        window.storeManager = new StoreManager();
        window.storeManager.initAjaxFilters();
    }
});
