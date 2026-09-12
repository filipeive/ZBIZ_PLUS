<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_product_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('sender_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('recipient_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('product_name');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('quantity')->nullable();
            $table->text('message');
            $table->string('status')->default('pending');
            $table->text('response')->nullable();
            $table->foreignId('response_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'created_at'], 'bpi_tenant_status_created_idx');
            $table->index(['recipient_branch_id', 'status', 'created_at'], 'bpi_recipient_status_created_idx');
            $table->index(['sender_branch_id', 'created_at'], 'bpi_sender_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_product_inquiries');
    }
};
