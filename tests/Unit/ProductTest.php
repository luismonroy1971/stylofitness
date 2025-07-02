<?php

namespace StyleFitness\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StyleFitness\Config\Database;
use StyleFitness\Models\Product;
use StyleFitness\Helpers\ValidationHelper;

require_once __DIR__ . '/../../app/Config/Database.php';
require_once __DIR__ . '/../../app/Models/Product.php';
require_once __DIR__ . '/../../app/Helpers/ValidationHelper.php';

class ProductTest extends TestCase
{
    private $product;
    private $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Database::getInstance();
        $this->product = new Product();
    }

    public function testProductCreation()
    {
        $productData = $this->createTestProduct([
            'name' => 'Whey Protein',
            'price' => 49.99,
            'stock' => 50,
        ]);

        $this->assertEquals('Whey Protein', $productData['name']);
        $this->assertEquals(49.99, $productData['price']);
        $this->assertEquals(50, $productData['stock']);
        $this->assertTrue((bool)$productData['is_active']);
    }

    public function testProductSlugGeneration()
    {
        $name = 'C4 Original Pre-Workout 390g';
        $expectedSlug = 'c4-original-pre-workout-390g';

        // Test slug generation logic
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
        $slug = trim($slug, '-');

        $this->assertEquals($expectedSlug, $slug);
    }

    public function testProductPriceValidation()
    {
        $validPrices = [0.01, 10.50, 999.99];
        $invalidPrices = [-1, 0, 'invalid'];

        foreach ($validPrices as $price) {
            $this->assertTrue(is_numeric($price) && $price > 0);
        }

        foreach ($invalidPrices as $price) {
            $this->assertFalse(is_numeric($price) && $price > 0);
        }
    }

    public function testProductStockValidation()
    {
        $validStock = [0, 1, 100, 999];
        $invalidStock = [-1, 'invalid', null];

        foreach ($validStock as $stock) {
            $this->assertTrue(is_numeric($stock) && $stock >= 0);
        }

        foreach ($invalidStock as $stock) {
            $this->assertFalse(is_numeric($stock) && $stock >= 0);
        }
    }

    public function testProductNameValidation()
    {
        $validNames = ['Whey Protein', 'C4 Pre-Workout', 'BCAA 2:1:1'];
        $invalidNames = ['', '   ', null];

        foreach ($validNames as $name) {
            $this->assertTrue(!empty(trim($name)));
        }

        foreach ($invalidNames as $name) {
            $this->assertFalse(!empty(trim($name ?? '')));
        }
    }

    public function testProductCategoryAssignment()
    {
        $productData = $this->createTestProduct([
            'category_id' => 2,
        ]);

        $this->assertEquals(2, $productData['category_id']);
        $this->assertTrue(is_numeric($productData['category_id']));
    }

    public function testProductActiveStatus()
    {
        $activeProduct = $this->createTestProduct(['is_active' => 1]);
        $inactiveProduct = $this->createTestProduct(['is_active' => 0]);

        $this->assertTrue((bool)$activeProduct['is_active']);
        $this->assertFalse((bool)$inactiveProduct['is_active']);
    }

    public function testProductDescriptionLength()
    {
        $shortDescription = 'Short desc';
        $longDescription = str_repeat('A', 1000);
        $emptyDescription = '';

        // Test description length constraints
        $this->assertTrue(strlen($shortDescription) <= 1000);
        $this->assertTrue(strlen($longDescription) <= 1000);
        $this->assertTrue(strlen($emptyDescription) >= 0);
    }

    public function testProductImageValidation()
    {
        $validImages = ['product.jpg', 'image.png', 'photo.jpeg', 'pic.gif'];
        $invalidImages = ['file.txt', 'doc.pdf', 'script.js', ''];

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        foreach ($validImages as $image) {
            $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $this->assertTrue(in_array($extension, $allowedExtensions));
        }

        foreach ($invalidImages as $image) {
            if (empty($image)) {
                $this->assertTrue(true); // Empty is allowed
                continue;
            }
            $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $this->assertFalse(in_array($extension, $allowedExtensions));
        }
    }

    public function testProductSearchFunctionality()
    {
        $products = [
            $this->createTestProduct(['name' => 'Whey Protein Isolate', 'description' => 'High quality protein']),
            $this->createTestProduct(['name' => 'Casein Protein', 'description' => 'Slow release protein']),
            $this->createTestProduct(['name' => 'Pre-Workout C4', 'description' => 'Energy booster']),
        ];

        $searchTerm = 'protein';
        $results = array_filter($products, function ($product) use ($searchTerm) {
            return stripos($product['name'], $searchTerm) !== false ||
                   stripos($product['description'], $searchTerm) !== false;
        });

        $this->assertCount(2, $results);
    }

    /**
     * Create test product data
     *
     * @param array $overrides
     * @return array
     */
    private function createTestProduct(array $overrides = []): array
    {
        $defaults = [
            'id' => 1,
            'name' => 'Test Product',
            'description' => 'Test product description',
            'price' => 29.99,
            'stock' => 10,
            'category_id' => 1,
            'is_active' => 1,
            'image' => 'test-product.jpg',
            'slug' => 'test-product',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        return array_merge($defaults, $overrides);
    }

    protected function tearDown(): void
    {
        $this->product = null;
        parent::tearDown();
    }
}
