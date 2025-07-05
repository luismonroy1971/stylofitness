/**
 * Manual Detallado por Perfiles - JavaScript
 * Funcionalidades interactivas para la navegación entre perfiles
 */

// Configuración de perfiles
const profilesConfig = {
    admin: {
        name: 'Administrador',
        icon: 'fas fa-crown',
        color: '#7c3aed',
        screens: 15,
        implemented: 85,
        missing: 3
    },
    trainer: {
        name: 'Entrenador',
        icon: 'fas fa-dumbbell',
        color: '#059669',
        screens: 11,
        implemented: 80,
        missing: 4
    },
    staff: {
        name: 'Staff',
        icon: 'fas fa-users-cog',
        color: '#d97706',
        screens: 8,
        implemented: 60,
        missing: 5
    },
    client: {
        name: 'Cliente',
        icon: 'fas fa-user',
        color: '#2563eb',
        screens: 12,
        implemented: 75,
        missing: 6
    }
};

// Estado actual
let currentProfile = 'admin';
let isAnimating = false;

/**
 * Cambia el perfil activo
 * @param {string} profileType - Tipo de perfil (admin, trainer, staff, client)
 */
function showProfile(profileType) {
    if (isAnimating || profileType === currentProfile) return;
    
    isAnimating = true;
    
    // Actualizar navegación
    updateNavigation(profileType);
    
    // Cambiar sección con animación
    changeProfileSection(profileType);
    
    // Actualizar estado
    currentProfile = profileType;
    
    // Actualizar URL sin recargar
    updateURL(profileType);
    
    setTimeout(() => {
        isAnimating = false;
    }, 400);
}

/**
 * Actualiza la navegación activa
 * @param {string} profileType - Tipo de perfil
 */
function updateNavigation(profileType) {
    // Remover clase active de todos los nav-links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    
    // Activar el nav-link correspondiente
    const activeLink = document.querySelector(`[onclick="showProfile('${profileType}')"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

/**
 * Cambia la sección de perfil con animación
 * @param {string} profileType - Tipo de perfil
 */
function changeProfileSection(profileType) {
    const currentSection = document.querySelector('.profile-section.active');
    const newSection = document.getElementById(profileType + '-profile');
    
    if (!newSection) return;
    
    // Ocultar sección actual
    if (currentSection) {
        currentSection.style.opacity = '0';
        currentSection.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            currentSection.classList.remove('active');
            currentSection.style.opacity = '';
            currentSection.style.transform = '';
        }, 200);
    }
    
    // Mostrar nueva sección
    setTimeout(() => {
        newSection.classList.add('active');
        newSection.style.opacity = '0';
        newSection.style.transform = 'translateX(20px)';
        
        setTimeout(() => {
            newSection.style.opacity = '1';
            newSection.style.transform = 'translateX(0)';
            
            // Animar tarjetas de la nueva sección
            animateScreenCards(newSection);
        }, 50);
    }, 200);
}

/**
 * Anima las tarjetas de pantallas
 * @param {Element} section - Sección del perfil
 */
function animateScreenCards(section) {
    const cards = section.querySelectorAll('.screen-card');
    
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Actualiza la URL sin recargar la página
 * @param {string} profileType - Tipo de perfil
 */
function updateURL(profileType) {
    const url = new URL(window.location);
    url.searchParams.set('profile', profileType);
    window.history.pushState({ profile: profileType }, '', url);
}

/**
 * Obtiene el perfil desde la URL
 * @returns {string} - Tipo de perfil
 */
function getProfileFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const profile = urlParams.get('profile');
    return profilesConfig[profile] ? profile : 'admin';
}

/**
 * Anima los números de estadísticas
 * @param {Element} element - Elemento que contiene el número
 * @param {number} target - Número objetivo
 * @param {number} duration - Duración de la animación en ms
 */
function animateNumber(element, target, duration = 1000) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        
        if (target % 1 === 0) {
            element.textContent = Math.floor(current);
        } else {
            element.textContent = current.toFixed(1);
        }
    }, 16);
}

/**
 * Anima las estadísticas del perfil
 * @param {string} profileType - Tipo de perfil
 */
function animateProfileStats(profileType) {
    const config = profilesConfig[profileType];
    if (!config) return;
    
    const section = document.getElementById(profileType + '-profile');
    if (!section) return;
    
    const statNumbers = section.querySelectorAll('.stat-number');
    const values = [config.screens, config.implemented, config.missing];
    
    statNumbers.forEach((element, index) => {
        if (values[index] !== undefined) {
            setTimeout(() => {
                animateNumber(element, values[index]);
            }, index * 200);
        }
    });
}

/**
 * Maneja el evento de cambio de historial del navegador
 */
function handlePopState(event) {
    if (event.state && event.state.profile) {
        showProfile(event.state.profile);
    }
}

/**
 * Añade tooltips a elementos con información adicional
 */
function initializeTooltips() {
    const statusBadges = document.querySelectorAll('.status-badge');
    
    statusBadges.forEach(badge => {
        const status = badge.textContent.toLowerCase();
        let tooltip = '';
        
        switch(status) {
            case 'implementado':
                tooltip = 'Funcionalidad completamente desarrollada y operativa';
                break;
            case 'parcial':
                tooltip = 'Funcionalidad básica implementada, faltan características avanzadas';
                break;
            case 'faltante':
                tooltip = 'Funcionalidad no implementada, requiere desarrollo completo';
                break;
        }
        
        if (tooltip) {
            badge.setAttribute('data-tooltip', tooltip);
        }
    });
}

/**
 * Maneja atajos de teclado
 * @param {KeyboardEvent} event - Evento de teclado
 */
function handleKeyboardShortcuts(event) {
    // Solo procesar si no hay elementos de entrada activos
    if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') {
        return;
    }
    
    const profiles = ['admin', 'trainer', 'staff', 'client'];
    
    switch(event.key) {
        case '1':
            showProfile('admin');
            break;
        case '2':
            showProfile('trainer');
            break;
        case '3':
            showProfile('staff');
            break;
        case '4':
            showProfile('client');
            break;
        case 'ArrowLeft':
            const currentIndex = profiles.indexOf(currentProfile);
            const prevIndex = currentIndex > 0 ? currentIndex - 1 : profiles.length - 1;
            showProfile(profiles[prevIndex]);
            event.preventDefault();
            break;
        case 'ArrowRight':
            const currentIdx = profiles.indexOf(currentProfile);
            const nextIndex = currentIdx < profiles.length - 1 ? currentIdx + 1 : 0;
            showProfile(profiles[nextIndex]);
            event.preventDefault();
            break;
    }
}

/**
 * Añade funcionalidad de búsqueda
 */
function initializeSearch() {
    // Crear input de búsqueda
    const searchContainer = document.createElement('div');
    searchContainer.className = 'search-container mb-3';
    searchContainer.innerHTML = `
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control" id="searchInput" placeholder="Buscar pantallas...">
        </div>
    `;
    
    // Insertar antes del contenido
    const content = document.querySelector('.content');
    content.insertBefore(searchContainer, content.firstChild);
    
    // Manejar búsqueda
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', handleSearch);
}

/**
 * Maneja la funcionalidad de búsqueda
 * @param {Event} event - Evento de input
 */
function handleSearch(event) {
    const query = event.target.value.toLowerCase();
    const allCards = document.querySelectorAll('.screen-card');
    
    allCards.forEach(card => {
        const title = card.querySelector('.screen-title').textContent.toLowerCase();
        const description = card.querySelector('.screen-description').textContent.toLowerCase();
        const features = Array.from(card.querySelectorAll('.screen-features li'))
            .map(li => li.textContent.toLowerCase())
            .join(' ');
        
        const matches = title.includes(query) || 
                       description.includes(query) || 
                       features.includes(query);
        
        card.style.display = matches ? 'block' : 'none';
        
        if (matches && query.length > 0) {
            card.style.animation = 'highlight 0.5s ease';
        }
    });
}

/**
 * Inicialización cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    // Obtener perfil inicial desde URL
    const initialProfile = getProfileFromURL();
    
    // Mostrar perfil inicial
    if (initialProfile !== 'admin') {
        showProfile(initialProfile);
    }
    
    // Animar estadísticas del perfil inicial
    setTimeout(() => {
        animateProfileStats(initialProfile);
    }, 500);
    
    // Inicializar tooltips
    initializeTooltips();
    
    // Inicializar búsqueda
    initializeSearch();
    
    // Animar tarjetas iniciales
    setTimeout(() => {
        const initialSection = document.querySelector('.profile-section.active');
        if (initialSection) {
            animateScreenCards(initialSection);
        }
    }, 300);
    
    // Event listeners
    window.addEventListener('popstate', handlePopState);
    document.addEventListener('keydown', handleKeyboardShortcuts);
    
    // Añadir información de atajos de teclado
    const helpText = document.createElement('div');
    helpText.className = 'help-text text-muted small mt-3 text-center';
    helpText.innerHTML = `
        <i class="fas fa-keyboard me-1"></i>
        Atajos: <kbd>1-4</kbd> para cambiar perfiles, <kbd>←→</kbd> para navegar
    `;
    document.querySelector('.navigation').appendChild(helpText);
});

// CSS adicional para animaciones
const additionalStyles = `
<style>
@keyframes highlight {
    0% { background-color: transparent; }
    50% { background-color: rgba(37, 99, 235, 0.1); }
    100% { background-color: transparent; }
}

.search-container {
    max-width: 400px;
    margin: 0 auto;
}

.help-text kbd {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    padding: 2px 4px;
    font-size: 0.8em;
}

.help-text {
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.help-text:hover {
    opacity: 1;
}
</style>
`;

// Inyectar estilos adicionales
document.head.insertAdjacentHTML('beforeend', additionalStyles);

// Exportar funciones para uso global
window.showProfile = showProfile;
window.profilesConfig = profilesConfig;