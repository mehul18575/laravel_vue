<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookLanguage extends Model
{
    protected $table = 'books_language';
    protected $fillable = [
        'code',
    ];

}
