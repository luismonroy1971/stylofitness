document.addEventListener('DOMContentLoaded', function() {
    // Ocultar pantalla de carga inmediatamente para páginas de admin
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        loadingScreen.style.display = 'none';
        loadingScreen.style.opacity = '0';
        loadingScreen.style.visibility = 'hidden';
        loadingScreen.style.pointerEvents = 'none';
    }
    
    // Asegurar que el contenido sea visible
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';

    // Inicializar funcionalidades de la página de clases
    initClassesManagement();
});

function initClassesManagement() {
    // Funcionalidad para gestión de clases
    console.log('Admin Classes page initialized');
    
    // Aquí se pueden agregar más funcionalidades específicas para la gestión de clases
    // como filtros, búsqueda, paginación, etc.
}