<?php
// Verificar que las variables necesarias estén disponibles
if (!isset($class) || !isset($schedule) || !isset($accessStatus)) {
    echo '<div class="alert alert-danger">Error: Datos de clase no disponibles</div>';
    return;
}
?>

<div class="class-access-container">
    <!-- Header de la clase -->
    <div class="class-access-header">
        <div class="class-info">
            <h1 class="class-title"><?= htmlspecialchars($class['name']) ?></h1>
            <div class="class-meta">
                <span class="class-time">
                    <i class="fas fa-clock"></i>
                    <?= date('H:i', strtotime($schedule['start_time'])) ?> - <?= date('H:i', strtotime($schedule['end_time'])) ?>
                </span>
                <span class="class-date">
                    <i class="fas fa-calendar"></i>
                    <?= date('d/m/Y', strtotime($bookingDate)) ?>
                </span>
                <span class="class-duration">
                    <i class="fas fa-hourglass-half"></i>
                    <?= $class['duration_minutes'] ?> min
                </span>
            </div>
        </div>
        
        <!-- Estado de acceso -->
        <div class="access-status <?= $accessStatus['can_access'] ? 'active' : 'inactive' ?>">
            <div class="status-indicator">
                <i class="fas <?= $accessStatus['can_access'] ? 'fa-check-circle' : 'fa-clock' ?>"></i>
            </div>
            <div class="status-text">
                <h3><?= $accessStatus['can_access'] ? 'Acceso Activo' : 'Acceso Restringido' ?></h3>
                <p><?= htmlspecialchars($accessStatus['message']) ?></p>
                <?php if (isset($accessStatus['minutes_remaining'])): ?>
                    <span class="time-remaining">Tiempo restante: <?= $accessStatus['minutes_remaining'] ?> minutos</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($accessStatus['can_access']): ?>
    <!-- Contenido de la clase -->
    <div class="class-content">
        <div class="row">
            <!-- Información de la clase -->
            <div class="col-md-8">
                <div class="class-details-card">
                    <h3><i class="fas fa-info-circle"></i> Información de la Clase</h3>
                    <div class="class-description">
                        <p><?= htmlspecialchars($class['description']) ?></p>
                    </div>
                    
                    <div class="class-attributes">
                        <div class="attribute">
                            <span class="label">Tipo:</span>
                            <span class="value"><?= htmlspecialchars($class['class_type']) ?></span>
                        </div>
                        <div class="attribute">
                            <span class="label">Nivel:</span>
                            <span class="value"><?= htmlspecialchars($class['difficulty_level']) ?></span>
                        </div>
                        <div class="attribute">
                            <span class="label">Instructor:</span>
                            <span class="value"><?= htmlspecialchars($schedule['instructor_first_name'] . ' ' . $schedule['instructor_last_name']) ?></span>
                        </div>
                        <div class="attribute">
                            <span class="label">Participantes:</span>
                            <span class="value"><?= $class['max_participants'] ?> máximo</span>
                        </div>
                    </div>
                </div>

                <!-- Rutina de la clase -->
                <div class="class-routine-card">
                    <h3><i class="fas fa-dumbbell"></i> Rutina de la Clase</h3>
                    <div class="routine-content">
                        <div class="routine-phase">
                            <h4>Calentamiento (5-10 min)</h4>
                            <ul>
                                <li>Movilidad articular</li>
                                <li>Activación cardiovascular ligera</li>
                                <li>Estiramientos dinámicos</li>
                            </ul>
                        </div>
                        
                        <div class="routine-phase">
                            <h4>Desarrollo Principal (<?= max(0, $class['duration_minutes'] - 20) ?> min)</h4>
                            <ul>
                                <li>Ejercicios específicos del tipo de clase</li>
                                <li>Progresión de intensidad</li>
                                <li>Técnica y forma correcta</li>
                            </ul>
                        </div>
                        
                        <div class="routine-phase">
                            <h4>Enfriamiento (5-10 min)</h4>
                            <ul>
                                <li>Reducción gradual de intensidad</li>
                                <li>Estiramientos estáticos</li>
                                <li>Relajación y respiración</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel lateral -->
            <div class="col-md-4">
                <!-- Timer de acceso -->
                <div class="access-timer-card">
                    <h3><i class="fas fa-stopwatch"></i> Tiempo de Acceso</h3>
                    <div class="timer-display" id="accessTimer">
                        <span class="time-value"><?= $accessStatus['minutes_remaining'] ?? 0 ?></span>
                        <span class="time-label">minutos restantes</span>
                    </div>
                    <div class="timer-info">
                        <p>El acceso finaliza a las <?= $accessStatus['access_ends_at'] ?? '' ?></p>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="quick-actions-card">
                    <h3><i class="fas fa-bolt"></i> Acciones Rápidas</h3>
                    <div class="action-buttons">
                        <button class="btn btn-primary btn-block" onclick="startWorkout()">
                            <i class="fas fa-play"></i>
                            Iniciar Entrenamiento
                        </button>
                        <button class="btn btn-outline-secondary btn-block" onclick="viewExercises()">
                            <i class="fas fa-list"></i>
                            Ver Ejercicios
                        </button>
                        <button class="btn btn-outline-info btn-block" onclick="contactInstructor()">
                            <i class="fas fa-comments"></i>
                            Contactar Instructor
                        </button>
                    </div>
                </div>

                <!-- Progreso personal -->
                <div class="personal-progress-card">
                    <h3><i class="fas fa-chart-line"></i> Tu Progreso</h3>
                    <div class="progress-stats">
                        <div class="stat">
                            <span class="stat-value">12</span>
                            <span class="stat-label">Clases completadas</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value">85%</span>
                            <span class="stat-label">Asistencia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Mensaje de acceso denegado -->
    <div class="access-denied">
        <div class="denied-content">
            <i class="fas fa-lock fa-3x"></i>
            <h3>Acceso No Disponible</h3>
            <p><?= htmlspecialchars($accessStatus['message']) ?></p>
            
            <?php if (isset($accessStatus['access_starts_at'])): ?>
                <div class="access-info">
                    <p><strong>El acceso estará disponible a partir de:</strong></p>
                    <p class="access-time"><?= $accessStatus['access_starts_at'] ?></p>
                </div>
            <?php endif; ?>
            
            <div class="denied-actions">
                <a href="/classes/my-bookings" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Volver a Mis Reservas
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Timer de cuenta regresiva para el acceso
function updateAccessTimer() {
    const timerElement = document.getElementById('accessTimer');
    if (!timerElement) return;
    
    const timeValue = timerElement.querySelector('.time-value');
    let minutes = parseInt(timeValue.textContent);
    
    if (minutes > 0) {
        minutes--;
        timeValue.textContent = minutes;
        
        if (minutes === 0) {
            // Recargar la página cuando se acabe el tiempo
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }
}

// Actualizar timer cada minuto
setInterval(updateAccessTimer, 60000);

// Funciones de acciones rápidas
function startWorkout() {
    alert('Función de entrenamiento en desarrollo');
}

function viewExercises() {
    alert('Función de ejercicios en desarrollo');
}

function contactInstructor() {
    alert('Función de contacto en desarrollo');
}

// Verificar estado de acceso cada 30 segundos
setInterval(function() {
    fetch(`/classes/check-access?schedule_id=<?= $schedule['id'] ?>&booking_date=<?= $bookingDate ?>`)
        .then(response => response.json())
        .then(data => {
            if (!data.can_access && data.reason === 'too_late') {
                // El acceso ha expirado, redirigir
                window.location.href = '/classes/my-bookings';
            }
        })
        .catch(error => console.error('Error verificando acceso:', error));
}, 30000);
</script>