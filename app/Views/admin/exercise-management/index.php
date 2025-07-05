<?php
use StyleFitness\Helpers\AppHelper;

// Verificar permisos
if (!AppHelper::isLoggedIn() || !AppHelper::hasRole('admin')) {
    AppHelper::redirect('/login');
}

$pageTitle = 'Gestión de Ejercicios';
$exerciseController = new \StyleFitness\Controllers\ExerciseManagementController();
$stats = $exerciseController->getGeneralStats();
$categories = $exerciseController->getCategories();
$exercises = $exerciseController->getExercises($_GET);
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Gestión de Ejercicios</h1>
                    <p class="text-muted mb-0">Administra el catálogo completo de ejercicios y videos</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/admin" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Panel Admin
                    </a>
                    <a href="/admin/exercise-management/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nuevo Ejercicio
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Ejercicios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total_exercises'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Con Videos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['exercises_with_videos'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-video fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Categorías
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total_categories'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Más Usados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['most_used_exercises'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="/admin/exercise-management" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Buscar Ejercicios</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                                   placeholder="Nombre, descripción, músculos...">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="category" class="form-label">Categoría</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Todas</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" 
                                                <?= ($_GET['category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="difficulty" class="form-label">Dificultad</label>
                            <select class="form-select" id="difficulty" name="difficulty">
                                <option value="">Todas</option>
                                <option value="beginner" <?= ($_GET['difficulty'] ?? '') === 'beginner' ? 'selected' : '' ?>>Principiante</option>
                                <option value="intermediate" <?= ($_GET['difficulty'] ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermedio</option>
                                <option value="advanced" <?= ($_GET['difficulty'] ?? '') === 'advanced' ? 'selected' : '' ?>>Avanzado</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="has_video" class="form-label">Video</label>
                            <select class="form-select" id="has_video" name="has_video">
                                <option value="">Todos</option>
                                <option value="1" <?= ($_GET['has_video'] ?? '') === '1' ? 'selected' : '' ?>>Con Video</option>
                                <option value="0" <?= ($_GET['has_video'] ?? '') === '0' ? 'selected' : '' ?>>Sin Video</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="/admin/exercise-management" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Exercises Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>
                            Lista de Ejercicios
                        </h6>
                        <div class="d-flex gap-2">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-download me-1"></i>
                                    Exportar
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="exportExercises('csv')">
                                        <i class="fas fa-file-csv me-2"></i>CSV
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportExercises('pdf')">
                                        <i class="fas fa-file-pdf me-2"></i>PDF
                                    </a></li>
                                </ul>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()" 
                                    id="bulkDeleteBtn" style="display: none;">
                                <i class="fas fa-trash me-1"></i>
                                Eliminar Seleccionados
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($exercises)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-dumbbell fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay ejercicios registrados</h5>
                            <p class="text-muted">Comienza agregando ejercicios al catálogo</p>
                            <a href="/admin/exercise-management/create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Crear Primer Ejercicio
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="exercisesTable">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        </th>
                                        <th width="80">Video</th>
                                        <th>Ejercicio</th>
                                        <th>Categoría</th>
                                        <th>Dificultad</th>
                                        <th>Músculos</th>
                                        <th>Equipamiento</th>
                                        <th width="120">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($exercises as $exercise): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="exercise-checkbox" 
                                                       value="<?= $exercise['id'] ?>" onchange="updateBulkActions()">
                                            </td>
                                            <td>
                                                <?php if (!empty($exercise['video_url'])): ?>
                                                    <div class="video-thumbnail" 
                                                         onclick="playVideo('<?= htmlspecialchars($exercise['video_url']) ?>', '<?= htmlspecialchars($exercise['name']) ?>')">
                                                        <video class="video-thumb" muted>
                                                            <source src="<?= htmlspecialchars($exercise['video_url']) ?>" type="video/mp4">
                                                        </video>
                                                        <div class="video-overlay">
                                                            <i class="fas fa-play text-white"></i>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="video-placeholder">
                                                        <i class="fas fa-video-slash text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($exercise['name']) ?></strong>
                                                    <?php if (!empty($exercise['description'])): ?>
                                                        <br><small class="text-muted">
                                                            <?= htmlspecialchars(substr($exercise['description'], 0, 100)) ?>
                                                            <?= strlen($exercise['description']) > 100 ? '...' : '' ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($exercise['category_name'] ?? 'Sin categoría') ?>
                                                </span>
                                            </td>
                                            <td>
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
                                                <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                if (!empty($exercise['muscle_groups'])):
                                                    $muscles = is_string($exercise['muscle_groups']) ? 
                                                        json_decode($exercise['muscle_groups'], true) : 
                                                        $exercise['muscle_groups'];
                                                    if (is_array($muscles)):
                                                        foreach (array_slice($muscles, 0, 3) as $muscle):
                                                ?>
                                                    <span class="badge bg-light text-dark me-1"><?= htmlspecialchars($muscle) ?></span>
                                                <?php 
                                                        endforeach;
                                                        if (count($muscles) > 3):
                                                ?>
                                                    <span class="badge bg-info">+<?= count($muscles) - 3 ?></span>
                                                <?php 
                                                        endif;
                                                    endif;
                                                endif;
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($exercise['equipment_needed'])): ?>
                                                    <span class="badge bg-info">
                                                        <?= htmlspecialchars($exercise['equipment_needed']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted small">Peso corporal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="/admin/exercise-management/show/<?= $exercise['id'] ?>" 
                                                       class="btn btn-sm btn-outline-info" title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="/admin/exercise-management/edit/<?= $exercise['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteExercise(<?= $exercise['id'] ?>, '<?= htmlspecialchars($exercise['name']) ?>')" 
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if (!empty($pagination)): ?>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Mostrando <?= $pagination['start'] ?> a <?= $pagination['end'] ?> de <?= $pagination['total'] ?> ejercicios
                                </div>
                                <nav aria-label="Paginación de ejercicios">
                                    <ul class="pagination mb-0">
                                        <?php if ($pagination['current_page'] > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?><?= $pagination['query_string'] ?>">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?><?= $pagination['query_string'] ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?><?= $pagination['query_string'] ?>">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Video -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalLabel">
                    <i class="fas fa-play-circle me-2"></i>
                    Video del Ejercicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <video id="videoPlayer" controls class="rounded">
                        Tu navegador no soporta el elemento de video.
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.video-thumb {
    width: 60px;
    height: 45px;
    object-fit: cover;
    border-radius: 0.375rem;
    cursor: pointer;
}

.video-thumbnail {
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

.video-thumbnail:hover .video-overlay {
    opacity: 1;
}

.video-placeholder {
    width: 60px;
    height: 45px;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table th {
    background-color: #f8f9fc;
    border-color: #e3e6f0;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    border-color: #e3e6f0;
}

@media (max-width: 768px) {
    .video-thumb {
        width: 50px;
        height: 38px;
    }
    
    .video-placeholder {
        width: 50px;
        height: 38px;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
}
</style>

<script>
// Reproducir video
function playVideo(videoUrl, exerciseName) {
    document.getElementById('videoModalLabel').innerHTML = 
        `<i class="fas fa-play-circle me-2"></i>${exerciseName}`;
    document.getElementById('videoPlayer').src = videoUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('videoModal'));
    modal.show();
}

// Seleccionar todos los ejercicios
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.exercise-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

// Actualizar acciones en lote
function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.exercise-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (checkedBoxes.length > 0) {
        bulkDeleteBtn.style.display = 'inline-block';
        bulkDeleteBtn.textContent = `Eliminar ${checkedBoxes.length} seleccionado${checkedBoxes.length > 1 ? 's' : ''}`;
    } else {
        bulkDeleteBtn.style.display = 'none';
    }
}

// Eliminar ejercicio individual
function deleteExercise(exerciseId, exerciseName) {
    if (confirm(`¿Estás seguro de que quieres eliminar el ejercicio "${exerciseName}"?\n\nEsta acción no se puede deshacer.`)) {
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

// Eliminar ejercicios en lote
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.exercise-checkbox:checked');
    const exerciseIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (exerciseIds.length === 0) {
        alert('No hay ejercicios seleccionados');
        return;
    }
    
    if (confirm(`¿Estás seguro de que quieres eliminar ${exerciseIds.length} ejercicio${exerciseIds.length > 1 ? 's' : ''}?\n\nEsta acción no se puede deshacer.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/exercise-management/bulk-delete';
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }
        
        // Add exercise IDs
        exerciseIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'exercise_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Exportar ejercicios
function exportExercises(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    
    window.open(`/admin/exercise-management/export?${params.toString()}`, '_blank');
}

// Limpiar video cuando se cierra el modal
document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('videoPlayer').src = '';
});
</script>