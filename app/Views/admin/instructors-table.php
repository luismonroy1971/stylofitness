<?php
use StyleFitness\Helpers\AppHelper;
?>
<table class="instructors-table">
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
        <?php if (!empty($instructors)): ?>
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
        <?php else: ?>
            <tr>
                <td colspan="9" class="no-results">No se encontraron instructores</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>