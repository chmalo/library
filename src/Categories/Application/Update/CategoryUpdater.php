<?php

namespace Src\Categories\Application\Update;

use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryAlreadyExists;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

final readonly class CategoryUpdater
{
    public function __construct(private CategoryRepository $repository) {}

    public function __invoke(UpdateCategoryDto $dto): Category
    {
        $category = $this->repository->findById($dto->id);

        if ($category === null) {
            throw CategoryNotFound::withId($dto->id);
        }

        if ($this->repository->existsByName($dto->name, $dto->id)) {
            throw CategoryAlreadyExists::withName($dto->name);
        }

        $category->rename($dto->name);

        return $this->repository->save($category);
    }
}
