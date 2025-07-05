<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista: Generador de Reportes de Progreso - STYLOFITNESS
 * Permite a los entrenadores generar reportes detallados del progreso de sus clientes
 */

$currentUser = AppHelper::getCurrentUser();
$isAdmin = ($currentUser['role'] === 'admin');
?>

<div class="progress-reports">
    <!-- Header -->
    <div class="reports-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-auto">
                    <a href="/trainer/progress" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
                <div class="col">
                    <h1 class="page-title">
                        <i class="fas fa-file-alt me-2"></i>
                        Generador de Reportes
                    </h1>
                    <p class="page-subtitle">
                        Crea reportes detallados del progreso y rendimiento de tus clientes
                    </p>
                </div>
                <div class="col-auto">
                    <div class="header-actions">
                        <button type="button" class="btn btn-light" onclick="loadTemplate()">
                            <i class="fas fa-file-import me-2"></i>
                            Cargar Plantilla
                        </button>
                        <button type="button" class="btn btn-light" onclick="saveTemplate()">
                            <i class="fas fa-save me-2"></i>
                            Guardar Plantilla
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Panel de configuración -->
            <div class="col-xl-4 col-lg-5">
                <div class="card report-config">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Configuración del Reporte
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="reportConfigForm">
                            <!-- Tipo de reporte -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Tipo de Reporte</label>
                                <div class="report-types">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reportType" 
                                               id="individual" value="individual" checked>
                                        <label class="form-check-label" for="individual">
                                            <i class="fas fa-user me-2"></i>
                                            Reporte Individual
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reportType" 
                                               id="group" value="group">
                                        <label class="form-check-label" for="group">
                                            <i class="fas fa-users me-2"></i>
                                            Reporte Grupal
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reportType" 
                                               id="summary" value="summary">
                                        <label class="form-check-label" for="summary">
                                            <i class="fas fa-chart-pie me-2"></i>
                                            Resumen Ejecutivo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Selección de clientes -->
                            <div class="mb-4" id="clientSelection">
                                <label class="form-label fw-bold">Seleccionar Cliente(s)</label>
                                <div class="client-selector">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control" id="clientSearchReport" 
                                               placeholder="Buscar cliente...">
                                    </div>
                                    <div class="clients-list" id="clientsList">
                                        <?php foreach ($availableClients as $client): ?>
                                            <div class="client-item" data-client-id="<?= $client['id'] ?>" 
                                                 data-client-name="<?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>">
                                                <div class="form-check">
                                                    <input class="form-check-input client-checkbox" type="checkbox" 
                                                           id="reportClient_<?= $client['id'] ?>" 
                                                           value="<?= $client['id'] ?>">
                                                    <label class="form-check-label" for="reportClient_<?= $client['id'] ?>">
                                                        <div class="client-info">
                                                            <div class="client-avatar-small">
                                                                <?php if (!empty($client['avatar'])): ?>
                                                                    <img src="<?= htmlspecialchars($client['avatar']) ?>" 
                                                                         alt="<?= htmlspecialchars($client['first_name']) ?>">
                                                                <?php else: ?>
                                                                    <div class="avatar-placeholder-small">
                                                                        <?= strtoupper(substr($client['first_name'], 0, 1) . substr($client['last_name'], 0, 1)) ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="client-details">
                                                                <span class="client-name">
                                                                    <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                                                                </span>
                                                                <small class="client-stats">
                                                                    <?= $client['total_workouts'] ?> entrenamientos
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Período de tiempo -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Período de Tiempo</label>
                                <div class="row">
                                    <div class="col-6">
                                        <select class="form-select" id="reportPeriod">
                                            <option value="custom">Personalizado</option>
                                            <option value="7">Últimos 7 días</option>
                                            <option value="30" selected>Últimos 30 días</option>
                                            <option value="90">Últimos 90 días</option>
                                            <option value="180">Últimos 6 meses</option>
                                            <option value="365">Último año</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary w-100" 
                                                onclick="toggleCustomDates()" id="customDatesBtn">
                                            <i class="fas fa-calendar me-1"></i>
                                            Fechas
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="customDatesPanel" class="mt-3" style="display: none;">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Desde</label>
                                            <input type="date" class="form-control" id="startDate">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Hasta</label>
                                            <input type="date" class="form-control" id="endDate">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Secciones del reporte -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Secciones a Incluir</label>
                                <div class="report-sections">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="section_summary" checked>
                                        <label class="form-check-label" for="section_summary">
                                            Resumen Ejecutivo
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="section_progress" checked>
                                        <label class="form-check-label" for="section_progress">
                                            Gráficos de Progreso
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="section_workouts" checked>
                                        <label class="form-check-label" for="section_workouts">
                                            Historial de Entrenamientos
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="section_exercises">
                                        <label class="form-check-label" for="section_exercises">
                                            Análisis de Ejercicios
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="section_physical">
                                        <label class="form-check-label" for="section_physical">
                                            Progreso Físico
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="section_recommendations">
                                        <label class="form-check-label" for="section_recommendations">
                                            Recomendaciones
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Formato de salida -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Formato de Salida</label>
                                <div class="output-formats">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="outputFormat" 
                                               id="pdf" value="pdf" checked>
                                        <label class="form-check-label" for="pdf">
                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                            PDF
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="outputFormat" 
                                               id="excel" value="excel">
                                        <label class="form-check-label" for="excel">
                                            <i class="fas fa-file-excel text-success me-2"></i>
                                            Excel
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="outputFormat" 
                                               id="html" value="html">
                                        <label class="form-check-label" for="html">
                                            <i class="fas fa-globe text-primary me-2"></i>
                                            HTML
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Opciones adicionales -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Opciones Adicionales</label>
                                <div class="additional-options">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="includeCharts" checked>
                                        <label class="form-check-label" for="includeCharts">
                                            Incluir Gráficos
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="includePhotos">
                                        <label class="form-check-label" for="includePhotos">
                                            Incluir Fotos de Progreso
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="includeNotes">
                                        <label class="form-check-label" for="includeNotes">
                                            Incluir Notas del Entrenador
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="includeGoals">
                                        <label class="form-check-label" for="includeGoals">
                                            Incluir Objetivos
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" onclick="generateReport()">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Generar Reporte
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="previewReport()">
                                    <i class="fas fa-eye me-2"></i>
                                    Vista Previa
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="scheduleReport()">
                                    <i class="fas fa-clock me-2"></i>
                                    Programar Envío
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Panel de vista previa -->
            <div class="col-xl-8 col-lg-7">
                <div class="card report-preview">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-eye me-2"></i>
                                Vista Previa del Reporte
                            </h5>
                            <div class="preview-actions">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshPreview()">
                                    <i class="fas fa-refresh"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleFullscreen()">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="reportPreview" class="report-preview-content">
                            <div class="preview-placeholder">
                                <div class="placeholder-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <h5>Vista Previa del Reporte</h5>
                                <p class="text-muted">
                                    Configura las opciones del reporte y haz clic en "Vista Previa" 
                                    para ver cómo se verá el documento final.
                                </p>
                                <div class="preview-features">
                                    <div class="feature-item">
                                        <i class="fas fa-chart-line text-primary"></i>
                                        <span>Gráficos interactivos</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-table text-success"></i>
                                        <span>Tablas detalladas</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-image text-warning"></i>
                                        <span>Fotos de progreso</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-lightbulb text-info"></i>
                                        <span>Recomendaciones</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de reportes -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            Reportes Recientes
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentReports)): ?>
                            <div class="no-reports text-center py-4">
                                <i class="fas fa-file-alt text-muted mb-3" style="font-size: 3rem;"></i>
                                <h6 class="text-muted">No hay reportes generados</h6>
                                <p class="text-muted">Los reportes que generes aparecerán aquí para fácil acceso.</p>
                            </div>
                        <?php else: ?>
                            <div class="reports-list">
                                <?php foreach ($recentReports as $report): ?>
                                    <div class="report-item">
                                        <div class="report-icon">
                                            <i class="fas fa-file-<?= $report['format'] === 'pdf' ? 'pdf text-danger' : 
                                                                    ($report['format'] === 'excel' ? 'excel text-success' : 'code text-primary') ?>"></i>
                                        </div>
                                        <div class="report-info">
                                            <h6 class="report-title"><?= htmlspecialchars($report['title']) ?></h6>
                                            <div class="report-meta">
                                                <span class="badge bg-secondary me-2"><?= ucfirst($report['type']) ?></span>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($report['created_at'])) ?> • 
                                                    <?= $report['client_count'] ?> cliente(s)
                                                </small>
                                            </div>
                                        </div>
                                        <div class="report-actions">
                                            <a href="<?= $report['download_url'] ?>" class="btn btn-outline-primary btn-sm" 
                                               target="_blank">
                                                <i class="fas fa-download me-1"></i>
                                                Descargar
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="duplicateReport(<?= $report['id'] ?>)">
                                                            <i class="fas fa-copy me-2"></i>
                                                            Duplicar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="shareReport(<?= $report['id'] ?>)">
                                                            <i class="fas fa-share me-2"></i>
                                                            Compartir
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="deleteReport(<?= $report['id'] ?>)">
                                                            <i class="fas fa-trash me-2"></i>
                                                            Eliminar
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para programar reportes -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-clock me-2"></i>
                    Programar Envío de Reporte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleForm">
                    <div class="mb-3">
                        <label class="form-label">Frecuencia</label>
                        <select class="form-select" id="scheduleFrequency">
                            <option value="once">Una vez</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly">Mensual</option>
                            <option value="quarterly">Trimestral</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Fecha y Hora</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" class="form-control" id="scheduleDate">
                            </div>
                            <div class="col-6">
                                <input type="time" class="form-control" id="scheduleTime">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Destinatarios</label>
                        <div class="recipients-list">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sendToClient">
                                <label class="form-check-label" for="sendToClient">
                                    Enviar al cliente
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sendToMe" checked>
                                <label class="form-check-label" for="sendToMe">
                                    Enviarme una copia
                                </label>
                            </div>
                        </div>
                        <div class="mt-2">
                            <input type="email" class="form-control" id="additionalEmails" 
                                   placeholder="Emails adicionales (separados por coma)">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mensaje personalizado</label>
                        <textarea class="form-control" id="scheduleMessage" rows="3" 
                                  placeholder="Mensaje opcional para incluir en el email..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmSchedule()">
                    <i class="fas fa-clock me-2"></i>
                    Programar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.progress-reports {
    background: #f8f9fa;
    min-height: 100vh;
}

.reports-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.report-config {
    position: sticky;
    top: 2rem;
}

.report-types,
.output-formats,
.report-sections,
.additional-options {
    max-height: 200px;
    overflow-y: auto;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.form-check {
    margin-bottom: 0.75rem;
}

.form-check-label {
    cursor: pointer;
    width: 100%;
}

.clients-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    background: #f8f9fa;
}

.client-item {
    padding: 0.75rem;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.3s ease;
}

.client-item:hover {
    background: #e3f2fd;
}

.client-item:last-child {
    border-bottom: none;
}

.client-info {
    display: flex;
    align-items: center;
    width: 100%;
}

.client-avatar-small {
    width: 35px;
    height: 35px;
    margin-right: 0.75rem;
}

.client-avatar-small img,
.avatar-placeholder-small {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-placeholder-small {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.client-details {
    flex: 1;
}

.client-name {
    font-weight: 600;
    display: block;
    margin-bottom: 0.25rem;
}

.client-stats {
    color: #6c757d;
    font-size: 0.875rem;
}

.report-preview-content {
    min-height: 500px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    position: relative;
}

.preview-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 500px;
    text-align: center;
    padding: 2rem;
}

.placeholder-icon {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.preview-features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.reports-list {
    max-height: 400px;
    overflow-y: auto;
}

.report-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 1rem;
    background: white;
    transition: all 0.3s ease;
}

.report-item:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.report-icon {
    font-size: 2rem;
    margin-right: 1rem;
    width: 50px;
    text-align: center;
}

.report-info {
    flex: 1;
    margin-right: 1rem;
}

.report-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.report-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.report-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.recipients-list {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .report-config {
        position: static;
        margin-bottom: 2rem;
    }
    
    .preview-features {
        grid-template-columns: 1fr;
    }
    
    .report-item {
        flex-direction: column;
        text-align: center;
    }
    
    .report-icon,
    .report-info {
        margin-right: 0;
        margin-bottom: 1rem;
    }
}
</style>

<script>
// Variables globales
let selectedClients = [];
let reportConfig = {};

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    initializeDates();
});

function setupEventListeners() {
    // Tipo de reporte
    document.querySelectorAll('input[name="reportType"]').forEach(radio => {
        radio.addEventListener('change', updateClientSelection);
    });
    
    // Búsqueda de clientes
    document.getElementById('clientSearchReport').addEventListener('input', filterReportClients);
    
    // Selección de clientes
    document.querySelectorAll('.client-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedClients);
    });
    
    // Período personalizado
    document.getElementById('reportPeriod').addEventListener('change', function() {
        if (this.value === 'custom') {
            document.getElementById('customDatesPanel').style.display = 'block';
        } else {
            document.getElementById('customDatesPanel').style.display = 'none';
        }
    });
}

function initializeDates() {
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('endDate').value = today.toISOString().split('T')[0];
    document.getElementById('startDate').value = thirtyDaysAgo.toISOString().split('T')[0];
}

function updateClientSelection() {
    const reportType = document.querySelector('input[name="reportType"]:checked').value;
    const clientSelection = document.getElementById('clientSelection');
    
    if (reportType === 'summary') {
        clientSelection.style.display = 'none';
    } else {
        clientSelection.style.display = 'block';
        
        // Actualizar límites de selección
        const checkboxes = document.querySelectorAll('.client-checkbox');
        if (reportType === 'individual') {
            // Solo un cliente para reporte individual
            checkboxes.forEach(checkbox => {
                if (checkbox.checked && selectedClients.length > 1) {
                    checkbox.checked = false;
                }
            });
            selectedClients = selectedClients.slice(0, 1);
        }
    }
}

function filterReportClients() {
    const searchTerm = document.getElementById('clientSearchReport').value.toLowerCase();
    const clientItems = document.querySelectorAll('.client-item');
    
    clientItems.forEach(item => {
        const clientName = item.dataset.clientName.toLowerCase();
        const isVisible = clientName.includes(searchTerm);
        item.style.display = isVisible ? 'block' : 'none';
    });
}

function updateSelectedClients() {
    const reportType = document.querySelector('input[name="reportType"]:checked').value;
    const checkedBoxes = document.querySelectorAll('.client-checkbox:checked');
    
    selectedClients = Array.from(checkedBoxes).map(checkbox => parseInt(checkbox.value));
    
    // Validar límites según tipo de reporte
    if (reportType === 'individual' && selectedClients.length > 1) {
        // Deseleccionar todos excepto el último
        checkedBoxes.forEach((checkbox, index) => {
            if (index < checkedBoxes.length - 1) {
                checkbox.checked = false;
            }
        });
        selectedClients = [selectedClients[selectedClients.length - 1]];
    }
}

function toggleCustomDates() {
    const panel = document.getElementById('customDatesPanel');
    const isVisible = panel.style.display !== 'none';
    
    panel.style.display = isVisible ? 'none' : 'block';
    
    if (!isVisible) {
        document.getElementById('reportPeriod').value = 'custom';
    }
}

function generateReport() {
    const config = getReportConfig();
    
    if (!validateReportConfig(config)) {
        return;
    }
    
    // Mostrar loading
    showReportLoading();
    
    // Generar reporte
    fetch('/api/trainer/reports/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(config)
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Error al generar el reporte');
    })
    .then(blob => {
        // Descargar archivo
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `reporte_progreso_${new Date().toISOString().split('T')[0]}.${config.outputFormat}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        showNotification('Reporte generado exitosamente', 'success');
        
        // Actualizar historial
        setTimeout(() => {
            location.reload();
        }, 1000);
    })
    .catch(error => {
        console.error('Error generating report:', error);
        showNotification('Error al generar el reporte', 'error');
    })
    .finally(() => {
        hideReportLoading();
    });
}

function previewReport() {
    const config = getReportConfig();
    
    if (!validateReportConfig(config)) {
        return;
    }
    
    // Mostrar loading en preview
    showPreviewLoading();
    
    // Generar vista previa
    fetch('/api/trainer/reports/preview', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(config)
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('reportPreview').innerHTML = html;
    })
    .catch(error => {
        console.error('Error generating preview:', error);
        showNotification('Error al generar la vista previa', 'error');
    })
    .finally(() => {
        hidePreviewLoading();
    });
}

function getReportConfig() {
    const reportType = document.querySelector('input[name="reportType"]:checked').value;
    const outputFormat = document.querySelector('input[name="outputFormat"]:checked').value;
    const period = document.getElementById('reportPeriod').value;
    
    const config = {
        type: reportType,
        outputFormat: outputFormat,
        period: period,
        clientIds: reportType !== 'summary' ? selectedClients : [],
        sections: getSections(),
        options: getOptions()
    };
    
    if (period === 'custom') {
        config.startDate = document.getElementById('startDate').value;
        config.endDate = document.getElementById('endDate').value;
    }
    
    return config;
}

function getSections() {
    const sections = [];
    document.querySelectorAll('.report-sections input:checked').forEach(checkbox => {
        sections.push(checkbox.id.replace('section_', ''));
    });
    return sections;
}

function getOptions() {
    const options = {};
    document.querySelectorAll('.additional-options input').forEach(checkbox => {
        options[checkbox.id] = checkbox.checked;
    });
    return options;
}

function validateReportConfig(config) {
    if (config.type !== 'summary' && config.clientIds.length === 0) {
        showNotification('Selecciona al menos un cliente', 'error');
        return false;
    }
    
    if (config.period === 'custom') {
        if (!config.startDate || !config.endDate) {
            showNotification('Selecciona las fechas de inicio y fin', 'error');
            return false;
        }
        
        if (new Date(config.startDate) > new Date(config.endDate)) {
            showNotification('La fecha de inicio debe ser anterior a la fecha de fin', 'error');
            return false;
        }
    }
    
    if (config.sections.length === 0) {
        showNotification('Selecciona al menos una sección para incluir', 'error');
        return false;
    }
    
    return true;
}

function scheduleReport() {
    const config = getReportConfig();
    
    if (!validateReportConfig(config)) {
        return;
    }
    
    // Guardar configuración para el modal
    reportConfig = config;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
    modal.show();
}

function confirmSchedule() {
    const scheduleData = {
        ...reportConfig,
        schedule: {
            frequency: document.getElementById('scheduleFrequency').value,
            date: document.getElementById('scheduleDate').value,
            time: document.getElementById('scheduleTime').value,
            recipients: {
                sendToClient: document.getElementById('sendToClient').checked,
                sendToMe: document.getElementById('sendToMe').checked,
                additionalEmails: document.getElementById('additionalEmails').value
            },
            message: document.getElementById('scheduleMessage').value
        }
    };
    
    fetch('/api/trainer/reports/schedule', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(scheduleData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Reporte programado exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('scheduleModal')).hide();
        } else {
            showNotification('Error al programar el reporte', 'error');
        }
    })
    .catch(error => {
        console.error('Error scheduling report:', error);
        showNotification('Error al programar el reporte', 'error');
    });
}

function loadTemplate() {
    // Implementar carga de plantillas
    showNotification('Funcionalidad en desarrollo', 'info');
}

function saveTemplate() {
    const config = getReportConfig();
    
    if (!validateReportConfig(config)) {
        return;
    }
    
    const templateName = prompt('Nombre de la plantilla:');
    if (!templateName) return;
    
    fetch('/api/trainer/reports/templates', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            name: templateName,
            config: config
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Plantilla guardada exitosamente', 'success');
        } else {
            showNotification('Error al guardar la plantilla', 'error');
        }
    })
    .catch(error => {
        console.error('Error saving template:', error);
        showNotification('Error al guardar la plantilla', 'error');
    });
}

function duplicateReport(reportId) {
    fetch(`/api/trainer/reports/${reportId}/duplicate`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Reporte duplicado exitosamente', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Error al duplicar el reporte', 'error');
        }
    })
    .catch(error => {
        console.error('Error duplicating report:', error);
        showNotification('Error al duplicar el reporte', 'error');
    });
}

function shareReport(reportId) {
    const email = prompt('Email del destinatario:');
    if (!email) return;
    
    fetch(`/api/trainer/reports/${reportId}/share`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Reporte compartido exitosamente', 'success');
        } else {
            showNotification('Error al compartir el reporte', 'error');
        }
    })
    .catch(error => {
        console.error('Error sharing report:', error);
        showNotification('Error al compartir el reporte', 'error');
    });
}

function deleteReport(reportId) {
    if (!confirm('¿Estás seguro de que quieres eliminar este reporte?')) {
        return;
    }
    
    fetch(`/api/trainer/reports/${reportId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Reporte eliminado exitosamente', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Error al eliminar el reporte', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting report:', error);
        showNotification('Error al eliminar el reporte', 'error');
    });
}

function refreshPreview() {
    previewReport();
}

function toggleFullscreen() {
    const preview = document.getElementById('reportPreview');
    if (preview.requestFullscreen) {
        preview.requestFullscreen();
    }
}

function showReportLoading() {
    const btn = document.querySelector('button[onclick="generateReport()"]');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando...';
    btn.disabled = true;
}

function hideReportLoading() {
    const btn = document.querySelector('button[onclick="generateReport()"]');
    btn.innerHTML = '<i class="fas fa-file-alt me-2"></i>Generar Reporte';
    btn.disabled = false;
}

function showPreviewLoading() {
    document.getElementById('reportPreview').innerHTML = `
        <div class="d-flex justify-content-center align-items-center" style="height: 500px;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Generando vista previa...</p>
            </div>
        </div>
    `;
}

function hidePreviewLoading() {
    // El contenido se reemplaza automáticamente
}

function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 
                      type === 'info' ? 'alert-info' : 'alert-warning';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-dismiss después de 5 segundos
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>