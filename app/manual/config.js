/**
 * Configuración del Manual de Usuario - StyloFitness
 * Este archivo contiene todas las configuraciones personalizables del manual
 */

const ManualConfig = {
    // Información general del sistema
    system: {
        name: 'StyloFitness',
        version: '1.0',
        lastUpdate: 'Diciembre 2024',
        environment: 'Desarrollo'
    },

    // Configuración de colores por rol
    roleColors: {
        admin: {
            primary: '#e74c3c',
            secondary: '#c0392b',
            light: '#fadbd8',
            icon: '👑'
        },
        trainer: {
            primary: '#3498db',
            secondary: '#2980b9',
            light: '#d6eaf8',
            icon: '💪'
        },
        staff: {
            primary: '#f39c12',
            secondary: '#e67e22',
            light: '#fdeaa7',
            icon: '👥'
        },
        client: {
            primary: '#27ae60',
            secondary: '#229954',
            light: '#d5f4e6',
            icon: '🏃‍♂️'
        }
    },

    // Estados de implementación
    implementationStatus: {
        implemented: {
            icon: '✅',
            text: 'Implementado',
            color: '#27ae60'
        },
        partial: {
            icon: '⚠️',
            text: 'Parcial',
            color: '#f39c12'
        },
        missing: {
            icon: '❌',
            text: 'Faltante',
            color: '#e74c3c'
        }
    },

    // Configuración de prioridades
    priorities: {
        high: {
            color: '#e74c3c',
            text: 'Alta',
            icon: '🔥'
        },
        medium: {
            color: '#f39c12',
            text: 'Media',
            icon: '⚡'
        },
        low: {
            color: '#27ae60',
            text: 'Baja',
            icon: '📋'
        }
    },

    // Rutas base del sistema
    routes: {
        base: '/',
        admin: '/admin',
        trainer: '/trainer',
        auth: '/auth',
        api: '/api'
    },

    // Configuración de la interfaz
    ui: {
        animationDuration: 300,
        modalMaxWidth: '90%',
        cardBorderRadius: '12px',
        shadowIntensity: '0 4px 6px rgba(0, 0, 0, 0.1)'
    },

    // Estadísticas del sistema
    statistics: {
        totalScreens: 50,
        implementedRoles: 4,
        totalControllers: 12,
        totalModels: 11,
        totalViews: 35,
        overallProgress: {
            core: 85,
            ui: 90,
            roles: 95,
            security: 80,
            reports: 40,
            communication: 20,
            gamification: 0
        }
    },

    // Funcionalidades por rol
    roleFeatures: {
        admin: {
            total: 15,
            implemented: 13,
            partial: 1,
            missing: 1,
            percentage: 85
        },
        trainer: {
            total: 11,
            implemented: 9,
            partial: 1,
            missing: 1,
            percentage: 80
        },
        staff: {
            total: 8,
            implemented: 5,
            partial: 1,
            missing: 2,
            percentage: 60
        },
        client: {
            total: 15,
            implemented: 11,
            partial: 2,
            missing: 2,
            percentage: 75
        }
    },

    // Mensajes del sistema
    messages: {
        loading: 'Cargando información...',
        noData: 'No hay datos disponibles',
        error: 'Error al cargar la información',
        success: 'Operación completada exitosamente'
    },

    // Configuración de exportación
    export: {
        formats: ['PDF', 'Excel', 'JSON'],
        defaultFormat: 'PDF',
        includeImages: true,
        includeStatistics: true
    }
};

// Función para obtener configuración por rol
function getRoleConfig(role) {
    return {
        colors: ManualConfig.roleColors[role] || ManualConfig.roleColors.client,
        features: ManualConfig.roleFeatures[role] || ManualConfig.roleFeatures.client
    };
}

// Función para obtener estadísticas generales
function getSystemStatistics() {
    return ManualConfig.statistics;
}

// Función para obtener estado de implementación
function getImplementationStatus(status) {
    return ManualConfig.implementationStatus[status] || ManualConfig.implementationStatus.missing;
}

// Exportar configuración para uso en otros archivos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        ManualConfig,
        getRoleConfig,
        getSystemStatistics,
        getImplementationStatus
    };
}