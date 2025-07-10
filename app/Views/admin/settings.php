<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista de Configuración del Sistema - STYLOFITNESS
 * Panel de configuración y ajustes del sistema
 */
?>

<div class="admin-layout full-width">
<div class="admin-container">
    <?php include APP_PATH . '/Views/admin/partials/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-cog"></i> Configuración del Sistema</h1>
            <div class="admin-actions">
                <button type="button" class="btn btn-success" onclick="saveAllSettings()">
                    <i class="fas fa-save"></i> Guardar Todos los Cambios
                </button>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php if (AppHelper::hasFlashMessage('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= AppHelper::getFlashMessage('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (AppHelper::hasFlashMessage('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= AppHelper::getFlashMessage('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulario de configuración -->
        <form method="POST" action="/admin/settings/update" id="settingsForm">
            <div class="settings-container">
                
                <!-- Configuración General -->
                <div class="settings-section">
                    <h3><i class="fas fa-globe"></i> Configuración General</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="site_name">Nombre del Sitio</label>
                                <input type="text" name="settings[site_name]" id="site_name" class="form-control" 
                                       value="<?= htmlspecialchars($settings['site_name'] ?? 'STYLOFITNESS') ?>">
                                <small class="form-text text-muted">Nombre que aparecerá en el título del sitio</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="site_description">Descripción del Sitio</label>
                                <input type="text" name="settings[site_description]" id="site_description" class="form-control" 
                                       value="<?= htmlspecialchars($settings['site_description'] ?? 'Tu gimnasio de confianza') ?>">
                                <small class="form-text text-muted">Descripción meta para SEO</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="admin_email">Email del Administrador</label>
                                <input type="email" name="settings[admin_email]" id="admin_email" class="form-control" 
                                       value="<?= htmlspecialchars($settings['admin_email'] ?? '') ?>">
                                <small class="form-text text-muted">Email para notificaciones del sistema</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="timezone">Zona Horaria</label>
                                <select name="settings[timezone]" id="timezone" class="form-control">
                                    <option value="America/Mexico_City" <?= ($settings['timezone'] ?? '') === 'America/Mexico_City' ? 'selected' : '' ?>>México (GMT-6)</option>
                                    <option value="America/New_York" <?= ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>Nueva York (GMT-5)</option>
                                    <option value="America/Los_Angeles" <?= ($settings['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' ?>>Los Ángeles (GMT-8)</option>
                                    <option value="Europe/Madrid" <?= ($settings['timezone'] ?? '') === 'Europe/Madrid' ? 'selected' : '' ?>>Madrid (GMT+1)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Membresías -->
                <div class="settings-section">
                    <h3><i class="fas fa-id-card"></i> Configuración de Membresías</h3>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basic_membership_price">Precio Membresía Básica</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="settings[basic_membership_price]" id="basic_membership_price" 
                                           class="form-control" step="0.01" 
                                           value="<?= htmlspecialchars($settings['basic_membership_price'] ?? '500.00') ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="premium_membership_price">Precio Membresía Premium</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="settings[premium_membership_price]" id="premium_membership_price" 
                                           class="form-control" step="0.01" 
                                           value="<?= htmlspecialchars($settings['premium_membership_price'] ?? '800.00') ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vip_membership_price">Precio Membresía VIP</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="settings[vip_membership_price]" id="vip_membership_price" 
                                           class="form-control" step="0.01" 
                                           value="<?= htmlspecialchars($settings['vip_membership_price'] ?? '1200.00') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="membership_duration">Duración de Membresía (días)</label>
                                <input type="number" name="settings[membership_duration]" id="membership_duration" 
                                       class="form-control" value="<?= htmlspecialchars($settings['membership_duration'] ?? '30') ?>">
                                <small class="form-text text-muted">Duración por defecto de las membresías</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="trial_period">Período de Prueba (días)</label>
                                <input type="number" name="settings[trial_period]" id="trial_period" 
                                       class="form-control" value="<?= htmlspecialchars($settings['trial_period'] ?? '7') ?>">
                                <small class="form-text text-muted">Días de prueba gratuita para nuevos usuarios</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Notificaciones -->
                <div class="settings-section">
                    <h3><i class="fas fa-bell"></i> Configuración de Notificaciones</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="settings[email_notifications]" id="email_notifications" 
                                       class="form-check-input" value="1" 
                                       <?= ($settings['email_notifications'] ?? '1') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="email_notifications">
                                    Notificaciones por Email
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="settings[sms_notifications]" id="sms_notifications" 
                                       class="form-check-input" value="1" 
                                       <?= ($settings['sms_notifications'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="sms_notifications">
                                    Notificaciones por SMS
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="settings[membership_expiry_alerts]" id="membership_expiry_alerts" 
                                       class="form-check-input" value="1" 
                                       <?= ($settings['membership_expiry_alerts'] ?? '1') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="membership_expiry_alerts">
                                    Alertas de Vencimiento de Membresía
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="settings[low_stock_alerts]" id="low_stock_alerts" 
                                       class="form-check-input" value="1" 
                                       <?= ($settings['low_stock_alerts'] ?? '1') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="low_stock_alerts">
                                    Alertas de Stock Bajo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Pagos -->
                <div class="settings-section">
                    <h3><i class="fas fa-credit-card"></i> Configuración de Pagos</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="currency">Moneda</label>
                                <select name="settings[currency]" id="currency" class="form-control">
                                    <option value="MXN" <?= ($settings['currency'] ?? 'MXN') === 'MXN' ? 'selected' : '' ?>>Peso Mexicano (MXN)</option>
                                    <option value="USD" <?= ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>Dólar Americano (USD)</option>
                                    <option value="EUR" <?= ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>Euro (EUR)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tax_rate">Tasa de Impuesto (%)</label>
                                <input type="number" name="settings[tax_rate]" id="tax_rate" 
                                       class="form-control" step="0.01" min="0" max="100" 
                                       value="<?= htmlspecialchars($settings['tax_rate'] ?? '16.00') ?>">
                                <small class="form-text text-muted">IVA o impuesto aplicable</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="settings[stripe_enabled]" id="stripe_enabled" 
                                       class="form-check-input" value="1" 
                                       <?= ($settings['stripe_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="stripe_enabled">
                                    Habilitar Stripe
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="settings[paypal_enabled]" id="paypal_enabled" 
                                       class="form-check-input" value="1" 
                                       <?= ($settings['paypal_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="paypal_enabled">
                                    Habilitar PayPal
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Mantenimiento -->
                <div class="settings-section">
                    <h3><i class="fas fa-tools"></i> Configuración de Mantenimiento</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="settings[maintenance_mode]" id="maintenance_mode" 
                                       class="form-check-input" value="1" 
                                       <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="maintenance_mode">
                                    Modo Mantenimiento
                                </label>
                                <small class="form-text text-muted">Activar para mostrar página de mantenimiento</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="settings[debug_mode]" id="debug_mode" 
                                       class="form-check-input" value="1" 
                                       <?= ($settings['debug_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="debug_mode">
                                    Modo Debug
                                </label>
                                <small class="form-text text-muted">Solo para desarrollo</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="maintenance_message">Mensaje de Mantenimiento</label>
                                <textarea name="settings[maintenance_message]" id="maintenance_message" 
                                          class="form-control" rows="3"><?= htmlspecialchars($settings['maintenance_message'] ?? 'Sitio en mantenimiento. Volveremos pronto.') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="settings-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-undo"></i> Restablecer
                    </button>
                    <button type="button" class="btn btn-warning" onclick="exportSettings()">
                        <i class="fas fa-download"></i> Exportar Configuración
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

<style>
.settings-container {
    max-width: 1000px;
}

.settings-section {
    background: white;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.settings-section h3 {
    margin-bottom: 25px;
    color: #333;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
}

.settings-section h3 i {
    margin-right: 10px;
    color: #007bff;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.form-check {
    margin-bottom: 15px;
}

.form-check-label {
    font-weight: 500;
    color: #333;
}

.settings-actions {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.settings-actions .btn {
    margin: 0 10px;
    min-width: 150px;
}
</style>

<script>
function saveAllSettings() {
    document.getElementById('settingsForm').submit();
}

function resetForm() {
    if (confirm('¿Estás seguro de que quieres restablecer todos los cambios?')) {
        document.getElementById('settingsForm').reset();
    }
}

function exportSettings() {
    window.open('/admin/settings/export', '_blank');
}

// Validación del formulario
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const requiredFields = ['site_name', 'admin_email'];
    let isValid = true;
    
    requiredFields.forEach(function(fieldName) {
        const field = document.getElementById(fieldName);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Por favor, completa todos los campos requeridos.');
    }
});

// Confirmación para modo mantenimiento
document.getElementById('maintenance_mode').addEventListener('change', function() {
    if (this.checked) {
        if (!confirm('¿Estás seguro de que quieres activar el modo mantenimiento? Esto hará que el sitio no esté disponible para los usuarios.')) {
            this.checked = false;
        }
    }
});
</script>