-- Corregir todas las rutas de imágenes de productos para usar placeholder
-- STYLOFITNESS - Fix All Product Images

USE stylofitness_gym;

-- Actualizar todos los productos destacados para usar placeholder
UPDATE `products` 
SET `images` = JSON_ARRAY('/images/placeholder.jpg')
WHERE `is_featured` = 1;

-- Verificar que se actualizaron correctamente
SELECT 
    id,
    name,
    images,
    is_featured
FROM products 
WHERE is_featured = 1 
ORDER BY id;