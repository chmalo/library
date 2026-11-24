<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiBaseTest extends TestCase
{
    public function test_health_endpoint_returns_ok_json(): void
    {
        $this->get('/api/health')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    public function test_unknown_api_route_returns_json_not_found(): void
    {
        $this->get('/api/does-not-exist')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure(['message']);
    }

    public function test_swagger_docs_generate_and_ui_is_accessible(): void
    {
        $this->artisan('l5-swagger:generate')->assertSuccessful();

        $this->get('/api/documentation')->assertOk();

        $this->get('/docs')
            ->assertOk()
            ->assertJsonStructure([
                'paths' => [
                    '/api/health',
                    '/api/categories',
                    '/api/categories/{category}',
                    '/api/books',
                    '/api/books/{book}',
                ],
            ]);
    }
}
