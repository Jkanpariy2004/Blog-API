<?php

use App\Http\Controllers\BlogsController;
use Illuminate\Support\Facades\Route;

Route::get('/blogs',[BlogsController::class,'index']);
Route::post('/create-blog',[BlogsController::class,'store']);
