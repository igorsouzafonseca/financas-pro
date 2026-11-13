<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    try {
        // Força o encerramento de qualquer transação abortada pendente no PostgreSQL
        DB::statement('ABORT;');
        DB::rollBack();

        // Roda as migrações normalmente
        Artisan::call('migrate', ['--force' => true]);

        Log::info('Migração executada com sucesso.');
        return 'Banco migrado e estruturado com sucesso na Neon!';
    } catch (\Exception $e) {
        Log::error('Erro ao migrar: ' . $e->getMessage());
        return 'Erro ao migrar: ' . $e->getMessage();
    }
});

// Rotas públicas de autenticação (Com controle de força bruta throttle)
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:5,10');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,5');
Route::post('/auth/logout', [AuthController::class, 'logout']);

// Rotas protegidas por sessão/Sanctum (IDOR prevenido pelo user_id do token/sessão)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::patch('/transactions/{id}/status', [TransactionController::class, 'toggleStatus']);
    Route::delete('/transactions/{groupUuid}', [TransactionController::class, 'destroy']);
});