document.addEventListener('DOMContentLoaded', function() {
    // Ocultar pantalla de carga
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        loadingScreen.style.display = 'none';
    }

    // Inicializar funcionalidades de la página de rutinas
    initRoutinesManagement();
});

function initRoutinesManagement() {
    // Funcionalidad para gestión de rutinas
    console.log('Admin Routines page initialized');
    
    // Aquí se pueden agregar más funcionalidades específicas para la gestión de rutinas
    // como filtros, búsqueda, paginación, etc.
}