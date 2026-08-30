<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tenants Table
        if (!Schema::hasTable('tenants')) {
            Schema::create('tenants', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('domain')->nullable()->unique();
                $table->string('subdomain')->nullable()->unique();
                $table->string('business_type')->default('retail'); // retail, pharmacy, reprography, services, restaurant, other
                $table->string('nuit', 15)->nullable()->index();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('currency', 3)->default('MZN');
                $table->string('status')->default('trial'); // trial, active, suspended, cancelled
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('subscription_ends_at')->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }

        // 2. Branches Table
        if (!Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('code', 20)->default('MAIN');
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('email')->nullable();
                $table->boolean('is_main')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['tenant_id', 'is_active']);
            });
        }

        // 3. Add tenant_id & branch_id columns to existing business tables safely
        $tablesWithTenantAndBranch = [
            'users'                  => ['tenant_id', 'branch_id'],
            'products'               => ['tenant_id'],
            'categories'             => ['tenant_id'],
            'sales'                  => ['tenant_id', 'branch_id'],
            'sale_items'             => ['tenant_id', 'branch_id'],
            'debt_items'             => ['tenant_id', 'branch_id'],
            'order_items'            => ['tenant_id', 'branch_id'],
            'debts'                  => ['tenant_id', 'branch_id'],
            'expenses'               => ['tenant_id', 'branch_id'],
            'expense_categories'     => ['tenant_id'],
            'stock_movements'        => ['tenant_id', 'branch_id'],
            'financial_accounts'     => ['tenant_id', 'branch_id'],
            'financial_transactions' => ['tenant_id', 'branch_id'],
            'orders'                 => ['tenant_id', 'branch_id'],
            'notifications'          => ['tenant_id'],
            'user_activities'        => ['tenant_id'],
            'settings'               => ['tenant_id'],
        ];

        foreach ($tablesWithTenantAndBranch as $tableName => $columns) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
                    if (in_array('tenant_id', $columns) && !Schema::hasColumn($tableName, 'tenant_id')) {
                        $table->unsignedBigInteger('tenant_id')->nullable()->index();
                    }
                    if (in_array('branch_id', $columns) && !Schema::hasColumn($tableName, 'branch_id')) {
                        $table->unsignedBigInteger('branch_id')->nullable()->index();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'users', 'products', 'categories', 'sales', 'debts', 'expenses',
            'expense_categories', 'stock_movements', 'financial_accounts',
            'financial_transactions', 'orders', 'notifications', 'user_activities', 'settings'
        ];

        foreach ($tables as $t) {
            if (Schema::hasTable($t)) {
                Schema::table($t, function (Blueprint $table) use ($t) {
                    $cols = array_filter(['tenant_id', 'branch_id'], fn($c) => Schema::hasColumn($t, $c));
                    if (!empty($cols)) {
                        $table->dropColumn($cols);
                    }
                });
            }
        }

        Schema::dropIfExists('branches');
        Schema::dropIfExists('tenants');
    }
};
