document.addEventListener('DOMContentLoaded', function() {
    // Ocultar pantalla de carga inmediatamente en páginas de admin
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        loadingScreen.style.display = 'none';
        loadingScreen.style.opacity = '0';
        loadingScreen.style.visibility = 'hidden';
        loadingScreen.style.pointerEvents = 'none';
    }
    
    // Asegurar que el contenido del body sea visible
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';

    // Inicializar funcionalidades de la página de rutinas
    initRoutinesManagement();
});

function initRoutinesManagement() {
    // Funcionalidad para gestión de rutinas
    console.log('Admin Routines page initialized');
    
    // Aquí se pueden agregar más funcionalidades específicas para la gestión de rutinas
    // como filtros, búsqueda, paginación, etc.
}