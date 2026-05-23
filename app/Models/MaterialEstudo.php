<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialEstudo extends Model
{
    protected $table = 'material_estudo';

    public $timestamps = false;

    protected $fillable = [
        'conteudo_trilha_id',
        'titulo',
        'tipo',
        'url',
    ];

    public function conteudo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ConteudoTrilha::class, 'conteudo_trilha_id');
    }
}
