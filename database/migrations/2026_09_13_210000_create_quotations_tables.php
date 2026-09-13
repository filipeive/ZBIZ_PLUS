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
        if (!Schema::hasTable('quotations')) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('quotation_number', 50)->index();
                $table->string('customer_name');
                $table->string('customer_nuit', 25)->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone', 30)->nullable();
                $table->string('customer_address')->nullable();
                $table->date('date');
                $table->date('valid_until')->nullable();
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->decimal('discount_amount', 14, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(16.00);
                $table->decimal('tax_amount', 14, 2)->default(0);
                $table->decimal('total_amount', 14, 2)->default(0);
                $table->string('tax_regime', 30)->default('normal'); // normal, exempt, simplified
                $table->string('tax_exemption_reason')->nullable();
                $table->boolean('prices_include_tax')->default(true);
                $table->string('status', 20)->default('draft')->index(); // draft, sent, approved, rejected, converted
                $table->unsignedBigInteger('converted_sale_id')->nullable()->index();
                $table->timestamp('converted_at')->nullable();
                $table->text('notes')->nullable();
                $table->text('terms_conditions')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'quotation_number']);
            });
        }

        if (!Schema::hasTable('quotation_items')) {
            Schema::create('quotation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->string('item_name');
                $table->text('description')->nullable();
                $table->decimal('quantity', 10, 2)->default(1);
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(16.00);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('total_price', 14, 2)->default(0);
                $table->boolean('is_tax_exempt')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
