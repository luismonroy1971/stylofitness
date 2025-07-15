/**
 * JavaScript para la gestión de reservas de clases
 * Maneja la interfaz de usuario, actualizaciones en tiempo real y acciones de reservas
 */

class MyBookingsManager {
    constructor() {
        this.init();
        this.setupEventListeners();
        this.startAutoRefresh();
    }

    init() {
        this.bindTabNavigation();
        this.bindBookingActions();
        this.bindModalEvents();
        this.initializeTooltips();
    }

    setupEventListeners() {
        // Gestión de pestañas
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchTab(e));
        });

        // Botones de detalles
        document.querySelectorAll('.btn-details').forEach(btn => {
            btn.addEventListener('click', (e) => this.showBookingDetails(e));
        });

        // Botones de cancelación
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', (e) => this.showCancelModal(e));
        });

        // Botones de contacto
        document.querySelectorAll('.btn-contact').forEach(btn => {
            btn.addEventListener('click', (e) => this.contactInstructor(e));
        });

        // Confirmación de cancelación
        const confirmCancelBtn = document.getElementById('confirmCancelBtn');
        if (confirmCancelBtn) {
            confirmCancelBtn.addEventListener('click', () => this.confirmCancellation());
        }
    }

    bindTabNavigation() {
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const targetTab = tab.dataset.tab;
                
                // Actualizar pestañas activas
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Actualizar contenido activo
                contents.forEach(content => {
                    content.classList.remove('active');
                });
                
                const targetContent = document.getElementById(targetTab);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
                
                // Guardar pestaña activa en localStorage
                localStorage.setItem('activeBookingsTab', targetTab);
            });
        });

        // Restaurar pestaña activa
        const savedTab = localStorage.getItem('activeBookingsTab');
        if (savedTab) {
            const tabBtn = document.querySelector(`[data-tab="${savedTab}"]`);
            if (tabBtn) {
                tabBtn.click();
            }
        }
    }

    bindBookingActions() {
        // Botones de acceso a clase
        document.querySelectorAll('.btn-access').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.trackClassAccess(e.target.href);
            });
        });
    }

    bindModalEvents() {
        // Limpiar modales al cerrar
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', () => {
                this.clearModalContent(modal);
            });
        });
    }

    initializeTooltips() {
        // Inicializar tooltips de Bootstrap si están disponibles
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    switchTab(event) {
        const tabId = event.target.closest('.tab-btn').dataset.tab;
        
        // Actualizar pestañas
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.closest('.tab-btn').classList.add('active');
        
        // Actualizar contenido
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(tabId).classList.add('active');
        
        // Analíticas
        this.trackTabSwitch(tabId);
    }

    showBookingDetails(event) {
        const booking = JSON.parse(event.target.closest('.btn-details').dataset.booking);
        this.renderBookingDetails(booking);
        
        const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
        modal.show();
        
        this.trackAction('view_booking_details', { booking_id: booking.booking_id });
    }

    renderBookingDetails(booking) {
        const content = `
            <div class="booking-details-full">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-dumbbell"></i> Información de la Clase</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Clase:</strong></td><td>${this.escapeHtml(booking.class_name)}</td></tr>
                            <tr><td><strong>Tipo:</strong></td><td>${this.escapeHtml(booking.class_type)}</td></tr>
                            <tr><td><strong>Nivel:</strong></td><td>${this.escapeHtml(booking.difficulty_level)}</td></tr>
                            <tr><td><strong>Duración:</strong></td><td>${booking.duration_minutes} minutos</td></tr>
                            <tr><td><strong>Instructor:</strong></td><td>${this.escapeHtml(booking.instructor_first_name)} ${this.escapeHtml(booking.instructor_last_name)}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-calendar-check"></i> Información de la Reserva</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Fecha:</strong></td><td>${this.formatDate(booking.booking_date)}</td></tr>
                            <tr><td><strong>Hora:</strong></td><td>${this.formatTime(booking.start_time)} - ${this.formatTime(booking.end_time)}</td></tr>
                            <tr><td><strong>Sala:</strong></td><td>${this.escapeHtml(booking.room_name)}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${this.getStatusColor(booking.status)}">${this.capitalizeFirst(booking.status)}</span></td></tr>
                            <tr><td><strong>Reservado:</strong></td><td>${this.formatDateTime(booking.booking_time)}</td></tr>
                            ${booking.position_number ? `<tr><td><strong>Posición:</strong></td><td>Fila ${booking.row_number}, Asiento ${booking.seat_number}</td></tr>` : ''}
                        </table>
                    </div>
                </div>
                ${booking.class_description ? `
                    <div class="mt-4">
                        <h6><i class="fas fa-info-circle"></i> Descripción de la Clase</h6>
                        <div class="class-description-detail">
                            <p>${this.escapeHtml(booking.class_description)}</p>
                        </div>
                    </div>
                ` : ''}
                ${this.renderAccessInfo(booking)}
            </div>
        `;
        
        document.getElementById('bookingDetailsContent').innerHTML = content;
    }

    renderAccessInfo(booking) {
        const now = new Date();
        const bookingDate = new Date(booking.booking_date + ' ' + booking.start_time);
        const timeDiff = bookingDate.getTime() - now.getTime();
        const minutesDiff = Math.floor(timeDiff / (1000 * 60));
        
        let accessInfo = '';
        
        if (minutesDiff > 15) {
            accessInfo = `
                <div class="mt-4">
                    <h6><i class="fas fa-clock"></i> Información de Acceso</h6>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        El acceso a la clase estará disponible 15 minutos antes del inicio.
                        <br><small>Podrás acceder a partir de las ${this.formatTime(this.subtractMinutes(booking.start_time, 15))}</small>
                    </div>
                </div>
            `;
        } else if (minutesDiff >= -10) {
            accessInfo = `
                <div class="mt-4">
                    <h6><i class="fas fa-unlock"></i> Acceso Disponible</h6>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        ¡Puedes acceder a la clase ahora!
                        <br><small>El acceso estará disponible hasta 10 minutos después del inicio</small>
                    </div>
                </div>
            `;
        } else {
            accessInfo = `
                <div class="mt-4">
                    <h6><i class="fas fa-lock"></i> Acceso Expirado</h6>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        El tiempo de acceso para esta clase ha expirado.
                    </div>
                </div>
            `;
        }
        
        return accessInfo;
    }

    showCancelModal(event) {
        const btn = event.target.closest('.btn-cancel');
        this.bookingToCancel = btn.dataset.bookingId;
        
        document.getElementById('cancelClassName').textContent = btn.dataset.className;
        
        const modal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));
        modal.show();
        
        this.trackAction('cancel_booking_attempt', { booking_id: this.bookingToCancel });
    }

    async confirmCancellation() {
        if (!this.bookingToCancel) return;
        
        const confirmBtn = document.getElementById('confirmCancelBtn');
        const originalText = confirmBtn.innerHTML;
        
        // Mostrar estado de carga
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelando...';
        confirmBtn.disabled = true;
        
        try {
            const response = await fetch('/classes/cancel-booking', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    booking_id: this.bookingToCancel
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('cancelBookingModal')).hide();
                
                // Mostrar mensaje de éxito
                this.showAlert('Reserva cancelada exitosamente', 'success');
                
                // Remover la tarjeta de reserva
                this.removeBookingCard(this.bookingToCancel);
                
                // Actualizar contadores
                this.updateTabCounters();
                
                this.trackAction('booking_cancelled', { booking_id: this.bookingToCancel });
            } else {
                this.showAlert(data.error || 'Error al cancelar la reserva', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('Error de conexión. Inténtalo de nuevo.', 'danger');
        } finally {
            // Restaurar botón
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
            this.bookingToCancel = null;
        }
    }

    removeBookingCard(bookingId) {
        const card = document.querySelector(`[data-booking-id="${bookingId}"]`)?.closest('.booking-card');
        if (card) {
            card.style.transition = 'all 0.3s ease';
            card.style.transform = 'translateX(-100%)';
            card.style.opacity = '0';
            
            setTimeout(() => {
                card.remove();
                this.checkEmptyState();
            }, 300);
        }
    }

    updateTabCounters() {
        const upcomingCount = document.querySelectorAll('#upcoming .booking-card').length;
        const pastCount = document.querySelectorAll('#past .booking-item').length;
        
        const upcomingBadge = document.querySelector('[data-tab="upcoming"] .badge');
        const pastBadge = document.querySelector('[data-tab="past"] .badge');
        
        if (upcomingBadge) upcomingBadge.textContent = upcomingCount;
        if (pastBadge) pastBadge.textContent = pastCount;
    }

    checkEmptyState() {
        const upcomingCards = document.querySelectorAll('#upcoming .booking-card');
        const upcomingContainer = document.getElementById('upcoming');
        
        if (upcomingCards.length === 0 && !upcomingContainer.querySelector('.empty-state')) {
            upcomingContainer.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <h3>No tienes clases reservadas</h3>
                    <p>¡Explora nuestras clases grupales y reserva tu próxima sesión!</p>
                    <a href="/classes" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Explorar Clases
                    </a>
                </div>
            `;
        }
    }

    contactInstructor(event) {
        const btn = event.target.closest('.btn-contact');
        const instructorId = btn.dataset.instructorId;
        const instructorName = btn.dataset.instructorName;
        
        // Por ahora mostrar un mensaje, en el futuro se puede implementar un chat o modal de contacto
        this.showAlert(`Función de contacto con ${instructorName} en desarrollo`, 'info');
        
        this.trackAction('contact_instructor_attempt', { instructor_id: instructorId });
    }

    trackClassAccess(url) {
        const urlParams = new URLSearchParams(url.split('?')[1]);
        const scheduleId = urlParams.get('schedule_id');
        
        this.trackAction('class_access', { schedule_id: scheduleId });
    }

    startAutoRefresh() {
        // Actualizar estados de acceso cada 30 segundos
        setInterval(() => {
            if (document.querySelector('.tab-btn[data-tab="upcoming"]').classList.contains('active')) {
                this.updateAccessStates();
            }
        }, 30000);
        
        // Actualizar cada 5 minutos para cambios más generales
        setInterval(() => {
            this.refreshBookingData();
        }, 300000);
    }

    async updateAccessStates() {
        const bookingCards = document.querySelectorAll('.booking-card');
        
        for (const card of bookingCards) {
            const accessBtn = card.querySelector('.btn-access');
            if (accessBtn) {
                const url = new URL(accessBtn.href);
                const scheduleId = url.searchParams.get('schedule_id');
                const bookingDate = url.searchParams.get('booking_date');
                
                try {
                    const response = await fetch(`/classes/check-access?schedule_id=${scheduleId}&booking_date=${bookingDate}`);
                    const data = await response.json();
                    
                    this.updateCardAccessState(card, data);
                } catch (error) {
                    console.error('Error checking access:', error);
                }
            }
        }
    }

    updateCardAccessState(card, accessStatus) {
        const indicator = card.querySelector('.access-indicator');
        const message = card.querySelector('.access-message');
        const accessBtn = card.querySelector('.btn-access');
        
        // Actualizar indicador
        if (indicator) {
            indicator.className = `access-indicator ${accessStatus.can_access ? 'active' : 'inactive'}`;
            const statusText = indicator.querySelector('span');
            if (statusText) {
                if (accessStatus.can_access) {
                    statusText.textContent = 'Acceso Activo';
                } else if (accessStatus.reason === 'too_early') {
                    statusText.textContent = 'Próximamente';
                } else if (accessStatus.reason === 'too_late') {
                    statusText.textContent = 'Expirado';
                } else {
                    statusText.textContent = 'Programado';
                }
            }
        }
        
        // Actualizar mensaje
        if (message) {
            message.innerHTML = `
                <i class="fas ${accessStatus.can_access ? 'fa-check-circle' : 'fa-info-circle'}"></i>
                ${accessStatus.message}
                ${accessStatus.minutes_remaining && accessStatus.can_access ? `<br><small>Tiempo restante: ${accessStatus.minutes_remaining} minutos</small>` : ''}
            `;
            message.className = `access-message ${accessStatus.can_access ? 'success' : 'info'}`;
        }
        
        // Mostrar/ocultar botón de acceso
        if (accessBtn) {
            accessBtn.style.display = accessStatus.can_access ? 'inline-flex' : 'none';
            
            // Actualizar clase de la tarjeta
            if (accessStatus.can_access) {
                card.classList.add('accessible');
            } else {
                card.classList.remove('accessible');
            }
        }
    }

    async refreshBookingData() {
        // Implementar actualización completa de datos si es necesario
        console.log('Refreshing booking data...');
    }

    showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="fas ${this.getAlertIcon(type)}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.my-bookings-container');
        const header = container.querySelector('.page-header');
        container.insertBefore(alertDiv, header.nextSibling);
        
        // Auto-dismiss después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    getAlertIcon(type) {
        const icons = {
            'success': 'fa-check-circle',
            'danger': 'fa-exclamation-triangle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };
        return icons[type] || 'fa-info-circle';
    }

    clearModalContent(modal) {
        const content = modal.querySelector('.modal-body');
        if (content && modal.id === 'bookingDetailsModal') {
            content.innerHTML = '';
        }
    }

    // Utilidades
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    formatTime(timeString) {
        return timeString.substring(0, 5); // HH:MM
    }

    formatDateTime(dateTimeString) {
        const date = new Date(dateTimeString);
        return date.toLocaleString('es-ES');
    }

    subtractMinutes(timeString, minutes) {
        const [hours, mins] = timeString.split(':').map(Number);
        const date = new Date();
        date.setHours(hours, mins - minutes, 0, 0);
        return date.toTimeString().substring(0, 5);
    }

    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    getStatusColor(status) {
        const colors = {
            'booked': 'success',
            'confirmed': 'primary',
            'cancelled': 'danger',
            'completed': 'secondary'
        };
        return colors[status] || 'secondary';
    }

    trackAction(action, data = {}) {
        // Implementar tracking de analíticas si es necesario
        console.log('Action tracked:', action, data);
    }

    trackTabSwitch(tabId) {
        this.trackAction('tab_switch', { tab: tabId });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    new MyBookingsManager();
});

// Exportar para uso global si es necesario
window.MyBookingsManager = MyBookingsManager;