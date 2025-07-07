<?php
use StyleFitness\Helpers\AppHelper;

$user = AppHelper::getCurrentUser();
?>

<div class="staff-dashboard">
    <style>
        .staff-dashboard {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-header {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-text {
            color: #2c3e50;
            margin: 0;
            font-size: 2.2em;
            font-weight: 300;
        }
        
        .user-info {
            color: #7f8c8d;
            margin: 10px 0 0 0;
            font-size: 1.1em;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 1.1em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .dashboard-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            color: #2c3e50;
            margin: 0 0 20px 0;
            font-size: 1.4em;
            font-weight: 600;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
        }
        
        .action-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 15px 10px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 0.9em;
            font-weight: 500;
            transition: all 0.3s ease;
            display: block;
        }
        
        .action-btn:hover {
            background: linear-gradient(135deg, #2980b9, #1f5f8b);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .registration-item {
            padding: 15px;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .registration-item:last-child {
            border-bottom: none;
        }
        
        .member-info {
            flex: 1;
        }
        
        .member-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .member-email {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        
        .registration-date {
            color: #95a5a6;
            font-size: 0.85em;
        }
        
        .alert-badge {
            background: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }
        
        .no-data {
            text-align: center;
            color: #95a5a6;
            font-style: italic;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .welcome-text {
                font-size: 1.8em;
            }
        }
    </style>
    
    <!-- Header -->
    <div class="dashboard-header">
        <h1 class="welcome-text">Panel de Staff</h1>
        <p class="user-info">
            Bienvenido/a, <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong> | 
            Rol: <strong>Personal de Staff</strong> | 
            Último acceso: <strong><?= date('d/m/Y H:i') ?></strong>
        </p>
    </div>
    
    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= number_format($dashboardData['total_members']) ?></div>
            <div class="stat-label">Miembros Activos</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= number_format($dashboardData['pending_orders']) ?></div>
            <div class="stat-label">Órdenes Pendientes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= number_format($dashboardData['todays_classes']) ?></div>
            <div class="stat-label">Clases de Hoy</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">
                <?= number_format($dashboardData['inventory_alerts']) ?>
                <?php if ($dashboardData['inventory_alerts'] > 0): ?>
                    <span class="alert-badge">!</span>
                <?php endif; ?>
            </div>
            <div class="stat-label">Alertas de Inventario</div>
        </div>
    </div>
    
    <!-- Contenido Principal -->
    <div class="content-grid">
        <!-- Acciones Rápidas -->
        <div class="dashboard-section">
            <h2 class="section-title">Acciones Rápidas</h2>
            <div class="quick-actions">
                <a href="/members" class="action-btn">Gestionar Miembros</a>
                <a href="/orders" class="action-btn">Ver Órdenes</a>
                <a href="/inventory" class="action-btn">Inventario</a>
                <a href="/classes" class="action-btn">Programar Clases</a>
                <a href="/reports" class="action-btn">Reportes</a>
                <a href="/support" class="action-btn">Soporte</a>
            </div>
        </div>
        
        <!-- Registros Recientes -->
        <div class="dashboard-section">
            <h2 class="section-title">Registros Recientes (7 días)</h2>
            <?php if (!empty($dashboardData['recent_registrations'])): ?>
                <?php foreach ($dashboardData['recent_registrations'] as $registration): ?>
                    <div class="registration-item">
                        <div class="member-info">
                            <div class="member-name">
                                <?= htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']) ?>
                            </div>
                            <div class="member-email">
                                <?= htmlspecialchars($registration['email']) ?>
                            </div>
                        </div>
                        <div class="registration-date">
                            <?= date('d/m/Y', strtotime($registration['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    No hay registros recientes en los últimos 7 días
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>