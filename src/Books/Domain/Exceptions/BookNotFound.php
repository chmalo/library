<?php

namespace Src\Books\Domain\Exceptions;

use DomainException;

final class BookNotFound extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Book [{$id}] not found.");
    }
}
