<?php

namespace Src\Categories\Application\Find;

use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

final readonly class CategoryFinder
{
    public function __construct(private CategoryRepository $repository) {}

    public function __invoke(int $id): Category
    {
        $category = $this->repository->findById($id);

        if ($category === null) {
            throw CategoryNotFound::withId($id);
        }

        return $category;
    }
}
