<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $table = 'certificados';

    const CREATED_AT = 'emitido_em';
    const UPDATED_AT = null;

    protected $fillable = [
        'usuario_id',
        'trilha_id',
        'emitido_em',
        'url_certificado',
    ];

    protected function casts(): array
    {
        return [
            'emitido_em' => 'datetime',
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
