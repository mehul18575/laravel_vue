<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookSubject extends Model
{
    protected $table = 'books_subject';
    protected $fillable = [
        'name',
    ];
}
