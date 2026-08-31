<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'sku')) {
                    $table->string('sku', 60)->nullable()->index()->after('barcode');
                }
                if (!Schema::hasColumn('products', 'dosage')) {
                    $table->string('dosage', 100)->nullable()->after('sku');
                }
                if (!Schema::hasColumn('products', 'active_ingredient')) {
                    $table->string('active_ingredient', 150)->nullable()->after('dosage');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $cols = array_filter(['sku', 'dosage', 'active_ingredient'], fn($c) => Schema::hasColumn('products', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
