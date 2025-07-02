<?php
// Verificar autenticación de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

$pageTitle = 'Gestión de Ofertas Especiales - Admin STYLOFITNESS';
?>

<div class="admin-container">
    <div class="admin-header">
        <div class="admin-header-content">
            <h1><i class="fas fa-tags"></i> Gestión de Ofertas Especiales</h1>
            <p>Administra las ofertas especiales que se muestran en la landing page</p>
        </div>
        <div class="admin-header-actions">
            <button class="btn btn-primary" onclick="openOfferModal()">
                <i class="fas fa-plus"></i> Nueva Oferta
            </button>
            <button class="btn btn-secondary" onclick="refreshOffers()">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-content">
                <h3 id="total-offers">0</h3>
                <p>Total de Ofertas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-content">
                <h3 id="active-offers">0</h3>
                <p>Ofertas Activas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <h3 id="featured-offers">0</h3>
                <p>Ofertas Destacadas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-content">
                <h3 id="avg-discount">0%</h3>
                <p>Descuento Promedio</p>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="admin-filters">
        <div class="filter-group">
            <input type="text" id="search-offers" placeholder="Buscar ofertas..." class="form-control">
        </div>
        <div class="filter-group">
            <select id="filter-status" class="form-control">
                <option value="">Todos los estados</option>
                <option value="1">Activas</option>
                <option value="0">Inactivas</option>
            </select>
        </div>
        <div class="filter-group">
            <select id="filter-type" class="form-control">
                <option value="">Todos los tipos</option>
                <option value="percentage">Porcentaje</option>
                <option value="fixed">Monto fijo</option>
                <option value="bogo">2x1</option>
                <option value="free_shipping">Envío gratis</option>
            </select>
        </div>
    </div>

    <!-- Tabla de ofertas -->
    <div class="admin-table-container">
        <table class="admin-table" id="offers-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Descuento</th>
                    <th>Válida hasta</th>
                    <th>Estado</th>
                    <th>Destacada</th>
                    <th>Orden</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="offers-tbody">
                <!-- Los datos se cargarán dinámicamente -->
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="admin-pagination" id="offers-pagination">
        <!-- La paginación se generará dinámicamente -->
    </div>
</div>

<!-- Modal para crear/editar oferta -->
<div class="modal fade" id="offerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="offerModalTitle">Nueva Oferta Especial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="offerForm">
                <div class="modal-body">
                    <input type="hidden" id="offer-id" name="id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="offer-title" class="form-label">Título de la Oferta *</label>
                                <input type="text" class="form-control" id="offer-title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="offer-type" class="form-label">Tipo de Oferta *</label>
                                <select class="form-control" id="offer-type" name="offer_type" required>
                                    <option value="percentage">Porcentaje</option>
                                    <option value="fixed">Monto fijo</option>
                                    <option value="bogo">2x1</option>
                                    <option value="free_shipping">Envío gratis</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="offer-description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="offer-description" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="offer-discount-value" class="form-label">Valor del Descuento *</label>
                                <input type="number" class="form-control" id="offer-discount-value" name="discount_value" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="offer-min-amount" class="form-label">Monto Mínimo</label>
                                <input type="number" class="form-control" id="offer-min-amount" name="min_amount" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="offer-max-uses" class="form-label">Usos Máximos</label>
                                <input type="number" class="form-control" id="offer-max-uses" name="max_uses">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="offer-start-date" class="form-label">Fecha de Inicio *</label>
                                <input type="datetime-local" class="form-control" id="offer-start-date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="offer-end-date" class="form-label">Fecha de Fin *</label>
                                <input type="datetime-local" class="form-control" id="offer-end-date" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="offer-image" class="form-label">Imagen de la Oferta</label>
                                <input type="file" class="form-control" id="offer-image" name="image" accept="image/*">
                                <div id="current-image-preview" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="offer-display-order" class="form-label">Orden de Visualización</label>
                                <input type="number" class="form-control" id="offer-display-order" name="display_order" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="offer-is-featured" name="is_featured">
                                <label class="form-check-label" for="offer-is-featured">
                                    Oferta Destacada
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="offer-is-active" name="is_active" checked>
                                <label class="form-check-label" for="offer-is-active">
                                    Oferta Activa
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="offer-terms" class="form-label">Términos y Condiciones</label>
                        <textarea class="form-control" id="offer-terms" name="terms_conditions" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Oferta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteOfferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta oferta especial?</p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteOffer">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.admin-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e9ecef;
}

.admin-header h1 {
    color: #2c3e50;
    margin: 0;
    font-size: 2rem;
}

.admin-header p {
    color: #6c757d;
    margin: 5px 0 0 0;
}

.admin-header-actions {
    display: flex;
    gap: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-content h3 {
    margin: 0;
    font-size: 2rem;
    color: #2c3e50;
}

.stat-content p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.admin-filters {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.admin-table-container {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    border-bottom: 2px solid #e9ecef;
}

.admin-table td {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}

.admin-table tbody tr:hover {
    background-color: #f8f9fa;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.featured-badge {
    background-color: #fff3cd;
    color: #856404;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.8rem;
}

.admin-pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.modal-lg {
    max-width: 800px;
}

#current-image-preview img {
    max-width: 100px;
    max-height: 100px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 8px;
}

@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .admin-filters {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-table-container {
        overflow-x: auto;
    }
}
</style>

<script>
// Variables globales
let currentPage = 1;
let currentFilters = {};
let editingOfferId = null;

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    loadOffers();
    loadStats();
    setupEventListeners();
});

// Configurar event listeners
function setupEventListeners() {
    // Búsqueda
    document.getElementById('search-offers').addEventListener('input', debounce(function() {
        currentFilters.search = this.value;
        currentPage = 1;
        loadOffers();
    }, 300));
    
    // Filtros
    document.getElementById('filter-status').addEventListener('change', function() {
        currentFilters.status = this.value;
        currentPage = 1;
        loadOffers();
    });
    
    document.getElementById('filter-type').addEventListener('change', function() {
        currentFilters.type = this.value;
        currentPage = 1;
        loadOffers();
    });
    
    // Formulario de oferta
    document.getElementById('offerForm').addEventListener('submit', handleOfferSubmit);
    
    // Confirmación de eliminación
    document.getElementById('confirmDeleteOffer').addEventListener('click', function() {
        if (editingOfferId) {
            deleteOffer(editingOfferId);
        }
    });
}

// Cargar ofertas
function loadOffers() {
    const params = new URLSearchParams({
        page: currentPage,
        ...currentFilters
    });
    
    fetch(`/api/admin/special-offers?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderOffersTable(data.offers);
                renderPagination(data.pagination);
            } else {
                showAlert('Error al cargar las ofertas', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error de conexión', 'error');
        });
}

// Cargar estadísticas
function loadStats() {
    fetch('/api/admin/special-offers/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('total-offers').textContent = data.stats.total || 0;
                document.getElementById('active-offers').textContent = data.stats.active || 0;
                document.getElementById('featured-offers').textContent = data.stats.featured || 0;
                document.getElementById('avg-discount').textContent = (data.stats.avg_discount || 0) + '%';
            }
        })
        .catch(error => console.error('Error loading stats:', error));
}

// Renderizar tabla de ofertas
function renderOffersTable(offers) {
    const tbody = document.getElementById('offers-tbody');
    
    if (!offers || offers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No se encontraron ofertas</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = offers.map(offer => `
        <tr>
            <td>${offer.id}</td>
            <td>
                <strong>${offer.title}</strong>
                ${offer.description ? `<br><small class="text-muted">${offer.description.substring(0, 50)}...</small>` : ''}
            </td>
            <td>
                <span class="badge bg-info">${getOfferTypeLabel(offer.offer_type)}</span>
            </td>
            <td>
                ${formatDiscount(offer.offer_type, offer.discount_value)}
            </td>
            <td>
                ${formatDate(offer.end_date)}
                ${isOfferExpired(offer.end_date) ? '<br><small class="text-danger">Expirada</small>' : ''}
            </td>
            <td>
                <span class="status-badge ${offer.is_active ? 'status-active' : 'status-inactive'}">
                    ${offer.is_active ? 'Activa' : 'Inactiva'}
                </span>
            </td>
            <td>
                ${offer.is_featured ? '<span class="featured-badge">Destacada</span>' : '-'}
            </td>
            <td>${offer.display_order}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-primary" onclick="editOffer(${offer.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm ${offer.is_active ? 'btn-warning' : 'btn-success'}" 
                            onclick="toggleOfferStatus(${offer.id})" 
                            title="${offer.is_active ? 'Desactivar' : 'Activar'}">
                        <i class="fas ${offer.is_active ? 'fa-eye-slash' : 'fa-eye'}"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="confirmDeleteOffer(${offer.id})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Funciones auxiliares
function getOfferTypeLabel(type) {
    const labels = {
        'percentage': 'Porcentaje',
        'fixed': 'Monto fijo',
        'bogo': '2x1',
        'free_shipping': 'Envío gratis'
    };
    return labels[type] || type;
}

function formatDiscount(type, value) {
    switch(type) {
        case 'percentage':
            return value + '%';
        case 'fixed':
            return '$' + parseFloat(value).toFixed(2);
        case 'bogo':
            return '2x1';
        case 'free_shipping':
            return 'Envío gratis';
        default:
            return value;
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function isOfferExpired(endDate) {
    return new Date(endDate) < new Date();
}

// Funciones de modal
function openOfferModal(offerId = null) {
    editingOfferId = offerId;
    const modal = new bootstrap.Modal(document.getElementById('offerModal'));
    const title = document.getElementById('offerModalTitle');
    
    if (offerId) {
        title.textContent = 'Editar Oferta Especial';
        loadOfferData(offerId);
    } else {
        title.textContent = 'Nueva Oferta Especial';
        document.getElementById('offerForm').reset();
        document.getElementById('offer-is-active').checked = true;
    }
    
    modal.show();
}

function editOffer(id) {
    openOfferModal(id);
}

function loadOfferData(id) {
    fetch(`/api/admin/special-offers/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const offer = data.offer;
                document.getElementById('offer-id').value = offer.id;
                document.getElementById('offer-title').value = offer.title;
                document.getElementById('offer-description').value = offer.description || '';
                document.getElementById('offer-type').value = offer.offer_type;
                document.getElementById('offer-discount-value').value = offer.discount_value;
                document.getElementById('offer-min-amount').value = offer.min_amount || '';
                document.getElementById('offer-max-uses').value = offer.max_uses || '';
                document.getElementById('offer-start-date').value = formatDateTimeLocal(offer.start_date);
                document.getElementById('offer-end-date').value = formatDateTimeLocal(offer.end_date);
                document.getElementById('offer-display-order').value = offer.display_order;
                document.getElementById('offer-is-featured').checked = offer.is_featured;
                document.getElementById('offer-is-active').checked = offer.is_active;
                document.getElementById('offer-terms').value = offer.terms_conditions || '';
                
                // Mostrar imagen actual si existe
                if (offer.image) {
                    document.getElementById('current-image-preview').innerHTML = 
                        `<img src="${offer.image}" alt="Imagen actual">`;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al cargar los datos de la oferta', 'error');
        });
}

function handleOfferSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const url = editingOfferId ? `/api/admin/special-offers/${editingOfferId}` : '/api/admin/special-offers';
    const method = editingOfferId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(editingOfferId ? 'Oferta actualizada correctamente' : 'Oferta creada correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('offerModal')).hide();
            loadOffers();
            loadStats();
        } else {
            showAlert(data.message || 'Error al guardar la oferta', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error de conexión', 'error');
    });
}

// Funciones de acciones
function toggleOfferStatus(id) {
    fetch(`/api/admin/special-offers/${id}/toggle-status`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Estado actualizado correctamente', 'success');
            loadOffers();
            loadStats();
        } else {
            showAlert('Error al actualizar el estado', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error de conexión', 'error');
    });
}

function confirmDeleteOffer(id) {
    editingOfferId = id;
    const modal = new bootstrap.Modal(document.getElementById('deleteOfferModal'));
    modal.show();
}

function deleteOffer(id) {
    fetch(`/api/admin/special-offers/${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Oferta eliminada correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('deleteOfferModal')).hide();
            loadOffers();
            loadStats();
        } else {
            showAlert('Error al eliminar la oferta', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error de conexión', 'error');
    });
}

function refreshOffers() {
    loadOffers();
    loadStats();
    showAlert('Datos actualizados', 'info');
}

// Funciones auxiliares
function formatDateTimeLocal(dateString) {
    const date = new Date(dateString);
    return date.toISOString().slice(0, 16);
}

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

function showAlert(message, type = 'info') {
    // Implementar sistema de alertas
    console.log(`${type.toUpperCase()}: ${message}`);
    
    // Crear y mostrar alerta temporal
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

function renderPagination(pagination) {
    // Implementar paginación si es necesario
    const container = document.getElementById('offers-pagination');
    if (pagination && pagination.total_pages > 1) {
        // Generar controles de paginación
        container.innerHTML = `
            <nav>
                <ul class="pagination">
                    ${pagination.current_page > 1 ? `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${pagination.current_page - 1})">Anterior</a></li>` : ''}
                    ${Array.from({length: pagination.total_pages}, (_, i) => i + 1).map(page => `
                        <li class="page-item ${page === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="changePage(${page})">${page}</a>
                        </li>
                    `).join('')}
                    ${pagination.current_page < pagination.total_pages ? `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${pagination.current_page + 1})">Siguiente</a></li>` : ''}
                </ul>
            </nav>
        `;
    } else {
        container.innerHTML = '';
    }
}

function changePage(page) {
    currentPage = page;
    loadOffers();
}
</script>