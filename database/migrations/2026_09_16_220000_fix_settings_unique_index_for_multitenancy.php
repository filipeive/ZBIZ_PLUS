<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id')->index();
            }
        });

        // 1. Remover o índice único global antigo em 'key' se existir
        try {
            $connection = Schema::getConnection()->getDriverName();
            if ($connection === 'mysql') {
                $indexes = DB::select("SHOW INDEX FROM settings WHERE Key_name = 'settings_key_unique'");
                if (!empty($indexes)) {
                    DB::statement("ALTER TABLE `settings` DROP INDEX `settings_key_unique`");
                }
            } else {
                Schema::table('settings', function (Blueprint $table) {
                    $table->dropUnique('settings_key_unique');
                });
            }
        } catch (\Throwable $e) {
            // Ignora se não existir
        }

        // 2. Adicionar o índice composto único (tenant_id, key) se ainda não existir
        try {
            $connection = Schema::getConnection()->getDriverName();
            if ($connection === 'mysql') {
                $compound = DB::select("SHOW INDEX FROM settings WHERE Key_name = 'settings_tenant_key_unique'");
                if (empty($compound)) {
                    DB::statement("ALTER TABLE `settings` ADD UNIQUE `settings_tenant_key_unique` (`tenant_id`, `key`)");
                }
            } else {
                Schema::table('settings', function (Blueprint $table) {
                    $table->unique(['tenant_id', 'key'], 'settings_tenant_key_unique');
                });
            }
        } catch (\Throwable $e) {
            // Ignora se já existir
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};

