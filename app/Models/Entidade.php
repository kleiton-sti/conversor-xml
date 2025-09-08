<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entidade extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entidade',
    ];

    public function scopeEntidade($query, $value)
    {
        return $query->where('entidade', 'LIKE', "%$value%");
    }

    public function unidades()
    {
        return $this->hasMany(Unidade::class);
    }
}
