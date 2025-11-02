<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Book;
use App\Http\Resources\BookResource;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'book_id' => 'nullable|string',
            'language' => 'nullable|string|max:255',
            'mime_type' => 'nullable|string|max:255',
            'topic' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'search' => 'nullable|string|max:255',
        ]);

        $perPage = $request->get('per_page', 25);

        $cacheKey = 'books_' . md5(serialize($request->all()));
        $books = Cache::remember($cacheKey, 3600, function () use ($request, $perPage) {
            $query = Book::with(['authors', 'languages', 'subjects', 'bookshelves', 'bookFormats'])
            ->whereHas('bookFormats', function ($q) {
                $q->where('mime_type', 'like', 'image/%');
            });

            if ($request->has('book_id') && !empty($request->book_id)) {
                $bookIds = explode(',', $request->book_id);
                $query->whereIn('gutenberg_id', array_map('intval', $bookIds));
            }

            if ($request->has('genre') && !empty($request->genre)) {
                $genres = explode(',', $request->genre);
                $query->whereHas('bookshelves', function ($q) use ($genres) {
                    $q->whereIn('bookshelf_id', $genres);
                });
            }

            if ($request->has('language') && !empty($request->language)) {
                $languages = explode(',', $request->language);
                $query->whereHas('languages', function ($q) use ($languages) {
                    $q->whereIn('code', $languages);
                });
            }

            if ($request->has('mime_type') && !empty($request->mime_type)) {
                $mimeTypes = explode(',', $request->mime_type);
                $query->whereHas('bookFormats', function ($q) use ($mimeTypes) {
                    $q->whereIn('mime_type', $mimeTypes);
                });
            }

            if ($request->has('topic') && !empty($request->topic)) {
                $topics = explode(',', $request->topic);
                $query->where(function ($q) use ($topics) {
                    foreach ($topics as $topic) {
                        $q->orWhereHas('subjects', function ($subQ) use ($topic) {
                            $subQ->where('name', 'ILIKE', '%' . $topic . '%');
                        })->orWhereHas('bookshelves', function ($bookQ) use ($topic) {
                            $bookQ->where('name', 'ILIKE', '%' . $topic . '%');
                        });
                    }
                });
            }
            if ($request->has('author') && !empty($request->author)) {
                $authors = explode(',', $request->author);
                $query->whereHas('authors', function ($q) use ($authors) {
                    foreach ($authors as $author) {
                        $q->orWhere('name', 'ILIKE', '%' . $author . '%');
                    }
                });
            }

            if ($request->has('title') && !empty($request->title)) {
                $titles = explode(',', $request->title);
                $query->where(function ($q) use ($titles) {
                    foreach ($titles as $title) {
                        $q->orWhere('title', 'ILIKE', '%' . $title . '%');
                    }
                });
            }

            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'ILIKE', '%' . $searchTerm . '%')
                      ->orWhereHas('authors', function ($authorQ) use ($searchTerm) {
                          $authorQ->where('name', 'ILIKE', '%' . $searchTerm . '%');
                      });
                });
            }

            $query->orderByRaw('download_count DESC NULLS LAST');
            return $query->paginate($perPage);
        });

        return BookResource::collection($books);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
