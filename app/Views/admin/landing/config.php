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
        <h1><i class="fas fa-cogs"></i> Configuración de Landing Page</h1>
        <div class="admin-actions">
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Nueva Configuración
            </button>
            <button class="btn btn-success" onclick="exportConfig()">
                <i class="fas fa-download"></i> Exportar
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
                <i class="fas fa-cogs text-primary"></i>
            </div>
            <div class="stat-info">
                <h3 id="totalConfigs">0</h3>
                <p>Total Configuraciones</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-toggle-on text-success"></i>
            </div>
            <div class="stat-info">
                <h3 id="activeConfigs">0</h3>
                <p>Activas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-layer-group text-warning"></i>
            </div>
            <div class="stat-info">
                <h3 id="totalSections">0</h3>
                <p>Secciones Únicas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock text-info"></i>
            </div>
            <div class="stat-info">
                <h3 id="lastUpdate">-</h3>
                <p>Última Actualización</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <div class="filter-group">
            <label for="sectionFilter">Sección:</label>
            <select id="sectionFilter" class="form-control">
                <option value="">Todas las Secciones</option>
                <option value="hero">Hero/Banner Principal</option>
                <option value="stats">Estadísticas</option>
                <option value="features">Características</option>
                <option value="cta">Call to Action</option>
                <option value="footer">Footer</option>
                <option value="general">General</option>
            </select>
        </div>
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
            <input type="text" id="searchFilter" class="form-control" placeholder="Buscar por clave o valor...">
        </div>
        <div class="filter-group">
            <button class="btn btn-secondary" onclick="clearFilters()">
                <i class="fas fa-times"></i> Limpiar
            </button>
        </div>
    </div>

    <!-- Secciones de configuración -->
    <div class="config-sections">
        <!-- Hero Section -->
        <div class="config-section" id="heroSection">
            <div class="section-header">
                <h3><i class="fas fa-image"></i> Configuración Hero/Banner</h3>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleSection('heroSection')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="section-content">
                <div class="config-grid" id="heroConfigs">
                    <!-- Configuraciones dinámicas -->
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="config-section" id="statsSection">
            <div class="section-header">
                <h3><i class="fas fa-chart-bar"></i> Configuración Estadísticas</h3>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleSection('statsSection')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="section-content">
                <div class="config-grid" id="statsConfigs">
                    <!-- Configuraciones dinámicas -->
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="config-section" id="featuresSection">
            <div class="section-header">
                <h3><i class="fas fa-star"></i> Configuración Características</h3>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleSection('featuresSection')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="section-content">
                <div class="config-grid" id="featuresConfigs">
                    <!-- Configuraciones dinámicas -->
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="config-section" id="ctaSection">
            <div class="section-header">
                <h3><i class="fas fa-bullhorn"></i> Configuración Call to Action</h3>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleSection('ctaSection')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="section-content">
                <div class="config-grid" id="ctaConfigs">
                    <!-- Configuraciones dinámicas -->
                </div>
            </div>
        </div>

        <!-- General Section -->
        <div class="config-section" id="generalSection">
            <div class="section-header">
                <h3><i class="fas fa-cog"></i> Configuración General</h3>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleSection('generalSection')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="section-content">
                <div class="config-grid" id="generalConfigs">
                    <!-- Configuraciones dinámicas -->
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de todas las configuraciones -->
    <div class="table-container">
        <h3>Todas las Configuraciones</h3>
        <table class="admin-table" id="configsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sección</th>
                    <th>Clave</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Última Actualización</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="configsTableBody">
                <!-- Contenido dinámico -->
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="pagination-container" id="paginationContainer">
        <!-- Paginación dinámica -->
    </div>
</div>

<!-- Modal para crear/editar configuración -->
<div class="modal fade" id="configModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="configModalTitle">Nueva Configuración</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="configForm">
                <div class="modal-body">
                    <input type="hidden" id="configId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="configSection">Sección *</label>
                                <select class="form-control" id="configSection" name="section_name" required>
                                    <option value="">Seleccionar sección...</option>
                                    <option value="hero">Hero/Banner Principal</option>
                                    <option value="stats">Estadísticas</option>
                                    <option value="features">Características</option>
                                    <option value="cta">Call to Action</option>
                                    <option value="footer">Footer</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="configKey">Clave de Configuración *</label>
                                <input type="text" class="form-control" id="configKey" name="config_key" required placeholder="ej: hero_title, stats_enabled">
                                <small class="text-muted">Usar snake_case (ej: hero_title)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="configType">Tipo de Valor *</label>
                                <select class="form-control" id="configType" name="value_type" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="string">Texto (String)</option>
                                    <option value="number">Número</option>
                                    <option value="boolean">Booleano (true/false)</option>
                                    <option value="json">JSON</option>
                                    <option value="url">URL</option>
                                    <option value="color">Color</option>
                                    <option value="email">Email</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="configCategory">Categoría</label>
                                <input type="text" class="form-control" id="configCategory" name="category" placeholder="ej: appearance, content, behavior">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="configValue">Valor de Configuración *</label>
                        <div id="valueInputContainer">
                            <textarea class="form-control" id="configValue" name="config_value" rows="3" required></textarea>
                        </div>
                        <small class="text-muted" id="valueHelp">Ingrese el valor según el tipo seleccionado</small>
                    </div>

                    <div class="form-group">
                        <label for="configDescription">Descripción</label>
                        <textarea class="form-control" id="configDescription" name="description" rows="2" placeholder="Descripción de qué hace esta configuración..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="configDefault">Valor por Defecto</label>
                                <input type="text" class="form-control" id="configDefault" name="default_value" placeholder="Valor por defecto">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="configOrder">Orden de Visualización</label>
                                <input type="number" class="form-control" id="configOrder" name="display_order" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="configActive" name="is_active" value="1" checked>
                            <label class="form-check-label" for="configActive">
                                Configuración activa
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Configuración
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
                <p>¿Estás seguro de que deseas eliminar esta configuración?</p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer y puede afectar el funcionamiento de la landing page.</strong></p>
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
    max-width: 1400px;
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

.config-sections {
    margin-bottom: 30px;
}

.config-section {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.section-header {
    background: #f8f9fa;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    border-bottom: 1px solid #dee2e6;
}

.section-header h3 {
    margin: 0;
    color: #495057;
    font-size: 1.2rem;
}

.section-content {
    padding: 20px;
    display: none;
}

.section-content.active {
    display: block;
}

.config-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.config-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.config-item h5 {
    margin: 0 0 10px 0;
    color: #495057;
    font-size: 1rem;
}

.config-item .config-value {
    background: white;
    padding: 8px 12px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.9rem;
    border: 1px solid #dee2e6;
    margin-bottom: 10px;
}

.config-item .config-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: #6c757d;
}

.table-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 30px;
}

.table-container h3 {
    padding: 20px;
    margin: 0;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    color: #495057;
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

.type-badge {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.type-string { background: #e3f2fd; color: #1565c0; }
.type-number { background: #f3e5f5; color: #7b1fa2; }
.type-boolean { background: #e8f5e8; color: #2e7d32; }
.type-json { background: #fff3e0; color: #ef6c00; }
.type-url { background: #e0f2f1; color: #00695c; }
.type-color { background: #fce4ec; color: #c2185b; }
.type-email { background: #f1f8e9; color: #558b2f; }

.value-preview {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-family: monospace;
    font-size: 0.9rem;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
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
    max-width: 900px;
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
    
    .config-grid {
        grid-template-columns: 1fr;
    }
    
    .table-container {
        overflow-x: auto;
    }
}
</style>

<script src="/assets/js/admin/landing-config.js"></script>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>