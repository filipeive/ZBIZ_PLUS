<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Para quem é a notificação
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info'); // success, error, warning, info
            $table->string('icon')->nullable(); // ex: fa-shopping-cart
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('action_url')->nullable(); // opcional: link para ação
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('read');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};