<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Library API',
    description: 'REST API for managing books and categories.'
)]
#[OA\Server(url: '/', description: 'API Server')]
abstract class Controller {}
