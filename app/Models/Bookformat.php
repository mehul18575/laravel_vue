<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookformat extends Model
{
    protected $table = 'books_format';
    protected $fillable = [
        'mime_type',
        'url',
    ];
}
