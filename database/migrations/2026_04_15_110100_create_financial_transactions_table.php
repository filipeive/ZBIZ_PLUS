<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('financial_account_id');
            $table->integer('user_id')->nullable();
            $table->string('type', 50);
            $table->string('direction', 10);
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('description');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('payment_method', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index(['transaction_date', 'direction']);
            $table->index(['type', 'transaction_date']);
            $table->index('financial_account_id');
            $table->index('user_id');
        });

        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->foreign('financial_account_id')
                ->references('id')
                ->on('financial_accounts')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropForeign(['financial_account_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('financial_transactions');
    }
};
