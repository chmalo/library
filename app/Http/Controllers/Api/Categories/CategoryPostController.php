<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use Src\Categories\Application\Create\CategoryCreator;
use Src\Categories\Application\Create\CreateCategoryDto;
use Src\Categories\Domain\Exceptions\CategoryAlreadyExists;

#[OA\Post(
    path: '/api/categories',
    summary: 'Create a category',
    tags: ['Categories'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/CategoryInput')
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Category created',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', ref: '#/components/schemas/Category'),
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
class CategoryPostController extends Controller
{
    public function __construct(private readonly CategoryCreator $creator) {}

    public function __invoke(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = ($this->creator)(new CreateCategoryDto(
                name: $request->validated('name'),
            ));

            return (new CategoryResource($category))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (CategoryAlreadyExists $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }
}
