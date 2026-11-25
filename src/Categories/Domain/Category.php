<?php

namespace Src\Categories\Domain;

use DateTimeImmutable;

final class Category
{
    public function __construct(
        private ?int $id,
        private string $name,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
    ) {}

    public static function create(string $name): self
    {
        return new self(
            id: null,
            name: $name,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }
}
