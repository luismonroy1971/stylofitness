<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error del Servidor | STYLOFITNESS</title>
    <link rel="icon" type="image/x-icon" href="<?php echo AppHelper::asset('images/favicon.ico'); ?>">
    
    <!-- Meta tags SEO -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Error interno del servidor en STYLOFITNESS">
    
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
            background: linear-gradient(135deg, #DC3545 0%, #C82333 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(220, 53, 69, 0.3);
            animation: shake 1s ease-in-out infinite;
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
            background: linear-gradient(135deg, #DC3545 0%, #C82333 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .error-details {
            margin-top: 40px;
            padding: 20px;
            background: rgba(220, 53, 69, 0.1);
            border-radius: 10px;
            border-left: 4px solid #DC3545;
            text-align: left;
        }
        
        .error-details h3 {
            color: #FF6B6B;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .error-details p {
            color: #cccccc;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(220, 53, 69, 0.2);
            border-radius: 20px;
            font-size: 0.9rem;
            color: #FF6B6B;
            margin-bottom: 20px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background: #DC3545;
            border-radius: 50%;
            animation: blink 1.5s ease-in-out infinite;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        .retry-timer {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #999;
        }
        
        .loading-bar {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .loading-progress {
            height: 100%;
            background: linear-gradient(90deg, #FF6B00, #E55A00);
            border-radius: 2px;
            animation: loading 3s ease-in-out infinite;
        }
        
        @keyframes loading {
            0% { width: 0%; }
            50% { width: 100%; }
            100% { width: 0%; }
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
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            ⚠️
        </div>
        
        <div class="status-indicator">
            <div class="status-dot"></div>
            Sistema Temporalmente Fuera de Servicio
        </div>
        
        <div class="error-code">500</div>
        
        <h1 class="error-title">Error del Servidor</h1>
        
        <p class="error-message">
            Oops! Algo salió mal en nuestros servidores. Nuestro equipo técnico ya ha sido notificado 
            y está trabajando para resolver este problema lo antes posible.
        </p>
        
        <div class="error-actions">
            <a href="<?php echo AppHelper::baseUrl(); ?>" class="btn btn-primary">
                <span>🏠</span>
                Ir al Inicio
            </a>
            
            <button onclick="location.reload()" class="btn btn-secondary" id="retry-btn">
                <span>🔄</span>
                Reintentar
            </button>
        </div>
        
        <div class="error-details">
            <h3>🔧 Información Técnica</h3>
            <p><strong>Error:</strong> Error interno del servidor</p>
            <p><strong>Código:</strong> HTTP 500</p>
            <p><strong>Tiempo:</strong> <span id="error-time"></span></p>
            <p><strong>ID de Referencia:</strong> <span id="error-id"></span></p>
            <p><strong>Estado:</strong> Nuestro equipo ha sido notificado automáticamente</p>
        </div>
        
        <div class="retry-timer">
            <p>Reintento automático en <span id="countdown">30</span> segundos</p>
            <div class="loading-bar">
                <div class="loading-progress"></div>
            </div>
        </div>
    </div>

    <script>
        // Generar ID de error único
        function generateErrorId() {
            return 'ERR-' + Date.now().toString(36).toUpperCase() + '-' + Math.random().toString(36).substr(2, 5).toUpperCase();
        }

        // Formatear fecha actual
        function getCurrentTime() {
            return new Date().toLocaleString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        // Inicializar información de error
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('error-time').textContent = getCurrentTime();
            document.getElementById('error-id').textContent = generateErrorId();

            // Contador regresivo
            let countdown = 30;
            const countdownElement = document.getElementById('countdown');
            const retryBtn = document.getElementById('retry-btn');

            const timer = setInterval(function() {
                countdown--;
                countdownElement.textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(timer);
                    location.reload();
                }
            }, 1000);

            // Detener contador si el usuario hace clic en reintentar
            retryBtn.addEventListener('click', function() {
                clearInterval(timer);
            });
        });

        // Registro de error para analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'server_error', {
                'event_category': 'Error',
                'event_label': 'HTTP 500',
                'value': 500
            });
        }

        // Enviar reporte de error al servidor (si está disponible)
        try {
            fetch('/api/error-report', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    error_type: 'server_error',
                    error_code: 500,
                    url: window.location.href,
                    user_agent: navigator.userAgent,
                    timestamp: new Date().toISOString(),
                    error_id: document.getElementById('error-id')?.textContent
                })
            }).catch(() => {
                // Silenciosamente fallar si no se puede enviar el reporte
            });
        } catch (e) {
            // Ignorar errores de reporte
        }

        // Console message
        console.error('%c⚠️ STYLOFITNESS - Error 500', 'color: #DC3545; font-size: 20px; font-weight: bold;');
        console.error('%cError interno del servidor. El equipo técnico ha sido notificado.', 'color: #666; font-size: 14px;');
    </script>
</body>
</html>