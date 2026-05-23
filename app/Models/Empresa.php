<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'nome',
        'area_atuacao',
        'descricao',
        'estagio_negocio_id',
    ];

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function localizacoes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LocalizacaoEmpresa::class, 'empresa_id');
    }
}
