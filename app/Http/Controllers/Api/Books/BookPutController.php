<?php

namespace App\Http\Controllers\Api\Books;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use Src\Books\Application\Update\BookUpdater;
use Src\Books\Application\Update\UpdateBookDto;
use Src\Books\Domain\Exceptions\BookNotFound;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

#[OA\Put(
    path: '/api/books/{book}',
    summary: 'Update a book',
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
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/BookInput')
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Book updated',
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
        new OA\Response(
            response: 422,
            description: 'Validation error',
            content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
        ),
    ]
)]
class BookPutController extends Controller
{
    public function __construct(private readonly BookUpdater $updater) {}

    public function __invoke(UpdateBookRequest $request, int $book): BookResource|JsonResponse
    {
        try {
            $validated = $request->validated();

            $updated = ($this->updater)(new UpdateBookDto(
                id: $book,
                title: $validated['title'],
                author: $validated['author'],
                description: $validated['description'] ?? null,
                publishedYear: (int) $validated['published_year'],
                categoryId: (int) $validated['category_id'],
            ));

            return new BookResource($updated);
        } catch (BookNotFound|CategoryNotFound $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
