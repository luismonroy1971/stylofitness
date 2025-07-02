<?php
/**
 * Archivo de prueba para verificar las secciones configuradas
 */

// Configuración básica
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Incluir configuraciones
require_once APP_PATH . '/Config/Database.php';
require_once APP_PATH . '/Config/App.php';
require_once APP_PATH . '/Helpers/AppHelper.php';

// Autoloader
spl_autoload_register(function ($class) {
    $className = basename(str_replace('\\', '/', $class));
    $paths = [
        APP_PATH . '/Controllers/',
        APP_PATH . '/Models/',
        APP_PATH . '/Helpers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

use StyleFitness\Controllers\LandingController;
use StyleFitness\Helpers\AppHelper;

// Crear instancia del controlador
$landingController = new LandingController();

// Obtener datos de configuración
$heroData = $landingController->getHeroData();
$servicesData = $landingController->getServicesData();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Secciones Configuradas - STYLOFITNESS</title>
    <link rel="stylesheet" href="<?php echo AppHelper::asset('css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo AppHelper::asset('css/homepage-enhanced.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; }
        .test-info {
            background: #000;
            color: #fff;
            padding: 1rem;
            text-align: center;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="test-info">
        <h1>🧪 PRUEBA DE SECCIONES CONFIGURADAS</h1>
        <p>Esta página muestra las secciones Hero y Features con configuración dinámica</p>
    </div>

    <!-- Sección Hero Configurada -->
    <?php 
    $heroConfig = $heroData['config'];
    $gymStats = $heroData['stats'];
    ?>
    <section class="hero-config-section" id="hero-configurado">
        <div class="container">
            <div class="hero-config-content">
                <div class="hero-config-badge">
                    <i class="fas fa-rocket"></i>
                    <span>CONFIGURACIÓN DINÁMICA</span>
                </div>
                <h1 class="hero-config-title">
                    <?php echo htmlspecialchars($heroConfig['title'] ?? 'Bienvenido a STYLOFITNESS'); ?>
                </h1>
                <h2 class="hero-config-subtitle">
                    <?php echo htmlspecialchars($heroConfig['subtitle'] ?? 'Tu transformación comienza aquí'); ?>
                </h2>
                <p class="hero-config-description">
                    <?php echo htmlspecialchars($heroConfig['description'] ?? 'Descubre una nueva forma de entrenar con tecnología de vanguardia y entrenadores expertos.'); ?>
                </p>
                <?php if (!empty($heroConfig['cta_link']) && !empty($heroConfig['cta_text'])): ?>
                <a href="<?php echo htmlspecialchars($heroConfig['cta_link']); ?>" class="hero-config-cta">
                    <i class="fas fa-play"></i>
                    <?php echo htmlspecialchars($heroConfig['cta_text']); ?>
                </a>
                <?php endif; ?>
                
                <?php if (!empty($gymStats)): ?>
                <div class="hero-config-stats">
                    <?php foreach ($gymStats as $stat): ?>
                    <div class="hero-config-stat">
                        <span class="hero-config-stat-number"><?php echo htmlspecialchars($stat['value']); ?></span>
                        <span class="hero-config-stat-label"><?php echo htmlspecialchars($stat['label']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Sección Features Configurada -->
    <?php 
    $featuresConfig = $servicesData['config'];
    $whyChooseUsItems = $servicesData['why_choose_us'];
    ?>
    <section class="section features-config-section" id="features-configurado">
        <div class="features-background">
            <div class="gradient-overlay-enhanced"></div>
        </div>
        
        <div class="container">
            <div class="features-hero-enhanced">
                <div class="features-config-badge">
                    <i class="fas fa-star"></i>
                    <span>CARACTERÍSTICAS ÚNICAS</span>
                </div>
                <h2 class="features-mega-title-new">
                    <span class="title-line-1-new"><?php echo htmlspecialchars($featuresConfig['title'] ?? '¿Por qué elegir'); ?></span>
                    <span class="title-line-2-new features-config-title">STYLOFITNESS?</span>
                </h2>
                <p class="features-mega-subtitle-new">
                    <?php echo htmlspecialchars($featuresConfig['subtitle'] ?? 'Descubre lo que nos hace únicos'); ?>
                </p>
            </div>
            
            <?php if (!empty($whyChooseUsItems)): ?>
            <div class="features-grid-enhanced">
                <?php foreach ($whyChooseUsItems as $item): ?>
                <div class="feature-card-enhanced">
                    <div class="feature-icon-enhanced">
                        <i class="<?php echo htmlspecialchars($item['icon'] ?? 'fas fa-star'); ?>"></i>
                    </div>
                    <h3 class="feature-title-enhanced">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </h3>
                    <p class="feature-description-enhanced">
                        <?php echo htmlspecialchars($item['description']); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="test-info">
        <h2>✅ VERIFICACIÓN COMPLETADA</h2>
        <p>Las secciones arriba muestran:</p>
        <ul style="text-align: left; max-width: 600px; margin: 0 auto;">
            <li><strong>Sección Hero:</strong> Fondo azul degradado con badge "CONFIGURACIÓN DINÁMICA"</li>
            <li><strong>Sección Features:</strong> Fondo verde/morado degradado con badge "CARACTERÍSTICAS ÚNICAS"</li>
            <li><strong>Datos dinámicos:</strong> Contenido cargado desde la base de datos</li>
            <li><strong>Estilos distintivos:</strong> Cada sección tiene colores y efectos únicos</li>
        </ul>
    </div>
</body>
</html>