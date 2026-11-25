<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;
use Src\Categories\Application\Search\CategoriesSearcher;

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
class CategoriesGetController extends Controller
{
    public function __construct(private readonly CategoriesSearcher $searcher) {}

    public function __invoke(): AnonymousResourceCollection
    {
        return CategoryResource::collection(($this->searcher)());
    }
}
