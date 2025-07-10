<?php
use StyleFitness\Helpers\AppHelper;
?>
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Editar Plantilla de Rutina</h1>
                    <p class="text-muted mb-0">Modifica la información y ejercicios de la plantilla</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/trainer/templates/show/<?= $template['id'] ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver
                    </a>
                    <a href="/trainer/templates" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>
                        Ver Todas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form id="editTemplateForm" method="POST" action="/trainer/templates/update/<?= $template['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= \StyleFitness\Helpers\AppHelper::generateCsrfToken() ?>">
        
        <!-- Basic Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Información Básica
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre de la Plantilla *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($template['name']) ?>" required>
                                <div class="form-text">Nombre descriptivo para identificar la plantilla</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="target_gender" class="form-label">Género Objetivo *</label>
                                <select class="form-select" id="target_gender" name="target_gender" required>
                                    <option value="male" <?= $template['target_gender'] === 'male' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="female" <?= $template['target_gender'] === 'female' ? 'selected' : '' ?>>Femenino</option>
                                    <option value="unisex" <?= $template['target_gender'] === 'unisex' ? 'selected' : '' ?>>Unisex</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                          placeholder="Describe el propósito y características de esta plantilla..."><?= htmlspecialchars($template['description']) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="objective" class="form-label">Objetivo *</label>
                                <select class="form-select" id="objective" name="objective" required>
                                    <option value="muscle_gain" <?= $template['objective'] === 'muscle_gain' ? 'selected' : '' ?>>Ganancia Muscular</option>
                                    <option value="weight_loss" <?= $template['objective'] === 'weight_loss' ? 'selected' : '' ?>>Pérdida de Peso</option>
                                    <option value="strength" <?= $template['objective'] === 'strength' ? 'selected' : '' ?>>Fuerza</option>
                                    <option value="endurance" <?= $template['objective'] === 'endurance' ? 'selected' : '' ?>>Resistencia</option>
                                    <option value="toning" <?= $template['objective'] === 'toning' ? 'selected' : '' ?>>Tonificación</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="difficulty_level" class="form-label">Dificultad *</label>
                                <select class="form-select" id="difficulty_level" name="difficulty_level" required>
                                    <option value="beginner" <?= $template['difficulty_level'] === 'beginner' ? 'selected' : '' ?>>Principiante</option>
                                    <option value="intermediate" <?= $template['difficulty_level'] === 'intermediate' ? 'selected' : '' ?>>Intermedio</option>
                                    <option value="advanced" <?= $template['difficulty_level'] === 'advanced' ? 'selected' : '' ?>>Avanzado</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="duration_weeks" class="form-label">Duración (semanas) *</label>
                                <input type="number" class="form-control" id="duration_weeks" name="duration_weeks" 
                                       value="<?= $template['duration_weeks'] ?>" min="1" max="52" required>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="sessions_per_week" class="form-label">Sesiones/Semana *</label>
                                <input type="number" class="form-control" id="sessions_per_week" name="sessions_per_week" 
                                       value="<?= $template['sessions_per_week'] ?>" min="1" max="7" required>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="estimated_duration_minutes" class="form-label">Duración (min) *</label>
                                <input type="number" class="form-control" id="estimated_duration_minutes" name="estimated_duration_minutes" 
                                       value="<?= $template['estimated_duration_minutes'] ?>" min="15" max="180" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public" 
                                           <?= $template['is_public'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_public">
                                        Plantilla pública
                                    </label>
                                    <div class="form-text">Permitir que otros instructores vean y usen esta plantilla</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Body Zone Selector -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-crosshairs me-2"></i>
                            Zonas Corporales
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Selecciona las zonas corporales que incluirá esta plantilla</p>
                        
                        <div class="row">
                            <?php 
                            $bodyZones = [
                                'chest' => ['name' => 'Pecho', 'icon' => 'fas fa-heart', 'color' => 'danger'],
                                'back' => ['name' => 'Espalda', 'icon' => 'fas fa-shield-alt', 'color' => 'primary'],
                                'shoulders' => ['name' => 'Hombros', 'icon' => 'fas fa-expand-arrows-alt', 'color' => 'warning'],
                                'arms' => ['name' => 'Brazos', 'icon' => 'fas fa-fist-raised', 'color' => 'info'],
                                'legs' => ['name' => 'Piernas', 'icon' => 'fas fa-running', 'color' => 'success'],
                                'glutes' => ['name' => 'Glúteos', 'icon' => 'fas fa-circle', 'color' => 'secondary'],
                                'core' => ['name' => 'Core/Abdomen', 'icon' => 'fas fa-circle-notch', 'color' => 'dark'],
                                'cardio' => ['name' => 'Cardio', 'icon' => 'fas fa-heartbeat', 'color' => 'danger']
                            ];
                            
                            $selectedZones = !empty($templateExercises) ? array_keys($templateExercises) : [];
                            
                            foreach ($bodyZones as $zoneKey => $zone):
                                $isSelected = in_array($zoneKey, $selectedZones);
                            ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input body-zone-checkbox" type="checkbox" 
                                               id="zone_<?= $zoneKey ?>" name="body_zones[]" value="<?= $zoneKey ?>"
                                               <?= $isSelected ? 'checked' : '' ?>
                                               onchange="toggleBodyZone('<?= $zoneKey ?>')">
                                        <label class="form-check-label w-100" for="zone_<?= $zoneKey ?>">
                                            <div class="body-zone-card <?= $isSelected ? 'selected' : '' ?>" data-zone="<?= $zoneKey ?>">
                                                <div class="text-center">
                                                    <i class="<?= $zone['icon'] ?> fa-2x text-<?= $zone['color'] ?> mb-2"></i>
                                                    <h6 class="mb-0"><?= $zone['name'] ?></h6>
                                                    <small class="text-muted exercise-count" id="count_<?= $zoneKey ?>">
                                                        <?= isset($templateExercises[$zoneKey]) ? count($templateExercises[$zoneKey]) : 0 ?> ejercicios
                                                    </small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exercise Configuration by Body Zone -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-dumbbell me-2"></i>
                            Configuración de Ejercicios
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="exerciseConfiguration">
                            <?php foreach ($bodyZones as $zoneKey => $zone): 
                                $isSelected = in_array($zoneKey, $selectedZones);
                                $zoneExercises = $templateExercises[$zoneKey] ?? [];
                            ?>
                                <div class="zone-config" id="config_<?= $zoneKey ?>" style="<?= $isSelected ? '' : 'display: none;' ?>">
                                    <div class="zone-header p-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="<?= $zone['icon'] ?> text-<?= $zone['color'] ?> me-2"></i>
                                                Ejercicios para <?= $zone['name'] ?>
                                            </h6>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="showExerciseLibrary('<?= $zoneKey ?>')">
                                                <i class="fas fa-plus me-1"></i>
                                                Agregar Ejercicio
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="zone-exercises p-3" id="exercises_<?= $zoneKey ?>">
                                        <?php if (empty($zoneExercises)): ?>
                                            <div class="text-center py-4 text-muted empty-state" id="empty_<?= $zoneKey ?>">
                                                <i class="fas fa-dumbbell fa-2x mb-3"></i>
                                                <p class="mb-0">No hay ejercicios configurados para esta zona</p>
                                                <small>Haz clic en "Agregar Ejercicio" para comenzar</small>
                                            </div>
                                        <?php else: ?>
                                            <div class="exercise-list" id="list_<?= $zoneKey ?>">
                                                <?php foreach ($zoneExercises as $index => $exercise): ?>
                                                    <div class="exercise-item mb-3" data-exercise-id="<?= $exercise['exercise_id'] ?>" data-zone="<?= $zoneKey ?>">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-start">
                                                                    <div class="flex-shrink-0 me-3">
                                                                        <?php if (!empty($exercise['video_url'])): ?>
                                                                            <video class="exercise-thumb" muted>
                                                                                <source src="<?= htmlspecialchars($exercise['video_url']) ?>" type="video/mp4">
                                                                            </video>
                                                                        <?php else: ?>
                                                                            <div class="exercise-placeholder">
                                                                                <i class="fas fa-dumbbell text-muted"></i>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-2"><?= htmlspecialchars($exercise['name']) ?></h6>
                                                                        
                                                                        <div class="row">
                                                                            <div class="col-md-2">
                                                                                <label class="form-label small">Series</label>
                                                                                <input type="number" class="form-control form-control-sm" 
                                                                                       name="exercises[<?= $zoneKey ?>][<?= $exercise['exercise_id'] ?>][sets]" 
                                                                                       value="<?= $exercise['sets'] ?? 3 ?>" min="1" max="10">
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <label class="form-label small">Reps</label>
                                                                                <input type="text" class="form-control form-control-sm" 
                                                                                       name="exercises[<?= $zoneKey ?>][<?= $exercise['exercise_id'] ?>][reps]" 
                                                                                       value="<?= $exercise['reps'] ?? '12' ?>" 
                                                                                       placeholder="12 o 8-12">
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <label class="form-label small">Peso</label>
                                                                                <input type="text" class="form-control form-control-sm" 
                                                                                       name="exercises[<?= $zoneKey ?>][<?= $exercise['exercise_id'] ?>][weight]" 
                                                                                       value="<?= $exercise['weight'] ?? '' ?>" 
                                                                                       placeholder="kg o %">
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <label class="form-label small">Descanso (s)</label>
                                                                                <input type="number" class="form-control form-control-sm" 
                                                                                       name="exercises[<?= $zoneKey ?>][<?= $exercise['exercise_id'] ?>][rest_seconds]" 
                                                                                       value="<?= $exercise['rest_seconds'] ?? 60 ?>" min="15" max="300">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label class="form-label small">Notas</label>
                                                                                <input type="text" class="form-control form-control-sm" 
                                                                                       name="exercises[<?= $zoneKey ?>][<?= $exercise['exercise_id'] ?>][notes]" 
                                                                                       value="<?= htmlspecialchars($exercise['notes'] ?? '') ?>" 
                                                                                       placeholder="Instrucciones especiales">
                                                                            </div>
                                                                            <div class="col-md-1">
                                                                                <label class="form-label small">&nbsp;</label>
                                                                                <button type="button" class="btn btn-sm btn-outline-danger d-block" 
                                                                                        onclick="removeExercise(this, '<?= $zoneKey ?>', <?= $exercise['exercise_id'] ?>)">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <input type="hidden" name="exercises[<?= $zoneKey ?>][<?= $exercise['exercise_id'] ?>][exercise_id]" value="<?= $exercise['exercise_id'] ?>">
                                                                        <input type="hidden" name="exercises[<?= $zoneKey ?>][<?= $exercise['exercise_id'] ?>][body_zone]" value="<?= $zoneKey ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="/trainer/templates/show/<?= $template['id'] ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancelar
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="previewTemplate()">
                            <i class="fas fa-eye me-2"></i>
                            Vista Previa
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal para Biblioteca de Ejercicios -->
<div class="modal fade" id="exerciseLibraryModal" tabindex="-1" aria-labelledby="exerciseLibraryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exerciseLibraryModalLabel">
                    <i class="fas fa-search me-2"></i>
                    Biblioteca de Ejercicios - <span id="currentZoneName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search and Filters -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="exerciseSearch" 
                               placeholder="Buscar ejercicios..." onkeyup="filterExercises()">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="categoryFilter" onchange="filterExercises()">
                            <option value="">Todas las categorías</option>
                            <?php if (!empty($exerciseCategories)): ?>
                                <?php foreach ($exerciseCategories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="difficultyFilter" onchange="filterExercises()">
                            <option value="">Todas las dificultades</option>
                            <option value="beginner">Principiante</option>
                            <option value="intermediate">Intermedio</option>
                            <option value="advanced">Avanzado</option>
                        </select>
                    </div>
                </div>
                
                <!-- Exercise Grid -->
                <div id="exerciseGrid" class="row">
                    <!-- Exercises will be loaded here -->
                </div>
                
                <div id="exerciseLoading" class="text-center py-4" style="display: none;">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="text-muted mt-2">Cargando ejercicios...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
.body-zone-card {
    border: 2px solid #e3e6f0;
    border-radius: 0.5rem;
    padding: 1rem;
    transition: all 0.2s ease;
    cursor: pointer;
    background: #fff;
}

.body-zone-card:hover {
    border-color: #007bff;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 123, 255, 0.1);
}

.body-zone-card.selected {
    border-color: #007bff;
    background: #f8f9ff;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 123, 255, 0.15);
}

.zone-config {
    border-bottom: 1px solid #e3e6f0;
}

.zone-config:last-child {
    border-bottom: none;
}

.zone-header {
    background: #f8f9fa;
}

.exercise-thumb {
    width: 60px;
    height: 45px;
    object-fit: cover;
    border-radius: 0.375rem;
}

.exercise-placeholder {
    width: 60px;
    height: 45px;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.exercise-item {
    border-left: 4px solid #007bff;
    transition: all 0.2s ease;
}

.exercise-item:hover {
    border-left-color: #0056b3;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.exercise-card {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.exercise-card:hover {
    border-color: #007bff;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 123, 255, 0.1);
}

.exercise-card.selected {
    border-color: #28a745;
    background: #f8fff9;
}

.empty-state {
    background: #f8f9fa;
    border-radius: 0.5rem;
}

@media (max-width: 768px) {
    .exercise-thumb {
        width: 50px;
        height: 38px;
    }
    
    .exercise-placeholder {
        width: 50px;
        height: 38px;
    }
}
</style>

<script>
let currentBodyZone = '';
let availableExercises = [];

// Toggle body zone selection
function toggleBodyZone(zone) {
    const checkbox = document.getElementById(`zone_${zone}`);
    const config = document.getElementById(`config_${zone}`);
    const card = document.querySelector(`[data-zone="${zone}"]`);
    
    if (checkbox.checked) {
        config.style.display = 'block';
        card.classList.add('selected');
    } else {
        config.style.display = 'none';
        card.classList.remove('selected');
        
        // Remove all exercises from this zone
        const exerciseList = document.getElementById(`list_${zone}`);
        if (exerciseList) {
            exerciseList.innerHTML = '';
        }
        
        // Show empty state
        const emptyState = document.getElementById(`empty_${zone}`);
        if (emptyState) {
            emptyState.style.display = 'block';
        }
        
        updateExerciseCount(zone, 0);
    }
}

// Show exercise library modal
function showExerciseLibrary(zone) {
    currentBodyZone = zone;
    
    const zoneNames = {
        'chest': 'Pecho',
        'back': 'Espalda',
        'shoulders': 'Hombros',
        'arms': 'Brazos',
        'legs': 'Piernas',
        'glutes': 'Glúteos',
        'core': 'Core/Abdomen',
        'cardio': 'Cardio'
    };
    
    document.getElementById('currentZoneName').textContent = zoneNames[zone] || zone;
    
    const modal = new bootstrap.Modal(document.getElementById('exerciseLibraryModal'));
    modal.show();
    
    loadExercisesByZone(zone);
}

// Load exercises by body zone
function loadExercisesByZone(zone) {
    document.getElementById('exerciseLoading').style.display = 'block';
    document.getElementById('exerciseGrid').innerHTML = '';
    
    fetch(`/api/exercises/by-zone/${zone}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                availableExercises = data.exercises;
                displayExercises(data.exercises);
            } else {
                console.error('Error loading exercises:', data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            document.getElementById('exerciseLoading').style.display = 'none';
        });
}

// Display exercises in grid
function displayExercises(exercises) {
    const grid = document.getElementById('exerciseGrid');
    
    if (exercises.length === 0) {
        grid.innerHTML = `
            <div class="col-12 text-center py-4">
                <i class="fas fa-search fa-2x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron ejercicios</h5>
                <p class="text-muted">Intenta con otros filtros de búsqueda</p>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = exercises.map(exercise => `
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card exercise-card h-100" onclick="selectExercise(${exercise.id})">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            ${exercise.video_url ? 
                                `<video class="exercise-thumb" muted>
                                    <source src="${exercise.video_url}" type="video/mp4">
                                </video>` :
                                `<div class="exercise-placeholder">
                                    <i class="fas fa-dumbbell text-muted"></i>
                                </div>`
                            }
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-2">${exercise.name}</h6>
                            <div class="mb-2">
                                <span class="badge bg-secondary badge-sm">${exercise.difficulty_level}</span>
                                ${exercise.equipment_needed ? 
                                    `<span class="badge bg-info badge-sm">${exercise.equipment_needed}</span>` : 
                                    ''
                                }
                            </div>
                            <p class="card-text small text-muted">${exercise.description || 'Sin descripción'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Filter exercises
function filterExercises() {
    const searchTerm = document.getElementById('exerciseSearch').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const difficultyFilter = document.getElementById('difficultyFilter').value;
    
    let filtered = availableExercises.filter(exercise => {
        const matchesSearch = exercise.name.toLowerCase().includes(searchTerm) ||
                            (exercise.description && exercise.description.toLowerCase().includes(searchTerm));
        const matchesCategory = !categoryFilter || exercise.category_id == categoryFilter;
        const matchesDifficulty = !difficultyFilter || exercise.difficulty_level === difficultyFilter;
        
        return matchesSearch && matchesCategory && matchesDifficulty;
    });
    
    displayExercises(filtered);
}

// Select exercise and add to zone
function selectExercise(exerciseId) {
    const exercise = availableExercises.find(ex => ex.id === exerciseId);
    if (!exercise) return;
    
    // Check if exercise is already added
    const existingExercise = document.querySelector(`[data-exercise-id="${exerciseId}"][data-zone="${currentBodyZone}"]`);
    if (existingExercise) {
        alert('Este ejercicio ya está agregado a esta zona');
        return;
    }
    
    addExerciseToZone(exercise, currentBodyZone);
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('exerciseLibraryModal'));
    modal.hide();
}

// Add exercise to zone
function addExerciseToZone(exercise, zone) {
    const exerciseList = document.getElementById(`list_${zone}`);
    const emptyState = document.getElementById(`empty_${zone}`);
    
    // Hide empty state
    if (emptyState) {
        emptyState.style.display = 'none';
    }
    
    // Create exercise list if it doesn't exist
    if (!exerciseList) {
        const exercisesContainer = document.getElementById(`exercises_${zone}`);
        const newList = document.createElement('div');
        newList.className = 'exercise-list';
        newList.id = `list_${zone}`;
        exercisesContainer.appendChild(newList);
    }
    
    // Add exercise HTML
    const exerciseHtml = `
        <div class="exercise-item mb-3" data-exercise-id="${exercise.id}" data-zone="${zone}">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            ${exercise.video_url ? 
                                `<video class="exercise-thumb" muted>
                                    <source src="${exercise.video_url}" type="video/mp4">
                                </video>` :
                                `<div class="exercise-placeholder">
                                    <i class="fas fa-dumbbell text-muted"></i>
                                </div>`
                            }
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-2">${exercise.name}</h6>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="form-label small">Series</label>
                                    <input type="number" class="form-control form-control-sm" 
                                           name="exercises[${zone}][${exercise.id}][sets]" 
                                           value="3" min="1" max="10">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Reps</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="exercises[${zone}][${exercise.id}][reps]" 
                                           value="12" placeholder="12 o 8-12">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Peso</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="exercises[${zone}][${exercise.id}][weight]" 
                                           placeholder="kg o %">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Descanso (s)</label>
                                    <input type="number" class="form-control form-control-sm" 
                                           name="exercises[${zone}][${exercise.id}][rest_seconds]" 
                                           value="60" min="15" max="300">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Notas</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="exercises[${zone}][${exercise.id}][notes]" 
                                           placeholder="Instrucciones especiales">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label small">&nbsp;</label>
                                    <button type="button" class="btn btn-sm btn-outline-danger d-block" 
                                            onclick="removeExercise(this, '${zone}', ${exercise.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="exercises[${zone}][${exercise.id}][exercise_id]" value="${exercise.id}">
                            <input type="hidden" name="exercises[${zone}][${exercise.id}][body_zone]" value="${zone}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById(`list_${zone}`).insertAdjacentHTML('beforeend', exerciseHtml);
    
    // Update exercise count
    const currentCount = document.querySelectorAll(`[data-zone="${zone}"]`).length;
    updateExerciseCount(zone, currentCount);
}

// Remove exercise from zone
function removeExercise(button, zone, exerciseId) {
    if (confirm('¿Estás seguro de que quieres eliminar este ejercicio?')) {
        const exerciseItem = button.closest('.exercise-item');
        exerciseItem.remove();
        
        // Update exercise count
        const currentCount = document.querySelectorAll(`[data-zone="${zone}"]`).length;
        updateExerciseCount(zone, currentCount);
        
        // Show empty state if no exercises left
        if (currentCount === 0) {
            const emptyState = document.getElementById(`empty_${zone}`);
            if (emptyState) {
                emptyState.style.display = 'block';
            }
        }
    }
}

// Update exercise count
function updateExerciseCount(zone, count) {
    const countElement = document.getElementById(`count_${zone}`);
    if (countElement) {
        countElement.textContent = `${count} ejercicio${count !== 1 ? 's' : ''}`;
    }
}

// Preview template
function previewTemplate() {
    // This could open a modal or redirect to a preview page
    alert('Función de vista previa en desarrollo');
}

// Form validation before submit
document.getElementById('editTemplateForm').addEventListener('submit', function(e) {
    const selectedZones = document.querySelectorAll('.body-zone-checkbox:checked');
    
    if (selectedZones.length === 0) {
        e.preventDefault();
        alert('Debes seleccionar al menos una zona corporal');
        return;
    }
    
    // Check if selected zones have exercises
    let hasExercises = false;
    selectedZones.forEach(checkbox => {
        const zone = checkbox.value;
        const exercises = document.querySelectorAll(`[data-zone="${zone}"]`);
        if (exercises.length > 0) {
            hasExercises = true;
        }
    });
    
    if (!hasExercises) {
        const confirmSave = confirm('No has agregado ejercicios a ninguna zona. ¿Quieres guardar la plantilla sin ejercicios?');
        if (!confirmSave) {
            e.preventDefault();
        }
    }
});
</script>