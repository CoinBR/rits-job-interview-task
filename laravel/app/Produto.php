<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $guarded = [];

    public function produtos(){
        return $this->belongsToMany('App\Pedido');
    }
}
