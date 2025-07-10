<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista de Administración de Pedidos - STYLOFITNESS
 * Gestión de pedidos desde el panel de administración
 */

$currentUser = AppHelper::getCurrentUser();
?>

<div class="admin-layout full-width">

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-content">
                <h1><i class="fas fa-shopping-cart"></i> Gestión de Pedidos</h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="exportOrders()">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-content">
            <!-- Filtros -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Buscar por número, cliente..." 
                               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="form-control">
                    </div>
                    <div class="filter-group">
                        <select name="status" class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="pending" <?php echo ($_GET['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="processing" <?php echo ($_GET['status'] ?? '') === 'processing' ? 'selected' : ''; ?>>Procesando</option>
                            <option value="shipped" <?php echo ($_GET['status'] ?? '') === 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                            <option value="delivered" <?php echo ($_GET['status'] ?? '') === 'delivered' ? 'selected' : ''; ?>>Entregado</option>
                            <option value="cancelled" <?php echo ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <select name="payment_status" class="form-control">
                            <option value="">Estado de pago</option>
                            <option value="pending" <?php echo ($_GET['payment_status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="paid" <?php echo ($_GET['payment_status'] ?? '') === 'paid' ? 'selected' : ''; ?>>Pagado</option>
                            <option value="failed" <?php echo ($_GET['payment_status'] ?? '') === 'failed' ? 'selected' : ''; ?>>Fallido</option>
                            <option value="refunded" <?php echo ($_GET['payment_status'] ?? '') === 'refunded' ? 'selected' : ''; ?>>Reembolsado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <input type="date" name="date_from" value="<?php echo $_GET['date_from'] ?? ''; ?>" class="form-control">
                    </div>
                    <div class="filter-group">
                        <input type="date" name="date_to" value="<?php echo $_GET['date_to'] ?? ''; ?>" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="<?php echo AppHelper::baseUrl('admin/orders'); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </form>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo count($orders ?? []); ?></h3>
                        <p>Total Pedidos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo count(array_filter($orders ?? [], function($o) { return $o['status'] === 'pending'; })); ?></h3>
                        <p>Pendientes</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo count(array_filter($orders ?? [], function($o) { return $o['status'] === 'delivered'; })); ?></h3>
                        <p>Entregados</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>S/. <?php echo number_format(array_sum(array_map(function($o) { return $o['total_amount']; }, $orders ?? [])), 2); ?></h3>
                        <p>Total Ventas</p>
                    </div>
                </div>
            </div>

            <!-- Tabla de pedidos -->
            <div class="table-section">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Pago</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?php echo htmlspecialchars($order['order_number']); ?></strong>
                                        </td>
                                        <td>
                                            <div class="customer-info">
                                                <strong><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></strong>
                                                <small><?php echo htmlspecialchars($order['email']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?php echo $order['item_count']; ?> items</span>
                                        </td>
                                        <td>
                                            <strong>S/. <?php echo number_format($order['total_amount'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="payment-badge payment-<?php echo $order['payment_status']; ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action view" onclick="viewOrder(<?php echo $order['id']; ?>)" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-action edit" onclick="editOrder(<?php echo $order['id']; ?>)" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-action print" onclick="printOrder(<?php echo $order['id']; ?>)" title="Imprimir">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-shopping-cart text-muted mb-3" style="font-size: 3rem;"></i>
                                        <p class="text-muted">No se encontraron pedidos</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
                    <div class="pagination-wrapper">
                        <nav class="pagination">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <a href="?page=<?php echo $pagination['current_page'] - 1; ?><?php echo http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) ? '&' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) : ''; ?>" class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <a href="?page=<?php echo $i; ?><?php echo http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) ? '&' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) : ''; ?>" 
                                   class="page-link <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <a href="?page=<?php echo $pagination['current_page'] + 1; ?><?php echo http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) ? '&' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) : ''; ?>" class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Modal para ver detalles del pedido -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalles del Pedido</h3>
            <button class="modal-close" onclick="closeOrderModal()">&times;</button>
        </div>
        <div class="modal-body" id="orderModalBody">
            <!-- Contenido cargado dinámicamente -->
        </div>
    </div>
</div>

<script>
function viewOrder(orderId) {
    // Implementar vista de detalles
    fetch(`/admin/orders/view/${orderId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('orderModalBody').innerHTML = html;
            document.getElementById('orderModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los detalles del pedido');
        });
}

function editOrder(orderId) {
    window.location.href = `/admin/orders/edit/${orderId}`;
}

function printOrder(orderId) {
    window.open(`/admin/orders/print/${orderId}`, '_blank');
}

function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
}

function exportOrders() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = '/admin/orders?' + params.toString();
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
.filters-section {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filters-form {
    display: flex;
    gap: 1rem;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 150px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.stat-content h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: bold;
    color: #333;
}

.stat-content p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.customer-info {
    display: flex;
    flex-direction: column;
}

.customer-info small {
    color: #666;
    font-size: 0.8rem;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #d1ecf1; color: #0c5460; }
.status-shipped { background: #d4edda; color: #155724; }
.status-delivered { background: #d1ecf1; color: #0c5460; }
.status-cancelled { background: #f8d7da; color: #721c24; }

.payment-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.payment-pending { background: #fff3cd; color: #856404; }
.payment-paid { background: #d4edda; color: #155724; }
.payment-failed { background: #f8d7da; color: #721c24; }
.payment-refunded { background: #e2e3e5; color: #383d41; }

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 8px;
    width: 80%;
    max-width: 800px;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #999;
}

.modal-close:hover {
    color: #333;
}

.modal-body {
    padding: 1.5rem;
}

@media (max-width: 768px) {
    .filters-form {
        flex-direction: column;
    }
    
    .filter-group {
        min-width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        width: 95%;
        margin: 2% auto;
    }
}

/* Estilos mejorados para la página de pedidos */
.admin-layout.full-width {
    display: block;
    margin-left: 0;
    padding: 0;
    width: 100vw;
    position: relative;
    max-width: 100%;
    box-sizing: border-box;
}

.admin-layout.full-width .admin-main {
    margin-left: 0;
    width: 100%;
    max-width: 100%;
    padding: 2rem;
    box-sizing: border-box;
}

.admin-layout.full-width .admin-content {
    margin-left: 0;
    width: 100%;
    max-width: 100%;
}

.admin-layout.full-width .container {
    max-width: 100% !important;
    width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}

.admin-layout.full-width .main-content {
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}

.admin-header {
    background: linear-gradient(135deg, #FF6B00 0%, #FF8533 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(255, 107, 0, 0.2);
}

.admin-header h1 {
    font-size: 2rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-actions .btn-primary {
    background: white;
    color: #FF6B00;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.header-actions .btn-primary:hover {
    background: rgba(255, 255, 255, 0.9);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.filters-section {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.filters-form {
    display: flex;
    gap: 1rem;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 150px;
}

.filter-group input, .filter-group select {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    width: 100%;
    transition: all 0.3s ease;
}

.filter-group input:focus, .filter-group select:focus {
    border-color: #FF6B00;
    box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
    outline: none;
}

.filters-form .btn-primary {
    background: linear-gradient(135deg, #FF6B00 0%, #FF8533 100%);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    color: white;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.filters-form .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 107, 0, 0.2);
}

.filters-form .btn-secondary {
    background: #f5f5f5;
    border: 1px solid #e0e0e0;
    color: #666;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.filters-form .btn-secondary:hover {
    background: #eeeeee;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #FF6B00 0%, #FF8533 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 4px 10px rgba(255, 107, 0, 0.2);
}

.stat-content h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: bold;
    color: #333;
}

.stat-content p {
    margin: 0;
    color: #666;
    font-size: 1rem;
}

.admin-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.admin-table thead th {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    font-weight: 600;
    color: #333;
    text-align: left;
    border-bottom: 2px solid #e0e0e0;
}

.admin-table tbody td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e0e0e0;
    vertical-align: middle;
}

.admin-table tbody tr:last-child td {
    border-bottom: none;
}

.admin-table tbody tr:hover {
    background: #f8f9fa;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-action.view {
    background: #17a2b8;
}

.btn-action.edit {
    background: #ffc107;
}

.btn-action.print {
    background: #6c757d;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

.pagination {
    display: flex;
    gap: 0.5rem;
}

.page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: white;
    color: #333;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.page-link.active {
    background: linear-gradient(135deg, #FF6B00 0%, #FF8533 100%);
    color: white;
}

.page-link:hover:not(.active) {
    background: #f5f5f5;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>