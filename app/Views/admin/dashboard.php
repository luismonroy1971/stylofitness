<?php
use StyleFitness\Helpers\AppHelper;
?>

<div class="admin-dashboard">
    <!-- Flash Messages -->
    <?php if (AppHelper::hasFlashMessage('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= AppHelper::getFlashMessage('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (AppHelper::hasFlashMessage('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= AppHelper::getFlashMessage('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Panel de Administración</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
            <a href="/admin/reports" class="btn btn-primary">
                <i class="fas fa-chart-bar"></i> Reportes
            </a>
        </div>
    </div>

    <!-- System Alerts -->
    <?php if (!empty($alerts)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Alertas del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($alerts as $alert): ?>
                            <div class="alert alert-<?= $alert['type'] ?> mb-2" role="alert">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($alert['message']) ?></span>
                                    <?php if (isset($alert['action'])): ?>
                                        <a href="<?= $alert['action'] ?>" class="btn btn-sm btn-outline-<?= $alert['type'] ?>">
                                            Ver detalles
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Usuarios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['total_users']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ingresos del Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats['monthly_revenue'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Productos Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['total_products']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pedidos Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['pending_orders']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Clientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['total_clients']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Instructores
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['total_instructors']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Rutinas Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['total_routines']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Membresías Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['active_memberships']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ventas de los Últimos 30 Días</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Nuevos Usuarios</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="usersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actividad Reciente</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentActivity)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($activity['action'] ?? 'Actividad') ?></h6>
                                        <small><?= AppHelper::timeAgo($activity['created_at']) ?></small>
                                    </div>
                                    <p class="mb-1">
                                        <?= htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']) ?>
                                    </p>
                                    <small><?= htmlspecialchars($activity['description'] ?? '') ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No hay actividad reciente registrada.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Accesos Rápidos</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="/admin/users" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-users"></i><br>
                                Gestionar Usuarios
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="/admin/products" class="btn btn-outline-success btn-block">
                                <i class="fas fa-box"></i><br>
                                Gestionar Productos
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="/admin/orders" class="btn btn-outline-info btn-block">
                                <i class="fas fa-shopping-cart"></i><br>
                                Ver Pedidos
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="/admin/routines" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-list"></i><br>
                                Gestionar Rutinas
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="/admin/classes" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-calendar"></i><br>
                                Clases Grupales
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="/admin/reports" class="btn btn-outline-dark btn-block">
                                <i class="fas fa-chart-bar"></i><br>
                                Reportes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Data for JavaScript -->
<script>
    window.chartData = <?= json_encode($chartData) ?>;
</script>

<script>
function refreshDashboard() {
    location.reload();
}

// Initialize charts when document is ready
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined' && window.chartData) {
        // Sales Chart
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: window.chartData.labels.map(date => {
                        return new Date(date).toLocaleDateString('es-ES', { month: 'short', day: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Ventas ($)',
                        data: window.chartData.sales,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Users Chart
        const usersCtx = document.getElementById('usersChart');
        if (usersCtx) {
            new Chart(usersCtx, {
                type: 'line',
                data: {
                    labels: window.chartData.labels.slice(-7).map(date => {
                        return new Date(date).toLocaleDateString('es-ES', { weekday: 'short' });
                    }),
                    datasets: [{
                        label: 'Nuevos Usuarios',
                        data: window.chartData.users.slice(-7),
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
});
</script>