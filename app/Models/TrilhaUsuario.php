<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrilhaUsuario extends Model
{
    protected $table = 'trilhas_usuario';

    const CREATED_AT = 'iniciado_em';
    const UPDATED_AT = null;

    protected $fillable = [
        'usuario_id',
        'trilha_id',
        'progresso',
        'status',
        'iniciado_em',
        'concluido_em',
    ];

    protected function casts(): array
    {
        return [
            'iniciado_em'  => 'datetime',
            'concluido_em' => 'datetime',
            'progresso'    => 'integer',
        ];
    }

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function trilha(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TrilhaAprendizagem::class, 'trilha_id');
    }
}
