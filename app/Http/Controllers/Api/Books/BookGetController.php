<?php

namespace App\Http\Controllers\Api\Books;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use Src\Books\Application\Find\BookFinder;
use Src\Books\Domain\Exceptions\BookNotFound;

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
class BookGetController extends Controller
{
    public function __construct(private readonly BookFinder $finder) {}

    public function __invoke(int $book): BookResource|JsonResponse
    {
        try {
            return new BookResource(($this->finder)($book));
        } catch (BookNotFound $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
