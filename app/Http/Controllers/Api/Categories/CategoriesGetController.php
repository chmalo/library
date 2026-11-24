<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class CategoriesGetController extends Controller
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
    public function __invoke(): AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::query()->orderBy('id')->get());
    }
}
