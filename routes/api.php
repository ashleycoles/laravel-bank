<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AccountController::class)->group(function () {
    Route::get('/accounts', 'index')->name('accounts.index');
    Route::post('/accounts', 'create')->name('accounts.create');

    Route::post('/accounts/deposit', 'deposit')->name('accounts.deposit');
    Route::post('/accounts/withdraw', 'withdraw')->name('accounts.withdraw');
    Route::post('/accounts/overdraft', 'updateOverdraftLimit')->name('accounts.overdraft.update');
});

Route::controller(TransactionController::class)->group(function () {
    Route::post('/transactions/send', 'send')->name('transactions.send');
});
