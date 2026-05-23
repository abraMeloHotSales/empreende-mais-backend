<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfis';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'nome_completo',
        'telefone',
        'bio',
        'nivel_letramento_digital',
    ];

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
