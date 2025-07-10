<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista de Administración de Instructores - STYLOFITNESS
 * Gestión de instructores desde el panel de administración
 */
?>

<div class="admin-layout full-width">
<div class="admin-container">
    <?php include APP_PATH . '/Views/admin/partials/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-user-tie"></i> Gestión de Instructores</h1>
            <div class="admin-actions">
                <a href="/admin/instructors/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Instructor
                </a>
            </div>
        </div>
        
        <div class="admin-filters">
            <form action="/admin/instructors" method="GET" class="filter-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Buscar instructores..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                <div class="filter-group">
                    <select name="specialty">
                        <option value="">Todas las especialidades</option>
                        <option value="strength" <?= ($filters['specialty'] ?? '') === 'strength' ? 'selected' : '' ?>>Fuerza</option>
                        <option value="cardio" <?= ($filters['specialty'] ?? '') === 'cardio' ? 'selected' : '' ?>>Cardio</option>
                        <option value="yoga" <?= ($filters['specialty'] ?? '') === 'yoga' ? 'selected' : '' ?>>Yoga</option>
                        <option value="pilates" <?= ($filters['specialty'] ?? '') === 'pilates' ? 'selected' : '' ?>>Pilates</option>
                        <option value="crossfit" <?= ($filters['specialty'] ?? '') === 'crossfit' ? 'selected' : '' ?>>CrossFit</option>
                        <option value="dance" <?= ($filters['specialty'] ?? '') === 'dance' ? 'selected' : '' ?>>Baile</option>
                        <option value="nutrition" <?= ($filters['specialty'] ?? '') === 'nutrition' ? 'selected' : '' ?>>Nutrición</option>
                        <option value="rehabilitation" <?= ($filters['specialty'] ?? '') === 'rehabilitation' ? 'selected' : '' ?>>Rehabilitación</option>
                        <option value="other" <?= ($filters['specialty'] ?? '') === 'other' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="status">
                        <option value="">Todos los estados</option>
                        <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-filter">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="/admin/instructors" class="btn btn-clear">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </form>
        </div>
        
        <div class="admin-table-container instructors-table-container">
            <table class="admin-table instructors-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Especialidad</th>
                        <th>Clases</th>
                        <th>Clientes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($instructors)): ?>
                        <tr>
                            <td colspan="9" class="no-results">No se encontraron instructores</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($instructors as $instructor): ?>
                            <tr>
                                <td><?= $instructor['id'] ?></td>
                                <td>
                                    <div class="table-image">
                                        <img src="<?= htmlspecialchars($instructor['profile_image'] ?: AppHelper::asset('images/placeholder.jpg')) ?>" alt="<?= htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']) ?>">
                                    </div>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <span class="user-name"><?= htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']) ?></span>
                                        <span class="user-since">Desde: <?= date('d/m/Y', strtotime($instructor['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($instructor['email']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($instructor['specialty'] ?? 'General')) ?></td>
                                <td><?= $instructor['class_count'] ?? 0 ?></td>
                                <td><?= $instructor['client_count'] ?? 0 ?></td>
                                <td>
                                    <span class="status-badge <?= $instructor['is_active'] ? 'active' : 'inactive' ?>">
                                        <?= $instructor['is_active'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/admin/instructors/edit/<?= $instructor['id'] ?>" class="btn-action edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/admin/instructors/view/<?= $instructor['id'] ?>" class="btn-action view" title="Ver perfil">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/instructors/delete/<?= $instructor['id'] ?>" class="btn-action delete" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este instructor?')">
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
                        <a href="/admin/instructors?page=<?= $pagination['previous_page'] ?>&<?= http_build_query(array_filter($filters, function($key) { return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>" class="pagination-link">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    <?php endif; ?>
                    
                    <span class="pagination-info">
                        Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?>
                    </span>
                    
                    <?php if ($pagination['has_next']): ?>
                        <a href="/admin/instructors?page=<?= $pagination['next_page'] ?>&<?= http_build_query(array_filter($filters, function($key) { return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>" class="pagination-link">
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
        // Inicializar el gestor de instructores
        const adminInstructors = new AdminInstructorsManager();
    });
</script>