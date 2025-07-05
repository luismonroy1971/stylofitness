// Room Layout JavaScript
class RoomLayoutManager {
    constructor() {
        this.selectedPositionId = null;
        this.tempReservationId = null;
        this.countdownTimer = null;
        this.timeRemaining = 300; // 5 minutes in seconds
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updateAvailabilityDisplay();
        
        // Auto-refresh availability every 30 seconds
        setInterval(() => {
            this.refreshAvailability();
        }, 30000);
    }
    
    bindEvents() {
        // Position selection events are bound via onclick in HTML
        
        // Modal events
        const confirmationModal = document.getElementById('confirmationModal');
        if (confirmationModal) {
            confirmationModal.addEventListener('hidden.bs.modal', () => {
                this.cancelTempReservation();
            });
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.cancelSelection();
            }
        });
        
        // Prevent accidental page refresh
        window.addEventListener('beforeunload', (e) => {
            if (this.tempReservationId) {
                e.preventDefault();
                e.returnValue = 'Tienes una reserva temporal activa. ¿Estás seguro de que quieres salir?';
            }
        });
    }
    
    selectPosition(element) {
        const positionId = parseInt(element.dataset.positionId);
        const row = element.dataset.row;
        const seat = element.dataset.seat;
        
        // Clear previous selection
        this.clearSelection();
        
        // Mark as selected
        element.classList.add('selected');
        this.selectedPositionId = positionId;
        
        // Update UI
        this.showSelectedPositionInfo(row, seat, element);
        this.enableConfirmButton();
        
        // Create temporary reservation
        this.createTempReservation(positionId);
    }
    
    clearSelection() {
        // Remove selected class from all positions
        document.querySelectorAll('.position.selected').forEach(pos => {
            pos.classList.remove('selected');
        });
        
        // Clear selection data
        this.selectedPositionId = null;
        
        // Update UI
        this.hideSelectedPositionInfo();
        this.disableConfirmButton();
        
        // Cancel temp reservation
        this.cancelTempReservation();
    }
    
    showSelectedPositionInfo(row, seat, element) {
        const infoDiv = document.getElementById('selectedPositionInfo');
        const rowSpan = document.getElementById('selectedRow');
        const seatSpan = document.getElementById('selectedSeat');
        const typeSpan = document.getElementById('selectedType');
        
        if (infoDiv && rowSpan && seatSpan && typeSpan) {
            rowSpan.textContent = row;
            seatSpan.textContent = seat;
            
            // Determine position type
            const isPremium = element.querySelector('.premium-icon');
            typeSpan.textContent = isPremium ? 'Premium' : 'Estándar';
            typeSpan.className = 'position-type ' + (isPremium ? 'premium' : 'standard');
            
            infoDiv.style.display = 'block';
            
            // Smooth scroll to show the info
            infoDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    
    hideSelectedPositionInfo() {
        const infoDiv = document.getElementById('selectedPositionInfo');
        if (infoDiv) {
            infoDiv.style.display = 'none';
        }
    }
    
    enableConfirmButton() {
        const btn = document.getElementById('confirmPositionBtn');
        if (btn) {
            btn.disabled = false;
        }
    }
    
    disableConfirmButton() {
        const btn = document.getElementById('confirmPositionBtn');
        if (btn) {
            btn.disabled = true;
        }
    }
    
    async createTempReservation(positionId) {
        try {
            const response = await fetch('/classes/select-position', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    schedule_id: window.roomLayoutData.scheduleId,
                    position_id: positionId,
                    booking_date: window.roomLayoutData.bookingDate
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.tempReservationId = data.temp_reservation_id;
                this.startCountdown();
                this.showBookingTimer();
            } else {
                this.showError(data.error || 'Error al reservar posición');
                this.clearSelection();
            }
        } catch (error) {
            console.error('Error creating temp reservation:', error);
            this.showError('Error de conexión');
            this.clearSelection();
        }
    }
    
    async cancelTempReservation() {
        if (!this.tempReservationId) return;
        
        try {
            await fetch('/classes/cancel-temp-reservation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    temp_reservation_id: this.tempReservationId
                })
            });
        } catch (error) {
            console.error('Error canceling temp reservation:', error);
        } finally {
            this.tempReservationId = null;
            this.stopCountdown();
            this.hideBookingTimer();
        }
    }
    
    startCountdown() {
        this.timeRemaining = 300; // 5 minutes
        this.updateCountdownDisplay();
        
        this.countdownTimer = setInterval(() => {
            this.timeRemaining--;
            this.updateCountdownDisplay();
            
            if (this.timeRemaining <= 0) {
                this.handleTimeExpired();
            }
        }, 1000);
    }
    
    stopCountdown() {
        if (this.countdownTimer) {
            clearInterval(this.countdownTimer);
            this.countdownTimer = null;
        }
    }
    
    updateCountdownDisplay() {
        const timeSpan = document.getElementById('timeRemaining');
        if (timeSpan) {
            const minutes = Math.floor(this.timeRemaining / 60);
            const seconds = this.timeRemaining % 60;
            timeSpan.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    }
    
    showBookingTimer() {
        const timer = document.getElementById('bookingTimer');
        if (timer) {
            timer.style.display = 'block';
        }
    }
    
    hideBookingTimer() {
        const timer = document.getElementById('bookingTimer');
        if (timer) {
            timer.style.display = 'none';
        }
    }
    
    handleTimeExpired() {
        this.stopCountdown();
        this.hideBookingTimer();
        this.clearSelection();
        this.showError('El tiempo de reserva ha expirado. Por favor, selecciona otra posición.');
        this.refreshAvailability();
    }
    
    async refreshAvailability() {
        try {
            const response = await fetch(`/classes/room-layout?schedule_id=${window.roomLayoutData.scheduleId}&booking_date=${window.roomLayoutData.bookingDate}`);
            const data = await response.json();
            
            if (data.layout && data.occupied_positions) {
                this.updatePositionAvailability(data.occupied_positions);
                this.updateAvailabilityStats(data.layout.positions, data.occupied_positions);
            }
        } catch (error) {
            console.error('Error refreshing availability:', error);
        }
    }
    
    updatePositionAvailability(occupiedPositions) {
        const occupiedIds = occupiedPositions.map(pos => pos.position_id);
        
        document.querySelectorAll('.position').forEach(posElement => {
            const positionId = parseInt(posElement.dataset.positionId);
            const wasOccupied = posElement.classList.contains('occupied');
            const isNowOccupied = occupiedIds.includes(positionId);
            
            if (!wasOccupied && isNowOccupied) {
                // Position became occupied
                posElement.classList.remove('available', 'selected');
                posElement.classList.add('occupied');
                posElement.onclick = null;
                
                // If this was the selected position, clear selection
                if (positionId === this.selectedPositionId) {
                    this.clearSelection();
                }
            } else if (wasOccupied && !isNowOccupied) {
                // Position became available
                posElement.classList.remove('occupied');
                posElement.classList.add('available');
                posElement.onclick = () => this.selectPosition(posElement);
            }
        });
    }
    
    updateAvailabilityStats(allPositions, occupiedPositions) {
        const availableCount = document.getElementById('availableCount');
        const occupiedCount = document.getElementById('occupiedCount');
        
        if (availableCount && occupiedCount) {
            const totalAvailable = allPositions.filter(pos => pos.is_available).length;
            const totalOccupied = occupiedPositions.length;
            const currentlyAvailable = totalAvailable - totalOccupied;
            
            availableCount.textContent = Math.max(0, currentlyAvailable);
            occupiedCount.textContent = totalOccupied;
        }
    }
    
    updateAvailabilityDisplay() {
        // This method can be called to update the display based on current data
        if (window.roomLayoutData && window.roomLayoutData.positions && window.roomLayoutData.occupiedPositions) {
            this.updateAvailabilityStats(window.roomLayoutData.positions, window.roomLayoutData.occupiedPositions);
        }
    }
    
    showError(message) {
        // Create or update error alert
        let errorAlert = document.getElementById('errorAlert');
        if (!errorAlert) {
            errorAlert = document.createElement('div');
            errorAlert.id = 'errorAlert';
            errorAlert.className = 'alert alert-danger alert-dismissible fade show';
            errorAlert.style.position = 'fixed';
            errorAlert.style.top = '20px';
            errorAlert.style.right = '20px';
            errorAlert.style.zIndex = '9999';
            errorAlert.style.maxWidth = '400px';
            
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'btn-close';
            closeBtn.setAttribute('data-bs-dismiss', 'alert');
            
            errorAlert.appendChild(closeBtn);
            document.body.appendChild(errorAlert);
        }
        
        errorAlert.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (errorAlert && errorAlert.parentNode) {
                errorAlert.remove();
            }
        }, 5000);
    }
    
    showSuccess(message) {
        // Create success alert
        const successAlert = document.createElement('div');
        successAlert.className = 'alert alert-success alert-dismissible fade show';
        successAlert.style.position = 'fixed';
        successAlert.style.top = '20px';
        successAlert.style.right = '20px';
        successAlert.style.zIndex = '9999';
        successAlert.style.maxWidth = '400px';
        
        successAlert.innerHTML = `
            <i class="fas fa-check-circle"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(successAlert);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            if (successAlert && successAlert.parentNode) {
                successAlert.remove();
            }
        }, 3000);
    }
    
    cancelSelection() {
        this.clearSelection();
    }
}

// Global functions for HTML onclick events
function selectPosition(element) {
    if (window.roomManager) {
        window.roomManager.selectPosition(element);
    }
}

function confirmPosition() {
    if (window.roomManager && window.roomManager.selectedPositionId) {
        // Update modal with position info
        const positionSummary = document.getElementById('positionSummary');
        const positionSummaryText = document.getElementById('positionSummaryText');
        const selectedRow = document.getElementById('selectedRow');
        const selectedSeat = document.getElementById('selectedSeat');
        
        if (positionSummary && positionSummaryText && selectedRow && selectedSeat) {
            positionSummaryText.textContent = `Fila ${selectedRow.textContent}, Asiento ${selectedSeat.textContent}`;
            positionSummary.style.display = 'block';
        }
        
        // Show confirmation modal
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        modal.show();
    }
}

function bookWithoutPosition() {
    // For capacity-only rooms
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    modal.show();
}

async function finalizeBooking() {
    try {
        const formData = new URLSearchParams({
            schedule_id: window.roomLayoutData.scheduleId,
            booking_date: window.roomLayoutData.bookingDate
        });
        
        if (window.roomManager && window.roomManager.selectedPositionId) {
            formData.append('position_id', window.roomManager.selectedPositionId);
        }
        
        const response = await fetch('/classes/book', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
            
            // Show success message
            if (window.roomManager) {
                window.roomManager.showSuccess('¡Reserva confirmada exitosamente!');
            }
            
            // Redirect after a short delay
            setTimeout(() => {
                window.location.href = '/classes/my-bookings';
            }, 2000);
        } else {
            throw new Error(data.error || 'Error al procesar la reserva');
        }
    } catch (error) {
        console.error('Error finalizing booking:', error);
        if (window.roomManager) {
            window.roomManager.showError(error.message || 'Error al procesar la reserva');
        }
    }
}

function goBack() {
    if (window.roomManager && window.roomManager.tempReservationId) {
        if (confirm('Tienes una reserva temporal activa. ¿Estás seguro de que quieres salir?')) {
            window.roomManager.cancelTempReservation().then(() => {
                window.history.back();
            });
        }
    } else {
        window.history.back();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.roomManager = new RoomLayoutManager();
});