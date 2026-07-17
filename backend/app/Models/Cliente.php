<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'codigo', 'tipo', 'nome', 'email', 'documento', 'telefone', 'empresa', 'cidade', 'estado', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'status',
    ];

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }
}
