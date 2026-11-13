<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionController extends Controller {
    public function index(Request $request) {
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->orderBy('due_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(['transactions' => $transactions]);
    }

    public function store(Request $request) {
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
        $groupUuid = \Illuminate\Support\Str::uuid();
        $totalCents = (int) round($validated['amount'] * 100);
        $installments = $validated['installments'];
        
        $baseCents = intdiv($totalCents, $installments);
        $remainderCents = $totalCents % $installments;
        $baseDate = \Carbon\Carbon::parse($validated['due_date']);

        try {
            DB::transaction(function () use ($validated, $userId, $groupUuid, $installments, $baseCents, $remainderCents, $baseDate) {
                for ($i = 1; $i <= $installments; $i++) {
                    $targetDate = $baseDate->copy()->addMonths($i - 1);
                    $installmentCents = ($i === 1) ? ($baseCents + $remainderCents) : $baseCents;
                    $status = ($i === 1) ? $validated['initial_status'] : 'pending';

                    \App\Models\Transaction::create([
                        'user_id' => $userId,
                        'group_uuid' => $groupUuid,
                        'description' => $validated['description'],
                        'category' => $validated['category'],
                        'amount' => $installmentCents / 100,
                        'current_installment' => $i,
                        'total_installments' => $installments,
                        'due_date' => $targetDate->toDateString(),
                        'type' => $validated['type'],
                        'status' => $status
                    ]);
                }
            });
        } catch (\Exception $e) {
            // O DB::transaction faz o rollback automático se houver exceção,
            // mas capturamos aqui para retornar um JSON amigável e logar o erro real.
            \Illuminate\Support\Facades\Log::error("Erro ao salvar parcelas: " . $e->getMessage());
            return response()->json(['message' => 'Erro interno ao salvar transação: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Lançamento salvo com sucesso!'], 201);
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