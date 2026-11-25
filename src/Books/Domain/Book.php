<?php

namespace Src\Books\Domain;

use DateTimeImmutable;
use Src\Categories\Domain\Category;

final class Book
{
    public function __construct(
        private ?int $id,
        private string $title,
        private string $author,
        private ?string $description,
        private int $publishedYear,
        private int $categoryId,
        private ?Category $category = null,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
    ) {}

    public static function create(
        string $title,
        string $author,
        ?string $description,
        int $publishedYear,
        int $categoryId,
    ): self {
        return new self(
            id: null,
            title: $title,
            author: $author,
            description: $description,
            publishedYear: $publishedYear,
            categoryId: $categoryId,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function publishedYear(): int
    {
        return $this->publishedYear;
    }

    public function categoryId(): int
    {
        return $this->categoryId;
    }

    public function category(): ?Category
    {
        return $this->category;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function update(
        string $title,
        string $author,
        ?string $description,
        int $publishedYear,
        int $categoryId,
    ): void {
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
        $this->publishedYear = $publishedYear;
        $this->categoryId = $categoryId;
    }
}
