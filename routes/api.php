<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\GenerController;


Route::get('/books', [BookController::class, 'index']);
Route::get('/genres', [GenerController::class, 'index']);
