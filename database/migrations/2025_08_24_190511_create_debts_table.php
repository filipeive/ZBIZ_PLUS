<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->string('debt_type', 20)->default('product'); // product, money
            $table->unsignedBigInteger('user_id')->nullable(); // usuário que criou
            $table->unsignedBigInteger('employee_id')->nullable(); // se dívida de funcionário
            $table->unsignedBigInteger('sale_id')->nullable(); // venda relacionada
            $table->unsignedBigInteger('generated_sale_id')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_phone', 30)->nullable();
            $table->string('customer_document', 30)->nullable();

            $table->string('employee_name', 100)->nullable();
            $table->string('employee_phone', 30)->nullable();
            $table->string('employee_document', 30)->nullable();

            $table->decimal('original_amount', 14, 2);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('remaining_amount', 14, 2);
            $table->date('debt_date');
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('active'); // active, partial, paid, overdue, cancelled
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'due_date']);
            $table->index(['customer_name']);
            $table->index(['debt_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('debts');
    }
};
