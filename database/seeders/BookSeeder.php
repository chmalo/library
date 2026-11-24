<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'name');

        $books = [
            [
                'title' => 'Cien años de soledad',
                'author' => 'Gabriel García Márquez',
                'description' => 'La historia de la familia Buendía en el pueblo ficticio de Macondo.',
                'published_year' => 1967,
                'category' => 'Ficción',
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'description' => 'Novela distópica sobre vigilancia estatal y control del pensamiento.',
                'published_year' => 1949,
                'category' => 'Ficción',
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'description' => 'Principios y prácticas para escribir código legible y mantenible.',
                'published_year' => 2008,
                'category' => 'Tecnología',
            ],
            [
                'title' => 'The Pragmatic Programmer',
                'author' => 'David Thomas & Andrew Hunt',
                'description' => 'Guía práctica de ingeniería de software y desarrollo profesional.',
                'published_year' => 1999,
                'category' => 'Tecnología',
            ],
            [
                'title' => 'Cosmos',
                'author' => 'Carl Sagan',
                'description' => 'Recorrido divulgativo por la astronomía y el lugar humano en el universo.',
                'published_year' => 1980,
                'category' => 'Ciencia',
            ],
            [
                'title' => 'Breve historia del tiempo',
                'author' => 'Stephen Hawking',
                'description' => 'Introducción a la cosmología, los agujeros negros y el origen del universo.',
                'published_year' => 1988,
                'category' => 'Ciencia',
            ],
            [
                'title' => 'Sapiens',
                'author' => 'Yuval Noah Harari',
                'description' => 'Breve historia de la humanidad desde la revolución cognitiva hasta hoy.',
                'published_year' => 2011,
                'category' => 'Historia',
            ],
            [
                'title' => 'Armas, gérmenes y acero',
                'author' => 'Jared Diamond',
                'description' => 'Análisis geográfico e histórico del desarrollo de las civilizaciones.',
                'published_year' => 1997,
                'category' => 'Historia',
            ],
            [
                'title' => 'Steve Jobs',
                'author' => 'Walter Isaacson',
                'description' => 'Biografía autorizada del cofundador de Apple.',
                'published_year' => 2011,
                'category' => 'Biografía',
            ],
            [
                'title' => 'Einstein: Su vida y su universo',
                'author' => 'Walter Isaacson',
                'description' => 'Retrato biográfico del físico teórico Albert Einstein.',
                'published_year' => 2007,
                'category' => 'Biografía',
            ],
        ];

        foreach ($books as $book) {
            Book::query()->firstOrCreate(
                [
                    'title' => $book['title'],
                    'author' => $book['author'],
                ],
                [
                    'description' => $book['description'],
                    'published_year' => $book['published_year'],
                    'category_id' => $categories[$book['category']],
                ]
            );
        }
    }
}
