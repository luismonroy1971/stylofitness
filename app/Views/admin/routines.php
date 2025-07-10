<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista de Administración de Rutinas - STYLOFITNESS
 * Gestión de rutinas desde el panel de administración
 */
?>

<div class="admin-layout full-width">
<div class="admin-container">
    <?php include APP_PATH . '/Views/admin/partials/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-list-alt"></i> Gestión de Rutinas</h1>
            <div class="admin-actions">
                <a href="/admin/routines/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Rutina
                </a>
            </div>
        </div>
        
        <div class="admin-filters">
            <form action="/admin/routines" method="GET" class="filter-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Buscar rutinas..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                <div class="filter-group">
                    <select name="objective">
                        <option value="">Todos los objetivos</option>
                        <option value="strength" <?= ($filters['objective'] ?? '') === 'strength' ? 'selected' : '' ?>>Fuerza</option>
                        <option value="hypertrophy" <?= ($filters['objective'] ?? '') === 'hypertrophy' ? 'selected' : '' ?>>Hipertrofia</option>
                        <option value="endurance" <?= ($filters['objective'] ?? '') === 'endurance' ? 'selected' : '' ?>>Resistencia</option>
                        <option value="weight_loss" <?= ($filters['objective'] ?? '') === 'weight_loss' ? 'selected' : '' ?>>Pérdida de peso</option>
                        <option value="toning" <?= ($filters['objective'] ?? '') === 'toning' ? 'selected' : '' ?>>Tonificación</option>
                        <option value="rehabilitation" <?= ($filters['objective'] ?? '') === 'rehabilitation' ? 'selected' : '' ?>>Rehabilitación</option>
                        <option value="general" <?= ($filters['objective'] ?? '') === 'general' ? 'selected' : '' ?>>General</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="difficulty">
                        <option value="">Todas las dificultades</option>
                        <option value="beginner" <?= ($filters['difficulty'] ?? '') === 'beginner' ? 'selected' : '' ?>>Principiante</option>
                        <option value="intermediate" <?= ($filters['difficulty'] ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermedio</option>
                        <option value="advanced" <?= ($filters['difficulty'] ?? '') === 'advanced' ? 'selected' : '' ?>>Avanzado</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="is_template">
                        <option value="">Todos los tipos</option>
                        <option value="1" <?= ($filters['is_template'] ?? '') === '1' ? 'selected' : '' ?>>Plantillas</option>
                        <option value="0" <?= ($filters['is_template'] ?? '') === '0' ? 'selected' : '' ?>>Rutinas personalizadas</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-filter">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="/admin/routines" class="btn btn-clear">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </form>
        </div>
        
        <div class="admin-table-container routines-table-container">
            <table class="admin-table routines-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Objetivo</th>
                        <th>Dificultad</th>
                        <th>Tipo</th>
                        <th>Creador</th>
                        <th>Ejercicios</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($routines)): ?>
                        <tr>
                            <td colspan="9" class="no-results">No se encontraron rutinas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($routines as $routine): ?>
                            <tr>
                                <td><?= $routine['id'] ?></td>
                                <td><?= htmlspecialchars($routine['name']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($routine['objective'])) ?></td>
                                <td><?= htmlspecialchars(ucfirst($routine['difficulty'] ?? 'No definida')) ?></td>
                                <td>
                                    <span class="badge <?= $routine['is_template'] ? 'badge-primary' : 'badge-secondary' ?>">
                                        <?= $routine['is_template'] ? 'Plantilla' : 'Personalizada' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($routine['creator_name'] ?? 'Sistema') ?></td>
                                <td><?= $routine['exercise_count'] ?? 0 ?></td>
                                <td>
                                    <span class="status-badge <?= $routine['is_active'] ? 'active' : 'inactive' ?>">
                                        <?= $routine['is_active'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/admin/routines/edit/<?= $routine['id'] ?>" class="btn-action edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/routines/view/<?= $routine['id'] ?>" class="btn-action view" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/routines/duplicate/<?= $routine['id'] ?>" class="btn-action duplicate" title="Duplicar">
                                            <i class="fas fa-copy"></i>
                                        </a>
                                        <a href="/admin/routines/delete/<?= $routine['id'] ?>" class="btn-action delete" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta rutina?')">
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
                        <a href="/admin/routines?page=<?= $pagination['previous_page'] ?>&<?= http_build_query(array_filter($filters, function($key) { return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>" class="pagination-link">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    <?php endif; ?>
                    
                    <span class="pagination-info">
                        Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?>
                    </span>
                    
                    <?php if ($pagination['has_next']): ?>
                        <a href="/admin/routines?page=<?= $pagination['next_page'] ?>&<?= http_build_query(array_filter($filters, function($key) { return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>" class="pagination-link">
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
        // Inicializar el gestor de rutinas
        const adminRoutines = new AdminRoutinesManager();
    });
</script>