<?php
/**
 * Script de prueba para verificar la limpieza de descripciones
 * Prueba la función AppHelper::cleanDescription() y AppHelper::safeDescription()
 */

require_once 'app/Config/bootstrap.php';
require_once 'app/Helpers/AppHelper.php';

use StyleFitness\Helpers\AppHelper;
use StyleFitness\Config\Database;

// Configurar headers para evitar cache
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
    <title>Prueba de Limpieza de Descripciones - STYLOFITNESS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
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
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .test-title {
            color: #333;
            border-bottom: 2px solid #FF6B00;
            padding-bottom: 10px;
        }
        .before, .after {
            margin: 15px 0;
            padding: 15px;
            border-radius: 5px;
        }
        .before {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
        }
        .after {
            background-color: #e8f5e8;
            border-left: 4px solid #4caf50;
        }
        .product-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            background: #fafafa;
        }
        .product-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .btn {
            background: #FF6B00;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #E55A00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧹 Prueba de Limpieza de Descripciones</h1>
        <p>Este script verifica que las descripciones de productos se muestren correctamente sin caracteres especiales como &amp;, &lt;, &gt;</p>
        
        <!-- Pruebas con ejemplos manuales -->
        <div class="test-section">
            <h2 class="test-title">📝 Pruebas con Ejemplos Manuales</h2>
            
            <?php
            $testDescriptions = [
                '¡CARNIVOR&amp;amp;lt;strong&amp;amp;gt; es la proteína de carne pura más vendida del mundo!',
                'Proteína &amp;lt;strong&amp;gt;WHEY&amp;lt;/strong&amp;gt; de alta calidad &amp;amp; pureza',
                'Suplemento con &amp;quot;ingredientes naturales&amp;quot; &amp;amp; vitaminas',
                'Producto &amp;lt;em&amp;gt;premium&amp;lt;/em&amp;gt; para &amp;amp; atletas profesionales',
                'Descripción normal sin caracteres especiales'
            ];
            
            foreach ($testDescriptions as $index => $testDesc) {
                echo "<div class='product-card'>";
                echo "<div class='product-name'>Ejemplo " . ($index + 1) . "</div>";
                echo "<div class='before'><strong>ANTES (con entidades HTML):</strong><br>" . htmlspecialchars($testDesc) . "</div>";
                echo "<div class='after'><strong>DESPUÉS (limpio):</strong><br>" . AppHelper::safeDescription($testDesc) . "</div>";
                echo "</div>";
            }
            ?>
        </div>
        
        <!-- Pruebas con productos reales de la base de datos -->
        <div class="test-section">
            <h2 class="test-title">🗄️ Pruebas con Productos Reales</h2>
            
            <?php
            try {
                $db = Database::getInstance();
                $products = $db->fetchAll(
                    "SELECT id, name, description, short_description 
                     FROM products 
                     WHERE (description LIKE '%&amp;%' OR description LIKE '%&lt;%' OR description LIKE '%&gt;%' 
                            OR short_description LIKE '%&amp;%' OR short_description LIKE '%&lt;%' OR short_description LIKE '%&gt;%')
                     AND is_active = 1 
                     LIMIT 10"
                );
                
                if (empty($products)) {
                    echo "<p>✅ ¡Excelente! No se encontraron productos con entidades HTML problemáticas.</p>";
                    
                    // Mostrar algunos productos normales para verificar que la función funciona
                    $normalProducts = $db->fetchAll(
                        "SELECT id, name, description, short_description 
                         FROM products 
                         WHERE is_active = 1 
                         LIMIT 5"
                    );
                    
                    echo "<h3>Productos de muestra (funcionamiento normal):</h3>";
                    foreach ($normalProducts as $product) {
                        echo "<div class='product-card'>";
                        echo "<div class='product-name'>" . htmlspecialchars($product['name']) . " (ID: {$product['id']})</div>";
                        
                        $desc = $product['short_description'] ?: $product['description'];
                        if ($desc) {
                            echo "<div class='before'><strong>ORIGINAL:</strong><br>" . htmlspecialchars($desc) . "</div>";
                            echo "<div class='after'><strong>PROCESADO:</strong><br>" . AppHelper::safeDescription($desc, 150) . "</div>";
                        } else {
                            echo "<p><em>Sin descripción disponible</em></p>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<p>⚠️ Se encontraron " . count($products) . " productos con entidades HTML que necesitan limpieza:</p>";
                    
                    foreach ($products as $product) {
                        echo "<div class='product-card'>";
                        echo "<div class='product-name'>" . htmlspecialchars($product['name']) . " (ID: {$product['id']})</div>";
                        
                        // Mostrar descripción corta si existe
                        if (!empty($product['short_description'])) {
                            echo "<h4>Descripción Corta:</h4>";
                            echo "<div class='before'><strong>ANTES:</strong><br>" . htmlspecialchars($product['short_description']) . "</div>";
                            echo "<div class='after'><strong>DESPUÉS:</strong><br>" . AppHelper::safeDescription($product['short_description']) . "</div>";
                        }
                        
                        // Mostrar descripción completa
                        if (!empty($product['description'])) {
                            echo "<h4>Descripción Completa:</h4>";
                            echo "<div class='before'><strong>ANTES:</strong><br>" . htmlspecialchars(substr($product['description'], 0, 200)) . "...</div>";
                            echo "<div class='after'><strong>DESPUÉS:</strong><br>" . AppHelper::safeDescription($product['description'], 200) . "</div>";
                        }
                        
                        echo "</div>";
                    }
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error al consultar la base de datos: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>
        
        <!-- Funciones de prueba -->
        <div class="test-section">
            <h2 class="test-title">🔧 Funciones Disponibles</h2>
            <p><strong>AppHelper::cleanDescription($text, $maxLength)</strong> - Limpia entidades HTML y caracteres especiales</p>
            <p><strong>AppHelper::safeDescription($text, $maxLength)</strong> - Limpia y escapa para mostrar en HTML de forma segura</p>
            
            <h3>Ejemplo de uso en las vistas:</h3>
            <pre style="background: #f0f0f0; padding: 15px; border-radius: 5px; overflow-x: auto;">
// En lugar de:
echo htmlspecialchars($product['description']);

// Usar:
echo AppHelper::safeDescription($product['description']);

// Con límite de caracteres:
echo AppHelper::safeDescription($product['description'], 100);
            </pre>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="/" class="btn">🏠 Volver al Inicio</a>
            <a href="/store" class="btn">🛒 Ver Tienda</a>
            <button onclick="location.reload()" class="btn">🔄 Actualizar Prueba</button>
        </div>
    </div>
    
    <script>
        // Auto-refresh cada 30 segundos para pruebas
        setTimeout(function() {
            console.log('Auto-refresh en 30 segundos...');
        }, 30000);
    </script>
</body>
</html>