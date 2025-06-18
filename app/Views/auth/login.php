<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - STYLOFITNESS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #FF6B00;
            --primary-dark: #E55A00;
            --primary-light: #FFB366;
            --accent-color: #FFD700;
            --dark-bg: #0F0F0F;
            --dark-card: #1A1A1A;
            --text-light: #F8F9FA;
            --text-gray: #8E8E93;
            --success: #00D4AA;
            --danger: #FF3B30;
            --warning: #FF9500;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0F0F0F 0%, #1A1A1A 25%, #2D2D2D 50%, #1A1A1A 75%, #0F0F0F 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            position: relative;
            overflow-x: hidden;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Partículas flotantes de fondo */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 50%;
            opacity: 0.6;
            animation: float 20s infinite ease-in-out;
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 6s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 8s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 10s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 12s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 14s; }
        .particle:nth-child(9) { left: 90%; animation-delay: 16s; }

        @keyframes float {
            0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10%, 90% { opacity: 0.6; }
            50% { transform: translateY(-10vh) rotate(180deg); opacity: 1; }
        }

        /* Efectos de luces */
        .light-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(40px);
            pointer-events: none;
            z-index: 0;
        }

        .light-orb-1 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 107, 0, 0.3) 0%, transparent 70%);
            top: 20%;
            left: 20%;
            animation: pulse 8s ease-in-out infinite;
        }

        .light-orb-2 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 211, 0, 0.2) 0%, transparent 70%);
            top: 60%;
            right: 20%;
            animation: pulse 8s ease-in-out infinite reverse;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.3; }
            50% { transform: scale(1.2) rotate(180deg); opacity: 0.6; }
        }

        /* Container principal */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 2;
        }

        .auth-wrapper {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            max-width: 1400px;
            width: 100%;
            gap: 0;
            border-radius: 24px;
            overflow: hidden;
            backdrop-filter: blur(20px);
            background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(255, 255, 255, 0.05) 100%);
            border: 1px solid var(--glass-border);
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.4),
                0 16px 32px rgba(255, 107, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        /* Panel izquierdo - Información */
        .auth-info-panel {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .auth-info-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="gym-pattern" patternUnits="userSpaceOnUse" width="20" height="20"><circle cx="2" cy="2" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23gym-pattern)"/></svg>');
            opacity: 0.3;
        }

        .info-content {
            position: relative;
            z-index: 1;
        }

        .info-logo {
            margin-bottom: 2rem;
        }

        .info-logo h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            color: white;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            margin-bottom: 0.5rem;
        }

        .info-tagline {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 3rem;
        }

        .info-features {
            list-style: none;
            margin-bottom: 3rem;
        }

        .info-features li {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            color: white;
        }

        .info-features i {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: var(--accent-color);
        }

        .info-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            backdrop-filter: blur(10px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        /* Panel derecho - Formulario */
        .auth-form-panel {
            background: var(--dark-card);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .form-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--text-gray);
            font-size: 1rem;
        }

        /* Formulario */
        .auth-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        .form-label {
            display: block;
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 4px rgba(255, 107, 0, 0.1);
        }

        .form-input::placeholder {
            color: var(--text-gray);
        }

        .form-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
            transition: color 0.3s ease;
        }

        .form-input:focus + .form-icon {
            color: var(--primary-color);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-gray);
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        /* Opciones del formulario */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .checkbox-custom {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .checkbox-input:checked + .checkbox-custom {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .checkbox-input:checked + .checkbox-custom::after {
            content: '✓';
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .checkbox-input {
            display: none;
        }

        .checkbox-label {
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .forgot-link {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: var(--primary-light);
        }

        /* Botón principal */
        .btn-primary {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Separador */
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .divider span {
            background: var(--dark-card);
            padding: 0 1rem;
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        /* Botones sociales */
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
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-social:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .btn-google:hover {
            border-color: #db4437;
        }

        .btn-facebook:hover {
            border-color: #4267B2;
        }

        /* Footer del formulario */
        .form-footer {
            text-align: center;
            color: var(--text-gray);
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--primary-light);
        }

        /* Alertas */
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            backdrop-filter: blur(10px);
        }

        .alert-error {
            background: rgba(255, 59, 48, 0.1);
            border: 1px solid rgba(255, 59, 48, 0.3);
            color: #FF6B6B;
        }

        .alert-success {
            background: rgba(0, 212, 170, 0.1);
            border: 1px solid rgba(0, 212, 170, 0.3);
            color: var(--success);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .auth-wrapper {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .auth-info-panel {
                display: none;
            }

            .auth-form-panel {
                padding: 2rem;
            }
        }

        @media (max-width: 640px) {
            .auth-container {
                padding: 1rem;
            }

            .auth-form-panel {
                padding: 1.5rem;
            }

            .form-title {
                font-size: 2rem;
            }

            .social-login {
                grid-template-columns: 1fr;
            }
        }

        /* Animaciones de entrada */
        .auth-wrapper {
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Partículas de fondo -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Efectos de luz -->
    <div class="light-orb light-orb-1"></div>
    <div class="light-orb light-orb-2"></div>

    <div class="auth-container">
        <div class="auth-wrapper">
            <!-- Panel de información -->
            <div class="auth-info-panel">
                <div class="info-content">
                    <div class="info-logo">
                        <h1>STYLOFITNESS</h1>
                        <p class="info-tagline">Tu transformación comienza aquí</p>
                    </div>

                    <ul class="info-features">
                        <li>
                            <i class="fas fa-dumbbell"></i>
                            <span>Rutinas personalizadas diseñadas por expertos</span>
                        </li>
                        <li>
                            <i class="fas fa-chart-line"></i>
                            <span>Seguimiento detallado de tu progreso</span>
                        </li>
                        <li>
                            <i class="fas fa-users"></i>
                            <span>Clases grupales y entrenamientos virtuales</span>
                        </li>
                        <li>
                            <i class="fas fa-shopping-cart"></i>
                            <span>Tienda especializada en suplementos</span>
                        </li>
                        <li>
                            <i class="fas fa-trophy"></i>
                            <span>Sistema de logros y recompensas</span>
                        </li>
                    </ul>

                    <div class="info-stats">
                        <div class="stat-item">
                            <div class="stat-number">15K+</div>
                            <div class="stat-label">Miembros activos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Satisfacción</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel del formulario -->
            <div class="auth-form-panel">
                <div class="form-header">
                    <h2 class="form-title">Bienvenido</h2>
                    <p class="form-subtitle">Ingresa tus credenciales para acceder a tu cuenta</p>
                </div>

                <!-- Mensaje de error ejemplo -->
                <div class="alert alert-error" style="display: none;" id="error-alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Credenciales incorrectas. Inténtalo de nuevo.</span>
                </div>

                <form class="auth-form" id="login-form">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="form-input-wrapper">
                            <input type="email" id="email" name="email" class="form-input" placeholder="tu@email.com" required>
                            <i class="fas fa-envelope form-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="form-input-wrapper">
                            <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                            <i class="fas fa-lock form-icon"></i>
                            <button type="button" class="password-toggle" id="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember" class="checkbox-input">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-label">Recordarme</span>
                        </label>
                        <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-primary">
                        Iniciar Sesión
                    </button>
                </form>

                <div class="divider">
                    <span>O continúa con</span>
                </div>

                <div class="social-login">
                    <button class="btn-social btn-google">
                        <i class="fab fa-google"></i>
                        Google
                    </button>
                    <button class="btn-social btn-facebook">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </button>
                </div>

                <div class="form-footer">
                    <p>¿No tienes una cuenta? <a href="/register">Regístrate gratis</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle de contraseña
        document.getElementById('password-toggle').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Simulación de envío de formulario
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Simulación de validación
            if (email === 'demo@stylofitness.com' && password === 'demo123') {
                alert('¡Login exitoso! Redirigiendo al dashboard...');
            } else {
                document.getElementById('error-alert').style.display = 'flex';
                setTimeout(() => {
                    document.getElementById('error-alert').style.display = 'none';
                }, 3000);
            }
        });

        // Efectos de focus en inputs
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>