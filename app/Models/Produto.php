<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Movimento;

class Produto extends Model
{
    protected $fillable = [
        'nome',
        'quantidade',
        'estoquemin',
        'estoquemax'
    ];

    function movimento(){
        return $this->hasMany(Movimento::class);
    }
}
