/**
 * STYLOFITNESS - Panel de Administración
 * JavaScript para funcionalidades administrativas
 */

class AdminPanel {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 20;
        this.filters = {};
        this.selectedItems = [];
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initializeDataTables();
        this.initializeCharts();
        this.initializeUploadHandlers();
        this.initializeBulkActions();
    }
    
    bindEvents() {
        // Filtros y búsqueda
        document.addEventListener('input', (e) => {
            if (e.target.matches('.admin-search')) {
                this.debounce(() => {
                    this.handleSearch(e.target.value);
                }, 300)();
            }
        });
        
        document.addEventListener('change', (e) => {
            if (e.target.matches('.admin-filter')) {
                this.handleFilter(e.target);
            }
        });
        
        // Acciones CRUD
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-edit') || e.target.closest('.btn-edit')) {
                e.preventDefault();
                const btn = e.target.matches('.btn-edit') ? e.target : e.target.closest('.btn-edit');
                this.handleEdit(btn);
            }
            
            if (e.target.matches('.btn-delete') || e.target.closest('.btn-delete')) {
                e.preventDefault();
                const btn = e.target.matches('.btn-delete') ? e.target : e.target.closest('.btn-delete');
                this.handleDelete(btn);
            }
            
            if (e.target.matches('.btn-view') || e.target.closest('.btn-view')) {
                e.preventDefault();
                const btn = e.target.matches('.btn-view') ? e.target : e.target.closest('.btn-view');
                this.handleView(btn);
            }
        });
        
        // Selección múltiple
        document.addEventListener('change', (e) => {
            if (e.target.matches('.select-all-checkbox')) {
                this.handleSelectAll(e.target.checked);
            }
            
            if (e.target.matches('.select-item-checkbox')) {
                this.handleSelectItem(e.target);
            }
        });
        
        // Acciones masivas
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-bulk-action')) {
                e.preventDefault();
                this.handleBulkAction(e.target.dataset.action);
            }
        });
        
        // Modales
        document.addEventListener('click', (e) => {
            if (e.target.matches('.modal-close') || e.target.matches('.modal-backdrop')) {
                this.closeModal();
            }
        });
        
        // Formularios de administración
        document.addEventListener('submit', (e) => {
            if (e.target.matches('.admin-form')) {
                e.preventDefault();
                this.handleFormSubmit(e.target);
            }
        });
        
        // Paginación
        document.addEventListener('click', (e) => {
            if (e.target.matches('.pagination-link')) {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page);
                this.loadPage(page);
            }
        });
        
        // Ordenamiento
        document.addEventListener('click', (e) => {
            if (e.target.matches('.sortable-header')) {
                e.preventDefault();
                this.handleSort(e.target);
            }
        });
    }
    
    async handleSearch(query) {
        this.filters.search = query;
        this.currentPage = 1;
        await this.reloadData();
    }
    
    async handleFilter(filterElement) {
        const filterName = filterElement.name;
        const filterValue = filterElement.value;
        
        this.filters[filterName] = filterValue;
        this.currentPage = 1;
        await this.reloadData();
    }
    
    async handleEdit(btn) {
        const itemId = btn.dataset.id;
        const itemType = btn.dataset.type;
        
        if (!itemId || !itemType) return;
        
        try {
            this.showLoading('Cargando datos...');
            
            const response = await fetch(`/admin/${itemType}/edit/${itemId}`);
            
            if (response.ok) {
                const html = await response.text();
                this.showModal('Editar', html);
            } else {
                this.showNotification('Error al cargar los datos', 'error');
            }
        } catch (error) {
            console.error('Error loading edit form:', error);
            this.showNotification('Error al cargar el formulario', 'error');
        } finally {
            this.hideLoading();
        }
    }
    
    async handleDelete(btn) {
        const itemId = btn.dataset.id;
        const itemType = btn.dataset.type;
        const itemName = btn.dataset.name || 'este elemento';
        
        if (!itemId || !itemType) return;
        
        const confirmed = await this.showConfirmDialog(
            '¿Confirmar eliminación?',
            `¿Estás seguro de que quieres eliminar "${itemName}"?`,
            'Eliminar',
            'danger'
        );
        
        if (!confirmed) return;
        
        try {
            this.setButtonLoading(btn, true);
            
            const response = await fetch(`/admin/${itemType}/delete/${itemId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message || 'Eliminado correctamente', 'success');
                await this.reloadData();
            } else {
                this.showNotification(data.error || 'Error al eliminar', 'error');
            }
        } catch (error) {
            console.error('Error deleting item:', error);
            this.showNotification('Error al eliminar el elemento', 'error');
        } finally {
            this.setButtonLoading(btn, false);
        }
    }
    
    async handleView(btn) {
        const itemId = btn.dataset.id;
        const itemType = btn.dataset.type;
        
        if (!itemId || !itemType) return;
        
        try {
            this.showLoading('Cargando detalles...');
            
            const response = await fetch(`/admin/${itemType}/view/${itemId}`);
            
            if (response.ok) {
                const html = await response.text();
                this.showModal('Detalles', html, 'large');
            } else {
                this.showNotification('Error al cargar los detalles', 'error');
            }
        } catch (error) {
            console.error('Error loading details:', error);
            this.showNotification('Error al cargar los detalles', 'error');
        } finally {
            this.hideLoading();
        }
    }
    
    handleSelectAll(checked) {
        const checkboxes = document.querySelectorAll('.select-item-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        
        this.updateSelectedItems();
        this.updateBulkActionsVisibility();
    }
    
    handleSelectItem(checkbox) {
        this.updateSelectedItems();
        this.updateBulkActionsVisibility();
        
        // Actualizar estado del checkbox "seleccionar todo"
        const selectAllCheckbox = document.querySelector('.select-all-checkbox');
        if (selectAllCheckbox) {
            const allCheckboxes = document.querySelectorAll('.select-item-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.select-item-checkbox:checked');
            
            selectAllCheckbox.checked = allCheckboxes.length > 0 && allCheckboxes.length === checkedCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
        }
    }
    
    updateSelectedItems() {
        const checkedCheckboxes = document.querySelectorAll('.select-item-checkbox:checked');
        this.selectedItems = Array.from(checkedCheckboxes).map(cb => cb.value);
        
        // Actualizar contador
        const counter = document.querySelector('.selected-count');
        if (counter) {
            counter.textContent = this.selectedItems.length;
        }
    }
    
    updateBulkActionsVisibility() {
        const bulkActions = document.querySelector('.bulk-actions');
        if (bulkActions) {
            bulkActions.style.display = this.selectedItems.length > 0 ? 'block' : 'none';
        }
    }
    
    async handleBulkAction(action) {
        if (this.selectedItems.length === 0) {
            this.showNotification('Selecciona al menos un elemento', 'warning');
            return;
        }
        
        const actionNames = {
            delete: 'eliminar',
            activate: 'activar',
            deactivate: 'desactivar',
            export: 'exportar'
        };
        
        const actionName = actionNames[action] || action;
        const confirmed = await this.showConfirmDialog(
            `¿Confirmar acción masiva?`,
            `¿Estás seguro de que quieres ${actionName} ${this.selectedItems.length} elementos?`,
            'Confirmar',
            action === 'delete' ? 'danger' : 'primary'
        );
        
        if (!confirmed) return;
        
        try {
            this.showLoading(`Procesando ${actionName}...`);
            
            const response = await fetch('/admin/bulk-action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action: action,
                    items: this.selectedItems,
                    type: this.getCurrentItemType()
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message || `${actionName} completado`, 'success');
                await this.reloadData();
                this.clearSelection();
            } else {
                this.showNotification(data.error || `Error al ${actionName}`, 'error');
            }
        } catch (error) {
            console.error('Error in bulk action:', error);
            this.showNotification(`Error al ${actionName} los elementos`, 'error');
        } finally {
            this.hideLoading();
        }
    }
    
    async handleFormSubmit(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('[type="submit"]');
        
        try {
            this.setButtonLoading(submitButton, true);
            
            const response = await fetch(form.action, {
                method: form.method || 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message || 'Guardado correctamente', 'success');
                this.closeModal();
                await this.reloadData();
            } else {
                if (data.validation_errors) {
                    this.displayValidationErrors(form, data.validation_errors);
                } else {
                    this.showNotification(data.error || 'Error al guardar', 'error');
                }
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            this.showNotification('Error al enviar el formulario', 'error');
        } finally {
            this.setButtonLoading(submitButton, false);
        }
    }
    
    async handleSort(header) {
        const column = header.dataset.column;
        const currentOrder = header.dataset.order || 'asc';
        const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
        
        // Actualizar indicadores visuales
        document.querySelectorAll('.sortable-header').forEach(h => {
            h.classList.remove('sorted-asc', 'sorted-desc');
        });
        
        header.classList.add(`sorted-${newOrder}`);
        header.dataset.order = newOrder;
        
        // Aplicar ordenamiento
        this.filters.sort_by = column;
        this.filters.sort_order = newOrder;
        await this.reloadData();
    }
    
    async loadPage(page) {
        this.currentPage = page;
        await this.reloadData();
    }
    
    async reloadData() {
        const url = new URL(window.location);
        
        // Aplicar filtros a la URL
        Object.keys(this.filters).forEach(key => {
            if (this.filters[key]) {
                url.searchParams.set(key, this.filters[key]);
            } else {
                url.searchParams.delete(key);
            }
        });
        
        url.searchParams.set('page', this.currentPage);
        
        try {
            this.showTableLoading(true);
            
            const response = await fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const html = await response.text();
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                const newTable = tempDiv.querySelector('.admin-table-container');
                const currentTable = document.querySelector('.admin-table-container');
                
                if (newTable && currentTable) {
                    currentTable.innerHTML = newTable.innerHTML;
                }
                
                // Actualizar URL sin recargar la página
                window.history.pushState({}, '', url.toString());
                
                this.clearSelection();
            }
        } catch (error) {
            console.error('Error reloading data:', error);
            this.showNotification('Error al cargar los datos', 'error');
        } finally {
            this.showTableLoading(false);
        }
    }
    
    initializeDataTables() {
        // Configurar tablas responsivas
        const tables = document.querySelectorAll('.admin-table');
        tables.forEach(table => {
            this.makeTableResponsive(table);
        });
    }
    
    makeTableResponsive(table) {
        const wrapper = document.createElement('div');
        wrapper.className = 'table-responsive';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
        
        // Añadir indicadores de scroll
        wrapper.addEventListener('scroll', () => {
            const isScrolledLeft = wrapper.scrollLeft === 0;
            const isScrolledRight = wrapper.scrollLeft === wrapper.scrollWidth - wrapper.clientWidth;
            
            wrapper.classList.toggle('scrolled-left', !isScrolledLeft);
            wrapper.classList.toggle('scrolled-right', !isScrolledRight);
        });
    }
    
    initializeCharts() {
        // Inicializar gráficos del dashboard
        this.initializeDashboardCharts();
        this.initializeRealtimeUpdates();
    }
    
    initializeDashboardCharts() {
        // Gráfico de ventas
        const salesChart = document.getElementById('sales-chart');
        if (salesChart && window.Chart) {
            const ctx = salesChart.getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: window.chartData?.labels || [],
                    datasets: [{
                        label: 'Ventas',
                        data: window.chartData?.sales || [],
                        borderColor: '#FF6B00',
                        backgroundColor: 'rgba(255, 107, 0, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'S/ ' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Gráfico de usuarios
        const usersChart = document.getElementById('users-chart');
        if (usersChart && window.Chart) {
            const ctx = usersChart.getContext('2d');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: window.chartData?.labels || [],
                    datasets: [{
                        label: 'Nuevos usuarios',
                        data: window.chartData?.users || [],
                        backgroundColor: '#FF6B00'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    }
    
    initializeRealtimeUpdates() {
        // Actualizaciones en tiempo real del dashboard
        if (document.querySelector('.dashboard-stats')) {
            setInterval(() => {
                this.updateDashboardStats();
            }, 30000); // Actualizar cada 30 segundos
        }
    }
    
    async updateDashboardStats() {
        try {
            const response = await fetch('/api/stats/dashboard');
            const data = await response.json();
            
            if (data.success) {
                this.updateStatCards(data.data);
            }
        } catch (error) {
            console.error('Error updating dashboard stats:', error);
        }
    }
    
    updateStatCards(stats) {
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element) {
                this.animateNumber(element, parseInt(element.textContent.replace(/\D/g, '')), stats[key]);
            }
        });
    }
    
    animateNumber(element, start, end) {
        const duration = 1000;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = Math.floor(start + (end - start) * progress);
            element.textContent = current.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }
    
    initializeUploadHandlers() {
        // Drag and drop para subida de archivos
        const uploadAreas = document.querySelectorAll('.upload-area');
        
        uploadAreas.forEach(area => {
            this.setupFileUpload(area);
        });
    }
    
    setupFileUpload(uploadArea) {
        const fileInput = uploadArea.querySelector('input[type="file"]');
        
        if (!fileInput) return;
        
        // Drag and drop events
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                this.handleFileUpload(fileInput);
            }
        });
        
        // Click to upload
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        // File input change
        fileInput.addEventListener('change', () => {
            this.handleFileUpload(fileInput);
        });
    }
    
    async handleFileUpload(fileInput) {
        const files = Array.from(fileInput.files);
        
        if (files.length === 0) return;
        
        const uploadArea = fileInput.closest('.upload-area');
        const progressBar = uploadArea.querySelector('.upload-progress');
        
        try {
            for (const file of files) {
                await this.uploadFile(file, progressBar);
            }
            
            this.showNotification('Archivos subidos correctamente', 'success');
        } catch (error) {
            console.error('Upload error:', error);
            this.showNotification('Error al subir archivos', 'error');
        }
    }
    
    async uploadFile(file, progressBar) {
        const formData = new FormData();
        formData.append('file', file);
        
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const progress = (e.loaded / e.total) * 100;
                    if (progressBar) {
                        progressBar.style.width = `${progress}%`;
                        progressBar.style.display = 'block';
                    }
                }
            });
            
            xhr.addEventListener('load', () => {
                if (progressBar) {
                    progressBar.style.display = 'none';
                }
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        resolve(response);
                    } catch (e) {
                        reject(new Error('Invalid response'));
                    }
                } else {
                    reject(new Error(`Upload failed: ${xhr.status}`));
                }
            });
            
            xhr.addEventListener('error', () => {
                if (progressBar) {
                    progressBar.style.display = 'none';
                }
                reject(new Error('Upload failed'));
            });
            
            xhr.open('POST', '/api/upload');
            xhr.send(formData);
        });
    }
    
    initializeBulkActions() {
        // Configurar acciones masivas
        this.updateBulkActionsVisibility();
    }
    
    // Métodos de utilidad
    
    getCurrentItemType() {
        const path = window.location.pathname;
        const match = path.match(/\/admin\/([^\/]+)/);
        return match ? match[1] : 'items';
    }
    
    clearSelection() {
        this.selectedItems = [];
        
        const checkboxes = document.querySelectorAll('.select-item-checkbox, .select-all-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.indeterminate = false;
        });
        
        this.updateBulkActionsVisibility();
    }
    
    showModal(title, content, size = 'medium') {
        let modal = document.getElementById('admin-modal');
        
        if (!modal) {
            modal = this.createModal();
        }
        
        const modalDialog = modal.querySelector('.modal-dialog');
        modalDialog.className = `modal-dialog modal-${size}`;
        
        modal.querySelector('.modal-title').textContent = title;
        modal.querySelector('.modal-body').innerHTML = content;
        
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
        
        // Focus en el primer input
        const firstInput = modal.querySelector('input, select, textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
    
    createModal() {
        const modal = document.createElement('div');
        modal.id = 'admin-modal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="modal-close">×</button>
                    </div>
                    <div class="modal-body"></div>
                </div>
            </div>
            <div class="modal-backdrop"></div>
        `;
        
        document.body.appendChild(modal);
        return modal;
    }
    
    closeModal() {
        const modal = document.getElementById('admin-modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    }
    
    async showConfirmDialog(title, message, confirmText = 'Confirmar', type = 'primary') {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'modal confirm-modal';
            modal.innerHTML = `
                <div class="modal-dialog modal-small">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">${title}</h4>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-action="cancel">Cancelar</button>
                            <button type="button" class="btn btn-${type}" data-action="confirm">${confirmText}</button>
                        </div>
                    </div>
                </div>
                <div class="modal-backdrop"></div>
            `;
            
            document.body.appendChild(modal);
            modal.style.display = 'block';
            document.body.classList.add('modal-open');
            
            const handleClick = (e) => {
                const action = e.target.dataset.action;
                if (action === 'confirm' || action === 'cancel') {
                    modal.remove();
                    document.body.classList.remove('modal-open');
                    resolve(action === 'confirm');
                }
            };
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target.classList.contains('modal-backdrop')) {
                    modal.remove();
                    document.body.classList.remove('modal-open');
                    resolve(false);
                } else {
                    handleClick(e);
                }
            });
        });
    }
    
    showLoading(message = 'Cargando...') {
        let loader = document.getElementById('admin-loader');
        
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'admin-loader';
            loader.className = 'admin-loader';
            loader.innerHTML = `
                <div class="loader-content">
                    <div class="spinner"></div>
                    <p class="loader-message">${message}</p>
                </div>
            `;
            document.body.appendChild(loader);
        }
        
        loader.querySelector('.loader-message').textContent = message;
        loader.style.display = 'flex';
    }
    
    hideLoading() {
        const loader = document.getElementById('admin-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    }
    
    showTableLoading(show) {
        const table = document.querySelector('.admin-table');
        if (table) {
            table.classList.toggle('loading', show);
        }
    }
    
    setButtonLoading(button, loading) {
        if (!button) return;
        
        if (loading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || button.innerHTML;
        }
    }
    
    displayValidationErrors(form, errors) {
        // Limpiar errores anteriores
        form.querySelectorAll('.field-error').forEach(error => {
            error.remove();
        });
        
        form.querySelectorAll('.error').forEach(field => {
            field.classList.remove('error');
        });
        
        // Mostrar nuevos errores
        Object.keys(errors).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('error');
                
                const errorElement = document.createElement('div');
                errorElement.className = 'field-error';
                errorElement.textContent = errors[fieldName];
                
                field.parentNode.appendChild(errorElement);
            }
        });
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `admin-notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
                <button class="notification-close">×</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.hideNotification(notification);
        });
        
        setTimeout(() => {
            this.hideNotification(notification);
        }, 5000);
    }
    
    hideNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
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
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    if (document.body.classList.contains('admin-page')) {
        window.adminPanel = new AdminPanel();
    }
});
