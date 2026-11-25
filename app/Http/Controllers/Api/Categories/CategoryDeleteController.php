<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use Src\Categories\Application\Delete\CategoryDeleter;

class CategoryDeleteController extends Controller
{
    public function __construct(private readonly CategoryDeleter $deleter) {}

    #[OA\Delete(
        path: '/api/categories/{category}',
        summary: 'Delete a category',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(
                name: 'category',
                in: 'path',
                required: true,
                description: 'Category ID',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Category deleted'
            ),
            new OA\Response(
                response: 404,
                description: 'Category not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorMessage')
            ),
            new OA\Response(
                response: 409,
                description: 'Category has associated books',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorMessage')
            ),
        ]
    )]
    public function __invoke(int $category): Response
    {
        ($this->deleter)($category);

        return response()->noContent();
    }
}
