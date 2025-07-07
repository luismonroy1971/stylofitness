/**
 * Admin Products Management - STYLOFITNESS
 * Manejo de filtros y paginación AJAX para productos
 */

class AdminProductsManager {
    constructor() {
        this.currentPage = 1;
        this.isLoading = false;
        this.filters = {};
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeFilters();
    }

    bindEvents() {
        // Evento para el formulario de filtros
        const filterForm = document.querySelector('.filters-form');
        if (filterForm) {
            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.applyFilters();
            });

            // Filtrado en tiempo real para el campo de búsqueda
            const searchInput = document.getElementById('search');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.applyFilters();
                    }, 500);
                });
            }

            // Filtrado automático para selects
            const selects = filterForm.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', () => {
                    this.applyFilters();
                });
            });
        }

        // Eventos para paginación
        document.addEventListener('click', (e) => {
            if (e.target.matches('.pagination a')) {
                e.preventDefault();
                const url = new URL(e.target.href);
                const page = url.searchParams.get('page');
                if (page) {
                    this.loadPage(parseInt(page));
                }
            }
        });
    }

    initializeFilters() {
        const filterForm = document.querySelector('.filters-form');
        if (filterForm) {
            const formData = new FormData(filterForm);
            this.filters = {};
            
            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    this.filters[key] = value;
                }
            }
        }
    }

    applyFilters() {
        if (this.isLoading) return;
        
        this.currentPage = 1;
        this.collectFilters();
        this.loadProducts();
    }

    collectFilters() {
        const filterForm = document.querySelector('.filters-form');
        if (filterForm) {
            const formData = new FormData(filterForm);
            this.filters = {};
            
            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    this.filters[key] = value;
                }
            }
        }
    }

    loadPage(page) {
        if (this.isLoading) return;
        
        this.currentPage = page;
        this.loadProducts();
    }

    async loadProducts() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoading();

        try {
            const params = new URLSearchParams({
                ajax: '1',
                page: this.currentPage,
                ...this.filters
            });

            const response = await fetch(`/admin/products?${params.toString()}`);
            const data = await response.json();

            if (data.success) {
                this.updateProductsTable(data.products);
                this.updatePagination(data.pagination);
                this.updateURL();
            } else {
                this.showError('Error al cargar los productos');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('Error de conexión');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    updateProductsTable(products) {
        const tableContainer = document.querySelector('.products-table-container');
        if (!tableContainer) return;

        if (products.length === 0) {
            tableContainer.innerHTML = `
                <div class="no-products">
                    <p>No se encontraron productos con los filtros aplicados.</p>
                    <a href="/admin/products/create" class="btn-primary">Crear primer producto</a>
                </div>
            `;
            return;
        }

        let tableHTML = `
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Destacado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
        `;

        products.forEach(product => {
            const images = typeof product.images === 'string' ? JSON.parse(product.images || '[]') : (product.images || []);
            const mainImage = images.length > 0 ? images[0] : '/images/placeholder.jpg';
            const stockClass = product.stock_quantity > 20 ? 'stock-high' : 
                             (product.stock_quantity > 5 ? 'stock-medium' : 'stock-low');

            tableHTML += `
                <tr>
                    <td>
                        <img src="${this.escapeHtml(mainImage)}" 
                             alt="${this.escapeHtml(product.name)}" 
                             class="product-image">
                    </td>
                    <td>
                        <div class="product-name">${this.escapeHtml(product.name)}</div>
                        <div class="product-sku">SKU: ${this.escapeHtml(product.sku)}</div>
                    </td>
                    <td>
                        ${this.escapeHtml(product.category_name || 'Sin categoría')}
                    </td>
                    <td>
                        ${this.formatPrice(product)}
                    </td>
                    <td>
                        <span class="stock-badge ${stockClass}">
                            ${product.stock_quantity} unidades
                        </span>
                    </td>
                    <td>
                        <span class="status-badge ${product.is_active ? 'status-active' : 'status-inactive'}">
                            ${product.is_active ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge ${product.is_featured ? 'status-active' : 'status-inactive'}">
                            ${product.is_featured ? 'Sí' : 'No'}
                        </span>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <a href="/admin/products/edit/${product.id}" class="btn-sm btn-edit">
                                Editar
                            </a>
                            <button type="button" class="btn-sm btn-delete" 
                                    onclick="deleteProduct(${product.id})">
                                Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableHTML += `
                </tbody>
            </table>
        `;

        tableContainer.innerHTML = tableHTML;
    }

    updatePagination(pagination) {
        const existingPagination = document.querySelector('.pagination');
        if (existingPagination) {
            existingPagination.remove();
        }

        if (pagination.total_pages <= 1) return;

        const tableContainer = document.querySelector('.products-table-container');
        if (!tableContainer) return;

        let paginationHTML = '<div class="pagination">';

        // Botón anterior
        if (pagination.current_page > 1) {
            const params = new URLSearchParams({ page: pagination.current_page - 1, ...this.filters });
            paginationHTML += `<a href="?${params.toString()}">← Anterior</a>`;
        }

        // Números de página
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                paginationHTML += `<span class="current">${i}</span>`;
            } else {
                const params = new URLSearchParams({ page: i, ...this.filters });
                paginationHTML += `<a href="?${params.toString()}">${i}</a>`;
            }
        }

        // Botón siguiente
        if (pagination.current_page < pagination.total_pages) {
            const params = new URLSearchParams({ page: pagination.current_page + 1, ...this.filters });
            paginationHTML += `<a href="?${params.toString()}">Siguiente →</a>`;
        }

        paginationHTML += '</div>';
        tableContainer.insertAdjacentHTML('afterend', paginationHTML);
    }

    updateURL() {
        const params = new URLSearchParams({ page: this.currentPage, ...this.filters });
        const newURL = `${window.location.pathname}?${params.toString()}`;
        window.history.replaceState({}, '', newURL);
    }

    formatPrice(product) {
        if (product.sale_price && product.sale_price > 0) {
            return `
                <div class="price-display">$${this.numberFormat(product.sale_price, 2)}</div>
                <div class="sale-price">$${this.numberFormat(product.price, 2)}</div>
            `;
        } else {
            return `<div class="price-display">$${this.numberFormat(product.price, 2)}</div>`;
        }
    }

    showLoading() {
        const tableContainer = document.querySelector('.products-table-container');
        if (tableContainer) {
            tableContainer.style.opacity = '0.6';
            tableContainer.style.pointerEvents = 'none';
        }

        // Agregar indicador de carga si no existe
        if (!document.querySelector('.loading-indicator')) {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'loading-indicator';
            loadingDiv.innerHTML = '<div class="spinner"></div><span>Cargando...</span>';
            loadingDiv.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(255, 255, 255, 0.95);
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 1000;
                display: flex;
                align-items: center;
                gap: 10px;
            `;
            
            const spinner = loadingDiv.querySelector('.spinner');
            spinner.style.cssText = `
                width: 20px;
                height: 20px;
                border: 2px solid #f3f3f3;
                border-top: 2px solid #3498db;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            `;
            
            // Agregar animación CSS
            if (!document.querySelector('#spinner-style')) {
                const style = document.createElement('style');
                style.id = 'spinner-style';
                style.textContent = `
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            }
            
            document.body.appendChild(loadingDiv);
        }
    }

    hideLoading() {
        const tableContainer = document.querySelector('.products-table-container');
        if (tableContainer) {
            tableContainer.style.opacity = '1';
            tableContainer.style.pointerEvents = 'auto';
        }

        const loadingIndicator = document.querySelector('.loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.remove();
        }
    }

    showError(message) {
        // Remover alertas existentes
        const existingAlert = document.querySelector('.alert-error');
        if (existingAlert) {
            existingAlert.remove();
        }

        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.textContent = message;
        
        const pageHeader = document.querySelector('.page-header');
        if (pageHeader) {
            pageHeader.insertAdjacentElement('afterend', alertDiv);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    numberFormat(number, decimals) {
        return parseFloat(number).toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
}

// Función global para eliminar productos con AJAX
function deleteProduct(productId) {
    if (confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.')) {
        // Mostrar indicador de carga
        showLoadingIndicator('Eliminando producto...');
        
        const formData = new FormData();
        
        // Agregar token CSRF si existe
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.getAttribute('content'));
        }
        
        fetch(`/admin/products/delete/${productId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingIndicator();
            
            if (data.success) {
                showNotification(data.message, 'success');
                // Recargar la lista de productos sin refrescar la página
                if (window.adminProductsManager) {
                    window.adminProductsManager.loadProducts();
                } else {
                    // Fallback: regresar a la página anterior
                    window.history.back();
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            hideLoadingIndicator();
            console.error('Error:', error);
            showNotification('Error de conexión al eliminar el producto', 'error');
        });
    }
}

// Funciones auxiliares para notificaciones y carga
function showLoadingIndicator(message = 'Cargando...') {
    // Remover indicador existente
    hideLoadingIndicator();
    
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'global-loading-indicator';
    loadingDiv.innerHTML = `<div class="spinner"></div><span>${message}</span>`;
    loadingDiv.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.95);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    
    const spinner = loadingDiv.querySelector('.spinner');
    spinner.style.cssText = `
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    `;
    
    document.body.appendChild(loadingDiv);
}

function hideLoadingIndicator() {
    const loadingIndicator = document.getElementById('global-loading-indicator');
    if (loadingIndicator) {
        loadingIndicator.remove();
    }
}

function showNotification(message, type = 'info') {
    // Remover notificaciones existentes
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 10001;
        max-width: 400px;
        word-wrap: break-word;
        animation: slideInRight 0.3s ease-out;
    `;
    
    // Colores según el tipo
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    
    notification.style.backgroundColor = colors[type] || colors.info;
    
    // Agregar animación CSS si no existe
    if (!document.querySelector('#notification-style')) {
        const style = document.createElement('style');
        style.id = 'notification-style';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideInRight 0.3s ease-out reverse';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.adminProductsManager = new AdminProductsManager();
});

// Función para manejar formularios de actualización con AJAX
function handleProductUpdateForm() {
    const updateForm = document.querySelector('form[action*="/admin/products/"][method="POST"]');
    if (!updateForm || updateForm.dataset.ajaxHandled) return;
    
    updateForm.dataset.ajaxHandled = 'true';
    
    updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        showLoadingIndicator('Actualizando producto...');
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingIndicator();
            
            if (data.success) {
                showNotification(data.message, 'success');
                // Regresar a la página anterior después de un breve delay
                setTimeout(() => {
                    window.history.back();
                }, 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            hideLoadingIndicator();
            console.error('Error:', error);
            showNotification('Error de conexión al actualizar el producto', 'error');
        });
    });
}

// Inicializar el manejo de formularios cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    handleProductUpdateForm();
});