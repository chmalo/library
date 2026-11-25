<?php

namespace Tests\Unit\Categories;

use PHPUnit\Framework\TestCase;
use Src\Books\Domain\BookRepository;
use Src\Categories\Application\Delete\CategoryDeleter;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryHasBooks;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

class CategoryDeleterTest extends TestCase
{
    public function test_deletes_category_without_books(): void
    {
        $categoryRepo = $this->createMock(CategoryRepository::class);
        $categoryRepo->expects($this->once())->method('findById')->with(1)->willReturn(new Category(1, 'Vacía'));
        $categoryRepo->expects($this->once())->method('delete')->with(1);

        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->expects($this->once())->method('existsByCategoryId')->with(1)->willReturn(false);

        (new CategoryDeleter($categoryRepo, $bookRepo))(1);
    }

    public function test_throws_when_category_is_missing(): void
    {
        $categoryRepo = $this->createMock(CategoryRepository::class);
        $categoryRepo->expects($this->once())->method('findById')->with(99)->willReturn(null);
        $categoryRepo->expects($this->never())->method('delete');

        $bookRepo = $this->createMock(BookRepository::class);

        $this->expectException(CategoryNotFound::class);

        (new CategoryDeleter($categoryRepo, $bookRepo))(99);
    }

    public function test_throws_when_category_has_associated_books(): void
    {
        $categoryRepo = $this->createMock(CategoryRepository::class);
        $categoryRepo->expects($this->once())->method('findById')->with(2)->willReturn(new Category(2, 'Con Libros'));
        $categoryRepo->expects($this->never())->method('delete');

        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->expects($this->once())->method('existsByCategoryId')->with(2)->willReturn(true);

        $this->expectException(CategoryHasBooks::class);

        (new CategoryDeleter($categoryRepo, $bookRepo))(2);
    }
}
