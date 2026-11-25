<?php

namespace Src\Categories\Application\Delete;

use Src\Books\Domain\BookRepository;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryHasBooks;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

final readonly class CategoryDeleter
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private BookRepository $bookRepository,
    ) {}

    public function __invoke(int $id): void
    {
        if ($this->categoryRepository->findById($id) === null) {
            throw CategoryNotFound::withId($id);
        }

        if ($this->bookRepository->existsByCategoryId($id)) {
            throw CategoryHasBooks::withId($id);
        }

        $this->categoryRepository->delete($id);
    }
}
