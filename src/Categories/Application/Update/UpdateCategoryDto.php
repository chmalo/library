<?php

namespace Src\Categories\Application\Update;

final readonly class UpdateCategoryDto
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
