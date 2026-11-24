<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        summary: 'List all categories',
        tags: ['Categories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of categories',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Category')
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::query()->orderBy('id')->get());
    }

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
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::query()->create($request->validated());

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/categories/{category}',
        summary: 'Get a category by ID',
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
                response: 200,
                description: 'Category details',
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
        ]
    )]
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

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
    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());

        return new CategoryResource($category);
    }

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
    public function destroy(Category $category): Response|JsonResponse
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
