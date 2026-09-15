<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('license_audit_logs')) {
            Schema::create('license_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->foreignId('license_key_id')->nullable()->constrained('license_keys')->nullOnDelete();
                $table->string('event', 50)->index(); // issued, activated, verify_failed, revoked
                $table->string('key_code', 50)->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('app_version', 20)->nullable();
                $table->json('details')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('license_audit_logs');
    }
};
