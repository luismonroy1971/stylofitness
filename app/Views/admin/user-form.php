<?php
use StyleFitness\Helpers\AppHelper;

// Verificar que el usuario esté autenticado y sea admin
if (!AppHelper::isLoggedIn() || AppHelper::getCurrentUser()['role'] !== 'admin') {
    AppHelper::redirect('/login');
    exit;
}

$currentUser = AppHelper::getCurrentUser();
$isEdit = isset($user) && !empty($user);
$formTitle = $isEdit ? 'Editar Usuario' : 'Crear Usuario';
$formAction = $isEdit ? "/admin/users/update/{$user['id']}" : '/admin/users/store';

// Obtener datos del formulario (para repoblar en caso de error)
$formData = $_SESSION['admin_form_data'] ?? [];
$errors = $_SESSION['admin_errors'] ?? [];

// Limpiar datos de sesión
unset($_SESSION['admin_form_data'], $_SESSION['admin_errors']);

// Si es edición, usar datos del usuario
if ($isEdit && empty($formData)) {
    $formData = $user;
}

$pageTitle = $formTitle . ' - STYLOFITNESS';
$additionalCSS = ['admin.css'];
$additionalJS = ['admin-user-form.js'];
?>

<div class="admin-user-form-page">
    <style>
        .admin-user-form-page {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
        
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-group label.required::after {
            content: ' *';
            color: #e74c3c;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-control.error {
            border-color: #e74c3c;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #1f5f8b);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #fadbd8;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
        
        .form-help {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .password-toggle {
            position: relative;
        }
        
        .password-toggle .toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #7f8c8d;
            cursor: pointer;
            padding: 5px;
        }
        
        .section-title {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }
    </style>

    <!-- Header de la página -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-<?= $isEdit ? 'edit' : 'plus' ?>"></i>
            <?= $formTitle ?>
        </h1>
        <a href="/admin/users" class="btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver a Usuarios
        </a>
    </div>

    <!-- Formulario -->
    <div class="form-container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $formAction ?>" id="userForm">
            <div class="section-title">Información Personal</div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name" class="required">Nombre</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" 
                           value="<?= htmlspecialchars($formData['first_name'] ?? '') ?>" required>
                    <div class="form-help">Nombre del usuario</div>
                </div>
                
                <div class="form-group">
                    <label for="last_name" class="required">Apellido</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" 
                           value="<?= htmlspecialchars($formData['last_name'] ?? '') ?>" required>
                    <div class="form-help">Apellido del usuario</div>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="username" class="required">Nombre de Usuario</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?= htmlspecialchars($formData['username'] ?? '') ?>" required>
                    <div class="form-help">Debe ser único en el sistema</div>
                </div>
                
                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                    <div class="form-help">Dirección de correo electrónico</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="phone">Teléfono</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       value="<?= htmlspecialchars($formData['phone'] ?? '') ?>">
                <div class="form-help">Número de teléfono (opcional)</div>
            </div>

            <div class="section-title">Credenciales</div>
            
            <div class="form-group password-toggle">
                <label for="password" class="<?= $isEdit ? '' : 'required' ?>">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" 
                       <?= $isEdit ? '' : 'required' ?>>
                <button type="button" class="toggle-btn" onclick="togglePassword('password')">
                    <i class="fas fa-eye" id="password-eye"></i>
                </button>
                <div class="form-help">
                    <?= $isEdit ? 'Dejar en blanco para mantener la contraseña actual' : 'Mínimo 6 caracteres' ?>
                </div>
            </div>

            <div class="section-title">Configuración de Cuenta</div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="role" class="required">Rol</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">Seleccionar rol</option>
                        <option value="client" <?= ($formData['role'] ?? '') === 'client' ? 'selected' : '' ?>>Cliente</option>
                        <option value="trainer" <?= ($formData['role'] ?? '') === 'trainer' ? 'selected' : '' ?>>Entrenador</option>
                        <option value="admin" <?= ($formData['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                    <div class="form-help">Nivel de acceso del usuario</div>
                </div>
                
                <div class="form-group">
                    <label for="gym_id">Gimnasio</label>
                    <select id="gym_id" name="gym_id" class="form-control">
                        <option value="1" <?= ($formData['gym_id'] ?? 1) == 1 ? 'selected' : '' ?>>StyloFitness Principal</option>
                        <!-- Aquí se pueden agregar más gimnasios dinámicamente -->
                    </select>
                    <div class="form-help">Gimnasio al que pertenece</div>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="membership_type">Tipo de Membresía</label>
                    <select id="membership_type" name="membership_type" class="form-control">
                        <option value="basic" <?= ($formData['membership_type'] ?? 'basic') === 'basic' ? 'selected' : '' ?>>Básica</option>
                        <option value="premium" <?= ($formData['membership_type'] ?? '') === 'premium' ? 'selected' : '' ?>>Premium</option>
                        <option value="vip" <?= ($formData['membership_type'] ?? '') === 'vip' ? 'selected' : '' ?>>VIP</option>
                    </select>
                    <div class="form-help">Tipo de membresía del usuario</div>
                </div>
                
                <div class="form-group">
                    <label for="membership_expires">Fecha de Expiración</label>
                    <input type="date" id="membership_expires" name="membership_expires" class="form-control" 
                           value="<?= htmlspecialchars($formData['membership_expires'] ?? '') ?>">
                    <div class="form-help">Fecha de vencimiento de la membresía</div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" 
                           <?= ($formData['is_active'] ?? true) ? 'checked' : '' ?>>
                    <label for="is_active">Usuario Activo</label>
                </div>
                <div class="form-help">Los usuarios inactivos no pueden acceder al sistema</div>
            </div>

            <div class="form-actions">
                <a href="/admin/users" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    <?= $isEdit ? 'Actualizar Usuario' : 'Crear Usuario' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}

// Validación del formulario
document.getElementById('userForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
    
    if (!isEdit && password.length < 6) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 6 caracteres');
        return false;
    }
    
    if (isEdit && password.length > 0 && password.length < 6) {
        e.preventDefault();
        alert('Si cambias la contraseña, debe tener al menos 6 caracteres');
        return false;
    }
});
</script>