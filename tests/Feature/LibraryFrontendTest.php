<?php

namespace Tests\Feature;

use Tests\TestCase;

class LibraryFrontendTest extends TestCase
{
    public function test_home_page_renders_library_frontend(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertViewIs('library')
            ->assertSee('/css/library.css', false)
            ->assertSee('/js/library.js', false)
            ->assertSee('/api/documentation', false);
    }
}
