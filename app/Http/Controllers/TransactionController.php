<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller {
    public function index(Request $request) {
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->orderBy('due_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(['transactions' => $transactions]);
    }

    public function store(Request $request) 
    {
        // 1. Registra no log o payload exato que chegou do front-end
        Log::info('Tentativa de criar transação', [
            'user_id' => $request->user()->id,
            'payload' => $request->all()
        ]);

        $validated = $request->validate([
            'description' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:60'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'installments' => ['required', 'integer', 'min:1', 'max:120'],
            'due_date' => ['required', 'date_format:Y-m-d'],
            'type' => ['required', 'in:expense,income'],
            'initial_status' => ['required', 'in:pending,paid']
        ]);

        $userId = $request->user()->id;
        $groupUuid = Str::uuid();
        $totalCents = (int) round($validated['amount'] * 100);
        $installments = $validated['installments'];
        
        $baseCents = intdiv($totalCents, $installments);
        $remainderCents = $totalCents % $installments;
        $baseDate = Carbon::parse($validated['due_date']);

        try {
            DB::transaction(function () use ($validated, $userId, $groupUuid, $installments, $baseCents, $remainderCents, $baseDate) {
                $rows = [];

                for ($i = 1; $i <= $installments; $i++) {
                    // Ajusta o mês: parcela 1 usa o mês base (i-1 = 0), parcela 2 adiciona 1 mês, etc.
                    $installmentDate = $baseDate->copy()->addMonths($i - 1)->toDateString();

                    $rows[] = [
                        'user_id'             => $userId,
                        'group_uuid'          => $groupUuid,
                        'description'         => $validated['description'],
                        'category'            => $validated['category'],
                        'amount'              => (($i === 1) ? ($baseCents + $remainderCents) : $baseCents) / 100,
                        'current_installment' => $i,
                        'total_installments'  => $installments,
                        'due_date'            => $installmentDate,
                        'type'                => $validated['type'],
                        'status'              => ($i === 1) ? $validated['initial_status'] : 'pending',
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ];
                }

                Transaction::insert($rows);
            });

            Log::info('Transação criada com sucesso!', ['group_uuid' => $groupUuid]);
            
            $firstTransaction = Transaction::where('group_uuid', $groupUuid)->first();

            // Descobre exatamente onde o Laravel está gravando
            $dbName = DB::connection()->getDatabaseName();
            $dbHost = DB::connection()->getConfig('host');
            $totalTransactions = Transaction::count();

            Log::info('Diagnóstico de Gravação', [
                'database' => $dbName,
                'host' => $dbHost,
                'total_rows_in_table' => $totalTransactions
            ]);

            return response()->json([
                'message' => 'Lançamento salvo com sucesso!',
                'debug_info' => [
                    'database' => $dbName,
                    'host' => $dbHost,
                    'total_in_table' => $totalTransactions
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erro crítico ao salvar transação: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        // Retorna o erro exato temporariamente para vermos na tela do navegador/dev tools
        return response()->json([
            'message' => 'Erro ao salvar: ' . $e->getMessage()
        ], 500);
        }
    }

    public function toggleStatus(Request $request, $id) {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $transaction->status = $transaction->status === 'paid' ? 'pending' : 'paid';
        $transaction->save();

        return response()->json(['message' => 'Status atualizado.', 'transaction' => $transaction]);
    }

    public function destroy(Request $request, $groupUuid) {
        $deleted = Transaction::where('group_uuid', $groupUuid)
            ->where('user_id', $request->user()->id)
            ->delete();

        if (!$deleted) {
            return response()->json(['error' => 'Registro não encontrado.'], 404);
        }

        return response()->json(['message' => 'Parcelamento excluído com sucesso.']);
    }
}