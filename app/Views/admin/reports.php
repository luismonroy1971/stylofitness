<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista de Reportes y Análisis - STYLOFITNESS
 * Panel de reportes y estadísticas del sistema
 */
?>

<div class="admin-layout full-width">
<div class="admin-container">
    <?php include APP_PATH . '/Views/admin/partials/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-chart-bar"></i> Reportes y Análisis</h1>
            <div class="admin-actions">
                <button type="button" class="btn btn-primary" onclick="exportReport()">
                    <i class="fas fa-download"></i> Exportar Reporte
                </button>
            </div>
        </div>

        <!-- Filtros de reporte -->
        <div class="admin-filters">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label for="type">Tipo de Reporte:</label>
                    <select name="type" id="type" class="form-control" onchange="this.form.submit()">
                        <option value="overview" <?= (($_GET['type'] ?? 'overview') === 'overview') ? 'selected' : '' ?>>Resumen General</option>
                        <option value="sales" <?= (($_GET['type'] ?? '') === 'sales') ? 'selected' : '' ?>>Ventas</option>
                        <option value="users" <?= (($_GET['type'] ?? '') === 'users') ? 'selected' : '' ?>>Usuarios</option>
                        <option value="routines" <?= (($_GET['type'] ?? '') === 'routines') ? 'selected' : '' ?>>Rutinas</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date_from">Desde:</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" 
                           value="<?= htmlspecialchars($_GET['date_from'] ?? date('Y-m-01')) ?>">
                </div>
                
                <div class="filter-group">
                    <label for="date_to">Hasta:</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" 
                           value="<?= htmlspecialchars($_GET['date_to'] ?? date('Y-m-d')) ?>">
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>

        <!-- Contenido del reporte -->
        <div class="reports-content">
            <?php $reportType = $_GET['type'] ?? 'overview'; ?>
            
            <?php if ($reportType === 'overview'): ?>
                <!-- Reporte General -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $reportData['total_users'] ?? 0 ?></h3>
                                <p>Total Usuarios</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $reportData['total_orders'] ?? 0 ?></h3>
                                <p>Total Pedidos</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $reportData['total_products'] ?? 0 ?></h3>
                                <p>Total Productos</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-dumbbell"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $reportData['total_routines'] ?? 0 ?></h3>
                                <p>Total Rutinas</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráficos -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h4>Ventas por Día</h4>
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h4>Nuevos Usuarios</h4>
                            <canvas id="usersChart"></canvas>
                        </div>
                    </div>
                </div>
                
            <?php elseif ($reportType === 'sales'): ?>
                <!-- Reporte de Ventas -->
                <div class="report-section">
                    <h3>Reporte de Ventas</h3>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="metric-card">
                                <h4>Ingresos Totales</h4>
                                <p class="metric-value">$<?= number_format($reportData['total_revenue'] ?? 0, 2) ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="metric-card">
                                <h4>Pedidos Completados</h4>
                                <p class="metric-value"><?= $reportData['completed_orders'] ?? 0 ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="metric-card">
                                <h4>Ticket Promedio</h4>
                                <p class="metric-value">$<?= number_format($reportData['average_order'] ?? 0, 2) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container mt-4">
                        <canvas id="salesDetailChart"></canvas>
                    </div>
                </div>
                
            <?php elseif ($reportType === 'users'): ?>
                <!-- Reporte de Usuarios -->
                <div class="report-section">
                    <h3>Reporte de Usuarios</h3>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric-card">
                                <h4>Nuevos Usuarios</h4>
                                <p class="metric-value"><?= $reportData['new_users'] ?? 0 ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="metric-card">
                                <h4>Usuarios Activos</h4>
                                <p class="metric-value"><?= $reportData['active_users'] ?? 0 ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="metric-card">
                                <h4>Clientes</h4>
                                <p class="metric-value"><?= $reportData['clients'] ?? 0 ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="metric-card">
                                <h4>Instructores</h4>
                                <p class="metric-value"><?= $reportData['instructors'] ?? 0 ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container mt-4">
                        <canvas id="usersDetailChart"></canvas>
                    </div>
                </div>
                
            <?php elseif ($reportType === 'routines'): ?>
                <!-- Reporte de Rutinas -->
                <div class="report-section">
                    <h3>Reporte de Rutinas</h3>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="metric-card">
                                <h4>Rutinas Creadas</h4>
                                <p class="metric-value"><?= $reportData['created_routines'] ?? 0 ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="metric-card">
                                <h4>Rutinas Activas</h4>
                                <p class="metric-value"><?= $reportData['active_routines'] ?? 0 ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="metric-card">
                                <h4>Plantillas</h4>
                                <p class="metric-value"><?= $reportData['templates'] ?? 0 ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container mt-4">
                        <canvas id="routinesDetailChart"></canvas>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

<style>
.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: white;
    font-size: 24px;
}

.stat-content h3 {
    margin: 0;
    font-size: 28px;
    font-weight: bold;
    color: #333;
}

.stat-content p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.chart-container {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.chart-container h4 {
    margin-bottom: 20px;
    color: #333;
}

.metric-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
    margin-bottom: 20px;
}

.metric-card h4 {
    margin-bottom: 10px;
    color: #666;
    font-size: 14px;
    text-transform: uppercase;
}

.metric-value {
    font-size: 32px;
    font-weight: bold;
    color: #333;
    margin: 0;
}

.report-section {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.report-section h3 {
    margin-bottom: 30px;
    color: #333;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
}
</style>

<script>
function exportReport() {
    const reportType = document.getElementById('type').value;
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    
    const params = new URLSearchParams({
        type: reportType,
        date_from: dateFrom,
        date_to: dateTo,
        export: 'pdf'
    });
    
    window.open(`/admin/reports/export?${params.toString()}`, '_blank');
}

// Inicializar gráficos si Chart.js está disponible
if (typeof Chart !== 'undefined') {
    // Configuración base para gráficos
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        }
    };
    
    // Gráfico de ventas
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($reportData['sales_labels'] ?? []) ?>,
                datasets: [{
                    label: 'Ventas',
                    data: <?= json_encode($reportData['sales_data'] ?? []) ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: chartOptions
        });
    }
    
    // Gráfico de usuarios
    const usersCtx = document.getElementById('usersChart');
    if (usersCtx) {
        new Chart(usersCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($reportData['users_labels'] ?? []) ?>,
                datasets: [{
                    label: 'Nuevos Usuarios',
                    data: <?= json_encode($reportData['users_data'] ?? []) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });
    }
}
</script>