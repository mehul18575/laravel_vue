<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookshelf extends Model
{
    protected $table = 'books_bookshelf';
    protected $fillable = [
        'name',
    ];
}
