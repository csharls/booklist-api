<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class BookController extends Controller
{
    /**
     * List books, newest first, 15 per page.
     */
    public function index(): AnonymousResourceCollection
    {
        return BookResource::collection(Book::latest('id')->paginate());
    }

    /**
     * Store a new book.
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = Book::create($request->validated());

        return BookResource::make($book)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Delete a book.
     */
    public function destroy(Book $book): Response
    {
        $book->delete();

        return response()->noContent();
    }
}
