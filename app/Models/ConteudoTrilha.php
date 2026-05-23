<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConteudoTrilha extends Model
{
    protected $table = 'conteudo_trilha';

    public $timestamps = false;

    protected $fillable = [
        'trilha_id',
        'titulo',
        'descricao',
        'ordem',
        'tipo_conteudo',
    ];

    public function trilha(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TrilhaAprendizagem::class, 'trilha_id');
    }

    public function materiais(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MaterialEstudo::class, 'conteudo_trilha_id');
    }
}
