<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'offline_id')) {
                    $table->string('offline_id', 100)->nullable()->index()->after('id');
                }
                if (!Schema::hasColumn('sales', 'synced_at')) {
                    $table->timestamp('synced_at')->nullable()->index()->after('updated_at');
                }
            });
        }

        if (Schema::hasTable('stock_movements')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_movements', 'offline_id')) {
                    $table->string('offline_id', 100)->nullable()->index()->after('id');
                }
                if (!Schema::hasColumn('stock_movements', 'synced_at')) {
                    $table->timestamp('synced_at')->nullable()->index()->after('updated_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (Schema::hasColumn('sales', 'offline_id')) {
                    $table->dropColumn('offline_id');
                }
                if (Schema::hasColumn('sales', 'synced_at')) {
                    $table->dropColumn('synced_at');
                }
            });
        }

        if (Schema::hasTable('stock_movements')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                if (Schema::hasColumn('stock_movements', 'offline_id')) {
                    $table->dropColumn('offline_id');
                }
                if (Schema::hasColumn('stock_movements', 'synced_at')) {
                    $table->dropColumn('synced_at');
                }
            });
        }
    }
};
