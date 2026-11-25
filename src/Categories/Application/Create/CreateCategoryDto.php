<?php

namespace Src\Categories\Application\Create;

final readonly class CreateCategoryDto
{
    public function __construct(public string $name) {}
}
