<?php
$title = 'Crear Nueva Sala';
$pageTitle = 'Nueva Sala';
include '../app/Views/layouts/header.php';

$formData = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus"></i> Crear Nueva Sala
            </h1>
            <p class="text-muted">Configura una nueva sala para clases grupales</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="/rooms" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Salas
            </a>
        </div>
    </div>

    <form method="POST" action="/rooms/store" id="roomForm">
        <div class="row">
            <!-- Información básica -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i> Información Básica
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gym_id" class="form-label required">Gimnasio</label>
                                    <select class="form-control <?= isset($errors['gym_id']) ? 'is-invalid' : '' ?>" 
                                            id="gym_id" name="gym_id" required>
                                        <option value="">Seleccionar gimnasio...</option>
                                        <?php foreach ($gyms as $gym): ?>
                                            <option value="<?= $gym['id'] ?>" 
                                                    <?= ($formData['gym_id'] ?? '') == $gym['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($gym['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['gym_id'])): ?>
                                        <div class="invalid-feedback"><?= $errors['gym_id'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label required">Nombre de la Sala</label>
                                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                           id="name" name="name" 
                                           value="<?= htmlspecialchars($formData['name'] ?? '') ?>" 
                                           placeholder="Ej: Sala de Yoga Principal" required>
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Descripción opcional de la sala..."><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= $errors['description'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_type" class="form-label required">Tipo de Sala</label>
                                    <select class="form-control <?= isset($errors['room_type']) ? 'is-invalid' : '' ?>" 
                                            id="room_type" name="room_type" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <?php foreach ($roomTypes as $type => $label): ?>
                                            <option value="<?= $type ?>" 
                                                    <?= ($formData['room_type'] ?? '') === $type ? 'selected' : '' ?>
                                                    data-description="<?= $type === 'positioned' ? 'Permite selección de posiciones específicas como en un cine' : 'Solo controla el aforo total, ideal para CrossFit o clases dinámicas' ?>">
                                                <?= htmlspecialchars($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small id="roomTypeHelp" class="form-text text-muted"></small>
                                    <?php if (isset($errors['room_type'])): ?>
                                        <div class="invalid-feedback"><?= $errors['room_type'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_capacity" class="form-label required">Capacidad Total</label>
                                    <input type="number" class="form-control <?= isset($errors['total_capacity']) ? 'is-invalid' : '' ?>" 
                                           id="total_capacity" name="total_capacity" 
                                           value="<?= htmlspecialchars($formData['total_capacity'] ?? '') ?>" 
                                           min="1" max="500" placeholder="Ej: 30" required>
                                    <small class="form-text text-muted">Número máximo de personas</small>
                                    <?php if (isset($errors['total_capacity'])): ?>
                                        <div class="invalid-feedback"><?= $errors['total_capacity'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dimensions" class="form-label">Dimensiones</label>
                                    <input type="text" class="form-control" 
                                           id="dimensions" name="dimensions" 
                                           value="<?= htmlspecialchars($formData['dimensions'] ?? '') ?>" 
                                           placeholder="Ej: 10m x 8m">
                                    <small class="form-text text-muted">Dimensiones físicas de la sala</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location_notes" class="form-label">Ubicación</label>
                                    <input type="text" class="form-control" 
                                           id="location_notes" name="location_notes" 
                                           value="<?= htmlspecialchars($formData['location_notes'] ?? '') ?>" 
                                           placeholder="Ej: Segundo piso, ala norte">
                                    <small class="form-text text-muted">Notas sobre la ubicación</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración avanzada -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cogs"></i> Configuración Avanzada
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="floor_plan_image" class="form-label">Imagen del Plano</label>
                            <input type="url" class="form-control" 
                                   id="floor_plan_image" name="floor_plan_image" 
                                   value="<?= htmlspecialchars($formData['floor_plan_image'] ?? '') ?>" 
                                   placeholder="https://ejemplo.com/plano-sala.jpg">
                            <small class="form-text text-muted">URL de la imagen del plano de la sala (opcional)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Equipamiento Disponible</label>
                                    <div class="equipment-checkboxes">
                                        <?php 
                                        $equipmentOptions = [
                                            'mats' => 'Colchonetas',
                                            'weights' => 'Pesas',
                                            'mirrors' => 'Espejos',
                                            'sound_system' => 'Sistema de Sonido',
                                            'air_conditioning' => 'Aire Acondicionado',
                                            'projector' => 'Proyector',
                                            'storage' => 'Almacenamiento',
                                            'water_dispenser' => 'Dispensador de Agua'
                                        ];
                                        $selectedEquipment = $formData['equipment_available'] ?? [];
                                        ?>
                                        <?php foreach ($equipmentOptions as $value => $label): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="equipment_<?= $value ?>" 
                                                       name="equipment_available[]" 
                                                       value="<?= $value ?>"
                                                       <?= in_array($value, $selectedEquipment) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="equipment_<?= $value ?>">
                                                    <?= $label ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Amenidades</label>
                                    <div class="amenities-checkboxes">
                                        <?php 
                                        $amenitiesOptions = [
                                            'lockers' => 'Casilleros',
                                            'showers' => 'Duchas',
                                            'changing_room' => 'Vestidores',
                                            'parking' => 'Estacionamiento',
                                            'wifi' => 'WiFi',
                                            'natural_light' => 'Luz Natural',
                                            'ventilation' => 'Ventilación',
                                            'accessibility' => 'Accesibilidad'
                                        ];
                                        $selectedAmenities = $formData['amenities'] ?? [];
                                        ?>
                                        <?php foreach ($amenitiesOptions as $value => $label): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="amenity_<?= $value ?>" 
                                                       name="amenities[]" 
                                                       value="<?= $value ?>"
                                                       <?= in_array($value, $selectedAmenities) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="amenity_<?= $value ?>">
                                                    <?= $label ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel lateral -->
            <div class="col-md-4">
                <!-- Estado -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-toggle-on"></i> Estado
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" name="is_active" 
                                   <?= ($formData['is_active'] ?? true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                Sala Activa
                            </label>
                            <small class="form-text text-muted d-block">
                                Las salas inactivas no aparecerán disponibles para nuevas clases
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Información del tipo de sala -->
                <div class="card mb-4" id="roomTypeInfo" style="display: none;">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-info-circle"></i> Información del Tipo
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="positionedInfo" style="display: none;">
                            <h6 class="text-primary">
                                <i class="fas fa-th"></i> Sala con Posiciones Específicas
                            </h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success"></i> Selección visual de asientos</li>
                                <li><i class="fas fa-check text-success"></i> Control individual de posiciones</li>
                                <li><i class="fas fa-check text-success"></i> Ideal para yoga, pilates, spinning</li>
                                <li><i class="fas fa-check text-success"></i> Interfaz similar a cines</li>
                            </ul>
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-lightbulb"></i>
                                    Después de crear la sala, podrás configurar las posiciones específicas.
                                </small>
                            </div>
                        </div>
                        <div id="capacityInfo" style="display: none;">
                            <h6 class="text-success">
                                <i class="fas fa-users"></i> Sala Solo de Aforo
                            </h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success"></i> Control solo por capacidad total</li>
                                <li><i class="fas fa-check text-success"></i> Sin posiciones específicas</li>
                                <li><i class="fas fa-check text-success"></i> Ideal para CrossFit, aeróbicos</li>
                                <li><i class="fas fa-check text-success"></i> Reservas más flexibles</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-save"></i> Crear Sala
                        </button>
                        <a href="/rooms" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomTypeSelect = document.getElementById('room_type');
    const roomTypeInfo = document.getElementById('roomTypeInfo');
    const positionedInfo = document.getElementById('positionedInfo');
    const capacityInfo = document.getElementById('capacityInfo');
    const roomTypeHelp = document.getElementById('roomTypeHelp');

    function updateRoomTypeInfo() {
        const selectedType = roomTypeSelect.value;
        const selectedOption = roomTypeSelect.querySelector(`option[value="${selectedType}"]`);
        
        if (selectedType) {
            roomTypeInfo.style.display = 'block';
            roomTypeHelp.textContent = selectedOption ? selectedOption.dataset.description : '';
            
            if (selectedType === 'positioned') {
                positionedInfo.style.display = 'block';
                capacityInfo.style.display = 'none';
            } else if (selectedType === 'capacity') {
                positionedInfo.style.display = 'none';
                capacityInfo.style.display = 'block';
            }
        } else {
            roomTypeInfo.style.display = 'none';
            roomTypeHelp.textContent = '';
        }
    }

    roomTypeSelect.addEventListener('change', updateRoomTypeInfo);
    
    // Inicializar en caso de que haya un valor preseleccionado
    updateRoomTypeInfo();

    // Validación del formulario
    document.getElementById('roomForm').addEventListener('submit', function(e) {
        const requiredFields = ['gym_id', 'name', 'room_type', 'total_capacity'];
        let isValid = true;

        requiredFields.forEach(fieldName => {
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
            alert('Por favor completa todos los campos requeridos.');
        }
    });
});
</script>

<style>
.required::after {
    content: ' *';
    color: #dc3545;
}

.equipment-checkboxes,
.amenities-checkboxes {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.75rem;
}

.form-check {
    margin-bottom: 0.5rem;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
}

.btn-block {
    width: 100%;
}
</style>

<?php include '../app/Views/layouts/footer.php'; ?>