<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacao_anexos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('solicitacao_id')->constrained('solicitacoes')->cascadeOnDelete();
            $table->string('nome_original');
            $table->string('caminho');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('tamanho');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacao_anexos');
    }
};
