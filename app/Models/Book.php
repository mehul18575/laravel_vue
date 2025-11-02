<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Author;
use App\Models\BookLanguage;
use App\Models\BookSubject;
use App\Models\Bookshelf;
use App\Models\Bookformat;

class Book extends Model
{
    protected $table = 'books_book';
    protected $fillable = [
        'title',
        'media_type',
        'download_count',
    ];

    public function authors()
    {
        return $this->belongsToMany(
            Author::class,  
            'books_book_authors',
            'book_id',
            'author_id'           
        );
    }

    public function languages()
    {
        return $this->belongsToMany(
            BookLanguage::class,
            'books_book_languages',
            'book_id',
            'language_id'
        );
    }

    public function subjects()
    {
        return $this->belongsToMany(
            BookSubject::class,
            'books_book_subjects',
            'book_id',
            'subject_id'
        );
    }

    public function bookshelves()
    {
        return $this->belongsToMany(
            Bookshelf::class,
            'books_book_bookshelves',
            'book_id',
            'bookshelf_id'
        );
    }

    public function bookFormats()
    {
        return $this->hasMany(
            Bookformat::class,
            'book_id',
        );
    }

}

