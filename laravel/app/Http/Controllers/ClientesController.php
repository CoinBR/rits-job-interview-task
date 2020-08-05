<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cliente;


class ClientesController extends Controller
{
    private function getValidatedData($fields){
        0 / 0;
        dd(request());
        dd('123');
        dd($fields);
        $validationRules = [
            'nome' => 'required',
            'email' => 'unique:clientes|required|email',
            'telefone' => 'unique:clientes|required|digits_between:10,11',
            'endereco' => 'required',
        ]);
        if (!isset($fields)){
            dd('123');
            return request()->validate($validationRules);
        } else{
            return array_filter($validationRules, function ($key){
                return in_array($key, $fields); 
            });
        }


    }
    
    public function store(){
       $newObj = Cliente::create($this->getValidatedData()); 
       return $newObj;
    }

    public function show(Cliente $cliente){
        return $cliente;
    }

    public function update(Cliente $cliente){
        $cliente->update($this->getValidatedData(request()->all));
        return $cliente;
    }

    public function destroy(Cliente $cliente){
        $cliente->delete();
    }
}
