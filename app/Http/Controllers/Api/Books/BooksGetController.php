<?php

namespace App\Http\Controllers\Api\Books;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

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
class BooksGetController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
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
}
