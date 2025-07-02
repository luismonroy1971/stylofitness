-- Agregar 2 productos más destacados para verificar el carrusel del home
-- STYLOFITNESS - Productos Destacados Adicionales

USE stylofitness_gym;

-- Producto 5: BCAA 2:1:1 Powder
INSERT INTO `products` (`category_id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `sale_price`, `stock_quantity`, `weight`, `images`, `brand`, `is_featured`) VALUES
(6, 'BCAA 2:1:1 Powder 300g', 'bcaa-211-powder-300g', 'Aminoácidos de cadena ramificada en proporción 2:1:1 (Leucina, Isoleucina, Valina). Ideal para prevenir el catabolismo muscular y acelerar la recuperación post-entreno.', 'BCAA 2:1:1 - Recuperación muscular avanzada - Sabor Frutas Tropicales', 'BCAA-211-001', 119.90, 99.90, 60, 0.30, JSON_ARRAY('/images/products/bcaa-powder-1.jpg'), 'Scivation', 1);

-- Producto 6: Quemador de Grasa Thermogenic
INSERT INTO `products` (`category_id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `sale_price`, `stock_quantity`, `weight`, `images`, `brand`, `is_featured`) VALUES
(5, 'Hydroxycut Hardcore Elite 100 caps', 'hydroxycut-hardcore-elite-100caps', 'Quemador de grasa termogénico de máxima potencia. Fórmula avanzada con cafeína, extracto de café verde y otros ingredientes científicamente probados para acelerar el metabolismo.', 'Quemador #1 en ventas - Fórmula termogénica avanzada', 'BURN-HCE-001', 179.90, 159.90, 35, 0.12, JSON_ARRAY('/images/products/hydroxycut-1.jpg'), 'MuscleTech', 1);

-- Verificar productos destacados
SELECT 
    id,
    name,
    brand,
    price,
    sale_price,
    is_featured
FROM products 
WHERE is_featured = 1 
ORDER BY id;

-- Contar total de productos destacados
SELECT COUNT(*) as total_productos_destacados 
FROM products 
WHERE is_featured = 1;