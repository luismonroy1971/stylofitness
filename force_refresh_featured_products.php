<?php
// Script para forzar la actualización de productos destacados

// Headers para evitar cualquier caché
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Type: text/html; charset=UTF-8');

// Definir constantes necesarias
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

require_once 'app/Config/Database.php';
require_once 'app/Models/Product.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Productos Destacados - STYLOFITNESS</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #f9f9f9;
        }
        .product-name {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .product-info {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }
        .refresh-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        .refresh-btn:hover {
            background-color: #0056b3;
        }
        .api-test {
            margin-top: 30px;
            padding: 20px;
            background: #e9ecef;
            border-radius: 8px;
        }
        .timestamp {
            font-size: 12px;
            color: #999;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Verificación de Productos Destacados</h1>
            <p>STYLOFITNESS - Limpieza de Caché y Verificación</p>
        </div>

        <?php
        try {
            echo '<div class="status success">✅ Conexión a la base de datos establecida</div>';
            
            $product = new StyleFitness\Models\Product();
            $featuredProducts = $product->getFeaturedProducts();
            
            echo '<div class="status success">✅ Productos destacados obtenidos: ' . count($featuredProducts) . '</div>';
            
            if (count($featuredProducts) > 0) {
                echo '<h2>📦 Productos Destacados Actuales</h2>';
                echo '<div class="product-grid">';
                
                foreach ($featuredProducts as $prod) {
                    echo '<div class="product-card">';
                    echo '<div class="product-name">' . htmlspecialchars($prod['name']) . '</div>';
                    echo '<div class="product-info"><strong>ID:</strong> ' . $prod['id'] . '</div>';
                    echo '<div class="product-info"><strong>Slug:</strong> ' . htmlspecialchars($prod['slug']) . '</div>';
                    echo '<div class="product-info"><strong>Precio:</strong> $' . number_format($prod['price'], 2) . '</div>';
                    if (!empty($prod['sale_price'])) {
                        echo '<div class="product-info"><strong>Precio Oferta:</strong> $' . number_format($prod['sale_price'], 2) . '</div>';
                    }
                    echo '<div class="product-info"><strong>Stock:</strong> ' . ($prod['stock_quantity'] ?? 'N/A') . '</div>';
                    echo '<div class="product-info"><strong>Marca:</strong> ' . htmlspecialchars($prod['brand'] ?? 'N/A') . '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
            } else {
                echo '<div class="status warning">⚠️ No se encontraron productos destacados</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="status error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>

        <div class="api-test">
            <h2>🔗 Prueba de API</h2>
            <p>Haz clic en el botón para probar la API de productos destacados:</p>
            <button class="refresh-btn" onclick="testAPI()">Probar API /api/products/featured</button>
            <button class="refresh-btn" onclick="clearBrowserCache()">Limpiar Caché del Navegador</button>
            <button class="refresh-btn" onclick="location.reload(true)">Recargar Página (Forzado)</button>
            
            <div id="api-result" style="margin-top: 15px;"></div>
        </div>

        <div class="timestamp">
            Generado el: <?php echo date('Y-m-d H:i:s'); ?> | 
            Timestamp: <?php echo time(); ?>
        </div>
    </div>

    <script>
        // Función para probar la API
        function testAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.innerHTML = '<div class="status warning">🔄 Probando API...</div>';
            
            // Agregar timestamp para evitar caché
            const timestamp = new Date().getTime();
            const apiUrl = '/api/products/featured?_t=' + timestamp;
            
            fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data && Array.isArray(data) && data.length > 0) {
                    resultDiv.innerHTML = `
                        <div class="status success">✅ API funcionando correctamente</div>
                        <div class="status success">📦 Productos obtenidos: ${data.length}</div>
                        <div style="margin-top: 10px; font-size: 12px; color: #666;">
                            <strong>Productos:</strong><br>
                            ${data.map(p => `• ${p.name} (ID: ${p.id})`).join('<br>')}
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = '<div class="status warning">⚠️ API responde pero no hay productos</div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<div class="status error">❌ Error en API: ${error.message}</div>`;
            });
        }
        
        // Función para limpiar caché del navegador
        function clearBrowserCache() {
            // Limpiar localStorage
            if (typeof(Storage) !== "undefined") {
                localStorage.clear();
                sessionStorage.clear();
            }
            
            // Mostrar instrucciones
            alert('Caché del navegador limpiado.\n\nPara una limpieza completa:\n1. Presiona Ctrl+Shift+Delete\n2. Selecciona "Imágenes y archivos en caché"\n3. Haz clic en "Eliminar datos"\n\nO presiona Ctrl+F5 para recargar sin caché.');
        }
        
        // Auto-test al cargar la página
        window.onload = function() {
            setTimeout(testAPI, 1000);
        };
    </script>
</body>
</html>