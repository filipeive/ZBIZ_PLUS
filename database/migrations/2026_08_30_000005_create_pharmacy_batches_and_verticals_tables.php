<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Product Batches (Lotes de Medicamentos - Farmácia)
        if (!Schema::hasTable('product_batches')) {
            Schema::create('product_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('batch_number', 60);
                $table->date('expiry_date');
                $table->date('manufacture_date')->nullable();
                $table->integer('quantity')->default(0);
                $table->decimal('cost_price', 14, 2)->default(0);
                $table->string('status', 20)->default('active'); // active, quarantine, expired, recalled
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'expiry_date', 'status']);
                $table->index(['product_id', 'expiry_date']);
            });
        }

        // 2. Prescriptions (Receitas Médicas - Psicotrópicos e Antibióticos ANARME)
        if (!Schema::hasTable('prescriptions')) {
            Schema::create('prescriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->string('patient_name');
                $table->string('patient_nuit', 15)->nullable();
                $table->string('prescriber_name');
                $table->string('prescriber_license', 50)->nullable(); // Ordem dos Médicos
                $table->string('health_facility')->nullable();
                $table->date('prescription_date');
                $table->timestamp('dispensed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'prescription_date']);
            });
        }

        // 3. Product Insumos (Vinculação de Matéria-Prima em Gráfica / Serigrafia)
        if (!Schema::hasTable('product_insumos')) {
            Schema::create('product_insumos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('parent_product_id')->constrained('products')->cascadeOnDelete(); // Serviço
                $table->foreignId('insumo_product_id')->constrained('products')->cascadeOnDelete(); // Material Físico
                $table->decimal('quantity_used', 10, 3)->default(1.000);
                $table->timestamps();

                $table->unique(['tenant_id', 'parent_product_id', 'insumo_product_id'], 'insumos_parent_child_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_insumos');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('product_batches');
    }
};
