<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('license_keys')) {
            Schema::create('license_keys', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
                $table->foreignId('issued_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('key_hash', 64)->unique();
                $table->string('mode')->default('offline'); // cloud, local_online, offline
                $table->string('status')->default('issued'); // issued, active, revoked, expired
                $table->string('issued_to')->nullable();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('revoked_at')->nullable();
                $table->json('payload')->nullable();
                $table->string('signature', 128)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
                $table->index(['mode', 'status']);
                $table->index('expires_at');
            });
        }

        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'installation_mode')) {
                $table->string('installation_mode')->default('cloud')->after('status');
            }
            if (!Schema::hasColumn('tenants', 'license_status')) {
                $table->string('license_status')->default('active')->after('installation_mode');
            }
            if (!Schema::hasColumn('tenants', 'license_expires_at')) {
                $table->timestamp('license_expires_at')->nullable()->after('license_status');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_keys');

        Schema::table('tenants', function (Blueprint $table) {
            foreach (['installation_mode', 'license_status', 'license_expires_at'] as $column) {
                if (Schema::hasColumn('tenants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
