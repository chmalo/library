<?php

namespace Src\Books\Application\Update;

final readonly class UpdateBookDto
{
    public function __construct(
        public int $id,
        public string $title,
        public string $author,
        public ?string $description,
        public int $publishedYear,
        public int $categoryId,
    ) {}
}
