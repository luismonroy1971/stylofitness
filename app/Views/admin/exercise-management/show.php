<?php
use StyleFitness\Helpers\AppHelper;

// Verificar permisos
if (!AppHelper::isLoggedIn() || !AppHelper::hasRole('admin')) {
    AppHelper::redirect('/login');
}

$pageTitle = 'Detalles del Ejercicio';
$exerciseId = $_GET['id'] ?? 0;
$exerciseController = new \StyleFitness\Controllers\ExerciseManagementController();
$exercise = $exerciseController->getExercise($exerciseId);
$usage_stats = $exerciseController->getUsageStats($exerciseId);
$related_exercises = $exerciseController->getRelatedExercises($exerciseId);

if (!$exercise) {
    AppHelper::setFlashMessage('Ejercicio no encontrado', 'error');
    AppHelper::redirect('/admin/exercise-management');
}
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($exercise['name']) ?></h1>
                    <p class="text-muted mb-0">Detalles completos del ejercicio</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/admin/exercise-management" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver a Lista
                    </a>
                    <a href="/admin/exercise-management/edit/<?= $exercise['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        Editar
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="duplicateExercise(<?= $exercise['id'] ?>)">
                                <i class="fas fa-copy me-2"></i>Duplicar
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportExercise(<?= $exercise['id'] ?>)">
                                <i class="fas fa-download me-2"></i>Exportar PDF
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" 
                                   onclick="deleteExercise(<?= $exercise['id'] ?>, '<?= htmlspecialchars($exercise['name']) ?>')">
                                <i class="fas fa-trash me-2"></i>Eliminar
                            </a></li>
                        </ul>
                    </div>
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

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Información Básica
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre del Ejercicio</label>
                            <p class="mb-0"><?= htmlspecialchars($exercise['name']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Categoría</label>
                            <p class="mb-0">
                                <span class="badge bg-secondary fs-6">
                                    <?= htmlspecialchars($exercise['category_name'] ?? 'Sin categoría') ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <?php if (!empty($exercise['description'])): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($exercise['description'])) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nivel de Dificultad</label>
                            <p class="mb-0">
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
                                $color = $difficultyColors[$exercise['difficulty_level']] ?? 'secondary';
                                $label = $difficultyLabels[$exercise['difficulty_level']] ?? $exercise['difficulty_level'];
                                ?>
                                <span class="badge bg-<?= $color ?> fs-6"><?= $label ?></span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Equipamiento Necesario</label>
                            <p class="mb-0">
                                <?php if (!empty($exercise['equipment_needed'])): ?>
                                    <span class="badge bg-info fs-6">
                                        <?= htmlspecialchars($exercise['equipment_needed']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Peso corporal</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if (!empty($exercise['muscle_groups'])): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Grupos Musculares</label>
                            <div>
                                <?php 
                                $muscles = is_string($exercise['muscle_groups']) ? 
                                    json_decode($exercise['muscle_groups'], true) : 
                                    $exercise['muscle_groups'];
                                if (is_array($muscles)):
                                    foreach ($muscles as $muscle):
                                ?>
                                    <span class="badge bg-light text-dark me-2 mb-1"><?= htmlspecialchars($muscle) ?></span>
                                <?php 
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fecha de Creación</label>
                            <p class="mb-0 text-muted">
                                <?= date('d/m/Y H:i', strtotime($exercise['created_at'])) ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Última Actualización</label>
                            <p class="mb-0 text-muted">
                                <?= date('d/m/Y H:i', strtotime($exercise['updated_at'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Instructions -->
            <?php if (!empty($exercise['instructions'])): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list-ol me-2"></i>
                            Instrucciones de Ejecución
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="instructions-content">
                            <?= nl2br(htmlspecialchars($exercise['instructions'])) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Tips -->
            <?php if (!empty($exercise['tips'])): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-lightbulb me-2"></i>
                            Consejos y Precauciones
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="tips-content">
                            <?= nl2br(htmlspecialchars($exercise['tips'])) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Usage Statistics -->
            <?php if (!empty($usage_stats)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-chart-bar me-2"></i>
                            Estadísticas de Uso
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-primary"><?= $usage_stats['total_routines'] ?? 0 ?></div>
                                    <div class="small text-muted">Rutinas que lo incluyen</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-success"><?= $usage_stats['active_clients'] ?? 0 ?></div>
                                    <div class="small text-muted">Clientes activos</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-warning"><?= $usage_stats['total_workouts'] ?? 0 ?></div>
                                    <div class="small text-muted">Entrenamientos registrados</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-info"><?= $usage_stats['avg_rating'] ?? 'N/A' ?></div>
                                    <div class="small text-muted">Calificación promedio</div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($usage_stats['recent_usage'])): ?>
                            <hr>
                            <h6 class="text-muted mb-3">Uso Reciente</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Rutina</th>
                                            <th>Fecha</th>
                                            <th>Sets Completados</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($usage_stats['recent_usage'], 0, 5) as $usage): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($usage['client_name']) ?></td>
                                                <td><?= htmlspecialchars($usage['routine_name']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($usage['workout_date'])) ?></td>
                                                <td>
                                                    <span class="badge bg-success"><?= $usage['sets_completed'] ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Video Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-video me-2"></i>
                        Video del Ejercicio
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($exercise['video_url'])): ?>
                        <div class="video-container mb-3">
                            <div class="ratio ratio-16x9">
                                <video id="exerciseVideo" controls class="rounded">
                                    <source src="<?= htmlspecialchars($exercise['video_url']) ?>" type="video/mp4">
                                    Tu navegador no soporta el elemento de video.
                                </video>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="downloadVideo()">
                                <i class="fas fa-download me-1"></i>
                                Descargar
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="shareVideo()">
                                <i class="fas fa-share me-1"></i>
                                Compartir
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-video-slash fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Sin video disponible</h6>
                            <p class="text-muted small mb-3">Este ejercicio no tiene un video asociado</p>
                            <a href="/admin/exercise-management/edit/<?= $exercise['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Agregar Video
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Image Section -->
            <?php if (!empty($exercise['image_url'])): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-image me-2"></i>
                            Imagen del Ejercicio
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="image-container mb-3">
                            <img src="<?= htmlspecialchars($exercise['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($exercise['name']) ?>" 
                                 class="img-fluid rounded" 
                                 onclick="showImageModal(this.src)">
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="downloadImage()">
                                <i class="fas fa-download me-1"></i>
                                Descargar
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="showImageModal('<?= htmlspecialchars($exercise['image_url']) ?>')">
                                <i class="fas fa-expand me-1"></i>
                                Ver Grande
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/admin/exercise-management/edit/<?= $exercise['id'] ?>" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>
                            Editar Ejercicio
                        </a>
                        <button class="btn btn-outline-secondary" onclick="duplicateExercise(<?= $exercise['id'] ?>)">
                            <i class="fas fa-copy me-2"></i>
                            Duplicar Ejercicio
                        </button>
                        <button class="btn btn-outline-info" onclick="viewRoutines(<?= $exercise['id'] ?>)">
                            <i class="fas fa-list me-2"></i>
                            Ver Rutinas que lo Usan
                        </button>
                        <button class="btn btn-outline-success" onclick="exportExercise(<?= $exercise['id'] ?>)">
                            <i class="fas fa-file-pdf me-2"></i>
                            Exportar a PDF
                        </button>
                        <hr>
                        <button class="btn btn-outline-danger" 
                                onclick="deleteExercise(<?= $exercise['id'] ?>, '<?= htmlspecialchars($exercise['name']) ?>')">
                            <i class="fas fa-trash me-2"></i>
                            Eliminar Ejercicio
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Related Exercises -->
            <?php if (!empty($related_exercises)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-link me-2"></i>
                            Ejercicios Relacionados
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php foreach (array_slice($related_exercises, 0, 5) as $related): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <?php if (!empty($related['video_url'])): ?>
                                        <video class="related-video-thumb" muted>
                                            <source src="<?= htmlspecialchars($related['video_url']) ?>" type="video/mp4">
                                        </video>
                                    <?php else: ?>
                                        <div class="related-video-placeholder">
                                            <i class="fas fa-dumbbell"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="/admin/exercise-management/show/<?= $related['id'] ?>" 
                                           class="text-decoration-none">
                                            <?= htmlspecialchars($related['name']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($related['category_name'] ?? 'Sin categoría') ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="text-center mt-3">
                            <a href="/admin/exercise-management?category=<?= $exercise['category_id'] ?>" 
                               class="btn btn-sm btn-outline-primary">
                                Ver Más de esta Categoría
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para Imagen Grande -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">
                    <i class="fas fa-image me-2"></i>
                    <?= htmlspecialchars($exercise['name']) ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Modal para Rutinas -->
<div class="modal fade" id="routinesModal" tabindex="-1" aria-labelledby="routinesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="routinesModalLabel">
                    <i class="fas fa-list me-2"></i>
                    Rutinas que incluyen este ejercicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="routinesContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.video-container {
    position: relative;
}

.image-container img {
    cursor: pointer;
    transition: transform 0.2s ease;
}

.image-container img:hover {
    transform: scale(1.02);
}

.related-video-thumb {
    width: 50px;
    height: 38px;
    object-fit: cover;
    border-radius: 0.375rem;
}

.related-video-placeholder {
    width: 50px;
    height: 38px;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.instructions-content {
    line-height: 1.6;
    font-size: 0.95rem;
}

.tips-content {
    line-height: 1.6;
    font-size: 0.95rem;
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border-left: 4px solid #28a745;
}

@media (max-width: 768px) {
    .related-video-thumb,
    .related-video-placeholder {
        width: 40px;
        height: 30px;
    }
}
</style>

<script>
// Show image modal
function showImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Download video
function downloadVideo() {
    const video = document.getElementById('exerciseVideo');
    if (video && video.src) {
        const link = document.createElement('a');
        link.href = video.src;
        link.download = `<?= htmlspecialchars($exercise['name']) ?>_video.mp4`;
        link.click();
    }
}

// Download image
function downloadImage() {
    const imageSrc = '<?= htmlspecialchars($exercise['image_url'] ?? '') ?>';
    if (imageSrc) {
        const link = document.createElement('a');
        link.href = imageSrc;
        link.download = `<?= htmlspecialchars($exercise['name']) ?>_image.jpg`;
        link.click();
    }
}

// Share video
function shareVideo() {
    const url = window.location.href;
    if (navigator.share) {
        navigator.share({
            title: '<?= htmlspecialchars($exercise['name']) ?>',
            text: 'Mira este ejercicio',
            url: url
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('URL copiada al portapapeles');
        });
    }
}

// Duplicate exercise
function duplicateExercise(exerciseId) {
    if (confirm('¿Quieres crear una copia de este ejercicio?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/exercise-management/duplicate/${exerciseId}`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete exercise
function deleteExercise(exerciseId, exerciseName) {
    if (confirm(`¿Estás seguro de que quieres eliminar el ejercicio "${exerciseName}"?\n\nEsta acción no se puede deshacer y afectará todas las rutinas que lo incluyen.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/exercise-management/delete/${exerciseId}`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Export exercise to PDF
function exportExercise(exerciseId) {
    window.open(`/admin/exercise-management/export-pdf/${exerciseId}`, '_blank');
}

// View routines that use this exercise
function viewRoutines(exerciseId) {
    const modal = new bootstrap.Modal(document.getElementById('routinesModal'));
    modal.show();
    
    // Load routines via AJAX
    fetch(`/api/exercise-routines/${exerciseId}`)
        .then(response => response.json())
        .then(data => {
            let content = '';
            
            if (data.routines && data.routines.length > 0) {
                content = `
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rutina</th>
                                    <th>Entrenador</th>
                                    <th>Clientes Asignados</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.routines.forEach(routine => {
                    content += `
                        <tr>
                            <td>
                                <strong>${routine.name}</strong><br>
                                <small class="text-muted">${routine.description || 'Sin descripción'}</small>
                            </td>
                            <td>${routine.trainer_name}</td>
                            <td>
                                <span class="badge bg-info">${routine.client_count} cliente${routine.client_count !== 1 ? 's' : ''}</span>
                            </td>
                            <td>
                                <span class="badge bg-${routine.is_active ? 'success' : 'secondary'}">
                                    ${routine.is_active ? 'Activa' : 'Inactiva'}
                                </span>
                            </td>
                            <td>
                                <a href="/routines/show/${routine.id}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                
                content += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                content = `
                    <div class="text-center py-4">
                        <i class="fas fa-list fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No hay rutinas que incluyan este ejercicio</h6>
                        <p class="text-muted">Este ejercicio aún no ha sido agregado a ninguna rutina.</p>
                    </div>
                `;
            }
            
            document.getElementById('routinesContent').innerHTML = content;
        })
        .catch(error => {
            console.error('Error loading routines:', error);
            document.getElementById('routinesContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar las rutinas. Por favor, intenta de nuevo.
                </div>
            `;
        });
}
</script>