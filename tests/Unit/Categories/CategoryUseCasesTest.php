<?php

namespace Tests\Unit\Categories;

use PHPUnit\Framework\TestCase;
use Src\Books\Domain\BookRepository;
use Src\Categories\Application\Create\CategoryCreator;
use Src\Categories\Application\Create\CreateCategoryDto;
use Src\Categories\Application\Delete\CategoryDeleter;
use Src\Categories\Application\Find\CategoryFinder;
use Src\Categories\Application\Search\CategoriesSearcher;
use Src\Categories\Application\Update\CategoryUpdater;
use Src\Categories\Application\Update\UpdateCategoryDto;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryAlreadyExists;
use Src\Categories\Domain\Exceptions\CategoryHasBooks;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

class CategoryUseCasesTest extends TestCase
{
    public function test_searcher_returns_all_categories(): void
    {
        $categories = [new Category(1, 'Ficción'), new Category(2, 'Ciencia')];
        $repository = $this->createMock(CategoryRepository::class);
        $repository->expects($this->once())->method('findAll')->willReturn($categories);

        $result = (new CategoriesSearcher($repository))();

        $this->assertSame($categories, $result);
    }

    public function test_finder_returns_category_and_throws_when_missing(): void
    {
        $category = new Category(1, 'Ficción');
        $repository = $this->createMock(CategoryRepository::class);
        $repository->method('findById')->willReturnMap([
            [1, $category],
            [99, null],
        ]);

        $finder = new CategoryFinder($repository);

        $this->assertSame($category, $finder(1));

        $this->expectException(CategoryNotFound::class);
        $finder(99);
    }

    public function test_creator_saves_category_and_rejects_duplicate_name(): void
    {
        $saved = new Category(1, 'Fantasía');
        $repository = $this->createMock(CategoryRepository::class);
        $repository->method('existsByName')->willReturnMap([
            ['Fantasía', null, false],
            ['Duplicada', null, true],
        ]);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Category $category): bool => $category->name() === 'Fantasía'))
            ->willReturn($saved);

        $creator = new CategoryCreator($repository);

        $this->assertSame($saved, $creator(new CreateCategoryDto('Fantasía')));

        $this->expectException(CategoryAlreadyExists::class);
        $creator(new CreateCategoryDto('Duplicada'));
    }

    public function test_updater_renames_category_and_rejects_missing_or_duplicate_name(): void
    {
        $existing = new Category(1, 'Historia');
        $repository = $this->createMock(CategoryRepository::class);
        $repository->method('findById')->willReturnMap([
            [1, $existing],
            [99, null],
        ]);
        $repository->method('existsByName')->willReturnMap([
            ['Historia Moderna', 1, false],
            ['Biografía', 1, true],
        ]);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Category $category): bool => $category->id() === 1 && $category->name() === 'Historia Moderna'))
            ->willReturn($existing);

        $updater = new CategoryUpdater($repository);

        $this->assertSame('Historia Moderna', $updater(new UpdateCategoryDto(1, 'Historia Moderna'))->name());

        try {
            $updater(new UpdateCategoryDto(99, 'Nada'));
            $this->fail('Expected CategoryNotFound exception.');
        } catch (CategoryNotFound) {
        }

        $this->expectException(CategoryAlreadyExists::class);
        $updater(new UpdateCategoryDto(1, 'Biografía'));
    }

    public function test_deleter_deletes_empty_category_and_rejects_missing_or_category_with_books(): void
    {
        $category = new Category(1, 'Vacía');
        $categoryRepo = $this->createMock(CategoryRepository::class);
        $categoryRepo->method('findById')->willReturnMap([
            [1, $category],
            [2, new Category(2, 'Con Libros')],
            [99, null],
        ]);
        $categoryRepo->expects($this->once())->method('delete')->with(1);

        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->method('existsByCategoryId')->willReturnMap([
            [1, false],
            [2, true],
        ]);

        $deleter = new CategoryDeleter($categoryRepo, $bookRepo);

        $deleter(1);

        try {
            $deleter(99);
            $this->fail('Expected CategoryNotFound exception.');
        } catch (CategoryNotFound) {
        }

        $this->expectException(CategoryHasBooks::class);
        $deleter(2);
    }
}
