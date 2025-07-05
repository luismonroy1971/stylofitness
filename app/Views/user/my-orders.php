<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Mis Pedidos - STYLOFITNESS
 * Muestra el historial de pedidos del usuario
 */

$currentUser = AppHelper::getCurrentUser();
?>

<div class="my-orders-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title">
                        <i class="fas fa-shopping-bag me-3"></i>
                        Mis Pedidos
                    </h1>
                    <p class="page-subtitle">Historial y estado de tus compras</p>
                </div>
                <div class="col-auto">
                    <a href="/store" class="btn btn-outline-primary">
                        <i class="fas fa-store me-2"></i>
                        Ir a la Tienda
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="search-box">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchOrders" 
                                   placeholder="Buscar por número de pedido o producto...">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-buttons">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="statusFilter" id="all" value="all" checked>
                            <label class="btn btn-outline-primary" for="all">Todos</label>
                            
                            <input type="radio" class="btn-check" name="statusFilter" id="pending" value="pending">
                            <label class="btn btn-outline-primary" for="pending">Pendientes</label>
                            
                            <input type="radio" class="btn-check" name="statusFilter" id="completed" value="completed">
                            <label class="btn btn-outline-primary" for="completed">Completados</label>
                            
                            <input type="radio" class="btn-check" name="statusFilter" id="cancelled" value="cancelled">
                            <label class="btn btn-outline-primary" for="cancelled">Cancelados</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de pedidos -->
        <div class="orders-list">
            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag text-muted mb-4" style="font-size: 4rem;"></i>
                        <h4 class="text-muted">No tienes pedidos aún</h4>
                        <p class="text-muted">Explora nuestra tienda y encuentra productos increíbles para tu entrenamiento.</p>
                        <a href="/store" class="btn btn-primary mt-3">
                            <i class="fas fa-store me-2"></i>
                            Ir a la Tienda
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card" data-status="<?= strtolower($order['status']) ?>">
                        <div class="order-header">
                            <div class="order-info">
                                <h5 class="order-number">
                                    Pedido #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
                                </h5>
                                <div class="order-date">
                                    <i class="fas fa-calendar me-2"></i>
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                </div>
                            </div>
                            <div class="order-status">
                                <span class="badge bg-<?= getStatusColor($order['status']) ?>">
                                    <?= getStatusText($order['status']) ?>
                                </span>
                            </div>
                            <div class="order-total">
                                <div class="total-label">Total</div>
                                <div class="total-amount">$<?= number_format($order['total'], 2) ?></div>
                            </div>
                        </div>

                        <div class="order-content">
                            <!-- Productos del pedido -->
                            <div class="order-items">
                                <?php if (!empty($order['items'])): ?>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div class="order-item">
                                            <div class="item-image">
                                                <?php if (!empty($item['product_image'])): ?>
                                                    <img src="<?= $item['product_image'] ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                                                <?php else: ?>
                                                    <div class="no-image">
                                                        <i class="fas fa-box"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="item-details">
                                                <h6 class="item-name"><?= htmlspecialchars($item['product_name']) ?></h6>
                                                <div class="item-meta">
                                                    <span class="item-quantity">Cantidad: <?= $item['quantity'] ?></span>
                                                    <span class="item-price">$<?= number_format($item['price'], 2) ?></span>
                                                </div>
                                                <?php if (!empty($item['product_description'])): ?>
                                                    <p class="item-description"><?= htmlspecialchars($item['product_description']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="item-total">
                                                $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Información de envío -->
                            <?php if (!empty($order['shipping_address'])): ?>
                            <div class="shipping-info">
                                <h6><i class="fas fa-truck me-2"></i>Información de Envío</h6>
                                <div class="shipping-details">
                                    <p class="mb-1"><?= htmlspecialchars($order['shipping_address']) ?></p>
                                    <?php if (!empty($order['tracking_number'])): ?>
                                        <p class="mb-0">
                                            <strong>Número de seguimiento:</strong> 
                                            <span class="tracking-number"><?= $order['tracking_number'] ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Información de pago -->
                            <div class="payment-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-credit-card me-2"></i>Método de Pago</h6>
                                        <p><?= htmlspecialchars($order['payment_method'] ?? 'No especificado') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-receipt me-2"></i>Resumen</h6>
                                        <div class="payment-summary">
                                            <div class="summary-line">
                                                <span>Subtotal:</span>
                                                <span>$<?= number_format($order['subtotal'] ?? $order['total'], 2) ?></span>
                                            </div>
                                            <?php if (!empty($order['shipping_cost'])): ?>
                                            <div class="summary-line">
                                                <span>Envío:</span>
                                                <span>$<?= number_format($order['shipping_cost'], 2) ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($order['tax'])): ?>
                                            <div class="summary-line">
                                                <span>Impuestos:</span>
                                                <span>$<?= number_format($order['tax'], 2) ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <div class="summary-line total">
                                                <span><strong>Total:</strong></span>
                                                <span><strong>$<?= number_format($order['total'], 2) ?></strong></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="order-actions">
                            <div class="row">
                                <div class="col">
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <button class="btn btn-outline-danger btn-sm" onclick="cancelOrder(<?= $order['id'] ?>)">
                                            <i class="fas fa-times me-2"></i>
                                            Cancelar Pedido
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($order['tracking_number'])): ?>
                                        <button class="btn btn-outline-info btn-sm" onclick="trackOrder('<?= $order['tracking_number'] ?>')">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            Rastrear Envío
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-outline-primary btn-sm" onclick="downloadInvoice(<?= $order['id'] ?>)">
                                        <i class="fas fa-download me-2"></i>
                                        Descargar Factura
                                    </button>
                                    
                                    <?php if ($order['status'] === 'completed'): ?>
                                        <button class="btn btn-primary btn-sm" onclick="reorderItems(<?= $order['id'] ?>)">
                                            <i class="fas fa-redo me-2"></i>
                                            Volver a Comprar
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Funciones auxiliares para el estado
function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'pending': return 'warning';
        case 'processing': return 'info';
        case 'shipped': return 'primary';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function getStatusText($status) {
    switch (strtolower($status)) {
        case 'pending': return 'Pendiente';
        case 'processing': return 'Procesando';
        case 'shipped': return 'Enviado';
        case 'completed': return 'Completado';
        case 'cancelled': return 'Cancelado';
        default: return 'Desconocido';
    }
}
?>

<style>
.my-orders-container {
    padding: 2rem 0;
    min-height: calc(100vh - 200px);
}

.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.page-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #6c757d;
    margin-bottom: 0;
}

.filters-section {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.search-box .input-group-text {
    background: #f8f9fa;
    border-right: none;
}

.search-box .form-control {
    border-left: none;
    background: #f8f9fa;
}

.search-box .form-control:focus {
    background: white;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.filter-buttons {
    display: flex;
    justify-content: flex-end;
}

.btn-check:checked + .btn-outline-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
}

.order-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.order-number {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.order-date {
    color: #6c757d;
    font-size: 0.9rem;
}

.order-date i {
    color: #667eea;
}

.total-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.total-amount {
    font-size: 1.5rem;
    font-weight: bold;
    color: #667eea;
}

.order-content {
    padding: 1.5rem;
}

.order-items {
    margin-bottom: 2rem;
}

.order-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 1px solid #eee;
    border-radius: 8px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.order-item:hover {
    background: #f8f9fa;
    border-color: #667eea;
}

.item-image {
    width: 60px;
    height: 60px;
    margin-right: 1rem;
    flex-shrink: 0;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
}

.no-image {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.item-details {
    flex-grow: 1;
}

.item-name {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.item-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.item-quantity, .item-price {
    font-size: 0.9rem;
    color: #6c757d;
}

.item-description {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 0;
    line-height: 1.4;
}

.item-total {
    font-size: 1.1rem;
    font-weight: 600;
    color: #667eea;
    text-align: right;
}

.shipping-info, .payment-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.shipping-info h6, .payment-info h6 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.shipping-info h6 i, .payment-info h6 i {
    color: #667eea;
}

.tracking-number {
    font-family: monospace;
    background: #e9ecef;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
}

.payment-summary {
    border-top: 1px solid #dee2e6;
    padding-top: 1rem;
}

.summary-line {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.summary-line.total {
    border-top: 1px solid #dee2e6;
    padding-top: 0.5rem;
    margin-top: 0.5rem;
}

.order-actions {
    padding: 1.5rem;
    border-top: 1px solid #eee;
    background: #f8f9fa;
    border-radius: 0 0 12px 12px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 6px;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.no-orders {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin: 2rem 0;
}

@media (max-width: 768px) {
    .my-orders-container {
        padding: 1rem 0;
    }
    
    .filters-section {
        padding: 1rem;
    }
    
    .filter-buttons {
        justify-content: center;
        margin-top: 1rem;
    }
    
    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .order-item {
        flex-direction: column;
        text-align: center;
    }
    
    .item-image {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .order-actions .row {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<script>
// Filtrado de pedidos
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchOrders');
    const statusFilters = document.querySelectorAll('input[name="statusFilter"]');
    const orderCards = document.querySelectorAll('.order-card');

    function filterOrders() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = document.querySelector('input[name="statusFilter"]:checked').value;

        orderCards.forEach(card => {
            const orderNumber = card.querySelector('.order-number').textContent.toLowerCase();
            const itemNames = Array.from(card.querySelectorAll('.item-name'))
                .map(item => item.textContent.toLowerCase()).join(' ');
            const status = card.dataset.status;

            const matchesSearch = orderNumber.includes(searchTerm) || itemNames.includes(searchTerm);
            const matchesStatus = selectedStatus === 'all' || status === selectedStatus;

            if (matchesSearch && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterOrders);
    statusFilters.forEach(filter => {
        filter.addEventListener('change', filterOrders);
    });
});

// Funciones de acciones
function cancelOrder(orderId) {
    if (confirm('¿Estás seguro de que quieres cancelar este pedido?')) {
        // Implementar cancelación de pedido
        console.log('Cancelando pedido:', orderId);
        // Aquí se puede implementar la lógica de cancelación
    }
}

function trackOrder(trackingNumber) {
    // Implementar seguimiento de pedido
    console.log('Rastreando pedido:', trackingNumber);
    // Aquí se puede abrir una ventana con el seguimiento
    window.open(`https://tracking-service.com/track/${trackingNumber}`, '_blank');
}

function downloadInvoice(orderId) {
    // Implementar descarga de factura
    console.log('Descargando factura:', orderId);
    // Aquí se puede implementar la descarga de PDF
    window.location.href = `/orders/${orderId}/invoice`;
}

function reorderItems(orderId) {
    // Implementar recompra
    console.log('Recomprando pedido:', orderId);
    // Aquí se puede implementar la lógica de recompra
    if (confirm('¿Quieres agregar todos los productos de este pedido al carrito?')) {
        window.location.href = `/orders/${orderId}/reorder`;
    }
}
</script>