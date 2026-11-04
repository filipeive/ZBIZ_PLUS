<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('debt_payments') || !Schema::hasColumn('debt_payments', 'payment_method')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $column = DB::selectOne("SHOW COLUMNS FROM `debt_payments` WHERE Field = 'payment_method'");
            $type = strtolower((string) ($column->Type ?? ''));

            // Se ainda for ENUM sem mpesa/emola ou restrito, converter para VARCHAR(30) de forma idempotente
            if (str_starts_with($type, 'enum(')) {
                DB::statement("ALTER TABLE `debt_payments` MODIFY COLUMN `payment_method` VARCHAR(30) NOT NULL DEFAULT 'cash'");
            }
        } else {
            Schema::table('debt_payments', function (Blueprint $table) {
                $table->string('payment_method', 30)->default('cash')->change();
            });
        }
    }

    public function down(): void
    {
        // Mantém coluna compatível com mpesa e emola sem perda de dados
    }
};
