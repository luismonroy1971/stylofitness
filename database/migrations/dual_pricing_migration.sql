-- =====================================================
-- MIGRACIÓN: Sistema de Doble Precio
-- =====================================================
-- Descripción: Convierte los precios actuales en precios de oferta
-- y establece el precio normal como precio actual + 30 soles
-- =====================================================

USE `stylofitness`;

-- Actualizar productos existentes
-- El precio actual se convierte en sale_price
-- El nuevo price será el precio actual + 30 soles
UPDATE `products` 
SET 
    `sale_price` = `price`,
    `price` = `price` + 30.00
WHERE `sale_price` IS NULL OR `sale_price` = 0;

-- Verificar los cambios
SELECT 
    id,
    name,
    price as precio_normal,
    sale_price as precio_oferta,
    (price - sale_price) as descuento
FROM products 
LIMIT 10;

COMMIT;