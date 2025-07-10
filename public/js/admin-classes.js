document.addEventListener('DOMContentLoaded', function() {
    // Ocultar pantalla de carga
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        loadingScreen.style.display = 'none';
    }

    // Inicializar funcionalidades de la página de clases
    initClassesManagement();
});

function initClassesManagement() {
    // Funcionalidad para gestión de clases
    console.log('Admin Classes page initialized');
    
    // Aquí se pueden agregar más funcionalidades específicas para la gestión de clases
    // como filtros, búsqueda, paginación, etc.
}