/**
 * Admin Instructors Management JavaScript
 * Maneja la funcionalidad de gestión de instructores en el panel administrativo
 */

class AdminInstructorsManager {
    constructor() {
        this.currentPage = 1;
        this.isLoading = false;
        this.filters = {};
        this.selectedInstructors = [];
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeFilters();
    }

    bindEvents() {
        // Evento para el formulario de filtros
        const filterForm = document.querySelector('.filter-form');
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
        const selectFilters = document.querySelectorAll('.filter-form select');
        selectFilters.forEach(select => {
            select.addEventListener('change', () => {
                this.applyFilters();
            });
        });

        // Eventos para acciones de instructor
        this.bindInstructorActions();
    }

    bindInstructorActions() {
        // Botones de editar, ver y eliminar
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-action.edit')) {
                // La acción de editar se maneja por el enlace href
            }

            if (e.target.closest('.btn-action.view')) {
                // La acción de ver se maneja por el enlace href
            }

            if (e.target.closest('.btn-action.delete')) {
                // La acción de eliminar se maneja por el enlace href con confirmación
            }
        });
    }

    initializeFilters() {
        // Obtener filtros actuales de la URL
        const urlParams = new URLSearchParams(window.location.search);
        this.filters = {
            search: urlParams.get('search') || '',
            specialty: urlParams.get('specialty') || '',
            status: urlParams.get('status') || ''
        };

        // Aplicar filtros a los campos del formulario
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) searchInput.value = this.filters.search;

        const specialtySelect = document.querySelector('select[name="specialty"]');
        if (specialtySelect) specialtySelect.value = this.filters.specialty;

        const statusSelect = document.querySelector('select[name="status"]');
        if (statusSelect) statusSelect.value = this.filters.status;
    }

    applyFilters() {
        if (this.isLoading) return;

        this.filters = {
            search: document.querySelector('input[name="search"]')?.value || '',
            specialty: document.querySelector('select[name="specialty"]')?.value || '',
            status: document.querySelector('select[name="status"]')?.value || ''
        };

        this.currentPage = 1;
        this.loadInstructors();
    }

    goToPage(page) {
        if (this.isLoading || page === this.currentPage) return;
        this.currentPage = page;
        this.loadInstructors();
    }

    loadInstructors() {
        this.isLoading = true;
        this.showLoader();

        const params = new URLSearchParams({
            ...this.filters,
            page: this.currentPage,
            ajax: 1
        });

        fetch(`/admin/instructors?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateTable(data.tableHtml);
                    this.updatePagination(data.paginationHtml);
                    this.updateURL();
                } else {
                    console.error('Error al cargar instructores:', data.message);
                }
            })
            .catch(error => {
                console.error('Error al cargar instructores:', error);
            })
            .finally(() => {
                this.isLoading = false;
                this.hideLoader();
            });
    }

    updateTable(html) {
        const tableContainer = document.querySelector('.admin-table-container');
        if (tableContainer) {
            const tableElement = tableContainer.querySelector('table');
            if (tableElement) {
                tableElement.outerHTML = html;
            }
        }
    }

    updatePagination(html) {
        const paginationContainer = document.querySelector('.pagination');
        if (paginationContainer) {
            paginationContainer.outerHTML = html;
        } else {
            const tableContainer = document.querySelector('.admin-table-container');
            if (tableContainer) {
                tableContainer.insertAdjacentHTML('beforeend', html);
            }
        }
    }

    updateURL() {
        const params = new URLSearchParams();
        
        if (this.filters.search) params.set('search', this.filters.search);
        if (this.filters.specialty) params.set('specialty', this.filters.specialty);
        if (this.filters.status) params.set('status', this.filters.status);
        if (this.currentPage > 1) params.set('page', this.currentPage);
        
        const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
        window.history.replaceState({}, '', newUrl);
    }

    showLoader() {
        const tableContainer = document.querySelector('.admin-table-container');
        if (tableContainer) {
            tableContainer.classList.add('loading');
            
            // Crear loader si no existe
            if (!document.querySelector('.table-loader')) {
                const loader = document.createElement('div');
                loader.className = 'table-loader';
                loader.innerHTML = '<div class="spinner"></div><p>Cargando instructores...</p>';
                tableContainer.appendChild(loader);
            }
        }
    }

    hideLoader() {
        const tableContainer = document.querySelector('.admin-table-container');
        if (tableContainer) {
            tableContainer.classList.remove('loading');
            
            const loader = document.querySelector('.table-loader');
            if (loader) {
                loader.remove();
            }
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Ocultar pantalla de carga inmediatamente para páginas de admin
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        loadingScreen.style.display = 'none';
        loadingScreen.style.opacity = '0';
        loadingScreen.style.visibility = 'hidden';
        loadingScreen.style.pointerEvents = 'none';
    }
    
    // Asegurar que el contenido sea visible
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';
    
    window.adminInstructorsManager = new AdminInstructorsManager();
});