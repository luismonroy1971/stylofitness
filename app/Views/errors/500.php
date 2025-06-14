<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Interno del Servidor - STYLOFITNESS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #FF6B00 0%, #E55A00 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            opacity: 0.8;
            margin-bottom: 1rem;
        }
        
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .error-message {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: rgba(255, 255, 255, 0.9);
            color: #FF6B00;
        }
        
        .btn-primary:hover {
            background: white;
        }
        
        .error-icon {
            font-size: 4rem;
            margin-bottom: 2rem;
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 2rem;
            }
            
            .error-message {
                font-size: 1.1rem;
            }
            
            .error-actions {
                flex-direction: column;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <div class="error-code">500</div>
        
        <h1 class="error-title">Error Interno del Servidor</h1>
        
        <p class="error-message">
            Lo sentimos, algo salió mal en nuestros servidores. Nuestro equipo técnico ya ha sido notificado 
            y está trabajando para resolver este problema lo antes posible.
        </p>
        
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Volver al Inicio
            </a>
            <a href="javascript:history.back()" class="btn">
                <i class="fas fa-arrow-left"></i>
                Página Anterior
            </a>
            <a href="mailto:soporte@stylofitness.com" class="btn">
                <i class="fas fa-envelope"></i>
                Contactar Soporte
            </a>
        </div>
    </div>
    
    <script>
        // Recargar automáticamente después de 30 segundos
        setTimeout(function() {
            if (confirm('¿Deseas intentar recargar la página?')) {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html>
