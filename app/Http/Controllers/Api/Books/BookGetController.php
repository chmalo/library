<?php

namespace App\Http\Controllers\Api\Books;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use OpenApi\Attributes as OA;

class BookGetController extends Controller
{
    #[OA\Get(
        path: '/api/books/{book}',
        summary: 'Get a book by ID',
        tags: ['Books'],
        parameters: [
            new OA\Parameter(
                name: 'book',
                in: 'path',
                required: true,
                description: 'Book ID',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Book details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Book'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Book not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorMessage')
            ),
        ]
    )]
    public function __invoke(Book $book): BookResource
    {
        return new BookResource($book->load('category'));
    }
}
