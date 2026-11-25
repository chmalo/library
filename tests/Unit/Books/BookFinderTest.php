<?php

namespace Tests\Unit\Books;

use PHPUnit\Framework\TestCase;
use Src\Books\Application\Find\BookFinder;
use Src\Books\Domain\Book;
use Src\Books\Domain\BookRepository;
use Src\Books\Domain\Exceptions\BookNotFound;

class BookFinderTest extends TestCase
{
    public function test_returns_book_when_it_exists(): void
    {
        $book = new Book(1, 'Clean Code', 'Robert C. Martin', null, 2008, 1);

        $repository = $this->createMock(BookRepository::class);
        $repository->expects($this->once())->method('findById')->with(1)->willReturn($book);

        $result = (new BookFinder($repository))(1);

        $this->assertSame($book, $result);
    }

    public function test_throws_when_book_is_missing(): void
    {
        $repository = $this->createMock(BookRepository::class);
        $repository->expects($this->once())->method('findById')->with(99)->willReturn(null);

        $this->expectException(BookNotFound::class);

        (new BookFinder($repository))(99);
    }
}
