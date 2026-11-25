<?php

namespace Src\Categories\Domain\Exceptions;

use DomainException;

final class CategoryNotFound extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Category [{$id}] not found.");
    }
}
