<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('financial_accounts')) {
            return;
        }

        $exists = DB::table('financial_accounts')
            ->where('slug', 'conta-bancaria')
            ->exists();

        if (!$exists) {
            DB::table('financial_accounts')->insert([
                'name' => 'Conta Bancaria',
                'slug' => 'conta-bancaria',
                'type' => 'bank',
                'opening_balance' => 0,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        DB::table('financial_accounts')
            ->where('slug', 'conta-bancaria')
            ->update([
                'is_active' => true,
                'sort_order' => 2,
            ]);
    }

    public function down(): void
    {
    }
};
