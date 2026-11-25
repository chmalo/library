<?php

namespace Src\Books\Application\Update;

use Src\Books\Domain\Book;
use Src\Books\Domain\BookRepository;
use Src\Books\Domain\Exceptions\BookNotFound;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

final readonly class BookUpdater
{
    public function __construct(
        private BookRepository $bookRepository,
        private CategoryRepository $categoryRepository,
    ) {}

    public function __invoke(UpdateBookDto $dto): Book
    {
        $book = $this->bookRepository->findById($dto->id);

        if ($book === null) {
            throw BookNotFound::withId($dto->id);
        }

        if ($this->categoryRepository->findById($dto->categoryId) === null) {
            throw CategoryNotFound::withId($dto->categoryId);
        }

        $book->update(
            title: $dto->title,
            author: $dto->author,
            description: $dto->description,
            publishedYear: $dto->publishedYear,
            categoryId: $dto->categoryId,
        );

        return $this->bookRepository->save($book);
    }
}
