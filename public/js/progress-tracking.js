/**
 * STYLOFITNESS - PROGRESS TRACKING JAVASCRIPT
 * Sistema de Seguimiento de Progreso - Funcionalidades Interactivas
 */

// =====================================================
// CONFIGURACIÓN GLOBAL
// =====================================================
const ProgressTracker = {
    config: {
        apiBaseUrl: '/api/trainer/progress',
        chartColors: {
            primary: '#007bff',
            secondary: '#6c757d',
            success: '#28a745',
            danger: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        },
        defaultChartOptions: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    enabled: true,
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            }
        }
    },
    charts: {},
    intervals: {},
    cache: {}
};

// =====================================================
// UTILIDADES GENERALES
// =====================================================
const Utils = {
    /**
     * Realizar petición AJAX
     */
    async request(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        const config = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Request failed:', error);
            this.showNotification('Error en la petición: ' + error.message, 'error');
            throw error;
        }
    },
    
    /**
     * Mostrar notificación
     */
    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // Agregar estilos si no existen
        if (!document.querySelector('#notification-styles')) {
            const styles = document.createElement('style');
            styles.id = 'notification-styles';
            styles.textContent = `
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    min-width: 300px;
                    padding: 15px;
                    border-radius: 6px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    animation: slideInRight 0.3s ease;
                }
                .notification-info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
                .notification-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
                .notification-warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
                .notification-error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
                .notification-content { display: flex; justify-content: space-between; align-items: center; }
                .notification-close { background: none; border: none; cursor: pointer; padding: 0; }
                @keyframes slideInRight { from { transform: translateX(100%); } to { transform: translateX(0); } }
            `;
            document.head.appendChild(styles);
        }
        
        document.body.appendChild(notification);
        
        // Auto-remove después del tiempo especificado
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, duration);
    },
    
    /**
     * Formatear fecha
     */
    formatDate(date, format = 'short') {
        const d = new Date(date);
        const options = {
            short: { month: 'short', day: 'numeric' },
            long: { year: 'numeric', month: 'long', day: 'numeric' },
            time: { hour: '2-digit', minute: '2-digit' }
        };
        
        return d.toLocaleDateString('es-ES', options[format] || options.short);
    },
    
    /**
     * Formatear número
     */
    formatNumber(number, decimals = 1) {
        return parseFloat(number).toFixed(decimals);
    },
    
    /**
     * Debounce function
     */
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
    },
    
    /**
     * Mostrar loading
     */
    showLoading(element) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        if (element) {
            element.classList.add('loading');
        }
    },
    
    /**
     * Ocultar loading
     */
    hideLoading(element) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        if (element) {
            element.classList.remove('loading');
        }
    }
};

// =====================================================
// GESTIÓN DE GRÁFICOS
// =====================================================
const ChartManager = {
    /**
     * Crear gráfico de líneas
     */
    createLineChart(canvasId, data, options = {}) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.error(`Canvas with id '${canvasId}' not found`);
            return null;
        }
        
        const ctx = canvas.getContext('2d');
        const chartOptions = {
            ...ProgressTracker.config.defaultChartOptions,
            ...options
        };
        
        // Destruir gráfico existente si existe
        if (ProgressTracker.charts[canvasId]) {
            ProgressTracker.charts[canvasId].destroy();
        }
        
        ProgressTracker.charts[canvasId] = new Chart(ctx, {
            type: 'line',
            data: data,
            options: chartOptions
        });
        
        return ProgressTracker.charts[canvasId];
    },
    
    /**
     * Crear gráfico de barras
     */
    createBarChart(canvasId, data, options = {}) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.error(`Canvas with id '${canvasId}' not found`);
            return null;
        }
        
        const ctx = canvas.getContext('2d');
        const chartOptions = {
            ...ProgressTracker.config.defaultChartOptions,
            ...options
        };
        
        // Destruir gráfico existente si existe
        if (ProgressTracker.charts[canvasId]) {
            ProgressTracker.charts[canvasId].destroy();
        }
        
        ProgressTracker.charts[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: chartOptions
        });
        
        return ProgressTracker.charts[canvasId];
    },
    
    /**
     * Crear gráfico circular
     */
    createDoughnutChart(canvasId, data, options = {}) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.error(`Canvas with id '${canvasId}' not found`);
            return null;
        }
        
        const ctx = canvas.getContext('2d');
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            ...options
        };
        
        // Destruir gráfico existente si existe
        if (ProgressTracker.charts[canvasId]) {
            ProgressTracker.charts[canvasId].destroy();
        }
        
        ProgressTracker.charts[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: chartOptions
        });
        
        return ProgressTracker.charts[canvasId];
    },
    
    /**
     * Actualizar datos de gráfico
     */
    updateChart(canvasId, newData) {
        const chart = ProgressTracker.charts[canvasId];
        if (chart) {
            chart.data = newData;
            chart.update();
        }
    },
    
    /**
     * Destruir gráfico
     */
    destroyChart(canvasId) {
        if (ProgressTracker.charts[canvasId]) {
            ProgressTracker.charts[canvasId].destroy();
            delete ProgressTracker.charts[canvasId];
        }
    }
};

// =====================================================
// DASHBOARD PRINCIPAL
// =====================================================
const Dashboard = {
    /**
     * Inicializar dashboard
     */
    async init() {
        try {
            await this.loadDashboardData();
            this.setupEventListeners();
            this.startAutoRefresh();
        } catch (error) {
            console.error('Error initializing dashboard:', error);
            Utils.showNotification('Error al cargar el dashboard', 'error');
        }
    },
    
    /**
     * Cargar datos del dashboard
     */
    async loadDashboardData() {
        Utils.showLoading('.stats-grid');
        
        try {
            const data = await Utils.request(`${ProgressTracker.config.apiBaseUrl}/dashboard`);
            
            this.updateStats(data.stats);
            this.updateAlerts(data.alerts);
            this.updateClientsList(data.clients);
            
            Utils.hideLoading('.stats-grid');
        } catch (error) {
            Utils.hideLoading('.stats-grid');
            throw error;
        }
    },
    
    /**
     * Actualizar estadísticas
     */
    updateStats(stats) {
        const statElements = {
            'active-clients': stats.activeClients,
            'avg-workouts': stats.avgWorkoutsPerClient,
            'active-routines': stats.activeRoutines,
            'pending-alerts': stats.pendingAlerts
        };
        
        Object.entries(statElements).forEach(([id, value]) => {
            const element = document.querySelector(`[data-stat="${id}"] .stat-value`);
            if (element) {
                this.animateNumber(element, value);
            }
        });
    },
    
    /**
     * Animar número
     */
    animateNumber(element, targetValue) {
        const startValue = parseInt(element.textContent) || 0;
        const duration = 1000;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const currentValue = Math.floor(startValue + (targetValue - startValue) * progress);
            element.textContent = currentValue;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    },
    
    /**
     * Actualizar alertas
     */
    updateAlerts(alerts) {
        const alertsContainer = document.querySelector('.alerts-list');
        if (!alertsContainer) return;
        
        alertsContainer.innerHTML = '';
        
        if (alerts.length === 0) {
            alertsContainer.innerHTML = `
                <div class="no-alerts">
                    <i class="fas fa-check-circle text-success"></i>
                    <p>No hay alertas pendientes</p>
                </div>
            `;
            return;
        }
        
        alerts.forEach(alert => {
            const alertElement = this.createAlertElement(alert);
            alertsContainer.appendChild(alertElement);
        });
    },
    
    /**
     * Crear elemento de alerta
     */
    createAlertElement(alert) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert-item';
        alertDiv.innerHTML = `
            <div class="alert-icon severity-${alert.severity}">
                <i class="${this.getAlertIcon(alert.alert_type)}"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">${alert.client_first_name} ${alert.client_last_name}</div>
                <div class="alert-message">${alert.alert_message}</div>
                <div class="alert-time">Hace ${alert.days_old} días</div>
            </div>
            <div class="alert-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="Dashboard.viewClientProgress(${alert.client_id})">
                    Ver Progreso
                </button>
                <button class="btn btn-sm btn-success" onclick="Dashboard.resolveAlert(${alert.id})">
                    Resolver
                </button>
            </div>
        `;
        
        return alertDiv;
    },
    
    /**
     * Obtener icono de alerta
     */
    getAlertIcon(type) {
        const icons = {
            'inactive': 'fas fa-user-clock',
            'high_rpe': 'fas fa-exclamation-triangle',
            'no_progress': 'fas fa-chart-line',
            'goal_achieved': 'fas fa-trophy',
            'injury_risk': 'fas fa-first-aid'
        };
        
        return icons[type] || 'fas fa-bell';
    },
    
    /**
     * Actualizar lista de clientes
     */
    updateClientsList(clients) {
        const clientsContainer = document.querySelector('.clients-grid');
        if (!clientsContainer) return;
        
        clientsContainer.innerHTML = '';
        
        clients.forEach(client => {
            const clientElement = this.createClientCard(client);
            clientsContainer.appendChild(clientElement);
        });
    },
    
    /**
     * Crear tarjeta de cliente
     */
    createClientCard(client) {
        const clientDiv = document.createElement('div');
        clientDiv.className = 'client-card fade-in';
        
        const statusClass = this.getClientStatusClass(client);
        const lastWorkout = client.last_workout_date ? 
            Utils.formatDate(client.last_workout_date) : 'Nunca';
        
        clientDiv.innerHTML = `
            <div class="client-card-header">
                <div class="client-info">
                    <img src="${client.avatar || '/images/default-avatar.png'}" 
                         alt="${client.first_name}" class="client-avatar">
                    <div class="client-details">
                        <h4>${client.first_name} ${client.last_name}</h4>
                        <div class="client-email">${client.email}</div>
                    </div>
                </div>
                <div class="client-status">
                    <span class="status-indicator ${statusClass}"></span>
                    <span class="status-text">${this.getClientStatusText(client)}</span>
                </div>
            </div>
            <div class="client-card-body">
                <div class="client-stats">
                    <div class="client-stat">
                        <span class="value">${client.total_workouts || 0}</span>
                        <span class="label">Entrenamientos</span>
                    </div>
                    <div class="client-stat">
                        <span class="value">${Utils.formatNumber(client.avg_rpe || 0)}</span>
                        <span class="label">RPE Promedio</span>
                    </div>
                    <div class="client-stat">
                        <span class="value">${client.active_routines || 0}</span>
                        <span class="label">Rutinas Activas</span>
                    </div>
                    <div class="client-stat">
                        <span class="value">${lastWorkout}</span>
                        <span class="label">Último Entreno</span>
                    </div>
                </div>
                <div class="client-actions">
                    <a href="/trainer/progress/client/${client.client_id}" class="btn btn-primary btn-sm">
                        <i class="fas fa-chart-line"></i> Progreso
                    </a>
                    <a href="/routines/client/${client.client_id}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-dumbbell"></i> Rutinas
                    </a>
                </div>
            </div>
        `;
        
        return clientDiv;
    },
    
    /**
     * Obtener clase de estado del cliente
     */
    getClientStatusClass(client) {
        const daysSinceLastWorkout = client.days_since_last_workout || 999;
        
        if (daysSinceLastWorkout <= 3) return 'active';
        if (daysSinceLastWorkout <= 7) return 'warning';
        return 'inactive';
    },
    
    /**
     * Obtener texto de estado del cliente
     */
    getClientStatusText(client) {
        const daysSinceLastWorkout = client.days_since_last_workout || 999;
        
        if (daysSinceLastWorkout <= 3) return 'Activo';
        if (daysSinceLastWorkout <= 7) return 'Moderado';
        return 'Inactivo';
    },
    
    /**
     * Ver progreso del cliente
     */
    viewClientProgress(clientId) {
        window.location.href = `/trainer/progress/client/${clientId}`;
    },
    
    /**
     * Resolver alerta
     */
    async resolveAlert(alertId) {
        try {
            await Utils.request(`${ProgressTracker.config.apiBaseUrl}/alerts/${alertId}/resolve`, {
                method: 'POST'
            });
            
            Utils.showNotification('Alerta resuelta correctamente', 'success');
            this.loadDashboardData(); // Recargar datos
        } catch (error) {
            Utils.showNotification('Error al resolver la alerta', 'error');
        }
    },
    
    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Botón de actualizar
        const refreshBtn = document.querySelector('.btn-refresh');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.loadDashboardData();
            });
        }
        
        // Filtros de clientes
        const clientFilter = document.querySelector('#client-filter');
        if (clientFilter) {
            clientFilter.addEventListener('input', Utils.debounce((e) => {
                this.filterClients(e.target.value);
            }, 300));
        }
        
        // Selector de período
        const periodSelector = document.querySelector('#period-selector');
        if (periodSelector) {
            periodSelector.addEventListener('change', (e) => {
                this.changePeriod(e.target.value);
            });
        }
    },
    
    /**
     * Filtrar clientes
     */
    filterClients(searchTerm) {
        const clientCards = document.querySelectorAll('.client-card');
        const term = searchTerm.toLowerCase();
        
        clientCards.forEach(card => {
            const clientName = card.querySelector('.client-details h4').textContent.toLowerCase();
            const clientEmail = card.querySelector('.client-email').textContent.toLowerCase();
            
            const matches = clientName.includes(term) || clientEmail.includes(term);
            card.style.display = matches ? 'block' : 'none';
        });
    },
    
    /**
     * Cambiar período
     */
    async changePeriod(period) {
        try {
            const data = await Utils.request(`${ProgressTracker.config.apiBaseUrl}/dashboard?period=${period}`);
            this.updateStats(data.stats);
        } catch (error) {
            Utils.showNotification('Error al cambiar el período', 'error');
        }
    },
    
    /**
     * Iniciar actualización automática
     */
    startAutoRefresh() {
        // Actualizar cada 5 minutos
        ProgressTracker.intervals.dashboard = setInterval(() => {
            this.loadDashboardData();
        }, 300000);
    },
    
    /**
     * Detener actualización automática
     */
    stopAutoRefresh() {
        if (ProgressTracker.intervals.dashboard) {
            clearInterval(ProgressTracker.intervals.dashboard);
            delete ProgressTracker.intervals.dashboard;
        }
    }
};

// =====================================================
// PROGRESO DEL CLIENTE
// =====================================================
const ClientProgress = {
    clientId: null,
    currentPeriod: '30d',
    
    /**
     * Inicializar vista de progreso del cliente
     */
    async init(clientId) {
        this.clientId = clientId;
        
        try {
            await this.loadClientData();
            this.setupEventListeners();
            this.initializeCharts();
        } catch (error) {
            console.error('Error initializing client progress:', error);
            Utils.showNotification('Error al cargar el progreso del cliente', 'error');
        }
    },
    
    /**
     * Cargar datos del cliente
     */
    async loadClientData() {
        Utils.showLoading('.client-progress-container');
        
        try {
            const data = await Utils.request(
                `${ProgressTracker.config.apiBaseUrl}/client/${this.clientId}?period=${this.currentPeriod}`
            );
            
            this.updateClientInfo(data.client);
            this.updateProgressCharts(data.charts);
            this.updateMetrics(data.metrics);
            this.updateWorkoutHistory(data.workouts);
            
            Utils.hideLoading('.client-progress-container');
        } catch (error) {
            Utils.hideLoading('.client-progress-container');
            throw error;
        }
    },
    
    /**
     * Actualizar información del cliente
     */
    updateClientInfo(client) {
        const elements = {
            '.client-name': `${client.first_name} ${client.last_name}`,
            '.client-email': client.email,
            '.client-avatar': client.avatar
        };
        
        Object.entries(elements).forEach(([selector, value]) => {
            const element = document.querySelector(selector);
            if (element) {
                if (selector === '.client-avatar') {
                    element.src = value || '/images/default-avatar.png';
                } else {
                    element.textContent = value;
                }
            }
        });
    },
    
    /**
     * Actualizar gráficos de progreso
     */
    updateProgressCharts(chartsData) {
        // Gráfico de entrenamientos
        if (chartsData.workouts) {
            ChartManager.createLineChart('workouts-chart', {
                labels: chartsData.workouts.labels,
                datasets: [{
                    label: 'Entrenamientos',
                    data: chartsData.workouts.data,
                    borderColor: ProgressTracker.config.chartColors.primary,
                    backgroundColor: ProgressTracker.config.chartColors.primary + '20',
                    fill: true,
                    tension: 0.4
                }]
            });
        }
        
        // Gráfico de volumen
        if (chartsData.volume) {
            ChartManager.createBarChart('volume-chart', {
                labels: chartsData.volume.labels,
                datasets: [{
                    label: 'Volumen (kg)',
                    data: chartsData.volume.data,
                    backgroundColor: ProgressTracker.config.chartColors.success,
                    borderColor: ProgressTracker.config.chartColors.success,
                    borderWidth: 1
                }]
            });
        }
        
        // Gráfico de RPE
        if (chartsData.rpe) {
            ChartManager.createLineChart('rpe-chart', {
                labels: chartsData.rpe.labels,
                datasets: [{
                    label: 'RPE Promedio',
                    data: chartsData.rpe.data,
                    borderColor: ProgressTracker.config.chartColors.warning,
                    backgroundColor: ProgressTracker.config.chartColors.warning + '20',
                    fill: false,
                    tension: 0.4
                }]
            }, {
                scales: {
                    y: {
                        min: 1,
                        max: 10,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            });
        }
        
        // Gráfico de calorías
        if (chartsData.calories) {
            ChartManager.createBarChart('calories-chart', {
                labels: chartsData.calories.labels,
                datasets: [{
                    label: 'Calorías Quemadas',
                    data: chartsData.calories.data,
                    backgroundColor: ProgressTracker.config.chartColors.danger,
                    borderColor: ProgressTracker.config.chartColors.danger,
                    borderWidth: 1
                }]
            });
        }
    },
    
    /**
     * Actualizar métricas
     */
    updateMetrics(metrics) {
        const metricElements = {
            'consistency': metrics.consistency,
            'total-calories': metrics.totalCalories,
            'total-volume': metrics.totalVolume,
            'personal-records': metrics.personalRecords
        };
        
        Object.entries(metricElements).forEach(([id, value]) => {
            const element = document.querySelector(`[data-metric="${id}"] .metric-value`);
            if (element) {
                if (id === 'consistency') {
                    element.textContent = `${value}%`;
                    this.updateProgressBar(`${id}-progress`, value);
                } else {
                    element.textContent = value;
                }
            }
        });
    },
    
    /**
     * Actualizar barra de progreso
     */
    updateProgressBar(id, percentage) {
        const progressBar = document.querySelector(`#${id} .progress-bar-fill`);
        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
            
            // Cambiar color según el porcentaje
            progressBar.className = 'progress-bar-fill';
            if (percentage >= 80) {
                progressBar.classList.add('success');
            } else if (percentage >= 60) {
                progressBar.classList.add('warning');
            } else {
                progressBar.classList.add('danger');
            }
        }
    },
    
    /**
     * Actualizar historial de entrenamientos
     */
    updateWorkoutHistory(workouts) {
        const workoutsList = document.querySelector('.workouts-list');
        if (!workoutsList) return;
        
        workoutsList.innerHTML = '';
        
        if (workouts.length === 0) {
            workoutsList.innerHTML = `
                <div class="no-workouts">
                    <i class="fas fa-dumbbell text-secondary"></i>
                    <p>No hay entrenamientos registrados en este período</p>
                </div>
            `;
            return;
        }
        
        workouts.forEach(workout => {
            const workoutElement = this.createWorkoutElement(workout);
            workoutsList.appendChild(workoutElement);
        });
    },
    
    /**
     * Crear elemento de entrenamiento
     */
    createWorkoutElement(workout) {
        const workoutDiv = document.createElement('div');
        workoutDiv.className = 'workout-item';
        
        const date = Utils.formatDate(workout.workout_date, 'long');
        const duration = this.formatDuration(workout.duration_seconds);
        
        workoutDiv.innerHTML = `
            <div class="workout-header">
                <div class="workout-date">${date}</div>
                <div class="workout-rpe">
                    <span class="rpe-label">RPE:</span>
                    <span class="rpe-value rpe-${workout.rpe}">${workout.rpe || 'N/A'}</span>
                </div>
            </div>
            <div class="workout-details">
                <div class="workout-stat">
                    <i class="fas fa-clock"></i>
                    <span>${duration}</span>
                </div>
                <div class="workout-stat">
                    <i class="fas fa-fire"></i>
                    <span>${workout.calories_burned || 0} cal</span>
                </div>
                <div class="workout-stat">
                    <i class="fas fa-weight-hanging"></i>
                    <span>${workout.total_volume || 0} kg</span>
                </div>
            </div>
            ${workout.notes ? `<div class="workout-notes">${workout.notes}</div>` : ''}
        `;
        
        return workoutDiv;
    },
    
    /**
     * Formatear duración
     */
    formatDuration(seconds) {
        if (!seconds) return '0m';
        
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        
        if (hours > 0) {
            return `${hours}h ${minutes}m`;
        }
        return `${minutes}m`;
    },
    
    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Selector de período
        const periodSelector = document.querySelector('#period-selector');
        if (periodSelector) {
            periodSelector.addEventListener('change', (e) => {
                this.changePeriod(e.target.value);
            });
        }
        
        // Botón de exportar
        const exportBtn = document.querySelector('.btn-export');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                this.exportProgress();
            });
        }
        
        // Guardar notas del entrenador
        const saveNotesBtn = document.querySelector('#save-notes');
        if (saveNotesBtn) {
            saveNotesBtn.addEventListener('click', () => {
                this.saveTrainerNotes();
            });
        }
    },
    
    /**
     * Cambiar período
     */
    async changePeriod(period) {
        this.currentPeriod = period;
        await this.loadClientData();
    },
    
    /**
     * Exportar progreso
     */
    async exportProgress() {
        try {
            const response = await Utils.request(
                `${ProgressTracker.config.apiBaseUrl}/export/${this.clientId}?period=${this.currentPeriod}`,
                { method: 'POST' }
            );
            
            if (response.download_url) {
                window.open(response.download_url, '_blank');
                Utils.showNotification('Exportación iniciada', 'success');
            }
        } catch (error) {
            Utils.showNotification('Error al exportar el progreso', 'error');
        }
    },
    
    /**
     * Guardar notas del entrenador
     */
    async saveTrainerNotes() {
        const notesTextarea = document.querySelector('#trainer-notes');
        if (!notesTextarea) return;
        
        const notes = notesTextarea.value.trim();
        
        try {
            await Utils.request(`${ProgressTracker.config.apiBaseUrl}/notes`, {
                method: 'POST',
                body: JSON.stringify({
                    client_id: this.clientId,
                    notes: notes
                })
            });
            
            Utils.showNotification('Notas guardadas correctamente', 'success');
        } catch (error) {
            Utils.showNotification('Error al guardar las notas', 'error');
        }
    },
    
    /**
     * Inicializar gráficos
     */
    initializeCharts() {
        // Los gráficos se inicializarán cuando se carguen los datos
        // Esta función puede usarse para configuraciones adicionales
    }
};

// =====================================================
// COMPARACIÓN DE CLIENTES
// =====================================================
const ClientComparison = {
    selectedClients: [],
    maxClients: 5,
    
    /**
     * Inicializar comparación
     */
    init() {
        this.setupEventListeners();
        this.loadAvailableClients();
    },
    
    /**
     * Cargar clientes disponibles
     */
    async loadAvailableClients() {
        try {
            const data = await Utils.request(`${ProgressTracker.config.apiBaseUrl}/clients`);
            this.updateClientsList(data.clients);
        } catch (error) {
            Utils.showNotification('Error al cargar la lista de clientes', 'error');
        }
    },
    
    /**
     * Actualizar lista de clientes
     */
    updateClientsList(clients) {
        const clientsList = document.querySelector('.clients-selection');
        if (!clientsList) return;
        
        clientsList.innerHTML = '';
        
        clients.forEach(client => {
            const clientElement = this.createClientSelectionElement(client);
            clientsList.appendChild(clientElement);
        });
    },
    
    /**
     * Crear elemento de selección de cliente
     */
    createClientSelectionElement(client) {
        const clientDiv = document.createElement('div');
        clientDiv.className = 'client-selection-item';
        clientDiv.dataset.clientId = client.id;
        
        clientDiv.innerHTML = `
            <div class="client-info">
                <img src="${client.avatar || '/images/default-avatar.png'}" 
                     alt="${client.first_name}" class="client-avatar-sm">
                <div class="client-details">
                    <div class="client-name">${client.first_name} ${client.last_name}</div>
                    <div class="client-stats-sm">
                        ${client.total_workouts || 0} entrenamientos
                    </div>
                </div>
            </div>
            <div class="client-selection-controls">
                <input type="checkbox" class="client-checkbox" 
                       value="${client.id}" 
                       onchange="ClientComparison.toggleClient(${client.id}, this.checked)">
            </div>
        `;
        
        return clientDiv;
    },
    
    /**
     * Alternar selección de cliente
     */
    toggleClient(clientId, selected) {
        if (selected) {
            if (this.selectedClients.length >= this.maxClients) {
                Utils.showNotification(`Máximo ${this.maxClients} clientes permitidos`, 'warning');
                const checkbox = document.querySelector(`input[value="${clientId}"]`);
                if (checkbox) checkbox.checked = false;
                return;
            }
            
            this.selectedClients.push(clientId);
        } else {
            this.selectedClients = this.selectedClients.filter(id => id !== clientId);
        }
        
        this.updateSelectedClientsDisplay();
        
        if (this.selectedClients.length >= 2) {
            this.loadComparisonData();
        }
    },
    
    /**
     * Actualizar visualización de clientes seleccionados
     */
    updateSelectedClientsDisplay() {
        const selectedDisplay = document.querySelector('.selected-clients');
        if (!selectedDisplay) return;
        
        selectedDisplay.innerHTML = `
            <div class="selected-count">
                ${this.selectedClients.length} de ${this.maxClients} clientes seleccionados
            </div>
        `;
        
        const compareBtn = document.querySelector('.btn-compare');
        if (compareBtn) {
            compareBtn.disabled = this.selectedClients.length < 2;
        }
    },
    
    /**
     * Cargar datos de comparación
     */
    async loadComparisonData() {
        if (this.selectedClients.length < 2) return;
        
        Utils.showLoading('.comparison-results');
        
        try {
            const clientIds = this.selectedClients.join(',');
            const data = await Utils.request(
                `${ProgressTracker.config.apiBaseUrl}/compare?clients=${clientIds}`
            );
            
            this.updateComparisonCharts(data.charts);
            this.updateComparisonTable(data.comparison);
            
            Utils.hideLoading('.comparison-results');
        } catch (error) {
            Utils.hideLoading('.comparison-results');
            Utils.showNotification('Error al cargar la comparación', 'error');
        }
    },
    
    /**
     * Actualizar gráficos de comparación
     */
    updateComparisonCharts(chartsData) {
        // Gráfico de entrenamientos comparativo
        if (chartsData.workouts) {
            ChartManager.createLineChart('comparison-workouts-chart', chartsData.workouts);
        }
        
        // Gráfico de progreso comparativo
        if (chartsData.progress) {
            ChartManager.createBarChart('comparison-progress-chart', chartsData.progress);
        }
    },
    
    /**
     * Actualizar tabla de comparación
     */
    updateComparisonTable(comparisonData) {
        const tableBody = document.querySelector('.comparison-table tbody');
        if (!tableBody) return;
        
        tableBody.innerHTML = '';
        
        comparisonData.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${row.client_name}</td>
                <td>${row.total_workouts}</td>
                <td>${Utils.formatNumber(row.avg_rpe)}</td>
                <td>${row.total_calories}</td>
                <td>${Utils.formatNumber(row.consistency)}%</td>
                <td>
                    <span class="badge badge-${row.trend_class}">
                        ${row.trend_text}
                    </span>
                </td>
            `;
            tableBody.appendChild(tr);
        });
    },
    
    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Búsqueda de clientes
        const searchInput = document.querySelector('#client-search');
        if (searchInput) {
            searchInput.addEventListener('input', Utils.debounce((e) => {
                this.filterClients(e.target.value);
            }, 300));
        }
        
        // Botón de limpiar selección
        const clearBtn = document.querySelector('.btn-clear-selection');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                this.clearSelection();
            });
        }
        
        // Botón de exportar comparación
        const exportBtn = document.querySelector('.btn-export-comparison');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                this.exportComparison();
            });
        }
    },
    
    /**
     * Filtrar clientes
     */
    filterClients(searchTerm) {
        const clientItems = document.querySelectorAll('.client-selection-item');
        const term = searchTerm.toLowerCase();
        
        clientItems.forEach(item => {
            const clientName = item.querySelector('.client-name').textContent.toLowerCase();
            const matches = clientName.includes(term);
            item.style.display = matches ? 'flex' : 'none';
        });
    },
    
    /**
     * Limpiar selección
     */
    clearSelection() {
        this.selectedClients = [];
        
        const checkboxes = document.querySelectorAll('.client-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        this.updateSelectedClientsDisplay();
        
        // Limpiar resultados
        const resultsContainer = document.querySelector('.comparison-results');
        if (resultsContainer) {
            resultsContainer.innerHTML = `
                <div class="no-comparison">
                    <i class="fas fa-users text-secondary"></i>
                    <p>Selecciona al menos 2 clientes para comparar</p>
                </div>
            `;
        }
    },
    
    /**
     * Exportar comparación
     */
    async exportComparison() {
        if (this.selectedClients.length < 2) {
            Utils.showNotification('Selecciona al menos 2 clientes', 'warning');
            return;
        }
        
        try {
            const clientIds = this.selectedClients.join(',');
            const response = await Utils.request(
                `${ProgressTracker.config.apiBaseUrl}/export-comparison?clients=${clientIds}`,
                { method: 'POST' }
            );
            
            if (response.download_url) {
                window.open(response.download_url, '_blank');
                Utils.showNotification('Exportación iniciada', 'success');
            }
        } catch (error) {
            Utils.showNotification('Error al exportar la comparación', 'error');
        }
    }
};

// =====================================================
// INICIALIZACIÓN GLOBAL
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    // Detectar qué página estamos cargando
    const currentPath = window.location.pathname;
    
    if (currentPath.includes('/trainer/progress')) {
        if (currentPath.includes('/client/')) {
            // Página de progreso individual del cliente
            const clientId = currentPath.split('/').pop();
            ClientProgress.init(clientId);
        } else if (currentPath.includes('/compare')) {
            // Página de comparación de clientes
            ClientComparison.init();
        } else {
            // Dashboard principal de progreso
            Dashboard.init();
        }
    }
    
    // Configurar tooltips si existe la librería
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Limpiar intervalos al salir de la página
window.addEventListener('beforeunload', function() {
    Object.values(ProgressTracker.intervals).forEach(interval => {
        clearInterval(interval);
    });
    
    Object.values(ProgressTracker.charts).forEach(chart => {
        chart.destroy();
    });
});

// Exportar para uso global
window.ProgressTracker = ProgressTracker;
window.Dashboard = Dashboard;
window.ClientProgress = ClientProgress;
window.ClientComparison = ClientComparison;
window.Utils = Utils;
window.ChartManager = ChartManager;