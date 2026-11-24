<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSchemaAndModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_and_book_relationships_work(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($book->category->is($category));
        $this->assertTrue($category->books->contains($book));
        $this->assertIsInt($book->fresh()->published_year);
    }

    public function test_category_name_must_be_unique(): void
    {
        Category::factory()->create(['name' => 'Tecnología']);

        $this->expectException(QueryException::class);

        Category::factory()->create(['name' => 'Tecnología']);
    }

    public function test_database_seeder_populates_categories_and_books_idempotently(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(5, Category::count());
        $this->assertSame(10, Book::count());
    }
}
