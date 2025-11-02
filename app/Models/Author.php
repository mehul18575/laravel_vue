<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $table = 'books_author';
    protected $fillable = [
        'name',
        'birth_year',
        'birth_year'
    ];
}
