<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $hidden = ['pivot', 'created_at', 'updated_at'];
    protected $guarded = [];

    public function produtos(){
        return $this->belongsToMany('App\Pedido');
    }

}
