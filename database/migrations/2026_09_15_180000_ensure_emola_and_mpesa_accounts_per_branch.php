<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('financial_accounts') || !Schema::hasTable('branches')) {
            return;
        }

        $branches = DB::table('branches')->get();

        foreach ($branches as $branch) {
            // 1. Garantir conta e-Mola para a filial
            $hasEmola = DB::table('financial_accounts')
                ->where('tenant_id', $branch->tenant_id)
                ->where('branch_id', $branch->id)
                ->where(function ($query) {
                    $query->where('name', 'like', '%e-mola%')
                        ->orWhere('name', 'like', '%emola%')
                        ->orWhere('slug', 'like', '%emola%');
                })
                ->exists();

            if (!$hasEmola) {
                DB::table('financial_accounts')->insert([
                    'tenant_id'       => $branch->tenant_id,
                    'branch_id'       => $branch->id,
                    'name'            => 'Carteira e-Mola (' . $branch->name . ')',
                    'slug'            => 'emola-branch-' . $branch->id,
                    'type'            => 'mobile_money',
                    'opening_balance' => 0.00,
                    'current_balance' => 0.00,
                    'is_active'       => true,
                    'sort_order'      => 3,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            // 2. Garantir conta M-Pesa para a filial
            $hasMpesa = DB::table('financial_accounts')
                ->where('tenant_id', $branch->tenant_id)
                ->where('branch_id', $branch->id)
                ->where(function ($query) {
                    $query->where('name', 'like', '%m-pesa%')
                        ->orWhere('name', 'like', '%mpesa%')
                        ->orWhere('slug', 'like', '%mpesa%');
                })
                ->exists();

            if (!$hasMpesa) {
                DB::table('financial_accounts')->insert([
                    'tenant_id'       => $branch->tenant_id,
                    'branch_id'       => $branch->id,
                    'name'            => 'Carteira M-Pesa (' . $branch->name . ')',
                    'slug'            => 'mpesa-branch-' . $branch->id,
                    'type'            => 'mobile_money',
                    'opening_balance' => 0.00,
                    'current_balance' => 0.00,
                    'is_active'       => true,
                    'sort_order'      => 2,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Preservar dados financeiros
    }
};

