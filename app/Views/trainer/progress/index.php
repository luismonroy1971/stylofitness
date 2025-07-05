<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Dashboard de Seguimiento de Progreso - STYLOFITNESS
 * Panel principal para entrenadores para monitorear el progreso de sus clientes
 */

$currentUser = AppHelper::getCurrentUser();
$isAdmin = ($currentUser['role'] === 'admin');
?>

<div class="progress-dashboard">
    <!-- Header con estadísticas generales -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-chart-line me-2"></i>
                            Seguimiento de Progreso
                        </h1>
                        <p class="page-subtitle">
                            Monitorea el progreso y rendimiento de tus clientes en tiempo real
                        </p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="row stats-cards">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?= $stats['active_clients_count'] ?? 0 ?></h3>
                            <p class="stat-label">Clientes Activos</p>
                            <small class="stat-change text-success">
                                <i class="fas fa-arrow-up"></i> +12% este mes
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?= $stats['avg_workouts_per_client'] ?? 0 ?></h3>
                            <p class="stat-label">Entrenamientos/Cliente</p>
                            <small class="stat-change text-success">
                                <i class="fas fa-arrow-up"></i> +8% esta semana
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?= $stats['total_routines'] ?? 0 ?></h3>
                            <p class="stat-label">Rutinas Activas</p>
                            <small class="stat-change text-info">
                                <i class="fas fa-minus"></i> Sin cambios
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon bg-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?= count($alerts) ?></h3>
                            <p class="stat-label">Alertas Activas</p>
                            <small class="stat-change text-danger">
                                <i class="fas fa-arrow-up"></i> +3 nuevas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Panel de alertas -->
            <div class="col-xl-4 col-lg-12">
                <div class="card alerts-panel">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bell me-2"></i>
                            Alertas de Progreso
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($alerts)): ?>
                            <div class="no-alerts">
                                <i class="fas fa-check-circle text-success"></i>
                                <p class="mb-0">¡Excelente! No hay alertas pendientes.</p>
                            </div>
                        <?php else: ?>
                            <div class="alerts-list">
                                <?php foreach ($alerts as $alert): ?>
                                    <div class="alert-item priority-<?= $alert['priority'] ?>">
                                        <div class="alert-icon">
                                            <?php if ($alert['type'] === 'inactive_client'): ?>
                                                <i class="fas fa-user-clock"></i>
                                            <?php elseif ($alert['type'] === 'high_rpe'): ?>
                                                <i class="fas fa-thermometer-full"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="alert-content">
                                            <h6 class="alert-title"><?= htmlspecialchars($alert['client_name']) ?></h6>
                                            <p class="alert-message"><?= htmlspecialchars($alert['message']) ?></p>
                                            <div class="alert-actions">
                                                <a href="/trainer/progress/client/<?= $alert['client_id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    Ver Detalle
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Acciones Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="/trainer/progress/compare" class="quick-action-btn">
                                <i class="fas fa-balance-scale"></i>
                                <span>Comparar Clientes</span>
                            </a>
                            <a href="/trainer/progress/report" class="quick-action-btn">
                                <i class="fas fa-file-alt"></i>
                                <span>Generar Reporte</span>
                            </a>
                            <a href="/routines/create" class="quick-action-btn">
                                <i class="fas fa-plus"></i>
                                <span>Nueva Rutina</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de clientes activos -->
            <div class="col-xl-8 col-lg-12">
                <div class="card clients-panel">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>
                            Clientes Activos
                        </h5>
                        <div class="header-actions">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm active" data-period="30">
                                    30 días
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-period="7">
                                    7 días
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-period="90">
                                    90 días
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($activeClients)): ?>
                            <div class="no-clients">
                                <i class="fas fa-user-plus text-muted"></i>
                                <h6>No hay clientes activos</h6>
                                <p class="text-muted">Asigna rutinas a tus clientes para comenzar el seguimiento.</p>
                                <a href="/routines/create" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Crear Primera Rutina
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="clients-grid">
                                <?php foreach ($activeClients as $client): ?>
                                    <div class="client-card" data-client-id="<?= $client['id'] ?>">
                                        <div class="client-avatar">
                                            <?php if (!empty($client['avatar'])): ?>
                                                <img src="<?= htmlspecialchars($client['avatar']) ?>" 
                                                     alt="<?= htmlspecialchars($client['first_name']) ?>">
                                            <?php else: ?>
                                                <div class="avatar-placeholder">
                                                    <?= strtoupper(substr($client['first_name'], 0, 1) . substr($client['last_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Indicador de actividad -->
                                            <?php 
                                            $daysSinceLastWorkout = $client['last_workout'] 
                                                ? (new DateTime())->diff(new DateTime($client['last_workout']))->days 
                                                : 999;
                                            $activityStatus = $daysSinceLastWorkout <= 2 ? 'active' : 
                                                            ($daysSinceLastWorkout <= 7 ? 'moderate' : 'inactive');
                                            ?>
                                            <div class="activity-indicator status-<?= $activityStatus ?>"></div>
                                        </div>
                                        
                                        <div class="client-info">
                                            <h6 class="client-name">
                                                <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                                            </h6>
                                            <p class="client-email"><?= htmlspecialchars($client['email']) ?></p>
                                        </div>
                                        
                                        <div class="client-stats">
                                            <div class="stat-item">
                                                <span class="stat-value"><?= $client['workout_days'] ?></span>
                                                <span class="stat-label">Días</span>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-value"><?= $client['total_exercises'] ?></span>
                                                <span class="stat-label">Ejercicios</span>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-value"><?= round($client['avg_rpe'] ?? 0, 1) ?></span>
                                                <span class="stat-label">RPE</span>
                                            </div>
                                        </div>
                                        
                                        <div class="client-actions">
                                            <a href="/trainer/progress/client/<?= $client['id'] ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-chart-line me-1"></i>
                                                Ver Progreso
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="/routines?client=<?= $client['id'] ?>">
                                                            <i class="fas fa-dumbbell me-2"></i>
                                                            Ver Rutinas
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="/trainer/progress/report?client_id=<?= $client['id'] ?>">
                                                            <i class="fas fa-file-alt me-2"></i>
                                                            Generar Reporte
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="/routines/create?client=<?= $client['id'] ?>">
                                                            <i class="fas fa-plus me-2"></i>
                                                            Nueva Rutina
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <?php if ($client['last_workout']): ?>
                                            <div class="last-workout">
                                                <small class="text-muted">
                                                    Último entrenamiento: <?= date('d/m/Y', strtotime($client['last_workout'])) ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para vista rápida de progreso -->
<div class="modal fade" id="quickProgressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Progreso Rápido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="loading-spinner text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
                <div class="progress-content" style="display: none;">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress-dashboard {
    background: #f8f9fa;
    min-height: 100vh;
}

.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 0;
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
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

.stats-cards {
    margin-top: 2rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    margin-bottom: 1rem;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.5rem;
    color: white;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #2c3e50;
}

.stat-label {
    color: #6c757d;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.stat-change {
    font-size: 0.875rem;
    font-weight: 600;
}

.alerts-panel .card-body {
    max-height: 400px;
    overflow-y: auto;
}

.alert-item {
    display: flex;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    border-left: 4px solid;
}

.alert-item.priority-high {
    background: #fff5f5;
    border-left-color: #e53e3e;
}

.alert-item.priority-medium {
    background: #fffbf0;
    border-left-color: #dd6b20;
}

.alert-icon {
    margin-right: 0.75rem;
    font-size: 1.25rem;
}

.alert-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.alert-message {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.quick-actions {
    display: grid;
    gap: 0.75rem;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
    color: #007bff;
    text-decoration: none;
}

.quick-action-btn i {
    margin-right: 0.5rem;
    width: 20px;
}

.clients-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.client-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
}

.client-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.client-avatar {
    position: relative;
    width: 60px;
    height: 60px;
    margin: 0 auto 1rem;
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
    font-size: 1.25rem;
}

.activity-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
}

.activity-indicator.status-active {
    background: #28a745;
}

.activity-indicator.status-moderate {
    background: #ffc107;
}

.activity-indicator.status-inactive {
    background: #dc3545;
}

.client-info {
    text-align: center;
    margin-bottom: 1rem;
}

.client-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.client-email {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0;
}

.client-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-weight: 700;
    font-size: 1.25rem;
    color: #2c3e50;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.client-actions {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.client-actions .btn {
    flex: 1;
}

.last-workout {
    text-align: center;
    padding-top: 0.75rem;
    border-top: 1px solid #e9ecef;
}

.no-clients,
.no-alerts {
    text-align: center;
    padding: 3rem 1rem;
}

.no-clients i,
.no-alerts i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .stats-cards {
        margin-top: 1rem;
    }
    
    .clients-grid {
        grid-template-columns: 1fr;
    }
    
    .client-actions {
        flex-direction: column;
    }
}
</style>