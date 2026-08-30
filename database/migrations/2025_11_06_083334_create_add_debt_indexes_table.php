<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        Schema::table('debts', function (Blueprint $table) use ($isMysql) {
            $existingIndexes = [];
            if ($isMysql) {
                $indexes = DB::select("SHOW INDEX FROM debts");
                $existingIndexes = array_column($indexes, 'Key_name');
            }

            $addIndex = function ($name, $callback) use ($table, $existingIndexes, $isMysql) {
                if (!$isMysql || !in_array($name, $existingIndexes)) {
                    try {
                        $callback();
                    } catch (\Throwable $e) {
                        // Index already exists
                    }
                }
            };

            $addIndex('idx_status_type', fn() => $table->index(['status', 'debt_type'], 'idx_status_type'));
            $addIndex('idx_status_due_date', fn() => $table->index(['status', 'due_date'], 'idx_status_due_date'));
            $addIndex('idx_customer_name', fn() => $table->index('customer_name', 'idx_customer_name'));
            $addIndex('idx_employee_name', fn() => $table->index('employee_name', 'idx_employee_name'));
            $addIndex('idx_debt_date', fn() => $table->index('debt_date', 'idx_debt_date'));
            $addIndex('idx_user_id', fn() => $table->index('user_id', 'idx_user_id'));
            $addIndex('idx_employee_id', fn() => $table->index('employee_id', 'idx_employee_id'));
            $addIndex('idx_generated_sale_id', fn() => $table->index('generated_sale_id', 'idx_generated_sale_id'));
        });

        Schema::table('debt_items', function (Blueprint $table) use ($isMysql) {
            $existingIndexes = [];
            if ($isMysql) {
                $indexes = DB::select("SHOW INDEX FROM debt_items");
                $existingIndexes = array_column($indexes, 'Key_name');
            }

            if (!$isMysql || !in_array('idx_debt_id', $existingIndexes)) {
                try { $table->index('debt_id', 'idx_debt_id'); } catch (\Throwable $e) {}
            }

            if (!$isMysql || !in_array('idx_product_id', $existingIndexes)) {
                try { $table->index('product_id', 'idx_product_id'); } catch (\Throwable $e) {}
            }
        });

        Schema::table('debt_payments', function (Blueprint $table) use ($isMysql) {
            $existingIndexes = [];
            if ($isMysql) {
                $indexes = DB::select("SHOW INDEX FROM debt_payments");
                $existingIndexes = array_column($indexes, 'Key_name');
            }

            if (!$isMysql || !in_array('idx_payment_debt_id', $existingIndexes)) {
                try { $table->index('debt_id', 'idx_payment_debt_id'); } catch (\Throwable $e) {}
            }

            if (!$isMysql || !in_array('idx_debt_payment_date', $existingIndexes)) {
                try { $table->index(['debt_id', 'payment_date'], 'idx_debt_payment_date'); } catch (\Throwable $e) {}
            }

            if (!$isMysql || !in_array('idx_payment_date', $existingIndexes)) {
                try { $table->index('payment_date', 'idx_payment_date'); } catch (\Throwable $e) {}
            }
        });

        Schema::table('products', function (Blueprint $table) use ($isMysql) {
            $existingIndexes = [];
            if ($isMysql) {
                $indexes = DB::select("SHOW INDEX FROM products");
                $existingIndexes = array_column($indexes, 'Key_name');
            }

            if (!$isMysql || !in_array('idx_is_active', $existingIndexes)) {
                try { $table->index('is_active', 'idx_is_active'); } catch (\Throwable $e) {}
            }

            if (!$isMysql || !in_array('idx_active_type', $existingIndexes)) {
                try { $table->index(['is_active', 'type'], 'idx_active_type'); } catch (\Throwable $e) {}
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropIndex('idx_status_type');
            $table->dropIndex('idx_status_due_date');
            $table->dropIndex('idx_customer_name');
            $table->dropIndex('idx_employee_name');
            $table->dropIndex('idx_debt_date');
            $table->dropIndex('idx_user_id');
            $table->dropIndex('idx_employee_id');
            $table->dropIndex('idx_generated_sale_id');
        });

        Schema::table('debt_items', function (Blueprint $table) {
            $table->dropIndex('idx_debt_id');
            $table->dropIndex('idx_product_id');
        });

        Schema::table('debt_payments', function (Blueprint $table) {
            $table->dropIndex('idx_payment_debt_id');
            $table->dropIndex('idx_debt_payment_date');
            $table->dropIndex('idx_payment_date');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_is_active');
            $table->dropIndex('idx_active_type');
        });
    }
};
