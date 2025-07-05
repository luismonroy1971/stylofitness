<?php
$pageTitle = 'Seleccionar Posición - ' . ($class['name'] ?? 'Clase Grupal');
$additionalCSS = ['room-layout.css'];
$additionalJS = ['room-layout.js'];
?>

<div class="room-layout-container">
    <!-- Header de la clase -->
    <div class="class-header">
        <div class="class-info">
            <h1><?= htmlspecialchars($class['name']) ?></h1>
            <div class="class-details">
                <span class="instructor">
                    <i class="fas fa-user"></i>
                    <?= htmlspecialchars($schedule['instructor_name']) ?>
                </span>
                <span class="date">
                    <i class="fas fa-calendar"></i>
                    <?= date('d/m/Y', strtotime($bookingDate)) ?>
                </span>
                <span class="time">
                    <i class="fas fa-clock"></i>
                    <?= date('H:i', strtotime($schedule['start_time'])) ?> - <?= date('H:i', strtotime($schedule['end_time'])) ?>
                </span>
                <span class="room">
                    <i class="fas fa-map-marker-alt"></i>
                    <?= htmlspecialchars($room['name']) ?> - <?= htmlspecialchars($gym['name']) ?>
                </span>
            </div>
        </div>
        <div class="booking-timer" id="bookingTimer" style="display: none;">
            <div class="timer-content">
                <i class="fas fa-clock"></i>
                <span>Tiempo restante: <span id="timeRemaining">5:00</span></span>
            </div>
        </div>
    </div>

    <!-- Información de la sala -->
    <div class="room-info">
        <div class="room-stats">
            <div class="stat">
                <span class="label">Capacidad Total:</span>
                <span class="value"><?= $room['capacity'] ?></span>
            </div>
            <div class="stat">
                <span class="label">Posiciones Disponibles:</span>
                <span class="value" id="availableCount"><?= count($availablePositions) ?></span>
            </div>
            <div class="stat">
                <span class="label">Posiciones Ocupadas:</span>
                <span class="value" id="occupiedCount"><?= count($occupiedPositions) ?></span>
            </div>
        </div>
        
        <!-- Leyenda -->
        <div class="legend">
            <div class="legend-item">
                <div class="position-sample available"></div>
                <span>Disponible</span>
            </div>
            <div class="legend-item">
                <div class="position-sample occupied"></div>
                <span>Ocupada</span>
            </div>
            <div class="legend-item">
                <div class="position-sample selected"></div>
                <span>Seleccionada</span>
            </div>
            <div class="legend-item">
                <div class="position-sample temp-reserved"></div>
                <span>Reserva Temporal</span>
            </div>
        </div>
    </div>

    <!-- Layout de la sala -->
    <div class="room-layout" id="roomLayout">
        <?php if ($room['room_type'] === 'positioned'): ?>
            <!-- Vista de posiciones específicas -->
            <div class="positioned-layout">
                <div class="stage">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Instructor</span>
                </div>
                
                <div class="positions-grid" id="positionsGrid">
                    <?php 
                    $occupiedIds = array_column($occupiedPositions, 'position_id');
                    $maxRow = max(array_column($positions, 'row_number'));
                    $maxSeat = max(array_column($positions, 'seat_number'));
                    
                    for ($row = 1; $row <= $maxRow; $row++): ?>
                        <div class="position-row" data-row="<?= $row ?>">
                            <div class="row-label">Fila <?= $row ?></div>
                            <div class="row-positions">
                                <?php 
                                $rowPositions = array_filter($positions, function($p) use ($row) {
                                    return $p['row_number'] == $row;
                                });
                                usort($rowPositions, function($a, $b) {
                                    return $a['seat_number'] - $b['seat_number'];
                                });
                                
                                foreach ($rowPositions as $position): 
                                    $isOccupied = in_array($position['id'], $occupiedIds);
                                    $isAvailable = $position['is_available'] && !$isOccupied;
                                ?>
                                    <div class="position <?= $isOccupied ? 'occupied' : ($isAvailable ? 'available' : 'unavailable') ?>" 
                                         data-position-id="<?= $position['id'] ?>"
                                         data-row="<?= $position['row_number'] ?>"
                                         data-seat="<?= $position['seat_number'] ?>"
                                         <?= $isAvailable ? 'onclick="selectPosition(this)"' : '' ?>>
                                        <span class="position-number"><?= $position['seat_number'] ?></span>
                                        <?php if ($position['position_type'] === 'premium'): ?>
                                            <i class="fas fa-star premium-icon"></i>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Vista de aforo general -->
            <div class="capacity-layout">
                <div class="capacity-info">
                    <i class="fas fa-users"></i>
                    <h3>Clase de Aforo General</h3>
                    <p>Esta clase no requiere selección de posición específica.</p>
                    <div class="capacity-stats">
                        <div class="stat-large">
                            <span class="number"><?= $availableSpots ?></span>
                            <span class="label">Cupos Disponibles</span>
                        </div>
                        <div class="stat-large">
                            <span class="number"><?= $room['capacity'] ?></span>
                            <span class="label">Capacidad Total</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Información de la posición seleccionada -->
    <div class="selected-position-info" id="selectedPositionInfo" style="display: none;">
        <div class="position-details">
            <h4>Posición Seleccionada</h4>
            <div class="details">
                <span class="position-label">Fila <span id="selectedRow"></span>, Asiento <span id="selectedSeat"></span></span>
                <span class="position-type" id="selectedType"></span>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="action-buttons">
        <button type="button" class="btn btn-secondary" onclick="goBack()">
            <i class="fas fa-arrow-left"></i>
            Volver
        </button>
        
        <?php if ($room['room_type'] === 'positioned'): ?>
            <button type="button" class="btn btn-primary" id="confirmPositionBtn" disabled onclick="confirmPosition()">
                <i class="fas fa-check"></i>
                Confirmar Posición
            </button>
        <?php else: ?>
            <button type="button" class="btn btn-primary" onclick="bookWithoutPosition()">
                <i class="fas fa-check"></i>
                Reservar Clase
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="booking-summary">
                    <h6>Resumen de la Reserva</h6>
                    <div class="summary-item">
                        <span class="label">Clase:</span>
                        <span class="value"><?= htmlspecialchars($class['name']) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Instructor:</span>
                        <span class="value"><?= htmlspecialchars($schedule['instructor_name']) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Fecha:</span>
                        <span class="value"><?= date('d/m/Y', strtotime($bookingDate)) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Hora:</span>
                        <span class="value"><?= date('H:i', strtotime($schedule['start_time'])) ?> - <?= date('H:i', strtotime($schedule['end_time'])) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Sala:</span>
                        <span class="value"><?= htmlspecialchars($room['name']) ?></span>
                    </div>
                    <?php if ($room['room_type'] === 'positioned'): ?>
                    <div class="summary-item" id="positionSummary" style="display: none;">
                        <span class="label">Posición:</span>
                        <span class="value" id="positionSummaryText"></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($class['price'] > 0): ?>
                    <div class="summary-item price">
                        <span class="label">Precio:</span>
                        <span class="value">$<?= number_format($class['price'], 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="finalizeBooking()">
                    <i class="fas fa-check"></i>
                    Confirmar Reserva
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
const scheduleId = <?= $schedule['id'] ?>;
const bookingDate = '<?= $bookingDate ?>';
const roomType = '<?= $room['room_type'] ?>';
let selectedPositionId = null;
let tempReservationId = null;
let countdownTimer = null;

// Datos para JavaScript
window.roomLayoutData = {
    scheduleId: scheduleId,
    bookingDate: bookingDate,
    roomType: roomType,
    positions: <?= json_encode($positions) ?>,
    occupiedPositions: <?= json_encode($occupiedPositions) ?>
};
</script>