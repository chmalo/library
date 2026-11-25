<?php

namespace Tests\Unit\Categories;

use PHPUnit\Framework\TestCase;
use Src\Categories\Application\Update\CategoryUpdater;
use Src\Categories\Application\Update\UpdateCategoryDto;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryAlreadyExists;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

class CategoryUpdaterTest extends TestCase
{
    public function test_renames_existing_category(): void
    {
        $existing = new Category(1, 'Historia');
        $repository = $this->createMock(CategoryRepository::class);
        $repository->expects($this->once())->method('findById')->with(1)->willReturn($existing);
        $repository->expects($this->once())->method('existsByName')->with('Historia Moderna', 1)->willReturn(false);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Category $category): bool => $category->id() === 1 && $category->name() === 'Historia Moderna'))
            ->willReturn($existing);

        $result = (new CategoryUpdater($repository))(new UpdateCategoryDto(1, 'Historia Moderna'));

        $this->assertSame('Historia Moderna', $result->name());
    }

    public function test_throws_when_category_to_update_is_missing(): void
    {
        $repository = $this->createMock(CategoryRepository::class);
        $repository->expects($this->once())->method('findById')->with(99)->willReturn(null);
        $repository->expects($this->never())->method('save');

        $this->expectException(CategoryNotFound::class);

        (new CategoryUpdater($repository))(new UpdateCategoryDto(99, 'Nada'));
    }

    public function test_throws_when_new_name_belongs_to_another_category(): void
    {
        $repository = $this->createMock(CategoryRepository::class);
        $repository->expects($this->once())->method('findById')->with(1)->willReturn(new Category(1, 'Historia'));
        $repository->expects($this->once())->method('existsByName')->with('Biografía', 1)->willReturn(true);
        $repository->expects($this->never())->method('save');

        $this->expectException(CategoryAlreadyExists::class);

        (new CategoryUpdater($repository))(new UpdateCategoryDto(1, 'Biografía'));
    }
}
