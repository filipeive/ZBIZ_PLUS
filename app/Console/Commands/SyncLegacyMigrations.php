<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncLegacyMigrations extends Command
{
    protected $signature = 'migrations:sync-legacy {--write : Persist the detected legacy migrations into the migrations table}';

    protected $description = 'Synchronize legacy schema state with Laravel migration records without replaying destructive old migrations.';

    public function handle(): int
    {
        $existing = DB::table('migrations')->pluck('migration')->all();
        $batch = ((int) DB::table('migrations')->max('batch')) + 1;

        $checks = [
            '0001_01_01_000000_create_users_table' => fn () => Schema::hasTable('users'),
            '0001_01_01_000001_create_cache_table' => fn () => Schema::hasTable('cache'),
            '0001_01_01_000002_create_jobs_table' => fn () => Schema::hasTable('jobs'),
            '2025_08_24_165649_create_permission_tables' => fn () => Schema::hasTable('roles'),
            '2025_08_24_190425_create_orders_table' => fn () => Schema::hasTable('orders'),
            '2025_08_24_190438_create_order_items_table' => fn () => Schema::hasTable('order_items'),
            '2025_08_24_190511_create_debts_table' => fn () => Schema::hasTable('debts'),
            '2025_08_24_190531_create_debt_payments_table' => fn () => Schema::hasTable('debt_payments'),
            '2025_09_02_114625_create_notifications_table' => fn () => Schema::hasTable('notifications'),
            '2025_11_06_083334_create_add_debt_indexes_table' => fn () => $this->hasIndex('debts', 'idx_customer_name')
                || $this->hasIndex('debts', 'debts_debt_type_status_index'),
            '2025_11_07_083651_add_phone_to_users_table' => fn () => Schema::hasColumn('users', 'phone'),
            '2025_11_17_091854_add_description_to_expense_categories_table' => fn () => Schema::hasColumn('expense_categories', 'description'),
        ];

        $toInsert = [];

        foreach ($checks as $migration => $resolver) {
            if (in_array($migration, $existing, true)) {
                $this->line("skip  {$migration}  already recorded");
                continue;
            }

            $matchesSchema = false;

            try {
                $matchesSchema = (bool) $resolver();
            } catch (\Throwable $e) {
                $this->warn("warn  {$migration}  check failed: {$e->getMessage()}");
                continue;
            }

            if ($matchesSchema) {
                $this->info("sync  {$migration}");
                $toInsert[] = [
                    'migration' => $migration,
                    'batch' => $batch,
                ];
            } else {
                $this->line("keep  {$migration}  still pending");
            }
        }

        if (empty($toInsert)) {
            $this->comment('No legacy migrations needed syncing.');
            return self::SUCCESS;
        }

        if (!$this->option('write')) {
            $this->comment('Dry run only. Re-run with --write to persist.');
            return self::SUCCESS;
        }

        DB::table('migrations')->insert($toInsert);
        $this->info(count($toInsert) . ' legacy migration record(s) inserted.');

        return self::SUCCESS;
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $database = DB::connection()->getDatabaseName();

        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $indexName)
            ->exists();
    }
}
