<?php

namespace Src\Books\Infrastructure\Persistence;

use App\Models\Book as EloquentBook;
use Src\Books\Domain\Book;
use Src\Books\Domain\BookRepository;
use Src\Categories\Infrastructure\Persistence\EloquentCategoryRepository;

final class EloquentBookRepository implements BookRepository
{
    public function search(?int $categoryId = null): array
    {
        return EloquentBook::query()
            ->with('category')
            ->when($categoryId !== null, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('id')
            ->get()
            ->map(fn (EloquentBook $model): Book => self::toDomain($model))
            ->all();
    }

    public function findById(int $id): ?Book
    {
        $model = EloquentBook::query()->with('category')->find($id);

        return $model ? self::toDomain($model) : null;
    }

    public function existsByCategoryId(int $categoryId): bool
    {
        return EloquentBook::query()
            ->where('category_id', $categoryId)
            ->exists();
    }

    public function save(Book $book): Book
    {
        $attributes = [
            'title' => $book->title(),
            'author' => $book->author(),
            'description' => $book->description(),
            'published_year' => $book->publishedYear(),
            'category_id' => $book->categoryId(),
        ];

        if ($book->id() === null) {
            $model = EloquentBook::query()->create($attributes);
            $model->load('category');

            return self::toDomain($model);
        }

        $model = EloquentBook::query()->findOrFail($book->id());
        $model->update($attributes);
        $model->refresh()->load('category');

        return self::toDomain($model);
    }

    public function delete(int $id): void
    {
        EloquentBook::query()->where('id', $id)->delete();
    }

    private static function toDomain(EloquentBook $model): Book
    {
        return new Book(
            id: $model->id,
            title: $model->title,
            author: $model->author,
            description: $model->description,
            publishedYear: $model->published_year,
            categoryId: $model->category_id,
            category: $model->relationLoaded('category') && $model->category
                ? EloquentCategoryRepository::toDomain($model->category)
                : null,
            createdAt: $model->created_at?->toDateTimeImmutable(),
            updatedAt: $model->updated_at?->toDateTimeImmutable(),
        );
    }
}
