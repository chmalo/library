<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use OpenApi\Attributes as OA;
use Src\Categories\Application\Update\CategoryUpdater;
use Src\Categories\Application\Update\UpdateCategoryDto;

#[OA\Put(
    path: '/api/categories/{category}',
    summary: 'Update a category',
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
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/CategoryInput')
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Category updated',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', ref: '#/components/schemas/Category'),
                ]
            )
        ),
        new OA\Response(
            response: 404,
            description: 'Category not found',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorMessage')
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error',
            content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
        ),
    ]
)]
class CategoryPutController extends Controller
{
    public function __construct(private readonly CategoryUpdater $updater) {}

    public function __invoke(UpdateCategoryRequest $request, int $category): CategoryResource
    {
        $updated = ($this->updater)(new UpdateCategoryDto(
            id: $category,
            name: $request->validated('name'),
        ));

        return new CategoryResource($updated);
    }
}
