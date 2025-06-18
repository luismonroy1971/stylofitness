/**
 * JavaScript para Clases Grupales - STYLOFITNESS
 */

class ClassesManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initializeAnimations();
    }
    
    bindEvents() {
        // Filtros responsivos en móvil
        const filterToggle = document.querySelector('.filter-toggle');
        const filterForm = document.querySelector('.filter-form');
        
        if (filterToggle && filterForm) {
            filterToggle.addEventListener('click', () => {
                filterForm.classList.toggle('active');
                filterToggle.classList.toggle('active');
            });
        }
        
        // Botones de reserva
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-book') || e.target.closest('.btn-book')) {
                e.preventDefault();
                const bookBtn = e.target.matches('.btn-book') ? e.target : e.target.closest('.btn-book');
                const classId = bookBtn.getAttribute('href').split('/').pop();
                this.handleBooking(classId);
            }
        });
    }
    
    initializeAnimations() {
        // Animación para las tarjetas de clases
        const classCards = document.querySelectorAll('.class-card');
        
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            classCards.forEach(card => {
                observer.observe(card);
            });
        } else {
            // Fallback para navegadores que no soportan IntersectionObserver
            classCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('visible');
                }, index * 100);
            });
        }
    }
    
    handleBooking(classId) {
        // Aquí iría la lógica para reservar una clase
        // Por ahora, simplemente redirigimos
        window.location.href = `/classes/${classId}/book`;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new ClassesManager();
});