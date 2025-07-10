<?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
    <div class="pagination">
        <?php if ($pagination['current_page'] > 1): ?>
            <a href="#" onclick="adminInstructorsManager.goToPage(<?= $pagination['current_page'] - 1 ?>); return false;">
                <i class="fas fa-chevron-left"></i> Anterior
            </a>
        <?php endif; ?>
        
        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
            <?php if ($i == $pagination['current_page']): ?>
                <span class="current"><?= $i ?></span>
            <?php else: ?>
                <a href="#" onclick="adminInstructorsManager.goToPage(<?= $i ?>); return false;"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
            <a href="#" onclick="adminInstructorsManager.goToPage(<?= $pagination['current_page'] + 1 ?>); return false;">
                Siguiente <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>