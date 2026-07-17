<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitacaoAnexo extends Model
{
    protected $table = 'solicitacao_anexos';

    protected $fillable = ['solicitacao_id', 'nome_original', 'caminho', 'mime_type', 'tamanho'];

    public function solicitacao(): BelongsTo
    {
        return $this->belongsTo(Solicitacao::class);
    }
}
