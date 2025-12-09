<?php

use App\Http\Controllers\WordbaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/wordbase/submit', [WordbaseController::class, 'submit']);
