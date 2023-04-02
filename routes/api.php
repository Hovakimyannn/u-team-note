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

Route::middleware('auth:sso')->controller(NoteController::class)
    ->prefix('/notes')
    ->group(function () {
        Route::GET('/', 'index');//->middleware('can:index_note');
        Route::POST('/', 'store');//->middleware('can:store_note');
        Route::POST('/{id}', 'update');//->middleware('can:update_note');
        Route::GET('/{id}', 'show');//->middleware('can:show_note');
        Route::DELETE('/{id}', 'destroy');//->middleware('can:destroy_note');
    });

Route::middleware('auth:sso')->get('/user', function () {
    return new \Illuminate\Http\JsonResponse(\Illuminate\Support\Facades\Auth::user());
});

