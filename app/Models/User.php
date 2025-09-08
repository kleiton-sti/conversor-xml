<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'registro',
        'nome',
        'email',
        'cpf',
        'password',
        'grupo_id',
        'setor_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }

    public function hasPermission()
    {
        // $arrayPermissoes[] = null;
        foreach ($this->grupo->permissoes as $p) {
            $arrayPermissoes[] =  $p->nome_permissao;
        }

        return $arrayPermissoes;
    }

    //SCOPES
    public function scopeFiltroNome($query, $value)
    {
        return $query->where('nome', 'LIKE', "%$value%");
    }

    public function scopeFiltroSetor($query, $value)
    {

        if (!empty($value)) {
            $query->whereHas('setor', function ($q) use ($value) {
                $q->where('setor', 'LIKE', "%$value%");
            });
        }
    }

    public function scopeFiltroUnidade($query, string $nomeUnidade)
    {
        return $query->whereHas('setor.unidade', function ($q) use ($nomeUnidade) {
            $q->where('unidade', 'like', '%' . $nomeUnidade . '%');
        });
    }

    public function scopeFiltroEntidade($query, string $nomeEntidade)
    {
        return $query->whereHas('setor.unidade.entidade', function ($q) use ($nomeEntidade) {
            $q->where('entidade', 'like', '%' . $nomeEntidade . '%');
        });
    }

    public function scopeFiltroAtivo($query, $value)
    {

        if ($value == 0) {
            return $query->whereNotNull('deleted_at');
        } elseif ($value == 1) {
            return $query->whereNull('deleted_at');
        }
    }
}
