<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $guarded = [];

    public function pedidos(){
        return $this->hasMany(Pedidos::class);
    }
}
