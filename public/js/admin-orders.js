/**
 * Admin Orders JavaScript - STYLOFITNESS
 * Funcionalidades para la gestión de pedidos en el panel de administración
 */

// Variables globales
let currentOrderId = null;
let ordersTable = null;

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Ocultar pantalla de carga
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        loadingScreen.style.display = 'none';
    }
    
    initializeOrdersPage();
});

function initializeOrdersPage() {
    // Inicializar tooltips
    initializeTooltips();
    
    // Configurar eventos de filtros
    setupFilterEvents();
    
    // Configurar eventos de tabla
    setupTableEvents();
    
    // Auto-refresh cada 30 segundos
    setInterval(refreshOrdersData, 30000);
}

function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[title]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function setupFilterEvents() {
    const filterForm = document.querySelector('.filters-form');
    if (filterForm) {
        // Auto-submit en cambios de select
        const selects = filterForm.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });
        
        // Búsqueda con delay
        const searchInput = filterForm.querySelector('input[name="search"]');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterForm.submit();
                }, 500);
            });
        }
    }
}

function setupTableEvents() {
    // Configurar ordenamiento de tabla
    const tableHeaders = document.querySelectorAll('.admin-table th[data-sort]');
    tableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            sortTable(this.dataset.sort);
        });
    });
    
    // Configurar selección múltiple
    const selectAllCheckbox = document.querySelector('#selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            toggleSelectAll(this.checked);
        });
    }
    
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
}

function viewOrder(orderId) {
    currentOrderId = orderId;
    showLoading();
    
    fetch(`/admin/orders/view/${orderId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar el pedido');
            }
            return response.json();
        })
        .then(data => {
            displayOrderDetails(data);
            document.getElementById('orderModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar los detalles del pedido', 'error');
        })
        .finally(() => {
            hideLoading();
        });
}

function displayOrderDetails(order) {
    const modalBody = document.getElementById('orderModalBody');
    
    const html = `
        <div class="order-details">
            <div class="order-header">
                <div class="order-info">
                    <h4>Pedido #${order.order_number}</h4>
                    <p class="order-date">Fecha: ${formatDate(order.created_at)}</p>
                </div>
                <div class="order-status">
                    <span class="status-badge status-${order.status}">${order.status}</span>
                    <span class="payment-badge payment-${order.payment_status}">${order.payment_status}</span>
                </div>
            </div>
            
            <div class="customer-section">
                <h5>Información del Cliente</h5>
                <div class="customer-details">
                    <p><strong>Nombre:</strong> ${order.first_name} ${order.last_name}</p>
                    <p><strong>Email:</strong> ${order.email}</p>
                    <p><strong>Teléfono:</strong> ${order.phone || 'No especificado'}</p>
                </div>
            </div>
            
            <div class="items-section">
                <h5>Productos</h5>
                <div class="items-list">
                    ${order.items.map(item => `
                        <div class="order-item">
                            <div class="item-image">
                                <img src="${item.product_image || '/images/placeholder.jpg'}" alt="${item.product_name}">
                            </div>
                            <div class="item-details">
                                <h6>${item.product_name}</h6>
                                <p>Cantidad: ${item.quantity}</p>
                                <p>Precio: S/. ${parseFloat(item.price).toFixed(2)}</p>
                            </div>
                            <div class="item-total">
                                S/. ${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            
            <div class="order-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>S/. ${parseFloat(order.subtotal).toFixed(2)}</span>
                </div>
                <div class="summary-row">
                    <span>Envío:</span>
                    <span>S/. ${parseFloat(order.shipping_amount || 0).toFixed(2)}</span>
                </div>
                <div class="summary-row">
                    <span>Impuestos:</span>
                    <span>S/. ${parseFloat(order.tax_amount || 0).toFixed(2)}</span>
                </div>
                <div class="summary-row total">
                    <span><strong>Total:</strong></span>
                    <span><strong>S/. ${parseFloat(order.total_amount).toFixed(2)}</strong></span>
                </div>
            </div>
            
            <div class="order-actions">
                <button class="btn btn-primary" onclick="editOrder(${order.id})">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn btn-secondary" onclick="printOrder(${order.id})">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <button class="btn btn-info" onclick="sendOrderEmail(${order.id})">
                    <i class="fas fa-envelope"></i> Enviar Email
                </button>
            </div>
        </div>
    `;
    
    modalBody.innerHTML = html;
}

function editOrder(orderId) {
    window.location.href = `/admin/orders/edit/${orderId}`;
}

function printOrder(orderId) {
    const printWindow = window.open(`/admin/orders/print/${orderId}`, '_blank');
    if (printWindow) {
        printWindow.focus();
    } else {
        showNotification('Por favor, permite las ventanas emergentes para imprimir', 'warning');
    }
}

function sendOrderEmail(orderId) {
    if (!confirm('¿Enviar email de confirmación al cliente?')) {
        return;
    }
    
    showLoading();
    
    fetch(`/admin/orders/send-email/${orderId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Email enviado correctamente', 'success');
        } else {
            showNotification(data.message || 'Error al enviar email', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al enviar email', 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

function updateOrderStatus(orderId, newStatus) {
    if (!confirm(`¿Cambiar estado del pedido a "${newStatus}"?`)) {
        return;
    }
    
    showLoading();
    
    fetch(`/admin/orders/update-status/${orderId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Estado actualizado correctamente', 'success');
            refreshOrdersData();
        } else {
            showNotification(data.message || 'Error al actualizar estado', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al actualizar estado', 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
    currentOrderId = null;
}

function exportOrders() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    
    showLoading();
    
    const exportUrl = '/admin/orders/export?' + params.toString();
    
    // Crear enlace temporal para descarga
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `pedidos_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    hideLoading();
    showNotification('Exportación iniciada', 'success');
}

function refreshOrdersData() {
    // Recargar datos sin refrescar la página
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('ajax', '1');
    
    fetch(currentUrl.toString())
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableBody = doc.querySelector('.admin-table tbody');
            
            if (newTableBody) {
                document.querySelector('.admin-table tbody').innerHTML = newTableBody.innerHTML;
                setupTableEvents();
            }
        })
        .catch(error => {
            console.error('Error al actualizar datos:', error);
        });
}

function sortTable(column) {
    const url = new URL(window.location);
    const currentSort = url.searchParams.get('sort');
    const currentOrder = url.searchParams.get('order');
    
    if (currentSort === column && currentOrder === 'asc') {
        url.searchParams.set('order', 'desc');
    } else {
        url.searchParams.set('order', 'asc');
    }
    
    url.searchParams.set('sort', column);
    window.location.href = url.toString();
}

function toggleSelectAll(checked) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = checked;
    });
    updateBulkActions();
}

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkActions = document.querySelector('.bulk-actions');
    
    if (bulkActions) {
        bulkActions.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
    }
}

function bulkUpdateStatus(newStatus) {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const orderIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (orderIds.length === 0) {
        showNotification('Selecciona al menos un pedido', 'warning');
        return;
    }
    
    if (!confirm(`¿Cambiar estado de ${orderIds.length} pedidos a "${newStatus}"?`)) {
        return;
    }
    
    showLoading();
    
    fetch('/admin/orders/bulk-update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            order_ids: orderIds, 
            status: newStatus 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`${data.updated} pedidos actualizados`, 'success');
            refreshOrdersData();
        } else {
            showNotification(data.message || 'Error en actualización masiva', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error en actualización masiva', 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

// Funciones de utilidad
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-PE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN'
    }).format(amount);
}

function showLoading() {
    const loader = document.querySelector('.loading-overlay') || createLoader();
    loader.style.display = 'flex';
}

function hideLoading() {
    const loader = document.querySelector('.loading-overlay');
    if (loader) {
        loader.style.display = 'none';
    }
}

function createLoader() {
    const loader = document.createElement('div');
    loader.className = 'loading-overlay';
    loader.innerHTML = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Cargando...</p>
        </div>
    `;
    document.body.appendChild(loader);
    return loader;
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove después de 5 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function showTooltip(event) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = event.target.getAttribute('title');
    
    document.body.appendChild(tooltip);
    
    const rect = event.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
    
    event.target.setAttribute('data-original-title', event.target.getAttribute('title'));
    event.target.removeAttribute('title');
    event.target._tooltip = tooltip;
}

function hideTooltip(event) {
    if (event.target._tooltip) {
        event.target._tooltip.remove();
        event.target._tooltip = null;
        event.target.setAttribute('title', event.target.getAttribute('data-original-title'));
        event.target.removeAttribute('data-original-title');
    }
}

// Event listeners globales
window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target === modal) {
        closeOrderModal();
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeOrderModal();
    }
    
    if (event.ctrlKey && event.key === 'f') {
        event.preventDefault();
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.focus();
        }
    }
});