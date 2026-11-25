<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use OpenApi\Attributes as OA;
use Src\Categories\Application\Find\CategoryFinder;

class CategoryGetController extends Controller
{
    public function __construct(private readonly CategoryFinder $finder) {}

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
    public function __invoke(int $category): CategoryResource
    {
        return new CategoryResource(($this->finder)($category));
    }
}
