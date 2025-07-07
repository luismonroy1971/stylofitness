<?php
/**
 * Script para limpiar descripciones de productos con entidades HTML
 * Actualiza directamente en la base de datos las descripciones que contengan &amp;, &lt;, &gt;
 */

// Definir constantes de la aplicación
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Incluir configuración
require_once APP_PATH . '/Config/Database.php';
require_once APP_PATH . '/Config/App.php';

// Incluir helpers
require_once APP_PATH . '/Helpers/AppHelper.php';

// Autoloader simple para clases
spl_autoload_register(function ($class) {
    // Extraer solo el nombre de la clase del namespace completo
    $className = basename(str_replace('\\', '/', $class));
    
    $paths = [
        APP_PATH . '/Controllers/',
        APP_PATH . '/Models/',
        APP_PATH . '/Helpers/',
        APP_PATH . '/Config/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

use StyleFitness\Helpers\AppHelper;
use StyleFitness\Config\Database;

// Configurar headers
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reparar Descripciones de Productos - STYLOFITNESS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .product-fix {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            background: #fafafa;
        }
        .before, .after {
            margin: 10px 0;
            padding: 10px;
            border-radius: 3px;
        }
        .before {
            background-color: #ffebee;
            border-left: 3px solid #f44336;
        }
        .after {
            background-color: #e8f5e8;
            border-left: 3px solid #4caf50;
        }
        .btn {
            background: #FF6B00;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
            font-size: 16px;
        }
        .btn:hover {
            background: #E55A00;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Reparar Descripciones de Productos</h1>
        <p>Este script identifica y corrige descripciones de productos que contengan entidades HTML problemáticas.</p>
        
        <?php
        $action = $_GET['action'] ?? 'scan';
        
        try {
            $db = Database::getInstance();
            
            if ($action === 'scan') {
                // Escanear productos con problemas
                echo "<div class='alert alert-info'>🔍 <strong>Escaneando productos...</strong></div>";
                
                $problematicProducts = $db->fetchAll(
                    "SELECT id, name, description, short_description 
                     FROM products 
                     WHERE (description LIKE '%&amp;%' OR description LIKE '%&lt;%' OR description LIKE '%&gt;%' 
                            OR short_description LIKE '%&amp;%' OR short_description LIKE '%&lt;%' OR short_description LIKE '%&gt;%')
                     AND is_active = 1"
                );
                
                if (empty($problematicProducts)) {
                    echo "<div class='alert alert-success'>✅ <strong>¡Excelente!</strong> No se encontraron productos con entidades HTML problemáticas.</div>";
                } else {
                    echo "<div class='alert alert-warning'>⚠️ <strong>Se encontraron " . count($problematicProducts) . " productos con entidades HTML que necesitan corrección.</strong></div>";
                    
                    echo "<h2>📋 Productos que serán corregidos:</h2>";
                    
                    foreach ($problematicProducts as $product) {
                        echo "<div class='product-fix'>";
                        echo "<h3>" . htmlspecialchars($product['name']) . " (ID: {$product['id']})</h3>";
                        
                        // Descripción corta
                        if (!empty($product['short_description']) && 
                            (strpos($product['short_description'], '&amp;') !== false || 
                             strpos($product['short_description'], '&lt;') !== false || 
                             strpos($product['short_description'], '&gt;') !== false)) {
                            
                            echo "<h4>Descripción Corta:</h4>";
                            echo "<div class='before'><strong>ANTES:</strong><br>" . htmlspecialchars($product['short_description']) . "</div>";
                            echo "<div class='after'><strong>DESPUÉS:</strong><br>" . AppHelper::cleanDescription($product['short_description']) . "</div>";
                        }
                        
                        // Descripción completa
                        if (!empty($product['description']) && 
                            (strpos($product['description'], '&amp;') !== false || 
                             strpos($product['description'], '&lt;') !== false || 
                             strpos($product['description'], '&gt;') !== false)) {
                            
                            echo "<h4>Descripción Completa:</h4>";
                            echo "<div class='before'><strong>ANTES:</strong><br>" . htmlspecialchars(substr($product['description'], 0, 300)) . "...</div>";
                            echo "<div class='after'><strong>DESPUÉS:</strong><br>" . substr(AppHelper::cleanDescription($product['description']), 0, 300) . "...</div>";
                        }
                        
                        echo "</div>";
                    }
                    
                    echo "<div style='text-align: center; margin: 30px 0;'>";
                    echo "<a href='?action=fix' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de que quieres corregir todos estos productos? Esta acción no se puede deshacer.\")'>🔧 Corregir Todos los Productos</a>";
                    echo "</div>";
                }
                
            } elseif ($action === 'fix') {
                // Corregir productos
                echo "<div class='alert alert-info'>🔧 <strong>Corrigiendo productos...</strong></div>";
                
                $problematicProducts = $db->fetchAll(
                    "SELECT id, name, description, short_description 
                     FROM products 
                     WHERE (description LIKE '%&amp;%' OR description LIKE '%&lt;%' OR description LIKE '%&gt;%' 
                            OR short_description LIKE '%&amp;%' OR short_description LIKE '%&lt;%' OR short_description LIKE '%&gt;%')
                     AND is_active = 1"
                );
                
                $fixedCount = 0;
                $errors = [];
                
                foreach ($problematicProducts as $product) {
                    try {
                        $newDescription = null;
                        $newShortDescription = null;
                        
                        // Limpiar descripción completa si tiene problemas
                        if (!empty($product['description']) && 
                            (strpos($product['description'], '&amp;') !== false || 
                             strpos($product['description'], '&lt;') !== false || 
                             strpos($product['description'], '&gt;') !== false)) {
                            $newDescription = AppHelper::cleanDescription($product['description']);
                        }
                        
                        // Limpiar descripción corta si tiene problemas
                        if (!empty($product['short_description']) && 
                            (strpos($product['short_description'], '&amp;') !== false || 
                             strpos($product['short_description'], '&lt;') !== false || 
                             strpos($product['short_description'], '&gt;') !== false)) {
                            $newShortDescription = AppHelper::cleanDescription($product['short_description']);
                        }
                        
                        // Actualizar solo si hay cambios
                        if ($newDescription !== null || $newShortDescription !== null) {
                            $updateFields = [];
                            $updateValues = [];
                            
                            if ($newDescription !== null) {
                                $updateFields[] = 'description = ?';
                                $updateValues[] = $newDescription;
                            }
                            
                            if ($newShortDescription !== null) {
                                $updateFields[] = 'short_description = ?';
                                $updateValues[] = $newShortDescription;
                            }
                            
                            $updateValues[] = $product['id'];
                            
                            $sql = "UPDATE products SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ?";
                            $db->query($sql, $updateValues);
                            
                            echo "<div class='product-fix'>";
                            echo "<h3>✅ " . htmlspecialchars($product['name']) . " (ID: {$product['id']}) - CORREGIDO</h3>";
                            
                            if ($newShortDescription !== null) {
                                echo "<div class='before'><strong>Descripción corta ANTES:</strong><br>" . htmlspecialchars($product['short_description']) . "</div>";
                                echo "<div class='after'><strong>Descripción corta DESPUÉS:</strong><br>" . htmlspecialchars($newShortDescription) . "</div>";
                            }
                            
                            if ($newDescription !== null) {
                                echo "<div class='before'><strong>Descripción completa ANTES:</strong><br>" . htmlspecialchars(substr($product['description'], 0, 200)) . "...</div>";
                                echo "<div class='after'><strong>Descripción completa DESPUÉS:</strong><br>" . htmlspecialchars(substr($newDescription, 0, 200)) . "...</div>";
                            }
                            
                            echo "</div>";
                            $fixedCount++;
                        }
                        
                    } catch (Exception $e) {
                        $errors[] = "Error al corregir producto {$product['name']} (ID: {$product['id']}): " . $e->getMessage();
                    }
                }
                
                if ($fixedCount > 0) {
                    echo "<div class='alert alert-success'>✅ <strong>¡Corrección completada!</strong> Se corrigieron {$fixedCount} productos.</div>";
                } else {
                    echo "<div class='alert alert-info'>ℹ️ <strong>No se encontraron productos que necesiten corrección.</strong></div>";
                }
                
                if (!empty($errors)) {
                    echo "<div class='alert alert-warning'><strong>Errores encontrados:</strong><ul>";
                    foreach ($errors as $error) {
                        echo "<li>" . htmlspecialchars($error) . "</li>";
                    }
                    echo "</ul></div>";
                }
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-warning'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="?action=scan" class="btn">🔍 Escanear Nuevamente</a>
            <a href="/test_description_cleanup.php" class="btn">🧪 Ver Pruebas</a>
            <a href="/" class="btn">🏠 Volver al Inicio</a>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <h3>📖 Instrucciones:</h3>
            <ol>
                <li><strong>Escanear:</strong> Identifica productos con entidades HTML problemáticas (&amp;amp;, &amp;lt;, &amp;gt;)</li>
                <li><strong>Revisar:</strong> Verifica los cambios propuestos antes de aplicarlos</li>
                <li><strong>Corregir:</strong> Aplica las correcciones a la base de datos</li>
                <li><strong>Verificar:</strong> Usa el script de pruebas para confirmar que todo funciona correctamente</li>
            </ol>
            
            <p><strong>Nota:</strong> Este script utiliza la función <code>AppHelper::cleanDescription()</code> que:</p>
            <ul>
                <li>Decodifica entidades HTML como &amp;amp; → &amp;</li>
                <li>Limpia caracteres de control</li>
                <li>Normaliza espacios en blanco</li>
                <li>Mantiene el contenido legible y seguro</li>
            </ul>
        </div>
    </div>
</body>
</html>