<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista de Administración de Clases - STYLOFITNESS
 * Gestión de clases grupales desde el panel de administración
 */
?>

<div class="admin-layout full-width">
<div class="admin-container">
    <?php include APP_PATH . '/Views/admin/partials/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-dumbbell"></i> Gestión de Clases</h1>
            <div class="admin-actions">
                <a href="/admin/classes/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Clase
                </a>
            </div>
        </div>
        
        <div class="admin-filters">
            <form action="/admin/classes" method="GET" class="filter-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Buscar clases..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                <div class="filter-group">
                    <select name="class_type">
                        <option value="">Todos los tipos</option>
                        <option value="strength" <?= ($filters['class_type'] ?? '') === 'strength' ? 'selected' : '' ?>>Fuerza</option>
                        <option value="cardio" <?= ($filters['class_type'] ?? '') === 'cardio' ? 'selected' : '' ?>>Cardio</option>
                        <option value="flexibility" <?= ($filters['class_type'] ?? '') === 'flexibility' ? 'selected' : '' ?>>Flexibilidad</option>
                        <option value="hiit" <?= ($filters['class_type'] ?? '') === 'hiit' ? 'selected' : '' ?>>HIIT</option>
                        <option value="yoga" <?= ($filters['class_type'] ?? '') === 'yoga' ? 'selected' : '' ?>>Yoga</option>
                        <option value="pilates" <?= ($filters['class_type'] ?? '') === 'pilates' ? 'selected' : '' ?>>Pilates</option>
                        <option value="dance" <?= ($filters['class_type'] ?? '') === 'dance' ? 'selected' : '' ?>>Baile</option>
                        <option value="other" <?= ($filters['class_type'] ?? '') === 'other' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="instructor_id">
                        <option value="">Todos los instructores</option>
                        <?php foreach ($instructors as $instructor): ?>
                            <option value="<?= $instructor['id'] ?>" <?= ($filters['instructor_id'] ?? 0) == $instructor['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-filter">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="/admin/classes" class="btn btn-clear">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </form>
        </div>
        
        <div class="admin-table-container classes-table-container">
            <table class="admin-table classes-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Instructor</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="8" class="no-results">No se encontraron clases</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><?= $class['id'] ?></td>
                                <td>
                                    <div class="table-image">
                                        <img src="<?= htmlspecialchars($class['image_url'] ?: AppHelper::asset('images/placeholder.jpg')) ?>" alt="<?= htmlspecialchars($class['name']) ?>">
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($class['name']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($class['class_type'])) ?></td>
                                <td><?= htmlspecialchars($class['instructor_name'] ?? 'Sin asignar') ?></td>
                                <td><?= $class['capacity'] ?></td>
                                <td>
                                    <span class="status-badge <?= $class['is_active'] ? 'active' : 'inactive' ?>">
                                        <?= $class['is_active'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/admin/classes/edit/<?= $class['id'] ?>" class="btn-action edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/admin/classes/view/<?= $class['id'] ?>" class="btn-action view" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/classes/delete/<?= $class['id'] ?>" class="btn-action delete" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta clase?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
                <div class="pagination">
                    <?php if ($pagination['has_previous']): ?>
                        <a href="/admin/classes?page=<?= $pagination['previous_page'] ?>&<?= http_build_query(array_filter($filters, function($key) { return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>" class="pagination-link">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    <?php endif; ?>
                    
                    <span class="pagination-info">
                        Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?>
                    </span>
                    
                    <?php if ($pagination['has_next']): ?>
                        <a href="/admin/classes?page=<?= $pagination['next_page'] ?>&<?= http_build_query(array_filter($filters, function($key) { return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>" class="pagination-link">
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar el gestor de clases
        const adminClasses = new AdminClassesManager();
    });
</script>