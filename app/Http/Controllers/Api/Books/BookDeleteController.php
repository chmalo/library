<?php

namespace App\Http\Controllers\Api\Books;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use Src\Books\Application\Delete\BookDeleter;
use Src\Books\Domain\Exceptions\BookNotFound;

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
    public function __construct(private readonly BookDeleter $deleter) {}

    public function __invoke(int $book): Response|JsonResponse
    {
        try {
            ($this->deleter)($book);

            return response()->noContent();
        } catch (BookNotFound $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
