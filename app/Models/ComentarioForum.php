<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioForum extends Model
{
    protected $table = 'comentarios_forum';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = null;

    protected $fillable = [
        'post_id',
        'usuario_id',
        'conteudo',
        'criado_em',
    ];

    protected function casts(): array
    {
        return [
            'criado_em' => 'datetime',
        ];
    }

    public function post(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PostForum::class, 'post_id');
    }

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
