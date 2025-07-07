<table class="users-table">
    <thead>
        <tr>
            <th>Usuario</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Membresía</th>
            <th>Fecha Registro</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $userItem): ?>
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">
                                <?= strtoupper(substr($userItem['first_name'] ?? $userItem['username'], 0, 1)) ?>
                            </div>
                            <div class="user-details">
                                <h4><?= htmlspecialchars($userItem['first_name'] . ' ' . $userItem['last_name']) ?></h4>
                                <p>@<?= htmlspecialchars($userItem['username']) ?></p>
                                <?php if (!empty($userItem['phone'])): ?>
                                    <p><?= htmlspecialchars($userItem['phone']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($userItem['email']) ?></td>
                    <td>
                        <span class="role-badge role-<?= $userItem['role'] ?>">
                            <?= ucfirst($userItem['role']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $userItem['is_active'] ? 'active' : 'inactive' ?>">
                            <?= $userItem['is_active'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <?= ucfirst($userItem['membership_type'] ?? 'basic') ?>
                        <?php if (!empty($userItem['membership_expires'])): ?>
                            <br><small>Expira: <?= date('d/m/Y', strtotime($userItem['membership_expires'])) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($userItem['created_at'])) ?></td>
                    <td>
                        <div class="actions">
                            <a href="/admin/users/edit/<?= $userItem['id'] ?>" class="btn-sm btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteUser(<?= $userItem['id'] ?>)" class="btn-sm btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #7f8c8d;">
                    <i class="fas fa-users" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                    No se encontraron usuarios
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>