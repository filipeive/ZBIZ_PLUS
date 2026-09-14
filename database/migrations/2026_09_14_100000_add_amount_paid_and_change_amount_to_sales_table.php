<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'amount_paid')) {
                $table->decimal('amount_paid', 14, 2)->default(0.00)->after('total_amount');
            }
            if (!Schema::hasColumn('sales', 'change_amount')) {
                $table->decimal('change_amount', 14, 2)->default(0.00)->after('amount_paid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'change_amount']);
        });
    }
};

