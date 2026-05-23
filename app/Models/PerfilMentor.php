<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilMentor extends Model
{
    protected $table = 'perfis_mentor';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'nivel_instrucao',
        'formado',
        'anos_experiencia',
    ];

    protected function casts(): array
    {
        return [
            'formado' => 'boolean',
        ];
    }

    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
