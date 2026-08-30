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
        // Verificar se a coluna já existe antes de adicionar
        if (!Schema::hasColumn('expense_categories', 'description')) {
            Schema::table('expense_categories', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('expense_categories', 'description')) {
            Schema::table('expense_categories', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};