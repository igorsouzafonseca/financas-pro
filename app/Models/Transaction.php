<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'group_uuid',
        'description',
        'category',
        'amount',
        'current_installment',
        'total_installments',
        'due_date',
        'type',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        //'due_date' => 'date',
        'current_installment' => 'integer',
        'total_installments' => 'integer',
    ];

    /**
     * Relacionamento com o usuário proprietário da transação.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}