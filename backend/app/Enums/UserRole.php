<?php

namespace App\Enums;

enum UserRole: string
{
    case Administrador = 'administrador';
    case Diretoria = 'diretoria';
    case Comercial = 'comercial';
    case Engenharia = 'engenharia';
    case Producao = 'producao';
    case Compras = 'compras';
    case Financeiro = 'financeiro';
    case Rh = 'rh';
    case Obras = 'obras';
}
