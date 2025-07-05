<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Editar Rutina - STYLOFITNESS
 * Formulario para editar una rutina existente
 */

$currentUser = \StyleFitness\Helpers\AppHelper::getCurrentUser();
$isInstructor = ($currentUser['role'] === 'instructor');
$isAdmin = ($currentUser['role'] === 'admin');

// Organizar ejercicios por día para JavaScript
$exercisesByDayJS = [];
foreach ($routineExercises as $exercise) {
    $day = $exercise['day_number'];
    if (!isset($exercisesByDayJS[$day])) {
        $exercisesByDayJS[$day] = [];
    }
    $exercisesByDayJS[$day][] = $exercise;
}
?>

<div class="edit-routine-page">
    <!-- Header -->
    <section class="page-header bg-gradient-primary">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/routines" class="text-white-75">Rutinas</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="/routines/view/<?= $routine['id'] ?>" class="text-white-75">
                                    <?= htmlspecialchars($routine['name']) ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white">Editar</li>
                        </ol>
                    </nav>
                    
                    <h1 class="page-title text-white mb-3">
                        Editar Rutina: <?= htmlspecialchars($routine['name']) ?>
                    </h1>
                    <p class="page-subtitle text-white-75">
                        Modifica los ejercicios y configuración de esta rutina personalizada
                    </p>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="page-actions">
                        <a href="/routines/view/<?= $routine['id'] ?>" class="btn btn-outline-white me-2">
                            <i class="fas fa-eye me-2"></i>Ver Rutina
                        </a>
                        <a href="/routines" class="btn btn-outline-white">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <form id="editRoutineForm" method="POST" action="/routines/update/<?= $routine['id'] ?>" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= \StyleFitness\Helpers\AppHelper::generateCsrfToken() ?>">
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Información Básica -->
                    <div class="form-section mb-5">
                        <div class="section-header mb-4">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle me-2"></i>
                                Información Básica
                            </h3>
                            <p class="section-subtitle">Modifica los aspectos fundamentales de la rutina</p>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label required">Nombre de la Rutina</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?= htmlspecialchars($routine['name']) ?>"
                                       required>
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="4"><?= htmlspecialchars($routine['description']) ?></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="objective" class="form-label required">Objetivo Principal</label>
                                <select class="form-select" id="objective" name="objective" required>
                                    <option value="">Seleccionar objetivo...</option>
                                    <option value="weight_loss" <?= $routine['objective'] === 'weight_loss' ? 'selected' : '' ?>>
                                        Pérdida de Peso
                                    </option>
                                    <option value="muscle_gain" <?= $routine['objective'] === 'muscle_gain' ? 'selected' : '' ?>>
                                        Ganancia Muscular
                                    </option>
                                    <option value="strength" <?= $routine['objective'] === 'strength' ? 'selected' : '' ?>>
                                        Fuerza
                                    </option>
                                    <option value="endurance" <?= $routine['objective'] === 'endurance' ? 'selected' : '' ?>>
                                        Resistencia
                                    </option>
                                    <option value="flexibility" <?= $routine['objective'] === 'flexibility' ? 'selected' : '' ?>>
                                        Flexibilidad
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="difficulty_level" class="form-label required">Nivel de Dificultad</label>
                                <select class="form-select" id="difficulty_level" name="difficulty_level" required>
                                    <option value="">Seleccionar nivel...</option>
                                    <option value="beginner" <?= $routine['difficulty_level'] === 'beginner' ? 'selected' : '' ?>>
                                        Principiante
                                    </option>
                                    <option value="intermediate" <?= $routine['difficulty_level'] === 'intermediate' ? 'selected' : '' ?>>
                                        Intermedio
                                    </option>
                                    <option value="advanced" <?= $routine['difficulty_level'] === 'advanced' ? 'selected' : '' ?>>
                                        Avanzado
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="duration_weeks" class="form-label">Duración (Semanas)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="duration_weeks" 
                                       name="duration_weeks" 
                                       value="<?= $routine['duration_weeks'] ?>"
                                       min="1" 
                                       max="52">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="sessions_per_week" class="form-label">Días por Semana</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="sessions_per_week" 
                                       name="sessions_per_week" 
                                       value="<?= $routine['sessions_per_week'] ?>"
                                       min="1" 
                                       max="7">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="estimated_duration_minutes" class="form-label">Duración por Sesión (min)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="estimated_duration_minutes" 
                                       name="estimated_duration_minutes" 
                                       value="<?= $routine['estimated_duration_minutes'] ?>"
                                       min="15" 
                                       max="180">
                            </div>
                        </div>
                    </div>

                    <!-- Constructor de Ejercicios -->
                    <div class="form-section mb-5">
                        <div class="section-header mb-4">
                            <h3 class="section-title">
                                <i class="fas fa-dumbbell me-2"></i>
                                Ejercicios de la Rutina
                            </h3>
                            <p class="section-subtitle">Modifica, reordena o agrega nuevos ejercicios</p>
                        </div>
                        
                        <!-- Biblioteca de Ejercicios -->
                        <div class="exercise-library mb-4">
                            <div class="library-header">
                                <h5>Biblioteca de Ejercicios</h5>
                                <div class="library-filters">
                                    <select class="form-select form-select-sm" id="categoryFilter">
                                        <option value="">Todas las categorías</option>
                                        <?php foreach ($exerciseCategories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" class="form-control form-control-sm" id="exerciseSearch" placeholder="Buscar ejercicio...">
                                </div>
                            </div>
                            
                            <div class="exercises-grid" id="exerciseLibrary">
                                <?php foreach ($exercises as $exercise): ?>
                                <div class="exercise-item" 
                                     data-exercise-id="<?= $exercise['id'] ?>"
                                     data-category-id="<?= $exercise['category_id'] ?>"
                                     data-name="<?= strtolower($exercise['name']) ?>"
                                     draggable="true">
                                    <div class="exercise-info">
                                        <h6 class="exercise-name"><?= htmlspecialchars($exercise['name']) ?></h6>
                                        <span class="exercise-category"><?= htmlspecialchars($exercise['category_name'] ?? '') ?></span>
                                    </div>
                                    <div class="exercise-difficulty">
                                        <span class="badge badge-<?= $exercise['difficulty_level'] ?>">
                                            <?= ucfirst($exercise['difficulty_level']) ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Días de Entrenamiento -->
                        <div class="routine-builder">
                            <div class="days-tabs">
                                <nav class="nav nav-tabs">
                                    <?php for ($day = 1; $day <= 7; $day++): ?>
                                    <button class="nav-link <?= $day === 1 ? 'active' : '' ?>" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#day-<?= $day ?>" 
                                            type="button">
                                        Día <?= $day ?>
                                        <small class="d-block exercise-count" id="count-day-<?= $day ?>">
                                            <?= count($exercisesByDayJS[$day] ?? []) ?> ejercicios
                                        </small>
                                    </button>
                                    <?php endfor; ?>
                                </nav>
                            </div>
                            
                            <div class="tab-content">
                                <?php for ($day = 1; $day <= 7; $day++): ?>
                                <div class="tab-pane fade <?= $day === 1 ? 'show active' : '' ?>" id="day-<?= $day ?>">
                                    <div class="day-content">
                                        <div class="day-header">
                                            <h5>Día <?= $day ?> - Ejercicios</h5>
                                            <small class="text-muted">Arrastra ejercicios aquí para agregarlos</small>
                                        </div>
                                        
                                        <div class="exercises-dropzone" 
                                             data-day="<?= $day ?>" 
                                             ondrop="dropExercise(event)" 
                                             ondragover="allowDrop(event)">
                                            <div class="dropzone-placeholder" <?= !empty($exercisesByDayJS[$day]) ? 'style="display: none;"' : '' ?>>
                                                <i class="fas fa-plus-circle"></i>
                                                <p>Arrastra ejercicios aquí</p>
                                            </div>
                                            <div class="exercises-list" id="exercises-day-<?= $day ?>">
                                                <?php if (!empty($exercisesByDayJS[$day])): ?>
                                                    <?php foreach ($exercisesByDayJS[$day] as $index => $exercise): ?>
                                                    <div class="routine-exercise-item" 
                                                         data-exercise-id="<?= $exercise['exercise_id'] ?>"
                                                         data-day="<?= $day ?>"
                                                         data-original-id="<?= $exercise['id'] ?>">
                                                        <div class="exercise-handle">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </div>
                                                        <div class="exercise-info">
                                                            <h6 class="exercise-name"><?= htmlspecialchars($exercise['exercise_name']) ?></h6>
                                                            <small class="exercise-category"><?= htmlspecialchars($exercise['category_name'] ?? '') ?></small>
                                                            <div class="exercise-params">
                                                                <span class="param">Series: <span class="sets-value"><?= $exercise['sets'] ?></span></span>
                                                                <span class="param">Reps: <span class="reps-value"><?= htmlspecialchars($exercise['reps']) ?></span></span>
                                                                <span class="param">Descanso: <span class="rest-value"><?= $exercise['rest_seconds'] ?></span>s</span>
                                                            </div>
                                                        </div>
                                                        <div class="exercise-actions">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editExercise(this)">
                                                                <i class="fas fa-cog"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeExercise(this)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Panel Lateral -->
                    <div class="sidebar-sticky">
                        <!-- Asignación de Cliente -->
                        <?php if ($isAdmin): ?>
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h5 class="section-title">Asignación</h5>
                            </div>
                            
                            <?php if (!empty($clients)): ?>
                            <div class="mb-3">
                                <label for="client_id" class="form-label">Asignar a Cliente</label>
                                <select class="form-select" id="client_id" name="client_id">
                                    <option value="">No asignar (plantilla pública)</option>
                                    <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>" <?= $routine['client_id'] == $client['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_template" 
                                       name="is_template" 
                                       value="1" 
                                       <?= $routine['is_template'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_template">
                                    Marcar como plantilla pública
                                </label>
                                <small class="form-text text-muted d-block">
                                    Las plantillas pueden ser vistas por todos los usuarios
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Información Actual -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h5 class="section-title">Información Actual</h5>
                            </div>
                            
                            <div class="info-list">
                                <div class="info-item">
                                    <span class="info-label">Creada:</span>
                                    <span class="info-value"><?= date('d/m/Y', strtotime($routine['created_at'])) ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Última edición:</span>
                                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($routine['updated_at'])) ?></span>
                                </div>
                                <?php if (!empty($routine['client_first_name'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Cliente:</span>
                                    <span class="info-value"><?= htmlspecialchars($routine['client_first_name'] . ' ' . $routine['client_last_name']) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="info-item">
                                    <span class="info-label">Tipo:</span>
                                    <span class="info-value">
                                        <?= $routine['is_template'] ? 'Plantilla Pública' : 'Rutina Personalizada' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resumen de la Rutina -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h5 class="section-title">Resumen</h5>
                            </div>
                            
                            <div class="routine-summary">
                                <div class="summary-item">
                                    <span class="summary-label">Total de ejercicios:</span>
                                    <span class="summary-value" id="totalExercises"><?= count($routineExercises) ?></span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Días con ejercicios:</span>
                                    <span class="summary-value" id="activeDays"><?= count($exercisesByDayJS) ?></span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Duración estimada:</span>
                                    <span class="summary-value" id="estimatedDuration"><?= $routine['estimated_duration_minutes'] ?> min</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary w-100" onclick="previewRoutine()">
                                        <i class="fas fa-eye me-1"></i>Vista Previa
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-info w-100" onclick="duplicateRoutine()">
                                        <i class="fas fa-copy me-1"></i>Duplicar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Configuración de Ejercicio -->
<div class="modal fade" id="exerciseConfigModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurar Ejercicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exerciseConfigForm">
                    <input type="hidden" id="config_exercise_id">
                    <input type="hidden" id="config_day">
                    <input type="hidden" id="config_order">
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Series</label>
                            <input type="number" class="form-control" id="config_sets" value="3" min="1" max="10">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Repeticiones</label>
                            <input type="text" class="form-control" id="config_reps" value="10" placeholder="ej: 8-12">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Peso (opcional)</label>
                            <input type="text" class="form-control" id="config_weight" placeholder="ej: 20kg">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Descanso (seg)</label>
                            <input type="number" class="form-control" id="config_rest" value="60" min="15" max="300">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tempo (opcional)</label>
                            <input type="text" class="form-control" id="config_tempo" placeholder="ej: 2-0-1-0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas para el cliente</label>
                            <textarea class="form-control" id="config_notes" rows="2" placeholder="Instrucciones especiales..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveExerciseConfig()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let exerciseData = <?= json_encode($exercises) ?>;
let routineExercises = {};
let draggedExercise = null;

// Cargar ejercicios existentes
let existingExercises = <?= json_encode($exercisesByDayJS) ?>;

// Inicializar el constructor de rutinas
document.addEventListener('DOMContentLoaded', function() {
    initializeRoutineBuilder();
    loadExistingExercises();
    updateRoutineSummary();
});

function initializeRoutineBuilder() {
    // Configurar drag and drop para biblioteca
    const exerciseItems = document.querySelectorAll('.exercise-item');
    exerciseItems.forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
    });
    
    // Configurar filtros
    document.getElementById('categoryFilter').addEventListener('change', filterExercises);
    document.getElementById('exerciseSearch').addEventListener('input', filterExercises);
    
    // Hacer los ejercicios existentes ordenables
    initializeSortable();
}

function loadExistingExercises() {
    // Cargar ejercicios existentes en routineExercises
    Object.keys(existingExercises).forEach(day => {
        routineExercises[day] = {};
        existingExercises[day].forEach((exercise, index) => {
            routineExercises[day][index] = {
                exercise_id: exercise.exercise_id,
                sets: exercise.sets,
                reps: exercise.reps,
                weight: exercise.weight,
                rest_seconds: exercise.rest_seconds,
                tempo: exercise.tempo,
                notes: exercise.notes
            };
        });
    });
}

function initializeSortable() {
    // Hacer los ejercicios drag and drop dentro del día
    for (let day = 1; day <= 7; day++) {
        const exercisesList = document.getElementById(`exercises-day-${day}`);
        if (exercisesList) {
            // Aquí podrías implementar SortableJS si está disponible
            // new Sortable(exercisesList, { ... });
        }
    }
}

// Reutilizar funciones del crear rutina con modificaciones
function handleDragStart(e) {
    draggedExercise = {
        id: e.target.dataset.exerciseId,
        name: e.target.querySelector('.exercise-name').textContent,
        category: e.target.querySelector('.exercise-category').textContent
    };
    e.dataTransfer.effectAllowed = 'copy';
}

function allowDrop(e) {
    e.preventDefault();
}

function dropExercise(e) {
    e.preventDefault();
    
    if (!draggedExercise) return;
    
    const dropzone = e.currentTarget;
    const day = dropzone.dataset.day;
    const exercisesList = dropzone.querySelector('.exercises-list');
    
    // Verificar si el ejercicio ya existe en este día
    const existingExercise = exercisesList.querySelector(`[data-exercise-id="${draggedExercise.id}"]`);
    if (existingExercise) {
        showAlert('warning', 'Este ejercicio ya está en el día ' + day);
        return;
    }
    
    // Crear elemento del ejercicio
    const exerciseElement = createExerciseElement(draggedExercise, day);
    exercisesList.appendChild(exerciseElement);
    
    // Ocultar placeholder si es necesario
    const placeholder = dropzone.querySelector('.dropzone-placeholder');
    if (placeholder) {
        placeholder.style.display = 'none';
    }
    
    // Actualizar contador
    updateDayCounter(day);
    
    // Configurar el ejercicio
    configureExercise(draggedExercise.id, day, exercisesList.children.length - 1);
    
    updateRoutineSummary();
}

function createExerciseElement(exercise, day) {
    const div = document.createElement('div');
    div.className = 'routine-exercise-item';
    div.dataset.exerciseId = exercise.id;
    div.dataset.day = day;
    
    div.innerHTML = `
        <div class="exercise-handle">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <div class="exercise-info">
            <h6 class="exercise-name">${exercise.name}</h6>
            <small class="exercise-category">${exercise.category}</small>
            <div class="exercise-params">
                <span class="param">Series: <span class="sets-value">3</span></span>
                <span class="param">Reps: <span class="reps-value">10</span></span>
                <span class="param">Descanso: <span class="rest-value">60</span>s</span>
            </div>
        </div>
        <div class="exercise-actions">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editExercise(this)">
                <i class="fas fa-cog"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeExercise(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    return div;
}

function editExercise(button) {
    const exerciseItem = button.closest('.routine-exercise-item');
    const exerciseId = exerciseItem.dataset.exerciseId;
    const day = exerciseItem.dataset.day;
    const order = Array.from(exerciseItem.parentNode.children).indexOf(exerciseItem);
    
    // Cargar valores actuales
    document.getElementById('config_exercise_id').value = exerciseId;
    document.getElementById('config_day').value = day;
    document.getElementById('config_order').value = order;
    
    const exerciseConfig = routineExercises[day] && routineExercises[day][order];
    if (exerciseConfig) {
        document.getElementById('config_sets').value = exerciseConfig.sets || 3;
        document.getElementById('config_reps').value = exerciseConfig.reps || '10';
        document.getElementById('config_weight').value = exerciseConfig.weight || '';
        document.getElementById('config_rest').value = exerciseConfig.rest_seconds || 60;
        document.getElementById('config_tempo').value = exerciseConfig.tempo || '';
        document.getElementById('config_notes').value = exerciseConfig.notes || '';
    }
    
    new bootstrap.Modal(document.getElementById('exerciseConfigModal')).show();
}

function configureExercise(exerciseId, day, order) {
    document.getElementById('config_exercise_id').value = exerciseId;
    document.getElementById('config_day').value = day;
    document.getElementById('config_order').value = order;
    
    // Resetear valores por defecto
    document.getElementById('config_sets').value = 3;
    document.getElementById('config_reps').value = '10';
    document.getElementById('config_weight').value = '';
    document.getElementById('config_rest').value = 60;
    document.getElementById('config_tempo').value = '';
    document.getElementById('config_notes').value = '';
    
    new bootstrap.Modal(document.getElementById('exerciseConfigModal')).show();
}

function saveExerciseConfig() {
    const exerciseId = document.getElementById('config_exercise_id').value;
    const day = document.getElementById('config_day').value;
    const order = document.getElementById('config_order').value;
    
    const config = {
        exercise_id: exerciseId,
        sets: document.getElementById('config_sets').value,
        reps: document.getElementById('config_reps').value,
        weight: document.getElementById('config_weight').value,
        rest_seconds: document.getElementById('config_rest').value,
        tempo: document.getElementById('config_tempo').value,
        notes: document.getElementById('config_notes').value
    };
    
    // Guardar configuración
    if (!routineExercises[day]) {
        routineExercises[day] = {};
    }
    routineExercises[day][order] = config;
    
    // Actualizar interfaz
    const exerciseItem = document.querySelector(`[data-day="${day}"] .routine-exercise-item[data-exercise-id="${exerciseId}"]`);
    if (exerciseItem) {
        exerciseItem.querySelector('.sets-value').textContent = config.sets;
        exerciseItem.querySelector('.reps-value').textContent = config.reps;
        exerciseItem.querySelector('.rest-value').textContent = config.rest_seconds;
    }
    
    bootstrap.Modal.getInstance(document.getElementById('exerciseConfigModal')).hide();
    updateRoutineSummary();
}

function removeExercise(button) {
    const exerciseItem = button.closest('.routine-exercise-item');
    const day = exerciseItem.dataset.day;
    const exercisesList = exerciseItem.parentNode;
    
    exerciseItem.remove();
    
    // Mostrar placeholder si no hay ejercicios
    if (exercisesList.children.length === 0) {
        const dropzone = exercisesList.parentNode;
        const placeholder = dropzone.querySelector('.dropzone-placeholder');
        if (placeholder) {
            placeholder.style.display = 'block';
        }
    }
    
    updateDayCounter(day);
    updateRoutineSummary();
}

function updateDayCounter(day) {
    const exercisesList = document.getElementById(`exercises-day-${day}`);
    const count = exercisesList.children.length;
    const counter = document.getElementById(`count-day-${day}`);
    if (counter) {
        counter.textContent = count + ' ejercicios';
    }
}

function filterExercises() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const searchFilter = document.getElementById('exerciseSearch').value.toLowerCase();
    const exerciseItems = document.querySelectorAll('.exercise-item');
    
    exerciseItems.forEach(item => {
        const matchesCategory = !categoryFilter || item.dataset.categoryId === categoryFilter;
        const matchesSearch = !searchFilter || item.dataset.name.includes(searchFilter);
        
        item.style.display = matchesCategory && matchesSearch ? 'block' : 'none';
    });
}

function updateRoutineSummary() {
    let totalExercises = 0;
    let activeDays = 0;
    
    for (let day = 1; day <= 7; day++) {
        const exercisesList = document.getElementById(`exercises-day-${day}`);
        const dayExercises = exercisesList.children.length;
        
        if (dayExercises > 0) {
            activeDays++;
            totalExercises += dayExercises;
        }
    }
    
    document.getElementById('totalExercises').textContent = totalExercises;
    document.getElementById('activeDays').textContent = activeDays;
    
    // Calcular duración estimada (promedio 3-4 minutos por ejercicio)
    const estimatedMinutes = totalExercises * 3.5;
    document.getElementById('estimatedDuration').textContent = Math.round(estimatedMinutes) + ' min';
}

function previewRoutine() {
    window.open('/routines/view/<?= $routine['id'] ?>', '_blank');
}

function duplicateRoutine() {
    if (confirm('¿Deseas crear una copia de esta rutina?')) {
        fetch('/routines/duplicate/<?= $routine['id'] ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('[name="csrf_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Rutina duplicada exitosamente');
                setTimeout(() => {
                    window.location.href = '/routines/edit/' + data.routine_id;
                }, 1500);
            } else {
                showAlert('error', data.error || 'Error al duplicar la rutina');
            }
        })
        .catch(() => {
            showAlert('error', 'Error de conexión');
        });
    }
}

// Preparar datos para envío del formulario
document.getElementById('editRoutineForm').addEventListener('submit', function(e) {
    // Agregar ejercicios como campos ocultos
    for (let day = 1; day <= 7; day++) {
        const exercisesList = document.getElementById(`exercises-day-${day}`);
        const exercises = exercisesList.querySelectorAll('.routine-exercise-item');
        
        exercises.forEach((exerciseItem, index) => {
            const exerciseId = exerciseItem.dataset.exerciseId;
            const config = routineExercises[day] && routineExercises[day][index];
            
            if (config) {
                const prefix = `exercises[${day}][${index}]`;
                
                // Crear campos ocultos
                const fields = [
                    { name: 'exercise_id', value: exerciseId },
                    { name: 'sets', value: config.sets || 3 },
                    { name: 'reps', value: config.reps || '10' },
                    { name: 'weight', value: config.weight || '' },
                    { name: 'rest_seconds', value: config.rest_seconds || 60 },
                    { name: 'tempo', value: config.tempo || '' },
                    { name: 'notes', value: config.notes || '' }
                ];
                
                fields.forEach(field => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `${prefix}[${field.name}]`;
                    input.value = field.value;
                    this.appendChild(input);
                });
            }
        });
    }
});

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                     type === 'warning' ? 'alert-warning' : 
                     type === 'error' ? 'alert-danger' : 'alert-info';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>