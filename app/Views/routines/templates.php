<?php
/**
 * Vista: Plantillas de Rutinas - STYLOFITNESS
 * Galería de plantillas públicas de rutinas profesionales
 */

$currentUser = AppHelper::getCurrentUser();
$isInstructor = ($currentUser['role'] === 'instructor');
$isAdmin = ($currentUser['role'] === 'admin');

$objectiveLabels = [
    'weight_loss' => 'Pérdida de Peso',
    'muscle_gain' => 'Ganancia Muscular',
    'strength' => 'Fuerza',
    'endurance' => 'Resistencia',
    'flexibility' => 'Flexibilidad'
];

$difficultyLabels = [
    'beginner' => 'Principiante',
    'intermediate' => 'Intermedio',
    'advanced' => 'Avanzado'
];
?>

<div class="templates-page">
    <!-- Hero Section -->
    <section class="hero-section bg-gradient-primary">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="hero-title text-white mb-3">
                        Plantillas Profesionales
                    </h1>
                    <p class="hero-subtitle text-white-75 mb-4">
                        Rutinas diseñadas por expertos para diferentes objetivos de entrenamiento.
                        Úsalas como base para crear rutinas personalizadas para tus clientes.
                    </p>
                    
                    <div class="hero-stats">
                        <div class="row g-3">
                            <div class="col-auto">
                                <div class="stat-badge">
                                    <i class="fas fa-star me-2"></i>
                                    <?= count($routines) ?> Plantillas Disponibles
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="stat-badge">
                                    <i class="fas fa-users me-2"></i>
                                    Creadas por Profesionales
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="stat-badge">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Probadas y Efectivas
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="hero-image">
                        <i class="fas fa-clipboard-list fa-8x text-white-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <!-- Filtros -->
        <div class="filters-section mb-5">
            <form method="GET" action="/routines/templates" class="filters-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Buscar plantillas</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Nombre o descripción..."
                                       value="<?= htmlspecialchars($filters['search']) ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Objetivo</label>
                            <select name="objective" class="form-select">
                                <option value="">Todos los objetivos</option>
                                <?php foreach ($objectiveLabels as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $filters['objective'] === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Dificultad</label>
                            <select name="difficulty" class="form-select">
                                <option value="">Todas las dificultades</option>
                                <?php foreach ($difficultyLabels as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $filters['difficulty'] === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Plantillas -->
        <?php if (empty($routines)): ?>
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-4">
                    <i class="fas fa-clipboard-list fa-4x text-muted"></i>
                </div>
                <h3 class="empty-title">No se encontraron plantillas</h3>
                <p class="empty-text text-muted mb-4">
                    Prueba ajustando los filtros de búsqueda para encontrar las plantillas que necesitas.
                </p>
                <a href="/routines/templates" class="btn btn-primary">
                    <i class="fas fa-refresh me-2"></i>Ver Todas las Plantillas
                </a>
            </div>
        <?php else: ?>
            <div class="templates-grid">
                <div class="row g-4">
                    <?php foreach ($routines as $routine): ?>
                    <div class="col-lg-6 col-xl-4">
                        <div class="template-card">
                            <div class="template-header">
                                <div class="template-badges">
                                    <span class="badge badge-<?= $routine['objective'] ?>">
                                        <?= $objectiveLabels[$routine['objective']] ?? ucfirst($routine['objective']) ?>
                                    </span>
                                    <span class="badge badge-outline-<?= $routine['difficulty_level'] ?>">
                                        <?= $difficultyLabels[$routine['difficulty_level']] ?? ucfirst($routine['difficulty_level']) ?>
                                    </span>
                                    <span class="badge badge-template">
                                        <i class="fas fa-star me-1"></i>Plantilla
                                    </span>
                                </div>
                                
                                <?php if ($isInstructor || $isAdmin): ?>
                                <div class="template-actions dropdown">
                                    <button class="btn btn-sm btn-ghost" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="/routines/view/<?= $routine['id'] ?>">
                                            <i class="fas fa-eye me-2"></i>Ver Detalle
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="useTemplate(<?= $routine['id'] ?>)">
                                            <i class="fas fa-plus me-2"></i>Usar Plantilla
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="assignTemplate(<?= $routine['id'] ?>)">
                                            <i class="fas fa-user-plus me-2"></i>Asignar a Cliente
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="duplicateRoutine(<?= $routine['id'] ?>)">
                                            <i class="fas fa-copy me-2"></i>Duplicar
                                        </a></li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="template-body">
                                <h3 class="template-title">
                                    <a href="/routines/view/<?= $routine['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($routine['name']) ?>
                                    </a>
                                </h3>
                                
                                <p class="template-description">
                                    <?= htmlspecialchars(substr($routine['description'], 0, 120)) ?>
                                    <?= strlen($routine['description']) > 120 ? '...' : '' ?>
                                </p>
                                
                                <div class="template-stats">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span><?= $routine['duration_weeks'] ?> semanas</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <i class="fas fa-repeat"></i>
                                                <span><?= $routine['sessions_per_week'] ?>/semana</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <i class="fas fa-clock"></i>
                                                <span><?= $routine['estimated_duration_minutes'] ?> min</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <i class="fas fa-dumbbell"></i>
                                                <span><?= $routine['exercise_count'] ?? 0 ?> ejercicios</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (!empty($routine['instructor_first_name'])): ?>
                                <div class="template-author">
                                    <small class="text-muted">
                                        <i class="fas fa-user-check me-1"></i>
                                        Por: <?= htmlspecialchars($routine['instructor_first_name'] . ' ' . $routine['instructor_last_name']) ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="template-footer">
                                <div class="template-date">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Actualizada: <?= date('d/m/Y', strtotime($routine['updated_at'])) ?>
                                    </small>
                                </div>
                                
                                <div class="template-cta">
                                    <div class="btn-group">
                                        <a href="/routines/view/<?= $routine['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            Ver Detalles
                                        </a>
                                        <?php if ($isInstructor || $isAdmin): ?>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="useTemplate(<?= $routine['id'] ?>)">
                                            <i class="fas fa-plus me-1"></i>Usar
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Paginación -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <nav class="pagination-wrapper mt-5" aria-label="Paginación de plantillas">
            <ul class="pagination justify-content-center">
                <?php if ($pagination['has_previous']): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&<?= http_build_query(array_filter($filters)) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <?php if ($pagination['has_next']): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&<?= http_build_query(array_filter($filters)) ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <!-- Sección de Beneficios -->
        <section class="benefits-section mt-5 pt-5">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="section-title mb-4">¿Por qué usar nuestras plantillas?</h2>
                    <p class="section-subtitle mb-5">
                        Nuestras plantillas han sido diseñadas por entrenadores profesionales y probadas 
                        con cientos de clientes para garantizar resultados efectivos.
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="benefit-card text-center">
                        <div class="benefit-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h5 class="benefit-title">Diseño Profesional</h5>
                        <p class="benefit-description">
                            Creadas por entrenadores certificados con años de experiencia.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="benefit-card text-center">
                        <div class="benefit-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="benefit-title">Resultados Probados</h5>
                        <p class="benefit-description">
                            Testadas con clientes reales para garantizar efectividad.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="benefit-card text-center">
                        <div class="benefit-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <h5 class="benefit-title">Totalmente Personalizables</h5>
                        <p class="benefit-description">
                            Modifica cualquier aspecto para adaptarlas a tus clientes.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="benefit-card text-center">
                        <div class="benefit-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h5 class="benefit-title">Ahorra Tiempo</h5>
                        <p class="benefit-description">
                            Comienza con una base sólida en lugar de partir de cero.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal para Asignar Plantilla -->
<div class="modal fade" id="assignTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Plantilla a Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignTemplateForm" method="POST" action="/routines/assign">
                <div class="modal-body">
                    <input type="hidden" id="assign_routine_id" name="routine_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Cliente</label>
                        <select class="form-select" name="client_id" required>
                            <option value="">Seleccionar cliente...</option>
                            <!-- Los clientes se cargarán dinámicamente -->
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Se creará una copia personalizada de la plantilla para el cliente seleccionado.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Asignar Plantilla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function useTemplate(routineId) {
    if (confirm('¿Deseas crear una nueva rutina basada en esta plantilla?')) {
        fetch('/routines/duplicate/' + routineId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= AppHelper::generateCsrfToken() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Plantilla copiada exitosamente');
                setTimeout(() => {
                    window.location.href = '/routines/edit/' + data.routine_id;
                }, 1500);
            } else {
                showAlert('error', data.error || 'Error al copiar la plantilla');
            }
        })
        .catch(() => {
            showAlert('error', 'Error de conexión');
        });
    }
}

function assignTemplate(routineId) {
    document.getElementById('assign_routine_id').value = routineId;
    
    // Cargar clientes disponibles
    fetch('/api/clients')
        .then(response => response.json())
        .then(data => {
            const select = document.querySelector('#assignTemplateModal select[name="client_id"]');
            select.innerHTML = '<option value="">Seleccionar cliente...</option>';
            
            if (data.success && data.clients) {
                data.clients.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = `${client.first_name} ${client.last_name}`;
                    select.appendChild(option);
                });
            }
        });
    
    new bootstrap.Modal(document.getElementById('assignTemplateModal')).show();
}

function duplicateRoutine(routineId) {
    useTemplate(routineId);
}

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

<style>
.templates-page {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
}

.stat-badge {
    background: rgba(255, 255, 255, 0.15);
    color: rgba(255, 255, 255, 0.9);
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.9rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.text-white-25 {
    color: rgba(255, 255, 255, 0.25) !important;
}

.template-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.template-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-color: var(--routine-primary);
}

.template-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.template-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.template-body {
    flex: 1;
    margin-bottom: 1rem;
}

.template-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: var(--routine-dark);
    line-height: 1.3;
}

.template-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.template-title a:hover {
    color: var(--routine-primary);
}

.template-description {
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.template-stats .stat-item {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.template-stats .stat-item i {
    color: var(--routine-primary);
    margin-right: 0.5rem;
    width: 16px;
    text-align: center;
}

.template-author {
    padding-top: 0.75rem;
    border-top: 1px solid #e9ecef;
}

.template-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.benefits-section {
    border-top: 1px solid #e9ecef;
}

.benefit-card {
    background: white;
    border-radius: 1rem;
    padding: 2rem 1.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.benefit-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.benefit-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--routine-primary), var(--routine-secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.benefit-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--routine-dark);
    margin-bottom: 0.75rem;
}

.benefit-description {
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 0;
}
</style>