<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Detalle de Rutina - STYLOFITNESS
 * Muestra el detalle completo de una rutina con ejercicios por días
 */

$currentUser = \StyleFitness\Helpers\AppHelper::getCurrentUser();
$isClient = ($currentUser['role'] === 'client');
$isInstructor = ($currentUser['role'] === 'instructor');
$isAdmin = ($currentUser['role'] === 'admin');
$canEdit = $isAdmin || ($isInstructor && $routine['instructor_id'] == $currentUser['id']);
$isMyRoutine = $isClient && $routine['client_id'] == $currentUser['id'];

// Organizar ejercicios por día
$dayNames = [
    1 => 'Día 1',
    2 => 'Día 2', 
    3 => 'Día 3',
    4 => 'Día 4',
    5 => 'Día 5',
    6 => 'Día 6',
    7 => 'Día 7'
];

$objectiveLabels = [
    'weight_loss' => 'Pérdida de Peso',
    'muscle_gain' => 'Ganancia Muscular',
    'strength' => 'Fuerza',
    'endurance' => 'Resistencia',
    'flexibility' => 'Flexibilidad'
];

$difficultyLabels = [
    'beginner' => 'Principiante',
    'intermediate' => 'Intermedio',
    'advanced' => 'Avanzado'
];
?>

<div class="routine-detail-page">
    <!-- Header de la Rutina -->
    <section class="routine-header bg-gradient-primary">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="breadcrumb-wrapper mb-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="/routines" class="text-white-75">Rutinas</a>
                                </li>
                                <li class="breadcrumb-item active text-white">
                                    <?= htmlspecialchars($routine['name']) ?>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    
                    <h1 class="routine-title text-white mb-3">
                        <?= htmlspecialchars($routine['name']) ?>
                    </h1>
                    
                    <p class="routine-description text-white-75 mb-4">
                        <?= htmlspecialchars($routine['description']) ?>
                    </p>
                    
                    <div class="routine-badges mb-4">
                        <span class="badge badge-white badge-lg me-2">
                            <i class="fas fa-target me-1"></i>
                            <?= $objectiveLabels[$routine['objective']] ?? $routine['objective'] ?>
                        </span>
                        <span class="badge badge-outline-white badge-lg me-2">
                            <i class="fas fa-signal me-1"></i>
                            <?= $difficultyLabels[$routine['difficulty_level']] ?? $routine['difficulty_level'] ?>
                        </span>
                        <?php if ($routine['is_template']): ?>
                        <span class="badge badge-gold badge-lg me-2">
                            <i class="fas fa-star me-1"></i>Plantilla
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="routine-quick-stats">
                        <div class="row g-3">
                            <div class="col-auto">
                                <div class="stat-card stat-card-white">
                                    <div class="stat-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number"><?= $routine['duration_weeks'] ?></div>
                                        <div class="stat-label">Semanas</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="stat-card stat-card-white">
                                    <div class="stat-icon">
                                        <i class="fas fa-repeat"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number"><?= $routine['sessions_per_week'] ?></div>
                                        <div class="stat-label">Días/Semana</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="stat-card stat-card-white">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number"><?= $routine['estimated_duration_minutes'] ?></div>
                                        <div class="stat-label">Minutos</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="stat-card stat-card-white">
                                    <div class="stat-icon">
                                        <i class="fas fa-dumbbell"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number"><?= count($exercisesByDay) ?></div>
                                        <div class="stat-label">Días</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="routine-actions text-end">
                        <?php if ($canEdit): ?>
                        <div class="btn-group mb-3">
                            <a href="/routines/edit/<?= $routine['id'] ?>" class="btn btn-white">
                                <i class="fas fa-edit me-2"></i>Editar Rutina
                            </a>
                            <button type="button" class="btn btn-white dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="duplicateRoutine(<?= $routine['id'] ?>)">
                                    <i class="fas fa-copy me-2"></i>Duplicar Rutina
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDeleteRoutine(<?= $routine['id'] ?>)">
                                    <i class="fas fa-trash me-2"></i>Eliminar
                                </a></li>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($progress && ($isMyRoutine || $canEdit)): ?>
                        <div class="progress-card bg-white-10 rounded p-3 mb-3">
                            <h6 class="text-white mb-2">Progreso</h6>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?= $progress['completion_percentage'] ?>%"></div>
                            </div>
                            <div class="progress-stats">
                                <small class="text-white-75">
                                    <?= $progress['completed_exercises'] ?> de <?= $progress['total_exercises'] ?> ejercicios completados
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <!-- Ejercicios por Día -->
                <div class="routine-exercises-section">
                    <div class="section-header mb-4">
                        <h2 class="section-title">Plan de Entrenamiento</h2>
                        <p class="section-subtitle">Ejercicios organizados por día de entrenamiento</p>
                    </div>
                    
                    <?php if (empty($exercisesByDay)): ?>
                    <div class="empty-state text-center py-5">
                        <div class="empty-icon mb-3">
                            <i class="fas fa-dumbbell fa-3x text-muted"></i>
                        </div>
                        <h4>No hay ejercicios configurados</h4>
                        <p class="text-muted">Esta rutina aún no tiene ejercicios asignados.</p>
                        <?php if ($canEdit): ?>
                        <a href="/routines/edit/<?= $routine['id'] ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Agregar Ejercicios
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    
                    <!-- Navegación por Días -->
                    <div class="days-navigation mb-4">
                        <nav class="nav nav-pills nav-justified">
                            <?php foreach ($exercisesByDay as $day => $exercises): ?>
                            <button class="nav-link <?= $day === 1 ? 'active' : '' ?>" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#day-<?= $day ?>" 
                                    type="button">
                                <?= $dayNames[$day] ?>
                                <small class="d-block"><?= count($exercises) ?> ejercicios</small>
                            </button>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                    
                    <!-- Contenido de los Días -->
                    <div class="tab-content">
                        <?php foreach ($exercisesByDay as $day => $exercises): ?>
                        <div class="tab-pane fade <?= $day === 1 ? 'show active' : '' ?>" 
                             id="day-<?= $day ?>">
                             
                            <div class="exercises-list">
                                <?php foreach ($exercises as $index => $exercise): ?>
                                <div class="exercise-card" data-exercise-id="<?= $exercise['exercise_id'] ?>">
                                    <div class="exercise-header">
                                        <div class="exercise-number">
                                            <?= $index + 1 ?>
                                        </div>
                                        <div class="exercise-info">
                                            <h4 class="exercise-name">
                                                <?= htmlspecialchars($exercise['exercise_name']) ?>
                                            </h4>
                                            <?php if (!empty($exercise['category_name'])): ?>
                                            <span class="exercise-category" style="color: <?= $exercise['category_color'] ?>">
                                                <?= htmlspecialchars($exercise['category_name']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($isMyRoutine): ?>
                                        <div class="exercise-actions">
                                            <button class="btn btn-sm btn-success" onclick="logExercise(<?= $routine['id'] ?>, <?= $exercise['exercise_id'] ?>)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="exercise-body">
                                        <?php if (!empty($exercise['exercise_description'])): ?>
                                        <p class="exercise-description">
                                            <?= htmlspecialchars($exercise['exercise_description']) ?>
                                        </p>
                                        <?php endif; ?>
                                        
                                        <div class="exercise-parameters">
                                            <div class="row g-3">
                                                <div class="col-6 col-md-3">
                                                    <div class="parameter-item">
                                                        <div class="parameter-label">Series</div>
                                                        <div class="parameter-value"><?= $exercise['sets'] ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="parameter-item">
                                                        <div class="parameter-label">Repeticiones</div>
                                                        <div class="parameter-value"><?= htmlspecialchars($exercise['reps']) ?></div>
                                                    </div>
                                                </div>
                                                <?php if (!empty($exercise['weight'])): ?>
                                                <div class="col-6 col-md-3">
                                                    <div class="parameter-item">
                                                        <div class="parameter-label">Peso</div>
                                                        <div class="parameter-value"><?= htmlspecialchars($exercise['weight']) ?></div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                                <div class="col-6 col-md-3">
                                                    <div class="parameter-item">
                                                        <div class="parameter-label">Descanso</div>
                                                        <div class="parameter-value"><?= $exercise['rest_seconds'] ?>s</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($exercise['muscle_groups'])): ?>
                                        <div class="muscle-groups mt-3">
                                            <span class="muscle-groups-label">Músculos:</span>
                                            <?php foreach ($exercise['muscle_groups'] as $muscle): ?>
                                            <span class="badge badge-outline-secondary me-1">
                                                <?= htmlspecialchars($muscle) ?>
                                            </span>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($exercise['notes'])): ?>
                                        <div class="exercise-notes mt-3">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <?= htmlspecialchars($exercise['notes']) ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($exercise['exercise_instructions'])): ?>
                                        <div class="exercise-instructions">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#instructions-<?= $exercise['exercise_id'] ?>">
                                                <i class="fas fa-list-ol me-1"></i>Ver Instrucciones
                                            </button>
                                            <div class="collapse mt-2" id="instructions-<?= $exercise['exercise_id'] ?>">
                                                <div class="card card-body">
                                                    <?= nl2br(htmlspecialchars($exercise['exercise_instructions'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Información de la Rutina -->
                <div class="routine-sidebar">
                    <!-- Información del Cliente/Instructor -->
                    <?php if (!empty($routine['client_first_name']) && !$isClient): ?>
                    <div class="info-card mb-4">
                        <h5 class="info-title">Cliente Asignado</h5>
                        <div class="client-info">
                            <div class="client-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="client-details">
                                <div class="client-name">
                                    <?= htmlspecialchars($routine['client_first_name'] . ' ' . $routine['client_last_name']) ?>
                                </div>
                                <div class="client-email text-muted">
                                    <?= htmlspecialchars($routine['client_email']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($routine['instructor_first_name']) && $isClient): ?>
                    <div class="info-card mb-4">
                        <h5 class="info-title">Tu Instructor</h5>
                        <div class="instructor-info">
                            <div class="instructor-avatar">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="instructor-details">
                                <div class="instructor-name">
                                    <?= htmlspecialchars($routine['instructor_first_name'] . ' ' . $routine['instructor_last_name']) ?>
                                </div>
                                <div class="instructor-role text-muted">
                                    Entrenador Personal
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Productos Recomendados -->
                    <?php if (!empty($recommendedProducts)): ?>
                    <div class="info-card mb-4">
                        <h5 class="info-title">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Productos Recomendados
                        </h5>
                        <p class="info-subtitle text-muted mb-3">
                            Suplementos que complementan tu rutina de <?= strtolower($objectiveLabels[$routine['objective']] ?? '') ?>
                        </p>
                        
                        <div class="products-list">
                            <?php foreach ($recommendedProducts as $product): ?>
                            <div class="product-item">
                                <div class="product-image">
                                    <?php if (!empty($product['images'])): ?>
                                        <?php $images = json_decode($product['images'], true); ?>
                                        <img src="<?= \StyleFitness\Helpers\AppHelper::uploadUrl('uploads/images/categories/products/' . $images[0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <?php else: ?>
                                        <div class="product-placeholder">
                                            <i class="fas fa-supplement"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h6 class="product-name">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </h6>
                                    <div class="product-price">
                                        <?php if (!empty($product['sale_price'])): ?>
                                        <span class="sale-price">S/ <?= number_format($product['sale_price'], 2) ?></span>
                                        <span class="original-price">S/ <?= number_format($product['price'], 2) ?></span>
                                        <?php else: ?>
                                        <span class="price">S/ <?= number_format($product['price'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="product-action">
                                    <a href="/products/<?= $product['slug'] ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="/store" class="btn btn-primary btn-sm">
                                Ver Todos los Productos
                                <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Estadísticas Adicionales -->
                    <div class="info-card">
                        <h5 class="info-title">Detalles de la Rutina</h5>
                        <div class="detail-list">
                            <div class="detail-item">
                                <span class="detail-label">Creada:</span>
                                <span class="detail-value"><?= date('d/m/Y', strtotime($routine['created_at'])) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Última actualización:</span>
                                <span class="detail-value"><?= date('d/m/Y', strtotime($routine['updated_at'])) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Tipo:</span>
                                <span class="detail-value">
                                    <?= $routine['is_template'] ? 'Plantilla Pública' : 'Rutina Personalizada' ?>
                                </span>
                            </div>
                            <?php if (isset($routine['views_count'])): ?>
                            <div class="detail-item">
                                <span class="detail-label">Visualizaciones:</span>
                                <span class="detail-value"><?= $routine['views_count'] ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Ejercicio -->
<div class="modal fade" id="logExerciseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Ejercicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="logExerciseForm">
                <div class="modal-body">
                    <input type="hidden" id="log_routine_id" name="routine_id">
                    <input type="hidden" id="log_exercise_id" name="exercise_id">
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Series Completadas</label>
                            <input type="number" class="form-control" name="sets" min="1" max="10" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Repeticiones</label>
                            <input type="text" class="form-control" name="reps" placeholder="ej: 10, 8-12" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Peso Utilizado (opcional)</label>
                            <input type="text" class="form-control" name="weight" placeholder="ej: 20kg, peso corporal">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas (opcional)</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="¿Cómo te sentiste? Observaciones..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function logExercise(routineId, exerciseId) {
    document.getElementById('log_routine_id').value = routineId;
    document.getElementById('log_exercise_id').value = exerciseId;
    new bootstrap.Modal(document.getElementById('logExerciseModal')).show();
}

document.getElementById('logExerciseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/routines/log-workout', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Ejercicio registrado exitosamente');
            bootstrap.Modal.getInstance(document.getElementById('logExerciseModal')).hide();
            
            // Marcar visualmente el ejercicio como completado
            const exerciseCard = document.querySelector(`[data-exercise-id="${formData.get('exercise_id')}"]`);
            if (exerciseCard) {
                exerciseCard.classList.add('exercise-completed');
            }
        } else {
            showAlert('error', data.error || 'Error al registrar el ejercicio');
        }
    })
    .catch(error => {
        showAlert('error', 'Error de conexión');
    });
});

function showAlert(type, message) {
    // Crear y mostrar notificación
    const alert = document.createElement('div');
    alert.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
</script>