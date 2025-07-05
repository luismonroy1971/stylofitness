/**
 * Scripts JavaScript para el Manual de Usuario - StyloFitness
 * Funcionalidades interactivas del manual
 */

// Datos de las pantallas por rol
const roleScreens = {
    admin: {
        name: 'Administrador',
        icon: '👑',
        description: 'Control total del sistema',
        screens: [
            { name: 'Dashboard Administrativo', path: '/admin/dashboard', status: 'implemented' },
            { name: 'Gestión de Usuarios', path: '/admin/users', status: 'implemented' },
            { name: 'Gestión de Productos', path: '/admin/products', status: 'implemented' },
            { name: 'Gestión de Rutinas', path: '/admin/routines', status: 'implemented' },
            { name: 'Gestión de Ejercicios', path: '/admin/exercises', status: 'implemented' },
            { name: 'Gestión de Órdenes', path: '/admin/orders', status: 'implemented' },
            { name: 'Gestión de Clases', path: '/admin/classes', status: 'implemented' },
            { name: 'Reportes Básicos', path: '/admin/reports', status: 'partial' },
            { name: 'Configuración del Sistema', path: '/admin/settings', status: 'implemented' },
            { name: 'Gestión de Contenido', path: '/admin/content', status: 'implemented' },
            { name: 'Ofertas Especiales', path: '/admin/offers', status: 'implemented' },
            { name: 'Testimonios', path: '/admin/testimonials', status: 'implemented' },
            { name: 'Características', path: '/admin/features', status: 'implemented' },
            { name: 'Reportes Avanzados', path: '/admin/advanced-reports', status: 'missing' },
            { name: 'Sistema de Notificaciones', path: '/admin/notifications', status: 'missing' }
        ],
        missingFeatures: [
            { name: 'Reportes Avanzados', description: 'Analytics detallados, métricas de rendimiento, exportación avanzada', priority: 'high' },
            { name: 'Sistema de Notificaciones', description: 'Push notifications, emails automáticos, alertas en tiempo real', priority: 'high' },
            { name: 'Auditoría del Sistema', description: 'Logs de actividad, seguimiento de cambios, historial de acciones', priority: 'medium' },
            { name: 'Backup Automático', description: 'Respaldos programados, restauración de datos', priority: 'medium' }
        ]
    },
    trainer: {
        name: 'Entrenador/Instructor',
        icon: '💪',
        description: 'Gestión de rutinas y clientes',
        screens: [
            { name: 'Dashboard del Entrenador', path: '/trainer/dashboard', status: 'implemented' },
            { name: 'Mis Clientes', path: '/trainer/clients', status: 'implemented' },
            { name: 'Plantillas de Rutinas', path: '/trainer/templates', status: 'implemented' },
            { name: 'Crear Rutina', path: '/trainer/routines/create', status: 'implemented' },
            { name: 'Editar Rutina', path: '/trainer/routines/edit', status: 'implemented' },
            { name: 'Asignar Rutina', path: '/trainer/routines/assign', status: 'implemented' },
            { name: 'Progreso de Clientes', path: '/trainer/progress', status: 'partial' },
            { name: 'Calendario de Clases', path: '/trainer/schedule', status: 'implemented' },
            { name: 'Gestión de Ejercicios', path: '/trainer/exercises', status: 'implemented' },
            { name: 'Perfil del Entrenador', path: '/trainer/profile', status: 'implemented' },
            { name: 'Chat con Clientes', path: '/trainer/chat', status: 'missing' },
            { name: 'Evaluaciones Físicas', path: '/trainer/assessments', status: 'missing' }
        ],
        missingFeatures: [
            { name: 'Chat con Clientes', description: 'Comunicación directa, mensajería en tiempo real, notificaciones', priority: 'high' },
            { name: 'Evaluaciones Físicas', description: 'Mediciones corporales, fotos de progreso, análisis de composición', priority: 'high' },
            { name: 'Calendario Avanzado', description: 'Sincronización externa, recordatorios automáticos, disponibilidad', priority: 'medium' },
            { name: 'Certificaciones', description: 'Gestión de certificados, renovaciones, especialidades', priority: 'low' }
        ]
    },
    staff: {
        name: 'Staff/Personal',
        icon: '👥',
        description: 'Operaciones diarias del gimnasio',
        screens: [
            { name: 'Dashboard del Staff', path: '/staff/dashboard', status: 'partial' },
            { name: 'Check-in de Miembros', path: '/staff/checkin', status: 'missing' },
            { name: 'Gestión de Membresías', path: '/staff/memberships', status: 'implemented' },
            { name: 'Atención al Cliente', path: '/staff/support', status: 'missing' },
            { name: 'Reservas de Clases', path: '/staff/bookings', status: 'implemented' },
            { name: 'Inventario Básico', path: '/staff/inventory', status: 'implemented' },
            { name: 'Reportes de Asistencia', path: '/staff/attendance', status: 'partial' },
            { name: 'Perfil del Staff', path: '/staff/profile', status: 'implemented' }
        ],
        missingFeatures: [
            { name: 'Check-in Digital', description: 'Control de acceso, validación de membresías, registro automático', priority: 'high' },
            { name: 'Sistema de Tickets', description: 'Atención al cliente, seguimiento de problemas, resolución', priority: 'high' },
            { name: 'Gestión de Equipos', description: 'Mantenimiento, reservas, estado de equipos', priority: 'medium' },
            { name: 'Horarios del Personal', description: 'Turnos, disponibilidad, gestión de horarios', priority: 'medium' }
        ]
    },
    client: {
        name: 'Cliente',
        icon: '🏃‍♂️',
        description: 'Rutinas, tienda y clases',
        screens: [
            { name: 'Dashboard del Cliente', path: '/dashboard', status: 'implemented' },
            { name: 'Mis Rutinas', path: '/routines', status: 'implemented' },
            { name: 'Registrar Entrenamiento', path: '/routines/log', status: 'implemented' },
            { name: 'Mi Progreso', path: '/progress', status: 'partial' },
            { name: 'Tienda', path: '/store', status: 'implemented' },
            { name: 'Carrito de Compras', path: '/cart', status: 'implemented' },
            { name: 'Lista de Deseos', path: '/wishlist', status: 'implemented' },
            { name: 'Checkout', path: '/checkout', status: 'implemented' },
            { name: 'Clases Grupales', path: '/classes', status: 'implemented' },
            { name: 'Reservar Clase', path: '/classes/book', status: 'implemented' },
            { name: 'Mi Perfil', path: '/profile', status: 'implemented' },
            { name: 'Configuración', path: '/settings', status: 'implemented' },
            { name: 'Historial de Compras', path: '/orders', status: 'implemented' },
            { name: 'Progreso Avanzado', path: '/advanced-progress', status: 'missing' },
            { name: 'Gamificación', path: '/achievements', status: 'missing' }
        ],
        missingFeatures: [
            { name: 'Progreso Avanzado', description: 'Gráficos detallados, análisis de tendencias, comparativas', priority: 'high' },
            { name: 'Sistema de Logros', description: 'Gamificación, badges, rankings, desafíos', priority: 'medium' },
            { name: 'Nutrición', description: 'Planes alimenticios, seguimiento calórico, recetas', priority: 'medium' },
            { name: 'Comunidad', description: 'Foros, grupos, compartir progreso, redes sociales', priority: 'low' }
        ]
    }
};

// Función para abrir modal
function openModal(role) {
    const modal = document.getElementById('roleModal');
    const data = roleScreens[role];
    
    if (!data) return;
    
    // Actualizar contenido del modal
    document.getElementById('modalTitle').innerHTML = `${data.icon} ${data.name}`;
    
    // Actualizar estadísticas
    const implemented = data.screens.filter(s => s.status === 'implemented').length;
    const partial = data.screens.filter(s => s.status === 'partial').length;
    const missing = data.screens.filter(s => s.status === 'missing').length;
    const total = data.screens.length;
    const percentage = Math.round((implemented + partial * 0.5) / total * 100);
    
    document.getElementById('modalStats').innerHTML = `
        <div class="role-stats">
            <div class="role-stat">
                <span class="role-stat-number" style="color: var(--success-color);">${implemented}</span>
                <span class="role-stat-label">Implementadas</span>
            </div>
            <div class="role-stat">
                <span class="role-stat-number" style="color: var(--warning-color);">${partial}</span>
                <span class="role-stat-label">Parciales</span>
            </div>
            <div class="role-stat">
                <span class="role-stat-number" style="color: var(--danger-color);">${missing}</span>
                <span class="role-stat-label">Faltantes</span>
            </div>
        </div>
        <div class="progress-container">
            <div class="progress-label">
                <span>Progreso General</span>
                <span>${percentage}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: ${percentage}%;"></div>
            </div>
        </div>
    `;
    
    // Actualizar lista de pantallas
    const screensList = document.getElementById('screensList');
    screensList.innerHTML = data.screens.map(screen => {
        const statusClass = `status-${screen.status}`;
        const statusText = {
            'implemented': '✅ Implementado',
            'partial': '⚠️ Parcial',
            'missing': '❌ Faltante'
        }[screen.status];
        
        return `
            <div class="screen-item">
                <div class="screen-info">
                    <div class="screen-name">${screen.name}</div>
                    <div class="screen-path">${screen.path}</div>
                </div>
                <div class="screen-status ${statusClass}">${statusText}</div>
            </div>
        `;
    }).join('');
    
    // Actualizar funcionalidades faltantes
    const missingList = document.getElementById('missingFeaturesList');
    missingList.innerHTML = data.missingFeatures.map(feature => {
        const priorityClass = `priority-${feature.priority}`;
        const priorityText = {
            'high': '🔥 Alta',
            'medium': '⚡ Media',
            'low': '📋 Baja'
        }[feature.priority];
        
        return `
            <div class="feature-item">
                <div class="feature-name">${feature.name}</div>
                <div class="feature-description">${feature.description}</div>
                <span class="feature-priority ${priorityClass}">${priorityText}</span>
            </div>
        `;
    }).join('');
    
    // Aplicar colores del rol al modal
    const modalHeader = modal.querySelector('.modal-header');
    const roleColors = {
        admin: { primary: '#e74c3c', secondary: '#c0392b' },
        trainer: { primary: '#3498db', secondary: '#2980b9' },
        staff: { primary: '#f39c12', secondary: '#e67e22' },
        client: { primary: '#27ae60', secondary: '#229954' }
    };
    
    const colors = roleColors[role];
    modalHeader.style.background = `linear-gradient(135deg, ${colors.primary}, ${colors.secondary})`;
    
    // Mostrar modal
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Función para cerrar modal
function closeModal() {
    const modal = document.getElementById('roleModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Función para actualizar estadísticas generales
function updateGeneralStats() {
    let totalScreens = 0;
    let totalImplemented = 0;
    let totalPartial = 0;
    let totalMissing = 0;
    
    Object.values(roleScreens).forEach(role => {
        totalScreens += role.screens.length;
        totalImplemented += role.screens.filter(s => s.status === 'implemented').length;
        totalPartial += role.screens.filter(s => s.status === 'partial').length;
        totalMissing += role.screens.filter(s => s.status === 'missing').length;
    });
    
    const overallProgress = Math.round((totalImplemented + totalPartial * 0.5) / totalScreens * 100);
    
    // Actualizar elementos en el DOM
    const statsElements = {
        'total-screens': totalScreens,
        'implemented-screens': totalImplemented,
        'partial-screens': totalPartial,
        'missing-screens': totalMissing,
        'overall-progress': `${overallProgress}%`
    };
    
    Object.entries(statsElements).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    });
}

// Función para animar las barras de progreso
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
}

// Función para animar las tarjetas al cargar
function animateCards() {
    const cards = document.querySelectorAll('.role-card, .stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Función para exportar datos (placeholder)
function exportData(format) {
    console.log(`Exportando datos en formato: ${format}`);
    // Aquí se implementaría la lógica de exportación
    alert(`Funcionalidad de exportación en ${format} será implementada próximamente.`);
}

// Función para buscar en el manual
function searchManual(query) {
    console.log(`Buscando: ${query}`);
    // Aquí se implementaría la lógica de búsqueda
    alert(`Funcionalidad de búsqueda será implementada próximamente.`);
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar estadísticas generales
    updateGeneralStats();
    
    // Animar elementos al cargar
    setTimeout(() => {
        animateCards();
        animateProgressBars();
    }, 500);
    
    // Event listener para cerrar modal al hacer clic fuera
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('roleModal');
        if (event.target === modal) {
            closeModal();
        }
    });
    
    // Event listener para tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
    
    // Actualizar año en el footer
    const currentYear = new Date().getFullYear();
    const yearElement = document.getElementById('current-year');
    if (yearElement) {
        yearElement.textContent = currentYear;
    }
});

// Exportar funciones para uso global
window.openModal = openModal;
window.closeModal = closeModal;
window.exportData = exportData;
window.searchManual = searchManual;