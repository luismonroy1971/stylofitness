<?php use StyleFitness\Helpers\AppHelper; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página No Encontrada | STYLOFITNESS</title>
    <link rel="icon" type="image/x-icon" href="<?php echo AppHelper::asset('images/favicon.ico'); ?>">
    
    <!-- Meta tags SEO -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="La página que buscas no existe en STYLOFITNESS">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #2C2C2C 0%, #4A4A4A 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #FF6B00 0%, #E55A00 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(255, 107, 0, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
        }
        
        .error-message {
            font-size: 1.2rem;
            color: #cccccc;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FF6B00 0%, #E55A00 100%);
            color: #fff;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 107, 0, 0.4);
        }
        
        .btn-secondary {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-3px);
        }
        
        .error-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #FF6B00 0%, #E55A00 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            animation: bounce 2s ease-in-out infinite;
        }
        
        .suggestions {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .suggestions h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: #FFB366;
        }
        
        .suggestions ul {
            list-style: none;
            text-align: left;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .suggestions li {
            margin: 10px 0;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border-left: 3px solid #FF6B00;
        }
        
        .suggestions a {
            color: #FFB366;
            text-decoration: none;
            font-weight: 500;
        }
        
        .suggestions a:hover {
            color: #FF6B00;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 5rem;
            }
            
            .error-title {
                font-size: 1.8rem;
            }
            
            .error-message {
                font-size: 1rem;
            }
            
            .error-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .error-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }
        }
        
        /* Efectos adicionales */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 107, 0, 0.6);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 1; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 0.5; }
        }
    </style>
</head>
<body>
    <!-- Partículas de fondo -->
    <div class="particles">
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 20%; animation-delay: 0.5s;"></div>
        <div class="particle" style="left: 30%; animation-delay: 1s;"></div>
        <div class="particle" style="left: 40%; animation-delay: 1.5s;"></div>
        <div class="particle" style="left: 50%; animation-delay: 2s;"></div>
        <div class="particle" style="left: 60%; animation-delay: 2.5s;"></div>
        <div class="particle" style="left: 70%; animation-delay: 3s;"></div>
        <div class="particle" style="left: 80%; animation-delay: 3.5s;"></div>
        <div class="particle" style="left: 90%; animation-delay: 4s;"></div>
    </div>

    <div class="error-container">
        <div class="error-icon">
            🏋️‍♂️
        </div>
        
        <div class="error-code">404</div>
        
        <h1 class="error-title">Página No Encontrada</h1>
        
        <p class="error-message">
            Lo sentimos, la página que buscas no existe o ha sido movida. 
            Pero no te preocupes, ¡tenemos muchas otras opciones increíbles para ti!
        </p>
        
        <div class="error-actions">
            <a href="<?php echo AppHelper::baseUrl(); ?>" class="btn btn-primary">
                <span>🏠</span>
                Ir al Inicio
            </a>
            
            <button onclick="history.back()" class="btn btn-secondary">
                <span>↩️</span>
                Volver Atrás
            </button>
        </div>
        
        <div class="suggestions">
            <h3>¿Qué puedes hacer?</h3>
            <ul>
                <li>
                    <a href="<?php echo AppHelper::baseUrl('routines'); ?>">
                        🏃‍♂️ Explorar rutinas personalizadas
                    </a>
                </li>
                <li>
                    <a href="<?php echo AppHelper::baseUrl('store'); ?>">
                        🛒 Visitar nuestra tienda de suplementos
                    </a>
                </li>
                <li>
                    <a href="<?php echo AppHelper::baseUrl('classes'); ?>">
                        👥 Ver clases grupales disponibles
                    </a>
                </li>
                <li>
                    <a href="<?php echo AppHelper::baseUrl('contact'); ?>">
                        📞 Contactar con soporte
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <script>
        // Efecto de partículas animadas
        document.addEventListener('DOMContentLoaded', function() {
            const particles = document.querySelectorAll('.particle');
            
            particles.forEach((particle, index) => {
                // Posición aleatoria
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 3 + 4) + 's';
                
                // Color aleatorio en la gama naranja
                const colors = [
                    'rgba(255, 107, 0, 0.6)',
                    'rgba(229, 90, 0, 0.6)',
                    'rgba(255, 179, 102, 0.6)'
                ];
                particle.style.background = colors[Math.floor(Math.random() * colors.length)];
            });
        });

        // Registro de error para analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'page_not_found', {
                'event_category': 'Error',
                'event_label': window.location.pathname,
                'value': 404
            });
        }

        // Console message
        console.log('%c🏋️‍♂️ STYLOFITNESS - Error 404', 'color: #FF6B00; font-size: 20px; font-weight: bold;');
        console.log('%cLa página que buscas no existe. ¿Necesitas ayuda? Contacta a soporte.', 'color: #666; font-size: 14px;');
    </script>
</body>
</html>