<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});

// Rotas públicas de autenticação (Com controle de força bruta throttle)
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:5,10')->name('register');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,5')->name('login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas protegidas por sessão/Sanctum (IDOR prevenido pelo user_id do token/sessão)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me'])->name('me');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('getTransactions');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions');
    Route::patch('/transactions/{id}/status', [TransactionController::class, 'toggleStatus'])->name('transactionsStatus');
    Route::delete('/transactions/{groupUuid}', [TransactionController::class, 'destroy'])->name('transactionsDestroy');
});