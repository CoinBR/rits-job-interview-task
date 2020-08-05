<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cliente;


class ClientesController extends Controller
{
    private function getValidatedData(){
        return request()->validate([
            'nome' => 'required',
            'email' => 'unique:clientes|required|email',
            'telefone' => 'unique:clientes|required',
            'endereco' => 'required',
        ]);
    }
    
    public function store(){
       $newObj = Cliente::create($this->getValidatedData()); 
       return $newObj;
    }

    public function show(Cliente $cliente){
        return $cliente;
    }

    public function update(Cliente $cliente){
        $cliente->update($this->getValidatedData());
        return $cliente;
    }
}
