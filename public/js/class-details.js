// Class Details JavaScript
class ClassDetails {
    constructor() {
        this.classData = window.classData || {};
        this.selectedSchedule = null;
        this.selectedDate = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeDatePickers();
    }

    bindEvents() {
        // Book schedule buttons
        document.querySelectorAll('.btn-book-schedule').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const scheduleId = btn.dataset.scheduleId;
                this.openBookingModal(scheduleId);
            });
        });

        // Date selection in modal
        const dateInput = document.getElementById('booking-date');
        if (dateInput) {
            dateInput.addEventListener('change', () => {
                this.checkAvailability();
            });
        }

        // Confirm booking button
        const confirmBtn = document.getElementById('confirm-booking');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                this.confirmBooking();
            });
        }

        // Modal close events
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', () => {
                this.resetModal();
            });
        });
    }

    initializeDatePickers() {
        const dateInputs = document.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            input.min = today;
            
            // Set maximum date to 30 days from now
            const maxDate = new Date();
            maxDate.setDate(maxDate.getDate() + 30);
            input.max = maxDate.toISOString().split('T')[0];
        });
    }

    openBookingModal(scheduleId) {
        this.selectedSchedule = scheduleId;
        
        // Find schedule data
        const schedule = this.findScheduleById(scheduleId);
        if (!schedule) {
            this.showError('Horario no encontrado');
            return;
        }

        // Update modal content
        this.updateModalContent(schedule);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
        modal.show();
    }

    findScheduleById(scheduleId) {
        if (!this.classData.schedules) return null;
        
        for (const daySchedules of Object.values(this.classData.schedules)) {
            const schedule = daySchedules.find(s => s.id == scheduleId);
            if (schedule) return schedule;
        }
        return null;
    }

    updateModalContent(schedule) {
        // Update schedule info
        const scheduleInfo = document.querySelector('.schedule-info');
        if (scheduleInfo) {
            scheduleInfo.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Día:</strong> ${this.getDayName(schedule.day_of_week)}
                    </div>
                    <div class="col-md-6">
                        <strong>Hora:</strong> ${schedule.start_time} - ${schedule.end_time}
                    </div>
                    <div class="col-md-6">
                        <strong>Instructor:</strong> ${schedule.instructor_name}
                    </div>
                    <div class="col-md-6">
                        <strong>Sala:</strong> ${this.classData.room_info?.name || 'No especificada'}
                    </div>
                </div>
            `;
        }

        // Reset availability check
        this.resetAvailabilityCheck();
        
        // Clear date input
        const dateInput = document.getElementById('booking-date');
        if (dateInput) {
            dateInput.value = '';
        }
    }

    getDayName(dayNumber) {
        const days = {
            1: 'Lunes',
            2: 'Martes', 
            3: 'Miércoles',
            4: 'Jueves',
            5: 'Viernes',
            6: 'Sábado',
            7: 'Domingo'
        };
        return days[dayNumber] || 'Día desconocido';
    }

    async checkAvailability() {
        const dateInput = document.getElementById('booking-date');
        const availabilityDiv = document.getElementById('availability-check');
        
        if (!dateInput || !dateInput.value || !this.selectedSchedule) {
            this.resetAvailabilityCheck();
            return;
        }

        this.selectedDate = dateInput.value;
        
        // Show loading
        availabilityDiv.innerHTML = `
            <div class="text-center">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Verificando disponibilidad...</span>
                </div>
                <span class="ms-2">Verificando disponibilidad...</span>
            </div>
        `;
        availabilityDiv.className = 'availability-check';

        try {
            const response = await fetch('/classes/getAvailability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    schedule_id: this.selectedSchedule,
                    booking_date: this.selectedDate
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateAvailabilityDisplay(data.data);
            } else {
                this.showAvailabilityError(data.message || 'Error al verificar disponibilidad');
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            this.showAvailabilityError('Error de conexión al verificar disponibilidad');
        }
    }

    updateAvailabilityDisplay(availability) {
        const availabilityDiv = document.getElementById('availability-check');
        const confirmBtn = document.getElementById('confirm-booking');
        
        let statusClass = '';
        let statusText = '';
        let canBook = false;

        if (availability.available_spots > 5) {
            statusClass = 'available';
            statusText = `✓ Disponible (${availability.available_spots} cupos libres)`;
            canBook = true;
        } else if (availability.available_spots > 0) {
            statusClass = 'limited';
            statusText = `⚠ Cupos limitados (${availability.available_spots} cupos libres)`;
            canBook = true;
        } else {
            statusClass = 'full';
            statusText = '✗ Clase llena - No hay cupos disponibles';
            canBook = false;
        }

        availabilityDiv.innerHTML = `
            <div class="d-flex align-items-center justify-content-center gap-2">
                <span>${statusText}</span>
            </div>
        `;
        availabilityDiv.className = `availability-check ${statusClass}`;
        
        // Enable/disable confirm button
        if (confirmBtn) {
            confirmBtn.disabled = !canBook;
            if (canBook) {
                confirmBtn.textContent = this.classData.room_info?.type === 'positioned' 
                    ? 'Seleccionar Posición' 
                    : 'Confirmar Reserva';
            } else {
                confirmBtn.textContent = 'No Disponible';
            }
        }
    }

    showAvailabilityError(message) {
        const availabilityDiv = document.getElementById('availability-check');
        availabilityDiv.innerHTML = `
            <div class="text-center text-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="ms-2">${message}</span>
            </div>
        `;
        availabilityDiv.className = 'availability-check';
        
        const confirmBtn = document.getElementById('confirm-booking');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Error';
        }
    }

    resetAvailabilityCheck() {
        const availabilityDiv = document.getElementById('availability-check');
        if (availabilityDiv) {
            availabilityDiv.innerHTML = `
                <div class="text-center text-muted">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="ms-2">Selecciona una fecha para verificar disponibilidad</span>
                </div>
            `;
            availabilityDiv.className = 'availability-check';
        }
        
        const confirmBtn = document.getElementById('confirm-booking');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Seleccionar Fecha';
        }
    }

    async confirmBooking() {
        if (!this.selectedSchedule || !this.selectedDate) {
            this.showError('Por favor selecciona una fecha válida');
            return;
        }

        const confirmBtn = document.getElementById('confirm-booking');
        const originalText = confirmBtn.textContent;
        
        // Show loading
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status"></span>
            <span class="ms-2">Procesando...</span>
        `;

        try {
            // Check if room requires position selection
            if (this.classData.room_info?.type === 'positioned') {
                // Redirect to room layout for position selection
                window.location.href = `/classes/room-layout/${this.selectedSchedule}?date=${this.selectedDate}`;
                return;
            }

            // Direct booking for capacity-only rooms
            const response = await fetch('/classes/book', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    schedule_id: this.selectedSchedule,
                    booking_date: this.selectedDate
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message || 'Reserva confirmada exitosamente');
                
                // Close modal after delay
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
                    if (modal) modal.hide();
                    
                    // Optionally redirect to bookings page
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // Refresh page to update availability
                        window.location.reload();
                    }
                }, 2000);
            } else {
                this.showError(data.message || 'Error al confirmar la reserva');
            }
        } catch (error) {
            console.error('Error confirming booking:', error);
            this.showError('Error de conexión al confirmar la reserva');
        } finally {
            // Restore button
            confirmBtn.disabled = false;
            confirmBtn.textContent = originalText;
        }
    }

    resetModal() {
        this.selectedSchedule = null;
        this.selectedDate = null;
        this.resetAvailabilityCheck();
        
        const dateInput = document.getElementById('booking-date');
        if (dateInput) {
            dateInput.value = '';
        }
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Utility method to format time
    formatTime(time) {
        if (!time) return '';
        
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        
        return `${displayHour}:${minutes} ${ampm}`;
    }

    // Utility method to format date
    formatDate(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ClassDetails();
});

// Export for potential external use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ClassDetails;
}