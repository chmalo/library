<?php

namespace Src\Books\Application\Create;

final readonly class CreateBookDto
{
    public function __construct(
        public string $title,
        public string $author,
        public ?string $description,
        public int $publishedYear,
        public int $categoryId,
    ) {}
}
