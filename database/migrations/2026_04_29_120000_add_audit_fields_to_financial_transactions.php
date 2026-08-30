<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('financial_transactions')) {
            Schema::table('financial_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('financial_transactions', 'balance_after')) {
                    $table->decimal('balance_after', 12, 2)->nullable()->after('status');
                }
                if (!Schema::hasColumn('financial_transactions', 'reversed_by')) {
                    $table->unsignedBigInteger('reversed_by')->nullable()->after('balance_after');
                }
                if (!Schema::hasColumn('financial_transactions', 'reversal_of')) {
                    $table->unsignedBigInteger('reversal_of')->nullable()->after('reversed_by');
                }
                if (!Schema::hasColumn('financial_transactions', 'deleted_at')) {
                    $table->softDeletes()->after('reversal_of');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('financial_transactions')) {
            Schema::table('financial_transactions', function (Blueprint $table) {
                $cols = array_filter(['balance_after', 'reversed_by', 'reversal_of', 'deleted_at'], fn($c) => Schema::hasColumn('financial_transactions', $c));
                if (!empty($cols)) $table->dropColumn($cols);
            });
        }
    }
};
