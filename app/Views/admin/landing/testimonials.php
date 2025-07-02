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
        <h1><i class="fas fa-comments"></i> Gestión de Testimonios</h1>
        <div class="admin-actions">
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Nuevo Testimonio
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
                <i class="fas fa-comments text-primary"></i>
            </div>
            <div class="stat-info">
                <h3 id="totalTestimonials">0</h3>
                <p>Total Testimonios</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-eye text-success"></i>
            </div>
            <div class="stat-info">
                <h3 id="activeTestimonials">0</h3>
                <p>Activos</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star text-warning"></i>
            </div>
            <div class="stat-info">
                <h3 id="featuredTestimonials">0</h3>
                <p>Destacados</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star-half-alt text-info"></i>
            </div>
            <div class="stat-info">
                <h3 id="avgRating">0</h3>
                <p>Rating Promedio</p>
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
            <label for="featuredFilter">Destacados:</label>
            <select id="featuredFilter" class="form-control">
                <option value="">Todos</option>
                <option value="1">Destacados</option>
                <option value="0">No Destacados</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="ratingFilter">Rating Mínimo:</label>
            <select id="ratingFilter" class="form-control">
                <option value="">Todos</option>
                <option value="5">5 Estrellas</option>
                <option value="4">4+ Estrellas</option>
                <option value="3">3+ Estrellas</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="searchFilter">Buscar:</label>
            <input type="text" id="searchFilter" class="form-control" placeholder="Buscar por nombre o contenido...">
        </div>
        <div class="filter-group">
            <button class="btn btn-secondary" onclick="clearFilters()">
                <i class="fas fa-times"></i> Limpiar
            </button>
        </div>
    </div>

    <!-- Tabla de testimonios -->
    <div class="table-container">
        <table class="admin-table" id="testimonialsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Testimonio</th>
                    <th>Rating</th>
                    <th>Imagen</th>
                    <th>Destacado</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="testimonialsTableBody">
                <!-- Contenido dinámico -->
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="pagination-container" id="paginationContainer">
        <!-- Paginación dinámica -->
    </div>
</div>

<!-- Modal para crear/editar testimonio -->
<div class="modal fade" id="testimonialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testimonialModalTitle">Nuevo Testimonio</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="testimonialForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="testimonialId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientName">Nombre del Cliente *</label>
                                <input type="text" class="form-control" id="clientName" name="client_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientPosition">Cargo/Posición</label>
                                <input type="text" class="form-control" id="clientPosition" name="client_position" placeholder="Ej: CEO, Estudiante, etc.">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientCompany">Empresa/Organización</label>
                                <input type="text" class="form-control" id="clientCompany" name="client_company">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientLocation">Ubicación</label>
                                <input type="text" class="form-control" id="clientLocation" name="client_location" placeholder="Ciudad, País">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="testimonialContent">Contenido del Testimonio *</label>
                        <textarea class="form-control" id="testimonialContent" name="content" rows="4" required placeholder="Escribe aquí el testimonio del cliente..."></textarea>
                        <small class="text-muted">Máximo 500 caracteres</small>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="testimonialRating">Rating *</label>
                                <select class="form-control" id="testimonialRating" name="rating" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="5">⭐⭐⭐⭐⭐ (5 estrellas)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 estrellas)</option>
                                    <option value="3">⭐⭐⭐ (3 estrellas)</option>
                                    <option value="2">⭐⭐ (2 estrellas)</option>
                                    <option value="1">⭐ (1 estrella)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="displayOrder">Orden de Visualización</label>
                                <input type="number" class="form-control" id="displayOrder" name="display_order" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="testimonialDate">Fecha del Testimonio</label>
                                <input type="date" class="form-control" id="testimonialDate" name="testimonial_date">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="clientImage">Imagen del Cliente</label>
                        <input type="file" class="form-control-file" id="clientImage" name="client_image" accept="image/*">
                        <small class="text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                        <div id="currentImagePreview" class="mt-2" style="display: none;">
                            <img id="currentImage" src="" alt="Imagen actual" style="max-width: 100px; height: auto; border-radius: 50%;">
                            <p class="text-muted">Imagen actual</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="testimonialTags">Tags/Etiquetas</label>
                        <input type="text" class="form-control" id="testimonialTags" name="tags" placeholder="fitness, perdida-peso, musculacion">
                        <small class="text-muted">Separar con comas</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="isFeatured" name="is_featured" value="1">
                                <label class="form-check-label" for="isFeatured">
                                    <strong>Testimonio destacado</strong>
                                </label>
                                <small class="form-text text-muted">Se mostrará en la página principal</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="isActive" name="is_active" value="1" checked>
                                <label class="form-check-label" for="isActive">
                                    <strong>Testimonio activo</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Testimonio
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
                <p>¿Estás seguro de que deseas eliminar este testimonio?</p>
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

.featured-badge {
    background: #fff3cd;
    color: #856404;
}

.rating-stars {
    color: #ffc107;
    font-size: 1.1rem;
}

.client-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.testimonial-preview {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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
    
    .table-container {
        overflow-x: auto;
    }
}
</style>

<script src="/assets/js/admin/testimonials.js"></script>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>