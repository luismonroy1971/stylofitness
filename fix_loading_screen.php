<?php

// Script para eliminar el código JavaScript duplicado del footer.php
$footerPath = __DIR__ . '/app/Views/layout/footer.php';

if (!file_exists($footerPath)) {
    die("Footer file not found: $footerPath\n");
}

$content = file_get_contents($footerPath);

// Buscar y eliminar el código JavaScript duplicado que maneja el loading screen
$patterns = [
    // Patrón 1: Script completo con comentario
    '/\s*<script>\s*\/\/ Ocultar loading screen[\s\S]*?<\/script>\s*/i',
    // Patrón 2: Solo el event listener
    '/\s*window\.addEventListener\(\s*[\'"]load[\'"]\s*,\s*function\(\)\s*\{[\s\S]*?loadingScreen\.style\.display\s*=\s*[\'"]none[\'"];[\s\S]*?\}\);\s*/i',
    // Patrón 3: Código específico del loading screen
    '/\s*const loadingScreen = document\.getElementById\([\'"]loading-screen[\'"]\);[\s\S]*?loadingScreen\.style\.display\s*=\s*[\'"]none[\'"];[\s\S]*?\}\s*/i'
];

$originalContent = $content;

foreach ($patterns as $pattern) {
    $content = preg_replace($pattern, '', $content);
}

// Verificar si se hicieron cambios
if ($content !== $originalContent) {
    // Hacer backup
    $backupPath = $footerPath . '.backup.' . date('Y-m-d-H-i-s');
    file_put_contents($backupPath, $originalContent);
    
    // Guardar el archivo modificado
    file_put_contents($footerPath, $content);
    
    echo "✅ Loading screen duplicado eliminado del footer.php\n";
    echo "📁 Backup creado: $backupPath\n";
    echo "🔧 El loading screen ahora solo se maneja desde app.js\n";
} else {
    echo "ℹ️ No se encontró código duplicado del loading screen en footer.php\n";
}

?>