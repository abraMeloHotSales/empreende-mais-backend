<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostForum extends Model
{
    protected $table = 'posts_forum';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = null;

    protected $fillable = [
        'usuario_id',
        'titulo',
        'conteudo',
        'criado_em',
    ];

    protected function casts(): array
    {
        return [
            'criado_em' => 'datetime',
        ];
    }

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function comentarios(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ComentarioForum::class, 'post_id');
    }
}
