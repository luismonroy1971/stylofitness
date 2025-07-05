<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Perfil de Usuario - STYLOFITNESS
 * Permite al usuario ver y editar su información personal
 */

$currentUser = AppHelper::getCurrentUser();
?>

<div class="profile-container">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar del perfil -->
            <div class="col-lg-3 col-md-4">
                <div class="profile-sidebar">
                    <div class="profile-avatar-section">
                        <div class="avatar-container">
                            <?php if (!empty($currentUser['avatar'])): ?>
                                <img src="<?= htmlspecialchars($currentUser['avatar']) ?>" 
                                     alt="Avatar" class="profile-avatar">
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <?= strtoupper(substr($currentUser['first_name'], 0, 1) . substr($currentUser['last_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                    data-bs-toggle="modal" data-bs-target="#avatarModal">
                                <i class="fas fa-camera me-1"></i>
                                Cambiar Foto
                            </button>
                        </div>
                        <div class="profile-info">
                            <h4><?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></h4>
                            <p class="text-muted"><?= ucfirst($currentUser['role']) ?></p>
                            <span class="badge bg-<?= $currentUser['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $currentUser['is_active'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Navegación del perfil -->
                    <div class="profile-nav">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="#personal-info" data-bs-toggle="pill">
                                    <i class="fas fa-user me-2"></i>
                                    Información Personal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#security" data-bs-toggle="pill">
                                    <i class="fas fa-lock me-2"></i>
                                    Seguridad
                                </a>
                            </li>
                            <?php if ($currentUser['role'] === 'client'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#fitness-info" data-bs-toggle="pill">
                                    <i class="fas fa-dumbbell me-2"></i>
                                    Información Fitness
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#emergency" data-bs-toggle="pill">
                                    <i class="fas fa-phone me-2"></i>
                                    Contacto de Emergencia
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Estadísticas rápidas -->
                    <?php if (isset($stats)): ?>
                    <div class="profile-stats">
                        <h6 class="text-muted mb-3">Estadísticas</h6>
                        <div class="stat-item">
                            <span class="stat-value"><?= $stats['total_routines'] ?></span>
                            <span class="stat-label">Rutinas</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= $stats['active_routines'] ?></span>
                            <span class="stat-label">Activas</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= $stats['total_orders'] ?></span>
                            <span class="stat-label">Órdenes</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="col-lg-9 col-md-8">
                <div class="profile-content">
                    <div class="tab-content">
                        <!-- Información Personal -->
                        <div class="tab-pane fade show active" id="personal-info">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user me-2"></i>
                                        Información Personal
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/profile/update">
                                        <?= AppHelper::generateCSRFToken() ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="first_name" class="form-label">Nombre *</label>
                                                    <input type="text" class="form-control" id="first_name" 
                                                           name="first_name" value="<?= htmlspecialchars($currentUser['first_name']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="last_name" class="form-label">Apellido *</label>
                                                    <input type="text" class="form-control" id="last_name" 
                                                           name="last_name" value="<?= htmlspecialchars($currentUser['last_name']) ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email *</label>
                                                    <input type="email" class="form-control" id="email" 
                                                           name="email" value="<?= htmlspecialchars($currentUser['email']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">Teléfono</label>
                                                    <input type="tel" class="form-control" id="phone" 
                                                           name="phone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="date_of_birth" class="form-label">Fecha de Nacimiento</label>
                                                    <input type="date" class="form-control" id="date_of_birth" 
                                                           name="date_of_birth" value="<?= htmlspecialchars($currentUser['date_of_birth'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="gender" class="form-label">Género</label>
                                                    <select class="form-select" id="gender" name="gender">
                                                        <option value="">Seleccionar...</option>
                                                        <option value="male" <?= ($currentUser['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Masculino</option>
                                                        <option value="female" <?= ($currentUser['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Femenino</option>
                                                        <option value="other" <?= ($currentUser['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Otro</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                Guardar Cambios
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Seguridad -->
                        <div class="tab-pane fade" id="security">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-lock me-2"></i>
                                        Cambiar Contraseña
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/profile/password">
                                        <?= AppHelper::generateCSRFToken() ?>
                                        
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Contraseña Actual *</label>
                                            <input type="password" class="form-control" id="current_password" 
                                                   name="current_password" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">Nueva Contraseña *</label>
                                            <input type="password" class="form-control" id="new_password" 
                                                   name="new_password" minlength="6" required>
                                            <div class="form-text">Mínimo 6 caracteres</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña *</label>
                                            <input type="password" class="form-control" id="confirm_password" 
                                                   name="confirm_password" minlength="6" required>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-key me-2"></i>
                                                Cambiar Contraseña
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <?php if ($currentUser['role'] === 'client'): ?>
                        <!-- Información Fitness -->
                        <div class="tab-pane fade" id="fitness-info">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-dumbbell me-2"></i>
                                        Información Fitness
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/profile/update">
                                        <?= AppHelper::generateCSRFToken() ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="height" class="form-label">Altura (cm)</label>
                                                    <input type="number" class="form-control" id="height" 
                                                           name="height" step="0.1" min="100" max="250"
                                                           value="<?= htmlspecialchars($currentUser['height'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="weight" class="form-label">Peso (kg)</label>
                                                    <input type="number" class="form-control" id="weight" 
                                                           name="weight" step="0.1" min="30" max="300"
                                                           value="<?= htmlspecialchars($currentUser['weight'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="fitness_goal" class="form-label">Objetivo Fitness</label>
                                            <select class="form-select" id="fitness_goal" name="fitness_goal">
                                                <option value="">Seleccionar...</option>
                                                <option value="weight_loss" <?= ($currentUser['fitness_goal'] ?? '') === 'weight_loss' ? 'selected' : '' ?>>Pérdida de peso</option>
                                                <option value="muscle_gain" <?= ($currentUser['fitness_goal'] ?? '') === 'muscle_gain' ? 'selected' : '' ?>>Ganancia muscular</option>
                                                <option value="endurance" <?= ($currentUser['fitness_goal'] ?? '') === 'endurance' ? 'selected' : '' ?>>Resistencia</option>
                                                <option value="strength" <?= ($currentUser['fitness_goal'] ?? '') === 'strength' ? 'selected' : '' ?>>Fuerza</option>
                                                <option value="general_fitness" <?= ($currentUser['fitness_goal'] ?? '') === 'general_fitness' ? 'selected' : '' ?>>Fitness general</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="experience_level" class="form-label">Nivel de Experiencia</label>
                                            <select class="form-select" id="experience_level" name="experience_level">
                                                <option value="">Seleccionar...</option>
                                                <option value="beginner" <?= ($currentUser['experience_level'] ?? '') === 'beginner' ? 'selected' : '' ?>>Principiante</option>
                                                <option value="intermediate" <?= ($currentUser['experience_level'] ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermedio</option>
                                                <option value="advanced" <?= ($currentUser['experience_level'] ?? '') === 'advanced' ? 'selected' : '' ?>>Avanzado</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="medical_conditions" class="form-label">Condiciones Médicas</label>
                                            <textarea class="form-control" id="medical_conditions" name="medical_conditions" 
                                                      rows="3" placeholder="Describe cualquier condición médica relevante..."><?= htmlspecialchars($currentUser['medical_conditions'] ?? '') ?></textarea>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                Guardar Información Fitness
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Contacto de Emergencia -->
                        <div class="tab-pane fade" id="emergency">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-phone me-2"></i>
                                        Contacto de Emergencia
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/profile/update">
                                        <?= AppHelper::generateCSRFToken() ?>
                                        
                                        <div class="mb-3">
                                            <label for="emergency_contact_name" class="form-label">Nombre del Contacto</label>
                                            <input type="text" class="form-control" id="emergency_contact_name" 
                                                   name="emergency_contact_name" 
                                                   value="<?= htmlspecialchars($currentUser['emergency_contact_name'] ?? '') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="emergency_contact_phone" class="form-label">Teléfono del Contacto</label>
                                            <input type="tel" class="form-control" id="emergency_contact_phone" 
                                                   name="emergency_contact_phone" 
                                                   value="<?= htmlspecialchars($currentUser['emergency_contact_phone'] ?? '') ?>">
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                Guardar Contacto de Emergencia
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar avatar -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Foto de Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/profile/avatar" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= AppHelper::generateCSRFToken() ?>
                    
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Seleccionar Imagen</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" 
                               accept="image/jpeg,image/png,image/gif,image/webp" required>
                        <div class="form-text">Formatos permitidos: JPG, PNG, GIF, WebP. Máximo 5MB.</div>
                    </div>
                    
                    <div id="imagePreview" class="text-center" style="display: none;">
                        <img id="previewImg" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>
                        Subir Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.profile-container {
    padding: 2rem 0;
    min-height: calc(100vh - 200px);
}

.profile-sidebar {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.profile-avatar-section {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.avatar-container {
    position: relative;
    display: inline-block;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #f8f9fa;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.avatar-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: bold;
    border: 4px solid #f8f9fa;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.profile-info h4 {
    margin: 1rem 0 0.5rem;
    color: #2c3e50;
}

.profile-nav .nav-pills .nav-link {
    color: #6c757d;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.profile-nav .nav-pills .nav-link:hover,
.profile-nav .nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.profile-stats {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
}

.stat-value {
    font-weight: bold;
    color: #667eea;
    font-size: 1.1rem;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
}

.profile-content .card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.profile-content .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 0 0 !important;
    border: none;
}

.form-control:focus,
.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1.5rem;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border: none;
    color: white;
}

@media (max-width: 768px) {
    .profile-container {
        padding: 1rem 0;
    }
    
    .profile-sidebar {
        margin-bottom: 1rem;
    }
    
    .profile-avatar,
    .avatar-placeholder {
        width: 80px;
        height: 80px;
        font-size: 1.5rem;
    }
}
</style>

<script>
// Vista previa de imagen
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Validación de contraseñas
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword !== confirmPassword) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});
</script>