<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Book',
    required: ['id', 'title', 'author', 'published_year', 'category_id'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Clean Code'),
        new OA\Property(property: 'author', type: 'string', example: 'Robert C. Martin'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'A Handbook of Agile Software Craftsmanship.'),
        new OA\Property(property: 'published_year', type: 'integer', example: 2008),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'category', ref: '#/components/schemas/Category'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'BookInput',
    required: ['title', 'author', 'published_year', 'category_id'],
    properties: [
        new OA\Property(property: 'title', type: 'string', minLength: 3, maxLength: 255, example: 'Clean Code'),
        new OA\Property(property: 'author', type: 'string', maxLength: 255, example: 'Robert C. Martin'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'A Handbook of Agile Software Craftsmanship.'),
        new OA\Property(property: 'published_year', type: 'integer', minimum: 1000, example: 2008),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
    ]
)]
class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            'published_year' => $this->published_year,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
