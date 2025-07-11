<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Header principal del sitio - STYLOFITNESS
 * Incluye navegación, meta tags y estilos
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- Meta tags anti-caché -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="<?php echo gmdate('D, d M Y H:i:s'); ?> GMT">
    <meta name="cache-control" content="no-cache">
    <meta name="expires" content="0">
    <meta name="pragma" content="no-cache">
    
    <!-- Meta tags SEO -->
    <title><?php echo $pageTitle ?? 'STYLOFITNESS - Gimnasio Profesional'; ?></title>
    <meta name="description" content="<?php echo $pageDescription ?? 'Gimnasio profesional con rutinas personalizadas y tienda de suplementos deportivos en Lima, Perú'; ?>">
    <meta name="keywords" content="<?php echo $pageKeywords ?? 'gimnasio, fitness, rutinas, suplementos, Lima, Perú, entrenamiento, proteínas'; ?>">
    <meta name="author" content="STYLOFITNESS">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo AppHelper::baseUrl($_SERVER['REQUEST_URI']); ?>">
    <meta property="og:title" content="<?php echo $pageTitle ?? 'STYLOFITNESS'; ?>">
    <meta property="og:description" content="<?php echo $pageDescription ?? 'Gimnasio profesional con rutinas personalizadas'; ?>">
    <meta property="og:image" content="<?php echo AppHelper::asset('images/og-image.jpg'); ?>">
    <meta property="og:site_name" content="STYLOFITNESS">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo AppHelper::baseUrl($_SERVER['REQUEST_URI']); ?>">
    <meta property="twitter:title" content="<?php echo $pageTitle ?? 'STYLOFITNESS'; ?>">
    <meta property="twitter:description" content="<?php echo $pageDescription ?? 'Gimnasio profesional con rutinas personalizadas'; ?>">
    <meta property="twitter:image" content="<?php echo AppHelper::asset('images/og-image.jpg'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo AppHelper::asset('images/favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php echo AppHelper::asset('images/apple-touch-icon.png'); ?>">
    
    <!-- Preconnect a fuentes y CDNs -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css para animaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Estilos principales -->
    <link rel="stylesheet" href="<?php echo AppHelper::asset('css/styles.css'); ?>">
    
    <!-- Correcciones CSS críticas -->
    <link rel="stylesheet" href="<?php echo AppHelper::asset('css/fixes.css'); ?>">
    
    <!-- Correcciones de dropdown -->
    <link rel="stylesheet" href="<?php echo AppHelper::asset('css/dropdown-fix.css'); ?>">
    
    <!-- Correcciones de desbordamiento del header -->
    <link rel="stylesheet" href="<?php echo AppHelper::asset('css/header-overflow-fix.css'); ?>">
    
    <!-- Estilos Homepage Mejorados - v2.0 -->
    <link rel="stylesheet" href="<?php echo AppHelper::asset('css/homepage-enhanced.css'); ?>?v=<?php echo time(); ?>">
    
    <!-- Correcciones del Carrusel -->
    <link rel="stylesheet" href="<?php echo AppHelper::asset('css/carousel-fixes.css'); ?>?v=<?php echo time(); ?>">
    
    <!-- Estilos del Carrusel de Landing -->
    <link rel="stylesheet" href="<?php echo AppHelper::asset('css/landing-carousel.css'); ?>?v=<?php echo time(); ?>">
    
    <!-- Correcciones de Contraste y Visibilidad -->
    <link rel="stylesheet" href="<?php echo AppHelper::asset('css/contrast-fixes.css'); ?>?v=<?php echo time(); ?>">
    
    <!-- Estilos adicionales según la página -->
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo AppHelper::asset("css/{$css}"); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Variables CSS dinámicas -->
    <style>
        :root {
            --primary-color: <?php echo defined('THEME_COLORS') ? THEME_COLORS['primary'] : '#FF6B00'; ?>;
            --secondary-color: <?php echo defined('THEME_COLORS') ? THEME_COLORS['secondary'] : '#E55A00'; ?>;
            --accent-color: <?php echo defined('THEME_COLORS') ? THEME_COLORS['accent'] : '#FFB366'; ?>;
            --dark-bg: <?php echo defined('THEME_COLORS') ? THEME_COLORS['dark'] : '#2C2C2C'; ?>;
            --light-bg: <?php echo defined('THEME_COLORS') ? THEME_COLORS['light'] : '#F8F9FA'; ?>;
        }
        
        /* Estilos críticos para el carrusel */
        .hero-carousel-section {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
        }
        
        .hero-carousel {
            position: relative !important;
            overflow: hidden !important;
            width: 100% !important;
            height: auto !important;
            min-height: 300px !important;
        }
        
        .carousel-track {
            display: flex !important;
            width: 500% !important;
            height: auto !important;
            transition: transform 0.6s ease !important;
        }
        
        .mega-slide {
            min-width: 20% !important;
            flex: 0 0 20% !important;
            height: auto !important;
            min-height: 280px !important;
            position: relative !important;
            display: flex !important;
            align-items: center !important;
        }
        
        .mega-slide.active {
            opacity: 1 !important;
            transform: scale(1) !important;
        }
        
        .carousel-controls {
            position: absolute !important;
            bottom: 20px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            display: flex !important;
            gap: 10px !important;
            z-index: 10 !important;
        }
        
        .mega-dot {
            width: 12px !important;
            height: 12px !important;
            border-radius: 50% !important;
            background: rgba(255, 255, 255, 0.5) !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }
        
        .mega-dot.active {
            background: #fff !important;
            transform: scale(1.2) !important;
        }
        
        .carousel-nav {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            background: rgba(0, 0, 0, 0.5) !important;
            color: white !important;
            border: none !important;
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 10 !important;
            transition: all 0.3s ease !important;
        }
        
        .carousel-nav:hover {
            background: rgba(0, 0, 0, 0.8) !important;
            transform: translateY(-50%) scale(1.1) !important;
        }
        
        .mega-prev {
            left: 20px !important;
        }
        
        .mega-next {
            right: 20px !important;
        }
        
        /* ESTILOS INMEDIATOS - FORZAR CAMBIOS */
        .compact-title-section {
            text-align: center !important;
            padding: 1rem 0 !important;
            margin-bottom: 0.5rem !important;
            background: rgba(0, 0, 0, 0.1) !important;
            border-radius: 15px !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 107, 0, 0.2) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
            position: relative !important;
            overflow: hidden !important;
        }
        
        .offers-title-compact {
            font-family: 'Montserrat', Arial, sans-serif !important;
            font-size: 2.2rem !important;
            font-weight: 900 !important;
            margin: 0 0 0.5rem 0 !important;
            color: #FFFFFF !important;
            text-shadow: 0 0 30px rgba(255, 107, 0, 0.5) !important;
            letter-spacing: 1px !important;
            line-height: 1.1 !important;
            text-transform: uppercase !important;
            position: relative !important;
            z-index: 2 !important;
        }
        
        .offers-title-compact .fire-icon {
            color: #FF6B00 !important;
            animation: fireFlicker 2s infinite alternate !important;
            margin: 0 0.75rem !important;
            font-size: 2rem !important;
            filter: drop-shadow(0 0 10px rgba(255, 107, 0, 0.6)) !important;
        }
        
        .gradient-text-enhanced {
            background: linear-gradient(135deg, #FF6B00 0%, #FFB366 50%, #FF6B00 100%) !important;
            background-size: 200% 200% !important;
            background-clip: text !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            animation: gradientShift 3s ease-in-out infinite !important;
            display: inline-block !important;
        }
        
        @keyframes fireFlicker {
            0%, 100% { 
                transform: scale(1) rotate(-1deg);
                filter: drop-shadow(0 0 10px rgba(255, 107, 0, 0.6));
            }
            50% { 
                transform: scale(1.1) rotate(1deg);
                filter: drop-shadow(0 0 15px rgba(255, 107, 0, 0.8));
            }
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        /* DROPDOWN USUARIO - FORZAR VISIBILIDAD */
        .header {
            overflow: visible !important;
        }
        
        .header .container {
            overflow: visible !important;
        }
        
        .navbar {
            overflow: visible !important;
        }
        
        .navbar-actions {
            overflow: visible !important;
        }
        
        .user-menu {
            overflow: visible !important;
        }
        
        .user-dropdown {
            position: relative !important;
            z-index: 99999 !important;
            overflow: visible !important;
        }
        
        .user-dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
            z-index: 99999 !important;
            background: white !important;
            border-radius: 15px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
            min-width: 250px !important;
            padding: 1rem 0 !important;
            margin-top: 5px !important;
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-10px) !important;
            transition: all 0.3s ease !important;
        }
        
        .user-dropdown-menu.show,
        .user-dropdown-menu.active {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        
        .dropdown-menu.show {
            display: block !important;
            z-index: 99999 !important;
        }
        
        /* MEJORAS NAVBAR COMPACTO */
        .header {
            padding: 0.25rem 0 !important;
        }
        
        .navbar {
            height: 45px !important;
        }
        
        .header.scrolled {
            padding: 0.15rem 0 !important;
        }
        
        .nav-link {
            padding: 0.5rem 1rem !important;
            font-size: 0.9rem !important;
        }
        
        .logo {
            font-size: 1.6rem !important;
        }
        
        .main-content {
            padding-top: 50px !important;
        }
        
        .modern-features-section {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #2d2d2d 100%) !important;
        }
        
        .featured-products-section-enhanced {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%) !important;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 0.15rem 0 !important;
            }
            
            .navbar {
                height: 40px !important;
            }
            
            .logo {
                font-size: 1.4rem !important;
            }
            
            .nav-link {
                padding: 0.4rem 0.8rem !important;
                font-size: 0.85rem !important;
            }
            
            .main-content {
                padding-top: 45px !important;
            }
            
            .offers-title-compact {
                font-size: 1.5rem !important;
            }
        }
        
        @media (max-width: 480px) {
            .header {
                padding: 0.1rem 0 !important;
            }
            
            .navbar {
                height: 35px !important;
            }
            
            .logo {
                font-size: 1.2rem !important;
            }
            
            .main-content {
                padding-top: 40px !important;
            }
            
            .offers-title-compact {
                font-size: 1.3rem !important;
            }
        }
    </style>
    
    <!-- Structured Data JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "STYLOFITNESS",
        "description": "Gimnasio profesional con rutinas personalizadas y tienda de suplementos deportivos",
        "url": "<?php echo AppHelper::baseUrl(); ?>",
        "logo": "<?php echo AppHelper::asset('images/logo.png'); ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Av. Principal 123",
            "addressLocality": "San Isidro",
            "addressRegion": "Lima",
            "addressCountry": "PE"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+51-999-888-777",
            "contactType": "customer service",
            "availableLanguage": "Spanish"
        },
        "sameAs": [
            "https://facebook.com/stylofitness",
            "https://instagram.com/stylofitness",
            "https://twitter.com/stylofitness"
        ]
    }
    </script>
</head>
<body class="<?php echo $bodyClass ?? ''; ?>">
    
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
        <div class="loading-content">
            <div class="loading-logo">
                <h1 class="gradient-text">STYLOFITNESS</h1>
            </div>
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
            <p class="loading-text">Cargando tu experiencia fitness...</p>
        </div>
    </div>
    
    <!-- Header -->
    <header class="header" id="main-header">
        <div class="container">
            <nav class="navbar">
                <!-- Logo -->
                <div class="navbar-brand">
                    <a href="<?php echo AppHelper::baseUrl(); ?>" class="logo">
                        STYLOFITNESS
                    </a>
                </div>
                
                <!-- Menu Principal -->
                <ul class="nav-menu" id="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo AppHelper::baseUrl(); ?>" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/' ? 'active' : ''); ?>">
                            <i class="fas fa-home"></i>
                            Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo AppHelper::baseUrl('routines'); ?>" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/routines') === 0 ? 'active' : ''); ?>">
                            <i class="fas fa-dumbbell"></i>
                            Rutinas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo AppHelper::baseUrl('store'); ?>" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/store') === 0 ? 'active' : ''); ?>">
                            <i class="fas fa-store"></i>
                            Tienda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo AppHelper::baseUrl('classes'); ?>" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/classes') === 0 ? 'active' : ''); ?>">
                            <i class="fas fa-users"></i>
                            Clases
                        </a>
                    </li>

                </ul>
                
                <!-- Buscador -->
                <div class="navbar-search">
                    <form action="<?php echo AppHelper::baseUrl('store/search'); ?>" method="GET" class="search-form">
                        <div class="search-input-group">
                            <input type="text" name="q" placeholder="Buscar productos..." class="search-input" 
                                   value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" autocomplete="off">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="search-suggestions" id="search-suggestions"></div>
                    </form>
                </div>
                
                <!-- Carrito y Usuario -->
                <div class="navbar-actions">
                    <!-- Carrito -->
                    <div class="cart-wrapper">
                        <a href="<?php echo AppHelper::baseUrl('cart'); ?>" class="cart-icon" id="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cart-count" style="display: none;">0</span>
                        </a>
                    </div>
                    
                    <!-- Wishlist -->
                    <?php if (AppHelper::isLoggedIn()): ?>
                    <div class="wishlist-wrapper">
                        <a href="<?php echo AppHelper::baseUrl('wishlist'); ?>" class="wishlist-icon">
                            <i class="fas fa-heart"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Usuario -->
                    <div class="user-menu">
                        <?php if (AppHelper::isLoggedIn()): ?>
                            <?php $user = AppHelper::getCurrentUser(); ?>
                            <div class="user-dropdown">
                                <button class="user-trigger" id="user-menu-trigger" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php if ($user['profile_image']): ?>
                                        <img src="<?php echo AppHelper::uploadUrl($user['profile_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($user['first_name']); ?>" 
                                             class="user-avatar">
                                    <?php else: ?>
                                        <div class="user-avatar-placeholder">
                                            <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="user-name"><?php echo htmlspecialchars($user['first_name']); ?></span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                
                                <div class="user-dropdown-menu dropdown-menu" id="user-dropdown-menu">
                                    <div class="user-info">
                                        <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                        <small><?php echo htmlspecialchars($user['email']); ?></small>
                                    </div>
                                    <hr>
                                    <a href="<?php echo AppHelper::baseUrl('dashboard'); ?>" class="dropdown-item">
                                        <i class="fas fa-tachometer-alt"></i>
                                        Dashboard
                                    </a>
                                    <a href="<?php echo AppHelper::baseUrl('profile'); ?>" class="dropdown-item">
                                        <i class="fas fa-user"></i>
                                        Mi Perfil
                                    </a>
                                    <a href="<?php echo AppHelper::baseUrl('orders'); ?>" class="dropdown-item">
                                        <i class="fas fa-shopping-bag"></i>
                                        Mis Pedidos
                                    </a>
                                    <?php if ($user['role'] === 'client'): ?>
                                    <a href="<?php echo AppHelper::baseUrl('my-routines'); ?>" class="dropdown-item">
                                        <i class="fas fa-dumbbell"></i>
                                        Mis Rutinas
                                    </a>
                                    <a href="<?php echo AppHelper::baseUrl('my-classes'); ?>" class="dropdown-item">
                                        <i class="fas fa-calendar"></i>
                                        Mis Clases
                                    </a>
                                    <?php endif; ?>
                                    <hr>
                                    <a href="<?php echo AppHelper::baseUrl('logout'); ?>" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Cerrar Sesión
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="auth-buttons">
                                <a href="<?php echo AppHelper::baseUrl('login'); ?>" class="btn-login" title="Ingresar">
                                    <i class="fas fa-sign-in-alt btn-icon"></i>
                                    <span class="btn-text">Ingresar</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Toggle móvil -->
                <button class="nav-toggle" id="nav-toggle">
                    <span class="nav-toggle-bar"></span>
                    <span class="nav-toggle-bar"></span>
                    <span class="nav-toggle-bar"></span>
                </button>
            </nav>
        </div>
    </header>
    
    <!-- Overlay para menú móvil -->
    <div class="nav-overlay" id="nav-overlay"></div>
    
    <!-- Mensajes Flash -->
    <?php if ($flashMessage = AppHelper::getFlashMessage('success')): ?>
        <div class="flash-message flash-success">
            <div class="container">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($flashMessage); ?></span>
                <button class="flash-close">&times;</button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($flashMessage = AppHelper::getFlashMessage('error')): ?>
        <div class="flash-message flash-error">
            <div class="container">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($flashMessage); ?></span>
                <button class="flash-close">&times;</button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($flashMessage = AppHelper::getFlashMessage('warning')): ?>
        <div class="flash-message flash-warning">
            <div class="container">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?php echo htmlspecialchars($flashMessage); ?></span>
                <button class="flash-close">&times;</button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($flashMessage = AppHelper::getFlashMessage('info')): ?>
        <div class="flash-message flash-info">
            <div class="container">
                <i class="fas fa-info-circle"></i>
                <span><?php echo htmlspecialchars($flashMessage); ?></span>
                <button class="flash-close">&times;</button>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Contenido Principal -->
    <main class="main-content" id="main-content">
