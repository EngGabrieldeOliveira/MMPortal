<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table): void {
            $table->string('tipo')->default('juridica')->after('id');
            $table->string('documento', 18)->nullable()->unique()->after('email');
            $table->string('cep', 9)->nullable()->after('estado');
            $table->string('logradouro')->nullable()->after('cep');
            $table->string('numero', 20)->nullable()->after('logradouro');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('bairro')->nullable()->after('complemento');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table): void {
            $table->dropUnique(['documento']);
            $table->dropColumn(['tipo', 'documento', 'cep', 'logradouro', 'numero', 'complemento', 'bairro']);
        });
    }
};
