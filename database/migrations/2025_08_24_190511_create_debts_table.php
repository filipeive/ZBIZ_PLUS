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
        $table->unsignedBigInteger('user_id')->nullable(); // usuário que criou
        $table->unsignedBigInteger('sale_id')->nullable(); // venda relacionada
        $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
        $table->string('customer_name', 100);
        $table->string('customer_phone', 20)->nullable();
        $table->string('customer_document', 20)->nullable();
        $table->decimal('original_amount', 10, 2);
        $table->decimal('paid_amount', 10, 2)->default(0);
        $table->decimal('remaining_amount', 10, 2);
        $table->date('debt_date');
        $table->date('due_date')->nullable();
        $table->enum('status', ['active', 'partial', 'paid', 'overdue', 'cancelled'])->default('active');
        $table->text('description')->nullable();
        $table->text('notes')->nullable();

        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');

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