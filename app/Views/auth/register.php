<?php use StyleFitness\Helpers\AppHelper; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - STYLOFITNESS</title>
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
            grid-template-columns: 1fr 1.2fr;
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

        /* Panel derecho - Formulario */
        .auth-card {
            background: var(--dark-card);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .gradient-text {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .auth-tagline {
            color: var(--text-gray);
            font-size: 1rem;
        }

        .auth-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            color: var(--text-gray);
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        /* Formulario */
        .auth-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 1rem;
            transition: all 0.3s ease;
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
            color: var(--text-gray);
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        /* Opciones del formulario */
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            color: var(--text-gray);
            font-size: 0.9rem;
            margin-bottom: 1rem;
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

        input[type="checkbox"]:checked + .checkbox-custom {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        input[type="checkbox"]:checked + .checkbox-custom::after {
            content: '✓';
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        input[type="checkbox"] {
            display: none;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
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

        .btn-full {
            width: 100%;
        }

        .btn-lg {
            padding: 1.25rem 2rem;
            font-size: 1.1rem;
        }

        /* Separador */
        .auth-divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }

        .auth-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .auth-divider span {
            background: var(--dark-card);
            padding: 0 1rem;
            color: var(--text-gray);
            font-size: 0.9rem;
            position: relative;
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

        /* Link a login */
        .auth-switch {
            text-align: center;
            color: var(--text-gray);
        }

        .auth-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .auth-link:hover {
            color: var(--primary-light);
        }

        /* Alertas */
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
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

        .info-features {
            list-style: none;
            margin-bottom: 2rem;
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
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
        }

        .objective-option:hover .option-content {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .objective-option input[type="radio"]:checked + .option-content {
            border-color: var(--primary-color);
            background: rgba(255, 107, 0, 0.1);
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

        .form-error {
            color: var(--danger);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
        }

        .form-input.error {
            border-color: var(--danger);
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
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }

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

        /* Responsive */
        @media (max-width: 1024px) {
            .auth-wrapper {
                grid-template-columns: 1fr;
                max-width: 600px;
            }

            .auth-info-panel {
                display: none;
            }

            .auth-card {
                padding: 2rem;
            }
        }

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

        @media (max-width: 640px) {
            .auth-container {
                padding: 1rem;
            }

            .auth-card {
                padding: 1.5rem;
            }

            .gradient-text {
                font-size: 2rem;
            }

            .social-login {
                grid-template-columns: 1fr;
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