<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessaoMentoria extends Model
{
    protected $table = 'sessoes_mentoria';

    public $timestamps = false;

    protected $fillable = [
        'mentor_id',
        'aluno_id',
        'agendado_em',
        'status',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'agendado_em' => 'datetime',
        ];
    }

    public function mentor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function aluno(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'aluno_id');
    }
}
