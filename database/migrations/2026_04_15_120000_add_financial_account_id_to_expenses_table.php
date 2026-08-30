<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'financial_account_id')) {
                $table->unsignedBigInteger('financial_account_id')->nullable()->after('expense_category_id');
            }
        });

        // Seed default financial accounts if table exists and empty
        if (Schema::hasTable('financial_accounts') && DB::table('financial_accounts')->count() === 0) {
            DB::table('financial_accounts')->insert([
                [
                    'name' => 'Caixa Principal',
                    'slug' => 'caixa-principal',
                    'type' => 'cash',
                    'current_balance' => 0,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Carteira Móvel (M-Pesa / e-Mola)',
                    'slug' => 'carteira-movel',
                    'type' => 'mobile_money',
                    'current_balance' => 0,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'financial_account_id')) {
                $table->dropColumn('financial_account_id');
            }
        });
    }
};
