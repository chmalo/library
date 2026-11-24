<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class DeleteCategoryController extends Controller
{
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
    public function __invoke(Category $category): Response|JsonResponse
    {
        if ($category->books()->exists()) {
            return response()->json(
                ['message' => 'Cannot delete category with associated books.'],
                Response::HTTP_CONFLICT
            );
        }

        $category->delete();

        return response()->noContent();
    }
}
