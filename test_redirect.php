<?php
/**
 * Script de prueba para redirecciones
 */

// Iniciar sesión
session_start();

// Incluir helpers
require_once __DIR__ . '/app/Helpers/AppHelper.php';

use StyleFitness\Helpers\AppHelper;

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Redirect</title></head><body>";
echo "<h1>Prueba de Redirección</h1>";

// Verificar si se está probando la redirección
if (isset($_GET['test'])) {
    $testType = $_GET['test'];
    
    echo "<p>Probando redirección tipo: {$testType}</p>";
    
    switch ($testType) {
        case 'header':
            echo "<p>Usando header() PHP...</p>";
            header('Location: /admin/dashboard');
            exit();
            break;
            
        case 'apphelper':
            echo "<p>Usando AppHelper::redirect()...</p>";
            AppHelper::redirect('/admin/dashboard');
            break;
            
        case 'javascript':
            echo "<p>Usando JavaScript...</p>";
            echo "<script>window.location.href = '/admin/dashboard';</script>";
            break;
            
        case 'meta':
            echo "<p>Usando meta refresh...</p>";
            echo "<meta http-equiv='refresh' content='0;url=/admin/dashboard'>";
            break;
    }
}

echo "<h2>Opciones de Prueba:</h2>";
echo "<ul>";
echo "<li><a href='?test=header'>Probar header() PHP</a></li>";
echo "<li><a href='?test=apphelper'>Probar AppHelper::redirect()</a></li>";
echo "<li><a href='?test=javascript'>Probar JavaScript redirect</a></li>";
echo "<li><a href='?test=meta'>Probar meta refresh</a></li>";
echo "</ul>";

echo "<h2>Información del Sistema:</h2>";
echo "<pre>";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Session Status: " . session_status() . "\n";
echo "Output Buffering: " . (ob_get_level() > 0 ? 'Activo (nivel ' . ob_get_level() . ')' : 'Inactivo') . "\n";
echo "Headers Sent: " . (headers_sent() ? 'Sí' : 'No') . "\n";
echo "Base URL: " . AppHelper::getBaseUrl() . "\n";
echo "</pre>";

echo "<p><a href='/login'>← Volver al Login</a></p>";
echo "</body></html>";
?>