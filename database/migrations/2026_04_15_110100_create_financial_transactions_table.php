<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('financial_transactions')) {
            Schema::create('financial_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('financial_account_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('type', 50);
                $table->string('direction', 10); // in, out
                $table->decimal('amount', 15, 2);
                $table->date('transaction_date');
                $table->string('description');
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('payment_method', 30)->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 20)->default('confirmed'); // confirmed, pending, reversed
                $table->decimal('balance_after', 15, 2)->nullable();
                $table->unsignedBigInteger('reversed_by')->nullable();
                $table->unsignedBigInteger('reversal_of')->nullable();
                $table->boolean('include_in_metrics')->default(true);
                $table->softDeletes();
                $table->timestamps();

                $table->index(['reference_type', 'reference_id']);
                $table->index(['transaction_date', 'direction']);
                $table->index(['type', 'transaction_date']);
                $table->index('financial_account_id');
                $table->index('user_id');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
