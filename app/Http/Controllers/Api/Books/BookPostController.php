<?php

namespace App\Http\Controllers\Api\Books;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use Src\Books\Application\Create\BookCreator;
use Src\Books\Application\Create\CreateBookDto;
use Src\Categories\Domain\Exceptions\CategoryNotFound;

#[OA\Post(
    path: '/api/books',
    summary: 'Create a book',
    tags: ['Books'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/BookInput')
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Book created',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', ref: '#/components/schemas/Book'),
                ]
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error',
            content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
        ),
    ]
)]
class BookPostController extends Controller
{
    public function __construct(private readonly BookCreator $creator) {}

    public function __invoke(StoreBookRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $book = ($this->creator)(new CreateBookDto(
                title: $validated['title'],
                author: $validated['author'],
                description: $validated['description'] ?? null,
                publishedYear: (int) $validated['published_year'],
                categoryId: (int) $validated['category_id'],
            ));

            return (new BookResource($book))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (CategoryNotFound $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
