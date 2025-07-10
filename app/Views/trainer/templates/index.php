<?php
use StyleFitness\Helpers\AppHelper;
?>
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Plantillas de Rutinas
                    </h1>
                    <p class="text-muted mb-0">Gestiona plantillas diferenciadas por género y zonas corporales</p>
                </div>
                <div>
                    <a href="/trainer/templates/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nueva Plantilla
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="/trainer/templates" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                                   placeholder="Nombre de plantilla...">
                        </div>
                        <div class="col-md-2">
                            <label for="objective" class="form-label">Objetivo</label>
                            <select class="form-select" id="objective" name="objective">
                                <option value="">Todos</option>
                                <option value="muscle_gain" <?= ($_GET['objective'] ?? '') === 'muscle_gain' ? 'selected' : '' ?>>Ganancia Muscular</option>
                                <option value="weight_loss" <?= ($_GET['objective'] ?? '') === 'weight_loss' ? 'selected' : '' ?>>Pérdida de Peso</option>
                                <option value="strength" <?= ($_GET['objective'] ?? '') === 'strength' ? 'selected' : '' ?>>Fuerza</option>
                                <option value="endurance" <?= ($_GET['objective'] ?? '') === 'endurance' ? 'selected' : '' ?>>Resistencia</option>
                                <option value="toning" <?= ($_GET['objective'] ?? '') === 'toning' ? 'selected' : '' ?>>Tonificación</option>
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
                            <label for="gender" class="form-label">Género</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Todos</option>
                                <option value="male" <?= ($_GET['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Masculino</option>
                                <option value="female" <?= ($_GET['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Femenino</option>
                                <option value="unisex" <?= ($_GET['gender'] ?? '') === 'unisex' ? 'selected' : '' ?>>Unisex</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-outline-primary flex-fill">
                                    <i class="fas fa-search me-1"></i>
                                    Filtrar
                                </button>
                                <a href="/trainer/templates" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="row">
        <?php if (empty($templates)): ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay plantillas disponibles</h5>
                        <p class="text-muted mb-4">Comienza creando tu primera plantilla de rutina</p>
                        <a href="/trainer/templates/create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Crear Primera Plantilla
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($templates as $template): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card template-card h-100 shadow-sm">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1 fw-bold">
                                        <?= htmlspecialchars($template['name']) ?>
                                    </h6>
                                    <div class="d-flex gap-1 mb-2">
                                        <!-- Gender Badge -->
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
                                        <span class="badge bg-<?= $genderColor ?> badge-sm">
                                            <?= $genderLabel ?>
                                        </span>
                                        
                                        <!-- Difficulty Badge -->
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
                                        <span class="badge bg-<?= $difficultyColor ?> badge-sm">
                                            <?= $difficultyLabel ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="/trainer/templates/view/<?= $template['id'] ?>">
                                                <i class="fas fa-eye me-2"></i>Ver Detalles
                                            </a>
                                        </li>
                                        <?php if ($template['instructor_id'] == \StyleFitness\Helpers\AppHelper::getCurrentUser()['id'] || \StyleFitness\Helpers\AppHelper::getCurrentUser()['role'] === 'admin'): ?>
                                            <li>
                                                <a class="dropdown-item" href="/trainer/templates/edit/<?= $template['id'] ?>">
                                                    <i class="fas fa-edit me-2"></i>Editar
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="duplicateTemplate(<?= $template['id'] ?>)">
                                                <i class="fas fa-copy me-2"></i>Duplicar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="showAssignModal(<?= $template['id'] ?>)">
                                                <i class="fas fa-user-plus me-2"></i>Asignar a Cliente
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body pt-2">
                            <p class="card-text text-muted small mb-3">
                                <?= htmlspecialchars(substr($template['description'], 0, 100)) ?>
                                <?= strlen($template['description']) > 100 ? '...' : '' ?>
                            </p>
                            
                            <!-- Template Stats -->
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="border-end">
                                        <div class="fw-bold text-primary"><?= $template['duration_weeks'] ?></div>
                                        <small class="text-muted">Semanas</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-end">
                                        <div class="fw-bold text-success"><?= $template['sessions_per_week'] ?></div>
                                        <small class="text-muted">Sesiones/sem</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold text-info"><?= $template['estimated_duration_minutes'] ?>'</div>
                                    <small class="text-muted">Duración</small>
                                </div>
                            </div>
                            
                            <!-- Objective Badge -->
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
                            <div class="mb-3">
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-bullseye me-1"></i>
                                    <?= $objectiveLabel ?>
                                </span>
                            </div>
                            
                            <!-- Public/Private Status -->
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    <?= htmlspecialchars($template['instructor_name'] ?? 'Instructor') ?>
                                </small>
                                <div>
                                    <?php if ($template['is_public']): ?>
                                        <span class="badge bg-success badge-sm">
                                            <i class="fas fa-globe me-1"></i>Pública
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary badge-sm">
                                            <i class="fas fa-lock me-1"></i>Privada
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0 pt-0">
                            <div class="d-grid gap-2">
                                <a href="/trainer/templates/view/<?= $template['id'] ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-2"></i>Ver Plantilla
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (!empty($templates) && $pagination['total_pages'] > 1): ?>
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Paginación de plantillas">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['has_previous']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1])) ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php 
                        $start = max(1, $pagination['current_page'] - 2);
                        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        
                        for ($i = $start; $i <= $end; $i++): 
                        ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1])) ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <div class="text-center text-muted">
                    Mostrando <?= ($pagination['current_page'] - 1) * $pagination['per_page'] + 1 ?> - 
                    <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total_items']) ?> 
                    de <?= $pagination['total_items'] ?> plantillas
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal para Asignar Plantilla a Cliente -->
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
                    <input type="hidden" id="templateIdToAssign" name="template_id">
                    
                    <div class="mb-3">
                        <label for="clientSelect" class="form-label">Seleccionar Cliente</label>
                        <select class="form-select" id="clientSelect" name="client_id" required>
                            <option value="">Selecciona un cliente...</option>
                            <!-- Se llenará dinámicamente -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="customName" class="form-label">Nombre de la Rutina (Opcional)</label>
                        <input type="text" class="form-control" id="customName" name="custom_name" 
                               placeholder="Deja vacío para usar el nombre de la plantilla">
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

<style>
.template-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e3e6f0;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.card-title {
    font-size: 1rem;
    line-height: 1.2;
}

.border-end {
    border-right: 1px solid #e3e6f0 !important;
}

@media (max-width: 768px) {
    .template-card {
        margin-bottom: 1rem;
    }
    
    .border-end {
        border-right: none !important;
    }
}
</style>

<script>
// Función para mostrar modal de asignación
function showAssignModal(templateId) {
    document.getElementById('templateIdToAssign').value = templateId;
    
    // Cargar lista de clientes
    loadClientsList();
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('assignTemplateModal'));
    modal.show();
}

// Cargar lista de clientes del instructor
function loadClientsList() {
    fetch('/api/instructor/clients')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('clientSelect');
            select.innerHTML = '<option value="">Selecciona un cliente...</option>';
            
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
            showAlert('Error al cargar la lista de clientes', 'error');
        });
}

// Asignar plantilla a cliente
function assignTemplate() {
    const form = document.getElementById('assignTemplateForm');
    const formData = new FormData(form);
    
    // Validar que se haya seleccionado un cliente
    if (!formData.get('client_id')) {
        showAlert('Por favor selecciona un cliente', 'warning');
        return;
    }
    
    // Mostrar loading
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
            showAlert('Rutina asignada exitosamente', 'success');
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('assignTemplateModal'));
            modal.hide();
            
            // Limpiar formulario
            form.reset();
        } else {
            showAlert(data.error || 'Error al asignar la rutina', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al asignar la rutina', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Duplicar plantilla
function duplicateTemplate(templateId) {
    if (confirm('¿Estás seguro de que quieres duplicar esta plantilla?')) {
        window.location.href = `/trainer/templates/duplicate/${templateId}`;
    }
}

// Función para mostrar alertas
function showAlert(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insertar al inicio del container
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss después de 5 segundos
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}
</script>