<?php
use StyleFitness\Helpers\AppHelper;

// Verificar que el usuario esté autenticado y sea admin
if (!AppHelper::isLoggedIn() || AppHelper::getCurrentUser()['role'] !== 'admin') {
    AppHelper::redirect('/login');
    exit;
}

$user = AppHelper::getCurrentUser();
$isEdit = isset($product) && !empty($product);
$formData = $_SESSION['admin_form_data'] ?? ($isEdit ? $product : []);
$errors = $_SESSION['admin_errors'] ?? [];

// Limpiar datos de sesión
unset($_SESSION['admin_form_data'], $_SESSION['admin_errors']);
?>

<div class="admin-product-form-page">
    <style>
        .admin-product-form-page {
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
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
        
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            color: #2c3e50;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control {
            padding: 12px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .form-control.error {
            border-color: #e74c3c;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        
        .checkbox-group label {
            margin: 0;
            cursor: pointer;
        }
        
        .file-upload-area {
            border: 2px dashed #bdc3c7;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: border-color 0.3s ease;
            cursor: pointer;
        }
        
        .file-upload-area:hover {
            border-color: #3498db;
        }
        
        .file-upload-area.dragover {
            border-color: #3498db;
            background: rgba(52, 152, 219, 0.1);
        }
        
        .upload-icon {
            font-size: 48px;
            color: #bdc3c7;
            margin-bottom: 15px;
        }
        
        .upload-text {
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .file-input {
            display: none;
        }
        
        .image-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .preview-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        
        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #1f5f8b);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
    
    <!-- Header de la página -->
    <div class="page-header">
        <h1 class="page-title"><?= $isEdit ? 'Editar Producto' : 'Crear Nuevo Producto' ?></h1>
        <a href="/admin/products" class="btn-secondary">
            ← Volver a Productos
        </a>
    </div>
    
    <!-- Errores generales -->
    <?php if (!empty($errors) && isset($errors['general'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($errors['general']) ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulario -->
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data" 
              action="<?= $isEdit ? '/admin/products/update/' . $product['id'] : '/admin/products/store' ?>">
            
            <div class="form-grid">
                <!-- Nombre del producto -->
                <div class="form-group">
                    <label for="name">Nombre del Producto *</label>
                    <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'error' : '' ?>" 
                           value="<?= htmlspecialchars($formData['name'] ?? '') ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- SKU -->
                <div class="form-group">
                    <label for="sku">SKU *</label>
                    <input type="text" id="sku" name="sku" class="form-control <?= isset($errors['sku']) ? 'error' : '' ?>" 
                           value="<?= htmlspecialchars($formData['sku'] ?? '') ?>" required>
                    <?php if (isset($errors['sku'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['sku']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Categoría -->
                <div class="form-group">
                    <label for="category_id">Categoría *</label>
                    <select id="category_id" name="category_id" class="form-control <?= isset($errors['category_id']) ? 'error' : '' ?>" required>
                        <option value="">Seleccionar categoría</option>
                        <?php if (isset($categories) && !empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= ($formData['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (isset($errors['category_id'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['category_id']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Marca -->
                <div class="form-group">
                    <label for="brand">Marca</label>
                    <input type="text" id="brand" name="brand" class="form-control" 
                           value="<?= htmlspecialchars($formData['brand'] ?? '') ?>">
                </div>
                
                <!-- Precio normal -->
                <div class="form-group">
                    <label for="price">Precio Normal *</label>
                    <input type="number" id="price" name="price" class="form-control <?= isset($errors['price']) ? 'error' : '' ?>" 
                           step="0.01" min="0" value="<?= htmlspecialchars($formData['price'] ?? '') ?>" required>
                    <?php if (isset($errors['price'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['price']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Precio de oferta -->
                <div class="form-group">
                    <label for="sale_price">Precio de Oferta</label>
                    <input type="number" id="sale_price" name="sale_price" class="form-control" 
                           step="0.01" min="0" value="<?= htmlspecialchars($formData['sale_price'] ?? '') ?>">
                </div>
                
                <!-- Stock -->
                <div class="form-group">
                    <label for="stock_quantity">Cantidad en Stock *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" class="form-control <?= isset($errors['stock_quantity']) ? 'error' : '' ?>" 
                           min="0" value="<?= htmlspecialchars($formData['stock_quantity'] ?? '') ?>" required>
                    <?php if (isset($errors['stock_quantity'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['stock_quantity']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Peso -->
                <div class="form-group">
                    <label for="weight">Peso (kg)</label>
                    <input type="number" id="weight" name="weight" class="form-control" 
                           step="0.01" min="0" value="<?= htmlspecialchars($formData['weight'] ?? '') ?>">
                </div>
                
                <!-- Descripción corta -->
                <div class="form-group full-width">
                    <label for="short_description">Descripción Corta</label>
                    <textarea id="short_description" name="short_description" class="form-control" 
                              rows="3" placeholder="Descripción breve del producto"><?= htmlspecialchars($formData['short_description'] ?? '') ?></textarea>
                </div>
                
                <!-- Descripción completa -->
                <div class="form-group full-width">
                    <label for="description">Descripción Completa</label>
                    <textarea id="description" name="description" class="form-control" 
                              rows="6" placeholder="Descripción detallada del producto"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                </div>
                
                <!-- Meta título -->
                <div class="form-group">
                    <label for="meta_title">Meta Título (SEO)</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" 
                           value="<?= htmlspecialchars($formData['meta_title'] ?? '') ?>" 
                           placeholder="Título para motores de búsqueda">
                </div>
                
                <!-- Meta descripción -->
                <div class="form-group">
                    <label for="meta_description">Meta Descripción (SEO)</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" 
                              rows="3" placeholder="Descripción para motores de búsqueda"><?= htmlspecialchars($formData['meta_description'] ?? '') ?></textarea>
                </div>
                
                <!-- Imágenes -->
                <div class="form-group full-width">
                    <label>Imágenes del Producto</label>
                    <div class="file-upload-area" onclick="document.getElementById('images').click()">
                        <div class="upload-icon">📷</div>
                        <div class="upload-text">Haz clic aquí o arrastra las imágenes</div>
                        <div style="font-size: 12px; color: #95a5a6;">Formatos: JPG, PNG, WebP (máx. 5MB cada una)</div>
                    </div>
                    <input type="file" id="images" name="images[]" class="file-input" 
                           multiple accept="image/*" onchange="previewImages(this)">
                    <div id="image-preview" class="image-preview"></div>
                </div>
                
                <!-- Opciones -->
                <div class="form-group full-width">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" 
                               <?= ($formData['is_active'] ?? true) ? 'checked' : '' ?>>
                        <label for="is_active">Producto activo</label>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_featured" name="is_featured" 
                               <?= ($formData['is_featured'] ?? false) ? 'checked' : '' ?>>
                        <label for="is_featured">Producto destacado</label>
                    </div>
                </div>
            </div>
            
            <!-- Acciones del formulario -->
            <div class="form-actions">
                <a href="/admin/products" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <?= $isEdit ? 'Actualizar Producto' : 'Crear Producto' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImages(input) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" class="preview-image" alt="Preview ${index + 1}">
                        <button type="button" class="remove-image" onclick="removePreviewImage(this, ${index})">
                            ×
                        </button>
                    `;
                    preview.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

function removePreviewImage(button, index) {
    const previewItem = button.parentElement;
    previewItem.remove();
    
    // Actualizar el input de archivos
    const input = document.getElementById('images');
    const dt = new DataTransfer();
    
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
}

// Drag and drop functionality
const uploadArea = document.querySelector('.file-upload-area');

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    const input = document.getElementById('images');
    input.files = files;
    previewImages(input);
});

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    
    // Si hay un campo slug visible, actualizarlo
    const slugField = document.getElementById('slug');
    if (slugField) {
        slugField.value = slug;
    }
});
</script>