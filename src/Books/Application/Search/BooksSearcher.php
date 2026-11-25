<?php

namespace Src\Books\Application\Search;

use Src\Books\Domain\BookRepository;

final readonly class BooksSearcher
{
    public function __construct(private BookRepository $repository) {}

    public function __invoke(?int $categoryId = null): array
    {
        return $this->repository->search($categoryId);
    }
}
