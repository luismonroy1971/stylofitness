<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Progreso Detallado del Cliente - STYLOFITNESS
 * Vista detallada del progreso de un cliente específico para entrenadores
 */

$currentUser = AppHelper::getCurrentUser();
$isAdmin = ($currentUser['role'] === 'admin');
?>

<div class="client-progress-detail">
    <!-- Header del cliente -->
    <div class="client-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-auto">
                    <a href="/trainer/progress" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver
                    </a>
                </div>
                <div class="col">
                    <div class="client-info-header">
                        <div class="client-avatar-large">
                            <?php if (!empty($client['avatar'])): ?>
                                <img src="<?= htmlspecialchars($client['avatar']) ?>" 
                                     alt="<?= htmlspecialchars($client['first_name']) ?>">
                            <?php else: ?>
                                <div class="avatar-placeholder-large">
                                    <?= strtoupper(substr($client['first_name'], 0, 1) . substr($client['last_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="client-details">
                            <h1 class="client-name">
                                <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                            </h1>
                            <p class="client-meta">
                                <span class="badge bg-primary me-2"><?= ucfirst($client['role']) ?></span>
                                <span class="text-light opacity-75"><?= htmlspecialchars($client['email']) ?></span>
                            </p>
                            <div class="client-quick-stats">
                                <div class="quick-stat">
                                    <span class="value"><?= $clientStats['total_workouts'] ?></span>
                                    <span class="label">Entrenamientos</span>
                                </div>
                                <div class="quick-stat">
                                    <span class="value"><?= $clientStats['workout_days'] ?></span>
                                    <span class="label">Días Activos</span>
                                </div>
                                <div class="quick-stat">
                                    <span class="value"><?= round($clientStats['avg_rpe'], 1) ?></span>
                                    <span class="label">RPE Promedio</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="header-actions">
                        <div class="btn-group">
                            <button type="button" class="btn btn-light" data-period="7" onclick="updatePeriod(7)">
                                7 días
                            </button>
                            <button type="button" class="btn btn-light active" data-period="30" onclick="updatePeriod(30)">
                                30 días
                            </button>
                            <button type="button" class="btn btn-light" data-period="90" onclick="updatePeriod(90)">
                                90 días
                            </button>
                        </div>
                        <div class="dropdown ms-2">
                            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="/trainer/progress/report?client_id=<?= $client['id'] ?>">
                                        <i class="fas fa-file-alt me-2"></i>
                                        Generar Reporte
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/routines/create?client=<?= $client['id'] ?>">
                                        <i class="fas fa-plus me-2"></i>
                                        Nueva Rutina
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportProgress()">
                                        <i class="fas fa-download me-2"></i>
                                        Exportar Datos
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Panel izquierdo: Gráficos y tendencias -->
            <div class="col-xl-8 col-lg-7">
                <!-- Gráfico de progreso principal -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Progreso de Entrenamientos
                            </h5>
                            <div class="chart-controls">
                                <select class="form-select form-select-sm" id="chartMetric">
                                    <option value="workouts">Entrenamientos</option>
                                    <option value="volume">Volumen Total</option>
                                    <option value="rpe">RPE Promedio</option>
                                    <option value="calories">Calorías</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="progressChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Análisis de ejercicios -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-dumbbell me-2"></i>
                            Análisis de Ejercicios
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Ejercicios Más Frecuentes</h6>
                                <div class="exercise-list">
                                    <?php foreach ($exerciseStats['most_frequent'] as $exercise): ?>
                                        <div class="exercise-item">
                                            <div class="exercise-info">
                                                <span class="exercise-name"><?= htmlspecialchars($exercise['name']) ?></span>
                                                <small class="text-muted"><?= $exercise['count'] ?> veces</small>
                                            </div>
                                            <div class="exercise-progress">
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: <?= ($exercise['count'] / $exerciseStats['max_count']) * 100 ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Progreso por Grupo Muscular</h6>
                                <canvas id="muscleGroupChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de entrenamientos recientes -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            Entrenamientos Recientes
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentWorkouts)): ?>
                            <div class="no-workouts text-center py-4">
                                <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                                <h6 class="text-muted">No hay entrenamientos registrados</h6>
                                <p class="text-muted">Los entrenamientos aparecerán aquí una vez que el cliente comience a registrar su actividad.</p>
                            </div>
                        <?php else: ?>
                            <div class="workout-timeline">
                                <?php foreach ($recentWorkouts as $workout): ?>
                                    <div class="workout-entry">
                                        <div class="workout-date">
                                            <div class="date-circle">
                                                <span class="day"><?= date('d', strtotime($workout['workout_date'])) ?></span>
                                                <span class="month"><?= date('M', strtotime($workout['workout_date'])) ?></span>
                                            </div>
                                        </div>
                                        <div class="workout-content">
                                            <div class="workout-header">
                                                <h6 class="workout-title"><?= htmlspecialchars($workout['routine_name']) ?></h6>
                                                <div class="workout-badges">
                                                    <span class="badge bg-primary"><?= $workout['exercise_count'] ?> ejercicios</span>
                                                    <span class="badge bg-success"><?= $workout['total_sets'] ?> series</span>
                                                    <?php if ($workout['avg_rpe']): ?>
                                                        <span class="badge bg-warning">RPE <?= round($workout['avg_rpe'], 1) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="workout-stats">
                                                <div class="stat-group">
                                                    <i class="fas fa-clock text-muted"></i>
                                                    <span><?= gmdate('H:i', $workout['total_duration']) ?></span>
                                                </div>
                                                <?php if ($workout['calories_burned']): ?>
                                                    <div class="stat-group">
                                                        <i class="fas fa-fire text-danger"></i>
                                                        <span><?= $workout['calories_burned'] ?> cal</span>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="stat-group">
                                                    <i class="fas fa-weight-hanging text-info"></i>
                                                    <span><?= number_format($workout['total_volume']) ?> kg</span>
                                                </div>
                                            </div>
                                            <?php if (!empty($workout['notes'])): ?>
                                                <div class="workout-notes">
                                                    <small class="text-muted">
                                                        <i class="fas fa-sticky-note me-1"></i>
                                                        <?= htmlspecialchars($workout['notes']) ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Panel derecho: Estadísticas y métricas -->
            <div class="col-xl-4 col-lg-5">
                <!-- Métricas clave -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Métricas Clave
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="metrics-grid">
                            <div class="metric-card">
                                <div class="metric-icon bg-primary">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="metric-content">
                                    <h4><?= $clientStats['consistency_percentage'] ?>%</h4>
                                    <p>Consistencia</p>
                                    <small class="text-muted">Últimos 30 días</small>
                                </div>
                            </div>

                            <div class="metric-card">
                                <div class="metric-icon bg-success">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <div class="metric-content">
                                    <h4><?= number_format($clientStats['total_calories']) ?></h4>
                                    <p>Calorías Quemadas</p>
                                    <small class="text-muted">Total acumulado</small>
                                </div>
                            </div>

                            <div class="metric-card">
                                <div class="metric-icon bg-warning">
                                    <i class="fas fa-weight-hanging"></i>
                                </div>
                                <div class="metric-content">
                                    <h4><?= number_format($clientStats['total_volume']) ?></h4>
                                    <p>Volumen Total (kg)</p>
                                    <small class="text-muted">Peso levantado</small>
                                </div>
                            </div>

                            <div class="metric-card">
                                <div class="metric-icon bg-info">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="metric-content">
                                    <h4><?= $clientStats['personal_records'] ?></h4>
                                    <p>Récords Personales</p>
                                    <small class="text-muted">Nuevos este mes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progreso físico -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-ruler me-2"></i>
                            Progreso Físico
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($physicalProgress)): ?>
                            <div class="no-measurements text-center py-3">
                                <i class="fas fa-ruler-combined text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">No hay mediciones registradas</p>
                                <small class="text-muted">Anima al cliente a registrar sus medidas</small>
                            </div>
                        <?php else: ?>
                            <div class="progress-measurements">
                                <?php foreach ($physicalProgress as $measurement): ?>
                                    <div class="measurement-item">
                                        <div class="measurement-label">
                                            <span><?= htmlspecialchars($measurement['label']) ?></span>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($measurement['date'])) ?></small>
                                        </div>
                                        <div class="measurement-value">
                                            <span class="current"><?= $measurement['current'] ?> <?= $measurement['unit'] ?></span>
                                            <?php if ($measurement['change'] != 0): ?>
                                                <span class="change <?= $measurement['change'] > 0 ? 'positive' : 'negative' ?>">
                                                    <?= $measurement['change'] > 0 ? '+' : '' ?><?= $measurement['change'] ?> <?= $measurement['unit'] ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Rutinas activas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list-alt me-2"></i>
                            Rutinas Activas
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($activeRoutines)): ?>
                            <div class="no-routines text-center py-3">
                                <i class="fas fa-plus-circle text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-2">No hay rutinas activas</p>
                                <a href="/routines/create?client=<?= $client['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>
                                    Crear Rutina
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="routines-list">
                                <?php foreach ($activeRoutines as $routine): ?>
                                    <div class="routine-item">
                                        <div class="routine-header">
                                            <h6 class="routine-name"><?= htmlspecialchars($routine['name']) ?></h6>
                                            <span class="badge bg-<?= $routine['completion_rate'] >= 80 ? 'success' : ($routine['completion_rate'] >= 50 ? 'warning' : 'danger') ?>">
                                                <?= round($routine['completion_rate']) ?>%
                                            </span>
                                        </div>
                                        <div class="routine-stats">
                                            <small class="text-muted">
                                                <?= $routine['exercises_count'] ?> ejercicios • 
                                                <?= $routine['sessions_completed'] ?>/<?= $routine['total_sessions'] ?> sesiones
                                            </small>
                                        </div>
                                        <div class="routine-progress">
                                            <div class="progress">
                                                <div class="progress-bar" style="width: <?= $routine['completion_rate'] ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="routine-actions mt-2">
                                            <a href="/routines/view/<?= $routine['id'] ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>
                                                Ver
                                            </a>
                                            <a href="/routines/edit/<?= $routine['id'] ?>" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-edit me-1"></i>
                                                Editar
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Notas del entrenador -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sticky-note me-2"></i>
                            Notas del Entrenador
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="trainerNotesForm">
                            <div class="mb-3">
                                <textarea class="form-control" id="trainerNotes" rows="4" 
                                          placeholder="Añade notas sobre el progreso del cliente..."><?= htmlspecialchars($trainerNotes ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-1"></i>
                                Guardar Notas
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.client-progress-detail {
    background: #f8f9fa;
    min-height: 100vh;
}

.client-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
}

.client-info-header {
    display: flex;
    align-items: center;
}

.client-avatar-large {
    width: 80px;
    height: 80px;
    margin-right: 1.5rem;
}

.client-avatar-large img,
.avatar-placeholder-large {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-placeholder-large {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 2rem;
}

.client-name {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.client-quick-stats {
    display: flex;
    gap: 2rem;
    margin-top: 1rem;
}

.quick-stat {
    text-align: center;
}

.quick-stat .value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
}

.quick-stat .label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.metrics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.metric-card {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.metric-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
}

.metric-content h4 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.metric-content p {
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.exercise-list {
    margin-bottom: 1rem;
}

.exercise-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 0.75rem;
}

.exercise-progress {
    width: 100px;
    margin-left: 1rem;
}

.exercise-progress .progress {
    height: 6px;
}

.workout-timeline {
    position: relative;
}

.workout-entry {
    display: flex;
    margin-bottom: 2rem;
    position: relative;
}

.workout-entry:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 30px;
    top: 60px;
    bottom: -2rem;
    width: 2px;
    background: #e9ecef;
}

.workout-date {
    margin-right: 1rem;
}

.date-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.date-circle .day {
    font-size: 1.25rem;
    line-height: 1;
}

.date-circle .month {
    font-size: 0.75rem;
    text-transform: uppercase;
}

.workout-content {
    flex: 1;
    background: white;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.workout-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.workout-title {
    font-weight: 600;
    margin-bottom: 0;
}

.workout-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.workout-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.stat-group {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
}

.progress-measurements {
    margin-bottom: 1rem;
}

.measurement-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 1rem;
}

.measurement-value {
    text-align: right;
}

.measurement-value .current {
    font-weight: 600;
    display: block;
}

.measurement-value .change {
    font-size: 0.875rem;
    font-weight: 500;
}

.measurement-value .change.positive {
    color: #28a745;
}

.measurement-value .change.negative {
    color: #dc3545;
}

.routines-list {
    margin-bottom: 1rem;
}

.routine-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.routine-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.routine-name {
    font-weight: 600;
    margin-bottom: 0;
}

.routine-progress .progress {
    height: 6px;
    margin-top: 0.5rem;
}

.routine-actions {
    display: flex;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .client-info-header {
        flex-direction: column;
        text-align: center;
    }
    
    .client-avatar-large {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .client-quick-stats {
        justify-content: center;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .workout-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .workout-badges {
        margin-top: 0.5rem;
    }
}
</style>

<script>
// Variables globales
let currentPeriod = 30;
let progressChart;
let muscleGroupChart;

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    loadProgressData();
    setupEventListeners();
});

function initializeCharts() {
    // Gráfico de progreso principal
    const progressCtx = document.getElementById('progressChart').getContext('2d');
    progressChart = new Chart(progressCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Entrenamientos',
                data: [],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
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
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de grupos musculares
    const muscleCtx = document.getElementById('muscleGroupChart').getContext('2d');
    muscleGroupChart = new Chart(muscleCtx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function updatePeriod(days) {
    currentPeriod = days;
    
    // Actualizar botones activos
    document.querySelectorAll('[data-period]').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-period="${days}"]`).classList.add('active');
    
    // Recargar datos
    loadProgressData();
}

function loadProgressData() {
    const clientId = <?= $client['id'] ?>;
    
    fetch(`/api/trainer/progress/client/${clientId}?period=${currentPeriod}`)
        .then(response => response.json())
        .then(data => {
            updateProgressChart(data.progress);
            updateMuscleGroupChart(data.muscleGroups);
        })
        .catch(error => {
            console.error('Error loading progress data:', error);
        });
}

function updateProgressChart(data) {
    const metric = document.getElementById('chartMetric').value;
    
    progressChart.data.labels = data.labels;
    progressChart.data.datasets[0].data = data[metric];
    progressChart.data.datasets[0].label = getMetricLabel(metric);
    progressChart.update();
}

function updateMuscleGroupChart(data) {
    muscleGroupChart.data.labels = data.labels;
    muscleGroupChart.data.datasets[0].data = data.values;
    muscleGroupChart.update();
}

function getMetricLabel(metric) {
    const labels = {
        'workouts': 'Entrenamientos',
        'volume': 'Volumen Total (kg)',
        'rpe': 'RPE Promedio',
        'calories': 'Calorías Quemadas'
    };
    return labels[metric] || 'Métrica';
}

function setupEventListeners() {
    // Cambio de métrica en el gráfico
    document.getElementById('chartMetric').addEventListener('change', function() {
        loadProgressData();
    });
    
    // Formulario de notas del entrenador
    document.getElementById('trainerNotesForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveTrainerNotes();
    });
}

function saveTrainerNotes() {
    const notes = document.getElementById('trainerNotes').value;
    const clientId = <?= $client['id'] ?>;
    
    fetch('/api/trainer/notes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            client_id: clientId,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Notas guardadas correctamente', 'success');
        } else {
            showNotification('Error al guardar las notas', 'error');
        }
    })
    .catch(error => {
        console.error('Error saving notes:', error);
        showNotification('Error al guardar las notas', 'error');
    });
}

function exportProgress() {
    const clientId = <?= $client['id'] ?>;
    window.open(`/api/trainer/progress/export/${clientId}?period=${currentPeriod}`, '_blank');
}

function showNotification(message, type) {
    // Implementar sistema de notificaciones
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-dismiss después de 3 segundos
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 3000);
}
</script>