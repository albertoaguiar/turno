<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.basic')->prefix('v1')->group(function () {
    Route::resource('users', UserController::class);
    
    Route::put('transactions/update-status', [TransactionController::class, 'updateTransactionStatus']);
    Route::resource('transactions', TransactionController::class)->except(['update']);;
    
});