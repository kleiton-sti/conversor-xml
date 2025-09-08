<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unidade extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unidades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unidade',
        'entidade_id',
    ];


    //Models Relacionados
    public function entidade()
    {
        return $this->belongsTo(Entidade::class);
    }

    public function setores()
    {
        return $this->hasMany(Setor::class);
    }


    //Scopes
    public function scopeUnidade($query, $value)
    {
        return $query->where('unidade', 'LIKE', "%$value%");
    }

    public function scopeEntidade($query, $value)
    {
        if (!empty($value)) {
            $query->whereHas('entidade', function ($q) use ($value) {
                $q->where('entidade', 'LIKE', "%$value%");
            });
        }
    }

    public function scopeFiltroAtivo($query, $value)
    {
        if($value == 0){
            return $query->whereNotNull('deleted_at');
        }elseif($value == 1){
            return $query->whereNull('deleted_at');
        }
    }
}
