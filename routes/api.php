<?php

use App\Http\Controllers\Api\Books\CreateBookController;
use App\Http\Controllers\Api\Books\DeleteBookController;
use App\Http\Controllers\Api\Books\ListBooksController;
use App\Http\Controllers\Api\Books\ShowBookController;
use App\Http\Controllers\Api\Books\UpdateBookController;
use App\Http\Controllers\Api\Categories\CreateCategoryController;
use App\Http\Controllers\Api\Categories\DeleteCategoryController;
use App\Http\Controllers\Api\Categories\ListCategoriesController;
use App\Http\Controllers\Api\Categories\ShowCategoryController;
use App\Http\Controllers\Api\Categories\UpdateCategoryController;
use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);

Route::prefix('categories')->group(function (): void {
    Route::get('/', ListCategoriesController::class);
    Route::post('/', CreateCategoryController::class);
    Route::get('/{category}', ShowCategoryController::class);
    Route::put('/{category}', UpdateCategoryController::class);
    Route::delete('/{category}', DeleteCategoryController::class);
});

Route::prefix('books')->group(function (): void {
    Route::get('/', ListBooksController::class);
    Route::post('/', CreateBookController::class);
    Route::get('/{book}', ShowBookController::class);
    Route::put('/{book}', UpdateBookController::class);
    Route::delete('/{book}', DeleteBookController::class);
});
