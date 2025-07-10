<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Footer del Panel de Administración - STYLOFITNESS
 * Scripts específicos para el panel de administración
 */
?>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts específicos para Admin -->
<script src="<?php echo AppHelper::asset('js/admin.js'); ?>" defer></script>

<!-- Scripts adicionales según la página admin -->
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

<!-- Inicialización específica para admin -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar panel de administración
        if (typeof AdminPanel !== 'undefined') {
            window.adminPanel = new AdminPanel();
        }
        
        // Flash messages auto-hide
        const flashMessages = document.querySelectorAll('.flash-message, .alert');
        flashMessages.forEach(message => {
            const closeBtn = message.querySelector('.flash-close, .btn-close');
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
</script>

</body>
</html>