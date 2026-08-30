<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Plans Table
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->decimal('monthly_price', 12, 2)->default(0);
                $table->decimal('annual_price', 12, 2)->default(0);
                $table->integer('max_branches')->default(1);
                $table->integer('max_users')->default(2);
                $table->integer('max_products')->default(0); // 0 = unlimited
                $table->json('features')->nullable(); // ['pos', 'debts', 'salaries', 'multi_branch', 'pharmacy', 'mpesa_api']
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Subscriptions Table
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
                $table->string('status')->default('trialing'); // trialing, active, past_due, suspended, cancelled
                $table->timestamp('trial_starts_at')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('current_period_starts_at')->nullable();
                $table->timestamp('current_period_ends_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->string('payment_method')->default('mpesa'); // mpesa, emola, card, bank_transfer, manual
                $table->string('last_payment_reference')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
            });
        }

        // 3. Subscription Payments Table
        if (!Schema::hasTable('subscription_payments')) {
            Schema::create('subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->string('currency', 3)->default('MZN');
                $table->string('payment_method')->default('mpesa');
                $table->string('mpesa_phone', 30)->nullable();
                $table->string('mpesa_transaction_id')->nullable()->index();
                $table->string('receipt_number')->nullable();
                $table->string('status')->default('completed'); // pending, completed, failed, refunded
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
