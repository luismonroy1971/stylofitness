<?php
/**
 * Vista del Dashboard de Administrador - STYLOFITNESS
 * Muestra estadísticas generales y herramientas de administración
 */

use StyleFitness\Helpers\AppHelper;

$pageTitle = 'Dashboard Administrativo';
$user = AppHelper::getCurrentUser();
?>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <div class="container">
            <div class="welcome-section">
                <h1>Bienvenido, <?php echo htmlspecialchars($user['first_name']); ?></h1>
                <p class="subtitle">Panel de Control Administrativo</p>
            </div>
            <div class="quick-actions">
                <a href="/admin/users" class="btn btn-primary">
                    <i class="fas fa-users"></i> Gestionar Usuarios
                </a>
                <a href="/admin/products" class="btn btn-secondary">
                    <i class="fas fa-box"></i> Productos
                </a>
                <a href="/admin/classes" class="btn btn-accent">
                    <i class="fas fa-dumbbell"></i> Clases
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="container">
            <!-- Estadísticas principales -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($dashboardData['total_users']); ?></h3>
                        <p>Usuarios Activos</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>$<?php echo number_format($dashboardData['monthly_revenue'], 2); ?></h3>
                        <p>Ingresos del Mes</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($dashboardData['pending_orders']); ?></h3>
                        <p>Pedidos Pendientes</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($dashboardData['active_memberships']); ?></h3>
                        <p>Membresías Activas</p>
                    </div>
                </div>
            </div>

            <!-- Secciones de gestión -->
            <div class="management-grid">
                <div class="management-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Resumen de Ventas</h3>
                    </div>
                    <div class="card-content">
                        <canvas id="sales-chart" width="400" height="200"></canvas>
                    </div>
                </div>

                <div class="management-card">
                    <div class="card-header">
                        <h3><i class="fas fa-tasks"></i> Acciones Rápidas</h3>
                    </div>
                    <div class="card-content">
                        <div class="quick-links">
                            <a href="/admin/users/create" class="quick-link">
                                <i class="fas fa-user-plus"></i>
                                <span>Nuevo Usuario</span>
                            </a>
                            <a href="/admin/products/create" class="quick-link">
                                <i class="fas fa-plus-circle"></i>
                                <span>Nuevo Producto</span>
                            </a>
                            <a href="/admin/classes/create" class="quick-link">
                                <i class="fas fa-calendar-plus"></i>
                                <span>Nueva Clase</span>
                            </a>
                            <a href="/admin/reports" class="quick-link">
                                <i class="fas fa-file-alt"></i>
                                <span>Reportes</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad reciente -->
            <div class="recent-activity">
                <div class="card-header">
                    <h3><i class="fas fa-clock"></i> Actividad Reciente</h3>
                </div>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-plus text-success"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>Nuevo usuario registrado</strong></p>
                            <small class="text-muted">Hace 2 horas</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-shopping-cart text-primary"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>Nueva orden procesada</strong></p>
                            <small class="text-muted">Hace 4 horas</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-dumbbell text-warning"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>Clase programada</strong></p>
                            <small class="text-muted">Hace 6 horas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-dashboard {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding-top: 2rem;
}

.dashboard-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    margin-bottom: 2rem;
    padding: 2rem;
}

.welcome-section h1 {
    color: white;
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.welcome-section .subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
}

.quick-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(45deg, #FF6B00, #FF8E53);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-content h3 {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin: 0;
}

.stat-content p {
    color: #666;
    margin: 0;
    font-size: 0.9rem;
}

.management-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.management-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    padding: 1rem 1.5rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1.1rem;
}

.card-content {
    padding: 1.5rem;
}

.quick-links {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.quick-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    border: 2px dashed #ddd;
    border-radius: 10px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s ease;
}

.quick-link:hover {
    border-color: #FF6B00;
    color: #FF6B00;
    background: rgba(255, 107, 0, 0.05);
}

.quick-link i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.recent-activity {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.activity-list {
    padding: 1.5rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.text-success { color: #28a745 !important; }
.text-primary { color: #007bff !important; }
.text-warning { color: #ffc107 !important; }
.text-muted { color: #6c757d !important; }

@media (max-width: 768px) {
    .management-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Inicializar gráfico de ventas si Chart.js está disponible
if (typeof Chart !== 'undefined') {
    const ctx = document.getElementById('sales-chart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Ventas ($)',
                    data: [12000, 19000, 15000, 25000, 22000, 30000],
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
                        beginAtZero: true
                    }
                }
            }
        });
    }
}
</script>