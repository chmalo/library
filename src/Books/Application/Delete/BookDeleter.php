<?php

namespace Src\Books\Application\Delete;

use Src\Books\Domain\BookRepository;
use Src\Books\Domain\Exceptions\BookNotFound;

final readonly class BookDeleter
{
    public function __construct(private BookRepository $repository) {}

    public function __invoke(int $id): void
    {
        if ($this->repository->findById($id) === null) {
            throw BookNotFound::withId($id);
        }

        $this->repository->delete($id);
    }
}
