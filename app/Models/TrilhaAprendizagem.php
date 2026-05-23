<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrilhaAprendizagem extends Model
{
    protected $table = 'trilhas_aprendizagem';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descricao',
        'estagio_alvo',
        'nivel_dificuldade',
    ];

    public function conteudos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ConteudoTrilha::class, 'trilha_id');
    }

    public function trilhasUsuario(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrilhaUsuario::class, 'trilha_id');
    }

    public function certificados(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Certificado::class, 'trilha_id');
    }
}
