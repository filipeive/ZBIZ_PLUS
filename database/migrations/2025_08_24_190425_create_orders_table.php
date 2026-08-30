<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // usuário que criou
        $table->string('customer_name', 100);
        $table->string('customer_phone', 20)->nullable();
        $table->string('customer_email', 100)->nullable();
        $table->text('description'); // descrição do trabalho
        $table->decimal('estimated_amount', 10, 2)->default(0);
        $table->decimal('advance_payment', 10, 2)->default(0); // sinal/entrada
        $table->datetime('delivery_date')->nullable();
        $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
        $table->enum('status', ['pending', 'in_progress', 'completed', 'delivered', 'cancelled'])->default('pending');
        $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
        $table->text('notes')->nullable();
        $table->text('internal_notes')->nullable();

        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->index(['status', 'created_at']);
        $table->index(['customer_name']);
        $table->index(['delivery_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};