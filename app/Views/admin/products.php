<?php
use StyleFitness\Helpers\AppHelper;

// Verificar que el usuario esté autenticado y sea admin
if (!AppHelper::isLoggedIn() || AppHelper::getCurrentUser()['role'] !== 'admin') {
    AppHelper::redirect('/login');
    exit;
}

$user = AppHelper::getCurrentUser();
$pageTitle = 'Gestión de Productos - STYLOFITNESS';
$additionalCSS = ['admin.css'];
$additionalJS = ['admin-products.js'];
?>

<div class="admin-products-page">
    <style>
        .admin-products-page {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .page-header {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            color: #2c3e50;
            margin: 0;
            font-size: 2.2em;
            font-weight: 300;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #1f5f8b);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .filters-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            color: #2c3e50;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .form-control {
            padding: 10px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .products-table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .products-table th {
            background: #34495e;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 500;
        }
        
        .products-table td {
            padding: 15px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .products-table tr:hover {
            background: #f8f9fa;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .product-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .product-sku {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        
        .price-display {
            font-weight: 600;
            color: #27ae60;
        }
        
        .sale-price {
            color: #e74c3c;
            text-decoration: line-through;
            font-size: 0.9em;
        }
        
        .stock-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
        }
        
        .stock-high {
            background: #d5f4e6;
            color: #27ae60;
        }
        
        .stock-medium {
            background: #fef9e7;
            color: #f39c12;
        }
        
        .stock-low {
            background: #fadbd8;
            color: #e74c3c;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
        }
        
        .status-active {
            background: #d5f4e6;
            color: #27ae60;
        }
        
        .status-inactive {
            background: #fadbd8;
            color: #e74c3c;
        }
        
        .actions-cell {
            display: flex;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8em;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background: #3498db;
            color: white;
        }
        
        .btn-edit:hover {
            background: #2980b9;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        .btn-delete:hover {
            background: #c0392b;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: #2c3e50;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }
        
        .pagination a:hover {
            background: #3498db;
            color: white;
        }
        
        .pagination .current {
            background: #3498db;
            color: white;
        }
        
        .no-products {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d5f4e6;
            color: #27ae60;
            border: 1px solid #27ae60;
        }
        
        .alert-error {
            background: #fadbd8;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .filters-form {
                grid-template-columns: 1fr;
            }
            
            .products-table-container {
                overflow-x: auto;
            }
            
            .actions-cell {
                flex-direction: column;
            }
        }
    </style>
    
    <!-- Mensajes Flash -->
    <?php if (AppHelper::hasFlashMessage('success')): ?>
        <div class="alert alert-success">
            <?= AppHelper::getFlashMessage('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if (AppHelper::hasFlashMessage('error')): ?>
        <div class="alert alert-error">
            <?= AppHelper::getFlashMessage('error') ?>
        </div>
    <?php endif; ?>
    
    <!-- Header de la página -->
    <div class="page-header">
        <h1 class="page-title">Gestión de Productos</h1>
        <a href="/admin/products/create" class="btn-primary">
            + Crear Nuevo Producto
        </a>
    </div>
    
    <!-- Filtros -->
    <div class="filters-section">
        <form method="GET" class="filters-form">
            <div class="form-group">
                <label for="search">Buscar</label>
                <input type="text" id="search" name="search" class="form-control" 
                       placeholder="Nombre, SKU o descripción" 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Categoría</label>
                <select id="category" name="category" class="form-control">
                    <option value="">Todas las categorías</option>
                    <?php if (isset($categories) && !empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" 
                                    <?= ($_GET['category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status">Estado</label>
                <select id="status" name="status" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" <?= ($_GET['status'] ?? '') === '1' ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= ($_GET['status'] ?? '') === '0' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="featured">Destacado</label>
                <select id="featured" name="featured" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" <?= ($_GET['featured'] ?? '') === '1' ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= ($_GET['featured'] ?? '') === '0' ? 'selected' : '' ?>>No</option>
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-secondary">Filtrar</button>
            </div>
        </form>
    </div>
    
    <!-- Tabla de productos -->
    <div class="products-table-container">
        <?php if (!empty($products)): ?>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Destacado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $images = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
                        $mainImage = !empty($images) ? $images[0] : '/images/placeholder.jpg';
                        $stockClass = $product['stock_quantity'] > 20 ? 'stock-high' : 
                                     ($product['stock_quantity'] > 5 ? 'stock-medium' : 'stock-low');
                        ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($mainImage) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="product-image">
                            </td>
                            <td>
                                <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                <div class="product-sku">SKU: <?= htmlspecialchars($product['sku']) ?></div>
                            </td>
                            <td>
                                <?= htmlspecialchars($product['category_name'] ?? 'Sin categoría') ?>
                            </td>
                            <td>
                                <?php if (!empty($product['sale_price']) && $product['sale_price'] > 0): ?>
                                    <div class="price-display">$<?= number_format($product['sale_price'], 2) ?></div>
                                    <div class="sale-price">$<?= number_format($product['price'], 2) ?></div>
                                <?php else: ?>
                                    <div class="price-display">$<?= number_format($product['price'], 2) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="stock-badge <?= $stockClass ?>">
                                    <?= $product['stock_quantity'] ?> unidades
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?= $product['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $product['is_active'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?= $product['is_featured'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $product['is_featured'] ? 'Sí' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <a href="/admin/products/edit/<?= $product['id'] ?>" class="btn-sm btn-edit">
                                        Editar
                                    </a>
                                    <button type="button" class="btn-sm btn-delete" 
                                            onclick="deleteProduct(<?= $product['id'] ?>)">
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Paginación -->
            <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                <div class="pagination">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?>&<?= http_build_query(array_filter($_GET, function($key) { return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>">
                            ← Anterior
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                        <?php if ($i == $pagination['current_page']): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>&<?= http_build_query(array_filter($_GET, function($key) { return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?>&<?= http_build_query(array_filter($_GET, function($key) { return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)) ?>">
                            Siguiente →
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-products">
                <p>No se encontraron productos con los filtros aplicados.</p>
                <a href="/admin/products/create" class="btn-primary">Crear primer producto</a>
            </div>
        <?php endif; ?>
    </div>
</div>