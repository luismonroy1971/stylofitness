<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Mis Rutinas - STYLOFITNESS
 * Muestra las rutinas asignadas al cliente
 */

$currentUser = AppHelper::getCurrentUser();
?>

<div class="my-routines-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title">
                        <i class="fas fa-dumbbell me-3"></i>
                        Mis Rutinas
                    </h1>
                    <p class="page-subtitle">Gestiona y sigue tus rutinas de entrenamiento</p>
                </div>
                <div class="col-auto">
                    <a href="/my-progress" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line me-2"></i>
                        Ver Mi Progreso
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="filters-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="search-box">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchRoutines" 
                                   placeholder="Buscar rutinas...">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-buttons">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="statusFilter" id="all" value="all" checked>
                            <label class="btn btn-outline-primary" for="all">Todas</label>
                            
                            <input type="radio" class="btn-check" name="statusFilter" id="active" value="active">
                            <label class="btn btn-outline-primary" for="active">Activas</label>
                            
                            <input type="radio" class="btn-check" name="statusFilter" id="completed" value="completed">
                            <label class="btn btn-outline-primary" for="completed">Completadas</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de rutinas -->
        <div class="routines-grid">
            <?php if (empty($routines)): ?>
                <div class="no-routines">
                    <div class="text-center py-5">
                        <i class="fas fa-dumbbell text-muted mb-4" style="font-size: 4rem;"></i>
                        <h4 class="text-muted">No tienes rutinas asignadas</h4>
                        <p class="text-muted">Contacta con tu entrenador para que te asigne una rutina personalizada.</p>
                        <a href="/profile" class="btn btn-primary mt-3">
                            <i class="fas fa-user me-2"></i>
                            Ir a Mi Perfil
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($routines as $routine): ?>
                        <div class="col-lg-6 col-xl-4 mb-4 routine-item" 
                             data-status="<?= $routine['is_active'] ? 'active' : 'completed' ?>">
                            <div class="routine-card">
                                <div class="routine-header">
                                    <div class="routine-status">
                                        <span class="badge bg-<?= $routine['is_active'] ? 'success' : 'secondary' ?>">
                                            <?= $routine['is_active'] ? 'Activa' : 'Completada' ?>
                                        </span>
                                    </div>
                                    <div class="routine-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="/routines/view/<?= $routine['id'] ?>">
                                                        <i class="fas fa-eye me-2"></i>
                                                        Ver Detalles
                                                    </a>
                                                </li>
                                                <?php if ($routine['is_active']): ?>
                                                <li>
                                                    <a class="dropdown-item" href="/routines/start/<?= $routine['id'] ?>">
                                                        <i class="fas fa-play me-2"></i>
                                                        Iniciar Entrenamiento
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="exportRoutine(<?= $routine['id'] ?>)">
                                                        <i class="fas fa-download me-2"></i>
                                                        Exportar
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="routine-content">
                                    <h5 class="routine-title"><?= htmlspecialchars($routine['name']) ?></h5>
                                    
                                    <?php if (!empty($routine['description'])): ?>
                                        <p class="routine-description"><?= htmlspecialchars($routine['description']) ?></p>
                                    <?php endif; ?>

                                    <div class="routine-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-calendar me-2"></i>
                                            <span>Creada: <?= date('d/m/Y', strtotime($routine['created_at'])) ?></span>
                                        </div>
                                        
                                        <?php if (!empty($routine['instructor_name'])): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-user-tie me-2"></i>
                                            <span>Entrenador: <?= htmlspecialchars($routine['instructor_name']) ?></span>
                                        </div>
                                        <?php endif; ?>

                                        <div class="meta-item">
                                            <i class="fas fa-clock me-2"></i>
                                            <span>Duración: <?= $routine['estimated_duration'] ?? 'No especificada' ?> min</span>
                                        </div>
                                    </div>

                                    <!-- Estadísticas de la rutina -->
                                    <div class="routine-stats">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="stat-value"><?= $routine['exercise_count'] ?? 0 ?></div>
                                                <div class="stat-label">Ejercicios</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-value"><?= $routine['total_sets'] ?? 0 ?></div>
                                                <div class="stat-label">Series</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-value"><?= $routine['difficulty_level'] ?? 'N/A' ?></div>
                                                <div class="stat-label">Nivel</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Progreso de la rutina -->
                                    <?php if ($routine['is_active'] && isset($routine['completion_percentage'])): ?>
                                    <div class="routine-progress">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="progress-label">Progreso</span>
                                            <span class="progress-percentage"><?= round($routine['completion_percentage']) ?>%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= round($routine['completion_percentage']) ?>%"
                                                 aria-valuenow="<?= round($routine['completion_percentage']) ?>" 
                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="routine-footer">
                                    <?php if ($routine['is_active']): ?>
                                        <a href="/routines/start/<?= $routine['id'] ?>" class="btn btn-primary w-100">
                                            <i class="fas fa-play me-2"></i>
                                            Iniciar Entrenamiento
                                        </a>
                                    <?php else: ?>
                                        <a href="/routines/view/<?= $routine['id'] ?>" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-eye me-2"></i>
                                            Ver Rutina
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.my-routines-container {
    padding: 2rem 0;
    min-height: calc(100vh - 200px);
}

.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.page-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #6c757d;
    margin-bottom: 0;
}

.filters-section {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.search-box .input-group-text {
    background: #f8f9fa;
    border-right: none;
}

.search-box .form-control {
    border-left: none;
    background: #f8f9fa;
}

.search-box .form-control:focus {
    background: white;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.filter-buttons {
    display: flex;
    justify-content: flex-end;
}

.btn-check:checked + .btn-outline-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
}

.routine-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.routine-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.routine-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 1.5rem 0;
}

.routine-content {
    padding: 1.5rem;
    flex-grow: 1;
}

.routine-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1rem;
}

.routine-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.routine-meta {
    margin-bottom: 1.5rem;
}

.meta-item {
    color: #6c757d;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.meta-item i {
    color: #667eea;
    width: 16px;
}

.routine-stats {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #667eea;
    display: block;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.routine-progress {
    margin-bottom: 1rem;
}

.progress-label {
    font-size: 0.9rem;
    color: #6c757d;
}

.progress-percentage {
    font-size: 0.9rem;
    font-weight: 600;
    color: #667eea;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background: #e9ecef;
}

.progress-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
}

.routine-footer {
    padding: 0 1.5rem 1.5rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.no-routines {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin: 2rem 0;
}

@media (max-width: 768px) {
    .my-routines-container {
        padding: 1rem 0;
    }
    
    .filters-section {
        padding: 1rem;
    }
    
    .filter-buttons {
        justify-content: center;
        margin-top: 1rem;
    }
    
    .routine-card {
        margin-bottom: 1rem;
    }
}
</style>

<script>
// Filtrado de rutinas
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchRoutines');
    const statusFilters = document.querySelectorAll('input[name="statusFilter"]');
    const routineItems = document.querySelectorAll('.routine-item');

    function filterRoutines() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = document.querySelector('input[name="statusFilter"]:checked').value;

        routineItems.forEach(item => {
            const title = item.querySelector('.routine-title').textContent.toLowerCase();
            const description = item.querySelector('.routine-description')?.textContent.toLowerCase() || '';
            const status = item.dataset.status;

            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesStatus = selectedStatus === 'all' || status === selectedStatus;

            if (matchesSearch && matchesStatus) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterRoutines);
    statusFilters.forEach(filter => {
        filter.addEventListener('change', filterRoutines);
    });
});

// Exportar rutina
function exportRoutine(routineId) {
    // Implementar exportación de rutina
    console.log('Exportando rutina:', routineId);
    // Aquí se puede implementar la lógica de exportación
}
</script>