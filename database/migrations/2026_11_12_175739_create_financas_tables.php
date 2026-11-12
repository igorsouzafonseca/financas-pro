<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('group_uuid');
            $table->string('description');
            $table->string('category')->default('Outros');
            $table->decimal('amount', 12, 2);
            $table->unsignedSmallInteger('current_installment')->default(1);
            $table->unsignedSmallInteger('total_installments')->default(1);
            $table->date('due_date');
            $table->enum('type', ['expense', 'income'])->default('expense');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'due_date']);
            $table->index(['user_id', 'group_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('users');
    }
};
