<?php

use App\Http\Controllers\Api\Books\BookDeleteController;
use App\Http\Controllers\Api\Books\BookGetController;
use App\Http\Controllers\Api\Books\BookPostController;
use App\Http\Controllers\Api\Books\BookPutController;
use App\Http\Controllers\Api\Books\BooksGetController;
use App\Http\Controllers\Api\Categories\CategoriesGetController;
use App\Http\Controllers\Api\Categories\CategoryDeleteController;
use App\Http\Controllers\Api\Categories\CategoryGetController;
use App\Http\Controllers\Api\Categories\CategoryPostController;
use App\Http\Controllers\Api\Categories\CategoryPutController;
use App\Http\Controllers\Api\HealthGetController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthGetController::class);

Route::prefix('categories')->group(function (): void {
    Route::get('/', CategoriesGetController::class);
    Route::post('/', CategoryPostController::class);
    Route::get('/{category}', CategoryGetController::class)->whereNumber('category');
    Route::put('/{category}', CategoryPutController::class)->whereNumber('category');
    Route::delete('/{category}', CategoryDeleteController::class)->whereNumber('category');
});

Route::prefix('books')->group(function (): void {
    Route::get('/', BooksGetController::class);
    Route::post('/', BookPostController::class);
    Route::get('/{book}', BookGetController::class);
    Route::put('/{book}', BookPutController::class);
    Route::delete('/{book}', BookDeleteController::class);
});
