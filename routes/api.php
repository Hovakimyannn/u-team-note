<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sso'/*,'can:is_owner'*/])->controller(NoteController::class)
    ->prefix('/notes')
    ->group(function () {
        Route::GET('/tag/{id?}', 'index');
        Route::GET('/tags', 'tags');
        Route::POST('/', 'store');
        Route::POST('/{id}', 'update');
        Route::GET('/{id}', 'show');
        Route::DELETE('/{id}', 'destroy');
    });

Route::middleware('auth:sso')->get('/user', function () {
    return new \Illuminate\Http\JsonResponse(\Illuminate\Support\Facades\Auth::user());
});
