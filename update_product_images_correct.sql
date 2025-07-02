USE stylofitness_gym;

-- Update featured products with correct image paths
UPDATE products 
SET images = JSON_ARRAY('/uploads/images/products/whey-gold-1.jpg')
WHERE name = 'Whey Protein Gold Standard 2.5kg';

UPDATE products 
SET images = JSON_ARRAY('/uploads/images/products/c4-original-1.jpg')
WHERE name = 'C4 Original Pre-Workout 390g';

UPDATE products 
SET images = JSON_ARRAY('/uploads/images/products/creatine-1.jpg')
WHERE name = 'Creatina Monohidrato Micronizada 500g';

-- For the new products, use multivit-1.jpg as placeholder since we don't have specific images
UPDATE products 
SET images = JSON_ARRAY('/uploads/images/products/multivit-1.jpg')
WHERE name = 'BCAA 2:1:1 Powder 300g';

UPDATE products 
SET images = JSON_ARRAY('/uploads/images/products/multivit-1.jpg')
WHERE name = 'Hydroxycut Hardcore Elite 100 caps';

-- Verify the updates
SELECT id, name, images, is_featured 
FROM products 
WHERE is_featured = 1 
ORDER BY id;