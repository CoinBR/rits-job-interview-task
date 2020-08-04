<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cliente;


class ClientesController extends Controller
{
    public function store(){
        $data = request()->validate([
            'nome' => 'required',
            'email' => 'unique:clientes|required|email',
            'telefone' => 'unique:clientes|required',
            'endereco' => 'required',
        ]);
        $newObj = Cliente::create($data); 
        return $newObj;
    }

    public function show(Cliente $cliente){
        return $cliente;
    }
}
