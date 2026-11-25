<?php

namespace Src\Categories\Domain\Exceptions;

use DomainException;

final class CategoryHasBooks extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Cannot delete category [{$id}] with associated books.");
    }
}
