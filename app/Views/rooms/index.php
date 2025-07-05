<?php
$title = 'Gestión de Salas';
$pageTitle = 'Salas';
include '../app/Views/layouts/header.php';
?>

<div class="container-fluid">
    <!-- Header con filtros -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-door-open"></i> Gestión de Salas
            </h1>
            <p class="text-muted">Administra las salas y sus configuraciones</p>
        </div>
        <div class="col-md-4 text-right">
            <?php if ($user['role'] === 'admin'): ?>
                <a href="/rooms/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Sala
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/rooms" class="row">
                <div class="col-md-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= htmlspecialchars($filters['search']) ?>" 
                           placeholder="Nombre de sala...">
                </div>
                
                <?php if ($user['role'] === 'admin'): ?>
                <div class="col-md-3">
                    <label for="gym_id" class="form-label">Gimnasio</label>
                    <select class="form-control" id="gym_id" name="gym_id">
                        <option value="">Todos los gimnasios</option>
                        <?php foreach ($gyms as $gym): ?>
                            <option value="<?= $gym['id'] ?>" 
                                    <?= $filters['gym_id'] == $gym['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($gym['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="col-md-3">
                    <label for="room_type" class="form-label">Tipo de Sala</label>
                    <select class="form-control" id="room_type" name="room_type">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($roomTypes as $type => $label): ?>
                            <option value="<?= $type ?>" 
                                    <?= $filters['room_type'] === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="/rooms" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de salas -->
    <div class="card">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                Salas Registradas 
                <span class="badge badge-secondary"><?= $pagination['total_items'] ?></span>
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($rooms)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay salas registradas</h5>
                    <p class="text-muted">Comienza creando tu primera sala</p>
                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="/rooms/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primera Sala
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sala</th>
                                <th>Gimnasio</th>
                                <th>Tipo</th>
                                <th>Capacidad</th>
                                <th>Estado</th>
                                <th>Estadísticas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="room-icon me-3">
                                                <?php if ($room['room_type'] === 'positioned'): ?>
                                                    <i class="fas fa-th text-primary"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-users text-success"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">
                                                    <a href="/rooms/<?= $room['id'] ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($room['name']) ?>
                                                    </a>
                                                </h6>
                                                <?php if ($room['description']): ?>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars(substr($room['description'], 0, 50)) ?>
                                                        <?= strlen($room['description']) > 50 ? '...' : '' ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= htmlspecialchars($room['gym_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($room['room_type'] === 'positioned'): ?>
                                            <span class="badge badge-primary">
                                                <i class="fas fa-th"></i> Con Posiciones
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-success">
                                                <i class="fas fa-users"></i> Solo Aforo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= $room['total_capacity'] ?></strong>
                                        <?php if ($room['room_type'] === 'positioned' && isset($room['positions_count'])): ?>
                                            <br><small class="text-muted">
                                                <?= $room['positions_count'] ?> posiciones
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($room['is_active']): ?>
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Activa
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-pause"></i> Inactiva
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($room['stats'])): ?>
                                            <small class="text-muted">
                                                <?= $room['stats']['total_classes'] ?? 0 ?> clases<br>
                                                <?= $room['stats']['total_bookings'] ?? 0 ?> reservas
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/rooms/<?= $room['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($room['room_type'] === 'positioned' && $user['role'] === 'admin'): ?>
                                                <a href="/rooms/<?= $room['id'] ?>/positions" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Gestionar posiciones">
                                                    <i class="fas fa-th"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <a href="/rooms/<?= $room['id'] ?>/edit" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete(<?= $room['id'] ?>, '<?= htmlspecialchars($room['name']) ?>')" 
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <nav aria-label="Paginación de salas">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&<?= http_build_query(array_filter($filters, fn($v) => $v !== '')) ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters, fn($v) => $v !== '')) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&<?= http_build_query(array_filter($filters, fn($v) => $v !== '')) ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar la sala <strong id="roomNameToDelete"></strong>?</p>
                <p class="text-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta acción no se puede deshacer y puede afectar las clases programadas.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar Sala
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(roomId, roomName) {
    document.getElementById('roomNameToDelete').textContent = roomName;
    document.getElementById('deleteForm').action = '/rooms/' + roomId + '/delete';
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Auto-submit del formulario de filtros cuando cambian los selects
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('#gym_id, #room_type');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>

<?php include '../app/Views/layouts/footer.php'; ?>