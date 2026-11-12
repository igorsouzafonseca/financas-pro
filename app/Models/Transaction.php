<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'group_uuid', 'description', 'category', 
        'amount', 'current_installment', 'total_installments', 
        'due_date', 'type', 'status'
    ];

    protected function casts(): array {
        return [
            'amount' => 'decimal:2',
            'current_installment' => 'integer',
            'total_installments' => 'integer',
        ];
    }
}
