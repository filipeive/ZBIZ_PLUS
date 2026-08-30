<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
       Schema::create('debt_payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('debt_id')->constrained('debts')->onDelete('cascade');
        $table->unsignedBigInteger('user_id')->nullable(); // usuário que registrou o pagamento
        $table->decimal('amount', 10, 2);
        $table->enum('payment_method', ['cash', 'card', 'transfer', 'pix'])->default('cash');
        $table->date('payment_date');
        $table->text('notes')->nullable();

        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        $table->index(['debt_id', 'payment_date']);
    });

    }

    public function down()
    {
        Schema::dropIfExists('debt_payments');
    }
};  