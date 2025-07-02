<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Listado de Rutinas - STYLOFITNESS
 * Muestra las rutinas del usuario con filtros y paginación
 */

$currentUser = AppHelper::getCurrentUser();
$isClient = ($currentUser['role'] === 'client');
$isInstructor = ($currentUser['role'] === 'instructor');
$isAdmin = ($currentUser['role'] === 'admin');
?>

<div class="routines-page">
    <!-- Hero Section -->
    <section class="hero-section bg-gradient-primary">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="hero-title text-white mb-3">
                        <?php if ($isClient): ?>
                            Mis Rutinas de Entrenamiento
                        <?php elseif ($isInstructor): ?>
                            Rutinas de mis Clientes
                        <?php else: ?>
                            Gestión de Rutinas
                        <?php endif; ?>
                    </h1>
                    <p class="hero-subtitle text-white-75 mb-4">
                        <?php if ($isClient): ?>
                            Sigue tus rutinas personalizadas y monitorea tu progreso
                        <?php elseif ($isInstructor): ?>
                            Crea y gestiona rutinas personalizadas para tus clientes
                        <?php else: ?>
                            Administra todas las rutinas y plantillas del sistema
                        <?php endif; ?>
                    </p>
                    
                    <?php if (!$isClient): ?>
                    <div class="hero-actions">
                        <a href="/routines/create" class="btn btn-white btn-lg me-3">
                            <i class="fas fa-plus me-2"></i>Crear Nueva Rutina
                        </a>
                        <a href="#plantillas" class="btn btn-outline-white btn-lg">
                            <i class="fas fa-copy me-2"></i>Ver Plantillas
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="hero-stats">
                        <div class="stat-card">
                            <div class="stat-number"><?= count($routines) ?></div>
                            <div class="stat-label">Rutinas Activas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <!-- Filtros y Búsqueda -->
        <div class="filters-section mb-5">
            <form method="GET" action="/routines" class="filters-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Buscar rutinas</label>
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
                                <option value="weight_loss" <?= $filters['objective'] === 'weight_loss' ? 'selected' : '' ?>>
                                    Pérdida de Peso
                                </option>
                                <option value="muscle_gain" <?= $filters['objective'] === 'muscle_gain' ? 'selected' : '' ?>>
                                    Ganancia Muscular
                                </option>
                                <option value="strength" <?= $filters['objective'] === 'strength' ? 'selected' : '' ?>>
                                    Fuerza
                                </option>
                                <option value="endurance" <?= $filters['objective'] === 'endurance' ? 'selected' : '' ?>>
                                    Resistencia
                                </option>
                                <option value="flexibility" <?= $filters['objective'] === 'flexibility' ? 'selected' : '' ?>>
                                    Flexibilidad
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Dificultad</label>
                            <select name="difficulty" class="form-select">
                                <option value="">Todas las dificultades</option>
                                <option value="beginner" <?= $filters['difficulty'] === 'beginner' ? 'selected' : '' ?>>
                                    Principiante
                                </option>
                                <option value="intermediate" <?= $filters['difficulty'] === 'intermediate' ? 'selected' : '' ?>>
                                    Intermedio
                                </option>
                                <option value="advanced" <?= $filters['difficulty'] === 'advanced' ? 'selected' : '' ?>>
                                    Avanzado
                                </option>
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

        <!-- Lista de Rutinas -->
        <div class="routines-grid">
            <?php if (empty($routines)): ?>
                <div class="empty-state text-center py-5">
                    <div class="empty-icon mb-4">
                        <i class="fas fa-dumbbell fa-4x text-muted"></i>
                    </div>
                    <h3 class="empty-title">No hay rutinas disponibles</h3>
                    <p class="empty-text text-muted mb-4">
                        <?php if ($isClient): ?>
                            Aún no tienes rutinas asignadas. Contacta con tu instructor para obtener tu rutina personalizada.
                        <?php else: ?>
                            Comienza creando tu primera rutina personalizada.
                        <?php endif; ?>
                    </p>
                    
                    <?php if (!$isClient): ?>
                    <a href="/routines/create" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Crear Primera Rutina
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($routines as $routine): ?>
                    <div class="col-lg-6 col-xl-4">
                        <div class="routine-card">
                            <div class="routine-header">
                                <div class="routine-meta">
                                    <span class="badge badge-<?= $routine['objective'] ?>">
                                        <?= ucfirst(str_replace('_', ' ', $routine['objective'])) ?>
                                    </span>
                                    <span class="badge badge-outline-<?= $routine['difficulty_level'] ?>">
                                        <?= ucfirst($routine['difficulty_level']) ?>
                                    </span>
                                </div>
                                
                                <?php if (!$isClient): ?>
                                <div class="routine-actions dropdown">
                                    <button class="btn btn-sm btn-ghost" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="/routines/view/<?= $routine['id'] ?>">
                                            <i class="fas fa-eye me-2"></i>Ver Detalle
                                        </a></li>
                                        <li><a class="dropdown-item" href="/routines/edit/<?= $routine['id'] ?>">
                                            <i class="fas fa-edit me-2"></i>Editar
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmDeleteRoutine(<?= $routine['id'] ?>)">
                                            <i class="fas fa-trash me-2"></i>Eliminar
                                        </a></li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="routine-body">
                                <h3 class="routine-title">
                                    <a href="/routines/view/<?= $routine['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($routine['name']) ?>
                                    </a>
                                </h3>
                                
                                <p class="routine-description">
                                    <?= htmlspecialchars(substr($routine['description'], 0, 120)) ?>
                                    <?= strlen($routine['description']) > 120 ? '...' : '' ?>
                                </p>
                                
                                <div class="routine-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span><?= $routine['duration_weeks'] ?> semanas</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-repeat"></i>
                                        <span><?= $routine['sessions_per_week'] ?>/semana</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?= $routine['estimated_duration_minutes'] ?> min</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-list"></i>
                                        <span><?= $routine['exercise_count'] ?? 0 ?> ejercicios</span>
                                    </div>
                                </div>
                                
                                <?php if ($isInstructor || $isAdmin): ?>
                                <div class="routine-client">
                                    <?php if (!empty($routine['client_first_name'])): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            <?= htmlspecialchars($routine['client_first_name'] . ' ' . $routine['client_last_name']) ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">
                                            <i class="fas fa-template me-1"></i>Plantilla pública
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <?php elseif ($isClient && !empty($routine['instructor_first_name'])): ?>
                                <div class="routine-instructor">
                                    <small class="text-muted">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>
                                        Instructor: <?= htmlspecialchars($routine['instructor_first_name'] . ' ' . $routine['instructor_last_name']) ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="routine-footer">
                                <div class="routine-date">
                                    <small class="text-muted">
                                        Creada: <?= date('d/m/Y', strtotime($routine['created_at'])) ?>
                                    </small>
                                </div>
                                
                                <div class="routine-cta">
                                    <a href="/routines/view/<?= $routine['id'] ?>" class="btn btn-primary btn-sm">
                                        <?= $isClient ? 'Ver Rutina' : 'Ver Detalles' ?>
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Paginación -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <nav class="pagination-wrapper mt-5" aria-label="Paginación de rutinas">
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

        <!-- Rutinas Públicas/Plantillas -->
        <?php if (!empty($publicRoutines) && $isClient): ?>
        <section id="plantillas" class="public-routines-section mt-5 pt-5">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Rutinas Recomendadas</h2>
                <p class="section-subtitle">Explora nuestras rutinas profesionales diseñadas por expertos</p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($publicRoutines as $routine): ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="routine-card routine-template">
                        <div class="routine-header">
                            <div class="routine-meta">
                                <span class="badge badge-<?= $routine['objective'] ?>">
                                    <?= ucfirst(str_replace('_', ' ', $routine['objective'])) ?>
                                </span>
                                <span class="badge badge-template">
                                    <i class="fas fa-star me-1"></i>Plantilla
                                </span>
                            </div>
                        </div>
                        
                        <div class="routine-body">
                            <h3 class="routine-title">
                                <a href="/routines/view/<?= $routine['id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($routine['name']) ?>
                                </a>
                            </h3>
                            
                            <p class="routine-description">
                                <?= htmlspecialchars(substr($routine['description'], 0, 100)) ?>...
                            </p>
                            
                            <div class="routine-stats">
                                <div class="stat-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?= $routine['duration_weeks'] ?> semanas</span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-list"></i>
                                    <span><?= $routine['exercise_count'] ?? 0 ?> ejercicios</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="routine-footer">
                            <a href="/routines/view/<?= $routine['id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                                Ver Plantilla
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteRoutineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta rutina?</p>
                <p class="text-muted small">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
let routineToDelete = null;

function confirmDeleteRoutine(routineId) {
    routineToDelete = routineId;
    new bootstrap.Modal(document.getElementById('deleteRoutineModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (routineToDelete) {
        window.location.href = '/routines/delete/' + routineToDelete;
    }
});
</script>