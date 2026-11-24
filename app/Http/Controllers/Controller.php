<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Library API',
    description: 'REST API for managing books and categories.'
)]
#[OA\Server(url: '/', description: 'API Server')]
#[OA\Tag(name: 'Health', description: 'API health check')]
#[OA\Tag(name: 'Categories', description: 'Category management endpoints')]
#[OA\Tag(name: 'Books', description: 'Book management endpoints')]
#[OA\Schema(
    schema: 'ErrorMessage',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Resource not found.'),
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    required: ['message', 'errors'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(property: 'errors', type: 'object'),
    ]
)]
abstract class Controller {}
