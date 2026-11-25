<?php

namespace Src\Books\Application\Find;

use Src\Books\Domain\Book;
use Src\Books\Domain\BookRepository;
use Src\Books\Domain\Exceptions\BookNotFound;

final readonly class BookFinder
{
    public function __construct(private BookRepository $repository) {}

    public function __invoke(int $id): Book
    {
        $book = $this->repository->findById($id);

        if ($book === null) {
            throw BookNotFound::withId($id);
        }

        return $book;
    }
}
