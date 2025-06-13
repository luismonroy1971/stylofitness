    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <!-- Información del Gimnasio -->
                <div class="footer-section">
                    <h3>STYLOFITNESS</h3>
                    <p>Tu gimnasio profesional con rutinas personalizadas y los mejores suplementos deportivos. Transformamos vidas a través del fitness.</p>
                    
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Av. Principal 123, San Isidro, Lima</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+51 999 888 777</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>info@stylofitness.com</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <span>Lun - Vie: 5:00 AM - 11:00 PM<br>Sáb - Dom: 6:00 AM - 10:00 PM</span>
                        </div>
                    </div>
                </div>
                
                <!-- Enlaces Rápidos -->
                <div class="footer-section">
                    <h3>Enlaces Rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="<?php echo AppHelper::baseUrl(); ?>">Inicio</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('routines'); ?>">Rutinas Personalizadas</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('store'); ?>">Tienda Online</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('classes'); ?>">Clases Grupales</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('memberships'); ?>">Membresías</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('trainers'); ?>">Entrenadores</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('about'); ?>">Nosotros</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('contact'); ?>">Contacto</a></li>
                    </ul>
                </div>
                
                <!-- Tienda -->
                <div class="footer-section">
                    <h3>Tienda</h3>
                    <ul class="footer-links">
                        <li><a href="<?php echo AppHelper::baseUrl('store/category/proteinas'); ?>">Proteínas</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('store/category/pre-entrenos'); ?>">Pre-entrenos</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('store/category/vitaminas'); ?>">Vitaminas</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('store/category/accesorios'); ?>">Accesorios</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('store/category/ropa-deportiva'); ?>">Ropa Deportiva</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('store/offers'); ?>">Ofertas Especiales</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('store/new'); ?>">Nuevos Productos</a></li>
                        <li><a href="<?php echo AppHelper::baseUrl('store/bundles'); ?>">Paquetes Combo</a></li>
                    </ul>
                </div>
                
                <!-- Newsletter y Redes Sociales -->
                <div class="footer-section">
                    <h3>Mantente Conectado</h3>
                    <p>Suscríbete a nuestro newsletter y recibe las mejores ofertas, rutinas exclusivas y consejos de fitness.</p>
                    
                    <form class="newsletter-form" id="newsletter-form">
                        <div class="newsletter-input">
                            <input type="email" name="email" placeholder="Tu email" required>
                            <button type="submit" class="newsletter-btn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <small class="newsletter-note">*No spam. Solo contenido de calidad.</small>
                    </form>
                    
                    <div class="social-links">
                        <a href="https://facebook.com/stylofitness" target="_blank" rel="noopener" class="social-link facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://instagram.com/stylofitness" target="_blank" rel="noopener" class="social-link instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://twitter.com/stylofitness" target="_blank" rel="noopener" class="social-link twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://youtube.com/stylofitness" target="_blank" rel="noopener" class="social-link youtube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="https://tiktok.com/@stylofitness" target="_blank" rel="noopener" class="social-link tiktok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="https://wa.me/51999888777" target="_blank" rel="noopener" class="social-link whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Información Adicional -->
            <div class="footer-extra">
                <div class="footer-badges">
                    <div class="badge-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Compra Segura</span>
                    </div>
                    <div class="badge-item">
                        <i class="fas fa-truck"></i>
                        <span>Envío Gratis +S/150</span>
                    </div>
                    <div class="badge-item">
                        <i class="fas fa-medal"></i>
                        <span>Productos Originales</span>
                    </div>
                    <div class="badge-item">
                        <i class="fas fa-headset"></i>
                        <span>Soporte 24/7</span>
                    </div>
                </div>
                
                <div class="payment-methods">
                    <span>Métodos de Pago:</span>
                    <div class="payment-icons">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-paypal"></i>
                        <i class="fas fa-mobile-alt"></i>
                        <span class="payment-text">Yape/Plin</span>
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> STYLOFITNESS. Todos los derechos reservados.</p>
                    <p>Desarrollado con 💪 para transformar vidas a través del fitness.</p>
                </div>
                
                <div class="footer-legal">
                    <a href="<?php echo AppHelper::baseUrl('privacy'); ?>">Política de Privacidad</a>
                    <a href="<?php echo AppHelper::baseUrl('terms'); ?>">Términos y Condiciones</a>
                    <a href="<?php echo AppHelper::baseUrl('refund'); ?>">Política de Devoluciones</a>
                    <a href="<?php echo AppHelper::baseUrl('shipping'); ?>">Información de Envío</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Botón Back to Top -->
    <button class="back-to-top" id="back-to-top" title="Ir arriba">
        <i class="fas fa-chevron-up"></i>
    </button>
    
    <!-- Chat Widget -->
    <div class="chat-widget" id="chat-widget">
        <button class="chat-toggle" id="chat-toggle">
            <i class="fas fa-comments"></i>
            <span class="chat-notification" id="chat-notification">1</span>
        </button>
        
        <div class="chat-window" id="chat-window">
            <div class="chat-header">
                <div class="chat-title">
                    <i class="fas fa-dumbbell"></i>
                    <span>Asistente STYLOFITNESS</span>
                </div>
                <button class="chat-close" id="chat-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="chat-messages" id="chat-messages">
                <div class="chat-message bot-message">
                    <div class="message-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="message-content">
                        <p>¡Hola! 👋 Soy tu asistente virtual de STYLOFITNESS.</p>
                        <p>¿En qué puedo ayudarte hoy?</p>
                        <div class="quick-actions">
                            <button class="quick-action" data-action="routines">Ver Rutinas</button>
                            <button class="quick-action" data-action="store">Explorar Tienda</button>
                            <button class="quick-action" data-action="classes">Reservar Clase</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="chat-input">
                <input type="text" placeholder="Escribe tu mensaje..." id="chat-input">
                <button type="submit" id="chat-send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Modales -->
    
    <!-- Modal de Producto -->
    <div class="modal-overlay" id="product-modal-overlay">
        <div class="modal product-modal" id="product-modal">
            <div class="modal-header">
                <h2 class="modal-title">Vista Rápida</h2>
                <button class="modal-close" id="product-modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="product-modal-body">
                <!-- Contenido cargado dinámicamente -->
            </div>
        </div>
    </div>
    
    <!-- Modal de Carrito -->
    <div class="modal-overlay" id="cart-modal-overlay">
        <div class="modal cart-modal" id="cart-modal">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-shopping-cart"></i>
                    Carrito de Compras
                </h2>
                <button class="modal-close" id="cart-modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="cart-modal-body">
                <!-- Contenido cargado dinámicamente -->
            </div>
        </div>
    </div>
    
    <!-- Scripts JavaScript -->
    
    <!-- jQuery (si es necesario para algún plugin) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" defer></script>
    
    <!-- Swiper para carruseles -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.7/swiper-bundle.min.js" defer></script>
    
    <!-- AOS (Animate On Scroll) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" defer></script>
    
    <!-- JavaScript principal -->
    <script src="<?php echo AppHelper::asset('js/app.js'); ?>" defer></script>
    
    <!-- Scripts adicionales según la página -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo AppHelper::asset("js/{$js}"); ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Scripts inline si es necesario -->
    <?php if (isset($inlineJS)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?php echo $inlineJS; ?>
            });
        </script>
    <?php endif; ?>
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>
    
    <!-- Facebook Pixel -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', 'FB_PIXEL_ID'); 
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" 
             src="https://www.facebook.com/tr?id=FB_PIXEL_ID&ev=PageView&noscript=1"/>
    </noscript>
    
    <!-- Schema.org datos estructurados específicos de la página -->
    <?php if (isset($structuredData)): ?>
        <script type="application/ld+json">
            <?php echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
        </script>
    <?php endif; ?>
    
    <!-- Inicialización final -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ocultar loading screen
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                setTimeout(() => {
                    loadingScreen.style.opacity = '0';
                    setTimeout(() => {
                        loadingScreen.style.display = 'none';
                    }, 300);
                }, 1000);
            }
            
            // Inicializar AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true,
                    offset: 100
                });
            }
            
            // Back to top button
            const backToTop = document.getElementById('back-to-top');
            if (backToTop) {
                window.addEventListener('scroll', () => {
                    if (window.pageYOffset > 300) {
                        backToTop.classList.add('show');
                    } else {
                        backToTop.classList.remove('show');
                    }
                });
                
                backToTop.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
            
            // Newsletter form
            const newsletterForm = document.getElementById('newsletter-form');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const email = this.querySelector('input[name="email"]').value;
                    
                    // Aquí iría la lógica de suscripción
                    STYLOFITNESS.showNotification('¡Suscrito!', 'Te has suscrito exitosamente a nuestro newsletter', 'success');
                    this.reset();
                });
            }
            
            // Chat widget
            const chatToggle = document.getElementById('chat-toggle');
            const chatWindow = document.getElementById('chat-window');
            const chatClose = document.getElementById('chat-close');
            
            if (chatToggle && chatWindow) {
                chatToggle.addEventListener('click', () => {
                    chatWindow.classList.toggle('show');
                    document.getElementById('chat-notification').style.display = 'none';
                });
                
                if (chatClose) {
                    chatClose.addEventListener('click', () => {
                        chatWindow.classList.remove('show');
                    });
                }
            }
            
            // Flash messages auto-hide
            const flashMessages = document.querySelectorAll('.flash-message');
            flashMessages.forEach(message => {
                const closeBtn = message.querySelector('.flash-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        message.style.opacity = '0';
                        setTimeout(() => {
                            message.remove();
                        }, 300);
                    });
                }
                
                // Auto-hide después de 5 segundos
                setTimeout(() => {
                    if (message.parentNode) {
                        message.style.opacity = '0';
                        setTimeout(() => {
                            if (message.parentNode) {
                                message.remove();
                            }
                        }, 300);
                    }
                }, 5000);
            });
        });
        
        // Service Worker para PWA (opcional)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW registered: ', registration);
                    })
                    .catch(function(registrationError) {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
</body>
</html>