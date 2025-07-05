<?php
$title = 'Detalles de Sala - ' . $room['name'];
$pageTitle = 'Detalles de Sala';
include '../app/Views/layouts/header.php';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-door-open"></i> <?= htmlspecialchars($room['name']) ?>
                <?php if (!$room['is_active']): ?>
                    <span class="badge badge-secondary ml-2">Inactiva</span>
                <?php endif; ?>
            </h1>
            <p class="text-muted">
                <i class="fas fa-building"></i> <?= htmlspecialchars($room['gym_name']) ?>
                <?php if ($room['location_notes']): ?>
                    • <?= htmlspecialchars($room['location_notes']) ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group" role="group">
                <a href="/rooms" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                
                <?php if ($room['room_type'] === 'positioned' && $user['role'] === 'admin'): ?>
                    <a href="/rooms/<?= $room['id'] ?>/positions" class="btn btn-info">
                        <i class="fas fa-th"></i> Gestionar Posiciones
                    </a>
                <?php endif; ?>
                
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="/rooms/<?= $room['id'] ?>/edit" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información principal -->
        <div class="col-md-8">
            <!-- Información básica -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Información General
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Tipo de Sala</h6>
                            <?php if ($room['room_type'] === 'positioned'): ?>
                                <p class="mb-3">
                                    <span class="badge badge-primary badge-lg">
                                        <i class="fas fa-th"></i> Con Posiciones Específicas
                                    </span>
                                </p>
                            <?php else: ?>
                                <p class="mb-3">
                                    <span class="badge badge-success badge-lg">
                                        <i class="fas fa-users"></i> Solo Aforo
                                    </span>
                                </p>
                            <?php endif; ?>
                            
                            <h6 class="text-muted">Capacidad Total</h6>
                            <p class="mb-3">
                                <span class="h4 text-primary"><?= $room['total_capacity'] ?></span> personas
                            </p>
                            
                            <?php if ($room['dimensions']): ?>
                                <h6 class="text-muted">Dimensiones</h6>
                                <p class="mb-3"><?= htmlspecialchars($room['dimensions']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php if ($room['description']): ?>
                                <h6 class="text-muted">Descripción</h6>
                                <p class="mb-3"><?= nl2br(htmlspecialchars($room['description'])) ?></p>
                            <?php endif; ?>
                            
                            <h6 class="text-muted">Estado</h6>
                            <p class="mb-3">
                                <?php if ($room['is_active']): ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Activa
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-pause"></i> Inactiva
                                    </span>
                                <?php endif; ?>
                            </p>
                            
                            <h6 class="text-muted">Fecha de Creación</h6>
                            <p class="mb-3"><?= date('d/m/Y H:i', strtotime($room['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipamiento y Amenidades -->
            <?php if (!empty($room['equipment_available']) || !empty($room['amenities'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cogs"></i> Equipamiento y Amenidades
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if (!empty($room['equipment_available'])): ?>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Equipamiento Disponible</h6>
                                    <div class="equipment-list">
                                        <?php 
                                        $equipmentLabels = [
                                            'mats' => 'Colchonetas',
                                            'weights' => 'Pesas',
                                            'mirrors' => 'Espejos',
                                            'sound_system' => 'Sistema de Sonido',
                                            'air_conditioning' => 'Aire Acondicionado',
                                            'projector' => 'Proyector',
                                            'storage' => 'Almacenamiento',
                                            'water_dispenser' => 'Dispensador de Agua'
                                        ];
                                        ?>
                                        <?php foreach ($room['equipment_available'] as $equipment): ?>
                                            <span class="badge badge-info mr-1 mb-1">
                                                <i class="fas fa-check"></i> 
                                                <?= $equipmentLabels[$equipment] ?? ucfirst($equipment) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($room['amenities'])): ?>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Amenidades</h6>
                                    <div class="amenities-list">
                                        <?php 
                                        $amenitiesLabels = [
                                            'lockers' => 'Casilleros',
                                            'showers' => 'Duchas',
                                            'changing_room' => 'Vestidores',
                                            'parking' => 'Estacionamiento',
                                            'wifi' => 'WiFi',
                                            'natural_light' => 'Luz Natural',
                                            'ventilation' => 'Ventilación',
                                            'accessibility' => 'Accesibilidad'
                                        ];
                                        ?>
                                        <?php foreach ($room['amenities'] as $amenity): ?>
                                            <span class="badge badge-success mr-1 mb-1">
                                                <i class="fas fa-check"></i> 
                                                <?= $amenitiesLabels[$amenity] ?? ucfirst($amenity) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Plano de la sala -->
            <?php if ($room['floor_plan_image']): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-map"></i> Plano de la Sala
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <img src="<?= htmlspecialchars($room['floor_plan_image']) ?>" 
                             alt="Plano de <?= htmlspecialchars($room['name']) ?>" 
                             class="img-fluid rounded shadow" 
                             style="max-height: 400px;">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Posiciones (solo para salas con posiciones específicas) -->
            <?php if ($room['room_type'] === 'positioned'): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-th"></i> Posiciones Configuradas
                        </h6>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="/rooms/<?= $room['id'] ?>/positions" class="btn btn-sm btn-primary">
                                <i class="fas fa-cog"></i> Gestionar
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (isset($room['positions']) && !empty($room['positions'])): ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h4 class="text-primary"><?= count($room['positions']) ?></h4>
                                        <small class="text-muted">Posiciones Totales</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h4 class="text-success">
                                            <?= count(array_filter($room['positions'], fn($p) => $p['is_available'])) ?>
                                        </h4>
                                        <small class="text-muted">Disponibles</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h4 class="text-warning">
                                            <?= count(array_filter($room['positions'], fn($p) => !$p['is_available'])) ?>
                                        </h4>
                                        <small class="text-muted">No Disponibles</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Vista previa de posiciones -->
                            <div class="mt-4">
                                <h6 class="text-muted mb-3">Vista Previa de Posiciones</h6>
                                <div class="positions-preview" style="max-height: 300px; overflow-y: auto;">
                                    <div class="position-grid">
                                        <?php 
                                        // Agrupar posiciones por fila
                                        $positionsByRow = [];
                                        foreach ($room['positions'] as $position) {
                                            $row = $position['row_number'] ?: 'Sin fila';
                                            $positionsByRow[$row][] = $position;
                                        }
                                        ksort($positionsByRow);
                                        ?>
                                        
                                        <?php foreach ($positionsByRow as $row => $positions): ?>
                                            <div class="position-row mb-2">
                                                <small class="text-muted d-block mb-1">Fila <?= htmlspecialchars($row) ?></small>
                                                <div class="d-flex flex-wrap">
                                                    <?php foreach ($positions as $position): ?>
                                                        <div class="position-seat m-1" 
                                                             title="Posición <?= htmlspecialchars($position['position_number']) ?>">
                                                            <span class="badge <?= $position['is_available'] ? 'badge-success' : 'badge-secondary' ?>">
                                                                <?= htmlspecialchars($position['position_number']) ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-th fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No hay posiciones configuradas</h6>
                                <p class="text-muted">Esta sala permite posiciones específicas pero aún no se han configurado.</p>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <a href="/rooms/<?= $room['id'] ?>/positions" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Configurar Posiciones
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Panel lateral -->
        <div class="col-md-4">
            <!-- Estadísticas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Estadísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-primary"><?= $stats['total_classes'] ?? 0 ?></h4>
                                <small class="text-muted">Clases Totales</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success"><?= $stats['active_classes'] ?? 0 ?></h4>
                            <small class="text-muted">Clases Activas</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-info"><?= $stats['total_bookings'] ?? 0 ?></h4>
                                <small class="text-muted">Reservas Totales</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning"><?= $stats['avg_occupancy'] ?? 0 ?>%</h4>
                            <small class="text-muted">Ocupación Promedio</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clases próximas -->
            <?php if (isset($stats['upcoming_classes']) && !empty($stats['upcoming_classes'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-alt"></i> Próximas Clases
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php foreach (array_slice($stats['upcoming_classes'], 0, 5) as $class): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0"><?= htmlspecialchars($class['name']) ?></h6>
                                    <small class="text-muted">
                                        <?= date('d/m H:i', strtotime($class['next_session'])) ?>
                                    </small>
                                </div>
                                <span class="badge badge-primary">
                                    <?= $class['bookings'] ?>/<?= $class['max_participants'] ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($stats['upcoming_classes']) > 5): ?>
                            <div class="text-center">
                                <small class="text-muted">
                                    Y <?= count($stats['upcoming_classes']) - 5 ?> clases más...
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Información del gimnasio -->
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building"></i> Información del Gimnasio
                    </h6>
                </div>
                <div class="card-body">
                    <h6><?= htmlspecialchars($room['gym_name']) ?></h6>
                    <?php if (isset($room['gym_address'])): ?>
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt"></i> 
                            <?= htmlspecialchars($room['gym_address']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (isset($room['gym_phone'])): ?>
                        <p class="text-muted mb-2">
                            <i class="fas fa-phone"></i> 
                            <?= htmlspecialchars($room['gym_phone']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <a href="/gyms/<?= $room['gym_id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Ver Gimnasio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}

.position-grid {
    background: #f8f9fc;
    border-radius: 0.375rem;
    padding: 1rem;
}

.position-row {
    margin-bottom: 0.5rem;
}

.position-seat {
    display: inline-block;
}

.equipment-list .badge,
.amenities-list .badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
}

.border-right {
    border-right: 1px solid #e3e6f0 !important;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
}
</style>

<?php include '../app/Views/layouts/footer.php'; ?>