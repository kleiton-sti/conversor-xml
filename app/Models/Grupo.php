<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grupo',
        'observacao'
    ];


    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function permissoes()
    {
        return $this->belongsToMany(Permissao::class, 'grupos_permissoes');
    }
}
