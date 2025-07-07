<?php
use StyleFitness\Helpers\AppHelper;

// Verificar que el usuario esté autenticado y sea admin
if (!AppHelper::isLoggedIn() || AppHelper::getCurrentUser()['role'] !== 'admin') {
    AppHelper::redirect('/login');
    exit;
}

$user = AppHelper::getCurrentUser();
$pageTitle = 'Gestión de Usuarios - STYLOFITNESS';
$additionalCSS = ['admin.css'];
$additionalJS = ['admin-users.js'];
?>

<div class="admin-content admin-users-page">
    <style>
        .admin-users-page {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin-left: 0 !important;
        }
        
        .page-header {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            color: #2c3e50;
            margin: 0;
            font-size: 2.2em;
            font-weight: 300;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #1f5f8b);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .filters-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            color: #2c3e50;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .form-control {
            padding: 10px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .users-table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .users-table th {
            background: #34495e;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 500;
        }
        
        .users-table td {
            padding: 15px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .users-table tr:hover {
            background: #f8f9fa;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-details h4 {
            margin: 0;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .user-details p {
            margin: 2px 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .role-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .role-admin {
            background: #e8f5e8;
            color: #27ae60;
        }
        
        .role-trainer {
            background: #f0ad4e;
            color: #ffffff;
        }
        
        .role-client {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
        }
        
        .status-active {
            background: #d5f4e6;
            color: #27ae60;
        }
        
        .status-inactive {
            background: #fadbd8;
            color: #e74c3c;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background: #f39c12;
            color: white;
        }
        
        .btn-edit:hover {
            background: #e67e22;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c0392b;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            padding: 20px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
        }
        
        .pagination a {
            background: #ecf0f1;
            color: #2c3e50;
            transition: background 0.3s ease;
        }
        
        .pagination a:hover {
            background: #3498db;
            color: white;
        }
        
        .pagination .current {
            background: #3498db;
            color: white;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d5f4e6;
            color: #27ae60;
            border: 1px solid #27ae60;
        }
        
        .alert-error {
            background: #fadbd8;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
        
        /* Mejoras para dispositivos móviles */
        @media (max-width: 768px) {
            .admin-users-page {
                padding: 10px;
                margin-left: 0 !important;
            }
            
            .page-header {
                padding: 20px 15px;
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .page-title {
                font-size: 1.8em;
            }
            
            .filters-form {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .filters-section {
                padding: 15px;
            }
            
            .users-table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .users-table {
                min-width: 800px;
                font-size: 14px;
            }
            
            .users-table th,
            .users-table td {
                padding: 10px 8px;
            }
            
            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            
            .user-details h4 {
                font-size: 14px;
            }
            
            .user-details p {
                font-size: 12px;
            }
            
            .actions {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn-sm {
                padding: 8px 12px;
                font-size: 11px;
                width: 100%;
                text-align: center;
            }
            
            .pagination {
                flex-wrap: wrap;
                gap: 5px;
                padding: 15px;
            }
            
            .pagination a,
            .pagination span {
                padding: 6px 10px;
                font-size: 14px;
            }
            
            .role-badge,
            .status-badge {
                font-size: 0.7em;
                padding: 3px 8px;
            }
        }
        
        @media (max-width: 480px) {
            .admin-users-page {
                padding: 5px;
            }
            
            .page-header {
                padding: 15px 10px;
            }
            
            .page-title {
                font-size: 1.5em;
            }
            
            .btn-primary {
                padding: 10px 16px;
                font-size: 14px;
            }
            
            .filters-section {
                padding: 10px;
            }
            
            .users-table {
                font-size: 12px;
            }
            
            .users-table th,
            .users-table td {
                padding: 8px 5px;
            }
            
            .user-info {
                gap: 10px;
            }
            
            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }
        }
    </style>

    <!-- Header de la página -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-users"></i>
            Gestión de Usuarios
        </h1>
        <a href="/admin/users/create" class="btn-primary">
            <i class="fas fa-plus"></i>
            Nuevo Usuario
        </a>
    </div>

    <!-- Mensajes flash -->
    <?php if (AppHelper::hasFlashMessages()): ?>
        <?php $flashMessages = AppHelper::getFlashMessage(); ?>
        <?php foreach ($flashMessages as $type => $message): ?>
            <div class="alert alert-<?= $type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="filters-section">
        <form method="GET" class="filters-form">
            <div class="form-group">
                <label for="search">Buscar</label>
                <input type="text" id="search" name="search" class="form-control" 
                       placeholder="Nombre, email o username..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="role">Rol</label>
                <select id="role" name="role" class="form-control">
                    <option value="">Todos los roles</option>
                    <option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="trainer" <?= ($_GET['role'] ?? '') === 'trainer' ? 'selected' : '' ?>>Entrenador</option>
                    <option value="client" <?= ($_GET['role'] ?? '') === 'client' ? 'selected' : '' ?>>Cliente</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status">Estado</label>
                <select id="status" name="status" class="form-control">
                    <option value="">Todos los estados</option>
                    <option value="1" <?= ($_GET['status'] ?? '') === '1' ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= ($_GET['status'] ?? '') === '0' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-primary">Filtrar</button>
                <a href="/admin/users" class="btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Tabla de usuarios -->
    <div class="users-table-container">
        <?php include APP_PATH . '/Views/admin/users-table.php'; ?>
    </div>

    <!-- Paginación -->
    <?php include APP_PATH . '/Views/admin/users-pagination.php'; ?>
</div>

<script>
function deleteUser(userId) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
        fetch(`/admin/users/delete/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al eliminar el usuario: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el usuario');
        });
    }
}
</script>