<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitacoes', function (Blueprint $table): void {
            $table->string('codigo', 20)->nullable()->unique()->after('id');
        });
        DB::table('solicitacoes')->orderBy('id')->get()->each(function (object $solicitacao): void {
            DB::table('solicitacoes')->where('id', $solicitacao->id)->update(['codigo' => 'SOL-'.str_pad((string) $solicitacao->id, 6, '0', STR_PAD_LEFT)]);
        });
    }

    public function down(): void
    {
        Schema::table('solicitacoes', function (Blueprint $table): void {
            $table->dropUnique(['codigo']);
            $table->dropColumn('codigo');
        });
    }
};
