<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['key' => 'clientes.view', 'module' => 'comercial', 'name' => 'Consultar clientes'],
            ['key' => 'clientes.manage', 'module' => 'comercial', 'name' => 'Gerenciar clientes'],
            ['key' => 'solicitacoes.manage', 'module' => 'comercial', 'name' => 'Gerenciar solicitações'],
            ['key' => 'orcamentos.manage', 'module' => 'comercial', 'name' => 'Gerenciar orçamentos'],
            ['key' => 'pedidos.manage', 'module' => 'comercial', 'name' => 'Gerenciar pedidos'],
            ['key' => 'ordens-servico.manage', 'module' => 'operacao', 'name' => 'Gerenciar ordens de serviço'],
            ['key' => 'auditoria.view', 'module' => 'sistema', 'name' => 'Consultar auditoria'],
            ['key' => 'permissoes.manage', 'module' => 'sistema', 'name' => 'Gerenciar permissões'],
        ] as $permission) {
            Permission::updateOrCreate(['key' => $permission['key']], $permission);
        }
    }
}
