<div class="auth-container">
    <div class="auth-background">
        <div class="auth-overlay"></div>
        <video autoplay muted loop class="auth-video">
            <source src="<?php echo AppHelper::asset('videos/auth-bg.mp4'); ?>" type="video/mp4">
        </video>
    </div>
    
    <div class="container">
        <div class="auth-wrapper">
            <div class="auth-card" data-aos="fade-up">
                <!-- Logo -->
                <div class="auth-logo">
                    <h1 class="gradient-text">STYLOFITNESS</h1>
                    <p class="auth-tagline">Comienza tu transformación hoy</p>
                </div>
                
                <!-- Formulario de Registro -->
                <div class="auth-form-container">
                    <h2 class="auth-title">Crear Cuenta</h2>
                    <p class="auth-subtitle">Únete a miles de personas que ya transformaron sus vidas</p>
                    
                    <!-- Mensajes de error -->
                    <?php if ($errors = $_SESSION['registration_errors'] ?? null): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <ul style="margin: 0; padding-left: 1rem;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['registration_errors']); ?>
                    <?php endif; ?>
                    
                    <form action="<?php echo AppHelper::baseUrl('register'); ?>" method="POST" class="auth-form" id="register-form">
                        <input type="hidden" name="csrf_token" value="<?php echo AppHelper::generateCsrfToken(); ?>">
                        
                        <!-- Nombres -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Nombre
                                </label>
                                <input type="text" 
                                       id="first_name" 
                                       name="first_name" 
                                       class="form-input" 
                                       placeholder="Tu nombre"
                                       value="<?php echo htmlspecialchars(($_SESSION['registration_data']['first_name'] ?? '')); ?>"
                                       required 
                                       autocomplete="given-name">
                                <div class="form-error" id="first_name-error"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Apellido
                                </label>
                                <input type="text" 
                                       id="last_name" 
                                       name="last_name" 
                                       class="form-input" 
                                       placeholder="Tu apellido"
                                       value="<?php echo htmlspecialchars(($_SESSION['registration_data']['last_name'] ?? '')); ?>"
                                       required 
                                       autocomplete="family-name">
                                <div class="form-error" id="last_name-error"></div>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i>
                                Email
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-input" 
                                   placeholder="tu@email.com"
                                   value="<?php echo htmlspecialchars(($_SESSION['registration_data']['email'] ?? '')); ?>"
                                   required 
                                   autocomplete="email">
                            <div class="form-error" id="email-error"></div>
                        </div>
                        
                        <!-- Teléfono -->
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone"></i>
                                Teléfono <span class="optional">(opcional)</span>
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-input" 
                                   placeholder="+51 999 888 777"
                                   value="<?php echo htmlspecialchars(($_SESSION['registration_data']['phone'] ?? '')); ?>"
                                   autocomplete="tel">
                            <div class="form-error" id="phone-error"></div>
                        </div>
                        
                        <!-- Contraseñas -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Contraseña
                                </label>
                                <div class="password-input-wrapper">
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="form-input" 
                                           placeholder="••••••••"
                                           required 
                                           autocomplete="new-password">
                                    <button type="button" class="password-toggle" id="password-toggle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength" id="password-strength"></div>
                                <div class="form-error" id="password-error"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirm" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Confirmar
                                </label>
                                <div class="password-input-wrapper">
                                    <input type="password" 
                                           id="password_confirm" 
                                           name="password_confirm" 
                                           class="form-input" 
                                           placeholder="••••••••"
                                           required 
                                           autocomplete="new-password">
                                    <button type="button" class="password-toggle" id="password-confirm-toggle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-error" id="password_confirm-error"></div>
                            </div>
                        </div>
                        
                        <!-- Objetivos -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-bullseye"></i>
                                ¿Cuál es tu objetivo principal?
                            </label>
                            <div class="objective-options">
                                <label class="objective-option">
                                    <input type="radio" name="objective" value="weight_loss" checked>
                                    <span class="option-content">
                                        <i class="fas fa-weight"></i>
                                        <span>Perder Peso</span>
                                    </span>
                                </label>
                                <label class="objective-option">
                                    <input type="radio" name="objective" value="muscle_gain">
                                    <span class="option-content">
                                        <i class="fas fa-dumbbell"></i>
                                        <span>Ganar Músculo</span>
                                    </span>
                                </label>
                                <label class="objective-option">
                                    <input type="radio" name="objective" value="strength">
                                    <span class="option-content">
                                        <i class="fas fa-fist-raised"></i>
                                        <span>Fuerza</span>
                                    </span>
                                </label>
                                <label class="objective-option">
                                    <input type="radio" name="objective" value="endurance">
                                    <span class="option-content">
                                        <i class="fas fa-running"></i>
                                        <span>Resistencia</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Términos y condiciones -->
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="terms" id="terms" required>
                                <span class="checkbox-custom"></span>
                                Acepto los 
                                <a href="<?php echo AppHelper::baseUrl('terms'); ?>" target="_blank" class="terms-link">
                                    Términos y Condiciones
                                </a> 
                                y la 
                                <a href="<?php echo AppHelper::baseUrl('privacy'); ?>" target="_blank" class="terms-link">
                                    Política de Privacidad
                                </a>
                            </label>
                            <div class="form-error" id="terms-error"></div>
                        </div>
                        
                        <!-- Newsletter -->
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="newsletter" id="newsletter" checked>
                                <span class="checkbox-custom"></span>
                                Quiero recibir consejos de fitness, ofertas especiales y noticias
                            </label>
                        </div>
                        
                        <!-- Botón de submit -->
                        <button type="submit" class="btn-primary btn-full btn-lg" id="register-btn">
                            <span class="btn-text">
                                <i class="fas fa-user-plus"></i>
                                Crear Mi Cuenta
                            </span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i>
                                Creando cuenta...
                            </span>
                        </button>
                    </form>
                    
                    <!-- Separador -->
                    <div class="auth-divider">
                        <span>O regístrate con</span>
                    </div>
                    
                    <!-- Registro Social -->
                    <div class="social-login">
                        <button class="btn-social btn-google" id="google-register">
                            <i class="fab fa-google"></i>
                            Google
                        </button>
                        <button class="btn-social btn-facebook" id="facebook-register">
                            <i class="fab fa-facebook-f"></i>
                            Facebook
                        </button>
                    </div>
                    
                    <!-- Link a login -->
                    <div class="auth-switch">
                        <p>¿Ya tienes cuenta? 
                            <a href="<?php echo AppHelper::baseUrl('login'); ?>" class="auth-link">
                                Inicia sesión
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Panel de información -->
            <div class="auth-info-panel" data-aos="fade-left" data-aos-delay="200">
                <div class="info-content">
                    <h3>¡Únete a la Revolución Fitness!</h3>
                    <p>Crea tu cuenta gratuita y accede a todos nuestros beneficios:</p>
                    
                    <ul class="info-features">
                        <li>
                            <i class="fas fa-dumbbell"></i>
                            <span>Rutinas personalizadas gratis</span>
                        </li>
                        <li>
                            <i class="fas fa-video"></i>
                            <span>Videos HD de ejercicios</span>
                        </li>
                        <li>
                            <i class="fas fa-chart-line"></i>
                            <span>Seguimiento de progreso</span>
                        </li>
                        <li>
                            <i class="fas fa-percentage"></i>
                            <span>Descuentos exclusivos</span>
                        </li>
                        <li>
                            <i class="fas fa-users"></i>
                            <span>Comunidad fitness</span>
                        </li>
                        <li>
                            <i class="fas fa-headset"></i>
                            <span>Soporte personalizado</span>
                        </li>
                    </ul>
                    
                    <div class="info-guarantee">
                        <div class="guarantee-badge">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <strong>Garantía 30 días</strong>
                                <p>Si no estás satisfecho, te devolvemos tu dinero</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-testimonial">
                        <blockquote>
                            "STYLOFITNESS cambió mi vida. En 3 meses perdí 15kg y gané confianza."
                        </blockquote>
                        <cite>- María García, miembro desde 2023</cite>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Limpiar datos de sesión
unset($_SESSION['registration_data']); 
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle de contraseñas
    ['password-toggle', 'password-confirm-toggle'].forEach(toggleId => {
        const toggle = document.getElementById(toggleId);
        const inputId = toggleId.replace('-toggle', '').replace('-', '_');
        const input = document.getElementById(inputId);
        
        if (toggle && input) {
            toggle.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }
    });
    
    // Validación de fortaleza de contraseña
    const passwordInput = document.getElementById('password');
    const strengthDiv = document.getElementById('password-strength');
    
    if (passwordInput && strengthDiv) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            
            strengthDiv.className = 'password-strength ' + strength.class;
            strengthDiv.textContent = strength.text;
        });
    }
    
    // Validación del formulario
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let hasErrors = false;
            
            // Validar campos requeridos
            const requiredFields = ['first_name', 'last_name', 'email', 'password', 'password_confirm'];
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    showFieldError(fieldId, 'Este campo es obligatorio');
                    hasErrors = true;
                }
            });
            
            // Validar email
            const email = document.getElementById('email').value;
            if (email && !isValidEmail(email)) {
                showFieldError('email', 'Ingresa un email válido');
                hasErrors = true;
            }
            
            // Validar contraseñas
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (password && password.length < 6) {
                showFieldError('password', 'La contraseña debe tener al menos 6 caracteres');
                hasErrors = true;
            }
            
            if (password !== passwordConfirm) {
                showFieldError('password_confirm', 'Las contraseñas no coinciden');
                hasErrors = true;
            }
            
            // Validar términos
            const terms = document.getElementById('terms');
            if (!terms.checked) {
                showFieldError('terms', 'Debes aceptar los términos y condiciones');
                hasErrors = true;
            }
            
            // Validar teléfono si se proporciona
            const phone = document.getElementById('phone').value;
            if (phone && !isValidPhone(phone)) {
                showFieldError('phone', 'Ingresa un teléfono válido');
                hasErrors = true;
            }
            
            if (hasErrors) {
                e.preventDefault();
                return;
            }
            
            // Mostrar loading
            const registerBtn = document.getElementById('register-btn');
            const btnText = registerBtn.querySelector('.btn-text');
            const btnLoading = registerBtn.querySelector('.btn-loading');
            
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-flex';
            registerBtn.disabled = true;
        });
    }
    
    // Limpiar errores al escribir
    ['first_name', 'last_name', 'email', 'phone', 'password', 'password_confirm'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', () => clearFieldError(fieldId));
        }
    });
    
    // Funciones auxiliares
    function calculatePasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 6) score++;
        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        
        if (score < 2) return { class: 'weak', text: 'Débil' };
        if (score < 4) return { class: 'medium', text: 'Media' };
        if (score < 6) return { class: 'strong', text: 'Fuerte' };
        return { class: 'very-strong', text: 'Muy Fuerte' };
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function isValidPhone(phone) {
        const phoneRegex = /^[+]?[\d\s\-\(\)]+$/;
        return phoneRegex.test(phone);
    }
    
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        
        if (field) field.classList.add('error');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }
    
    function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        
        if (field) field.classList.remove('error');
        if (errorDiv) errorDiv.style.display = 'none';
    }
});
</script>

<style>
/* Estilos adicionales para registro */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.optional {
    font-weight: 400;
    color: var(--text-light);
    font-size: 0.875rem;
}

.password-strength {
    margin-top: 0.5rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 600;
    text-align: center;
    transition: var(--transition-fast);
}

.password-strength.weak {
    background: rgba(220, 53, 69, 0.1);
    color: var(--danger);
}

.password-strength.medium {
    background: rgba(255, 193, 7, 0.1);
    color: var(--warning);
}

.password-strength.strong {
    background: rgba(40, 167, 69, 0.1);
    color: var(--success);
}

.password-strength.very-strong {
    background: rgba(40, 167, 69, 0.2);
    color: var(--success);
}

.objective-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.objective-option {
    cursor: pointer;
}

.objective-option input[type="radio"] {
    display: none;
}

.option-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    transition: var(--transition-fast);
    background: #fff;
}

.objective-option:hover .option-content {
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.objective-option input[type="radio"]:checked + .option-content {
    border-color: var(--primary-color);
    background: rgba(255, 107, 0, 0.05);
    color: var(--primary-color);
}

.option-content i {
    font-size: 1.5rem;
}

.option-content span {
    font-size: 0.9rem;
    font-weight: 600;
    text-align: center;
}

.terms-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.terms-link:hover {
    text-decoration: underline;
}

.guarantee-badge {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: rgba(40, 167, 69, 0.1);
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 2rem;
}

.guarantee-badge i {
    font-size: 2rem;
    color: var(--success);
}

.guarantee-badge strong {
    color: var(--success);
    font-size: 1.1rem;
}

.guarantee-badge p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.8;
}

.info-testimonial {
    background: rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
    border-radius: 10px;
    border-left: 4px solid var(--accent-color);
}

.info-testimonial blockquote {
    font-style: italic;
    font-size: 1.1rem;
    margin-bottom: 1rem;
    line-height: 1.6;
}

.info-testimonial cite {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Responsive para registro */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .objective-options {
        grid-template-columns: 1fr;
    }
    
    .option-content {
        flex-direction: row;
        justify-content: flex-start;
        text-align: left;
    }
    
    .option-content i {
        font-size: 1.25rem;
    }
}
</style>