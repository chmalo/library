<?php

namespace Src\Categories\Domain\Exceptions;

use DomainException;

final class CategoryAlreadyExists extends DomainException
{
    public static function withName(string $name): self
    {
        return new self("Category [{$name}] already exists.");
    }
}
