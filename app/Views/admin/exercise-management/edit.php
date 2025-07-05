<?php
use StyleFitness\Helpers\AppHelper;

// Verificar permisos
if (!AppHelper::isLoggedIn() || !AppHelper::hasRole('admin')) {
    AppHelper::redirect('/login');
}

$pageTitle = 'Editar Ejercicio';
$exerciseId = $_GET['id'] ?? 0;
$exerciseController = new \StyleFitness\Controllers\ExerciseManagementController();
$exercise = $exerciseController->getExercise($exerciseId);
$categories = $exerciseController->getCategories();

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
                    <h1 class="h3 mb-0 text-gray-800">Editar Ejercicio</h1>
                    <p class="text-muted mb-0">Modifica la información del ejercicio: <?= htmlspecialchars($exercise['name']) ?></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/admin/exercise-management/show/<?= $exercise['id'] ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver a Detalles
                    </a>
                    <a href="/admin/exercise-management" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>
                        Lista de Ejercicios
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

    <!-- Exercise Form -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>
                        Información del Ejercicio
                    </h6>
                </div>
                <div class="card-body">
                    <form action="/admin/exercise-management/update/<?= $exercise['id'] ?>" method="POST" enctype="multipart/form-data" id="exerciseForm">
                        <input type="hidden" name="csrf_token" value="<?= \StyleFitness\Helpers\AppHelper::generateCsrfToken() ?>">
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label required">Nombre del Ejercicio</label>
                                        <input type="text" class="form-control <?= isset($_SESSION['errors']['name']) ? 'is-invalid' : '' ?>" 
                                               id="name" name="name" 
                                               value="<?= htmlspecialchars($_SESSION['old']['name'] ?? $exercise['name']) ?>" 
                                               placeholder="Ej: Press de banca" required>
                                        <?php if (isset($_SESSION['errors']['name'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($_SESSION['errors']['name']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="category_id" class="form-label required">Categoría</label>
                                        <select class="form-select <?= isset($_SESSION['errors']['category_id']) ? 'is-invalid' : '' ?>" 
                                                id="category_id" name="category_id" required>
                                            <option value="">Seleccionar categoría</option>
                                            <?php if (!empty($categories)): ?>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['id'] ?>" 
                                                            <?= ($_SESSION['old']['category_id'] ?? $exercise['category_id']) == $category['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($category['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php if (isset($_SESSION['errors']['category_id'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($_SESSION['errors']['category_id']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Descripción</label>
                                    <textarea class="form-control <?= isset($_SESSION['errors']['description']) ? 'is-invalid' : '' ?>" 
                                              id="description" name="description" rows="4" 
                                              placeholder="Describe cómo realizar el ejercicio, técnica, beneficios..."><?= htmlspecialchars($_SESSION['old']['description'] ?? $exercise['description']) ?></textarea>
                                    <?php if (isset($_SESSION['errors']['description'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($_SESSION['errors']['description']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="difficulty_level" class="form-label required">Nivel de Dificultad</label>
                                        <select class="form-select <?= isset($_SESSION['errors']['difficulty_level']) ? 'is-invalid' : '' ?>" 
                                                id="difficulty_level" name="difficulty_level" required>
                                            <option value="">Seleccionar nivel</option>
                                            <option value="beginner" <?= ($_SESSION['old']['difficulty_level'] ?? $exercise['difficulty_level']) === 'beginner' ? 'selected' : '' ?>>
                                                Principiante
                                            </option>
                                            <option value="intermediate" <?= ($_SESSION['old']['difficulty_level'] ?? $exercise['difficulty_level']) === 'intermediate' ? 'selected' : '' ?>>
                                                Intermedio
                                            </option>
                                            <option value="advanced" <?= ($_SESSION['old']['difficulty_level'] ?? $exercise['difficulty_level']) === 'advanced' ? 'selected' : '' ?>>
                                                Avanzado
                                            </option>
                                        </select>
                                        <?php if (isset($_SESSION['errors']['difficulty_level'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($_SESSION['errors']['difficulty_level']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="equipment_needed" class="form-label">Equipamiento Necesario</label>
                                        <input type="text" class="form-control <?= isset($_SESSION['errors']['equipment_needed']) ? 'is-invalid' : '' ?>" 
                                               id="equipment_needed" name="equipment_needed" 
                                               value="<?= htmlspecialchars($_SESSION['old']['equipment_needed'] ?? $exercise['equipment_needed']) ?>" 
                                               placeholder="Ej: Mancuernas, Barra, Peso corporal">
                                        <?php if (isset($_SESSION['errors']['equipment_needed'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($_SESSION['errors']['equipment_needed']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="muscle_groups" class="form-label">Grupos Musculares</label>
                                    <div class="muscle-groups-container">
                                        <div class="row">
                                            <?php 
                                            $muscleGroups = [
                                                'Pecho', 'Espalda', 'Hombros', 'Bíceps', 'Tríceps', 'Antebrazos',
                                                'Cuádriceps', 'Isquiotibiales', 'Glúteos', 'Pantorrillas', 'Abdominales', 'Oblicuos'
                                            ];
                                            $selectedMuscles = $_SESSION['old']['muscle_groups'] ?? $exercise['muscle_groups'] ?? [];
                                            if (is_string($selectedMuscles)) {
                                                $selectedMuscles = json_decode($selectedMuscles, true) ?? [];
                                            }
                                            ?>
                                            <?php foreach ($muscleGroups as $muscle): ?>
                                                <div class="col-md-3 col-sm-4 col-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="muscle_groups[]" value="<?= $muscle ?>" 
                                                               id="muscle_<?= strtolower(str_replace(' ', '_', $muscle)) ?>"
                                                               <?= in_array($muscle, $selectedMuscles) ? 'checked' : '' ?>>
                                                        <label class="form-check-label" 
                                                               for="muscle_<?= strtolower(str_replace(' ', '_', $muscle)) ?>">
                                                            <?= $muscle ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php if (isset($_SESSION['errors']['muscle_groups'])): ?>
                                        <div class="text-danger small mt-1">
                                            <?= htmlspecialchars($_SESSION['errors']['muscle_groups']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="instructions" class="form-label">Instrucciones de Ejecución</label>
                                    <textarea class="form-control <?= isset($_SESSION['errors']['instructions']) ? 'is-invalid' : '' ?>" 
                                              id="instructions" name="instructions" rows="6" 
                                              placeholder="Paso a paso de cómo realizar el ejercicio..."><?= htmlspecialchars($_SESSION['old']['instructions'] ?? $exercise['instructions']) ?></textarea>
                                    <?php if (isset($_SESSION['errors']['instructions'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($_SESSION['errors']['instructions']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tips" class="form-label">Consejos y Precauciones</label>
                                    <textarea class="form-control <?= isset($_SESSION['errors']['tips']) ? 'is-invalid' : '' ?>" 
                                              id="tips" name="tips" rows="3" 
                                              placeholder="Consejos para mejorar la técnica, errores comunes a evitar..."><?= htmlspecialchars($_SESSION['old']['tips'] ?? $exercise['tips']) ?></textarea>
                                    <?php if (isset($_SESSION['errors']['tips'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($_SESSION['errors']['tips']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Media Upload Section -->
                            <div class="col-lg-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary mb-3">
                                            <i class="fas fa-video me-2"></i>
                                            Video del Ejercicio
                                        </h6>
                                        
                                        <!-- Current Video -->
                                        <?php if (!empty($exercise['video_url'])): ?>
                                            <div class="current-video-section mb-3">
                                                <label class="form-label small text-muted">Video Actual</label>
                                                <div class="current-video-container">
                                                    <video class="current-video" controls>
                                                        <source src="<?= htmlspecialchars($exercise['video_url']) ?>" type="video/mp4">
                                                        Tu navegador no soporta el elemento de video.
                                                    </video>
                                                    <div class="current-video-actions mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="removeCurrentVideo()">
                                                            <i class="fas fa-trash me-1"></i>
                                                            Eliminar Video Actual
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        <?php endif; ?>
                                        
                                        <div class="video-upload-section mb-4">
                                            <label class="form-label small text-muted">
                                                <?= !empty($exercise['video_url']) ? 'Reemplazar Video' : 'Subir Video' ?>
                                            </label>
                                            
                                            <div class="video-preview-container mb-3" id="videoPreviewContainer" style="display: none;">
                                                <video id="videoPreview" class="video-preview" controls>
                                                    Tu navegador no soporta el elemento de video.
                                                </video>
                                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" 
                                                        onclick="removeVideo()">
                                                    <i class="fas fa-trash me-1"></i>
                                                    Remover Video
                                                </button>
                                            </div>
                                            
                                            <div class="video-upload-area" id="videoUploadArea">
                                                <div class="upload-dropzone" onclick="document.getElementById('video_file').click()">
                                                    <div class="upload-content">
                                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                        <h6 class="text-muted small">Subir Nuevo Video</h6>
                                                        <p class="text-muted small mb-2">
                                                            Haz clic para seleccionar o arrastra el archivo aquí
                                                        </p>
                                                        <p class="text-muted small">
                                                            Formatos: MP4, AVI, MOV<br>
                                                            Tamaño máximo: 50MB
                                                        </p>
                                                    </div>
                                                </div>
                                                <input type="file" id="video_file" name="video_file" 
                                                       accept="video/*" style="display: none;" 
                                                       onchange="previewVideo(this)">
                                            </div>
                                            
                                            <?php if (isset($_SESSION['errors']['video_file'])): ?>
                                                <div class="text-danger small mt-2">
                                                    <?= htmlspecialchars($_SESSION['errors']['video_file']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <hr>
                                        
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-image me-2"></i>
                                            Imagen del Ejercicio
                                        </h6>
                                        
                                        <!-- Current Image -->
                                        <?php if (!empty($exercise['image_url'])): ?>
                                            <div class="current-image-section mb-3">
                                                <label class="form-label small text-muted">Imagen Actual</label>
                                                <div class="current-image-container">
                                                    <img src="<?= htmlspecialchars($exercise['image_url']) ?>" 
                                                         alt="<?= htmlspecialchars($exercise['name']) ?>" 
                                                         class="current-image">
                                                    <div class="current-image-actions mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="removeCurrentImage()">
                                                            <i class="fas fa-trash me-1"></i>
                                                            Eliminar Imagen Actual
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        <?php endif; ?>
                                        
                                        <div class="image-upload-section">
                                            <label class="form-label small text-muted">
                                                <?= !empty($exercise['image_url']) ? 'Reemplazar Imagen' : 'Subir Imagen' ?>
                                            </label>
                                            
                                            <div class="image-preview-container mb-3" id="imagePreviewContainer" style="display: none;">
                                                <img id="imagePreview" class="image-preview" alt="Vista previa">
                                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" 
                                                        onclick="removeImage()">
                                                    <i class="fas fa-trash me-1"></i>
                                                    Remover Imagen
                                                </button>
                                            </div>
                                            
                                            <div class="image-upload-area" id="imageUploadArea">
                                                <div class="upload-dropzone" onclick="document.getElementById('image_file').click()">
                                                    <div class="upload-content">
                                                        <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                                        <h6 class="text-muted small">Nueva Imagen</h6>
                                                        <p class="text-muted small mb-0">
                                                            JPG, PNG - Máx. 5MB
                                                        </p>
                                                    </div>
                                                </div>
                                                <input type="file" id="image_file" name="image_file" 
                                                       accept="image/*" style="display: none;" 
                                                       onchange="previewImage(this)">
                                            </div>
                                            
                                            <?php if (isset($_SESSION['errors']['image_file'])): ?>
                                                <div class="text-danger small mt-2">
                                                    <?= htmlspecialchars($_SESSION['errors']['image_file']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Hidden inputs for removing current media -->
                                        <input type="hidden" id="remove_current_video" name="remove_current_video" value="0">
                                        <input type="hidden" id="remove_current_image" name="remove_current_image" value="0">
                                        
                                        <div class="mt-4 p-3 bg-white rounded border">
                                            <h6 class="text-info mb-2">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Información
                                            </h6>
                                            <ul class="small text-muted mb-0">
                                                <li>Los archivos nuevos reemplazarán los existentes</li>
                                                <li>Deja vacío para mantener los archivos actuales</li>
                                                <li>Usa "Eliminar" para quitar archivos sin reemplazar</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex gap-2">
                                        <a href="/admin/exercise-management/show/<?= $exercise['id'] ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>
                                            Cancelar
                                        </a>
                                        <a href="/admin/exercise-management" class="btn btn-outline-secondary">
                                            <i class="fas fa-list me-2"></i>
                                            Lista de Ejercicios
                                        </a>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-primary" onclick="previewExercise()">
                                            <i class="fas fa-eye me-2"></i>
                                            Vista Previa
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-save me-2"></i>
                                            Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>
                    Vista Previa del Ejercicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<style>
.required::after {
    content: " *";
    color: #dc3545;
}

.upload-dropzone {
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    padding: 1.5rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
}

.upload-dropzone:hover {
    border-color: #4e73df;
    background: #f8f9fc;
}

.upload-dropzone.dragover {
    border-color: #4e73df;
    background: #e3f2fd;
}

.video-preview,
.current-video {
    width: 100%;
    max-height: 150px;
    border-radius: 0.5rem;
    object-fit: cover;
}

.image-preview,
.current-image {
    width: 100%;
    max-height: 120px;
    border-radius: 0.5rem;
    object-fit: cover;
}

.muscle-groups-container .form-check {
    margin-bottom: 0.5rem;
}

.muscle-groups-container .form-check-label {
    font-size: 0.9rem;
    cursor: pointer;
}

.muscle-groups-container .form-check-input:checked + .form-check-label {
    color: #4e73df;
    font-weight: 500;
}

.current-video-container,
.current-image-container {
    position: relative;
}

.current-video-actions,
.current-image-actions {
    text-align: center;
}

@media (max-width: 768px) {
    .upload-dropzone {
        padding: 1rem 0.5rem;
    }
    
    .upload-content h6 {
        font-size: 0.85rem;
    }
    
    .upload-content p {
        font-size: 0.75rem;
    }
}
</style>

<script>
// Preview video file
function previewVideo(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (50MB)
        if (file.size > 50 * 1024 * 1024) {
            alert('El archivo de video es demasiado grande. El tamaño máximo es 50MB.');
            input.value = '';
            return;
        }
        
        // Validate file type
        if (!file.type.startsWith('video/')) {
            alert('Por favor selecciona un archivo de video válido.');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('videoPreview').src = e.target.result;
            document.getElementById('videoPreviewContainer').style.display = 'block';
            document.getElementById('videoUploadArea').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

// Preview image file
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('El archivo de imagen es demasiado grande. El tamaño máximo es 5MB.');
            input.value = '';
            return;
        }
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Por favor selecciona un archivo de imagen válido.');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
            document.getElementById('imageUploadArea').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

// Remove new video
function removeVideo() {
    document.getElementById('video_file').value = '';
    document.getElementById('videoPreview').src = '';
    document.getElementById('videoPreviewContainer').style.display = 'none';
    document.getElementById('videoUploadArea').style.display = 'block';
}

// Remove new image
function removeImage() {
    document.getElementById('image_file').value = '';
    document.getElementById('imagePreview').src = '';
    document.getElementById('imagePreviewContainer').style.display = 'none';
    document.getElementById('imageUploadArea').style.display = 'block';
}

// Remove current video
function removeCurrentVideo() {
    if (confirm('¿Estás seguro de que quieres eliminar el video actual?')) {
        document.getElementById('remove_current_video').value = '1';
        document.querySelector('.current-video-section').style.display = 'none';
    }
}

// Remove current image
function removeCurrentImage() {
    if (confirm('¿Estás seguro de que quieres eliminar la imagen actual?')) {
        document.getElementById('remove_current_image').value = '1';
        document.querySelector('.current-image-section').style.display = 'none';
    }
}

// Preview exercise
function previewExercise() {
    const form = document.getElementById('exerciseForm');
    const formData = new FormData(form);
    
    // Get selected muscle groups
    const muscleGroups = [];
    document.querySelectorAll('input[name="muscle_groups[]"]:checked').forEach(checkbox => {
        muscleGroups.push(checkbox.value);
    });
    
    // Determine video source
    let videoSrc = '';
    if (document.getElementById('videoPreview').src) {
        videoSrc = document.getElementById('videoPreview').src;
    } else if (document.getElementById('remove_current_video').value === '0' && document.querySelector('.current-video')) {
        videoSrc = document.querySelector('.current-video').src;
    }
    
    // Determine image source
    let imageSrc = '';
    if (document.getElementById('imagePreview').src) {
        imageSrc = document.getElementById('imagePreview').src;
    } else if (document.getElementById('remove_current_image').value === '0' && document.querySelector('.current-image')) {
        imageSrc = document.querySelector('.current-image').src;
    }
    
    // Build preview content
    let previewHTML = `
        <div class="row">
            <div class="col-md-8">
                <h5 class="text-primary">${formData.get('name') || 'Sin nombre'}</h5>
                <p class="text-muted mb-3">${formData.get('description') || 'Sin descripción'}</p>
                
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <strong>Categoría:</strong><br>
                        <span class="badge bg-secondary">${getCategoryName(formData.get('category_id'))}</span>
                    </div>
                    <div class="col-sm-6">
                        <strong>Dificultad:</strong><br>
                        <span class="badge bg-${getDifficultyColor(formData.get('difficulty_level'))}">
                            ${getDifficultyLabel(formData.get('difficulty_level'))}
                        </span>
                    </div>
                </div>
                
                ${formData.get('equipment_needed') ? `
                    <div class="mb-3">
                        <strong>Equipamiento:</strong><br>
                        <span class="badge bg-info">${formData.get('equipment_needed')}</span>
                    </div>
                ` : ''}
                
                ${muscleGroups.length > 0 ? `
                    <div class="mb-3">
                        <strong>Grupos Musculares:</strong><br>
                        ${muscleGroups.map(muscle => `<span class="badge bg-light text-dark me-1">${muscle}</span>`).join('')}
                    </div>
                ` : ''}
                
                ${formData.get('instructions') ? `
                    <div class="mb-3">
                        <strong>Instrucciones:</strong>
                        <p class="mt-2">${formData.get('instructions').replace(/\n/g, '<br>')}</p>
                    </div>
                ` : ''}
                
                ${formData.get('tips') ? `
                    <div class="mb-3">
                        <strong>Consejos:</strong>
                        <p class="mt-2">${formData.get('tips').replace(/\n/g, '<br>')}</p>
                    </div>
                ` : ''}
            </div>
            <div class="col-md-4">
                ${videoSrc ? `
                    <div class="mb-3">
                        <strong>Video:</strong>
                        <video class="w-100 mt-2" controls style="max-height: 200px;">
                            <source src="${videoSrc}" type="video/mp4">
                        </video>
                    </div>
                ` : ''}
                
                ${imageSrc ? `
                    <div class="mb-3">
                        <strong>Imagen:</strong>
                        <img src="${imageSrc}" class="w-100 mt-2" style="max-height: 150px; object-fit: cover;">
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('previewContent').innerHTML = previewHTML;
    
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

// Helper functions
function getCategoryName(categoryId) {
    const select = document.getElementById('category_id');
    const option = select.querySelector(`option[value="${categoryId}"]`);
    return option ? option.textContent : 'Sin categoría';
}

function getDifficultyColor(difficulty) {
    const colors = {
        'beginner': 'success',
        'intermediate': 'warning',
        'advanced': 'danger'
    };
    return colors[difficulty] || 'secondary';
}

function getDifficultyLabel(difficulty) {
    const labels = {
        'beginner': 'Principiante',
        'intermediate': 'Intermedio',
        'advanced': 'Avanzado'
    };
    return labels[difficulty] || difficulty;
}

// Submit form
function submitForm() {
    document.getElementById('exerciseForm').submit();
}

// Form validation
document.getElementById('exerciseForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
});

// Drag and drop functionality
['videoUploadArea', 'imageUploadArea'].forEach(areaId => {
    const area = document.getElementById(areaId);
    if (!area) return;
    
    const dropzone = area.querySelector('.upload-dropzone');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
    });
    
    dropzone.addEventListener('drop', function(e) {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const input = areaId === 'videoUploadArea' ? 
                document.getElementById('video_file') : 
                document.getElementById('image_file');
            input.files = files;
            
            if (areaId === 'videoUploadArea') {
                previewVideo(input);
            } else {
                previewImage(input);
            }
        }
    });
});
</script>

<?php
// Clear session data
if (isset($_SESSION['errors'])) unset($_SESSION['errors']);
if (isset($_SESSION['old'])) unset($_SESSION['old']);
?>