<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class BookController extends Controller
{
    #[OA\Get(
        path: '/api/books',
        summary: 'List books or filter by category',
        tags: ['Books'],
        parameters: [
            new OA\Parameter(
                name: 'category',
                in: 'query',
                required: false,
                description: 'Filter books by category ID',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of books',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Book')
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Book::query()->with('category')->orderBy('id');
        $categoryId = $request->query('category');

        if ($categoryId !== null && $categoryId !== '') {
            if (! ctype_digit((string) $categoryId)) {
                return BookResource::collection(collect());
            }

            $query->where('category_id', (int) $categoryId);
        }

        return BookResource::collection($query->get());
    }

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
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = Book::query()->create($request->validated())->load('category');

        return (new BookResource($book))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

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
    public function show(Book $book): BookResource
    {
        return new BookResource($book->load('category'));
    }

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
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $book->update($request->validated());

        return new BookResource($book->load('category'));
    }

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
    public function destroy(Book $book): Response
    {
        $book->delete();

        return response()->noContent();
    }
}
