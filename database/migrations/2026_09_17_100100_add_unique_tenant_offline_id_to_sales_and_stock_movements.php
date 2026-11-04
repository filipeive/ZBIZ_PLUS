<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'offline_id')) {
            if (!Schema::hasIndex('sales', 'sales_tenant_offline_id_unique')) {
                $duplicateSale = DB::table('sales')
                    ->select('tenant_id', 'offline_id', DB::raw('COUNT(*) as total'))
                    ->whereNotNull('offline_id')
                    ->where('offline_id', '!=', '')
                    ->groupBy('tenant_id', 'offline_id')
                    ->havingRaw('COUNT(*) > 1')
                    ->first();

                if ($duplicateSale) {
                    throw new \RuntimeException(
                        "Não foi possível criar o índice único sales_tenant_offline_id_unique: "
                        . "existem vendas duplicadas para tenant_id={$duplicateSale->tenant_id} e offline_id={$duplicateSale->offline_id} ({$duplicateSale->total} registos)."
                    );
                }

                Schema::table('sales', function (Blueprint $table) {
                    $table->unique(['tenant_id', 'offline_id'], 'sales_tenant_offline_id_unique');
                });
            }
        }

        if (Schema::hasTable('stock_movements') && Schema::hasColumn('stock_movements', 'offline_id')) {
            if (!Schema::hasIndex('stock_movements', 'stock_movements_tenant_offline_id_unique')) {
                $duplicateMovement = DB::table('stock_movements')
                    ->select('tenant_id', 'offline_id', DB::raw('COUNT(*) as total'))
                    ->whereNotNull('offline_id')
                    ->where('offline_id', '!=', '')
                    ->groupBy('tenant_id', 'offline_id')
                    ->havingRaw('COUNT(*) > 1')
                    ->first();

                if ($duplicateMovement) {
                    throw new \RuntimeException(
                        "Não foi possível criar o índice único stock_movements_tenant_offline_id_unique: "
                        . "existem movimentos duplicados para tenant_id={$duplicateMovement->tenant_id} e offline_id={$duplicateMovement->offline_id} ({$duplicateMovement->total} registos)."
                    );
                }

                Schema::table('stock_movements', function (Blueprint $table) {
                    $table->unique(['tenant_id', 'offline_id'], 'stock_movements_tenant_offline_id_unique');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales') && Schema::hasIndex('sales', 'sales_tenant_offline_id_unique')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropUnique('sales_tenant_offline_id_unique');
            });
        }

        if (Schema::hasTable('stock_movements') && Schema::hasIndex('stock_movements', 'stock_movements_tenant_offline_id_unique')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->dropUnique('stock_movements_tenant_offline_id_unique');
            });
        }
    }
};
