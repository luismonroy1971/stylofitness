<?php
use StyleFitness\Helpers\AppHelper;
?>
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <h1 class="h3 mb-0 text-gray-800 me-3">
                            <?= htmlspecialchars($template['name']) ?>
                        </h1>
                        
                        <!-- Status Badges -->
                        <div class="d-flex gap-2">
                            <?php 
                            $genderColors = [
                                'male' => 'primary',
                                'female' => 'danger', 
                                'unisex' => 'success'
                            ];
                            $genderLabels = [
                                'male' => 'Masculino',
                                'female' => 'Femenino',
                                'unisex' => 'Unisex'
                            ];
                            $genderColor = $genderColors[$template['target_gender']] ?? 'secondary';
                            $genderLabel = $genderLabels[$template['target_gender']] ?? 'N/A';
                            ?>
                            <span class="badge bg-<?= $genderColor ?>">
                                <i class="fas fa-venus-mars me-1"></i>
                                <?= $genderLabel ?>
                            </span>
                            
                            <?php 
                            $difficultyColors = [
                                'beginner' => 'success',
                                'intermediate' => 'warning',
                                'advanced' => 'danger'
                            ];
                            $difficultyLabels = [
                                'beginner' => 'Principiante',
                                'intermediate' => 'Intermedio',
                                'advanced' => 'Avanzado'
                            ];
                            $difficultyColor = $difficultyColors[$template['difficulty_level']] ?? 'secondary';
                            $difficultyLabel = $difficultyLabels[$template['difficulty_level']] ?? 'N/A';
                            ?>
                            <span class="badge bg-<?= $difficultyColor ?>">
                                <i class="fas fa-signal me-1"></i>
                                <?= $difficultyLabel ?>
                            </span>
                            
                            <?php if ($template['is_public']): ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-globe me-1"></i>Pública
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-lock me-1"></i>Privada
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <p class="text-muted mb-0"><?= htmlspecialchars($template['description']) ?></p>
                    
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>
                            Creado por: <strong><?= htmlspecialchars($template['instructor_name'] ?? 'Instructor') ?></strong>
                            <span class="mx-2">•</span>
                            <i class="fas fa-calendar me-1"></i>
                            <?= date('d/m/Y', strtotime($template['created_at'])) ?>
                        </small>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="/trainer/templates" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver
                    </a>
                    
                    <?php if ($template['instructor_id'] == \StyleFitness\Helpers\AppHelper::getCurrentUser()['id'] || \StyleFitness\Helpers\AppHelper::getCurrentUser()['role'] === 'admin'): ?>
                        <a href="/trainer/templates/edit/<?= $template['id'] ?>" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>
                            Editar
                        </a>
                    <?php endif; ?>
                    
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog me-2"></i>
                            Acciones
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" onclick="showAssignModal()">
                                    <i class="fas fa-user-plus me-2"></i>Asignar a Cliente
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/trainer/templates/duplicate/<?= $template['id'] ?>">
                                    <i class="fas fa-copy me-2"></i>Duplicar Plantilla
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="exportTemplate()">
                                    <i class="fas fa-download me-2"></i>Exportar PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="border-end h-100 d-flex flex-column justify-content-center">
                                <h4 class="text-primary mb-1"><?= $template['duration_weeks'] ?></h4>
                                <small class="text-muted">Semanas</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border-end h-100 d-flex flex-column justify-content-center">
                                <h4 class="text-success mb-1"><?= $template['sessions_per_week'] ?></h4>
                                <small class="text-muted">Sesiones/Semana</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border-end h-100 d-flex flex-column justify-content-center">
                                <h4 class="text-info mb-1"><?= $template['estimated_duration_minutes'] ?>'</h4>
                                <small class="text-muted">Duración</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end h-100 d-flex flex-column justify-content-center">
                                <?php 
                                $objectiveLabels = [
                                    'muscle_gain' => 'Ganancia Muscular',
                                    'weight_loss' => 'Pérdida de Peso',
                                    'strength' => 'Fuerza',
                                    'endurance' => 'Resistencia',
                                    'toning' => 'Tonificación'
                                ];
                                $objectiveLabel = $objectiveLabels[$template['objective']] ?? $template['objective'];
                                ?>
                                <h6 class="text-warning mb-1"><?= $objectiveLabel ?></h6>
                                <small class="text-muted">Objetivo</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex flex-column justify-content-center">
                                <h4 class="text-secondary mb-1"><?= $usageStats['total_uses'] ?? 0 ?></h4>
                                <small class="text-muted">Veces Usada</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Body Zones and Exercises -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-dumbbell me-2"></i>
                        Ejercicios por Zonas Corporales
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($templateExercises)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-dumbbell fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay ejercicios configurados</h5>
                            <p class="text-muted">Esta plantilla aún no tiene ejercicios asignados</p>
                            <?php if ($template['instructor_id'] == \StyleFitness\Helpers\AppHelper::getCurrentUser()['id'] || \StyleFitness\Helpers\AppHelper::getCurrentUser()['role'] === 'admin'): ?>
                                <a href="/trainer/templates/edit/<?= $template['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Agregar Ejercicios
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Tabs for Body Zones -->
                        <ul class="nav nav-tabs" id="bodyZoneTabs" role="tablist">
                            <?php 
                            $bodyZoneNames = [
                                'chest' => 'Pecho',
                                'back' => 'Espalda',
                                'shoulders' => 'Hombros',
                                'arms' => 'Brazos',
                                'legs' => 'Piernas',
                                'glutes' => 'Glúteos',
                                'core' => 'Core/Abdomen',
                                'cardio' => 'Cardio'
                            ];
                            
                            $bodyZoneIcons = [
                                'chest' => 'fas fa-heart',
                                'back' => 'fas fa-shield-alt',
                                'shoulders' => 'fas fa-expand-arrows-alt',
                                'arms' => 'fas fa-fist-raised',
                                'legs' => 'fas fa-running',
                                'glutes' => 'fas fa-circle',
                                'core' => 'fas fa-circle-notch',
                                'cardio' => 'fas fa-heartbeat'
                            ];
                            
                            $zones = array_keys($templateExercises);
                            $firstZone = true;
                            
                            foreach ($zones as $zone): 
                                $zoneName = $bodyZoneNames[$zone] ?? ucfirst($zone);
                                $zoneIcon = $bodyZoneIcons[$zone] ?? 'fas fa-dumbbell';
                            ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?= $firstZone ? 'active' : '' ?>" 
                                            id="<?= $zone ?>-tab" data-bs-toggle="tab" 
                                            data-bs-target="#<?= $zone ?>-pane" type="button" 
                                            role="tab" aria-controls="<?= $zone ?>-pane" 
                                            aria-selected="<?= $firstZone ? 'true' : 'false' ?>">
                                        <i class="<?= $zoneIcon ?> me-2"></i>
                                        <?= $zoneName ?>
                                        <span class="badge bg-secondary ms-2"><?= count($templateExercises[$zone]) ?></span>
                                    </button>
                                </li>
                            <?php 
                                $firstZone = false;
                            endforeach; 
                            ?>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="bodyZoneTabContent">
                            <?php 
                            $firstZone = true;
                            foreach ($zones as $zone): 
                                $zoneName = $bodyZoneNames[$zone] ?? ucfirst($zone);
                                $exercises = $templateExercises[$zone];
                            ?>
                                <div class="tab-pane fade <?= $firstZone ? 'show active' : '' ?>" 
                                     id="<?= $zone ?>-pane" role="tabpanel" 
                                     aria-labelledby="<?= $zone ?>-tab" tabindex="0">
                                    <div class="p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 text-muted">
                                                <i class="<?= $bodyZoneIcons[$zone] ?? 'fas fa-dumbbell' ?> me-2"></i>
                                                Ejercicios para <?= $zoneName ?> (<?= count($exercises) ?>)
                                            </h6>
                                            <div class="text-muted small">
                                                Total estimado: <?= array_sum(array_column($exercises, 'estimated_time')) ?> min
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <?php foreach ($exercises as $index => $exercise): ?>
                                                <div class="col-lg-6 mb-3">
                                                    <div class="card exercise-card h-100">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-start">
                                                                <!-- Exercise Video/Image -->
                                                                <div class="flex-shrink-0 me-3">
                                                                    <?php if (!empty($exercise['video_url'])): ?>
                                                                        <div class="exercise-video-container" 
                                                                             onclick="playExerciseVideo('<?= htmlspecialchars($exercise['video_url']) ?>', '<?= htmlspecialchars($exercise['name']) ?>')">
                                                                            <video class="exercise-video-thumb" muted>
                                                                                <source src="<?= htmlspecialchars($exercise['video_url']) ?>" type="video/mp4">
                                                                            </video>
                                                                            <div class="video-overlay">
                                                                                <i class="fas fa-play-circle fa-2x text-white"></i>
                                                                            </div>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="exercise-placeholder">
                                                                            <i class="fas fa-dumbbell fa-2x text-muted"></i>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                                
                                                                <!-- Exercise Details -->
                                                                <div class="flex-grow-1">
                                                                    <h6 class="card-title mb-2">
                                                                        <?= htmlspecialchars($exercise['name']) ?>
                                                                        <span class="badge bg-light text-dark ms-2">#<?= $index + 1 ?></span>
                                                                    </h6>
                                                                    
                                                                    <!-- Exercise Configuration -->
                                                                    <div class="exercise-config mb-2">
                                                                        <div class="row text-sm">
                                                                            <div class="col-6">
                                                                                <strong class="text-primary">Series:</strong> <?= $exercise['sets'] ?? 'N/A' ?>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <strong class="text-success">Reps:</strong> <?= $exercise['reps'] ?? 'N/A' ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row text-sm mt-1">
                                                                            <div class="col-6">
                                                                                <strong class="text-warning">Peso:</strong> <?= $exercise['weight'] ?? 'Peso corporal' ?>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <strong class="text-info">Descanso:</strong> <?= $exercise['rest_seconds'] ?? 60 ?>s
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Exercise Tags -->
                                                                    <div class="mb-2">
                                                                        <span class="badge bg-secondary badge-sm"><?= $exercise['difficulty_level'] ?? 'N/A' ?></span>
                                                                        <?php if (!empty($exercise['equipment_needed'])): ?>
                                                                            <span class="badge bg-info badge-sm"><?= $exercise['equipment_needed'] ?></span>
                                                                        <?php endif; ?>
                                                                        <?php if (!empty($exercise['muscle_groups'])): ?>
                                                                            <?php 
                                                                            $muscleGroups = is_string($exercise['muscle_groups']) ? 
                                                                                json_decode($exercise['muscle_groups'], true) : 
                                                                                $exercise['muscle_groups'];
                                                                            if (is_array($muscleGroups)):
                                                                                foreach (array_slice($muscleGroups, 0, 2) as $muscle):
                                                                            ?>
                                                                                <span class="badge bg-light text-dark badge-sm"><?= $muscle ?></span>
                                                                            <?php 
                                                                                endforeach;
                                                                            endif;
                                                                            ?>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    
                                                                    <!-- Exercise Notes -->
                                                                    <?php if (!empty($exercise['notes'])): ?>
                                                                        <p class="text-muted small mb-0">
                                                                            <i class="fas fa-sticky-note me-1"></i>
                                                                            <?= htmlspecialchars($exercise['notes']) ?>
                                                                        </p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                $firstZone = false;
                            endforeach; 
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Statistics -->
    <?php if (!empty($usageStats) && $usageStats['total_uses'] > 0): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Estadísticas de Uso
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <h3 class="text-primary"><?= $usageStats['total_uses'] ?></h3>
                                    <p class="text-muted mb-0">Veces Asignada</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <h3 class="text-success"><?= $usageStats['unique_clients'] ?></h3>
                                    <p class="text-muted mb-0">Clientes Únicos</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <h3 class="text-info"><?= number_format($usageStats['active_rate'] * 100, 1) ?>%</h3>
                                    <p class="text-muted mb-0">Tasa de Actividad</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal para Asignar a Cliente -->
<div class="modal fade" id="assignTemplateModal" tabindex="-1" aria-labelledby="assignTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignTemplateModalLabel">
                    <i class="fas fa-user-plus me-2"></i>
                    Asignar Plantilla a Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignTemplateForm">
                    <input type="hidden" name="template_id" value="<?= $template['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="clientSelect" class="form-label">Seleccionar Cliente</label>
                        <select class="form-select" id="clientSelect" name="client_id" required>
                            <option value="">Selecciona un cliente...</option>
                            <?php if (!empty($clients)): ?>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>">
                                        <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="customName" class="form-label">Nombre de la Rutina (Opcional)</label>
                        <input type="text" class="form-control" id="customName" name="custom_name" 
                               placeholder="<?= htmlspecialchars($template['name']) ?> - Personalizada">
                    </div>
                    
                    <div class="mb-3">
                        <label for="customNotes" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" id="customNotes" name="custom_notes" rows="3" 
                                  placeholder="Instrucciones especiales para este cliente..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notifyClient" name="notify_client" checked>
                        <label class="form-check-label" for="notifyClient">
                            Notificar al cliente sobre la nueva rutina
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="assignTemplate()">
                    <i class="fas fa-check me-2"></i>
                    Asignar Rutina
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Video de Ejercicio -->
<div class="modal fade" id="exerciseVideoModal" tabindex="-1" aria-labelledby="exerciseVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exerciseVideoModalLabel">
                    <i class="fas fa-play-circle me-2"></i>
                    Video del Ejercicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <video id="exerciseVideoPlayer" controls class="rounded">
                        Tu navegador no soporta el elemento de video.
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.exercise-card {
    border: 1px solid #e3e6f0;
    transition: all 0.2s ease;
}

.exercise-card:hover {
    border-color: #007bff;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 123, 255, 0.1);
}

.exercise-video-thumb {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 0.375rem;
    cursor: pointer;
}

.exercise-video-container {
    position: relative;
    cursor: pointer;
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.375rem;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.exercise-video-container:hover .video-overlay {
    opacity: 1;
}

.exercise-placeholder {
    width: 80px;
    height: 60px;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.exercise-config {
    background: #f8f9fa;
    border-radius: 0.375rem;
    padding: 0.5rem;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.stat-item {
    padding: 1rem;
    border-right: 1px solid #e3e6f0;
}

.stat-item:last-child {
    border-right: none;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    background: none;
    border-bottom-color: #007bff;
    color: #007bff;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: #007bff;
    color: #007bff;
}

@media (max-width: 768px) {
    .exercise-video-thumb {
        width: 60px;
        height: 45px;
    }
    
    .exercise-placeholder {
        width: 60px;
        height: 45px;
    }
    
    .stat-item {
        border-right: none;
        border-bottom: 1px solid #e3e6f0;
        margin-bottom: 1rem;
    }
    
    .stat-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
}
</style>

<script>
// Mostrar modal de asignación
function showAssignModal() {
    // Cargar lista de clientes si está vacía
    const clientSelect = document.getElementById('clientSelect');
    if (clientSelect.children.length <= 1) {
        loadClientsList();
    }
    
    const modal = new bootstrap.Modal(document.getElementById('assignTemplateModal'));
    modal.show();
}

// Cargar lista de clientes
function loadClientsList() {
    fetch('/api/instructor/clients')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('clientSelect');
            
            if (data.success && data.clients) {
                data.clients.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = `${client.first_name} ${client.last_name}`;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading clients:', error);
        });
}

// Asignar plantilla a cliente
function assignTemplate() {
    const form = document.getElementById('assignTemplateForm');
    const formData = new FormData(form);
    
    if (!formData.get('client_id')) {
        alert('Por favor selecciona un cliente');
        return;
    }
    
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Asignando...';
    submitBtn.disabled = true;
    
    fetch('/trainer/templates/assign', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rutina asignada exitosamente');
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('assignTemplateModal'));
            modal.hide();
            
            form.reset();
        } else {
            alert(data.error || 'Error al asignar la rutina');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al asignar la rutina');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Reproducir video de ejercicio
function playExerciseVideo(videoUrl, exerciseName) {
    document.getElementById('exerciseVideoModalLabel').innerHTML = 
        `<i class="fas fa-play-circle me-2"></i>${exerciseName}`;
    document.getElementById('exerciseVideoPlayer').src = videoUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('exerciseVideoModal'));
    modal.show();
}

// Exportar plantilla a PDF
function exportTemplate() {
    window.open(`/trainer/templates/export/<?= $template['id'] ?>`, '_blank');
}

// Limpiar video cuando se cierra el modal
document.getElementById('exerciseVideoModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('exerciseVideoPlayer').src = '';
});
</script>