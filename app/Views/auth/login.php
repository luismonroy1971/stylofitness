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
                    <p class="auth-tagline">Tu transformación comienza aquí</p>
                </div>
                
                <!-- Formulario de Login -->
                <div class="auth-form-container">
                    <h2 class="auth-title">Iniciar Sesión</h2>
                    <p class="auth-subtitle">Bienvenido de vuelta. Ingresa tus datos para continuar.</p>
                    
                    <!-- Mensajes de error -->
                    <?php if ($error = AppHelper::getFlashMessage('error')): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo AppHelper::baseUrl('login'); ?>" method="POST" class="auth-form" id="login-form">
                        <input type="hidden" name="csrf_token" value="<?php echo AppHelper::generateCsrfToken(); ?>">
                        
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
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   required 
                                   autocomplete="email">
                            <div class="form-error" id="email-error"></div>
                        </div>
                        
                        <!-- Contraseña -->
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
                                       autocomplete="current-password">
                                <button type="button" class="password-toggle" id="password-toggle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-error" id="password-error"></div>
                        </div>
                        
                        <!-- Recordar sesión y recuperar contraseña -->
                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" name="remember" id="remember">
                                <span class="checkbox-custom"></span>
                                Recordarme
                            </label>
                            
                            <a href="<?php echo AppHelper::baseUrl('forgot-password'); ?>" class="forgot-password-link">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                        
                        <!-- Botón de submit -->
                        <button type="submit" class="btn-primary btn-full btn-lg" id="login-btn">
                            <span class="btn-text">Iniciar Sesión</span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i>
                                Iniciando...
                            </span>
                        </button>
                    </form>
                    
                    <!-- Separador -->
                    <div class="auth-divider">
                        <span>O continúa con</span>
                    </div>
                    
                    <!-- Login Social -->
                    <div class="social-login">
                        <button class="btn-social btn-google" id="google-login">
                            <i class="fab fa-google"></i>
                            Google
                        </button>
                        <button class="btn-social btn-facebook" id="facebook-login">
                            <i class="fab fa-facebook-f"></i>
                            Facebook
                        </button>
                    </div>
                    
                    <!-- Link a registro -->
                    <div class="auth-switch">
                        <p>¿No tienes una cuenta? 
                            <a href="<?php echo AppHelper::baseUrl('register'); ?>" class="auth-link">
                                Regístrate gratis
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Panel de información -->
            <div class="auth-info-panel" data-aos="fade-left" data-aos-delay="200">
                <div class="info-content">
                    <h3>¡Bienvenido de Vuelta!</h3>
                    <p>Accede a tu cuenta y continúa tu transformación fitness:</p>
                    
                    <ul class="info-features">
                        <li>
                            <i class="fas fa-dumbbell"></i>
                            <span>Tus rutinas personalizadas</span>
                        </li>
                        <li>
                            <i class="fas fa-chart-line"></i>
                            <span>Seguimiento de progreso</span>
                        </li>
                        <li>
                            <i class="fas fa-shopping-cart"></i>
                            <span>Historial de compras</span>
                        </li>
                        <li>
                            <i class="fas fa-calendar"></i>
                            <span>Clases reservadas</span>
                        </li>
                        <li>
                            <i class="fas fa-trophy"></i>
                            <span>Logros y metas</span>
                        </li>
                    </ul>
                    
                    <div class="info-stats">
                        <div class="stat-item">
                            <div class="stat-number">10,000+</div>
                            <div class="stat-label">Miembros activos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Satisfacción</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts específicos para login -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle de contraseña
    const passwordToggle = document.getElementById('password-toggle');
    const passwordInput = document.getElementById('password');
    
    if (passwordToggle && passwordInput) {
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Validación del formulario
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            let hasErrors = false;
            
            // Validar email
            if (!email || !isValidEmail(email)) {
                showFieldError('email', 'Ingresa un email válido');
                hasErrors = true;
            }
            
            // Validar contraseña
            if (!password) {
                showFieldError('password', 'La contraseña es obligatoria');
                hasErrors = true;
            }
            
            if (hasErrors) {
                e.preventDefault();
                return;
            }
            
            // Mostrar loading
            const loginBtn = document.getElementById('login-btn');
            const btnText = loginBtn.querySelector('.btn-text');
            const btnLoading = loginBtn.querySelector('.btn-loading');
            
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-flex';
            loginBtn.disabled = true;
        });
    }
    
    // Login social (placeholder)
    document.getElementById('google-login')?.addEventListener('click', function() {
        // Implementar Google OAuth
        console.log('Google login clicked');
    });
    
    document.getElementById('facebook-login')?.addEventListener('click', function() {
        // Implementar Facebook OAuth
        console.log('Facebook login clicked');
    });
    
    // Funciones auxiliares
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        
        field.classList.add('error');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
    
    function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        
        field.classList.remove('error');
        errorDiv.style.display = 'none';
    }
    
    // Limpiar errores al escribir
    ['email', 'password'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', () => clearFieldError(fieldId));
        }
    });
});
</script>

<style>
/* Estilos específicos para la página de login */
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    position: relative;
    padding: 2rem 0;
}

.auth-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
}

.auth-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.auth-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(44, 44, 44, 0.9) 0%, rgba(74, 74, 74, 0.8) 100%);
    z-index: 1;
}

.auth-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.auth-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 3rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.auth-logo {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-logo h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.auth-tagline {
    color: var(--text-light);
    font-size: 1rem;
}

.auth-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
    text-align: center;
}

.auth-subtitle {
    color: var(--text-light);
    text-align: center;
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    font-size: 1rem;
    transition: var(--transition-fast);
    background: #fff;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
}

.form-input.error {
    border-color: var(--danger);
}

.password-input-wrapper {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-light);
    cursor: pointer;
    transition: var(--transition-fast);
}

.password-toggle:hover {
    color: var(--primary-color);
}

.form-error {
    color: var(--danger);
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: none;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.9rem;
    color: var(--text-dark);
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid #e1e5e9;
    border-radius: 4px;
    margin-right: 0.5rem;
    position: relative;
    transition: var(--transition-fast);
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.forgot-password-link {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 0.9rem;
    transition: var(--transition-fast);
}

.forgot-password-link:hover {
    text-decoration: underline;
}

.btn-full {
    width: 100%;
}

.auth-divider {
    text-align: center;
    margin: 2rem 0;
    position: relative;
    color: var(--text-light);
}

.auth-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e1e5e9;
    z-index: 1;
}

.auth-divider span {
    background: rgba(255, 255, 255, 0.95);
    padding: 0 1rem;
    position: relative;
    z-index: 2;
}

.social-login {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 2rem;
}

.btn-social {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.875rem 1rem;
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    background: #fff;
    color: var(--text-dark);
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition-fast);
    cursor: pointer;
}

.btn-social:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.btn-google:hover {
    border-color: #db4437;
    color: #db4437;
}

.btn-facebook:hover {
    border-color: #4267B2;
    color: #4267B2;
}

.auth-switch {
    text-align: center;
    color: var(--text-light);
}

.auth-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition-fast);
}

.auth-link:hover {
    text-decoration: underline;
}

.auth-info-panel {
    color: white;
    padding: 2rem;
}

.info-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--accent-color);
}

.info-content p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.info-features {
    list-style: none;
    margin-bottom: 3rem;
}

.info-features li {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.info-features i {
    color: var(--accent-color);
    font-size: 1.2rem;
    width: 24px;
}

.info-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--accent-color);
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-error {
    background: rgba(220, 53, 69, 0.1);
    color: var(--danger);
    border: 1px solid rgba(220, 53, 69, 0.2);
}

/* Responsive */
@media (max-width: 768px) {
    .auth-wrapper {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .auth-card {
        padding: 2rem;
    }
    
    .social-login {
        grid-template-columns: 1fr;
    }
    
    .info-stats {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>