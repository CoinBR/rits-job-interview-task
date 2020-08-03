<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cliente;

class ClientesController extends Controller
{
    public function store(){
       Cliente::create([
            'nome' => request('nome')
       ]); 
    }
}
