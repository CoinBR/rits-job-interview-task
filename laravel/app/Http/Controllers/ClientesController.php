<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cliente;


class ClientesController extends Controller
{
    public function store(){
        $data = request()->validate([
            'nome' => 'required',
            'email' => 'required|email',
            'telefone' => 'required',
            'endereco' => 'required',
        ]);
        Cliente::create($data); 
    }
}
