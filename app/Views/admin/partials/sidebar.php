<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Sidebar del Panel de Administración - STYLOFITNESS
 * Barra lateral de navegación para el panel administrativo
 */

$currentUser = AppHelper::getCurrentUser();
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
?>

<div class="admin-sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <h2><i class="fas fa-dumbbell"></i> STYLOFITNESS</h2>
            <span class="admin-badge">Panel Admin</span>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="user-info">
            <h4><?= htmlspecialchars($currentUser['name'] ?? 'Administrador') ?></h4>
            <span class="user-role"><?= ucfirst($currentUser['role'] ?? 'admin') ?></span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li class="nav-item <?= strpos($currentPath, '/admin/dashboard') !== false ? 'active' : '' ?>">
                <a href="/admin/dashboard" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item <?= strpos($currentPath, '/admin/users') !== false ? 'active' : '' ?>">
                <a href="/admin/users" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
            </li>

            <li class="nav-item <?= strpos($currentPath, '/admin/instructors') !== false ? 'active' : '' ?>">
                <a href="/admin/instructors" class="nav-link">
                    <i class="fas fa-user-tie"></i>
                    <span>Instructores</span>
                </a>
            </li>

            <li class="nav-item <?= strpos($currentPath, '/admin/classes') !== false ? 'active' : '' ?>">
                <a href="/admin/classes" class="nav-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Clases</span>
                </a>
            </li>

            <li class="nav-item <?= strpos($currentPath, '/admin/routines') !== false ? 'active' : '' ?>">
                <a href="/admin/routines" class="nav-link">
                    <i class="fas fa-list-ol"></i>
                    <span>Rutinas</span>
                </a>
            </li>

            <li class="nav-item <?= strpos($currentPath, '/admin/exercises') !== false ? 'active' : '' ?>">
                <a href="/admin/exercises" class="nav-link">
                    <i class="fas fa-dumbbell"></i>
                    <span>Ejercicios</span>
                </a>
            </li>

            <li class="nav-item <?= strpos($currentPath, '/admin/products') !== false ? 'active' : '' ?>">
                <a href="/admin/products" class="nav-link">
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
            </li>

            <li class="nav-item <?= strpos($currentPath, '/admin/orders') !== false ? 'active' : '' ?>">
                <a href="/admin/orders" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pedidos</span>
                </a>
            </li>

            <li class="nav-item <?= strpos($currentPath, '/admin/reports') !== false ? 'active' : '' ?>">
                <a href="/admin/reports" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reportes</span>
                </a>
            </li>

            <li class="nav-item <?= strpos($currentPath, '/admin/settings') !== false ? 'active' : '' ?>">
                <a href="/admin/settings" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="/" class="nav-link" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Ver Sitio</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/logout" class="nav-link logout-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>

<style>
.admin-sidebar {
    width: 280px;
    height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    overflow-y: auto;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-align: center;
}

.sidebar-header .logo h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
}

.sidebar-header .admin-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    margin-top: 5px;
}

.sidebar-user {
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar i {
    font-size: 2.5rem;
    color: rgba(255,255,255,0.8);
}

.user-info h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.user-role {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.7);
}

.sidebar-nav {
    flex: 1;
    padding: 20px 0;
}

.nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: 5px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    border-left-color: rgba(255,255,255,0.5);
}

.nav-item.active .nav-link {
    background: rgba(255,255,255,0.15);
    color: white;
    border-left-color: white;
    font-weight: 600;
}

.nav-link i {
    width: 20px;
    margin-right: 12px;
    text-align: center;
}

.sidebar-footer {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 20px;
    margin-top: auto;
}

.logout-link:hover {
    background: rgba(220, 53, 69, 0.2) !important;
    border-left-color: #dc3545 !important;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
    
    .admin-content {
        margin-left: 0 !important;
    }
}

/* Ajuste para el contenido principal */
.admin-content {
    margin-left: 280px;
    min-height: 100vh;
    background: #f8f9fa;
}

.admin-layout {
    display: flex;
}

.admin-container {
    display: flex;
    min-height: 100vh;
}
</style>

<script>
// Confirmar logout
document.querySelector('.logout-link')?.addEventListener('click', function(e) {
    if (!confirm('¿Estás seguro de que quieres cerrar sesión?')) {
        e.preventDefault();
    }
});

// Colapsar sidebar en móviles
function toggleSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    sidebar.classList.toggle('collapsed');
}

// Auto-colapsar en pantallas pequeñas
if (window.innerWidth <= 768) {
    document.querySelector('.admin-sidebar')?.classList.add('collapsed');
}
</script>