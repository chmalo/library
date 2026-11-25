<?php

namespace App\Http\Controllers\Api\Books;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Delete(
    path: '/api/books/{book}',
    summary: 'Delete a book',
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
            response: 204,
            description: 'Book deleted'
        ),
        new OA\Response(
            response: 404,
            description: 'Book not found',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorMessage')
        ),
    ]
)]
class BookDeleteController extends Controller
{
    public function __invoke(Book $book): Response
    {
        $book->delete();

        return response()->noContent();
    }
}
