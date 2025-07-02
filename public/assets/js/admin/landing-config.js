/**
 * JavaScript para gestión de configuraciones de Landing Page
 * StyleFitness Admin Panel
 */

class LandingConfigManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.currentFilters = {};
        this.editingConfigId = null;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadConfigs();
        this.loadStats();
    }

    bindEvents() {
        // Filtros
        document.getElementById('sectionFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('statusFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('searchFilter').addEventListener('input', this.debounce(() => this.applyFilters(), 300));
        
        // Formulario
        document.getElementById('configForm').addEventListener('submit', (e) => this.handleFormSubmit(e));
        
        // Modal events
        $('#configModal').on('hidden.bs.modal', () => this.resetForm());
        
        // Botón de confirmación de eliminación
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => this.confirmDelete());
        
        // Tabs de secciones
        this.bindSectionTabs();
    }

    bindSectionTabs() {
        const tabs = document.querySelectorAll('.section-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchSection(tab.dataset.section);
            });
        });
    }

    switchSection(section) {
        // Actualizar tabs activos
        document.querySelectorAll('.section-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelector(`[data-section="${section}"]`).classList.add('active');
        
        // Actualizar contenido
        document.querySelectorAll('.section-content').forEach(content => {
            content.style.display = 'none';
        });
        document.getElementById(`${section}Section`).style.display = 'block';
        
        // Cargar configuraciones de la sección
        this.loadSectionConfigs(section);
    }

    async loadSectionConfigs(section) {
        try {
            const response = await fetch(`/admin/landing/config/get-section?section=${section}`);
            const data = await response.json();

            if (data.success) {
                this.renderSectionConfigs(section, data.data);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cargar configuraciones: ' + error.message);
        }
    }

    renderSectionConfigs(section, configs) {
        const container = document.getElementById(`${section}Configs`);
        
        if (configs.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay configuraciones para esta sección</p>
                    <button class="btn btn-primary" onclick="landingConfigManager.createSectionConfig('${section}')">
                        <i class="fas fa-plus"></i> Crear Configuración
                    </button>
                </div>
            `;
            return;
        }

        container.innerHTML = configs.map(config => `
            <div class="config-item card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title">
                                ${this.escapeHtml(config.config_key)}
                                <span class="badge ${config.is_active ? 'badge-success' : 'badge-secondary'} ml-2">
                                    ${config.is_active ? 'Activa' : 'Inactiva'}
                                </span>
                            </h6>
                            <p class="card-text text-muted">${this.escapeHtml(config.config_value.substring(0, 100))}${config.config_value.length > 100 ? '...' : ''}</p>
                            ${config.description ? `<small class="text-muted">${this.escapeHtml(config.description)}</small>` : ''}
                        </div>
                        <div class="action-buttons ml-3">
                            <button class="btn btn-sm btn-primary" onclick="landingConfigManager.editConfig(${config.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm ${config.is_active ? 'btn-warning' : 'btn-success'}" 
                                    onclick="landingConfigManager.toggleStatus(${config.id})" 
                                    title="${config.is_active ? 'Desactivar' : 'Activar'}">
                                <i class="fas fa-${config.is_active ? 'eye-slash' : 'eye'}"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="landingConfigManager.deleteConfig(${config.id})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    async loadConfigs() {
        try {
            this.showLoading();
            
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                ...this.currentFilters
            });

            const response = await fetch(`/admin/landing/config/get?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderConfigs(data.data.items);
                this.renderPagination(data.data.pagination);
                this.updateStats(data.stats);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cargar configuraciones: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    renderConfigs(configs) {
        const tbody = document.getElementById('configsTableBody');
        
        if (configs.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No se encontraron configuraciones</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = configs.map(config => `
            <tr>
                <td>${config.id}</td>
                <td>
                    <span class="badge badge-info">${config.section}</span>
                </td>
                <td>
                    <strong>${this.escapeHtml(config.config_key)}</strong>
                    ${config.description ? `<br><small class="text-muted">${this.escapeHtml(config.description)}</small>` : ''}
                </td>
                <td>
                    <div class="config-value">
                        ${this.escapeHtml(config.config_value.substring(0, 50))}${config.config_value.length > 50 ? '...' : ''}
                    </div>
                </td>
                <td>
                    <span class="status-badge ${config.is_active ? 'status-active' : 'status-inactive'}">
                        ${config.is_active ? 'Activa' : 'Inactiva'}
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="landingConfigManager.editConfig(${config.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm ${config.is_active ? 'btn-warning' : 'btn-success'}" 
                                onclick="landingConfigManager.toggleStatus(${config.id})" 
                                title="${config.is_active ? 'Desactivar' : 'Activar'}">
                            <i class="fas fa-${config.is_active ? 'eye-slash' : 'eye'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="landingConfigManager.deleteConfig(${config.id})" title="Eliminar">
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
                <a class="page-link" href="#" onclick="landingConfigManager.goToPage(${pagination.currentPage - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
        
        // Páginas
        for (let i = 1; i <= pagination.totalPages; i++) {
            if (i === 1 || i === pagination.totalPages || (i >= pagination.currentPage - 2 && i <= pagination.currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="landingConfigManager.goToPage(${i})">${i}</a>
                    </li>
                `;
            } else if (i === pagination.currentPage - 3 || i === pagination.currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        // Botón siguiente
        paginationHtml += `
            <li class="page-item ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="landingConfigManager.goToPage(${pagination.currentPage + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
        
        paginationHtml += '</ul></nav>';
        container.innerHTML = paginationHtml;
    }

    updateStats(stats) {
        document.getElementById('totalConfigs').textContent = stats.total || 0;
        document.getElementById('activeConfigs').textContent = stats.active || 0;
        document.getElementById('inactiveConfigs').textContent = stats.inactive || 0;
        document.getElementById('sectionsCount').textContent = stats.sections || 0;
    }

    applyFilters() {
        this.currentFilters = {
            section: document.getElementById('sectionFilter').value,
            status: document.getElementById('statusFilter').value,
            search: document.getElementById('searchFilter').value
        };
        this.currentPage = 1;
        this.loadConfigs();
    }

    clearFilters() {
        document.getElementById('sectionFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('searchFilter').value = '';
        this.currentFilters = {};
        this.currentPage = 1;
        this.loadConfigs();
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadConfigs();
    }

    createSectionConfig(section) {
        this.resetForm();
        document.getElementById('configSection').value = section;
        $('#configModal').modal('show');
    }

    async editConfig(id) {
        try {
            const response = await fetch(`/admin/landing/config/get-single?id=${id}`);
            const data = await response.json();

            if (data.success) {
                this.populateForm(data.data);
                this.editingConfigId = id;
                $('#configModal').modal('show');
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cargar configuración: ' + error.message);
        }
    }

    populateForm(config) {
        document.getElementById('configModalTitle').textContent = 'Editar Configuración';
        document.getElementById('configId').value = config.id;
        document.getElementById('configSection').value = config.section;
        document.getElementById('configKey').value = config.config_key;
        document.getElementById('configValue').value = config.config_value;
        document.getElementById('configDescription').value = config.description || '';
        document.getElementById('configActive').checked = config.is_active;
    }

    resetForm() {
        document.getElementById('configModalTitle').textContent = 'Nueva Configuración';
        document.getElementById('configForm').reset();
        document.getElementById('configId').value = '';
        this.editingConfigId = null;
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(e.target);
            const url = this.editingConfigId ? 
                '/admin/landing/config/update' : 
                '/admin/landing/config/create';

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                $('#configModal').modal('hide');
                this.loadConfigs();
                
                // Recargar sección si estamos en vista de secciones
                const activeTab = document.querySelector('.section-tab.active');
                if (activeTab) {
                    this.loadSectionConfigs(activeTab.dataset.section);
                }
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al guardar configuración: ' + error.message);
        }
    }

    async toggleStatus(id) {
        try {
            const response = await fetch('/admin/landing/config/toggle-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                this.loadConfigs();
                
                // Recargar sección si estamos en vista de secciones
                const activeTab = document.querySelector('.section-tab.active');
                if (activeTab) {
                    this.loadSectionConfigs(activeTab.dataset.section);
                }
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cambiar estado: ' + error.message);
        }
    }

    deleteConfig(id) {
        this.configToDelete = id;
        $('#deleteModal').modal('show');
    }

    async confirmDelete() {
        if (!this.configToDelete) return;

        try {
            const response = await fetch('/admin/landing/config/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${this.configToDelete}`
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                $('#deleteModal').modal('hide');
                this.loadConfigs();
                
                // Recargar sección si estamos en vista de secciones
                const activeTab = document.querySelector('.section-tab.active');
                if (activeTab) {
                    this.loadSectionConfigs(activeTab.dataset.section);
                }
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al eliminar configuración: ' + error.message);
        }

        this.configToDelete = null;
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
    landingConfigManager.resetForm();
    $('#configModal').modal('show');
}

function clearFilters() {
    landingConfigManager.clearFilters();
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.landingConfigManager = new LandingConfigManager();
    
    // Activar primera sección por defecto
    const firstTab = document.querySelector('.section-tab');
    if (firstTab) {
        firstTab.click();
    }
});

// CSS adicional para las configuraciones
const additionalCSS = `
.section-tabs {
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 20px;
}

.section-tab {
    padding: 10px 20px;
    border: none;
    background: none;
    color: #6c757d;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: all 0.3s;
}

.section-tab:hover {
    color: #495057;
    text-decoration: none;
}

.section-tab.active {
    color: #007bff;
    border-bottom-color: #007bff;
}

.config-item {
    transition: transform 0.2s;
}

.config-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.config-value {
    font-family: monospace;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}
`;

// Agregar CSS al documento
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);}