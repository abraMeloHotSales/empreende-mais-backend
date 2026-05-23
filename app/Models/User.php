<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $rememberTokenName = false;

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = null;

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'tipo_usuario',
    ];

    protected $hidden = ['senha'];

    protected function casts(): array
    {
        return [
            'criado_em' => 'datetime',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'senha';
    }

    public function perfil(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Perfil::class, 'usuario_id');
    }

    public function perfilMentor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PerfilMentor::class, 'usuario_id');
    }

    public function trilhasUsuario(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrilhaUsuario::class, 'usuario_id');
    }

    public function sessoesComoAluno(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SessaoMentoria::class, 'aluno_id');
    }

    public function sessoesComoMentor(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SessaoMentoria::class, 'mentor_id');
    }

    public function certificados(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Certificado::class, 'usuario_id');
    }

    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PostForum::class, 'usuario_id');
    }

    public function empresa(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Empresa::class, 'usuario_id');
    }
}
