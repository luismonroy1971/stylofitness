/**
 * Admin Users Management JavaScript
 * Maneja la funcionalidad de gestión de usuarios en el panel administrativo
 */

class AdminUsersManager {
    constructor() {
        this.currentPage = 1;
        this.isLoading = false;
        this.filters = {};
        this.selectedUsers = [];
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeFilters();
        this.initializeBulkActions();
    }

    bindEvents() {
        // Evento para el formulario de filtros
        const filterForm = document.querySelector('.filters-form');
        if (filterForm) {
            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.applyFilters();
            });
        }

        // Evento para búsqueda en tiempo real
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.applyFilters();
                }, 500);
            });
        }

        // Eventos para filtros de select
        const selectFilters = document.querySelectorAll('.filters-form select');
        selectFilters.forEach(select => {
            select.addEventListener('change', () => {
                this.applyFilters();
            });
        });

        // Eventos para acciones de usuario
        this.bindUserActions();

        // Eventos para selección múltiple
        this.bindBulkSelection();
    }

    bindUserActions() {
        // Botones de editar
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-edit-user')) {
                const userId = e.target.closest('.btn-edit-user').dataset.userId;
                this.editUser(userId);
            }

            if (e.target.closest('.btn-delete-user')) {
                const userId = e.target.closest('.btn-delete-user').dataset.userId;
                const userName = e.target.closest('.btn-delete-user').dataset.userName;
                this.deleteUser(userId, userName);
            }

            if (e.target.closest('.btn-toggle-status')) {
                const userId = e.target.closest('.btn-toggle-status').dataset.userId;
                this.toggleUserStatus(userId);
            }
        });
    }

    bindBulkSelection() {
        // Checkbox para seleccionar todos
        const selectAllCheckbox = document.querySelector('#selectAllUsers');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', (e) => {
                this.toggleSelectAll(e.target.checked);
            });
        }

        // Checkboxes individuales
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('user-checkbox')) {
                this.toggleUserSelection(e.target.value, e.target.checked);
            }
        });
    }

    initializeFilters() {
        // Obtener filtros actuales de la URL
        const urlParams = new URLSearchParams(window.location.search);
        this.filters = {
            search: urlParams.get('search') || '',
            role: urlParams.get('role') || '',
            status: urlParams.get('status') || '',
            page: parseInt(urlParams.get('page')) || 1
        };

        // Aplicar filtros a los elementos del formulario
        this.updateFilterInputs();
    }

    updateFilterInputs() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) searchInput.value = this.filters.search;

        const roleSelect = document.querySelector('select[name="role"]');
        if (roleSelect) roleSelect.value = this.filters.role;

        const statusSelect = document.querySelector('select[name="status"]');
        if (statusSelect) statusSelect.value = this.filters.status;
    }

    applyFilters() {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoading();

        // Recopilar filtros del formulario
        const formData = new FormData(document.querySelector('.filters-form'));
        this.filters = {
            search: formData.get('search') || '',
            role: formData.get('role') || '',
            status: formData.get('status') || '',
            page: 1 // Reset a la primera página
        };

        // Actualizar URL
        this.updateURL();

        // Hacer petición AJAX
        this.loadUsersAjax();
    }

    loadUsersAjax() {
        const params = new URLSearchParams(this.filters);
        params.set('ajax', '1');

        fetch(`/admin/users?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Actualizar tabla
                const tableContainer = document.querySelector('.users-table-container');
                if (tableContainer) {
                    tableContainer.innerHTML = data.tableHtml;
                }

                // Actualizar paginación
                const paginationContainer = document.querySelector('.pagination');
                if (paginationContainer && data.paginationHtml) {
                    paginationContainer.outerHTML = data.paginationHtml;
                } else if (paginationContainer && !data.paginationHtml) {
                    paginationContainer.style.display = 'none';
                }

                // Mostrar mensaje si no hay resultados
                if (data.totalUsers === 0) {
                    this.showMessage('No se encontraron usuarios con los filtros aplicados', 'info');
                }
            } else {
                throw new Error(data.message || 'Error al cargar usuarios');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showMessage('Error al cargar los usuarios', 'error');
        })
        .finally(() => {
            this.hideLoading();
        });
    }

    updateURL() {
        const url = new URL(window.location);
        
        Object.keys(this.filters).forEach(key => {
            if (this.filters[key]) {
                url.searchParams.set(key, this.filters[key]);
            } else {
                url.searchParams.delete(key);
            }
        });

        window.history.replaceState({}, '', url);
    }

    buildURL() {
        const params = new URLSearchParams();
        
        Object.keys(this.filters).forEach(key => {
            if (this.filters[key]) {
                params.set(key, this.filters[key]);
            }
        });

        return window.location.pathname + '?' + params.toString();
    }

    editUser(userId) {
        window.location.href = `/admin/users/edit/${userId}`;
    }

    deleteUser(userId, userName) {
        if (confirm(`¿Estás seguro de que quieres eliminar al usuario "${userName}"?\n\nEsta acción no se puede deshacer.`)) {
            this.showLoading();
            
            fetch(`/admin/users/delete/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    this.showMessage('Usuario eliminado exitosamente', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error('Error al eliminar usuario');
                }
            })
            .catch(error => {
                this.showMessage('Error al eliminar el usuario', 'error');
                console.error('Error:', error);
            })
            .finally(() => {
                this.hideLoading();
            });
        }
    }

    toggleUserStatus(userId) {
        this.showLoading();
        
        fetch(`/admin/users/toggle-status/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                this.showMessage('Estado del usuario actualizado', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error('Error al cambiar estado');
            }
        })
        .catch(error => {
            this.showMessage('Error al cambiar el estado del usuario', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            this.hideLoading();
        });
    }

    initializeBulkActions() {
        const bulkActionSelect = document.querySelector('#bulkAction');
        const applyBulkBtn = document.querySelector('#applyBulkAction');
        
        if (applyBulkBtn) {
            applyBulkBtn.addEventListener('click', () => {
                this.applyBulkAction();
            });
        }
    }

    toggleSelectAll(checked) {
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
            this.toggleUserSelection(checkbox.value, checked);
        });
    }

    toggleUserSelection(userId, selected) {
        if (selected) {
            if (!this.selectedUsers.includes(userId)) {
                this.selectedUsers.push(userId);
            }
        } else {
            this.selectedUsers = this.selectedUsers.filter(id => id !== userId);
        }

        this.updateBulkActionsVisibility();
    }

    updateBulkActionsVisibility() {
        const bulkActionsContainer = document.querySelector('.bulk-actions');
        if (bulkActionsContainer) {
            if (this.selectedUsers.length > 0) {
                bulkActionsContainer.style.display = 'flex';
                document.querySelector('#selectedCount').textContent = this.selectedUsers.length;
            } else {
                bulkActionsContainer.style.display = 'none';
            }
        }
    }

    applyBulkAction() {
        const action = document.querySelector('#bulkAction').value;
        
        if (!action || this.selectedUsers.length === 0) {
            this.showMessage('Selecciona una acción y al menos un usuario', 'warning');
            return;
        }

        let confirmMessage = '';
        switch (action) {
            case 'activate':
                confirmMessage = `¿Activar ${this.selectedUsers.length} usuario(s)?`;
                break;
            case 'deactivate':
                confirmMessage = `¿Desactivar ${this.selectedUsers.length} usuario(s)?`;
                break;
            case 'delete':
                confirmMessage = `¿Eliminar ${this.selectedUsers.length} usuario(s)?\n\nEsta acción no se puede deshacer.`;
                break;
            default:
                this.showMessage('Acción no válida', 'error');
                return;
        }

        if (confirm(confirmMessage)) {
            this.executeBulkAction(action);
        }
    }

    executeBulkAction(action) {
        this.showLoading();
        
        fetch('/admin/users/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                action: action,
                user_ids: this.selectedUsers
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showMessage(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Error en la operación');
            }
        })
        .catch(error => {
            this.showMessage('Error al ejecutar la acción', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            this.hideLoading();
        });
    }

    showLoading() {
        // Crear overlay de carga si no existe
        let loadingOverlay = document.querySelector('.loading-overlay');
        if (!loadingOverlay) {
            loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay';
            loadingOverlay.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Cargando...</p>
                </div>
            `;
            document.body.appendChild(loadingOverlay);
        }
        loadingOverlay.style.display = 'flex';
    }

    hideLoading() {
        const loadingOverlay = document.querySelector('.loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
        this.isLoading = false;
    }

    showMessage(message, type = 'info') {
        // Crear elemento de mensaje
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert alert-${type} alert-dismissible`;
        messageDiv.innerHTML = `
            <span>${message}</span>
            <button type="button" class="close" onclick="this.parentElement.remove()">
                <span>&times;</span>
            </button>
        `;

        // Insertar al inicio de la página
        const container = document.querySelector('.admin-users-page');
        if (container) {
            container.insertBefore(messageDiv, container.firstChild);
        }

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (messageDiv.parentElement) {
                messageDiv.remove();
            }
        }, 5000);
    }

    goToPage(page) {
        if (this.isLoading) return;
        
        this.filters.page = page;
        this.updateURL();
        this.loadUsersAjax();
    }
}

// Función global para eliminar usuario (llamada desde la vista)
function deleteUser(userId) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
        fetch(`/admin/users/delete/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al eliminar el usuario: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el usuario');
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.adminUsersManager = new AdminUsersManager();
});

// Estilos CSS adicionales para el loading y mensajes
const additionalStyles = `
<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    background: white;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    position: relative;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-error {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.alert .close {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    opacity: 0.7;
}

.alert .close:hover {
    opacity: 1;
}

.bulk-actions {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
    align-items: center;
    gap: 15px;
}

.bulk-actions select,
.bulk-actions button {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
</style>
`;

// Insertar estilos adicionales
document.head.insertAdjacentHTML('beforeend', additionalStyles);