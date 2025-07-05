<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista de Detalles de Clase Grupal - STYLOFITNESS
 * Muestra información detallada de una clase y permite reservar
 */

$pageTitle = htmlspecialchars($class['name']) . ' - STYLOFITNESS';
$additionalCSS = ['class-details.css'];
$additionalJS = ['class-details.js'];
?>

<div class="class-details-container">
    <!-- Header de la clase -->
    <div class="class-header">
        <div class="class-hero">
            <div class="class-image">
                <img src="<?= htmlspecialchars($class['image_url'] ?: AppHelper::asset('images/placeholder.jpg')) ?>" alt="<?= htmlspecialchars($class['name']) ?>">
                <div class="class-difficulty <?= strtolower($class['difficulty_level']) ?>">
                    <?= htmlspecialchars($class['difficulty_level']) ?>
                </div>
            </div>
            <div class="class-info">
                <h1><?= htmlspecialchars($class['name']) ?></h1>
                <p class="class-type"><?= htmlspecialchars($class['class_type']) ?></p>
                <div class="class-meta">
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span><?= htmlspecialchars($class['duration_minutes']) ?> minutos</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-users"></i>
                        <span>Máximo <?= htmlspecialchars($class['max_participants']) ?> participantes</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($class['room_info']['name'] ?? 'Sala no asignada') ?></span>
                    </div>
                    <?php if (isset($class['room_info'])): ?>
                    <div class="meta-item room-type">
                        <?php if ($class['room_info']['room_type'] === 'positioned'): ?>
                            <i class="fas fa-th"></i>
                            <span class="room-type-badge positioned">Selección de Posición</span>
                        <?php else: ?>
                            <i class="fas fa-users"></i>
                            <span class="room-type-badge capacity">Solo Aforo</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($class['price'] > 0): ?>
                    <div class="meta-item price">
                        <i class="fas fa-dollar-sign"></i>
                        <span>$<?= number_format($class['price'], 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="class-description">
                    <p><?= nl2br(htmlspecialchars($class['description'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de la sala -->
    <?php if (isset($class['room_info'])): ?>
    <div class="room-info-section">
        <div class="section-header">
            <h2>Información de la Sala</h2>
        </div>
        <div class="room-details">
            <div class="room-card">
                <div class="room-header">
                    <h3><?= htmlspecialchars($class['room_info']['name']) ?></h3>
                    <span class="room-type-label <?= $class['room_info']['room_type'] ?>">
                        <?= $class['room_info']['room_type'] === 'positioned' ? 'Posiciones Específicas' : 'Solo Aforo' ?>
                    </span>
                </div>
                <div class="room-stats">
                    <div class="stat">
                        <span class="label">Capacidad:</span>
                        <span class="value"><?= htmlspecialchars($class['room_info']['capacity']) ?> personas</span>
                    </div>
                    <div class="stat">
                        <span class="label">Tipo:</span>
                        <span class="value"><?= htmlspecialchars($class['room_info']['room_type']) ?></span>
                    </div>
                    <div class="stat">
                        <span class="label">Gimnasio:</span>
                        <span class="value"><?= htmlspecialchars($class['gym_name']) ?></span>
                    </div>
                </div>
                <?php if (!empty($class['room_info']['description'])): ?>
                <div class="room-description">
                    <p><?= nl2br(htmlspecialchars($class['room_info']['description'])) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($class['room_info']['room_type'] === 'positioned'): ?>
                <div class="position-info">
                    <div class="info-card">
                        <i class="fas fa-info-circle"></i>
                        <div class="info-content">
                            <h4>Selección de Posición</h4>
                            <p>Esta clase permite seleccionar tu posición específica en la sala. Podrás elegir tu lugar preferido al momento de reservar.</p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="capacity-info">
                    <div class="info-card">
                        <i class="fas fa-users"></i>
                        <div class="info-content">
                            <h4>Clase de Aforo</h4>
                            <p>Esta clase controla únicamente el número total de participantes. No requiere selección de posición específica.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Horarios disponibles -->
    <div class="schedules-section">
        <div class="section-header">
            <h2>Horarios Disponibles</h2>
            <p>Selecciona el horario que mejor se adapte a tu rutina</p>
        </div>
        
        <?php if (empty($schedules)): ?>
            <div class="no-schedules">
                <i class="fas fa-calendar-times"></i>
                <h3>No hay horarios disponibles</h3>
                <p>Esta clase no tiene horarios programados en este momento.</p>
            </div>
        <?php else: ?>
            <div class="schedules-grid">
                <?php 
                $groupedSchedules = [];
                foreach ($schedules as $schedule) {
                    $groupedSchedules[$schedule['day_of_week']][] = $schedule;
                }
                
                $dayNames = [
                    'monday' => 'Lunes',
                    'tuesday' => 'Martes', 
                    'wednesday' => 'Miércoles',
                    'thursday' => 'Jueves',
                    'friday' => 'Viernes',
                    'saturday' => 'Sábado',
                    'sunday' => 'Domingo'
                ];
                
                foreach ($groupedSchedules as $day => $daySchedules): ?>
                    <div class="day-schedule">
                        <h3 class="day-name"><?= $dayNames[$day] ?? ucfirst($day) ?></h3>
                        <div class="time-slots">
                            <?php foreach ($daySchedules as $schedule): ?>
                                <div class="time-slot" data-schedule-id="<?= $schedule['id'] ?>">
                                    <div class="time-info">
                                        <span class="time"><?= date('H:i', strtotime($schedule['start_time'])) ?> - <?= date('H:i', strtotime($schedule['end_time'])) ?></span>
                                        <span class="instructor"><?= htmlspecialchars($schedule['instructor_name']) ?></span>
                                    </div>
                                    <div class="availability-info">
                                        <span class="available-spots" id="spots-<?= $schedule['id'] ?>">Cargando...</span>
                                    </div>
                                    <?php if (AppHelper::isLoggedIn()): ?>
                                        <button class="btn-book-schedule" onclick="bookSchedule(<?= $schedule['id'] ?>)">
                                            <i class="fas fa-calendar-plus"></i>
                                            Reservar
                                        </button>
                                    <?php else: ?>
                                        <a href="/login?redirect=/classes/<?= $class['id'] ?>" class="btn-login">
                                            <i class="fas fa-sign-in-alt"></i>
                                            Iniciar Sesión
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Instructores -->
    <?php if (!empty($instructors)): ?>
    <div class="instructors-section">
        <div class="section-header">
            <h2>Nuestros Instructores</h2>
        </div>
        <div class="instructors-grid">
            <?php foreach ($instructors as $instructor): ?>
                <div class="instructor-card">
                    <div class="instructor-avatar">
                        <img src="<?= htmlspecialchars($instructor['profile_image'] ?: AppHelper::asset('images/default-avatar.jpg')) ?>" alt="<?= htmlspecialchars($instructor['name']) ?>">
                    </div>
                    <div class="instructor-info">
                        <h4><?= htmlspecialchars($instructor['name']) ?></h4>
                        <?php if (!empty($instructor['specialization'])): ?>
                            <p class="specialization"><?= htmlspecialchars($instructor['specialization']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($instructor['bio'])): ?>
                            <p class="bio"><?= htmlspecialchars(substr($instructor['bio'], 0, 100)) ?>...</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Botones de acción -->
    <div class="action-buttons">
        <a href="/classes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver a Clases
        </a>
        <?php if (AppHelper::isLoggedIn()): ?>
            <a href="/classes/my-bookings" class="btn btn-outline">
                <i class="fas fa-calendar-check"></i>
                Mis Reservas
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de selección de fecha -->
<div class="modal fade" id="dateSelectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Fecha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="schedule-info" id="selectedScheduleInfo"></div>
                <div class="date-picker">
                    <label for="bookingDate">Fecha de la clase:</label>
                    <input type="date" id="bookingDate" class="form-control" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
                <div class="availability-check" id="availabilityCheck" style="display: none;">
                    <div class="availability-info">
                        <span id="availabilityText"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="proceedToBooking" disabled>
                    <i class="fas fa-arrow-right"></i>
                    Continuar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
const classId = <?= $class['id'] ?>;
const roomType = '<?= $class['room_info']['room_type'] ?? 'capacity' ?>';
let selectedScheduleId = null;

// Datos para JavaScript
window.classData = {
    id: classId,
    name: '<?= addslashes($class['name']) ?>',
    roomType: roomType,
    schedules: <?= json_encode($schedules) ?>
};
</script>