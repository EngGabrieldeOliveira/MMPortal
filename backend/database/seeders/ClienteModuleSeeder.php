<?php

namespace Database\Seeders;

use App\Models\Classificacao;
use App\Models\Cliente;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClienteModuleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['cliente' => 'Cliente', 'fornecedor' => 'Fornecedor', 'transportadora' => 'Transportadora'] as $key => $name) {
            Classificacao::updateOrCreate(['chave' => $key], ['nome' => $name, 'ativo' => true]);
        }
        $customer = Cliente::firstOrCreate(['codigo' => 'CLI-000001'], ['uuid' => (string) Str::uuid(), 'tipo' => 'juridica', 'nome' => 'Marques Metais Demonstração', 'empresa' => 'Marques Metais Demonstração', 'email' => 'comercial@marquesmetais.test', 'telefone' => '51999999999', 'cidade' => 'Santa Cruz do Sul', 'estado' => 'RS', 'status' => 'ativo', 'razao_social' => 'Marques Metais Demonstração Ltda', 'nome_fantasia' => 'Marques Metais Demonstração', 'documento' => '11222333000144', 'cpf_cnpj' => '11222333000144', 'inscricao_estadual' => '123456789', 'inscricao_municipal' => '123456', 'segmento' => 'Metalurgia', 'condicao_pagamento_padrao' => '30 dias', 'limite_faturamento' => 50000, 'ativado_em' => now()]);
        $customer->classificacoes()->syncWithoutDetaching(Classificacao::where('chave', 'cliente')->pluck('id'));
        $customer->enderecoCobranca()->firstOrCreate(['tipo' => 'cobranca'], ['cep' => '96835-120', 'logradouro' => 'Rua Exemplo', 'cidade' => 'Santa Cruz do Sul', 'estado' => 'RS']);
        $customer->contatos()->firstOrCreate(['email' => 'comercial@marquesmetais.test'], ['nome' => 'Contato Comercial', 'telefone' => '51999999999', 'whatsapp' => '51999999999', 'is_principal' => true, 'aprova_orcamentos' => true]);
    }
}
