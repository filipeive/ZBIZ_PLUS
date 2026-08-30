<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('salary_payments')) {
            Schema::create('salary_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedBigInteger('financial_account_id')->nullable();
                $table->unsignedBigInteger('financial_transaction_id')->nullable();
                $table->unsignedBigInteger('paid_by')->nullable();
                $table->decimal('amount', 14, 2);
                $table->decimal('base_amount', 14, 2)->default(0);
                $table->decimal('variable_amount', 14, 2)->default(0);
                $table->date('payment_date');
                $table->string('reference_month', 7)->nullable();
                $table->string('description');
                $table->text('notes')->nullable();
                $table->string('receipt_path')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
