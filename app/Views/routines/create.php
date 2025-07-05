<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Crear Rutina - STYLOFITNESS
 * Formulario para crear una nueva rutina personalizada
 */

$currentUser = \StyleFitness\Helpers\AppHelper::getCurrentUser();
$isInstructor = ($currentUser['role'] === 'instructor');
$isAdmin = ($currentUser['role'] === 'admin');

// Obtener datos de sesión si hay errores
$errors = $_SESSION['routine_errors'] ?? [];
$oldData = $_SESSION['routine_data'] ?? [];
unset($_SESSION['routine_errors'], $_SESSION['routine_data']);
?>

<div class="create-routine-page">
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
                            <li class="breadcrumb-item active text-white">Crear Nueva</li>
                        </ol>
                    </nav>
                    
                    <h1 class="page-title text-white mb-3">Crear Nueva Rutina</h1>
                    <p class="page-subtitle text-white-75">
                        Diseña una rutina personalizada adaptada a las necesidades específicas de tu cliente
                    </p>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="page-actions">
                        <a href="/routines" class="btn btn-outline-white">
                            <i class="fas fa-arrow-left me-2"></i>Volver a Rutinas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <form id="createRoutineForm" method="POST" action="/routines/store" class="needs-validation" novalidate>
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
                            <p class="section-subtitle">Define los aspectos fundamentales de la rutina</p>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label required">Nombre de la Rutina</label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                       id="name" 
                                       name="name" 
                                       value="<?= htmlspecialchars($oldData['name'] ?? '') ?>"
                                       placeholder="ej: Rutina de Fuerza Intermedio"
                                       required>
                                <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Describe los objetivos y características principales de esta rutina..."><?= htmlspecialchars($oldData['description'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="objective" class="form-label required">Objetivo Principal</label>
                                <select class="form-select <?= isset($errors['objective']) ? 'is-invalid' : '' ?>" 
                                        id="objective" 
                                        name="objective" 
                                        required>
                                    <option value="">Seleccionar objetivo...</option>
                                    <option value="weight_loss" <?= ($oldData['objective'] ?? '') === 'weight_loss' ? 'selected' : '' ?>>
                                        Pérdida de Peso
                                    </option>
                                    <option value="muscle_gain" <?= ($oldData['objective'] ?? '') === 'muscle_gain' ? 'selected' : '' ?>>
                                        Ganancia Muscular
                                    </option>
                                    <option value="strength" <?= ($oldData['objective'] ?? '') === 'strength' ? 'selected' : '' ?>>
                                        Fuerza
                                    </option>
                                    <option value="endurance" <?= ($oldData['objective'] ?? '') === 'endurance' ? 'selected' : '' ?>>
                                        Resistencia
                                    </option>
                                    <option value="flexibility" <?= ($oldData['objective'] ?? '') === 'flexibility' ? 'selected' : '' ?>>
                                        Flexibilidad
                                    </option>
                                </select>
                                <?php if (isset($errors['objective'])): ?>
                                <div class="invalid-feedback"><?= $errors['objective'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="difficulty_level" class="form-label required">Nivel de Dificultad</label>
                                <select class="form-select <?= isset($errors['difficulty_level']) ? 'is-invalid' : '' ?>" 
                                        id="difficulty_level" 
                                        name="difficulty_level" 
                                        required>
                                    <option value="">Seleccionar nivel...</option>
                                    <option value="beginner" <?= ($oldData['difficulty_level'] ?? '') === 'beginner' ? 'selected' : '' ?>>
                                        Principiante
                                    </option>
                                    <option value="intermediate" <?= ($oldData['difficulty_level'] ?? '') === 'intermediate' ? 'selected' : '' ?>>
                                        Intermedio
                                    </option>
                                    <option value="advanced" <?= ($oldData['difficulty_level'] ?? '') === 'advanced' ? 'selected' : '' ?>>
                                        Avanzado
                                    </option>
                                </select>
                                <?php if (isset($errors['difficulty_level'])): ?>
                                <div class="invalid-feedback"><?= $errors['difficulty_level'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="duration_weeks" class="form-label">Duración (Semanas)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="duration_weeks" 
                                       name="duration_weeks" 
                                       value="<?= $oldData['duration_weeks'] ?? 8 ?>"
                                       min="1" 
                                       max="52">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="sessions_per_week" class="form-label">Días por Semana</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="sessions_per_week" 
                                       name="sessions_per_week" 
                                       value="<?= $oldData['sessions_per_week'] ?? 3 ?>"
                                       min="1" 
                                       max="7">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="estimated_duration_minutes" class="form-label">Duración por Sesión (min)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="estimated_duration_minutes" 
                                       name="estimated_duration_minutes" 
                                       value="<?= $oldData['estimated_duration_minutes'] ?? 60 ?>"
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
                                Constructor de Rutina
                            </h3>
                            <p class="section-subtitle">Arrastra y organiza los ejercicios por días de entrenamiento</p>
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
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#day-1" type="button">
                                        Día 1
                                    </button>
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#day-2" type="button">
                                        Día 2
                                    </button>
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#day-3" type="button">
                                        Día 3
                                    </button>
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#day-4" type="button">
                                        Día 4
                                    </button>
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#day-5" type="button">
                                        Día 5
                                    </button>
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#day-6" type="button">
                                        Día 6
                                    </button>
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#day-7" type="button">
                                        Día 7
                                    </button>
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
                                            <div class="dropzone-placeholder">
                                                <i class="fas fa-plus-circle"></i>
                                                <p>Arrastra ejercicios aquí</p>
                                            </div>
                                            <div class="exercises-list" id="exercises-day-<?= $day ?>">
                                                <!-- Los ejercicios se agregarán aquí dinámicamente -->
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
                        <?php if ($isAdmin || $isInstructor): ?>
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
                                    <option value="<?= $client['id'] ?>">
                                        <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_template" name="is_template" value="1">
                                <label class="form-check-label" for="is_template">
                                    Marcar como plantilla pública
                                </label>
                                <small class="form-text text-muted d-block">
                                    Las plantillas pueden ser vistas por todos los usuarios
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Rutinas Predefinidas -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h5 class="section-title">Plantillas Rápidas</h5>
                                <small class="text-muted">Carga una plantilla predefinida como base</small>
                            </div>
                            
                            <div class="templates-list">
                                <button type="button" class="template-btn" onclick="loadTemplate('fullbody_beginner')">
                                    <div class="template-info">
                                        <h6>FullBody Principiante</h6>
                                        <small>3 días • Ganancia muscular</small>
                                    </div>
                                </button>
                                
                                <button type="button" class="template-btn" onclick="loadTemplate('torso_piernas')">
                                    <div class="template-info">
                                        <h6>Torso/Piernas Intermedio</h6>
                                        <small>4 días • Hipertrofia</small>
                                    </div>
                                </button>
                                
                                <button type="button" class="template-btn" onclick="loadTemplate('weight_loss')">
                                    <div class="template-info">
                                        <h6>Pérdida de Peso</h6>
                                        <small>5 días • Definición</small>
                                    </div>
                                </button>
                                
                                <button type="button" class="template-btn" onclick="loadTemplate('strength')">
                                    <div class="template-info">
                                        <h6>Fuerza Avanzada</h6>
                                        <small>4 días • Fuerza máxima</small>
                                    </div>
                                </button>
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
                                    <span class="summary-value" id="totalExercises">0</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Días con ejercicios:</span>
                                    <span class="summary-value" id="activeDays">0</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Duración estimada:</span>
                                    <span class="summary-value" id="estimatedDuration">0 min</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-save me-2"></i>Crear Rutina
                            </button>
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="previewRoutine()">
                                <i class="fas fa-eye me-2"></i>Vista Previa
                            </button>
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

// Inicializar el constructor de rutinas
document.addEventListener('DOMContentLoaded', function() {
    initializeRoutineBuilder();
    updateRoutineSummary();
});

function initializeRoutineBuilder() {
    // Configurar drag and drop
    const exerciseItems = document.querySelectorAll('.exercise-item');
    exerciseItems.forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
    });
    
    // Configurar filtros
    document.getElementById('categoryFilter').addEventListener('change', filterExercises);
    document.getElementById('exerciseSearch').addEventListener('input', filterExercises);
}

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
    
    updateRoutineSummary();
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

function loadTemplate(templateType) {
    // Aquí cargarías los ejercicios predefinidos según el template
    showAlert('info', 'Función de plantillas en desarrollo');
}

function previewRoutine() {
    // Generar vista previa de la rutina
    showAlert('info', 'Vista previa en desarrollo');
}

// Preparar datos para envío del formulario
document.getElementById('createRoutineForm').addEventListener('submit', function(e) {
    // Agregar ejercicios como campos ocultos
    const formData = new FormData();
    
    // Agregar campos del formulario
    const formInputs = this.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        if (input.name && input.value) {
            formData.append(input.name, input.value);
        }
    });
    
    // Agregar ejercicios de cada día
    for (let day = 1; day <= 7; day++) {
        const exercisesList = document.getElementById(`exercises-day-${day}`);
        const exercises = exercisesList.querySelectorAll('.routine-exercise-item');
        
        exercises.forEach((exerciseItem, index) => {
            const exerciseId = exerciseItem.dataset.exerciseId;
            const config = routineExercises[day] && routineExercises[day][index];
            
            if (config) {
                const prefix = `exercises[${day}][${index}]`;
                formData.append(`${prefix}[exercise_id]`, exerciseId);
                formData.append(`${prefix}[sets]`, config.sets || 3);
                formData.append(`${prefix}[reps]`, config.reps || '10');
                formData.append(`${prefix}[weight]`, config.weight || '');
                formData.append(`${prefix}[rest_seconds]`, config.rest_seconds || 60);
                formData.append(`${prefix}[tempo]`, config.tempo || '');
                formData.append(`${prefix}[notes]`, config.notes || '');
            }
        });
    }
    
    // Verificar que tenga al menos un ejercicio
    if (Object.keys(routineExercises).length === 0) {
        e.preventDefault();
        showAlert('warning', 'Debes agregar al menos un ejercicio a la rutina');
        return false;
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