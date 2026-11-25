<?php

namespace Src\Categories\Application\Search;

use Src\Categories\Domain\CategoryRepository;

final readonly class CategoriesSearcher
{
    public function __construct(private CategoryRepository $repository) {}

    public function __invoke(): array
    {
        return $this->repository->findAll();
    }
}
