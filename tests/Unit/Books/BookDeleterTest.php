<?php

namespace Tests\Unit\Books;

use PHPUnit\Framework\TestCase;
use Src\Books\Application\Delete\BookDeleter;
use Src\Books\Domain\Book;
use Src\Books\Domain\BookRepository;
use Src\Books\Domain\Exceptions\BookNotFound;

class BookDeleterTest extends TestCase
{
    public function test_deletes_existing_book(): void
    {
        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->expects($this->once())->method('findById')->with(1)->willReturn(new Book(1, 'Clean Code', 'Robert C. Martin', null, 2008, 1));
        $bookRepo->expects($this->once())->method('delete')->with(1);

        (new BookDeleter($bookRepo))(1);
    }

    public function test_throws_when_book_is_missing(): void
    {
        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->expects($this->once())->method('findById')->with(99)->willReturn(null);
        $bookRepo->expects($this->never())->method('delete');

        $this->expectException(BookNotFound::class);

        (new BookDeleter($bookRepo))(99);
    }
}
