<?php


use App\Infrastructure\Controllers\CreateUserController;
use App\Infrastructure\Controllers\FollowStreamerController;
use App\Infrastructure\Controllers\GetStreamersController;
use App\Infrastructure\Controllers\GetStreamsController;
use App\Infrastructure\Controllers\GetTimelineController;
use App\Infrastructure\Controllers\GetTopsOfTheTopsController;
use App\Infrastructure\Controllers\GetUsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/analytics/streamers', GetStreamersController::class);
Route::get('/analytics/streams', GetStreamsController::class);
Route::get('/analytics/topsofthetops', GetTopsOfTheTopsController::class);
Route::post('/analytics/users', CreateUserController::class);
Route::get('analytics/userlist', GetUsersController::class);
Route::get('/analytics/timeline/{userId}', GetTimelineController::class);
Route::post('/analytics/follow', FollowStreamerController::class);