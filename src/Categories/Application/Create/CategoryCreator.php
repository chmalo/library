<?php

namespace Src\Categories\Application\Create;

use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryAlreadyExists;

final readonly class CategoryCreator
{
    public function __construct(private CategoryRepository $repository) {}

    public function __invoke(CreateCategoryDto $dto): Category
    {
        if ($this->repository->existsByName($dto->name)) {
            throw CategoryAlreadyExists::withName($dto->name);
        }

        return $this->repository->save(Category::create($dto->name));
    }
}
