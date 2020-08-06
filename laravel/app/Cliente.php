<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $hidden = ['pivot', 'created_at', 'updated_at'];
    protected $guarded = [];

    public function pedidos(){
        return $this->hasMany(Pedidos::class);
    }
}
