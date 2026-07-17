<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('nome_contato');
            $table->string('email_contato')->nullable();
            $table->string('telefone_contato')->nullable();
            $table->string('origem')->default('outro');
            $table->string('status')->default('nova');
            $table->text('descricao');
            $table->date('prazo_desejado')->nullable();
            $table->timestamp('proxima_acao_em')->nullable();
            $table->timestamp('postergada_ate')->nullable();
            $table->text('motivo_encerramento')->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('orcamentos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('solicitacao_id')->constrained('solicitacoes')->restrictOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->string('numero')->unique();
            $table->unsignedInteger('versao')->default(1);
            $table->string('status')->default('rascunho');
            $table->decimal('valor_total', 12, 2)->default(0);
            $table->date('validade_ate')->nullable();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamp('decidido_em')->nullable();
            $table->text('observacoes')->nullable();
            $table->unique(['solicitacao_id', 'versao']);
            $table->timestamps();
        });

        Schema::create('orcamento_itens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('orcamento_id')->constrained('orcamentos')->cascadeOnDelete();
            $table->unsignedInteger('ordem');
            $table->string('descricao');
            $table->decimal('quantidade', 10, 2);
            $table->string('unidade', 20)->default('un');
            $table->decimal('valor_unitario', 12, 2);
            $table->decimal('valor_total', 12, 2);
            $table->json('especificacoes')->nullable();
            $table->timestamps();
        });

        Schema::table('pedidos', function (Blueprint $table): void {
            $table->foreignId('orcamento_id')->nullable()->after('cliente_id')->constrained('orcamentos')->nullOnDelete();
            $table->date('prazo_prometido')->nullable()->after('data_entrega_real');
            $table->string('status')->default('aberto')->change();
        });

        Schema::create('pedido_itens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('orcamento_item_id')->nullable()->constrained('orcamento_itens')->nullOnDelete();
            $table->unsignedInteger('ordem');
            $table->string('descricao');
            $table->decimal('quantidade', 10, 2);
            $table->string('unidade', 20)->default('un');
            $table->decimal('valor_unitario', 12, 2);
            $table->decimal('valor_total', 12, 2);
            $table->json('especificacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('ordens_servico', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->string('numero')->unique();
            $table->string('titulo');
            $table->string('status')->default('aguardando_planejamento');
            $table->string('prioridade')->default('normal');
            $table->date('prazo_planejado')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('ordem_servico_etapas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ordem_servico_id')->constrained('ordens_servico')->cascadeOnDelete();
            $table->string('tipo');
            $table->unsignedInteger('sequencia');
            $table->string('status')->default('aguardando');
            $table->timestamp('planejada_inicio_em')->nullable();
            $table->timestamp('planejada_fim_em')->nullable();
            $table->timestamp('iniciada_em')->nullable();
            $table->timestamp('concluida_em')->nullable();
            $table->text('observacoes')->nullable();
            $table->unique(['ordem_servico_id', 'sequencia']);
            $table->timestamps();
        });

        Schema::create('historicos_status', function (Blueprint $table): void {
            $table->id();
            $table->morphs('historico');
            $table->string('status_anterior')->nullable();
            $table->string('status_novo');
            $table->text('motivo')->nullable();
            $table->json('metadados')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historicos_status');
        Schema::dropIfExists('ordem_servico_etapas');
        Schema::dropIfExists('ordens_servico');
        Schema::dropIfExists('pedido_itens');
        Schema::table('pedidos', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('orcamento_id');
            $table->dropColumn('prazo_prometido');
        });
        Schema::dropIfExists('orcamento_itens');
        Schema::dropIfExists('orcamentos');
        Schema::dropIfExists('solicitacoes');
    }
};
