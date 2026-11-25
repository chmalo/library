<?php

namespace Tests\Unit\Categories;

use PHPUnit\Framework\TestCase;
use Src\Categories\Application\Create\CategoryCreator;
use Src\Categories\Application\Create\CreateCategoryDto;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryAlreadyExists;

class CategoryCreatorTest extends TestCase
{
    public function test_creates_new_category(): void
    {
        $saved = new Category(1, 'Fantasía');
        $repository = $this->createMock(CategoryRepository::class);
        $repository->expects($this->once())->method('existsByName')->with('Fantasía')->willReturn(false);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Category $category): bool => $category->name() === 'Fantasía'))
            ->willReturn($saved);

        $result = (new CategoryCreator($repository))(new CreateCategoryDto('Fantasía'));

        $this->assertSame($saved, $result);
    }

    public function test_throws_when_category_name_already_exists(): void
    {
        $repository = $this->createMock(CategoryRepository::class);
        $repository->expects($this->once())->method('existsByName')->with('Duplicada')->willReturn(true);
        $repository->expects($this->never())->method('save');

        $this->expectException(CategoryAlreadyExists::class);

        (new CategoryCreator($repository))(new CreateCategoryDto('Duplicada'));
    }
}
