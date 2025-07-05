<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-plus-circle me-2"></i>
                        Crear Plantilla de Rutina
                    </h1>
                    <p class="text-muted mb-0">Diseña una plantilla diferenciada por género y zonas corporales</p>
                </div>
                <div>
                    <a href="/trainer/templates" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver a Plantillas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Creation Form -->
    <form id="templateForm" method="POST" action="/trainer/templates/store">
        <input type="hidden" name="csrf_token" value="<?= \StyleFitness\Helpers\AppHelper::generateCsrfToken() ?>">
        
        <!-- Basic Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Información Básica
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre de la Plantilla *</label>
                                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                           id="name" name="name" value="<?= htmlspecialchars($oldData['name'] ?? '') ?>" 
                                           placeholder="Ej: Rutina Fuerza Masculina" required>
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_gender" class="form-label">Género Objetivo *</label>
                                    <select class="form-select <?= isset($errors['target_gender']) ? 'is-invalid' : '' ?>" 
                                            id="target_gender" name="target_gender" required>
                                        <option value="">Selecciona el género...</option>
                                        <option value="male" <?= ($oldData['target_gender'] ?? '') === 'male' ? 'selected' : '' ?>>Masculino</option>
                                        <option value="female" <?= ($oldData['target_gender'] ?? '') === 'female' ? 'selected' : '' ?>>Femenino</option>
                                        <option value="unisex" <?= ($oldData['target_gender'] ?? '') === 'unisex' ? 'selected' : '' ?>>Unisex</option>
                                    </select>
                                    <?php if (isset($errors['target_gender'])): ?>
                                        <div class="invalid-feedback"><?= $errors['target_gender'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción *</label>
                            <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Describe el propósito y características de esta plantilla..." required><?= htmlspecialchars($oldData['description'] ?? '') ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= $errors['description'] ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="objective" class="form-label">Objetivo *</label>
                                    <select class="form-select <?= isset($errors['objective']) ? 'is-invalid' : '' ?>" 
                                            id="objective" name="objective" required>
                                        <option value="">Selecciona el objetivo...</option>
                                        <option value="muscle_gain" <?= ($oldData['objective'] ?? '') === 'muscle_gain' ? 'selected' : '' ?>>Ganancia Muscular</option>
                                        <option value="weight_loss" <?= ($oldData['objective'] ?? '') === 'weight_loss' ? 'selected' : '' ?>>Pérdida de Peso</option>
                                        <option value="strength" <?= ($oldData['objective'] ?? '') === 'strength' ? 'selected' : '' ?>>Fuerza</option>
                                        <option value="endurance" <?= ($oldData['objective'] ?? '') === 'endurance' ? 'selected' : '' ?>>Resistencia</option>
                                        <option value="toning" <?= ($oldData['objective'] ?? '') === 'toning' ? 'selected' : '' ?>>Tonificación</option>
                                    </select>
                                    <?php if (isset($errors['objective'])): ?>
                                        <div class="invalid-feedback"><?= $errors['objective'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="difficulty_level" class="form-label">Nivel de Dificultad *</label>
                                    <select class="form-select <?= isset($errors['difficulty_level']) ? 'is-invalid' : '' ?>" 
                                            id="difficulty_level" name="difficulty_level" required>
                                        <option value="">Selecciona la dificultad...</option>
                                        <option value="beginner" <?= ($oldData['difficulty_level'] ?? '') === 'beginner' ? 'selected' : '' ?>>Principiante</option>
                                        <option value="intermediate" <?= ($oldData['difficulty_level'] ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermedio</option>
                                        <option value="advanced" <?= ($oldData['difficulty_level'] ?? '') === 'advanced' ? 'selected' : '' ?>>Avanzado</option>
                                    </select>
                                    <?php if (isset($errors['difficulty_level'])): ?>
                                        <div class="invalid-feedback"><?= $errors['difficulty_level'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="duration_weeks" class="form-label">Duración (Semanas)</label>
                                    <input type="number" class="form-control" id="duration_weeks" name="duration_weeks" 
                                           value="<?= htmlspecialchars($oldData['duration_weeks'] ?? '4') ?>" min="1" max="52">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sessions_per_week" class="form-label">Sesiones por Semana</label>
                                    <input type="number" class="form-control" id="sessions_per_week" name="sessions_per_week" 
                                           value="<?= htmlspecialchars($oldData['sessions_per_week'] ?? '3') ?>" min="1" max="7">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_duration_minutes" class="form-label">Duración Estimada (Minutos)</label>
                                    <input type="number" class="form-control" id="estimated_duration_minutes" name="estimated_duration_minutes" 
                                           value="<?= htmlspecialchars($oldData['estimated_duration_minutes'] ?? '60') ?>" min="15" max="180">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="tags" class="form-label">Etiquetas (separadas por comas)</label>
                                    <input type="text" class="form-control" id="tags" name="tags" 
                                           value="<?= htmlspecialchars($oldData['tags'] ?? '') ?>" 
                                           placeholder="Ej: fuerza, hipertrofia, principiante">
                                    <div class="form-text">Ayuda a categorizar y buscar la plantilla</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_public" name="is_public" 
                                               <?= isset($oldData['is_public']) && $oldData['is_public'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_public">
                                            <strong>Plantilla Pública</strong>
                                            <div class="form-text">Otros instructores podrán usar esta plantilla</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Body Zones Configuration -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-dumbbell me-2"></i>
                            Configuración por Zonas Corporales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Instrucciones:</strong> Selecciona las zonas corporales que incluirá tu plantilla y configura los ejercicios para cada zona.
                        </div>
                        
                        <!-- Body Zone Selector -->
                        <div id="bodyZoneSelector" class="mb-4">
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
                                
                                foreach ($bodyZones as $zoneKey => $zone): 
                                ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                        <div class="body-zone-card" data-zone="<?= $zoneKey ?>">
                                            <div class="card h-100 border-2 zone-selector" style="cursor: pointer;">
                                                <div class="card-body text-center">
                                                    <i class="<?= $zone['icon'] ?> fa-2x text-<?= $zone['color'] ?> mb-2"></i>
                                                    <h6 class="card-title mb-0"><?= $zone['name'] ?></h6>
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input zone-checkbox" type="checkbox" 
                                                               id="zone_<?= $zoneKey ?>" name="zones[]" value="<?= $zoneKey ?>">
                                                        <label class="form-check-label" for="zone_<?= $zoneKey ?>">
                                                            Incluir
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Exercise Configuration for Each Zone -->
                        <div id="zoneExerciseConfigs">
                            <?php foreach ($bodyZones as $zoneKey => $zone): ?>
                                <div class="zone-config" id="config_<?= $zoneKey ?>" style="display: none;">
                                    <div class="card border-<?= $zone['color'] ?> mb-4">
                                        <div class="card-header bg-<?= $zone['color'] ?> text-white">
                                            <h6 class="mb-0">
                                                <i class="<?= $zone['icon'] ?> me-2"></i>
                                                Ejercicios para <?= $zone['name'] ?>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Exercise Search and Filter -->
                                            <div class="row mb-3">
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control exercise-search" 
                                                           placeholder="Buscar ejercicios para <?= strtolower($zone['name']) ?>..." 
                                                           data-zone="<?= $zoneKey ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <select class="form-select exercise-filter" data-zone="<?= $zoneKey ?>">
                                                        <option value="">Todos los ejercicios</option>
                                                        <option value="beginner">Principiante</option>
                                                        <option value="intermediate">Intermedio</option>
                                                        <option value="advanced">Avanzado</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <!-- Available Exercises -->
                                            <div class="available-exercises mb-3" data-zone="<?= $zoneKey ?>">
                                                <h6 class="text-muted mb-2">Ejercicios Disponibles:</h6>
                                                <div class="exercise-list" style="max-height: 200px; overflow-y: auto;">
                                                    <!-- Se llenará dinámicamente -->
                                                </div>
                                            </div>
                                            
                                            <!-- Selected Exercises -->
                                            <div class="selected-exercises" data-zone="<?= $zoneKey ?>">
                                                <h6 class="text-success mb-2">Ejercicios Seleccionados:</h6>
                                                <div class="selected-list">
                                                    <div class="text-muted text-center py-3">
                                                        <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                                        <p>Selecciona ejercicios de la lista superior</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="/trainer/templates" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" onclick="previewTemplate()">
                                    <i class="fas fa-eye me-2"></i>
                                    Vista Previa
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>
                                    Crear Plantilla
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Exercise Selection Modal -->
<div class="modal fade" id="exerciseModal" tabindex="-1" aria-labelledby="exerciseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exerciseModalLabel">
                    <i class="fas fa-dumbbell me-2"></i>
                    Configurar Ejercicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exerciseConfigForm">
                    <input type="hidden" id="exerciseId" name="exercise_id">
                    <input type="hidden" id="exerciseZone" name="zone">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="exerciseSets" class="form-label">Series</label>
                            <input type="number" class="form-control" id="exerciseSets" name="sets" value="3" min="1" max="10">
                        </div>
                        <div class="col-md-6">
                            <label for="exerciseReps" class="form-label">Repeticiones</label>
                            <input type="text" class="form-control" id="exerciseReps" name="reps" value="10-12" 
                                   placeholder="Ej: 10-12, 8, 15">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="exerciseWeight" class="form-label">Peso (kg)</label>
                            <input type="text" class="form-control" id="exerciseWeight" name="weight" 
                                   placeholder="Ej: 20, 15-20, Peso corporal">
                        </div>
                        <div class="col-md-6">
                            <label for="exerciseRest" class="form-label">Descanso (segundos)</label>
                            <input type="number" class="form-control" id="exerciseRest" name="rest_seconds" value="60" min="15" max="300">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="exerciseNotes" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" id="exerciseNotes" name="notes" rows="2" 
                                  placeholder="Instrucciones especiales, técnica, etc."></textarea>
                    </div>
                    
                    <!-- Exercise Video Preview -->
                    <div id="exerciseVideoPreview" class="mb-3" style="display: none;">
                        <label class="form-label">Vista Previa del Ejercicio:</label>
                        <div class="ratio ratio-16x9">
                            <video id="exerciseVideo" controls class="rounded">
                                Tu navegador no soporta el elemento de video.
                            </video>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="addExerciseToZone()">
                    <i class="fas fa-plus me-2"></i>
                    Agregar Ejercicio
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.body-zone-card .card {
    transition: all 0.3s ease;
    border: 2px solid #e3e6f0;
}

.body-zone-card .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.body-zone-card .card.selected {
    border-color: #28a745;
    background-color: #f8fff9;
}

.zone-config {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.exercise-item {
    border: 1px solid #e3e6f0;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.exercise-item:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}

.selected-exercise {
    border: 1px solid #28a745;
    background-color: #f8fff9;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
}

.exercise-video-thumb {
    width: 60px;
    height: 40px;
    object-fit: cover;
    border-radius: 0.25rem;
}

.zone-selector {
    cursor: pointer;
}

.zone-checkbox {
    pointer-events: none;
}
</style>

<script>
// Variables globales
let selectedZones = new Set();
let exercisesByZone = {};
let selectedExercisesByZone = {};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeBodyZoneSelector();
    loadExercisesForAllZones();
});

// Inicializar selector de zonas corporales
function initializeBodyZoneSelector() {
    const zoneCards = document.querySelectorAll('.zone-selector');
    
    zoneCards.forEach(card => {
        card.addEventListener('click', function() {
            const checkbox = this.querySelector('.zone-checkbox');
            const zone = this.closest('.body-zone-card').dataset.zone;
            
            checkbox.checked = !checkbox.checked;
            
            if (checkbox.checked) {
                selectedZones.add(zone);
                this.classList.add('selected');
                showZoneConfig(zone);
            } else {
                selectedZones.delete(zone);
                this.classList.remove('selected');
                hideZoneConfig(zone);
            }
        });
    });
}

// Mostrar configuración de zona
function showZoneConfig(zone) {
    const config = document.getElementById(`config_${zone}`);
    if (config) {
        config.style.display = 'block';
        loadExercisesForZone(zone);
    }
}

// Ocultar configuración de zona
function hideZoneConfig(zone) {
    const config = document.getElementById(`config_${zone}`);
    if (config) {
        config.style.display = 'none';
    }
}

// Cargar ejercicios para todas las zonas
function loadExercisesForAllZones() {
    const zones = ['chest', 'back', 'shoulders', 'arms', 'legs', 'glutes', 'core', 'cardio'];
    
    zones.forEach(zone => {
        fetch(`/api/exercises/by-zone?zone=${zone}`)
            .then(response => response.json())
            .then(data => {
                exercisesByZone[zone] = data.exercises || [];
            })
            .catch(error => {
                console.error(`Error loading exercises for ${zone}:`, error);
            });
    });
}

// Cargar ejercicios para una zona específica
function loadExercisesForZone(zone) {
    const exerciseList = document.querySelector(`.available-exercises[data-zone="${zone}"] .exercise-list`);
    
    if (!exercisesByZone[zone]) {
        exerciseList.innerHTML = '<p class="text-muted">Cargando ejercicios...</p>';
        return;
    }
    
    const exercises = exercisesByZone[zone];
    
    if (exercises.length === 0) {
        exerciseList.innerHTML = '<p class="text-muted">No hay ejercicios disponibles para esta zona</p>';
        return;
    }
    
    let html = '';
    exercises.forEach(exercise => {
        html += `
            <div class="exercise-item" onclick="selectExercise('${zone}', ${exercise.id})">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        ${exercise.video_url ? 
                            `<img src="/uploads/exercise-thumbs/${exercise.id}.jpg" class="exercise-video-thumb" 
                                  onerror="this.src='/assets/images/exercise-placeholder.jpg'" alt="${exercise.name}">` :
                            `<div class="exercise-video-thumb bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-dumbbell text-muted"></i>
                            </div>`
                        }
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">${exercise.name}</h6>
                        <p class="mb-0 text-muted small">${exercise.description || 'Sin descripción'}</p>
                        <div class="mt-1">
                            <span class="badge bg-secondary badge-sm">${exercise.difficulty_level}</span>
                            ${exercise.equipment_needed ? 
                                `<span class="badge bg-info badge-sm">${exercise.equipment_needed}</span>` : 
                                ''
                            }
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-plus text-primary"></i>
                    </div>
                </div>
            </div>
        `;
    });
    
    exerciseList.innerHTML = html;
}

// Seleccionar ejercicio
function selectExercise(zone, exerciseId) {
    const exercise = exercisesByZone[zone].find(ex => ex.id == exerciseId);
    if (!exercise) return;
    
    // Configurar modal
    document.getElementById('exerciseId').value = exerciseId;
    document.getElementById('exerciseZone').value = zone;
    document.getElementById('exerciseModalLabel').innerHTML = 
        `<i class="fas fa-dumbbell me-2"></i>Configurar: ${exercise.name}`;
    
    // Mostrar video si está disponible
    if (exercise.video_url) {
        document.getElementById('exerciseVideoPreview').style.display = 'block';
        document.getElementById('exerciseVideo').src = exercise.video_url;
    } else {
        document.getElementById('exerciseVideoPreview').style.display = 'none';
    }
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('exerciseModal'));
    modal.show();
}

// Agregar ejercicio a la zona
function addExerciseToZone() {
    const form = document.getElementById('exerciseConfigForm');
    const formData = new FormData(form);
    
    const exerciseId = formData.get('exercise_id');
    const zone = formData.get('zone');
    
    const exercise = exercisesByZone[zone].find(ex => ex.id == exerciseId);
    if (!exercise) return;
    
    // Crear configuración del ejercicio
    const exerciseConfig = {
        id: exerciseId,
        name: exercise.name,
        sets: formData.get('sets'),
        reps: formData.get('reps'),
        weight: formData.get('weight'),
        rest_seconds: formData.get('rest_seconds'),
        notes: formData.get('notes'),
        video_url: exercise.video_url
    };
    
    // Agregar a la lista de ejercicios seleccionados
    if (!selectedExercisesByZone[zone]) {
        selectedExercisesByZone[zone] = [];
    }
    
    selectedExercisesByZone[zone].push(exerciseConfig);
    
    // Actualizar vista
    updateSelectedExercisesList(zone);
    
    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('exerciseModal'));
    modal.hide();
    
    // Limpiar formulario
    form.reset();
    document.getElementById('exerciseSets').value = '3';
    document.getElementById('exerciseRest').value = '60';
}

// Actualizar lista de ejercicios seleccionados
function updateSelectedExercisesList(zone) {
    const selectedList = document.querySelector(`.selected-exercises[data-zone="${zone}"] .selected-list`);
    const exercises = selectedExercisesByZone[zone] || [];
    
    if (exercises.length === 0) {
        selectedList.innerHTML = `
            <div class="text-muted text-center py-3">
                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                <p>Selecciona ejercicios de la lista superior</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    exercises.forEach((exercise, index) => {
        html += `
            <div class="selected-exercise">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${exercise.name}</h6>
                        <div class="row text-sm">
                            <div class="col-6">
                                <strong>Series:</strong> ${exercise.sets} | 
                                <strong>Reps:</strong> ${exercise.reps}
                            </div>
                            <div class="col-6">
                                <strong>Peso:</strong> ${exercise.weight || 'N/A'} | 
                                <strong>Descanso:</strong> ${exercise.rest_seconds}s
                            </div>
                        </div>
                        ${exercise.notes ? `<p class="mb-0 text-muted small mt-1">${exercise.notes}</p>` : ''}
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="removeExerciseFromZone('${zone}', ${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Hidden inputs para el formulario -->
                <input type="hidden" name="body_zones[${zone}][exercises][${index}][exercise_id]" value="${exercise.id}">
                <input type="hidden" name="body_zones[${zone}][exercises][${index}][sets]" value="${exercise.sets}">
                <input type="hidden" name="body_zones[${zone}][exercises][${index}][reps]" value="${exercise.reps}">
                <input type="hidden" name="body_zones[${zone}][exercises][${index}][weight]" value="${exercise.weight}">
                <input type="hidden" name="body_zones[${zone}][exercises][${index}][rest_seconds]" value="${exercise.rest_seconds}">
                <input type="hidden" name="body_zones[${zone}][exercises][${index}][notes]" value="${exercise.notes}">
            </div>
        `;
    });
    
    selectedList.innerHTML = html;
}

// Remover ejercicio de la zona
function removeExerciseFromZone(zone, index) {
    if (selectedExercisesByZone[zone]) {
        selectedExercisesByZone[zone].splice(index, 1);
        updateSelectedExercisesList(zone);
    }
}

// Vista previa de la plantilla
function previewTemplate() {
    // Validar que se hayan seleccionado zonas
    if (selectedZones.size === 0) {
        alert('Debes seleccionar al menos una zona corporal');
        return;
    }
    
    // Validar que cada zona tenga ejercicios
    let hasExercises = false;
    selectedZones.forEach(zone => {
        if (selectedExercisesByZone[zone] && selectedExercisesByZone[zone].length > 0) {
            hasExercises = true;
        }
    });
    
    if (!hasExercises) {
        alert('Debes agregar al menos un ejercicio a las zonas seleccionadas');
        return;
    }
    
    // Mostrar vista previa (implementar modal o página separada)
    console.log('Vista previa:', {
        zones: Array.from(selectedZones),
        exercises: selectedExercisesByZone
    });
}

// Validación del formulario antes de enviar
document.getElementById('templateForm').addEventListener('submit', function(e) {
    // Validar zonas seleccionadas
    if (selectedZones.size === 0) {
        e.preventDefault();
        alert('Debes seleccionar al menos una zona corporal');
        return;
    }
    
    // Validar ejercicios
    let hasExercises = false;
    selectedZones.forEach(zone => {
        if (selectedExercisesByZone[zone] && selectedExercisesByZone[zone].length > 0) {
            hasExercises = true;
        }
    });
    
    if (!hasExercises) {
        e.preventDefault();
        alert('Debes agregar al menos un ejercicio a las zonas seleccionadas');
        return;
    }
});
</script>