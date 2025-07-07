<?php
/**
 * Vista del Dashboard de Instructor - STYLOFITNESS
 * Muestra información específica para instructores
 */

use StyleFitness\Helpers\AppHelper;

$pageTitle = 'Dashboard Instructor';
$user = AppHelper::getCurrentUser();
?>

<div class="instructor-dashboard">
    <div class="dashboard-header">
        <div class="container">
            <div class="welcome-section">
                <h1>Hola, <?php echo htmlspecialchars($user['first_name']); ?></h1>
                <p class="subtitle">Panel de Instructor</p>
            </div>
            <div class="quick-actions">
                <a href="/routines/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Rutina
                </a>
                <a href="/classes" class="btn btn-secondary">
                    <i class="fas fa-calendar"></i> Mis Clases
                </a>
                <a href="/trainer/progress" class="btn btn-accent">
                    <i class="fas fa-chart-line"></i> Progreso
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="container">
            <!-- Estadísticas del instructor -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($dashboardData['assigned_clients']); ?></h3>
                        <p>Clientes Asignados</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($dashboardData['active_routines']); ?></h3>
                        <p>Rutinas Activas</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($dashboardData['scheduled_classes']); ?></h3>
                        <p>Clases Programadas</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($dashboardData['this_week_sessions']); ?></h3>
                        <p>Sesiones Esta Semana</p>
                    </div>
                </div>
            </div>

            <!-- Secciones principales -->
            <div class="main-grid">
                <div class="main-card">
                    <div class="card-header">
                        <h3><i class="fas fa-users"></i> Mis Clientes</h3>
                        <a href="/trainer/clients" class="btn btn-sm btn-outline">Ver Todos</a>
                    </div>
                    <div class="card-content">
                        <div class="clients-list">
                            <div class="client-item">
                                <div class="client-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="client-info">
                                    <h4>María González</h4>
                                    <p>Rutina: Pérdida de Peso</p>
                                    <small>Último entrenamiento: Ayer</small>
                                </div>
                                <div class="client-actions">
                                    <button class="btn btn-sm btn-primary">Ver Progreso</button>
                                </div>
                            </div>
                            <div class="client-item">
                                <div class="client-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="client-info">
                                    <h4>Carlos Rodríguez</h4>
                                    <p>Rutina: Ganancia Muscular</p>
                                    <small>Último entrenamiento: Hoy</small>
                                </div>
                                <div class="client-actions">
                                    <button class="btn btn-sm btn-primary">Ver Progreso</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main-card">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar"></i> Próximas Clases</h3>
                        <a href="/classes/schedule" class="btn btn-sm btn-outline">Ver Horario</a>
                    </div>
                    <div class="card-content">
                        <div class="classes-list">
                            <div class="class-item">
                                <div class="class-time">
                                    <span class="time">09:00</span>
                                    <span class="date">Hoy</span>
                                </div>
                                <div class="class-info">
                                    <h4>Yoga Matutino</h4>
                                    <p>Sala 1 • 12 participantes</p>
                                </div>
                                <div class="class-status">
                                    <span class="badge badge-success">Confirmada</span>
                                </div>
                            </div>
                            <div class="class-item">
                                <div class="class-time">
                                    <span class="time">18:00</span>
                                    <span class="date">Hoy</span>
                                </div>
                                <div class="class-info">
                                    <h4>HIIT Avanzado</h4>
                                    <p>Sala 2 • 8 participantes</p>
                                </div>
                                <div class="class-status">
                                    <span class="badge badge-warning">Pendiente</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Herramientas rápidas -->
            <div class="tools-section">
                <div class="card-header">
                    <h3><i class="fas fa-tools"></i> Herramientas Rápidas</h3>
                </div>
                <div class="tools-grid">
                    <a href="/routines/create" class="tool-card">
                        <i class="fas fa-plus-circle"></i>
                        <h4>Crear Rutina</h4>
                        <p>Diseña una nueva rutina personalizada</p>
                    </a>
                    <a href="/exercises" class="tool-card">
                        <i class="fas fa-dumbbell"></i>
                        <h4>Banco de Ejercicios</h4>
                        <p>Explora ejercicios disponibles</p>
                    </a>
                    <a href="/trainer/reports" class="tool-card">
                        <i class="fas fa-chart-bar"></i>
                        <h4>Reportes</h4>
                        <p>Analiza el progreso de tus clientes</p>
                    </a>
                    <a href="/classes/create" class="tool-card">
                        <i class="fas fa-calendar-plus"></i>
                        <h4>Programar Clase</h4>
                        <p>Agenda una nueva clase grupal</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.instructor-dashboard {
    min-height: 100vh;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
    background: linear-gradient(45deg, #4facfe, #00f2fe);
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

.main-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: linear-gradient(45deg, #4facfe, #00f2fe);
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

.clients-list, .classes-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.client-item, .class-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #f0f0f0;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.client-item:hover, .class-item:hover {
    border-color: #4facfe;
    background: rgba(79, 172, 254, 0.05);
}

.client-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(45deg, #4facfe, #00f2fe);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.client-info h4 {
    margin: 0 0 0.25rem 0;
    color: #333;
}

.client-info p {
    margin: 0 0 0.25rem 0;
    color: #666;
    font-size: 0.9rem;
}

.client-info small {
    color: #999;
    font-size: 0.8rem;
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

.class-info {
    flex: 1;
}

.class-info h4 {
    margin: 0 0 0.25rem 0;
    color: #333;
}

.class-info p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
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

.tools-section {
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
    border-color: #4facfe;
    color: #4facfe;
    background: rgba(79, 172, 254, 0.05);
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
    .main-grid {
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
}
</style>