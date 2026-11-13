<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


Route::get('/migrar', function () {

    try {

        DB::purge('pgsql');

        Artisan::call('migrate:fresh', [
            '--force' => true,
            '--verbose' => true,
        ]);

        return response()->json([
            'success' => true,
            'output' => Artisan::output()
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'previous' => $e->getPrevious()?->getMessage(),
            'output' => Artisan::output(),
        ], 500);
    }
});


Route::get('/teste-neon', function () {

    try {

        DB::purge('pgsql');

        DB::connection('pgsql')->beginTransaction();

        DB::statement('
            CREATE TABLE teste_neon (
                id BIGSERIAL PRIMARY KEY,
                email VARCHAR(255) UNIQUE
            )
        ');

        DB::rollBack();

        return response()->json([
            'success' => true,
            'message' => 'Teste executado com sucesso!'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'previous' => $e->getPrevious()?->getMessage(),
        ], 500);
    }
});

Route::get('/', function () {
    return view('welcome');
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