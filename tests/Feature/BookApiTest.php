<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_and_filter_books_by_category(): void
    {
        $first = Category::factory()->create();
        $second = Category::factory()->create();
        Book::factory()->count(2)->create(['category_id' => $first->id]);
        Book::factory()->create(['category_id' => $second->id]);

        $this->getJson('/api/books')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'category' => ['id', 'name']]]]);

        $this->getJson("/api/books?category={$first->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/books?category=999999')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_can_show_book_and_returns_not_found_for_missing_book(): void
    {
        $book = Book::factory()->create();

        $this->getJson("/api/books/{$book->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $book->id)
            ->assertJsonPath('data.category.id', $book->category_id);

        $this->get('/api/books/999999')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_can_create_book(): void
    {
        $category = Category::factory()->create();

        $this->postJson('/api/books', [
            'title' => 'Domain-Driven Design',
            'author' => 'Eric Evans',
            'description' => 'Tackling complexity in the heart of software.',
            'published_year' => 2003,
            'category_id' => $category->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Domain-Driven Design')
            ->assertJsonPath('data.category.id', $category->id);

        $this->assertDatabaseHas('books', ['title' => 'Domain-Driven Design']);
    }

    public function test_store_book_validates_fields(): void
    {
        $this->postJson('/api/books', [
            'title' => 'Ab',
            'author' => '',
            'published_year' => (int) date('Y') + 1,
            'category_id' => 999999,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'author', 'published_year', 'category_id']);
    }

    public function test_can_update_book_and_handles_invalid_or_missing_book(): void
    {
        $book = Book::factory()->create();
        $newCategory = Category::factory()->create();

        $this->putJson("/api/books/{$book->id}", [
            'title' => 'Refactoring',
            'author' => 'Martin Fowler',
            'description' => null,
            'published_year' => 2018,
            'category_id' => $newCategory->id,
        ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Refactoring')
            ->assertJsonPath('data.category.id', $newCategory->id);

        $this->putJson("/api/books/{$book->id}", ['title' => 'No'])
            ->assertUnprocessable();

        $this->putJson('/api/books/999999', [
            'title' => 'Refactoring',
            'author' => 'Martin Fowler',
            'published_year' => 2018,
            'category_id' => $newCategory->id,
        ])->assertNotFound();
    }

    public function test_can_delete_book_and_verifies_existence(): void
    {
        $book = Book::factory()->create();

        $this->deleteJson("/api/books/{$book->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('books', ['id' => $book->id]);

        $this->deleteJson('/api/books/999999')
            ->assertNotFound();
    }
}
