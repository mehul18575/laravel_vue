<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Book;
use App\Http\Resources\BookResource;

/**
 * @OA\Info(
 *     title="Books",
 *     description="API Endpoints for Books",
 *     version="1.0.0"
 * )
 */

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/books",
     *     tags={"Books"},
     *     summary="Get list of books",
     *     description="Retrieve paginated books with filters",
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Results per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search...",
     *         required=false,
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="book_id",
     *         in="query",
     *         description="Filter by book ID(s), comma-separated",
     *         required=false,
     *         @OA\Schema(type="string", example="1,2,3")
     *    ),
     *      @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Filter by book title or author",
     *         required=false,
     *         @OA\Schema(type="string", example="")
     *     ),
     *      @OA\Parameter(
     *         name="topic",
     *         in="query",
     *         description="Enter Topic",
     *         required=false,
     *         @OA\Schema(type="string", example="")
     *     ),
     *    @OA\Parameter(
     *      name="author",
     *      in="query",
     *      description="Enter Author Name",
     *      required=false,
     *      @OA\Schema(type="string", example="")
     *     ),
     * 
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language (fr,en,de,...)",
     *         required=false,
     *         @OA\Schema(
     *           type="string",
     *           enum={"en", "fr", "de", "es"},
     *           example="en"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="mime_type",
     *         in="query",
     *         description="Select MIME type format",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"text/html", "application/pdf", "text/plain", "image/jpeg", "image/jpg", "image/png", "application/epub+zip", "application/zip","application/x-mobipocket-ebook"},
     *             example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of books with pagination",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="total", type="integer", example=25),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Harry Potter"),
     *                     @OA\Property(property="author", type="string", example="J.K. Rowling"),
     *                     @OA\Property(property="genre", type="string", example="Fantasy")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad request")
     * )
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

        $cacheKey = 'books_' . md5(serialize($request->all())) . time();
        $books = Cache::remember($cacheKey, 3600, function () use ($request, $perPage) {
            $query = Book::with(['authors', 'languages', 'subjects', 'bookshelves', 'bookFormats'])
                ->whereHas('bookFormats', function ($q) {
                $q->where('mime_type', 'like', 'image/%');
            });

            if ($request->filled('book_id')) {
                $bookIds = array_map('intval', explode(',', $request->book_id));
                $query->whereIn('gutenberg_id', $bookIds);
            }

            if ($request->filled('genre') && !empty($request->genre)) {
                $genres = explode(',', $request->genre);
                $query->whereHas('bookshelves', function ($q) use ($genres) {
                    $q->whereIn('bookshelf_id', $genres);
                });
            }

            if ($request->filled('language') && !empty($request->language)) {
                $languages = explode(',', $request->language);
                $query->whereHas('languages', function ($q) use ($languages) {
                    $q->whereIn('code', $languages);
                });
            }

            if ($request->filled('mime_type') && !empty($request->mime_type)) {
                $mimeTypes = explode(',', $request->mime_type);
                $query->whereHas('bookFormats', function ($q) use ($mimeTypes) {
                    $q->whereIn('mime_type', $mimeTypes);
                });
            }

            if ($request->filled('topic') && !empty($request->topic)) {
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
            if ($request->filled('author')) {
                $authors = array_filter(array_map('trim', explode(',', $request->author)));
            
                $query->where(function ($query) use ($authors) {
                    foreach ($authors as $author) {
                        $query->orWhereHas('authors', function ($q) use ($author) {
                            $q->where('name', 'ILIKE', '%' . $author . '%');
                        });
                    }
                });
            }

            if ($request->filled('title') && !empty($request->title)) {
                $titles = explode(',', $request->title);
                $query->where(function ($q) use ($titles) {
                    foreach ($titles as $title) {
                        $q->orWhere('title', 'ILIKE', '%' . $title . '%');
                    }
                });
            }

            if ($request->filled('search') && !empty($request->search)) {
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
