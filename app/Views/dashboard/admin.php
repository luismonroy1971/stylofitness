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

            <!-- Gestión de Usuarios -->
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-users"></i> Gestión de Usuarios</h2>
                    <a href="/admin/users/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </a>
                </div>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dashboardData['recent_users'])): ?>
                                <?php foreach ($dashboardData['recent_users'] as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><span class="badge badge-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span></td>
                                        <td><span class="status-badge <?= $user['is_active'] ? 'active' : 'inactive' ?>"><?= $user['is_active'] ? 'Activo' : 'Inactivo' ?></span></td>
                                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                        <td>
                                            <a href="/admin/users/edit/<?= $user['id'] ?>" class="btn-action edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/admin/users/delete/<?= $user['id'] ?>" class="btn-action delete" onclick="return confirm('¿Eliminar usuario?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">No hay usuarios recientes</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <a href="/admin/users" class="view-all-link">Ver todos los usuarios <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Gestión de Productos -->
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-box"></i> Gestión de Productos</h2>
                    <a href="/admin/products/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Producto
                    </a>
                </div>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dashboardData['recent_products'])): ?>
                                <?php foreach ($dashboardData['recent_products'] as $product): ?>
                                    <tr>
                                        <td><?= $product['id'] ?></td>
                                        <td>
                                            <img src="<?= $product['image_url'] ?: '/images/placeholder.jpg' ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-thumb">
                                        </td>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= htmlspecialchars($product['category_name'] ?? 'Sin categoría') ?></td>
                                        <td>$<?= number_format($product['price'], 2) ?></td>
                                        <td><span class="stock-badge <?= $product['stock'] > 10 ? 'high' : ($product['stock'] > 0 ? 'low' : 'out') ?>"><?= $product['stock'] ?></span></td>
                                        <td><span class="status-badge <?= $product['is_active'] ? 'active' : 'inactive' ?>"><?= $product['is_active'] ? 'Activo' : 'Inactivo' ?></span></td>
                                        <td>
                                            <a href="/admin/products/edit/<?= $product['id'] ?>" class="btn-action edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/admin/products/delete/<?= $product['id'] ?>" class="btn-action delete" onclick="return confirm('¿Eliminar producto?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center">No hay productos recientes</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <a href="/admin/products" class="view-all-link">Ver todos los productos <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Gestión de Pedidos -->
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-shopping-cart"></i> Gestión de Pedidos</h2>
                    <a href="/admin/orders" class="btn btn-primary">
                        <i class="fas fa-list"></i> Ver Todos
                    </a>
                </div>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dashboardData['recent_orders'])): ?>
                                <?php foreach ($dashboardData['recent_orders'] as $order): ?>
                                    <tr>
                                        <td>#<?= $order['id'] ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td>$<?= number_format($order['total'], 2) ?></td>
                                        <td><span class="order-status <?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                        <td>
                                            <a href="/admin/orders/view/<?= $order['id'] ?>" class="btn-action view">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/admin/orders/edit/<?= $order['id'] ?>" class="btn-action edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">No hay pedidos recientes</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <a href="/admin/orders" class="view-all-link">Ver todos los pedidos <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Gestión de Clases Grupales -->
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-dumbbell"></i> Gestión de Clases</h2>
                    <a href="/admin/classes/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Clase
                    </a>
                </div>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Instructor</th>
                                <th>Capacidad</th>
                                <th>Inscritos</th>
                                <th>Horario</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dashboardData['recent_classes'])): ?>
                                <?php foreach ($dashboardData['recent_classes'] as $class): ?>
                                    <tr>
                                        <td><?= $class['id'] ?></td>
                                        <td><?= htmlspecialchars($class['name']) ?></td>
                                        <td><?= htmlspecialchars($class['instructor_name']) ?></td>
                                        <td><?= $class['max_participants'] ?></td>
                                        <td><span class="capacity-badge <?= $class['enrolled'] >= $class['max_participants'] ? 'full' : 'available' ?>"><?= $class['enrolled'] ?></span></td>
                                        <td><?= date('d/m H:i', strtotime($class['schedule'])) ?></td>
                                        <td><span class="status-badge <?= $class['is_active'] ? 'active' : 'inactive' ?>"><?= $class['is_active'] ? 'Activa' : 'Inactiva' ?></span></td>
                                        <td>
                                            <a href="/admin/classes/edit/<?= $class['id'] ?>" class="btn-action edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/admin/classes/delete/<?= $class['id'] ?>" class="btn-action delete" onclick="return confirm('¿Eliminar clase?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center">No hay clases programadas</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <a href="/admin/classes" class="view-all-link">Ver todas las clases <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Gestión de Rutinas -->
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-list-alt"></i> Gestión de Rutinas</h2>
                    <a href="/admin/routines/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Rutina
                    </a>
                </div>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Ejercicios</th>
                                <th>Duración</th>
                                <th>Nivel</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dashboardData['recent_routines'])): ?>
                                <?php foreach ($dashboardData['recent_routines'] as $routine): ?>
                                    <tr>
                                        <td><?= $routine['id'] ?></td>
                                        <td><?= htmlspecialchars($routine['name']) ?></td>
                                        <td><?= htmlspecialchars($routine['category']) ?></td>
                                        <td><?= $routine['exercise_count'] ?></td>
                                        <td><?= $routine['duration'] ?> min</td>
                                        <td><span class="level-badge <?= strtolower($routine['difficulty_level']) ?>"><?= $routine['difficulty_level'] ?></span></td>
                                        <td><span class="status-badge <?= $routine['is_active'] ? 'active' : 'inactive' ?>"><?= $routine['is_active'] ? 'Activa' : 'Inactiva' ?></span></td>
                                        <td>
                                            <a href="/admin/routines/edit/<?= $routine['id'] ?>" class="btn-action edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/admin/routines/delete/<?= $routine['id'] ?>" class="btn-action delete" onclick="return confirm('¿Eliminar rutina?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center">No hay rutinas creadas</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <a href="/admin/routines" class="view-all-link">Ver todas las rutinas <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Gestión de Instructores -->
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-user-tie"></i> Gestión de Instructores</h2>
                    <a href="/admin/instructors/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Instructor
                    </a>
                </div>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Especialidad</th>
                                <th>Clases Activas</th>
                                <th>Calificación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dashboardData['instructors'])): ?>
                                <?php foreach ($dashboardData['instructors'] as $instructor): ?>
                                    <tr>
                                        <td><?= $instructor['id'] ?></td>
                                        <td>
                                            <img src="<?= $instructor['photo_url'] ?: '/images/placeholder.jpg' ?>" alt="<?= htmlspecialchars($instructor['name']) ?>" class="instructor-thumb">
                                        </td>
                                        <td><?= htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']) ?></td>
                                        <td><?= htmlspecialchars($instructor['specialization']) ?></td>
                                        <td><?= $instructor['active_classes'] ?></td>
                                        <td>
                                            <div class="rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?= $i <= $instructor['rating'] ? 'active' : '' ?>"></i>
                                                <?php endfor; ?>
                                                <span>(<?= number_format($instructor['rating'], 1) ?>)</span>
                                            </div>
                                        </td>
                                        <td><span class="status-badge <?= $instructor['is_active'] ? 'active' : 'inactive' ?>"><?= $instructor['is_active'] ? 'Activo' : 'Inactivo' ?></span></td>
                                        <td>
                                            <a href="/admin/instructors/edit/<?= $instructor['id'] ?>" class="btn-action edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/admin/instructors/delete/<?= $instructor['id'] ?>" class="btn-action delete" onclick="return confirm('¿Eliminar instructor?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center">No hay instructores registrados</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <a href="/admin/instructors" class="view-all-link">Ver todos los instructores <i class="fas fa-arrow-right"></i></a>
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

/* Secciones administrativas */
.admin-section {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.section-header h2 {
    color: white;
    font-size: 20px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header h2 i {
    color: #ff6b35;
}

.btn {
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #e55a2b, #e8851a);
    transform: translateY(-1px);
    color: white;
    text-decoration: none;
}

/* Tablas administrativas */
.admin-table-container {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.admin-table th {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.admin-table td {
    padding: 12px 16px;
    color: rgba(255, 255, 255, 0.9);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    vertical-align: middle;
}

.admin-table tr:hover {
    background: rgba(255, 255, 255, 0.05);
}

.admin-table tr:last-child td {
    border-bottom: none;
}

/* Badges y estados */
.badge, .status-badge, .stock-badge, .order-status, .level-badge, .capacity-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-admin { background: #dc3545; color: white; }
.badge-instructor { background: #28a745; color: white; }
.badge-member { background: #007bff; color: white; }

.status-badge.active, .capacity-badge.available { background: #28a745; color: white; }
.status-badge.inactive, .capacity-badge.full { background: #dc3545; color: white; }

.stock-badge.high { background: #28a745; color: white; }
.stock-badge.low { background: #ffc107; color: #212529; }
.stock-badge.out { background: #dc3545; color: white; }

.order-status.pending { background: #ffc107; color: #212529; }
.order-status.processing { background: #17a2b8; color: white; }
.order-status.completed { background: #28a745; color: white; }
.order-status.cancelled { background: #dc3545; color: white; }

.level-badge.beginner { background: #28a745; color: white; }
.level-badge.intermediate { background: #ffc107; color: #212529; }
.level-badge.advanced { background: #dc3545; color: white; }

/* Imágenes en tablas */
.product-thumb, .instructor-thumb {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.1);
}

/* Botones de acción */
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    text-decoration: none;
    margin: 0 2px;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-action.view {
    background: rgba(23, 162, 184, 0.2);
    color: #17a2b8;
}

.btn-action.edit {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
}

.btn-action.delete {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.btn-action.view:hover { background: rgba(23, 162, 184, 0.3); color: #17a2b8; }
.btn-action.edit:hover { background: rgba(255, 193, 7, 0.3); color: #ffc107; }
.btn-action.delete:hover { background: rgba(220, 53, 69, 0.3); color: #dc3545; }

/* Rating */
.rating {
    display: flex;
    align-items: center;
    gap: 2px;
}

.rating .fas.fa-star {
    color: rgba(255, 255, 255, 0.3);
    font-size: 12px;
}

.rating .fas.fa-star.active {
    color: #ffc107;
}

.rating span {
    margin-left: 6px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
}

/* Footer de tabla */
.table-footer {
    padding: 16px;
    background: rgba(255, 255, 255, 0.05);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
}

.view-all-link {
    color: #ff6b35;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.view-all-link:hover {
    color: #f7931e;
    text-decoration: none;
    transform: translateX(2px);
}

.text-center {
    text-align: center;
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
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

    .admin-section {
        padding: 16px;
        margin-bottom: 16px;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .section-header h2 {
        font-size: 18px;
    }

    .admin-table-container {
        overflow-x: auto;
    }

    .admin-table {
        min-width: 600px;
        font-size: 12px;
    }

    .admin-table th,
    .admin-table td {
        padding: 8px 12px;
    }

    .product-thumb, .instructor-thumb {
        width: 32px;
        height: 32px;
    }

    .btn-action {
        width: 28px;
        height: 28px;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .admin-section {
        padding: 12px;
    }

    .section-header h2 {
        font-size: 16px;
    }

    .admin-table {
        font-size: 11px;
    }

    .admin-table th,
    .admin-table td {
        padding: 6px 8px;
    }

    .btn {
        padding: 6px 12px;
        font-size: 12px;
    }

    .badge, .status-badge, .stock-badge, .order-status, .level-badge, .capacity-badge {
        padding: 2px 6px;
        font-size: 10px;
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