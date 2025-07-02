/**
 * JavaScript para gestión de Ofertas Especiales
 * StyleFitness Admin Panel
 */

class SpecialOffersManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.currentFilters = {};
        this.editingOfferId = null;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadOffers();
        this.loadStats();
    }

    bindEvents() {
        // Filtros
        document.getElementById('statusFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('searchFilter').addEventListener('input', this.debounce(() => this.applyFilters(), 300));
        
        // Formulario
        document.getElementById('offerForm').addEventListener('submit', (e) => this.handleFormSubmit(e));
        
        // Modal events
        $('#offerModal').on('hidden.bs.modal', () => this.resetForm());
        
        // Botón de confirmación de eliminación
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => this.confirmDelete());
    }

    async loadOffers() {
        try {
            this.showLoading();
            
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                ...this.currentFilters
            });

            const response = await fetch(`/admin/landing/special-offers/get?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderOffers(data.data.items);
                this.renderPagination(data.data.pagination);
                this.updateStats(data.stats);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cargar ofertas: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    renderOffers(offers) {
        const tbody = document.getElementById('offersTableBody');
        
        if (offers.length === 0) {
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
                    <strong>${this.escapeHtml(offer.title)}</strong>
                    ${offer.description ? `<br><small class="text-muted">${this.escapeHtml(offer.description.substring(0, 50))}...</small>` : ''}
                </td>
                <td>
                    <span class="badge badge-success">${offer.discount_percentage}% OFF</span>
                </td>
                <td>
                    ${offer.original_price ? `<del>$${offer.original_price}</del><br>` : ''}
                    ${offer.discounted_price ? `<strong>$${offer.discounted_price}</strong>` : '-'}
                </td>
                <td>
                    ${offer.image_url ? 
                        `<img src="/${offer.image_url}" alt="Oferta" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">` : 
                        '<i class="fas fa-image text-muted"></i>'
                    }
                </td>
                <td>
                    ${offer.valid_from ? this.formatDate(offer.valid_from) : '-'}<br>
                    ${offer.valid_until ? `<small>hasta ${this.formatDate(offer.valid_until)}</small>` : ''}
                </td>
                <td>${offer.display_order}</td>
                <td>
                    <span class="status-badge ${offer.is_active ? 'status-active' : 'status-inactive'}">
                        ${offer.is_active ? 'Activa' : 'Inactiva'}
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="specialOffersManager.editOffer(${offer.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm ${offer.is_active ? 'btn-warning' : 'btn-success'}" 
                                onclick="specialOffersManager.toggleStatus(${offer.id})" 
                                title="${offer.is_active ? 'Desactivar' : 'Activar'}">
                            <i class="fas fa-${offer.is_active ? 'eye-slash' : 'eye'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="specialOffersManager.deleteOffer(${offer.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        
        if (pagination.totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHtml = '<nav><ul class="pagination">';
        
        // Botón anterior
        paginationHtml += `
            <li class="page-item ${pagination.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="specialOffersManager.goToPage(${pagination.currentPage - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
        
        // Páginas
        for (let i = 1; i <= pagination.totalPages; i++) {
            if (i === 1 || i === pagination.totalPages || (i >= pagination.currentPage - 2 && i <= pagination.currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="specialOffersManager.goToPage(${i})">${i}</a>
                    </li>
                `;
            } else if (i === pagination.currentPage - 3 || i === pagination.currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        // Botón siguiente
        paginationHtml += `
            <li class="page-item ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="specialOffersManager.goToPage(${pagination.currentPage + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
        
        paginationHtml += '</ul></nav>';
        container.innerHTML = paginationHtml;
    }

    updateStats(stats) {
        document.getElementById('totalOffers').textContent = stats.total || 0;
        document.getElementById('activeOffers').textContent = stats.active || 0;
        document.getElementById('inactiveOffers').textContent = stats.inactive || 0;
        document.getElementById('avgDiscount').textContent = stats.avgDiscount ? `${stats.avgDiscount}%` : '0%';
    }

    applyFilters() {
        this.currentFilters = {
            status: document.getElementById('statusFilter').value,
            search: document.getElementById('searchFilter').value
        };
        this.currentPage = 1;
        this.loadOffers();
    }

    clearFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('searchFilter').value = '';
        this.currentFilters = {};
        this.currentPage = 1;
        this.loadOffers();
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadOffers();
    }

    async editOffer(id) {
        try {
            const response = await fetch(`/admin/landing/special-offers/get-single?id=${id}`);
            const data = await response.json();

            if (data.success) {
                this.populateForm(data.data);
                this.editingOfferId = id;
                $('#offerModal').modal('show');
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cargar oferta: ' + error.message);
        }
    }

    populateForm(offer) {
        document.getElementById('offerModalTitle').textContent = 'Editar Oferta';
        document.getElementById('offerId').value = offer.id;
        document.getElementById('offerTitle').value = offer.title;
        document.getElementById('offerDescription').value = offer.description || '';
        document.getElementById('discountPercentage').value = offer.discount_percentage;
        document.getElementById('originalPrice').value = offer.original_price || '';
        document.getElementById('discountedPrice').value = offer.discounted_price || '';
        document.getElementById('validFrom').value = offer.valid_from || '';
        document.getElementById('validUntil').value = offer.valid_until || '';
        document.getElementById('termsConditions').value = offer.terms_conditions || '';
        document.getElementById('displayOrder').value = offer.display_order;
        document.getElementById('offerActive').checked = offer.is_active;
        
        // Mostrar imagen actual si existe
        if (offer.image_url) {
            document.getElementById('currentImagePreview').style.display = 'block';
            document.getElementById('currentImage').src = '/' + offer.image_url;
        }
    }

    resetForm() {
        document.getElementById('offerModalTitle').textContent = 'Nueva Oferta';
        document.getElementById('offerForm').reset();
        document.getElementById('offerId').value = '';
        document.getElementById('currentImagePreview').style.display = 'none';
        this.editingOfferId = null;
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(e.target);
            const url = this.editingOfferId ? 
                '/admin/landing/special-offers/update' : 
                '/admin/landing/special-offers/create';

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                $('#offerModal').modal('hide');
                this.loadOffers();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al guardar oferta: ' + error.message);
        }
    }

    async toggleStatus(id) {
        try {
            const response = await fetch('/admin/landing/special-offers/toggle-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                this.loadOffers();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cambiar estado: ' + error.message);
        }
    }

    deleteOffer(id) {
        this.offerToDelete = id;
        $('#deleteModal').modal('show');
    }

    async confirmDelete() {
        if (!this.offerToDelete) return;

        try {
            const response = await fetch('/admin/landing/special-offers/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${this.offerToDelete}`
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                $('#deleteModal').modal('hide');
                this.loadOffers();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al eliminar oferta: ' + error.message);
        }

        this.offerToDelete = null;
    }

    // Métodos auxiliares
    debounce(func, wait) {
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

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES');
    }

    showLoading() {
        // Implementar indicador de carga
        document.body.style.cursor = 'wait';
    }

    hideLoading() {
        document.body.style.cursor = 'default';
    }

    showSuccess(message) {
        // Implementar notificación de éxito
        this.showNotification(message, 'success');
    }

    showError(message) {
        // Implementar notificación de error
        this.showNotification(message, 'error');
    }

    showNotification(message, type) {
        // Implementación simple de notificaciones
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        // Insertar al inicio del contenedor
        const container = document.querySelector('.admin-container');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
}

// Funciones globales para los botones
function openCreateModal() {
    specialOffersManager.resetForm();
    $('#offerModal').modal('show');
}

function clearFilters() {
    specialOffersManager.clearFilters();
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.specialOffersManager = new SpecialOffersManager();
});