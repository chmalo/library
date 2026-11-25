<?php

namespace Tests\Unit\Categories;

use PHPUnit\Framework\TestCase;
use Src\Categories\Application\Find\CategoryFinder;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

class CategoryFinderTest extends TestCase
{
    public function test_returns_category_when_it_exists(): void
    {
        $category = new Category(1, 'Ficción');
        $repository = $this->createMock(CategoryRepository::class);
        $repository->expects($this->once())->method('findById')->with(1)->willReturn($category);

        $result = (new CategoryFinder($repository))(1);

        $this->assertSame($category, $result);
    }

    public function test_throws_when_category_is_missing(): void
    {
        $repository = $this->createMock(CategoryRepository::class);
        $repository->expects($this->once())->method('findById')->with(99)->willReturn(null);

        $this->expectException(CategoryNotFound::class);

        (new CategoryFinder($repository))(99);
    }
}
