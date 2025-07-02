/**
 * JavaScript para gestión de Testimonios
 * StyleFitness Admin Panel
 */

class TestimonialsManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.currentFilters = {};
        this.editingTestimonialId = null;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadTestimonials();
        this.loadStats();
    }

    bindEvents() {
        // Filtros
        document.getElementById('statusFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('featuredFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('searchFilter').addEventListener('input', this.debounce(() => this.applyFilters(), 300));
        
        // Formulario
        document.getElementById('testimonialForm').addEventListener('submit', (e) => this.handleFormSubmit(e));
        
        // Modal events
        $('#testimonialModal').on('hidden.bs.modal', () => this.resetForm());
        
        // Botón de confirmación de eliminación
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => this.confirmDelete());
        
        // Rating stars
        this.bindRatingEvents();
    }

    bindRatingEvents() {
        const stars = document.querySelectorAll('.rating-star');
        stars.forEach((star, index) => {
            star.addEventListener('click', () => this.setRating(index + 1));
            star.addEventListener('mouseover', () => this.highlightStars(index + 1));
        });
        
        document.querySelector('.rating-container').addEventListener('mouseleave', () => {
            this.highlightStars(this.getCurrentRating());
        });
    }

    setRating(rating) {
        document.getElementById('testimonialRating').value = rating;
        this.highlightStars(rating);
    }

    getCurrentRating() {
        return parseInt(document.getElementById('testimonialRating').value) || 0;
    }

    highlightStars(rating) {
        const stars = document.querySelectorAll('.rating-star');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    async loadTestimonials() {
        try {
            this.showLoading();
            
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                ...this.currentFilters
            });

            const response = await fetch(`/admin/landing/testimonials/get?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderTestimonials(data.data.items);
                this.renderPagination(data.data.pagination);
                this.updateStats(data.stats);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cargar testimonios: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    renderTestimonials(testimonials) {
        const tbody = document.getElementById('testimonialsTableBody');
        
        if (testimonials.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No se encontraron testimonios</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = testimonials.map(testimonial => `
            <tr>
                <td>${testimonial.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        ${testimonial.client_image ? 
                            `<img src="/${testimonial.client_image}" alt="Cliente" class="rounded-circle mr-2" style="width: 40px; height: 40px; object-fit: cover;">` : 
                            '<div class="rounded-circle bg-secondary mr-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-user text-white"></i></div>'
                        }
                        <div>
                            <strong>${this.escapeHtml(testimonial.client_name)}</strong>
                            ${testimonial.client_position ? `<br><small class="text-muted">${this.escapeHtml(testimonial.client_position)}</small>` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="testimonial-content">
                        ${this.escapeHtml(testimonial.content.substring(0, 80))}${testimonial.content.length > 80 ? '...' : ''}
                    </div>
                </td>
                <td>
                    <div class="rating-display">
                        ${this.renderStars(testimonial.rating)}
                        <span class="ml-1">(${testimonial.rating})</span>
                    </div>
                </td>
                <td>
                    <span class="badge ${testimonial.is_featured ? 'badge-warning' : 'badge-secondary'}">
                        ${testimonial.is_featured ? 'Destacado' : 'Normal'}
                    </span>
                </td>
                <td>${testimonial.display_order}</td>
                <td>
                    <span class="status-badge ${testimonial.is_active ? 'status-active' : 'status-inactive'}">
                        ${testimonial.is_active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="testimonialsManager.editTestimonial(${testimonial.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm ${testimonial.is_active ? 'btn-warning' : 'btn-success'}" 
                                onclick="testimonialsManager.toggleStatus(${testimonial.id})" 
                                title="${testimonial.is_active ? 'Desactivar' : 'Activar'}">
                            <i class="fas fa-${testimonial.is_active ? 'eye-slash' : 'eye'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="testimonialsManager.deleteTestimonial(${testimonial.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderStars(rating) {
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            starsHtml += `<i class="fas fa-star ${i <= rating ? 'text-warning' : 'text-muted'}"></i>`;
        }
        return starsHtml;
    }

    renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        
        if (pagination.totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHtml = '<nav><ul class="pagination">';
        
        // Botón anterior
        paginationHtml += `
            <li class="page-item ${pagination.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="testimonialsManager.goToPage(${pagination.currentPage - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
        
        // Páginas
        for (let i = 1; i <= pagination.totalPages; i++) {
            if (i === 1 || i === pagination.totalPages || (i >= pagination.currentPage - 2 && i <= pagination.currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="testimonialsManager.goToPage(${i})">${i}</a>
                    </li>
                `;
            } else if (i === pagination.currentPage - 3 || i === pagination.currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        // Botón siguiente
        paginationHtml += `
            <li class="page-item ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="testimonialsManager.goToPage(${pagination.currentPage + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
        
        paginationHtml += '</ul></nav>';
        container.innerHTML = paginationHtml;
    }

    updateStats(stats) {
        document.getElementById('totalTestimonials').textContent = stats.total || 0;
        document.getElementById('activeTestimonials').textContent = stats.active || 0;
        document.getElementById('featuredTestimonials').textContent = stats.featured || 0;
        document.getElementById('avgRating').textContent = stats.avgRating ? stats.avgRating.toFixed(1) : '0.0';
    }

    applyFilters() {
        this.currentFilters = {
            status: document.getElementById('statusFilter').value,
            featured: document.getElementById('featuredFilter').value,
            search: document.getElementById('searchFilter').value
        };
        this.currentPage = 1;
        this.loadTestimonials();
    }

    clearFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('featuredFilter').value = '';
        document.getElementById('searchFilter').value = '';
        this.currentFilters = {};
        this.currentPage = 1;
        this.loadTestimonials();
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadTestimonials();
    }

    async editTestimonial(id) {
        try {
            const response = await fetch(`/admin/landing/testimonials/get-single?id=${id}`);
            const data = await response.json();

            if (data.success) {
                this.populateForm(data.data);
                this.editingTestimonialId = id;
                $('#testimonialModal').modal('show');
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cargar testimonio: ' + error.message);
        }
    }

    populateForm(testimonial) {
        document.getElementById('testimonialModalTitle').textContent = 'Editar Testimonio';
        document.getElementById('testimonialId').value = testimonial.id;
        document.getElementById('clientName').value = testimonial.client_name;
        document.getElementById('clientPosition').value = testimonial.client_position || '';
        document.getElementById('testimonialContent').value = testimonial.content;
        document.getElementById('testimonialRating').value = testimonial.rating;
        document.getElementById('displayOrder').value = testimonial.display_order;
        document.getElementById('testimonialActive').checked = testimonial.is_active;
        document.getElementById('testimonialFeatured').checked = testimonial.is_featured;
        
        // Actualizar estrellas
        this.highlightStars(testimonial.rating);
        
        // Mostrar imagen actual si existe
        if (testimonial.client_image) {
            document.getElementById('currentImagePreview').style.display = 'block';
            document.getElementById('currentImage').src = '/' + testimonial.client_image;
        }
    }

    resetForm() {
        document.getElementById('testimonialModalTitle').textContent = 'Nuevo Testimonio';
        document.getElementById('testimonialForm').reset();
        document.getElementById('testimonialId').value = '';
        document.getElementById('currentImagePreview').style.display = 'none';
        this.editingTestimonialId = null;
        this.highlightStars(0);
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(e.target);
            const url = this.editingTestimonialId ? 
                '/admin/landing/testimonials/update' : 
                '/admin/landing/testimonials/create';

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                $('#testimonialModal').modal('hide');
                this.loadTestimonials();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al guardar testimonio: ' + error.message);
        }
    }

    async toggleStatus(id) {
        try {
            const response = await fetch('/admin/landing/testimonials/toggle-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                this.loadTestimonials();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al cambiar estado: ' + error.message);
        }
    }

    deleteTestimonial(id) {
        this.testimonialToDelete = id;
        $('#deleteModal').modal('show');
    }

    async confirmDelete() {
        if (!this.testimonialToDelete) return;

        try {
            const response = await fetch('/admin/landing/testimonials/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${this.testimonialToDelete}`
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                $('#deleteModal').modal('hide');
                this.loadTestimonials();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Error al eliminar testimonio: ' + error.message);
        }

        this.testimonialToDelete = null;
    }

    // Métodos auxiliares
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES');
    }

    showLoading() {
        document.body.style.cursor = 'wait';
    }

    hideLoading() {
        document.body.style.cursor = 'default';
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        const container = document.querySelector('.admin-container');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
}

// Funciones globales
function openCreateModal() {
    testimonialsManager.resetForm();
    $('#testimonialModal').modal('show');
}

function clearFilters() {
    testimonialsManager.clearFilters();
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.testimonialsManager = new TestimonialsManager();
});

// CSS adicional para las estrellas
const additionalCSS = `
.rating-container {
    display: flex;
    align-items: center;
    gap: 2px;
}

.rating-star {
    cursor: pointer;
    transition: color 0.2s;
    font-size: 1.2em;
}

.rating-star:hover {
    color: #ffc107 !important;
}

.rating-star.active {
    color: #ffc107 !important;
}

.rating-display .fas.fa-star {
    font-size: 0.9em;
}

.testimonial-content {
    max-width: 200px;
    word-wrap: break-word;
}
`;

// Agregar CSS al documento
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);}