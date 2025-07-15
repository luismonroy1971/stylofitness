<?php
// Verificar que las variables necesarias estén disponibles
if (!isset($upcomingBookings) || !isset($pastBookings)) {
    echo '<div class="alert alert-danger">Error: No se pudieron cargar las reservas</div>';
    return;
}
?>

<div class="my-bookings-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-calendar-check"></i>
            Mis Reservas de Clases
        </h1>
        <p class="page-subtitle">Gestiona tus reservas y accede a tus clases</p>
    </div>

    <!-- Navegación de pestañas -->
    <div class="bookings-tabs">
        <button class="tab-btn active" data-tab="upcoming">
            <i class="fas fa-clock"></i>
            Próximas Clases
            <span class="badge"><?= count($upcomingBookings) ?></span>
        </button>
        <button class="tab-btn" data-tab="past">
            <i class="fas fa-history"></i>
            Historial
            <span class="badge"><?= count($pastBookings) ?></span>
        </button>
    </div>

    <!-- Contenido de próximas clases -->
    <div class="tab-content active" id="upcoming">
        <?php if (empty($upcomingBookings)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h3>No tienes clases reservadas</h3>
                <p>¡Explora nuestras clases grupales y reserva tu próxima sesión!</p>
                <a href="/classes" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Explorar Clases
                </a>
            </div>
        <?php else: ?>
            <div class="bookings-grid">
                <?php foreach ($upcomingBookings as $booking): ?>
                    <?php
                    // Calcular estado de acceso para cada clase
                    $accessStatus = $this->classModel->getUserClassAccessStatus(
                        $booking['schedule_id'],
                        $currentUser['id'],
                        $booking['booking_date']
                    );
                    
                    $bookingDateTime = $booking['booking_date'] . ' ' . $booking['start_time'];
                    $isToday = date('Y-m-d') === $booking['booking_date'];
                    $isPast = strtotime($bookingDateTime) < time();
                    ?>
                    
                    <div class="booking-card <?= $accessStatus['can_access'] ? 'accessible' : '' ?> <?= $isToday ? 'today' : '' ?>">
                        <!-- Header de la reserva -->
                        <div class="booking-header">
                            <div class="class-info">
                                <h3 class="class-name"><?= htmlspecialchars($booking['class_name']) ?></h3>
                                <div class="class-meta">
                                    <span class="instructor">
                                        <i class="fas fa-user"></i>
                                        <?= htmlspecialchars($booking['instructor_first_name'] . ' ' . $booking['instructor_last_name']) ?>
                                    </span>
                                    <span class="room">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($booking['room_name']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Estado de acceso -->
                            <div class="access-indicator <?= $accessStatus['can_access'] ? 'active' : 'inactive' ?>">
                                <?php if ($accessStatus['can_access']): ?>
                                    <i class="fas fa-unlock"></i>
                                    <span>Acceso Activo</span>
                                <?php elseif ($accessStatus['reason'] === 'too_early'): ?>
                                    <i class="fas fa-clock"></i>
                                    <span>Próximamente</span>
                                <?php elseif ($accessStatus['reason'] === 'too_late'): ?>
                                    <i class="fas fa-lock"></i>
                                    <span>Expirado</span>
                                <?php else: ?>
                                    <i class="fas fa-calendar"></i>
                                    <span>Programado</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Información de fecha y hora -->
                        <div class="booking-datetime">
                            <div class="date-info">
                                <div class="date">
                                    <span class="day"><?= date('d', strtotime($booking['booking_date'])) ?></span>
                                    <span class="month"><?= date('M', strtotime($booking['booking_date'])) ?></span>
                                </div>
                                <div class="day-name">
                                    <?= date('l', strtotime($booking['booking_date'])) ?>
                                </div>
                            </div>
                            
                            <div class="time-info">
                                <div class="time-range">
                                    <i class="fas fa-clock"></i>
                                    <?= date('H:i', strtotime($booking['start_time'])) ?> - 
                                    <?= date('H:i', strtotime($booking['end_time'])) ?>
                                </div>
                                <div class="duration">
                                    <?= $booking['duration_minutes'] ?> minutos
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="booking-details">
                            <div class="detail-item">
                                <span class="label">Tipo:</span>
                                <span class="value"><?= htmlspecialchars($booking['class_type']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Nivel:</span>
                                <span class="value"><?= htmlspecialchars($booking['difficulty_level']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Estado:</span>
                                <span class="value status-<?= $booking['status'] ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                            </div>
                            <?php if (isset($booking['position_number'])): ?>
                                <div class="detail-item">
                                    <span class="label">Posición:</span>
                                    <span class="value">Fila <?= $booking['row_number'] ?>, Asiento <?= $booking['seat_number'] ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Mensaje de acceso -->
                        <?php if (isset($accessStatus['message'])): ?>
                            <div class="access-message <?= $accessStatus['can_access'] ? 'success' : 'info' ?>">
                                <i class="fas <?= $accessStatus['can_access'] ? 'fa-check-circle' : 'fa-info-circle' ?>"></i>
                                <?= htmlspecialchars($accessStatus['message']) ?>
                                <?php if (isset($accessStatus['minutes_remaining']) && $accessStatus['can_access']): ?>
                                    <br><small>Tiempo restante: <?= $accessStatus['minutes_remaining'] ?> minutos</small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Acciones -->
                        <div class="booking-actions">
                            <?php if ($accessStatus['can_access']): ?>
                                <!-- Botón de acceso principal -->
                                <a href="/classes/access?schedule_id=<?= $booking['schedule_id'] ?>&booking_date=<?= $booking['booking_date'] ?>" 
                                   class="btn btn-success btn-access">
                                    <i class="fas fa-play"></i>
                                    Acceder a la Clase
                                </a>
                            <?php elseif (!$isPast && $booking['status'] === 'booked'): ?>
                                <!-- Botón de cancelar -->
                                <button class="btn btn-outline-danger btn-cancel" 
                                        data-booking-id="<?= $booking['booking_id'] ?>"
                                        data-class-name="<?= htmlspecialchars($booking['class_name']) ?>">
                                    <i class="fas fa-times"></i>
                                    Cancelar Reserva
                                </button>
                            <?php endif; ?>
                            
                            <!-- Botón de detalles -->
                            <button class="btn btn-outline-primary btn-details" 
                                    data-booking='<?= json_encode($booking) ?>'>
                                <i class="fas fa-info"></i>
                                Ver Detalles
                            </button>
                            
                            <!-- Botón de contactar instructor -->
                            <?php if ($isToday || $accessStatus['can_access']): ?>
                                <button class="btn btn-outline-info btn-contact" 
                                        data-instructor-id="<?= $booking['instructor_id'] ?>"
                                        data-instructor-name="<?= htmlspecialchars($booking['instructor_first_name'] . ' ' . $booking['instructor_last_name']) ?>">
                                    <i class="fas fa-comments"></i>
                                    Contactar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contenido de historial -->
    <div class="tab-content" id="past">
        <?php if (empty($pastBookings)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3>Sin historial de clases</h3>
                <p>Aquí aparecerán las clases que hayas completado.</p>
            </div>
        <?php else: ?>
            <div class="bookings-list">
                <?php foreach ($pastBookings as $booking): ?>
                    <div class="booking-item past">
                        <div class="booking-summary">
                            <div class="class-info">
                                <h4><?= htmlspecialchars($booking['class_name']) ?></h4>
                                <p class="instructor"><?= htmlspecialchars($booking['instructor_first_name'] . ' ' . $booking['instructor_last_name']) ?></p>
                            </div>
                            <div class="date-time">
                                <span class="date"><?= date('d/m/Y', strtotime($booking['booking_date'])) ?></span>
                                <span class="time"><?= date('H:i', strtotime($booking['start_time'])) ?></span>
                            </div>
                            <div class="status">
                                <span class="status-badge status-<?= $booking['status'] ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="booking-actions-minimal">
                            <button class="btn btn-sm btn-outline-primary" 
                                    data-booking='<?= json_encode($booking) ?>'>
                                <i class="fas fa-eye"></i>
                                Ver Detalles
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de detalles de reserva -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingDetailsContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de cancelación -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas cancelar tu reserva para <strong id="cancelClassName"></strong>?</p>
                <p class="text-muted">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, mantener reserva</button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn">Sí, cancelar reserva</button>
            </div>
        </div>
    </div>
</div>

<script>
// Gestión de pestañas
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabId = this.dataset.tab;
        
        // Actualizar botones
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Actualizar contenido
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(tabId).classList.add('active');
    });
});

// Gestión de detalles de reserva
document.querySelectorAll('.btn-details').forEach(btn => {
    btn.addEventListener('click', function() {
        const booking = JSON.parse(this.dataset.booking);
        showBookingDetails(booking);
    });
});

function showBookingDetails(booking) {
    const content = `
        <div class="booking-details-full">
            <div class="row">
                <div class="col-md-6">
                    <h6>Información de la Clase</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Clase:</strong></td><td>${booking.class_name}</td></tr>
                        <tr><td><strong>Tipo:</strong></td><td>${booking.class_type}</td></tr>
                        <tr><td><strong>Nivel:</strong></td><td>${booking.difficulty_level}</td></tr>
                        <tr><td><strong>Duración:</strong></td><td>${booking.duration_minutes} minutos</td></tr>
                        <tr><td><strong>Instructor:</strong></td><td>${booking.instructor_first_name} ${booking.instructor_last_name}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Información de la Reserva</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Fecha:</strong></td><td>${new Date(booking.booking_date).toLocaleDateString()}</td></tr>
                        <tr><td><strong>Hora:</strong></td><td>${booking.start_time} - ${booking.end_time}</td></tr>
                        <tr><td><strong>Sala:</strong></td><td>${booking.room_name}</td></tr>
                        <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${booking.status === 'booked' ? 'success' : 'secondary'}">${booking.status}</span></td></tr>
                        ${booking.position_number ? `<tr><td><strong>Posición:</strong></td><td>Fila ${booking.row_number}, Asiento ${booking.seat_number}</td></tr>` : ''}
                    </table>
                </div>
            </div>
            ${booking.class_description ? `
                <div class="mt-3">
                    <h6>Descripción de la Clase</h6>
                    <p>${booking.class_description}</p>
                </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('bookingDetailsContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('bookingDetailsModal')).show();
}

// Gestión de cancelación de reservas
let bookingToCancel = null;

document.querySelectorAll('.btn-cancel').forEach(btn => {
    btn.addEventListener('click', function() {
        bookingToCancel = this.dataset.bookingId;
        document.getElementById('cancelClassName').textContent = this.dataset.className;
        new bootstrap.Modal(document.getElementById('cancelBookingModal')).show();
    });
});

document.getElementById('confirmCancelBtn').addEventListener('click', function() {
    if (bookingToCancel) {
        cancelBooking(bookingToCancel);
    }
});

function cancelBooking(bookingId) {
    fetch('/classes/cancel-booking', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            booking_id: bookingId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('cancelBookingModal')).hide();
            
            // Mostrar mensaje de éxito
            showAlert('Reserva cancelada exitosamente', 'success');
            
            // Recargar página después de un momento
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert(data.error || 'Error al cancelar la reserva', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger');
    });
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.my-bookings-container').insertBefore(alertDiv, document.querySelector('.page-header').nextSibling);
    
    // Auto-dismiss después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Actualizar estados de acceso cada 30 segundos
setInterval(function() {
    // Solo actualizar si estamos en la pestaña de próximas clases
    if (document.querySelector('.tab-btn[data-tab="upcoming"]').classList.contains('active')) {
        updateAccessStates();
    }
}, 30000);

function updateAccessStates() {
    const bookingCards = document.querySelectorAll('.booking-card');
    
    bookingCards.forEach(card => {
        const accessBtn = card.querySelector('.btn-access');
        if (accessBtn) {
            const url = new URL(accessBtn.href);
            const scheduleId = url.searchParams.get('schedule_id');
            const bookingDate = url.searchParams.get('booking_date');
            
            fetch(`/classes/check-access?schedule_id=${scheduleId}&booking_date=${bookingDate}`)
                .then(response => response.json())
                .then(data => {
                    updateCardAccessState(card, data);
                })
                .catch(error => console.error('Error checking access:', error));
        }
    });
}

function updateCardAccessState(card, accessStatus) {
    const indicator = card.querySelector('.access-indicator');
    const message = card.querySelector('.access-message');
    const accessBtn = card.querySelector('.btn-access');
    
    // Actualizar indicador
    indicator.className = `access-indicator ${accessStatus.can_access ? 'active' : 'inactive'}`;
    
    // Actualizar mensaje
    if (message) {
        message.innerHTML = `
            <i class="fas ${accessStatus.can_access ? 'fa-check-circle' : 'fa-info-circle'}"></i>
            ${accessStatus.message}
            ${accessStatus.minutes_remaining && accessStatus.can_access ? `<br><small>Tiempo restante: ${accessStatus.minutes_remaining} minutos</small>` : ''}
        `;
        message.className = `access-message ${accessStatus.can_access ? 'success' : 'info'}`;
    }
    
    // Mostrar/ocultar botón de acceso
    if (accessBtn) {
        accessBtn.style.display = accessStatus.can_access ? 'inline-flex' : 'none';
    }
}
</script>