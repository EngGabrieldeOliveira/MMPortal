<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HistoricoStatus extends Model
{
    protected $table = 'historicos_status';

    protected $fillable = ['historico_type', 'historico_id', 'status_anterior', 'status_novo', 'motivo', 'metadados', 'usuario_id'];

    protected $casts = ['metadados' => 'array'];

    public function historico(): MorphTo
    {
        return $this->morphTo();
    }
}
