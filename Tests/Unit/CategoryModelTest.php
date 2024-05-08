<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use RuntimeException;
use App\Models\Category;
use PHPUnit\Framework\TestCase;

class CategoryModelTest extends TestCase
{
    private const PATH_TO_PRODUCTS = __DIR__ . '/../../backup_products.json';

    private const CATEGORY_NAME = 'XYZ';

    private const CATEGORY_NAME_TO_UPDATE = 'ZYX';

    private const CATEGORY_NOT_EXISTING_NAME = 'ABC';

    /**
     * @test
     */
    public function wrong_path_when_create_model(): void
    {
        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('Failed to open stream: No such file or directory');

        new Category('../models.wrongs.path');
    }

    /**
     * @test
     */
    public function all_categories_are_returned(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $categories = $categoryModel->all();

        $this->assertNotNull($categories);
    }

    /**
     * @test
     */
    public function array_is_returned_for_existing_category(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $category = $categoryModel->one('A');

        $this->assertIsArray($category);
    }

    /**
     * @test
     */
    public function null_is_returned_for_non_existing_category(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $category = $categoryModel->one('MADE_UP_CATEGORY');

        $this->assertNull($category);
    }

    /**
     * @test
     */
    public function create_new_category(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $requestData = ['category' => self::CATEGORY_NAME];

        $categoryModel->create($requestData);

        $isSaved = $categoryModel->save(self::PATH_TO_PRODUCTS);

        $categoryCreated = $categoryModel->one(self::CATEGORY_NAME);

        $this->assertTrue($isSaved);

        $this->assertIsArray($categoryCreated);

        $this->assertEmpty($categoryCreated);
    }

    /**
     * @test
     */
    public function not_allowed_create_existing_category(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $requestData = ['category' => self::CATEGORY_NAME];

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('Can\'t create, category already exists');

        $categoryModel->create($requestData);
    }

    /**
     * @test
     */
    public function not_allowed_create_category_with_missing_category_key(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $requestData = ['product' => self::CATEGORY_NAME];

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('Missing `category` key in the array');

        $categoryModel->create($requestData);
    }

    /**
     * @test
     */
    public function update_category_name(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $requestData = [
            'category' => self::CATEGORY_NAME,
            'newCategory' => self::CATEGORY_NAME_TO_UPDATE,
        ];

        $categoryModel->update($requestData);

        $isSaved = $categoryModel->save(self::PATH_TO_PRODUCTS);

        $this->assertTrue($isSaved);
    }

    /**
     * @test
     */
    public function attempt_update_category_name_with_missing_keys(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $requestData = [
            'products' => self::CATEGORY_NAME,
            'newProducts' => self::CATEGORY_NAME_TO_UPDATE,
        ];

        $this->expectException(RuntimeException::class);
        
        $categoryModel->update($requestData);
    }

    /**
     * @test
     */
    public function update_not_existing_category(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $requestData = [
            'category' => self::CATEGORY_NOT_EXISTING_NAME,
            'newCategory' => self::CATEGORY_NAME_TO_UPDATE,
        ];

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage("Category `{$requestData['category']}` does not exist");

        $categoryModel->update($requestData);
    }

    /**
     * @test
     */
    public function delete_empty_category(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $categoryModel->delete(self::CATEGORY_NAME_TO_UPDATE);

        $isSaved = $categoryModel->save(self::PATH_TO_PRODUCTS);

        $category = $categoryModel->one(self::CATEGORY_NAME_TO_UPDATE);

        $this->assertTrue($isSaved);

        $this->assertNull($category);
    }

    /**
     * @test
     */
    public function attempt_delete_category_with_products(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('Can\'t delete category as it contains products');

        $categoryModel->delete('A');
    }

    /**
     * @test
     */
    public function attempt_delete_category_which_not_exist(): void
    {
        $categoryModel = new Category(self::PATH_TO_PRODUCTS);

        $categoryToDelete = self::CATEGORY_NAME_TO_UPDATE;

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage("Category `{$categoryToDelete}` does not exist");

        $categoryModel->delete($categoryToDelete);
    }
}
