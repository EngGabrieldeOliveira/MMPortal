<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table): void {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->decimal('valor_total', 12, 2);
            $table->unsignedInteger('quantidade_itens');
            $table->string('status')->default('pendente');
            $table->date('data_pedido');
            $table->date('data_entrega_prevista')->nullable();
            $table->date('data_entrega_real')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
