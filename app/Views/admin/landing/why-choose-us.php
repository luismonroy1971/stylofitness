<?php
// Verificar autenticación de administrador
if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login');
    exit;
}

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-star"></i> Gestión de "Por Qué Elegirnos"</h1>
        <div class="admin-actions">
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Nueva Característica
            </button>
            <button class="btn btn-secondary" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
        </div>
    </div>

    <!-- Dashboard de estadísticas -->
    <div class="stats-dashboard">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star text-primary"></i>
            </div>
            <div class="stat-info">
                <h3 id="totalFeatures">0</h3>
                <p>Total Características</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-eye text-success"></i>
            </div>
            <div class="stat-info">
                <h3 id="activeFeatures">0</h3>
                <p>Activas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-eye-slash text-warning"></i>
            </div>
            <div class="stat-info">
                <h3 id="inactiveFeatures">0</h3>
                <p>Inactivas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-sort-numeric-up text-info"></i>
            </div>
            <div class="stat-info">
                <h3 id="avgOrder">0</h3>
                <p>Orden Promedio</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <div class="filter-group">
            <label for="statusFilter">Estado:</label>
            <select id="statusFilter" class="form-control">
                <option value="">Todos</option>
                <option value="1">Activos</option>
                <option value="0">Inactivos</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="searchFilter">Buscar:</label>
            <input type="text" id="searchFilter" class="form-control" placeholder="Buscar por título...">
        </div>
        <div class="filter-group">
            <button class="btn btn-secondary" onclick="clearFilters()">
                <i class="fas fa-times"></i> Limpiar
            </button>
        </div>
    </div>

    <!-- Tabla de características -->
    <div class="table-container">
        <table class="admin-table" id="featuresTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Subtítulo</th>
                    <th>Icono</th>
                    <th>Color</th>
                    <th>Orden</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="featuresTableBody">
                <!-- Contenido dinámico -->
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="pagination-container" id="paginationContainer">
        <!-- Paginación dinámica -->
    </div>
</div>

<!-- Modal para crear/editar característica -->
<div class="modal fade" id="featureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="featureModalTitle">Nueva Característica</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="featureForm">
                <div class="modal-body">
                    <input type="hidden" id="featureId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="featureTitle">Título *</label>
                                <input type="text" class="form-control" id="featureTitle" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="featureSubtitle">Subtítulo</label>
                                <input type="text" class="form-control" id="featureSubtitle" name="subtitle">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="featureDescription">Descripción</label>
                        <textarea class="form-control" id="featureDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="featureIcon">Icono (FontAwesome)</label>
                                <input type="text" class="form-control" id="featureIcon" name="icon" placeholder="fas fa-star">
                                <small class="text-muted">Ejemplo: fas fa-star, fas fa-heart</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="featureIconColor">Color del Icono</label>
                                <input type="color" class="form-control" id="featureIconColor" name="icon_color" value="#ff6b35">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="featureOrder">Orden de Visualización</label>
                                <input type="number" class="form-control" id="featureOrder" name="display_order" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="featureGradient">Gradiente de Fondo</label>
                        <input type="text" class="form-control" id="featureGradient" name="background_gradient" placeholder="linear-gradient(135deg, #ff6b35, #f7931e)">
                        <small class="text-muted">CSS gradient (opcional)</small>
                    </div>

                    <div class="form-group">
                        <label for="featureHighlights">Características Destacadas (JSON)</label>
                        <textarea class="form-control" id="featureHighlights" name="highlights" rows="3" placeholder='["Característica 1", "Característica 2"]'></textarea>
                        <small class="text-muted">Array JSON de características</small>
                    </div>

                    <div class="form-group">
                        <label for="featureStats">Estadísticas (JSON)</label>
                        <textarea class="form-control" id="featureStats" name="stats" rows="3" placeholder='{"clients": 1000, "satisfaction": 98}'></textarea>
                        <small class="text-muted">Objeto JSON con estadísticas</small>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="featureActive" name="is_active" value="1" checked>
                            <label class="form-check-label" for="featureActive">
                                Característica activa
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta característica?</p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.admin-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.admin-header h1 {
    color: #2c3e50;
    margin: 0;
    font-size: 2rem;
}

.admin-actions {
    display: flex;
    gap: 10px;
}

.stats-dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    font-size: 2rem;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f8f9fa;
}

.stat-info h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: bold;
    color: #2c3e50;
}

.stat-info p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.filters-section {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    display: flex;
    gap: 20px;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.filter-group label {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

.table-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.admin-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
}

.admin-table tbody tr:hover {
    background-color: #f8f9fa;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.8rem;
}

.pagination-container {
    padding: 20px;
    display: flex;
    justify-content: center;
}

.modal-lg {
    max-width: 800px;
}

.text-primary { color: #007bff !important; }
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-info { color: #17a2b8 !important; }
.text-danger { color: #dc3545 !important; }

@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .admin-actions {
        justify-content: center;
    }
    
    .filters-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .table-container {
        overflow-x: auto;
    }
}
</style>

<script src="/assets/js/admin/why-choose-us.js"></script>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>