<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista de Administración de Ejercicios - STYLOFITNESS
 * Gestión de ejercicios desde el panel de administración
 */
?>

<div class="admin-layout full-width">
<div class="admin-container">
    <?php include APP_PATH . '/Views/admin/partials/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-dumbbell"></i> Gestión de Ejercicios</h1>
            <div class="admin-actions">
                <a href="/admin/exercises/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Ejercicio
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="admin-filters">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Buscar ejercicios..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control">
                </div>
                
                <div class="filter-group">
                    <select name="category_id" class="form-control">
                        <option value="">Todas las categorías</option>
                        <?php if (isset($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= (($_GET['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <select name="difficulty" class="form-control">
                        <option value="">Todas las dificultades</option>
                        <option value="beginner" <?= (($_GET['difficulty'] ?? '') === 'beginner') ? 'selected' : '' ?>>Principiante</option>
                        <option value="intermediate" <?= (($_GET['difficulty'] ?? '') === 'intermediate') ? 'selected' : '' ?>>Intermedio</option>
                        <option value="advanced" <?= (($_GET['difficulty'] ?? '') === 'advanced') ? 'selected' : '' ?>>Avanzado</option>
                    </select>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="/admin/exercises" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabla de ejercicios -->
        <div class="admin-table-container">
            <div class="table-responsive">
                <table class="table table-striped admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Dificultad</th>
                            <th>Músculos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($exercises)): ?>
                            <?php foreach ($exercises as $exercise): ?>
                                <tr>
                                    <td><?= $exercise['id'] ?></td>
                                    <td>
                                        <?php if (!empty($exercise['image'])): ?>
                                            <img src="<?= AppHelper::asset($exercise['image']) ?>" 
                                                 alt="<?= htmlspecialchars($exercise['name']) ?>" 
                                                 class="exercise-thumbnail">
                                        <?php else: ?>
                                            <div class="no-image">Sin imagen</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($exercise['name']) ?></strong>
                                        <?php if (!empty($exercise['description'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars(substr($exercise['description'], 0, 100)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($exercise['category_name'] ?? 'Sin categoría') ?></td>
                                    <td>
                                        <span class="badge badge-<?= $exercise['difficulty'] === 'beginner' ? 'success' : ($exercise['difficulty'] === 'intermediate' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($exercise['difficulty']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($exercise['muscle_groups'])): ?>
                                            <?php $muscles = is_string($exercise['muscle_groups']) ? json_decode($exercise['muscle_groups'], true) : $exercise['muscle_groups']; ?>
                                            <?php if (is_array($muscles)): ?>
                                                <?= implode(', ', array_slice($muscles, 0, 3)) ?>
                                                <?php if (count($muscles) > 3): ?>
                                                    <small class="text-muted">+<?= count($muscles) - 3 ?> más</small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $exercise['is_active'] ? 'success' : 'secondary' ?>">
                                            <?= $exercise['is_active'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="/admin/exercises/view/<?= $exercise['id'] ?>" 
                                               class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/admin/exercises/edit/<?= $exercise['id'] ?>" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteExercise(<?= $exercise['id'] ?>)" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-dumbbell fa-3x text-muted mb-3"></i>
                                        <h5>No hay ejercicios</h5>
                                        <p class="text-muted">No se encontraron ejercicios con los filtros aplicados.</p>
                                        <a href="/admin/exercises/create" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Crear primer ejercicio
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
            <div class="admin-pagination">
                <nav aria-label="Paginación de ejercicios">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['has_previous']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['previous_page'] ?><?= http_build_query(array_filter($_GET, fn($k) => $k !== 'page'), '', '&') ?>">
                                    <i class="fas fa-chevron-left"></i> Anterior
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= http_build_query(array_filter($_GET, fn($k) => $k !== 'page'), '', '&') ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['next_page'] ?><?= http_build_query(array_filter($_GET, fn($k) => $k !== 'page'), '', '&') ?>">
                                    Siguiente <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <div class="pagination-info text-center mt-2">
                    <small class="text-muted">
                        Mostrando <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?> - 
                        <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total_items']) ?> 
                        de <?= $pagination['total_items'] ?> ejercicios
                    </small>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>

<script>
function deleteExercise(exerciseId) {
    if (confirm('¿Estás seguro de que quieres eliminar este ejercicio?')) {
        fetch(`/admin/exercises/delete/${exerciseId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error al eliminar el ejercicio');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el ejercicio');
        });
    }
}
</script>