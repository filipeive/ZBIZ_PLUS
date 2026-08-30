<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
       Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
        $table->unsignedBigInteger('product_id')->nullable(); // produto se existir no cadastro
        $table->string('item_name', 150);
        $table->text('description')->nullable();
        $table->integer('quantity')->default(1);
        $table->decimal('unit_price', 10, 2);
        $table->decimal('total_price', 10, 2);

        $table->timestamps();

        $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });

    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};