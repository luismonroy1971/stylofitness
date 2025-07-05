<?php
$title = 'Gestión de Posiciones - ' . $room['name'];
$pageTitle = 'Gestión de Posiciones';
include '../app/Views/layouts/header.php';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-th"></i> Gestión de Posiciones
            </h1>
            <p class="text-muted">
                <i class="fas fa-door-open"></i> <?= htmlspecialchars($room['name']) ?> 
                • <i class="fas fa-building"></i> <?= htmlspecialchars($room['gym_name']) ?>
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group" role="group">
                <a href="/rooms/<?= $room['id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Sala
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                    <i class="fas fa-plus"></i> Nueva Posición
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Panel de posiciones -->
        <div class="col-md-8">
            <!-- Información de la sala -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Información de la Sala
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h4 class="text-primary"><?= $room['total_capacity'] ?></h4>
                            <small class="text-muted">Capacidad Total</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-success"><?= count($positions) ?></h4>
                            <small class="text-muted">Posiciones Creadas</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-info"><?= count(array_filter($positions, fn($p) => $p['is_available'])) ?></h4>
                            <small class="text-muted">Disponibles</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-warning"><?= $room['total_capacity'] - count($positions) ?></h4>
                            <small class="text-muted">Por Configurar</small>
                        </div>
                    </div>
                    
                    <?php if (count($positions) < $room['total_capacity']): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            Aún puedes agregar <?= $room['total_capacity'] - count($positions) ?> posiciones más.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mapa visual de posiciones -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map"></i> Mapa Visual de Posiciones
                    </h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary" id="gridViewBtn">
                            <i class="fas fa-th"></i> Vista Cuadrícula
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="listViewBtn">
                            <i class="fas fa-list"></i> Vista Lista
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($positions)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-th fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay posiciones configuradas</h5>
                            <p class="text-muted">Comienza agregando las primeras posiciones de la sala</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                                <i class="fas fa-plus"></i> Agregar Primera Posición
                            </button>
                        </div>
                    <?php else: ?>
                        <!-- Vista cuadrícula -->
                        <div id="gridView">
                            <div class="positions-grid">
                                <?php 
                                // Agrupar posiciones por fila
                                $positionsByRow = [];
                                foreach ($positions as $position) {
                                    $row = $position['row_number'] ?: 'Sin fila';
                                    $positionsByRow[$row][] = $position;
                                }
                                ksort($positionsByRow);
                                ?>
                                
                                <?php foreach ($positionsByRow as $row => $rowPositions): ?>
                                    <div class="position-row mb-3">
                                        <div class="row-header mb-2">
                                            <small class="text-muted font-weight-bold">Fila <?= htmlspecialchars($row) ?></small>
                                        </div>
                                        <div class="row-positions d-flex flex-wrap">
                                            <?php 
                                            // Ordenar por número de asiento
                                            usort($rowPositions, function($a, $b) {
                                                return ($a['seat_number'] ?: $a['position_number']) <=> ($b['seat_number'] ?: $b['position_number']);
                                            });
                                            ?>
                                            <?php foreach ($rowPositions as $position): ?>
                                                <div class="position-item m-1" 
                                                     data-position-id="<?= $position['id'] ?>"
                                                     onclick="editPosition(<?= $position['id'] ?>)">
                                                    <div class="position-seat <?= $position['is_available'] ? 'available' : 'unavailable' ?> <?= $position['position_type'] ?>" 
                                                         title="<?= htmlspecialchars($position['position_number']) ?> - <?= $position['is_available'] ? 'Disponible' : 'No disponible' ?>">
                                                        <span class="position-number"><?= htmlspecialchars($position['position_number']) ?></span>
                                                        <?php if ($position['position_type'] === 'premium'): ?>
                                                            <i class="fas fa-star position-icon"></i>
                                                        <?php elseif ($position['position_type'] === 'accessible'): ?>
                                                            <i class="fas fa-wheelchair position-icon"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Leyenda -->
                            <div class="positions-legend mt-4">
                                <h6 class="text-muted mb-2">Leyenda:</h6>
                                <div class="d-flex flex-wrap">
                                    <div class="legend-item me-3 mb-2">
                                        <div class="position-seat available small me-1">1</div>
                                        <small>Disponible</small>
                                    </div>
                                    <div class="legend-item me-3 mb-2">
                                        <div class="position-seat unavailable small me-1">2</div>
                                        <small>No disponible</small>
                                    </div>
                                    <div class="legend-item me-3 mb-2">
                                        <div class="position-seat available premium small me-1">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <small>Premium</small>
                                    </div>
                                    <div class="legend-item me-3 mb-2">
                                        <div class="position-seat available accessible small me-1">
                                            <i class="fas fa-wheelchair"></i>
                                        </div>
                                        <small>Accesible</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vista lista -->
                        <div id="listView" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Posición</th>
                                            <th>Fila</th>
                                            <th>Asiento</th>
                                            <th>Tipo</th>
                                            <th>Coordenadas</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($positions as $position): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($position['position_number']) ?></strong>
                                                </td>
                                                <td><?= htmlspecialchars($position['row_number'] ?: '-') ?></td>
                                                <td><?= htmlspecialchars($position['seat_number'] ?: '-') ?></td>
                                                <td>
                                                    <?php if ($position['position_type'] === 'premium'): ?>
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-star"></i> Premium
                                                        </span>
                                                    <?php elseif ($position['position_type'] === 'accessible'): ?>
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-wheelchair"></i> Accesible
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Regular</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        X: <?= $position['x_coordinate'] ?>, Y: <?= $position['y_coordinate'] ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php if ($position['is_available']): ?>
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> Disponible
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">
                                                            <i class="fas fa-times"></i> No disponible
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-primary" 
                                                                onclick="editPosition(<?= $position['id'] ?>)" 
                                                                title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="confirmDeletePosition(<?= $position['id'] ?>, '<?= htmlspecialchars($position['position_number']) ?>')" 
                                                                title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="col-md-4">
            <!-- Herramientas rápidas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools"></i> Herramientas Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-outline-success btn-block mb-2" 
                            onclick="generatePositions()">
                        <i class="fas fa-magic"></i> Generar Posiciones Automáticamente
                    </button>
                    <button type="button" class="btn btn-outline-info btn-block mb-2" 
                            onclick="toggleAllPositions(true)">
                        <i class="fas fa-check-circle"></i> Activar Todas
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-block mb-2" 
                            onclick="toggleAllPositions(false)">
                        <i class="fas fa-times-circle"></i> Desactivar Todas
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-block" 
                            onclick="confirmDeleteAllPositions()">
                        <i class="fas fa-trash"></i> Eliminar Todas
                    </button>
                </div>
            </div>

            <!-- Tipos de posición -->
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Tipos de Posición
                    </h6>
                </div>
                <div class="card-body">
                    <?php foreach ($positionTypes as $type => $label): ?>
                        <div class="mb-2">
                            <strong><?= htmlspecialchars($label) ?></strong>
                            <br>
                            <small class="text-muted">
                                <?php if ($type === 'regular'): ?>
                                    Posición estándar para la mayoría de usuarios
                                <?php elseif ($type === 'premium'): ?>
                                    Posición con mejor ubicación o características especiales
                                <?php elseif ($type === 'accessible'): ?>
                                    Posición adaptada para personas con discapacidad
                                <?php endif; ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar/editar posición -->
<div class="modal fade" id="addPositionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="positionModalTitle">
                    <i class="fas fa-plus"></i> Nueva Posición
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="positionForm" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="position_number" class="form-label required">Número de Posición</label>
                                <input type="text" class="form-control" id="position_number" name="position_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="position_type" class="form-label">Tipo</label>
                                <select class="form-control" id="position_type" name="position_type">
                                    <?php foreach ($positionTypes as $type => $label): ?>
                                        <option value="<?= $type ?>"><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="row_number" class="form-label">Fila</label>
                                <input type="text" class="form-control" id="row_number" name="row_number" placeholder="Ej: A, 1, Front">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="seat_number" class="form-label">Asiento</label>
                                <input type="text" class="form-control" id="seat_number" name="seat_number" placeholder="Ej: 1, 2, 3">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="x_coordinate" class="form-label">Coordenada X</label>
                                <input type="number" class="form-control" id="x_coordinate" name="x_coordinate" step="0.1" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="y_coordinate" class="form-label">Coordenada Y</label>
                                <input type="number" class="form-control" id="y_coordinate" name="y_coordinate" step="0.1" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_available" name="is_available" checked>
                            <label class="form-check-label" for="is_available">
                                Posición disponible
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes" class="form-label">Notas</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Notas adicionales sobre la posición..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="savePositionBtn">
                        <i class="fas fa-save"></i> Guardar Posición
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deletePositionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar la posición <strong id="positionToDelete"></strong>?</p>
                <p class="text-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deletePositionForm" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.positions-grid {
    background: #f8f9fc;
    border-radius: 0.375rem;
    padding: 1.5rem;
    min-height: 300px;
}

.position-row {
    margin-bottom: 1rem;
}

.row-header {
    text-align: center;
}

.row-positions {
    justify-content: center;
}

.position-item {
    cursor: pointer;
    transition: transform 0.2s;
}

.position-item:hover {
    transform: scale(1.1);
}

.position-seat {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.8rem;
    position: relative;
    border: 2px solid;
    transition: all 0.3s;
}

.position-seat.small {
    width: 30px;
    height: 30px;
    font-size: 0.7rem;
}

.position-seat.available {
    background-color: #28a745;
    border-color: #1e7e34;
    color: white;
}

.position-seat.unavailable {
    background-color: #6c757d;
    border-color: #545b62;
    color: white;
}

.position-seat.premium {
    background: linear-gradient(45deg, #ffc107, #ff8c00);
    border-color: #e0a800;
}

.position-seat.accessible {
    background-color: #17a2b8;
    border-color: #117a8b;
}

.position-number {
    font-size: 0.7rem;
    font-weight: bold;
}

.position-icon {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 0.6rem;
    background: white;
    border-radius: 50%;
    padding: 2px;
    color: #333;
}

.legend-item {
    display: flex;
    align-items: center;
}

.required::after {
    content: ' *';
    color: #dc3545;
}

.btn-block {
    width: 100%;
}
</style>

<script>
let currentEditingPosition = null;

// Cambiar entre vistas
document.getElementById('gridViewBtn').addEventListener('click', function() {
    document.getElementById('gridView').style.display = 'block';
    document.getElementById('listView').style.display = 'none';
    this.classList.add('active');
    document.getElementById('listViewBtn').classList.remove('active');
});

document.getElementById('listViewBtn').addEventListener('click', function() {
    document.getElementById('gridView').style.display = 'none';
    document.getElementById('listView').style.display = 'block';
    this.classList.add('active');
    document.getElementById('gridViewBtn').classList.remove('active');
});

// Editar posición
function editPosition(positionId) {
    // Buscar la posición en los datos
    const positions = <?= json_encode($positions) ?>;
    const position = positions.find(p => p.id == positionId);
    
    if (position) {
        currentEditingPosition = positionId;
        
        // Llenar el formulario
        document.getElementById('position_number').value = position.position_number;
        document.getElementById('position_type').value = position.position_type;
        document.getElementById('row_number').value = position.row_number || '';
        document.getElementById('seat_number').value = position.seat_number || '';
        document.getElementById('x_coordinate').value = position.x_coordinate;
        document.getElementById('y_coordinate').value = position.y_coordinate;
        document.getElementById('is_available').checked = position.is_available == 1;
        document.getElementById('notes').value = position.notes || '';
        
        // Cambiar título y acción del modal
        document.getElementById('positionModalTitle').innerHTML = '<i class="fas fa-edit"></i> Editar Posición';
        document.getElementById('savePositionBtn').innerHTML = '<i class="fas fa-save"></i> Actualizar Posición';
        document.getElementById('positionForm').action = `/rooms/<?= $room['id'] ?>/positions/${positionId}/update`;
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('addPositionModal'));
        modal.show();
    }
}

// Resetear modal al cerrarse
document.getElementById('addPositionModal').addEventListener('hidden.bs.modal', function() {
    currentEditingPosition = null;
    document.getElementById('positionForm').reset();
    document.getElementById('positionModalTitle').innerHTML = '<i class="fas fa-plus"></i> Nueva Posición';
    document.getElementById('savePositionBtn').innerHTML = '<i class="fas fa-save"></i> Guardar Posición';
    document.getElementById('positionForm').action = '/rooms/<?= $room['id'] ?>/positions/create';
});

// Confirmar eliminación de posición
function confirmDeletePosition(positionId, positionNumber) {
    document.getElementById('positionToDelete').textContent = positionNumber;
    document.getElementById('deletePositionForm').action = `/rooms/<?= $room['id'] ?>/positions/${positionId}/delete`;
    
    const modal = new bootstrap.Modal(document.getElementById('deletePositionModal'));
    modal.show();
}

// Generar posiciones automáticamente
function generatePositions() {
    const rows = prompt('¿Cuántas filas deseas crear?', '5');
    const seatsPerRow = prompt('¿Cuántos asientos por fila?', '6');
    
    if (rows && seatsPerRow) {
        if (confirm(`Esto creará ${rows * seatsPerRow} posiciones. ¿Continuar?`)) {
            // Aquí implementarías la lógica para generar posiciones automáticamente
            alert('Funcionalidad en desarrollo');
        }
    }
}

// Activar/desactivar todas las posiciones
function toggleAllPositions(activate) {
    const action = activate ? 'activar' : 'desactivar';
    if (confirm(`¿Estás seguro de que deseas ${action} todas las posiciones?`)) {
        // Aquí implementarías la lógica para activar/desactivar todas las posiciones
        alert('Funcionalidad en desarrollo');
    }
}

// Confirmar eliminación de todas las posiciones
function confirmDeleteAllPositions() {
    if (confirm('¿Estás seguro de que deseas eliminar TODAS las posiciones? Esta acción no se puede deshacer.')) {
        if (confirm('Esta acción eliminará permanentemente todas las posiciones. ¿Continuar?')) {
            // Aquí implementarías la lógica para eliminar todas las posiciones
            alert('Funcionalidad en desarrollo');
        }
    }
}
</script>

<?php include '../app/Views/layouts/footer.php'; ?>