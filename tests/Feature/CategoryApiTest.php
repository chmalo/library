<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_and_show_categories(): void
    {
        $first = Category::factory()->create(['name' => 'Ficción']);
        Category::factory()->create(['name' => 'Ciencia']);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson("/api/categories/{$first->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $first->id)
            ->assertJsonPath('data.name', 'Ficción');

        $this->get('/api/categories/999999')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_can_create_category(): void
    {
        $this->postJson('/api/categories', ['name' => 'Fantasía'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Fantasía');

        $this->assertDatabaseHas('categories', ['name' => 'Fantasía']);
    }

    public function test_store_category_validates_required_and_unique_name(): void
    {
        Category::factory()->create(['name' => 'Tecnología']);

        $this->postJson('/api/categories', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);

        $this->postJson('/api/categories', ['name' => 'Tecnología'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_update_category_and_validates_unique_name(): void
    {
        $first = Category::factory()->create(['name' => 'Historia']);
        Category::factory()->create(['name' => 'Biografía']);

        $this->putJson("/api/categories/{$first->id}", ['name' => 'Historia Moderna'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Historia Moderna');

        $this->putJson("/api/categories/{$first->id}", ['name' => 'Historia Moderna'])
            ->assertOk();

        $this->putJson("/api/categories/{$first->id}", ['name' => 'Biografía'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_delete_category_without_books(): void
    {
        $category = Category::factory()->create();

        $this->deleteJson("/api/categories/{$category->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        $this->deleteJson('/api/categories/999999')
            ->assertNotFound();
    }

    public function test_cannot_delete_category_with_associated_books(): void
    {
        $category = Category::factory()->create();
        Book::factory()->create(['category_id' => $category->id]);

        $this->deleteJson("/api/categories/{$category->id}")
            ->assertConflict()
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
