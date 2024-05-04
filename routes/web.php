<?php


use App\Http\Controllers\UsersController;
use App\Http\Controllers\StreamsController;
use App\Http\Controllers\TopsOfTheTopsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/analytics/users', UsersController::class);
Route::get('/analytics/streams', StreamsController::class);
Route::get('/analytics/topofthetops', TopsOfTheTopsController::class);
