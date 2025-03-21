<?php

use App\Http\Controllers\AccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AccountController::class)->group(function () {
    Route::get('/accounts', 'index')->name('accounts.index');
    Route::post('/accounts', 'create')->name('accounts.create');
});
