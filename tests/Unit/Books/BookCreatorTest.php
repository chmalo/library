<?php

namespace Tests\Unit\Books;

use PHPUnit\Framework\TestCase;
use Src\Books\Application\Create\BookCreator;
use Src\Books\Application\Create\CreateBookDto;
use Src\Books\Domain\Book;
use Src\Books\Domain\BookRepository;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

class BookCreatorTest extends TestCase
{
    public function test_creates_new_book(): void
    {
        $category = new Category(1, 'Tecnología');
        $saved = new Book(10, 'Clean Code', 'Robert C. Martin', 'Agile craft.', 2008, 1, $category);

        $categoryRepo = $this->createMock(CategoryRepository::class);
        $categoryRepo->expects($this->once())->method('findById')->with(1)->willReturn($category);

        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Book $book): bool => $book->title() === 'Clean Code' && $book->categoryId() === 1))
            ->willReturn($saved);

        $result = (new BookCreator($bookRepo, $categoryRepo))(new CreateBookDto(
            title: 'Clean Code',
            author: 'Robert C. Martin',
            description: 'Agile craft.',
            publishedYear: 2008,
            categoryId: 1,
        ));

        $this->assertSame($saved, $result);
    }

    public function test_throws_when_category_is_missing(): void
    {
        $categoryRepo = $this->createMock(CategoryRepository::class);
        $categoryRepo->expects($this->once())->method('findById')->with(99)->willReturn(null);

        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->expects($this->never())->method('save');

        $this->expectException(CategoryNotFound::class);

        (new BookCreator($bookRepo, $categoryRepo))(new CreateBookDto(
            title: 'Clean Code',
            author: 'Robert C. Martin',
            description: null,
            publishedYear: 2008,
            categoryId: 99,
        ));
    }
}
