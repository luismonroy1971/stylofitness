<!-- Página de Contacto - STYLOFITNESS -->
<section class="contact-hero bg-primary">
    <div class="container">
        <div class="hero-content text-center text-white" data-aos="fade-up">
            <h1 class="hero-title">
                <i class="fas fa-phone-alt"></i>
                Contáctanos
            </h1>
            <p class="hero-subtitle">
                Estamos aquí para ayudarte en tu transformación fitness. 
                Ponte en contacto con nuestro equipo de expertos.
            </p>
        </div>
    </div>
</section>

<section class="section contact-section">
    <div class="container">
        <div class="row">
            <!-- Formulario de Contacto -->
            <div class="col-lg-8">
                <div class="contact-form-card" data-aos="fade-right">
                    <div class="card-header">
                        <h2 class="section-title">
                            <i class="fas fa-envelope"></i>
                            Envíanos un Mensaje
                        </h2>
                        <p class="section-subtitle">
                            Completa el formulario y te responderemos en menos de 24 horas
                        </p>
                    </div>
                    
                    <form id="contact-form" class="contact-form" method="POST" action="<?php echo AppHelper::baseUrl('contact'); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="first_name">
                                    <i class="fas fa-user"></i>
                                    Nombre *
                                </label>
                                <input type="text" 
                                       id="first_name" 
                                       name="first_name" 
                                       class="form-control" 
                                       required 
                                       placeholder="Tu nombre">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="last_name">
                                    <i class="fas fa-user"></i>
                                    Apellido *
                                </label>
                                <input type="text" 
                                       id="last_name" 
                                       name="last_name" 
                                       class="form-control" 
                                       required 
                                       placeholder="Tu apellido">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="email">
                                    <i class="fas fa-envelope"></i>
                                    Email *
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       class="form-control" 
                                       required 
                                       placeholder="tu@email.com">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="phone">
                                    <i class="fas fa-phone"></i>
                                    Teléfono
                                </label>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       class="form-control" 
                                       placeholder="+51 999 888 777">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">
                                <i class="fas fa-tag"></i>
                                Asunto *
                            </label>
                            <select id="subject" name="subject" class="form-control" required>
                                <option value="">Selecciona un asunto</option>
                                <option value="info_general">Información General</option>
                                <option value="membresía">Consulta sobre Membresías</option>
                                <option value="rutinas">Rutinas Personalizadas</option>
                                <option value="clases">Clases Grupales</option>
                                <option value="tienda">Productos y Suplementos</option>
                                <option value="soporte_tecnico">Soporte Técnico</option>
                                <option value="instructor">Quiero ser Instructor</option>
                                <option value="empresa">Servicios Empresariales</option>
                                <option value="otros">Otros</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">
                                <i class="fas fa-comment"></i>
                                Mensaje *
                            </label>
                            <textarea id="message" 
                                      name="message" 
                                      class="form-control" 
                                      rows="6" 
                                      required 
                                      placeholder="Cuéntanos cómo podemos ayudarte..."></textarea>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">
                                Mínimo 10 caracteres. Máximo 1000 caracteres.
                            </small>
                        </div>
                        
                        <!-- Checkbox de políticas -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="privacy_policy" 
                                       name="privacy_policy" 
                                       required>
                                <label class="custom-control-label" for="privacy_policy">
                                    Acepto la <a href="<?php echo AppHelper::baseUrl('privacy'); ?>" target="_blank">Política de Privacidad</a> 
                                    y los <a href="<?php echo AppHelper::baseUrl('terms'); ?>" target="_blank">Términos de Servicio</a> *
                                </label>
                            </div>
                        </div>
                        
                        <!-- Newsletter opcional -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="newsletter" 
                                       name="newsletter" 
                                       value="1">
                                <label class="custom-control-label" for="newsletter">
                                    Quiero recibir noticias, ofertas y consejos fitness por email
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i>
                                Enviar Mensaje
                            </button>
                            
                            <button type="reset" class="btn btn-outline btn-lg">
                                <i class="fas fa-redo"></i>
                                Limpiar Formulario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Información de Contacto -->
            <div class="col-lg-4">
                <div class="contact-info" data-aos="fade-left">
                    <!-- Información Principal -->
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Nuestra Ubicación</h3>
                            <p>
                                Av. Principal 123<br>
                                San Isidro, Lima 27<br>
                                Perú
                            </p>
                            <a href="https://maps.google.com/?q=STYLOFITNESS" target="_blank" class="btn btn-sm btn-outline">
                                <i class="fas fa-map"></i>
                                Ver en Google Maps
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Teléfonos</h3>
                            <p>
                                <strong>Principal:</strong> <a href="tel:+51999888777">+51 999 888 777</a><br>
                                <strong>WhatsApp:</strong> <a href="https://wa.me/51999888777">+51 999 888 777</a><br>
                                <strong>Fijo:</strong> <a href="tel:+5114567890">+51 1 456 7890</a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Emails</h3>
                            <p>
                                <strong>General:</strong> <a href="mailto:info@stylofitness.com">info@stylofitness.com</a><br>
                                <strong>Ventas:</strong> <a href="mailto:ventas@stylofitness.com">ventas@stylofitness.com</a><br>
                                <strong>Soporte:</strong> <a href="mailto:soporte@stylofitness.com">soporte@stylofitness.com</a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Horarios de Atención</h3>
                            <p>
                                <strong>Lun - Vie:</strong> 6:00 AM - 11:00 PM<br>
                                <strong>Sábado:</strong> 7:00 AM - 10:00 PM<br>
                                <strong>Domingo:</strong> 8:00 AM - 8:00 PM<br>
                                <strong>Feriados:</strong> 8:00 AM - 6:00 PM
                            </p>
                        </div>
                    </div>
                    
                    <!-- Redes Sociales -->
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Síguenos</h3>
                            <div class="social-links">
                                <a href="https://facebook.com/stylofitness" target="_blank" class="social-link facebook">
                                    <i class="fab fa-facebook-f"></i>
                                    Facebook
                                </a>
                                <a href="https://instagram.com/stylofitness" target="_blank" class="social-link instagram">
                                    <i class="fab fa-instagram"></i>
                                    Instagram
                                </a>
                                <a href="https://youtube.com/stylofitness" target="_blank" class="social-link youtube">
                                    <i class="fab fa-youtube"></i>
                                    YouTube
                                </a>
                                <a href="https://tiktok.com/@stylofitness" target="_blank" class="social-link tiktok">
                                    <i class="fab fa-tiktok"></i>
                                    TikTok
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mapa de Ubicación -->
<section class="map-section">
    <div class="container-fluid p-0">
        <div class="map-container" data-aos="fade-up">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3901.3246!2d-77.0428!3d-12.0464!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTLCsDAyJzQ3LjAiUyA3N8KwMDInMzQuMSJX!5e0!3m2!1sen!2spe!4v1"
                width="100%" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
            
            <div class="map-overlay">
                <div class="map-info">
                    <h3>
                        <i class="fas fa-map-marker-alt"></i>
                        STYLOFITNESS Principal
                    </h3>
                    <p>Av. Principal 123, San Isidro, Lima</p>
                    <div class="map-actions">
                        <a href="https://maps.google.com/?q=STYLOFITNESS" target="_blank" class="btn btn-primary">
                            <i class="fas fa-directions"></i>
                            Cómo Llegar
                        </a>
                        <a href="tel:+51999888777" class="btn btn-outline">
                            <i class="fas fa-phone"></i>
                            Llamar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Rápidas -->
<section class="section faq-section bg-light">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <h2 class="section-title">
                <i class="fas fa-question-circle"></i>
                Preguntas Frecuentes
            </h2>
            <p class="section-subtitle">
                Respuestas rápidas a las consultas más comunes
            </p>
        </div>
        
        <div class="faq-grid">
            <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                <div class="faq-question">
                    <i class="fas fa-dumbbell"></i>
                    ¿Ofrecen rutinas personalizadas?
                </div>
                <div class="faq-answer">
                    Sí, nuestros instructores certificados crean rutinas personalizadas basadas en tus objetivos, 
                    nivel de fitness y disponibilidad de tiempo.
                </div>
            </div>
            
            <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                <div class="faq-question">
                    <i class="fas fa-users"></i>
                    ¿Cómo reservo clases grupales?
                </div>
                <div class="faq-answer">
                    Puedes reservar clases directamente desde nuestra app móvil o plataforma web. 
                    También puedes llamarnos o visitarnos en persona.
                </div>
            </div>
            
            <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                <div class="faq-question">
                    <i class="fas fa-shipping-fast"></i>
                    ¿Hacen envíos de suplementos?
                </div>
                <div class="faq-answer">
                    Sí, realizamos envíos a todo Lima y provincias. Envío gratis en compras mayores a S/150. 
                    Delivery express disponible.
                </div>
            </div>
            
            <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                <div class="faq-question">
                    <i class="fas fa-calendar-alt"></i>
                    ¿Puedo congelar mi membresía?
                </div>
                <div class="faq-answer">
                    Sí, ofrecemos opciones de congelamiento de membresía por motivos médicos, 
                    viajes o situaciones especiales. Consulta condiciones.
                </div>
            </div>
        </div>
        
        <div class="text-center" data-aos="fade-up" data-aos-delay="500">
            <a href="<?php echo AppHelper::baseUrl('faq'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Ver Todas las Preguntas
            </a>
        </div>
    </div>
</section>

<style>
/* Estilos específicos para la página de contacto */
.contact-hero {
    padding: 120px 0 80px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}

.contact-section {
    padding: 80px 0;
}

.contact-form-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 40px;
}

.contact-form .form-group {
    margin-bottom: 25px;
}

.contact-form label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.contact-form .form-control {
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-size: 16px;
}

.contact-form .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.25);
}

.contact-info {
    padding: 20px 0;
}

.contact-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    gap: 20px;
    transition: all 0.3s ease;
}

.contact-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.contact-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.contact-details h3 {
    color: var(--text-dark);
    margin-bottom: 15px;
    font-size: 1.2rem;
}

.contact-details p {
    color: var(--text-light);
    line-height: 1.6;
    margin-bottom: 10px;
}

.contact-details a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.contact-details a:hover {
    text-decoration: underline;
}

.social-links {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.social-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    font-weight: 500;
}

.social-link.facebook { color: #1877f2; }
.social-link.instagram { color: #e4405f; }
.social-link.youtube { color: #ff0000; }
.social-link.tiktok { color: #000000; }

.social-link:hover {
    background: rgba(255, 107, 0, 0.1);
    transform: translateX(5px);
}

.map-container {
    position: relative;
    overflow: hidden;
    border-radius: 0;
}

.map-overlay {
    position: absolute;
    top: 20px;
    left: 20px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    max-width: 300px;
}

.map-info h3 {
    color: var(--text-dark);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.map-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

.faq-item {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.faq-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.faq-question {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.1rem;
}

.faq-question i {
    color: var(--primary-color);
}

.faq-answer {
    color: var(--text-light);
    line-height: 1.6;
}

.form-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-top: 30px;
}

@media (max-width: 768px) {
    .contact-hero {
        padding: 100px 0 60px;
    }
    
    .contact-form-card {
        padding: 30px 20px;
    }
    
    .contact-card {
        flex-direction: column;
        text-align: center;
    }
    
    .contact-icon {
        align-self: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .map-overlay {
        position: static;
        margin: 20px;
        max-width: none;
    }
    
    .faq-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}
</style>

<script>
// JavaScript específico para la página de contacto
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validación personalizada
            if (validateContactForm()) {
                // Enviar formulario
                submitContactForm();
            }
        });
    }
    
    function validateContactForm() {
        let isValid = true;
        const requiredFields = ['first_name', 'last_name', 'email', 'subject', 'message'];
        
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            const value = input.value.trim();
            
            if (!value) {
                showFieldError(input, 'Este campo es obligatorio');
                isValid = false;
            } else {
                clearFieldError(input);
            }
        });
        
        // Validar email
        const email = document.getElementById('email');
        if (email.value && !isValidEmail(email.value)) {
            showFieldError(email, 'Ingresa un email válido');
            isValid = false;
        }
        
        // Validar mensaje mínimo
        const message = document.getElementById('message');
        if (message.value.trim().length < 10) {
            showFieldError(message, 'El mensaje debe tener al menos 10 caracteres');
            isValid = false;
        }
        
        // Validar políticas
        const privacy = document.getElementById('privacy_policy');
        if (!privacy.checked) {
            showFieldError(privacy, 'Debes aceptar la política de privacidad');
            isValid = false;
        }
        
        return isValid;
    }
    
    function submitContactForm() {
        const formData = new FormData(contactForm);
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        
        // Mostrar loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        submitBtn.disabled = true;
        
        fetch(contactForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('¡Mensaje enviado!', data.message, 'success');
                contactForm.reset();
            } else {
                showNotification('Error', data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error', 'Ocurrió un error al enviar el mensaje', 'error');
        })
        .finally(() => {
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Mensaje';
            submitBtn.disabled = false;
        });
    }
    
    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
        }
    }
    
    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = '';
        }
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function showNotification(title, message, type) {
        // Usar sistema de notificaciones de STYLOFITNESS
        if (typeof STYLOFITNESS !== 'undefined' && STYLOFITNESS.showNotification) {
            STYLOFITNESS.showNotification(title, message, type);
        } else {
            alert(title + ': ' + message);
        }
    }
});
</script>
