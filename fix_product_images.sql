-- Corregir las rutas de imágenes de los productos nuevos
-- STYLOFITNESS - Fix Product Images

USE stylofitness_gym;

-- Actualizar producto BCAA para usar placeholder
UPDATE `products` 
SET `images` = JSON_ARRAY('/images/placeholder.jpg')
WHERE `name` = 'BCAA 2:1:1 Powder 300g';

-- Actualizar producto Hydroxycut para usar placeholder
UPDATE `products` 
SET `images` = JSON_ARRAY('/images/placeholder.jpg')
WHERE `name` = 'Hydroxycut Hardcore Elite 100 caps';

-- Verificar que se actualizaron correctamente
SELECT 
    id,
    name,
    images,
    is_featured
FROM products 
WHERE is_featured = 1 
ORDER BY id;