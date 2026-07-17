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
            $table->string('codigo', 20)->nullable()->unique()->after('id');
        });

        DB::table('clientes')->orderBy('id')->get()->each(function (object $cliente): void {
            DB::table('clientes')->where('id', $cliente->id)->update([
                'codigo' => 'CLI-'.str_pad((string) $cliente->id, 6, '0', STR_PAD_LEFT),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table): void {
            $table->dropUnique(['codigo']);
            $table->dropColumn('codigo');
        });
    }
};
