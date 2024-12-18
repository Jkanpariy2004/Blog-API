<?php

use App\Http\Controllers\BlogsController;
use Illuminate\Support\Facades\Route;

Route::get('/blogs',[BlogsController::class,'index']);
Route::get('/blog/{id}',[BlogsController::class,'show']);
Route::post('/create-blog',[BlogsController::class,'store']);
Route::post('/update-blog/{id}',[BlogsController::class,'update']);
Route::get('/blog-delete/{id}',[BlogsController::class,'destroy']);
Route::post('/search',[BlogsController::class,'search']);
