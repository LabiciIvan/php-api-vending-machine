<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use RuntimeException;
use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductModelTest extends TestCase
{
    private const PATH_TO_PRODUCTS = __DIR__ . '/../../backup_products.json';

    /**
     * @test
     */
    public function wrong_path_when_create_class(): void
    {
        $path = '/random/path';

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('Failed to open stream: No such file or directory');

        new Product($path);
    }

    /**
     * @test
     */
    public function is_null_returned_when_product_not_found(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $category = 'NotExist';

        $id = 101;

        $product = $productModel->one($category, $id);

        $this->assertNull($product);
    }

    /**
     * @test
     */
    public function all_products_returned_as_expected(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $products = $productModel->all();

        $this->assertIsArray($products);

        foreach($products as $category => $product) {
            $this->assertIsString($category);
            $this->assertIsArray($product);
        }
    }


    /**
     * @test
     */
    public function exception_thrown_when_create_product_for_a_nonexistent_category(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $productData = [
            'category' => 'DOES_NOT_EXIST',
            'price' => '12.00',
            'quantity' => 12,
            'name' => 'PRODUCT UNIT TEST'
        ];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Category `{$productData['category']}` does not exist");

        $productModel->create($productData);
    }

    /**
     * @test
     */
    public function exception_thrown_when_create_product_with_missing_category_key(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $productData = [
            'price' => '12.00',
            'quantity' => 12,
            'name' => 'PRODUCT UNIT TEST'
        ];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing `category` key in the array");

        $productModel->create($productData);
    }

    /**
     * @test
     */
    public function create_product_successfully(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $productData = [
            'category' => 'A',
            'price' => '12.00',
            'quantity' => 12,
            'name' => 'PRODUCT UNIT TEST'
        ];

        $id = $productModel->create($productData);

        $product = $productModel->one('A', $id);

        $this->assertIsInt($id);
        $this->assertIsArray($product);
    }

    /**
     * @test
     */
    public function save_products_under_wrong_path_throw_exception(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find file in which to save data');

        $productModel->save('/unknown/path/items.jsonsr');
    }

    /**
     * @test
     */
    public function create_and_save_product_successfully(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $productData = [
            'category' => 'A',
            'price' => '12.00',
            'quantity' => 12,
            'name' => 'PRODUCT UNIT TEST'
        ];

        $id = $productModel->create($productData);

        $product = $productModel->one('A', $id);

        $isSaved = $productModel->save(self::PATH_TO_PRODUCTS);

        $this->assertIsInt($id);
        $this->assertIsArray($product);
        $this->assertTrue($isSaved);
    }

    /**
     * @test
     */
    public function update_product_successfully(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $product = $productModel->one('A', 1);

        $updateProductData = [
            'category' => 'A',
            'id' => 1,
            'price' => '5.00',
            'quantity' => 1,
            'name' => 'PRODUCT UNIT TEST'
        ];

        $productModel->update($product, $updateProductData);

        $isSaved = $productModel->save(self::PATH_TO_PRODUCTS);

        $productUpdated = $productModel->one('A', 1);

        $necessaryKeysAndValues = array_intersect_key($updateProductData, $productUpdated);

        $this->assertTrue($isSaved);

        foreach ($necessaryKeysAndValues as $key => $value) {
            $this->assertSame($value, $productUpdated[$key]);
        }
    }

    /**
     * @test
     */
    public function patch_product_successfully(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $product = $productModel->one('A', 1);

        $updateProductData = [
            'category' => 'A',
            'id' => 1,
            'price' => '105.00',
        ];

        $productModel->update($product, $updateProductData, true);

        $isSaved = $productModel->save(self::PATH_TO_PRODUCTS);

        $productUpdated = $productModel->one('A', 1);

        $this->assertTrue($isSaved);
        $this->assertNotEquals($product['price'], $productUpdated['price']);
    }

    /**
     * @test
     */
    public function delete_nonexistent_product(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $this->expectException(RuntimeException::class);

        $productModel->delete('A', 1234);
    }

    /**
     * @test
     */
    public function delete_product_successfully(): void
    {
        $productModel = new Product(self::PATH_TO_PRODUCTS);

        $productModel->delete('A', 2);

        $isSaved = $productModel->save(self::PATH_TO_PRODUCTS);

        $product = $productModel->one('A', 2);

        $this->assertTrue($isSaved);
        $this->assertNull($product);
    }
}
