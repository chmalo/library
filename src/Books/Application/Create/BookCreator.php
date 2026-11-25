<?php

namespace Src\Books\Application\Create;

use Src\Books\Domain\Book;
use Src\Books\Domain\BookRepository;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

final readonly class BookCreator
{
    public function __construct(
        private BookRepository $bookRepository,
        private CategoryRepository $categoryRepository,
    ) {}

    public function __invoke(CreateBookDto $dto): Book
    {
        if ($this->categoryRepository->findById($dto->categoryId) === null) {
            throw CategoryNotFound::withId($dto->categoryId);
        }

        return $this->bookRepository->save(Book::create(
            title: $dto->title,
            author: $dto->author,
            description: $dto->description,
            publishedYear: $dto->publishedYear,
            categoryId: $dto->categoryId,
        ));
    }
}
