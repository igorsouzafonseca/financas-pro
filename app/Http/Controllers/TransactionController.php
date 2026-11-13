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
        $groupUuid = (string) Str::uuid(); // Garante que seja string pura
        $totalCents = (int) round($validated['amount'] * 100);
        $installments = $validated['installments'];
        
        $baseCents = intdiv($totalCents, $installments);
        $remainderCents = $totalCents % $installments;
        $baseDate = Carbon::parse($validated['due_date']);

        try {
            // Removemos o DB::transaction temporariamente para testar a gravação direta
            for ($i = 1; $i <= $installments; $i++) {
                $installmentDate = $baseDate->copy()->addMonths($i - 1)->toDateString();

                Transaction::create([
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
                ]);
            }

            Log::info('Transação criada diretamente com sucesso!', ['group_uuid' => $groupUuid]);
            
            $totalTransactions = Transaction::count();

            return response()->json([
                'message' => 'Lançamento salvo com sucesso!',
                'total_in_table' => $totalTransactions
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erro crítico ao salvar transação diretamente: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
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