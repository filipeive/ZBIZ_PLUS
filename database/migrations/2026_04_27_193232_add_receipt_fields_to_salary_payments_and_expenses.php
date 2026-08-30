<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('salary_payments')) {
            Schema::table('salary_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('salary_payments', 'base_amount')) {
                    $table->decimal('base_amount', 12, 2)->nullable()->after('amount');
                }
                if (!Schema::hasColumn('salary_payments', 'variable_amount')) {
                    $table->decimal('variable_amount', 12, 2)->default(0)->after('base_amount');
                }
                if (!Schema::hasColumn('salary_payments', 'reference_month')) {
                    $table->string('reference_month', 7)->nullable()->after('payment_date');
                }
                if (!Schema::hasColumn('salary_payments', 'receipt_path')) {
                    $table->string('receipt_path')->nullable()->after('notes');
                }
            });
        }

        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('expenses', 'receipt_path')) {
                    $table->string('receipt_path')->nullable()->after('payment_method');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('salary_payments')) {
            Schema::table('salary_payments', function (Blueprint $table) {
                $cols = array_filter(['base_amount', 'variable_amount', 'reference_month', 'receipt_path'], fn($c) => Schema::hasColumn('salary_payments', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'receipt_path')) {
                    $table->dropColumn('receipt_path');
                }
            });
        }
    }
};
