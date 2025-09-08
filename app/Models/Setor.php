<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setor extends Model
{
    use HasFactory, SoftDeletes;

        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setores';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'setor',
        'unidade_id',
    ];

    public function unidade()
    {
        return $this->belongsTo(Unidade::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    //Scopes
    public function scopeFiltroSetor($query, $value)
    {
        return $query->where('setor', 'LIKE', "%$value%");
    }

    public function scopeFiltroUnidade($query, $value)
    {
        
        if (!empty($value)) {
            $query->whereHas('unidade', function ($q) use ($value) {
                $q->where('unidade', 'LIKE', "%$value%");
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
