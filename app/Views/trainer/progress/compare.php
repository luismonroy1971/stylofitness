<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Comparación de Progreso entre Clientes - STYLOFITNESS
 * Permite a los entrenadores comparar el rendimiento de múltiples clientes
 */

$currentUser = AppHelper::getCurrentUser();
$isAdmin = ($currentUser['role'] === 'admin');
?>

<div class="progress-comparison">
    <!-- Header -->
    <div class="comparison-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-auto">
                    <a href="/trainer/progress" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
                <div class="col">
                    <h1 class="page-title">
                        <i class="fas fa-balance-scale me-2"></i>
                        Comparación de Progreso
                    </h1>
                    <p class="page-subtitle">
                        Compara el rendimiento y progreso entre tus clientes
                    </p>
                </div>
                <div class="col-auto">
                    <div class="header-actions">
                        <button type="button" class="btn btn-light" onclick="exportComparison()">
                            <i class="fas fa-download me-2"></i>
                            Exportar
                        </button>
                        <button type="button" class="btn btn-light" onclick="resetComparison()">
                            <i class="fas fa-refresh me-2"></i>
                            Reiniciar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container-fluid mt-4">
        <!-- Panel de selección de clientes -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>
                    Seleccionar Clientes para Comparar
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="client-selector">
                            <div class="search-box mb-3">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="clientSearch" 
                                           placeholder="Buscar clientes por nombre o email...">
                                </div>
                            </div>
                            
                            <div class="clients-grid" id="clientsGrid">
                                <?php foreach ($availableClients as $client): ?>
                                    <div class="client-option" data-client-id="<?= $client['id'] ?>" 
                                         data-client-name="<?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>">
                                        <div class="client-checkbox">
                                            <input type="checkbox" class="form-check-input" 
                                                   id="client_<?= $client['id'] ?>" 
                                                   value="<?= $client['id'] ?>">
                                        </div>
                                        <div class="client-avatar">
                                            <?php if (!empty($client['avatar'])): ?>
                                                <img src="<?= htmlspecialchars($client['avatar']) ?>" 
                                                     alt="<?= htmlspecialchars($client['first_name']) ?>">
                                            <?php else: ?>
                                                <div class="avatar-placeholder">
                                                    <?= strtoupper(substr($client['first_name'], 0, 1) . substr($client['last_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="client-info">
                                            <h6 class="client-name">
                                                <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                                            </h6>
                                            <p class="client-stats">
                                                <?= $client['total_workouts'] ?> entrenamientos • 
                                                <?= $client['workout_days'] ?> días activos
                                            </p>
                                        </div>
                                        <div class="client-metrics">
                                            <div class="metric">
                                                <span class="value"><?= round($client['avg_rpe'], 1) ?></span>
                                                <span class="label">RPE</span>
                                            </div>
                                            <div class="metric">
                                                <span class="value"><?= $client['consistency'] ?>%</span>
                                                <span class="label">Consistencia</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="comparison-controls">
                            <h6 class="mb-3">Configuración de Comparación</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Período de Comparación</label>
                                <select class="form-select" id="comparisonPeriod">
                                    <option value="7">Últimos 7 días</option>
                                    <option value="30" selected>Últimos 30 días</option>
                                    <option value="90">Últimos 90 días</option>
                                    <option value="all">Todo el tiempo</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Métricas a Comparar</label>
                                <div class="metrics-checkboxes">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="metric_workouts" checked>
                                        <label class="form-check-label" for="metric_workouts">
                                            Número de Entrenamientos
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="metric_consistency" checked>
                                        <label class="form-check-label" for="metric_consistency">
                                            Consistencia
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="metric_volume">
                                        <label class="form-check-label" for="metric_volume">
                                            Volumen Total
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="metric_rpe">
                                        <label class="form-check-label" for="metric_rpe">
                                            RPE Promedio
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="metric_calories">
                                        <label class="form-check-label" for="metric_calories">
                                            Calorías Quemadas
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="selected-clients mb-3">
                                <label class="form-label">Clientes Seleccionados (<span id="selectedCount">0</span>)</label>
                                <div id="selectedClientsList" class="selected-list">
                                    <p class="text-muted">Selecciona al menos 2 clientes para comparar</p>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-primary w-100" id="compareBtn" disabled>
                                <i class="fas fa-chart-bar me-2"></i>
                                Generar Comparación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados de la comparación -->
        <div id="comparisonResults" style="display: none;">
            <!-- Gráficos comparativos -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Tendencias Comparativas
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="comparisonChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-trophy me-2"></i>
                                Ranking de Rendimiento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="performanceRanking">
                                <!-- Contenido generado dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla comparativa detallada -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>
                        Comparación Detallada
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="comparisonTable">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Entrenamientos</th>
                                    <th>Consistencia</th>
                                    <th>Volumen Total</th>
                                    <th>RPE Promedio</th>
                                    <th>Calorías</th>
                                    <th>Progreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Contenido generado dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Análisis de grupos musculares -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-dumbbell me-2"></i>
                        Análisis por Grupos Musculares
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="muscleGroupComparison" height="300"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div id="muscleGroupInsights">
                                <!-- Insights generados dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recomendaciones -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Recomendaciones Personalizadas
                    </h5>
                </div>
                <div class="card-body">
                    <div id="recommendations">
                        <!-- Recomendaciones generadas dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress-comparison {
    background: #f8f9fa;
    min-height: 100vh;
}

.comparison-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.clients-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    max-height: 400px;
    overflow-y: auto;
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #f8f9fa;
}

.client-option {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
}

.client-option:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.client-option.selected {
    border-color: #007bff;
    background: #e3f2fd;
}

.client-checkbox {
    margin-right: 1rem;
}

.client-avatar {
    width: 50px;
    height: 50px;
    margin-right: 1rem;
}

.client-avatar img,
.avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-placeholder {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

.client-info {
    flex: 1;
    margin-right: 1rem;
}

.client-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.client-stats {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0;
}

.client-metrics {
    display: flex;
    gap: 1rem;
}

.metric {
    text-align: center;
}

.metric .value {
    display: block;
    font-weight: 700;
    font-size: 1.25rem;
    color: #2c3e50;
}

.metric .label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
}

.comparison-controls {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    height: fit-content;
}

.metrics-checkboxes {
    max-height: 150px;
    overflow-y: auto;
}

.selected-list {
    max-height: 120px;
    overflow-y: auto;
    padding: 0.5rem;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 4px;
}

.selected-client-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.25rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.selected-client-item:last-child {
    border-bottom: none;
}

.remove-client {
    color: #dc3545;
    cursor: pointer;
    font-size: 0.875rem;
}

.ranking-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid;
}

.ranking-item.rank-1 {
    border-left-color: #ffd700;
    background: #fffbf0;
}

.ranking-item.rank-2 {
    border-left-color: #c0c0c0;
    background: #f8f9fa;
}

.ranking-item.rank-3 {
    border-left-color: #cd7f32;
    background: #fff5f0;
}

.rank-number {
    font-size: 1.5rem;
    font-weight: 700;
    margin-right: 1rem;
    width: 30px;
    text-align: center;
}

.rank-info {
    flex: 1;
}

.rank-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.rank-score {
    font-size: 0.875rem;
    color: #6c757d;
}

.recommendation-item {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    border-left: 4px solid;
}

.recommendation-item.priority-high {
    background: #fff5f5;
    border-left-color: #e53e3e;
}

.recommendation-item.priority-medium {
    background: #fffbf0;
    border-left-color: #dd6b20;
}

.recommendation-item.priority-low {
    background: #f0fff4;
    border-left-color: #38a169;
}

.recommendation-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.recommendation-description {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.recommendation-actions {
    display: flex;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .clients-grid {
        grid-template-columns: 1fr;
    }
    
    .client-option {
        flex-direction: column;
        text-align: center;
    }
    
    .client-checkbox,
    .client-avatar,
    .client-info {
        margin-right: 0;
        margin-bottom: 0.5rem;
    }
    
    .client-metrics {
        justify-content: center;
    }
}
</style>

<script>
// Variables globales
let selectedClients = [];
let comparisonChart;
let muscleGroupChart;
let comparisonData = {};

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    initializeCharts();
});

function setupEventListeners() {
    // Búsqueda de clientes
    document.getElementById('clientSearch').addEventListener('input', filterClients);
    
    // Selección de clientes
    document.querySelectorAll('.client-option').forEach(option => {
        option.addEventListener('click', toggleClientSelection);
    });
    
    // Botón de comparación
    document.getElementById('compareBtn').addEventListener('click', generateComparison);
    
    // Cambios en configuración
    document.getElementById('comparisonPeriod').addEventListener('change', updateComparison);
    document.querySelectorAll('.metrics-checkboxes input').forEach(checkbox => {
        checkbox.addEventListener('change', updateComparison);
    });
}

function filterClients() {
    const searchTerm = document.getElementById('clientSearch').value.toLowerCase();
    const clientOptions = document.querySelectorAll('.client-option');
    
    clientOptions.forEach(option => {
        const clientName = option.dataset.clientName.toLowerCase();
        const isVisible = clientName.includes(searchTerm);
        option.style.display = isVisible ? 'flex' : 'none';
    });
}

function toggleClientSelection(event) {
    const clientOption = event.currentTarget;
    const clientId = parseInt(clientOption.dataset.clientId);
    const clientName = clientOption.dataset.clientName;
    const checkbox = clientOption.querySelector('input[type="checkbox"]');
    
    if (selectedClients.includes(clientId)) {
        // Deseleccionar
        selectedClients = selectedClients.filter(id => id !== clientId);
        clientOption.classList.remove('selected');
        checkbox.checked = false;
    } else {
        // Seleccionar (máximo 6 clientes)
        if (selectedClients.length < 6) {
            selectedClients.push(clientId);
            clientOption.classList.add('selected');
            checkbox.checked = true;
        } else {
            alert('Máximo 6 clientes para comparar');
            return;
        }
    }
    
    updateSelectedClientsList();
    updateCompareButton();
}

function updateSelectedClientsList() {
    const selectedList = document.getElementById('selectedClientsList');
    const selectedCount = document.getElementById('selectedCount');
    
    selectedCount.textContent = selectedClients.length;
    
    if (selectedClients.length === 0) {
        selectedList.innerHTML = '<p class="text-muted">Selecciona al menos 2 clientes para comparar</p>';
        return;
    }
    
    let html = '';
    selectedClients.forEach(clientId => {
        const clientOption = document.querySelector(`[data-client-id="${clientId}"]`);
        const clientName = clientOption.dataset.clientName;
        
        html += `
            <div class="selected-client-item">
                <span>${clientName}</span>
                <span class="remove-client" onclick="removeClient(${clientId})">
                    <i class="fas fa-times"></i>
                </span>
            </div>
        `;
    });
    
    selectedList.innerHTML = html;
}

function removeClient(clientId) {
    const clientOption = document.querySelector(`[data-client-id="${clientId}"]`);
    const checkbox = clientOption.querySelector('input[type="checkbox"]');
    
    selectedClients = selectedClients.filter(id => id !== clientId);
    clientOption.classList.remove('selected');
    checkbox.checked = false;
    
    updateSelectedClientsList();
    updateCompareButton();
    
    if (selectedClients.length >= 2) {
        updateComparison();
    } else {
        document.getElementById('comparisonResults').style.display = 'none';
    }
}

function updateCompareButton() {
    const compareBtn = document.getElementById('compareBtn');
    compareBtn.disabled = selectedClients.length < 2;
}

function generateComparison() {
    if (selectedClients.length < 2) {
        alert('Selecciona al menos 2 clientes para comparar');
        return;
    }
    
    const period = document.getElementById('comparisonPeriod').value;
    const metrics = getSelectedMetrics();
    
    // Mostrar loading
    showLoading();
    
    // Hacer petición a la API
    fetch('/api/trainer/progress/compare', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            client_ids: selectedClients,
            period: period,
            metrics: metrics
        })
    })
    .then(response => response.json())
    .then(data => {
        comparisonData = data;
        displayComparisonResults(data);
        document.getElementById('comparisonResults').style.display = 'block';
        
        // Scroll a los resultados
        document.getElementById('comparisonResults').scrollIntoView({
            behavior: 'smooth'
        });
    })
    .catch(error => {
        console.error('Error generating comparison:', error);
        alert('Error al generar la comparación');
    })
    .finally(() => {
        hideLoading();
    });
}

function getSelectedMetrics() {
    const metrics = [];
    document.querySelectorAll('.metrics-checkboxes input:checked').forEach(checkbox => {
        metrics.push(checkbox.id.replace('metric_', ''));
    });
    return metrics;
}

function displayComparisonResults(data) {
    updateComparisonChart(data.chartData);
    updatePerformanceRanking(data.ranking);
    updateComparisonTable(data.tableData);
    updateMuscleGroupComparison(data.muscleGroups);
    updateRecommendations(data.recommendations);
}

function initializeCharts() {
    // Gráfico de comparación principal
    const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');
    comparisonChart = new Chart(comparisonCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Gráfico de grupos musculares
    const muscleCtx = document.getElementById('muscleGroupComparison').getContext('2d');
    muscleGroupChart = new Chart(muscleCtx, {
        type: 'radar',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                r: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateComparisonChart(chartData) {
    comparisonChart.data.labels = chartData.labels;
    comparisonChart.data.datasets = chartData.datasets;
    comparisonChart.update();
}

function updatePerformanceRanking(ranking) {
    const rankingContainer = document.getElementById('performanceRanking');
    let html = '';
    
    ranking.forEach((client, index) => {
        const rankClass = index < 3 ? `rank-${index + 1}` : '';
        html += `
            <div class="ranking-item ${rankClass}">
                <div class="rank-number">${index + 1}</div>
                <div class="rank-info">
                    <div class="rank-name">${client.name}</div>
                    <div class="rank-score">Puntuación: ${client.score}</div>
                </div>
            </div>
        `;
    });
    
    rankingContainer.innerHTML = html;
}

function updateComparisonTable(tableData) {
    const tbody = document.querySelector('#comparisonTable tbody');
    let html = '';
    
    tableData.forEach(row => {
        html += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="client-avatar me-2" style="width: 30px; height: 30px;">
                            ${row.avatar ? `<img src="${row.avatar}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">` : 
                              `<div class="avatar-placeholder" style="width: 100%; height: 100%; border-radius: 50%; background: #007bff; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">${row.initials}</div>`}
                        </div>
                        <strong>${row.name}</strong>
                    </div>
                </td>
                <td>${row.workouts}</td>
                <td>
                    <span class="badge bg-${row.consistency >= 80 ? 'success' : row.consistency >= 60 ? 'warning' : 'danger'}">
                        ${row.consistency}%
                    </span>
                </td>
                <td>${row.volume} kg</td>
                <td>${row.rpe}</td>
                <td>${row.calories}</td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-${row.progress >= 80 ? 'success' : row.progress >= 60 ? 'warning' : 'danger'}" 
                             style="width: ${row.progress}%">
                            ${row.progress}%
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function updateMuscleGroupComparison(muscleData) {
    muscleGroupChart.data.labels = muscleData.labels;
    muscleGroupChart.data.datasets = muscleData.datasets;
    muscleGroupChart.update();
    
    // Actualizar insights
    const insightsContainer = document.getElementById('muscleGroupInsights');
    let html = '<h6>Análisis de Grupos Musculares</h6>';
    
    muscleData.insights.forEach(insight => {
        html += `
            <div class="insight-item mb-3">
                <h6 class="text-primary">${insight.title}</h6>
                <p class="text-muted">${insight.description}</p>
            </div>
        `;
    });
    
    insightsContainer.innerHTML = html;
}

function updateRecommendations(recommendations) {
    const recommendationsContainer = document.getElementById('recommendations');
    let html = '';
    
    recommendations.forEach(rec => {
        html += `
            <div class="recommendation-item priority-${rec.priority}">
                <div class="recommendation-title">${rec.title}</div>
                <div class="recommendation-description">${rec.description}</div>
                <div class="recommendation-actions">
                    ${rec.actions.map(action => 
                        `<button class="btn btn-sm btn-outline-primary" onclick="${action.onclick}">
                            <i class="${action.icon} me-1"></i>
                            ${action.label}
                        </button>`
                    ).join('')}
                </div>
            </div>
        `;
    });
    
    recommendationsContainer.innerHTML = html;
}

function updateComparison() {
    if (selectedClients.length >= 2) {
        generateComparison();
    }
}

function exportComparison() {
    if (Object.keys(comparisonData).length === 0) {
        alert('Genera una comparación primero');
        return;
    }
    
    const period = document.getElementById('comparisonPeriod').value;
    const metrics = getSelectedMetrics();
    
    const params = new URLSearchParams({
        client_ids: selectedClients.join(','),
        period: period,
        metrics: metrics.join(','),
        format: 'pdf'
    });
    
    window.open(`/api/trainer/progress/export-comparison?${params}`, '_blank');
}

function resetComparison() {
    // Limpiar selecciones
    selectedClients = [];
    document.querySelectorAll('.client-option').forEach(option => {
        option.classList.remove('selected');
        option.querySelector('input[type="checkbox"]').checked = false;
    });
    
    // Resetear controles
    document.getElementById('clientSearch').value = '';
    document.getElementById('comparisonPeriod').value = '30';
    document.querySelectorAll('.metrics-checkboxes input').forEach(checkbox => {
        checkbox.checked = ['metric_workouts', 'metric_consistency'].includes(checkbox.id);
    });
    
    // Ocultar resultados
    document.getElementById('comparisonResults').style.display = 'none';
    
    // Actualizar UI
    updateSelectedClientsList();
    updateCompareButton();
    filterClients();
}

function showLoading() {
    // Implementar indicador de carga
    document.getElementById('compareBtn').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando...';
    document.getElementById('compareBtn').disabled = true;
}

function hideLoading() {
    document.getElementById('compareBtn').innerHTML = '<i class="fas fa-chart-bar me-2"></i>Generar Comparación';
    updateCompareButton();
}
</script>