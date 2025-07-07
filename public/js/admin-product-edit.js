/**
 * Admin Product Edit - STYLOFITNESS
 * Manejo AJAX para formularios de edición de productos
 */

// Funciones auxiliares para notificaciones y carga
function showLoadingIndicator(message = 'Cargando...') {
    // Remover indicador existente
    hideLoadingIndicator();
    
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'global-loading-indicator';
    loadingDiv.innerHTML = `<div class="spinner"></div><span>${message}</span>`;
    loadingDiv.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.95);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    
    const spinner = loadingDiv.querySelector('.spinner');
    spinner.style.cssText = `
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    `;
    
    // Agregar animación CSS si no existe
    if (!document.querySelector('#spinner-style')) {
        const style = document.createElement('style');
        style.id = 'spinner-style';
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(loadingDiv);
}

function hideLoadingIndicator() {
    const loadingIndicator = document.getElementById('global-loading-indicator');
    if (loadingIndicator) {
        loadingIndicator.remove();
    }
}

function showNotification(message, type = 'info') {
    // Remover notificaciones existentes
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 10001;
        max-width: 400px;
        word-wrap: break-word;
        animation: slideInRight 0.3s ease-out;
    `;
    
    // Colores según el tipo
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    
    notification.style.backgroundColor = colors[type] || colors.info;
    
    // Agregar animación CSS si no existe
    if (!document.querySelector('#notification-style')) {
        const style = document.createElement('style');
        style.id = 'notification-style';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideInRight 0.3s ease-out reverse';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Función para manejar formularios de actualización con AJAX
function handleProductUpdateForm() {
    const updateForm = document.querySelector('form[action*="/admin/products/"][method="POST"]');
    if (!updateForm || updateForm.dataset.ajaxHandled) return;
    
    updateForm.dataset.ajaxHandled = 'true';
    
    updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        showLoadingIndicator('Actualizando producto...');
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingIndicator();
            
            if (data.success) {
                showNotification(data.message, 'success');
                // Regresar a la página anterior después de un breve delay
                setTimeout(() => {
                    window.history.back();
                }, 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            hideLoadingIndicator();
            console.error('Error:', error);
            showNotification('Error de conexión al actualizar el producto', 'error');
        });
    });
}

// Función para agregar botón de cancelar que regrese a la página anterior
function addCancelButton() {
    const form = document.querySelector('form[action*="/admin/products/"][method="POST"]');
    if (!form) return;
    
    const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
    if (!submitButton) return;
    
    // Verificar si ya existe un botón de cancelar
    if (form.querySelector('.btn-cancel')) return;
    
    const cancelButton = document.createElement('button');
    cancelButton.type = 'button';
    cancelButton.className = 'btn-cancel';
    cancelButton.textContent = 'Cancelar';
    cancelButton.style.cssText = `
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    `;
    
    cancelButton.addEventListener('click', function() {
        if (confirm('¿Estás seguro de que deseas cancelar? Los cambios no guardados se perderán.')) {
            window.history.back();
        }
    });
    
    // Insertar el botón de cancelar antes del botón de envío
    submitButton.parentNode.insertBefore(cancelButton, submitButton);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    handleProductUpdateForm();
    addCancelButton();
});