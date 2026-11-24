<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class BookController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Book::query()->with('category')->orderBy('id');
        $categoryId = $request->query('category');

        if ($categoryId !== null && $categoryId !== '') {
            if (! ctype_digit((string) $categoryId)) {
                return BookResource::collection(collect());
            }

            $query->where('category_id', (int) $categoryId);
        }

        return BookResource::collection($query->get());
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = Book::query()->create($request->validated())->load('category');

        return (new BookResource($book))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Book $book): BookResource
    {
        return new BookResource($book->load('category'));
    }

    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $book->update($request->validated());

        return new BookResource($book->load('category'));
    }

    public function destroy(Book $book): Response
    {
        $book->delete();

        return response()->noContent();
    }
}
