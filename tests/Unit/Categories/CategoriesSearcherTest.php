<?php

namespace Tests\Unit\Categories;

use PHPUnit\Framework\TestCase;
use Src\Categories\Application\Search\CategoriesSearcher;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;

class CategoriesSearcherTest extends TestCase
{
    public function test_returns_all_categories(): void
    {
        $categories = [new Category(1, 'Ficción'), new Category(2, 'Ciencia')];
        $repository = $this->createMock(CategoryRepository::class);
        $repository->expects($this->once())->method('findAll')->willReturn($categories);

        $result = (new CategoriesSearcher($repository))();

        $this->assertSame($categories, $result);
    }
}
