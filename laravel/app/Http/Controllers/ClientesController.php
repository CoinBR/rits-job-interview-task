<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cliente;


class ClientesController extends Controller
{
    public function store(Request $request){
        Cliente::create([
                'nome' => $request['nome'],
                'email' => $request['email'],
                'telefone' => $request['telefone'],
                'endereco' => $request['endereco'],
        ]); 
    }
}
