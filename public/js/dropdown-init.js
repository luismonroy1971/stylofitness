/**
 * STYLOFITNESS - Dropdown Initialization
 * JavaScript para asegurar que los dropdowns funcionen correctamente
 */

(function() {
    'use strict';
    
    // Función para inicializar dropdowns
    function initializeDropdowns() {
        // Verificar si Bootstrap está disponible
        if (typeof bootstrap === 'undefined') {
            console.warn('Bootstrap no está cargado. Los dropdowns pueden no funcionar correctamente.');
            return;
        }
        
        // Inicializar todos los dropdowns
        const dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        const dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
        
        console.log('Dropdowns inicializados:', dropdownList.length);
        
        // Manejar clics en elementos del dropdown
        document.addEventListener('click', function(e) {
            // Si se hace clic en un elemento del dropdown que no es un enlace, cerrar el dropdown
            if (e.target.closest('.dropdown-menu') && !e.target.closest('a')) {
                const dropdown = e.target.closest('.dropdown');
                if (dropdown) {
                    const dropdownToggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
                    if (dropdownToggle) {
                        const bsDropdown = bootstrap.Dropdown.getInstance(dropdownToggle);
                        if (bsDropdown) {
                            bsDropdown.hide();
                        }
                    }
                }
            }
        });
        
        // Agregar eventos personalizados para debugging
        document.addEventListener('show.bs.dropdown', function(e) {
            console.log('Dropdown abierto:', e.target);
        });
        
        document.addEventListener('hide.bs.dropdown', function(e) {
            console.log('Dropdown cerrado:', e.target);
        });
    }
    
    // Función para reinicializar dropdowns (útil para contenido dinámico)
    function reinitializeDropdowns() {
        // Destruir instancias existentes
        const existingDropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        existingDropdowns.forEach(function(element) {
            const instance = bootstrap.Dropdown.getInstance(element);
            if (instance) {
                instance.dispose();
            }
        });
        
        // Reinicializar
        initializeDropdowns();
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeDropdowns);
    } else {
        initializeDropdowns();
    }
    
    // Reinicializar después de navegación AJAX (si aplica)
    window.addEventListener('load', function() {
        setTimeout(initializeDropdowns, 100);
    });
    
    // Exponer función global para reinicialización manual
    window.StyleFitnessDropdowns = {
        init: initializeDropdowns,
        reinit: reinitializeDropdowns
    };
    
    // Fallback para dropdowns sin Bootstrap
    function fallbackDropdownHandler() {
        document.addEventListener('click', function(e) {
            if (e.target.matches('[data-bs-toggle="dropdown"]') || e.target.closest('[data-bs-toggle="dropdown"]')) {
                e.preventDefault();
                const toggle = e.target.matches('[data-bs-toggle="dropdown"]') ? e.target : e.target.closest('[data-bs-toggle="dropdown"]');
                const menu = toggle.nextElementSibling;
                
                if (menu && menu.classList.contains('dropdown-menu')) {
                    // Cerrar otros dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(function(openMenu) {
                        if (openMenu !== menu) {
                            openMenu.classList.remove('show');
                        }
                    });
                    
                    // Toggle el dropdown actual
                    menu.classList.toggle('show');
                }
            }
            
            // Cerrar dropdowns al hacer clic fuera
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                    menu.classList.remove('show');
                });
            }
        });
    }
    
    // Activar fallback si Bootstrap no está disponible después de 1 segundo
    setTimeout(function() {
        if (typeof bootstrap === 'undefined') {
            console.warn('Bootstrap no detectado. Activando fallback para dropdowns.');
            fallbackDropdownHandler();
        }
    }, 1000);
    
})();