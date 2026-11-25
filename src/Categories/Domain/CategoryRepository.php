<?php

namespace Src\Categories\Domain;

interface CategoryRepository
{
    public function findAll(): array;

    public function findById(int $id): ?Category;

    public function existsByName(string $name, ?int $ignoreId = null): bool;

    public function save(Category $category): Category;

    public function delete(int $id): void;
}
