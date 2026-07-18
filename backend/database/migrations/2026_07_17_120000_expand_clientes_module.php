<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('razao_social')->nullable()->after('empresa');
            $table->string('nome_fantasia')->nullable()->after('razao_social');
            $table->string('cpf_cnpj', 18)->nullable()->unique()->after('documento');
            $table->string('inscricao_estadual')->nullable()->after('cpf_cnpj');
            $table->string('inscricao_municipal')->nullable()->after('inscricao_estadual');
            $table->string('segmento')->nullable()->after('inscricao_municipal');
            $table->string('condicao_pagamento_padrao')->nullable()->after('segmento');
            $table->decimal('limite_faturamento', 14, 2)->default(0)->after('condicao_pagamento_padrao');
            $table->timestamp('ativado_em')->nullable()->after('limite_faturamento');
            $table->timestamp('ultimo_pedido_em')->nullable()->after('ativado_em');
            $table->text('observacoes_internas')->nullable()->after('ultimo_pedido_em');
            $table->foreignId('matriz_id')->nullable()->after('status')->constrained('clientes')->nullOnDelete();
        });

        Schema::create('classificacoes', function (Blueprint $table): void {
            $table->id();
            $table->string('chave', 50)->unique();
            $table->string('nome');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('cliente_classificacoes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('classificacao_id')->constrained('classificacoes')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['cliente_id', 'classificacao_id']);
        });

        Schema::create('cliente_contatos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nome');
            $table->string('cargo')->nullable();
            $table->string('departamento')->nullable();
            $table->string('telefone', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('email')->nullable();
            $table->boolean('aprova_orcamentos')->default(false);
            $table->boolean('is_principal')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['cliente_id', 'is_principal']);
        });

        Schema::create('cliente_enderecos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('tipo', 30)->default('cobranca');
            $table->string('cep', 9);
            $table->string('logradouro');
            $table->string('numero', 20)->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade');
            $table->string('estado', 2);
            $table->string('pais', 2)->default('BR');
            $table->string('referencia')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cliente_id', 'tipo']);
        });

        Schema::create('cliente_documentos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('tipo', 50)->default('outro');
            $table->string('nome_original');
            $table->string('nome_exibicao')->nullable();
            $table->string('caminho')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('tamanho')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('enviado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cliente_responsaveis', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('papel', 50)->nullable();
            $table->timestamps();
            $table->unique(['cliente_id', 'user_id']);
        });

        Schema::create('obras', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('codigo')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->string('nome');
            $table->string('status')->default('planejada');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cliente_timeline_eventos', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('tipo', 80);
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->nullableMorphs('origem');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ocorreu_em')->useCurrent();
            $table->timestamps();
            $table->index(['cliente_id', 'ocorreu_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_timeline_eventos');
        Schema::dropIfExists('obras');
        Schema::dropIfExists('cliente_responsaveis');
        Schema::dropIfExists('cliente_documentos');
        Schema::dropIfExists('cliente_enderecos');
        Schema::dropIfExists('cliente_contatos');
        Schema::dropIfExists('cliente_classificacoes');
        Schema::dropIfExists('classificacoes');
        Schema::table('clientes', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('matriz_id');
            $table->dropUnique(['uuid']);
            $table->dropUnique(['cpf_cnpj']);
            $table->dropColumn(['uuid', 'razao_social', 'nome_fantasia', 'cpf_cnpj', 'inscricao_estadual', 'inscricao_municipal', 'segmento', 'condicao_pagamento_padrao', 'limite_faturamento', 'ativado_em', 'ultimo_pedido_em', 'observacoes_internas']);
        });
    }
};
