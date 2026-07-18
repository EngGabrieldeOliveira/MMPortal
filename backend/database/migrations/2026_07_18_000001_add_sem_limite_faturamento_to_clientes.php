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
            $table->decimal('limite_faturamento', 14, 2)->nullable()->default(null)->change();
            $table->boolean('sem_limite_faturamento')->default(false)->after('limite_faturamento');
        });
    }

    public function down(): void
    {
        DB::table('clientes')->whereNull('limite_faturamento')->update(['limite_faturamento' => 0]);

        Schema::table('clientes', function (Blueprint $table): void {
            $table->dropColumn('sem_limite_faturamento');
            $table->decimal('limite_faturamento', 14, 2)->default(0)->change();
        });
    }
};
