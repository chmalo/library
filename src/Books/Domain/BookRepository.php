<?php

namespace Src\Books\Domain;

interface BookRepository
{
    public function search(?int $categoryId = null): array;

    public function findById(int $id): ?Book;

    public function existsByCategoryId(int $categoryId): bool;

    public function save(Book $book): Book;

    public function delete(int $id): void;
}
