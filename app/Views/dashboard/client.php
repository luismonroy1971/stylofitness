<?php
/**
 * Vista del Dashboard de Cliente - STYLOFITNESS
 * Muestra información personalizada para clientes
 */

use StyleFitness\Helpers\AppHelper;

$pageTitle = 'Mi Dashboard';
$user = AppHelper::getCurrentUser();
?>

<div class="client-dashboard">
    <div class="dashboard-header">
        <div class="container">
            <div class="welcome-section">
                <h1>¡Hola, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                <p class="subtitle">Continúa tu transformación</p>
            </div>
            <div class="quick-actions">
                <a href="/routines" class="btn btn-primary">
                    <i class="fas fa-dumbbell"></i> Mi Rutina
                </a>
                <a href="/classes" class="btn btn-secondary">
                    <i class="fas fa-users"></i> Clases
                </a>
                <a href="/store" class="btn btn-accent">
                    <i class="fas fa-shopping-cart"></i> Tienda
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="container">
            <!-- Estadísticas del cliente -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($dashboardData['completed_workouts']); ?></h3>
                        <p>Entrenamientos Completados</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo count($dashboardData['upcoming_classes']); ?></h3>
                        <p>Clases Reservadas</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-content">
                        <h3>85%</h3>
                        <p>Progreso del Mes</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3>12h</h3>
                        <p>Tiempo Total</p>
                    </div>
                </div>
            </div>

            <!-- Sección principal -->
            <div class="main-grid">
                <!-- Rutina actual -->
                <div class="main-card">
                    <div class="card-header">
                        <h3><i class="fas fa-dumbbell"></i> Mi Rutina Actual</h3>
                        <?php if ($dashboardData['active_routine']): ?>
                            <a href="/routines/<?php echo $dashboardData['active_routine']['id']; ?>" class="btn btn-sm btn-outline">Ver Completa</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-content">
                        <?php if ($dashboardData['active_routine']): ?>
                            <div class="routine-info">
                                <h4><?php echo htmlspecialchars($dashboardData['active_routine']['name']); ?></h4>
                                <p class="routine-description"><?php echo htmlspecialchars($dashboardData['active_routine']['description']); ?></p>
                                <div class="routine-stats">
                                    <div class="stat-item">
                                        <span class="label">Duración:</span>
                                        <span class="value"><?php echo $dashboardData['active_routine']['duration_weeks']; ?> semanas</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="label">Nivel:</span>
                                        <span class="value"><?php echo ucfirst($dashboardData['active_routine']['difficulty_level']); ?></span>
                                    </div>
                                </div>
                                <div class="routine-actions">
                                    <a href="/routines/<?php echo $dashboardData['active_routine']['id']; ?>/start" class="btn btn-primary btn-block">
                                        <i class="fas fa-play"></i> Continuar Entrenamiento
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="no-routine">
                                <i class="fas fa-dumbbell"></i>
                                <h4>No tienes una rutina asignada</h4>
                                <p>Contacta a tu instructor para que te asigne una rutina personalizada.</p>
                                <a href="/contact" class="btn btn-primary">Contactar Instructor</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Próximas clases -->
                <div class="main-card">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar"></i> Próximas Clases</h3>
                        <a href="/classes" class="btn btn-sm btn-outline">Ver Todas</a>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($dashboardData['upcoming_classes'])): ?>
                            <div class="classes-list">
                                <?php foreach (array_slice($dashboardData['upcoming_classes'], 0, 3) as $class): ?>
                                    <div class="class-item">
                                        <div class="class-time">
                                            <span class="time"><?php echo date('H:i', strtotime($class['start_time'])); ?></span>
                                            <span class="date"><?php echo date('d/m', strtotime($class['booking_date'])); ?></span>
                                        </div>
                                        <div class="class-info">
                                            <h4><?php echo htmlspecialchars($class['name']); ?></h4>
                                            <p><?php echo ucfirst($class['day_of_week']); ?></p>
                                        </div>
                                        <div class="class-status">
                                            <span class="badge badge-success">Reservada</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-classes">
                                <i class="fas fa-calendar-plus"></i>
                                <h4>No tienes clases reservadas</h4>
                                <p>Explora nuestras clases grupales y reserva tu lugar.</p>
                                <a href="/classes" class="btn btn-primary">Explorar Clases</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Progreso y pedidos recientes -->
            <div class="secondary-grid">
                <!-- Progreso semanal -->
                <div class="progress-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Progreso Semanal</h3>
                    </div>
                    <div class="card-content">
                        <div class="progress-chart">
                            <canvas id="progress-chart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Pedidos recientes -->
                <div class="orders-card">
                    <div class="card-header">
                        <h3><i class="fas fa-shopping-bag"></i> Pedidos Recientes</h3>
                        <a href="/user/orders" class="btn btn-sm btn-outline">Ver Todos</a>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($dashboardData['recent_orders'])): ?>
                            <div class="orders-list">
                                <?php foreach ($dashboardData['recent_orders'] as $order): ?>
                                    <div class="order-item">
                                        <div class="order-info">
                                            <h4>Pedido #<?php echo $order['id']; ?></h4>
                                            <p>$<?php echo number_format($order['total_amount'], 2); ?></p>
                                            <small><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></small>
                                        </div>
                                        <div class="order-status">
                                            <span class="badge badge-<?php echo $order['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-orders">
                                <i class="fas fa-shopping-cart"></i>
                                <h4>No tienes pedidos recientes</h4>
                                <p>Explora nuestra tienda de suplementos y equipos.</p>
                                <a href="/store" class="btn btn-primary">Ir a la Tienda</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="quick-tools">
                <div class="card-header">
                    <h3><i class="fas fa-bolt"></i> Acciones Rápidas</h3>
                </div>
                <div class="tools-grid">
                    <a href="/user/profile" class="tool-card">
                        <i class="fas fa-user-edit"></i>
                        <h4>Editar Perfil</h4>
                        <p>Actualiza tu información personal</p>
                    </a>
                    <a href="/user/measurements" class="tool-card">
                        <i class="fas fa-ruler"></i>
                        <h4>Mis Medidas</h4>
                        <p>Registra tu progreso físico</p>
                    </a>
                    <a href="/user/nutrition" class="tool-card">
                        <i class="fas fa-apple-alt"></i>
                        <h4>Plan Nutricional</h4>
                        <p>Consulta tu dieta personalizada</p>
                    </a>
                    <a href="/support" class="tool-card">
                        <i class="fas fa-headset"></i>
                        <h4>Soporte</h4>
                        <p>¿Necesitas ayuda? Contáctanos</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.client-dashboard {
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

.main-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.secondary-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.main-card, .progress-card, .orders-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 1.1rem;
}

.card-content {
    padding: 1.5rem;
}

.routine-info h4 {
    color: #333;
    margin: 0 0 0.5rem 0;
    font-size: 1.3rem;
}

.routine-description {
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.routine-stats {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.stat-item .label {
    font-size: 0.8rem;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-item .value {
    font-weight: bold;
    color: #333;
}

.btn-block {
    width: 100%;
    text-align: center;
}

.no-routine, .no-classes, .no-orders {
    text-align: center;
    padding: 2rem;
    color: #666;
}

.no-routine i, .no-classes i, .no-orders i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #ddd;
}

.classes-list, .orders-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.class-item, .order-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #f0f0f0;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.class-item:hover, .order-item:hover {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

.class-time {
    text-align: center;
    min-width: 80px;
}

.class-time .time {
    display: block;
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
}

.class-time .date {
    display: block;
    font-size: 0.8rem;
    color: #666;
}

.class-info, .order-info {
    flex: 1;
}

.class-info h4, .order-info h4 {
    margin: 0 0 0.25rem 0;
    color: #333;
}

.class-info p, .order-info p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.order-info small {
    color: #999;
    font-size: 0.8rem;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.quick-tools {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
}

.tool-card {
    display: block;
    padding: 1.5rem;
    border: 2px dashed #ddd;
    border-radius: 10px;
    text-decoration: none;
    color: #666;
    text-align: center;
    transition: all 0.3s ease;
}

.tool-card:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
    text-decoration: none;
}

.tool-card i {
    font-size: 2rem;
    margin-bottom: 1rem;
    display: block;
}

.tool-card h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
}

.tool-card p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.8;
}

.btn-outline {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.btn-outline:hover {
    background: rgba(255, 255, 255, 0.1);
    text-decoration: none;
    color: white;
}

@media (max-width: 768px) {
    .main-grid, .secondary-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .tools-grid {
        grid-template-columns: 1fr;
    }
    
    .routine-stats {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<script>
// Inicializar gráfico de progreso si Chart.js está disponible
if (typeof Chart !== 'undefined') {
    const ctx = document.getElementById('progress-chart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Entrenamientos',
                    data: [1, 1, 0, 1, 1, 1, 0],
                    borderColor: '#FF6B00',
                    backgroundColor: 'rgba(255, 107, 0, 0.1)',
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
                        beginAtZero: true,
                        max: 2,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
}
</script>