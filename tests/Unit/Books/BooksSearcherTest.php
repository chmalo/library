<?php

namespace Tests\Unit\Books;

use PHPUnit\Framework\TestCase;
use Src\Books\Application\Search\BooksSearcher;
use Src\Books\Domain\Book;
use Src\Books\Domain\BookRepository;

class BooksSearcherTest extends TestCase
{
    public function test_returns_all_books(): void
    {
        $books = [
            new Book(1, 'Clean Code', 'Robert C. Martin', null, 2008, 1),
            new Book(2, 'Sapiens', 'Yuval Noah Harari', null, 2011, 2),
        ];

        $repository = $this->createMock(BookRepository::class);
        $repository->expects($this->once())->method('search')->with(null)->willReturn($books);

        $result = (new BooksSearcher($repository))();

        $this->assertSame($books, $result);
    }

    public function test_filters_books_by_category_id(): void
    {
        $books = [new Book(1, 'Clean Code', 'Robert C. Martin', null, 2008, 3)];

        $repository = $this->createMock(BookRepository::class);
        $repository->expects($this->once())->method('search')->with(3)->willReturn($books);

        $result = (new BooksSearcher($repository))(3);

        $this->assertSame($books, $result);
    }
}
