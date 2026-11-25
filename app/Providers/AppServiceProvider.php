<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Books\Domain\BookRepository;
use Src\Books\Infrastructure\Persistence\EloquentBookRepository;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Infrastructure\Persistence\EloquentCategoryRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CategoryRepository::class, EloquentCategoryRepository::class);
        $this->app->bind(BookRepository::class, EloquentBookRepository::class);
    }

    public function boot(): void {}
}
