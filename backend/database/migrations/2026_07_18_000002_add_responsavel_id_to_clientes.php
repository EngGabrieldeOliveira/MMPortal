<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table): void {
            $table->foreignId('responsavel_id')->nullable()->after('matriz_id')->constrained('users')->nullOnDelete();
        });

        DB::table('cliente_responsaveis')->orderBy('cliente_id')->orderBy('id')->get()
            ->unique('cliente_id')
            ->each(fn (object $responsavel) => DB::table('clientes')->where('id', $responsavel->cliente_id)->update(['responsavel_id' => $responsavel->user_id]));
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('responsavel_id');
        });
    }
};
