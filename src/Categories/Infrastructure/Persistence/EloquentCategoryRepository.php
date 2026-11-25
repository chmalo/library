<?php

namespace Src\Categories\Infrastructure\Persistence;

use App\Models\Category as EloquentCategory;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;

final class EloquentCategoryRepository implements CategoryRepository
{
    public function findAll(): array
    {
        return EloquentCategory::query()
            ->orderBy('id')
            ->get()
            ->map(fn (EloquentCategory $model): Category => self::toDomain($model))
            ->all();
    }

    public function findById(int $id): ?Category
    {
        $model = EloquentCategory::query()->find($id);

        return $model ? self::toDomain($model) : null;
    }

    public function existsByName(string $name, ?int $ignoreId = null): bool
    {
        return EloquentCategory::query()
            ->where('name', $name)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }

    public function save(Category $category): Category
    {
        if ($category->id() === null) {
            $model = EloquentCategory::query()->create([
                'name' => $category->name(),
            ]);

            return self::toDomain($model->refresh());
        }

        $model = EloquentCategory::query()->findOrFail($category->id());
        $model->update([
            'name' => $category->name(),
        ]);

        return self::toDomain($model->refresh());
    }

    public function delete(int $id): void
    {
        EloquentCategory::query()->where('id', $id)->delete();
    }

    public static function toDomain(EloquentCategory $model): Category
    {
        return new Category(
            id: $model->id,
            name: $model->name,
            createdAt: $model->created_at?->toDateTimeImmutable(),
            updatedAt: $model->updated_at?->toDateTimeImmutable(),
        );
    }
}
