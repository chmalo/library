<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Books\Domain\Book;
use Src\Books\Domain\BookRepository;
use Src\Books\Infrastructure\Persistence\EloquentBookRepository;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Infrastructure\Persistence\EloquentCategoryRepository;
use Tests\TestCase;

class EloquentRepositoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_container_resolves_repository_interfaces_to_eloquent_implementations(): void
    {
        $this->assertInstanceOf(EloquentCategoryRepository::class, app(CategoryRepository::class));
        $this->assertInstanceOf(EloquentBookRepository::class, app(BookRepository::class));
    }

    public function test_eloquent_category_repository_crud_and_name_check(): void
    {
        $repository = app(CategoryRepository::class);

        $created = $repository->save(Category::create('Ficción'));

        $this->assertNotNull($created->id());
        $this->assertSame('Ficción', $created->name());
        $this->assertTrue($repository->existsByName('Ficción'));
        $this->assertFalse($repository->existsByName('Ficción', $created->id()));

        $created->rename('Ciencia Ficción');
        $updated = $repository->save($created);

        $this->assertSame('Ciencia Ficción', $repository->findById($updated->id())?->name());
        $this->assertCount(1, $repository->findAll());

        $repository->delete($updated->id());

        $this->assertNull($repository->findById($updated->id()));
    }

    public function test_eloquent_book_repository_crud_filtering_and_category_check(): void
    {
        $categories = app(CategoryRepository::class);
        $books = app(BookRepository::class);

        $firstCategory = $categories->save(Category::create('Tecnología'));
        $secondCategory = $categories->save(Category::create('Historia'));

        $book = $books->save(Book::create(
            title: 'Clean Code',
            author: 'Robert C. Martin',
            description: 'Agile craftsmanship.',
            publishedYear: 2008,
            categoryId: $firstCategory->id(),
        ));

        $books->save(Book::create(
            title: 'Sapiens',
            author: 'Yuval Noah Harari',
            description: null,
            publishedYear: 2011,
            categoryId: $secondCategory->id(),
        ));

        $this->assertTrue($books->existsByCategoryId($firstCategory->id()));
        $this->assertCount(2, $books->search());
        $this->assertCount(1, $books->search($firstCategory->id()));
        $this->assertSame('Tecnología', $books->findById($book->id())?->category()?->name());

        $book->update('Clean Architecture', 'Robert C. Martin', null, 2017, $firstCategory->id());
        $updated = $books->save($book);

        $this->assertSame('Clean Architecture', $updated->title());

        $books->delete($updated->id());

        $this->assertNull($books->findById($updated->id()));
        $this->assertFalse($books->existsByCategoryId($firstCategory->id()));
    }
}
