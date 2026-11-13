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

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/teste-migration-neon', function () {

    try {

        DB::beginTransaction();

        Schema::create('teste_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role_title')->default('Conta Pessoal');
            $table->decimal('monthly_budget', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('teste_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('teste_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        DB::rollBack();

        return response()->json([
            'success' => true,
            'message' => 'Todas as estruturas foram criadas com sucesso!'
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