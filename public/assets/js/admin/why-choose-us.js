/**
 * JavaScript para gestión de características "Por qué elegirnos"
 * StyleFitness Admin Panel
 */

class WhyChooseUsManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.currentFilters = {};
        this.editingFeatureId = null;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadFeatures();
        this.loadStats();
    }

    bindEvents() {
        // Filtros
        document.getElementById('statusFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('searchFilter').addEventListener('input', this.debounce(() => this.applyFilters(), 300));
        
        // Formulario
        document.getElementById('featureForm').addEventListener('submit', (e) => this.handleFormSubmit(e));
        
        // Modal events
        $('#featureModal').on('hidden.bs.modal', () => this.resetForm());
        
        // Botón de confirmación de eliminación
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => this.confirmDelete());
    }

    async loadFeatures() {
        try {
            this.showLoading();
            
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                ...this.currentFilters
            });

            const response = await fetch(`/admin/landing/why-choose-us/get?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderFeatures(data.data.items);
                this.renderPagination(data.data.pagination);
                this.updateStats(data.stats);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cargar características: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    renderFeatures(features) {
        const tbody = document.getElementById('featuresTableBody');
        
        if (features.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No se encontraron características</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = features.map(feature => `
            <tr>
                <td>${feature.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        ${feature.icon ? `<i class="${feature.icon} fa-2x text-primary mr-3"></i>` : ''}
                        <div>
                            <strong>${this.escapeHtml(feature.title)}</strong>
                            ${feature.description ? `<br><small class="text-muted">${this.escapeHtml(feature.description.substring(0, 60))}...</small>` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    ${feature.statistic_value ? 
                        `<span class="badge badge-info">${feature.statistic_value}${feature.statistic_suffix || ''}</span>` : 
                        '-'
                    }
                </td>
                <td>${feature.display_order}</td>
                <td>
                    <span class="status-badge ${feature.is_active ? 'status-active' : 'status-inactive'}">
                        ${feature.is_active ? 'Activa' : 'Inactiva'}
                    </span>
                </td>
                <td>${this.formatDate(feature.created_at)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="whyChooseUsManager.editFeature(${feature.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm ${feature.is_active ? 'btn-warning' : 'btn-success'}" 
                                onclick="whyChooseUsManager.toggleStatus(${feature.id})" 
                                title="${feature.is_active ? 'Desactivar' : 'Activar'}">
                            <i class="fas fa-${feature.is_active ? 'eye-slash' : 'eye'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="whyChooseUsManager.deleteFeature(${feature.id})" title="Eliminar">
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
                <a class="page-link" href="#" onclick="whyChooseUsManager.goToPage(${pagination.currentPage - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
        
        // Páginas
        for (let i = 1; i <= pagination.totalPages; i++) {
            if (i === 1 || i === pagination.totalPages || (i >= pagination.currentPage - 2 && i <= pagination.currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="whyChooseUsManager.goToPage(${i})">${i}</a>
                    </li>
                `;
            } else if (i === pagination.currentPage - 3 || i === pagination.currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        // Botón siguiente
        paginationHtml += `
            <li class="page-item ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="whyChooseUsManager.goToPage(${pagination.currentPage + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
        
        paginationHtml += '</ul></nav>';
        container.innerHTML = paginationHtml;
    }

    updateStats(stats) {
        document.getElementById('totalFeatures').textContent = stats.total || 0;
        document.getElementById('activeFeatures').textContent = stats.active || 0;
        document.getElementById('inactiveFeatures').textContent = stats.inactive || 0;
        document.getElementById('avgStatistic').textContent = stats.avgStatistic || '0';
    }

    applyFilters() {
        this.currentFilters = {
            status: document.getElementById('statusFilter').value,
            search: document.getElementById('searchFilter').value
        };
        this.currentPage = 1;
        this.loadFeatures();
    }

    clearFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('searchFilter').value = '';
        this.currentFilters = {};
        this.currentPage = 1;
        this.loadFeatures();
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadFeatures();
    }

    async editFeature(id) {
        try {
            const response = await fetch(`/admin/landing/why-choose-us/get-single?id=${id}`);
            const data = await response.json();

            if (data.success) {
                this.populateForm(data.data);
                this.editingFeatureId = id;
                $('#featureModal').modal('show');
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cargar característica: ' + error.message);
        }
    }

    populateForm(feature) {
        document.getElementById('featureModalTitle').textContent = 'Editar Característica';
        document.getElementById('featureId').value = feature.id;
        document.getElementById('featureTitle').value = feature.title;
        document.getElementById('featureDescription').value = feature.description || '';
        document.getElementById('featureIcon').value = feature.icon || '';
        document.getElementById('statisticValue').value = feature.statistic_value || '';
        document.getElementById('statisticSuffix').value = feature.statistic_suffix || '';
        document.getElementById('displayOrder').value = feature.display_order;
        document.getElementById('featureActive').checked = feature.is_active;
        
        // Actualizar preview del icono
        this.updateIconPreview(feature.icon);
    }

    resetForm() {
        document.getElementById('featureModalTitle').textContent = 'Nueva Característica';
        document.getElementById('featureForm').reset();
        document.getElementById('featureId').value = '';
        this.editingFeatureId = null;
        this.updateIconPreview('');
    }

    updateIconPreview(iconClass) {
        const preview = document.getElementById('iconPreview');
        if (iconClass) {
            preview.innerHTML = `<i class="${iconClass} fa-2x"></i>`;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(e.target);
            const url = this.editingFeatureId ? 
                '/admin/landing/why-choose-us/update' : 
                '/admin/landing/why-choose-us/create';

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                $('#featureModal').modal('hide');
                this.loadFeatures();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al guardar característica: ' + error.message);
        }
    }

    async toggleStatus(id) {
        try {
            const response = await fetch('/admin/landing/why-choose-us/toggle-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                this.loadFeatures();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cambiar estado: ' + error.message);
        }
    }

    deleteFeature(id) {
        this.featureToDelete = id;
        $('#deleteModal').modal('show');
    }

    async confirmDelete() {
        if (!this.featureToDelete) return;

        try {
            const response = await fetch('/admin/landing/why-choose-us/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${this.featureToDelete}`
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                $('#deleteModal').modal('hide');
                this.loadFeatures();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al eliminar característica: ' + error.message);
        }

        this.featureToDelete = null;
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
        document.body.style.cursor = 'wait';
    }

    hideLoading() {
        document.body.style.cursor = 'default';
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        const container = document.querySelector('.admin-container');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
}

// Funciones globales
function openCreateModal() {
    whyChooseUsManager.resetForm();
    $('#featureModal').modal('show');
}

function clearFilters() {
    whyChooseUsManager.clearFilters();
}

// Event listener para el campo de icono
document.addEventListener('DOMContentLoaded', () => {
    window.whyChooseUsManager = new WhyChooseUsManager();
    
    // Actualizar preview del icono cuando cambie el input
    const iconInput = document.getElementById('featureIcon');
    if (iconInput) {
        iconInput.addEventListener('input', (e) => {
            whyChooseUsManager.updateIconPreview(e.target.value);
        });
    }
});