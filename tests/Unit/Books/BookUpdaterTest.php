<?php

namespace Tests\Unit\Books;

use PHPUnit\Framework\TestCase;
use Src\Books\Application\Update\BookUpdater;
use Src\Books\Application\Update\UpdateBookDto;
use Src\Books\Domain\Book;
use Src\Books\Domain\BookRepository;
use Src\Books\Domain\Exceptions\BookNotFound;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

class BookUpdaterTest extends TestCase
{
    public function test_updates_existing_book(): void
    {
        $existing = new Book(1, 'Old Title', 'Old Author', null, 2000, 1);
        $newCategory = new Category(2, 'Arquitectura');

        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->expects($this->once())->method('findById')->with(1)->willReturn($existing);
        $bookRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Book $book): bool => $book->id() === 1 && $book->title() === 'Refactoring' && $book->categoryId() === 2))
            ->willReturn($existing);

        $categoryRepo = $this->createMock(CategoryRepository::class);
        $categoryRepo->expects($this->once())->method('findById')->with(2)->willReturn($newCategory);

        $result = (new BookUpdater($bookRepo, $categoryRepo))(new UpdateBookDto(
            id: 1,
            title: 'Refactoring',
            author: 'Martin Fowler',
            description: 'Improving design.',
            publishedYear: 2018,
            categoryId: 2,
        ));

        $this->assertSame('Refactoring', $result->title());
        $this->assertSame(2, $result->categoryId());
    }

    public function test_throws_when_book_is_missing(): void
    {
        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->expects($this->once())->method('findById')->with(99)->willReturn(null);
        $bookRepo->expects($this->never())->method('save');

        $categoryRepo = $this->createMock(CategoryRepository::class);
        $categoryRepo->expects($this->never())->method('findById');

        $this->expectException(BookNotFound::class);

        (new BookUpdater($bookRepo, $categoryRepo))(new UpdateBookDto(99, 'Refactoring', 'Martin Fowler', null, 2018, 1));
    }

    public function test_throws_when_category_is_missing(): void
    {
        $existing = new Book(1, 'Clean Code', 'Robert C. Martin', null, 2008, 1);

        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->expects($this->once())->method('findById')->with(1)->willReturn($existing);
        $bookRepo->expects($this->never())->method('save');

        $categoryRepo = $this->createMock(CategoryRepository::class);
        $categoryRepo->expects($this->once())->method('findById')->with(99)->willReturn(null);

        $this->expectException(CategoryNotFound::class);

        (new BookUpdater($bookRepo, $categoryRepo))(new UpdateBookDto(1, 'Clean Code', 'Robert C. Martin', null, 2008, 99));
    }
}
